

var _saveAs = (function(view) {
	// IE <10 is explicitly unsupported
	if (typeof navigator !== "undefined" && /MSIE [1-9]\./.test(navigator.userAgent)) {
		return;
	}
	var
		  doc = view.document
		  // only get URL when necessary in case Blob.js hasn't overridden it yet
		, get_URL = function() {
			return view.URL || view.webkitURL || view;
		}
		, save_link = doc.createElementNS("http://www.w3.org/1999/xhtml", "a")
		, can_use_save_link = "download" in save_link
		, click = function(node) {
			var event = doc.createEvent("MouseEvents");
			event.initMouseEvent(
				"click", true, false, view, 0, 0, 0, 0, 0
				, false, false, false, false, 0, null
			);
			node.dispatchEvent(event);
		}
		, webkit_req_fs = view.webkitRequestFileSystem
		, req_fs = view.requestFileSystem || webkit_req_fs || view.mozRequestFileSystem
		, throw_outside = function(ex) {
			(view.setImmediate || view.setTimeout)(function() {
				throw ex;
			}, 0);
		}
		, force_saveable_type = "application/octet-stream"
		, fs_min_size = 0
		// See https://code.google.com/p/chromium/issues/detail?id=375297#c7 and
		// https://github.com/eligrey/FileSaver.js/commit/485930a#commitcomment-8768047
		// for the reasoning behind the timeout and revocation flow
		, arbitrary_revoke_timeout = 500 // in ms
		, revoke = function(file) {
			var revoker = function() {
				if (typeof file === "string") { // file is an object URL
					get_URL().revokeObjectURL(file);
				} else { // file is a File
					file.remove();
				}
			};
			if (view.chrome) {
				revoker();
			} else {
				setTimeout(revoker, arbitrary_revoke_timeout);
			}
		}
		, dispatch = function(filesaver, event_types, event) {
			event_types = [].concat(event_types);
			var i = event_types.length;
			while (i--) {
				var listener = filesaver["on" + event_types[i]];
				if (typeof listener === "function") {
					try {
						listener.call(filesaver, event || filesaver);
					} catch (ex) {
						throw_outside(ex);
					}
				}
			}
		}
		, auto_bom = function(blob) {
			// prepend BOM for UTF-8 XML and text/* types (including HTML)
			if (/^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(blob.type)) {
				return new Blob(["\ufeff", blob], {type: blob.type});
			}
			return blob;
		}
		, FileSaver = function(blob, name) {
			blob = auto_bom(blob);
			// First try a.download, then web filesystem, then object URLs
			var
				  filesaver = this
				, type = blob.type
				, blob_changed = false
				, object_url
				, target_view
				, dispatch_all = function() {
					dispatch(filesaver, "writestart progress write writeend".split(" "));
				}
				// on any filesys errors revert to saving with object URLs
				, fs_error = function() {
					// don't create more object URLs than needed
					if (blob_changed || !object_url) {
						object_url = get_URL().createObjectURL(blob);
					}
					if (target_view) {
						target_view.location.href = object_url;
					} else {
						var new_tab = view.open(object_url, "_blank");
						if (new_tab === undefined && typeof safari !== "undefined") {
							//Apple do not allow window.open, see http://bit.ly/1kZffRI
							view.location.href = object_url;
						}
					}
					filesaver.readyState = filesaver.DONE;
					dispatch_all();
					revoke(object_url);
				}
				, abortable = function(func) {
					return function() {
						if (filesaver.readyState !== filesaver.DONE) {
							return func.apply(this, arguments);
						}
					};
				}
				, create_if_not_found = {create: true, exclusive: false}
				, slice
			;
			filesaver.readyState = filesaver.INIT;
			if (!name) {
				name = "download";
			}
			if (can_use_save_link) {
				object_url = get_URL().createObjectURL(blob);
				save_link.href = object_url;
				save_link.download = name;
				click(save_link);
				filesaver.readyState = filesaver.DONE;
				dispatch_all();
				revoke(object_url);
				return;
			}
			// Object and web filesystem URLs have a problem saving in Google Chrome when
			// viewed in a tab, so I force save with application/octet-stream
			// http://code.google.com/p/chromium/issues/detail?id=91158
			// Update: Google errantly closed 91158, I submitted it again:
			// https://code.google.com/p/chromium/issues/detail?id=389642
			if (view.chrome && type && type !== force_saveable_type) {
				slice = blob.slice || blob.webkitSlice;
				blob = slice.call(blob, 0, blob.size, force_saveable_type);
				blob_changed = true;
			}
			// Since I can't be sure that the guessed media type will trigger a download
			// in WebKit, I append .download to the filename.
			// https://bugs.webkit.org/show_bug.cgi?id=65440
			if (webkit_req_fs && name !== "download") {
				name += ".download";
			}
			if (type === force_saveable_type || webkit_req_fs) {
				target_view = view;
			}
			if (!req_fs) {
				fs_error();
				return;
			}
			fs_min_size += blob.size;
			req_fs(view.TEMPORARY, fs_min_size, abortable(function(fs) {
				fs.root.getDirectory("saved", create_if_not_found, abortable(function(dir) {
					var save = function() {
						dir.getFile(name, create_if_not_found, abortable(function(file) {
							file.createWriter(abortable(function(writer) {
								writer.onwriteend = function(event) {
									target_view.location.href = file.toURL();
									filesaver.readyState = filesaver.DONE;
									dispatch(filesaver, "writeend", event);
									revoke(file);
								};
								writer.onerror = function() {
									var error = writer.error;
									if (error.code !== error.ABORT_ERR) {
										fs_error();
									}
								};
								"writestart progress write abort".split(" ").forEach(function(event) {
									writer["on" + event] = filesaver["on" + event];
								});
								writer.write(blob);
								filesaver.abort = function() {
									writer.abort();
									filesaver.readyState = filesaver.DONE;
								};
								filesaver.readyState = filesaver.WRITING;
							}), fs_error);
						}), fs_error);
					};
					dir.getFile(name, {create: false}, abortable(function(file) {
						// delete file if it already exists
						file.remove();
						save();
					}), abortable(function(ex) {
						if (ex.code === ex.NOT_FOUND_ERR) {
							save();
						} else {
							fs_error();
						}
					}));
				}), fs_error);
			}), fs_error);
		}
		, FS_proto = FileSaver.prototype
		, saveAs = function(blob, name) {
			return new FileSaver(blob, name);
		}
	;
	// IE 10+ (native saveAs)
	if (typeof navigator !== "undefined" && navigator.msSaveOrOpenBlob) {
		return function(blob, name) {
			return navigator.msSaveOrOpenBlob(auto_bom(blob), name);
		};
	}

	FS_proto.abort = function() {
		var filesaver = this;
		filesaver.readyState = filesaver.DONE;
		dispatch(filesaver, "abort");
	};
	FS_proto.readyState = FS_proto.INIT = 0;
	FS_proto.WRITING = 1;
	FS_proto.DONE = 2;

	FS_proto.error =
	FS_proto.onwritestart =
	FS_proto.onprogress =
	FS_proto.onwrite =
	FS_proto.onabort =
	FS_proto.onerror =
	FS_proto.onwriteend =
		null;

	return saveAs;
}(window));






/**
 * Safari's data: support for creating and downloading files is really poor, so
 * various options need to be disabled in it. See
 * https://bugs.webkit.org/show_bug.cgi?id=102914
 *
 * @return {Boolean} `true` if Safari
 */
var _isSafari = function ()
{
	return navigator.userAgent.indexOf('Safari') !== -1 &&
		navigator.userAgent.indexOf('Chrome') === -1 &&
		navigator.userAgent.indexOf('Opera') === -1;
};


// Excel - Pre-defined strings to build a minimal XLSX file
var excelStrings = {
	'_rels/.rels': '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',

	'docProps/app.xml': '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><DocSecurity>0</DocSecurity><ScaleCrop>false</ScaleCrop><HeadingPairs><vt:vector size="2" baseType="variant"><vt:variant><vt:lpstr>工作表</vt:lpstr></vt:variant><vt:variant><vt:i4>1</vt:i4></vt:variant></vt:vector></HeadingPairs><TitlesOfParts><vt:vector size="1" baseType="lpstr"><vt:lpstr>Sheet1</vt:lpstr></vt:vector></TitlesOfParts><LinksUpToDate>false</LinksUpToDate><SharedDoc>false</SharedDoc><HyperlinksChanged>false</HyperlinksChanged><AppVersion>12.0000</AppVersion></Properties>',

    'docProps/core.xml': '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><cp:lastModifiedBy>iqnehs</cp:lastModifiedBy><dcterms:modified xsi:type="dcterms:W3CDTF">2017-09-14T10:20:36Z</dcterms:modified></cp:coreProperties>',

    'xl/_rels/workbook.xml.rels': '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="theme/theme1.xml"/><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/></Relationships>',

    /*'xl/theme/theme1.xml': '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\
<a:theme xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" name="Office 主题"><a:themeElements><a:clrScheme name="Office"><a:dk1><a:sysClr val="windowText" lastClr="000000"/></a:dk1><a:lt1><a:sysClr val="window" lastClr="FFFFFF"/></a:lt1><a:dk2><a:srgbClr val="1F497D"/></a:dk2><a:lt2><a:srgbClr val="EEECE1"/></a:lt2><a:accent1><a:srgbClr val="4F81BD"/></a:accent1><a:accent2><a:srgbClr val="C0504D"/></a:accent2><a:accent3><a:srgbClr val="9BBB59"/></a:accent3><a:accent4><a:srgbClr val="8064A2"/></a:accent4><a:accent5><a:srgbClr val="4BACC6"/></a:accent5><a:accent6><a:srgbClr val="F79646"/></a:accent6><a:hlink><a:srgbClr val="0000FF"/></a:hlink><a:folHlink><a:srgbClr val="800080"/></a:folHlink></a:clrScheme><a:fontScheme name="Office"><a:majorFont><a:latin typeface="Cambria"/><a:ea typeface=""/><a:cs typeface=""/><a:font script="Jpan" typeface="ＭＳ Ｐゴシック"/><a:font script="Hang" typeface="맑은 고딕"/><a:font script="Hans" typeface="宋体"/><a:font script="Hant" typeface="新細明體"/><a:font script="Arab" typeface="Times New Roman"/><a:font script="Hebr" typeface="Times New Roman"/><a:font script="Thai" typeface="Tahoma"/><a:font script="Ethi" typeface="Nyala"/><a:font script="Beng" typeface="Vrinda"/><a:font script="Gujr" typeface="Shruti"/><a:font script="Khmr" typeface="MoolBoran"/><a:font script="Knda" typeface="Tunga"/><a:font script="Guru" typeface="Raavi"/><a:font script="Cans" typeface="Euphemia"/><a:font script="Cher" typeface="Plantagenet Cherokee"/><a:font script="Yiii" typeface="Microsoft Yi Baiti"/><a:font script="Tibt" typeface="Microsoft Himalaya"/><a:font script="Thaa" typeface="MV Boli"/><a:font script="Deva" typeface="Mangal"/><a:font script="Telu" typeface="Gautami"/><a:font script="Taml" typeface="Latha"/><a:font script="Syrc" typeface="Estrangelo Edessa"/><a:font script="Orya" typeface="Kalinga"/><a:font script="Mlym" typeface="Kartika"/><a:font script="Laoo" typeface="DokChampa"/><a:font script="Sinh" typeface="Iskoola Pota"/><a:font script="Mong" typeface="Mongolian Baiti"/><a:font script="Viet" typeface="Times New Roman"/><a:font script="Uigh" typeface="Microsoft Uighur"/></a:majorFont><a:minorFont><a:latin typeface="Calibri"/><a:ea typeface=""/><a:cs typeface=""/><a:font script="Jpan" typeface="ＭＳ Ｐゴシック"/><a:font script="Hang" typeface="맑은 고딕"/><a:font script="Hans" typeface="宋体"/><a:font script="Hant" typeface="新細明體"/><a:font script="Arab" typeface="Arial"/><a:font script="Hebr" typeface="Arial"/><a:font script="Thai" typeface="Tahoma"/><a:font script="Ethi" typeface="Nyala"/><a:font script="Beng" typeface="Vrinda"/><a:font script="Gujr" typeface="Shruti"/><a:font script="Khmr" typeface="DaunPenh"/><a:font script="Knda" typeface="Tunga"/><a:font script="Guru" typeface="Raavi"/><a:font script="Cans" typeface="Euphemia"/><a:font script="Cher" typeface="Plantagenet Cherokee"/><a:font script="Yiii" typeface="Microsoft Yi Baiti"/><a:font script="Tibt" typeface="Microsoft Himalaya"/><a:font script="Thaa" typeface="MV Boli"/><a:font script="Deva" typeface="Mangal"/><a:font script="Telu" typeface="Gautami"/><a:font script="Taml" typeface="Latha"/><a:font script="Syrc" typeface="Estrangelo Edessa"/><a:font script="Orya" typeface="Kalinga"/><a:font script="Mlym" typeface="Kartika"/><a:font script="Laoo" typeface="DokChampa"/><a:font script="Sinh" typeface="Iskoola Pota"/><a:font script="Mong" typeface="Mongolian Baiti"/><a:font script="Viet" typeface="Arial"/><a:font script="Uigh" typeface="Microsoft Uighur"/></a:minorFont></a:fontScheme><a:fmtScheme name="Office"><a:fillStyleLst><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:gradFill rotWithShape="1"><a:gsLst><a:gs pos="0"><a:schemeClr val="phClr"><a:tint val="50000"/><a:satMod val="300000"/></a:schemeClr></a:gs><a:gs pos="35000"><a:schemeClr val="phClr"><a:tint val="37000"/><a:satMod val="300000"/></a:schemeClr></a:gs><a:gs pos="100000"><a:schemeClr val="phClr"><a:tint val="15000"/><a:satMod val="350000"/></a:schemeClr></a:gs></a:gsLst><a:lin ang="16200000" scaled="1"/></a:gradFill><a:gradFill rotWithShape="1"><a:gsLst><a:gs pos="0"><a:schemeClr val="phClr"><a:shade val="51000"/><a:satMod val="130000"/></a:schemeClr></a:gs><a:gs pos="80000"><a:schemeClr val="phClr"><a:shade val="93000"/><a:satMod val="130000"/></a:schemeClr></a:gs><a:gs pos="100000"><a:schemeClr val="phClr"><a:shade val="94000"/><a:satMod val="135000"/></a:schemeClr></a:gs></a:gsLst><a:lin ang="16200000" scaled="0"/></a:gradFill></a:fillStyleLst><a:lnStyleLst><a:ln w="9525" cap="flat" cmpd="sng" algn="ctr"><a:solidFill><a:schemeClr val="phClr"><a:shade val="95000"/><a:satMod val="105000"/></a:schemeClr></a:solidFill><a:prstDash val="solid"/></a:ln><a:ln w="25400" cap="flat" cmpd="sng" algn="ctr"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:prstDash val="solid"/></a:ln><a:ln w="38100" cap="flat" cmpd="sng" algn="ctr"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:prstDash val="solid"/></a:ln></a:lnStyleLst><a:effectStyleLst><a:effectStyle><a:effectLst><a:outerShdw blurRad="40000" dist="20000" dir="5400000" rotWithShape="0"><a:srgbClr val="000000"><a:alpha val="38000"/></a:srgbClr></a:outerShdw></a:effectLst></a:effectStyle><a:effectStyle><a:effectLst><a:outerShdw blurRad="40000" dist="23000" dir="5400000" rotWithShape="0"><a:srgbClr val="000000"><a:alpha val="35000"/></a:srgbClr></a:outerShdw></a:effectLst></a:effectStyle><a:effectStyle><a:effectLst><a:outerShdw blurRad="40000" dist="23000" dir="5400000" rotWithShape="0"><a:srgbClr val="000000"><a:alpha val="35000"/></a:srgbClr></a:outerShdw></a:effectLst><a:scene3d><a:camera prst="orthographicFront"><a:rot lat="0" lon="0" rev="0"/></a:camera><a:lightRig rig="threePt" dir="t"><a:rot lat="0" lon="0" rev="1200000"/></a:lightRig></a:scene3d><a:sp3d><a:bevelT w="63500" h="25400"/></a:sp3d></a:effectStyle></a:effectStyleLst><a:bgFillStyleLst><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:gradFill rotWithShape="1"><a:gsLst><a:gs pos="0"><a:schemeClr val="phClr"><a:tint val="40000"/><a:satMod val="350000"/></a:schemeClr></a:gs><a:gs pos="40000"><a:schemeClr val="phClr"><a:tint val="45000"/><a:shade val="99000"/><a:satMod val="350000"/></a:schemeClr></a:gs><a:gs pos="100000"><a:schemeClr val="phClr"><a:shade val="20000"/><a:satMod val="255000"/></a:schemeClr></a:gs></a:gsLst><a:path path="circle"><a:fillToRect l="50000" t="-80000" r="50000" b="180000"/></a:path></a:gradFill><a:gradFill rotWithShape="1"><a:gsLst><a:gs pos="0"><a:schemeClr val="phClr"><a:tint val="80000"/><a:satMod val="300000"/></a:schemeClr></a:gs><a:gs pos="100000"><a:schemeClr val="phClr"><a:shade val="30000"/><a:satMod val="200000"/></a:schemeClr></a:gs></a:gsLst><a:path path="circle"><a:fillToRect l="50000" t="50000" r="50000" b="50000"/></a:path></a:gradFill></a:bgFillStyleLst></a:fmtScheme></a:themeElements><a:objectDefaults/><a:extraClrSchemeLst/></a:theme>',*/

    'xl/worksheets/sheet1.xml': '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheetViews><sheetView tabSelected="1" workbookViewId="0"><selection activeCell="A1" sqref="A1"/></sheetView></sheetViews><sheetFormatPr defaultRowHeight="13.5"/>\
__COLS_WIDTH__<sheetData>__DATA__</sheetData>\
<phoneticPr fontId="18" type="noConversion"/><pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/><pageSetup paperSize="9" orientation="portrait" r:id="rId1"/></worksheet>',

    'xl/styles.xml': '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="20"><font><sz val="11"/><color theme="1"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><sz val="11"/><color theme="1"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><b/><sz val="18"/><color theme="3"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="major"/></font><font><b/><sz val="15"/><color theme="3"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><b/><sz val="13"/><color theme="3"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><b/><sz val="11"/><color theme="3"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><sz val="11"/><color rgb="FF006100"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><sz val="11"/><color rgb="FF9C0006"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><sz val="11"/><color rgb="FF9C6500"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><sz val="11"/><color rgb="FF3F3F76"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><b/><sz val="11"/><color rgb="FF3F3F3F"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><b/><sz val="11"/><color rgb="FFFA7D00"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><sz val="11"/><color rgb="FFFA7D00"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><b/><sz val="11"/><color theme="0"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><sz val="11"/><color rgb="FFFF0000"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><i/><sz val="11"/><color rgb="FF7F7F7F"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><b/><sz val="11"/><color theme="1"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><sz val="11"/><color theme="0"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><sz val="9"/><name val="宋体"/><family val="2"/><charset val="134"/><scheme val="minor"/></font><font><b/><sz val="11"/><color theme="1"/><name val="宋体"/><family val="3"/><charset val="134"/><scheme val="minor"/></font></fonts><fills count="33"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFC6EFCE"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFFC7CE"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFFEB9C"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFFCC99"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF2F2F2"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFA5A5A5"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFFFFCC"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="4"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="4" tint="0.79998168889431442"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="4" tint="0.59999389629810485"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="4" tint="0.39997558519241921"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="5"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="5" tint="0.79998168889431442"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="5" tint="0.59999389629810485"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="5" tint="0.39997558519241921"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="6"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="6" tint="0.79998168889431442"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="6" tint="0.59999389629810485"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="6" tint="0.39997558519241921"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="7"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="7" tint="0.79998168889431442"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="7" tint="0.59999389629810485"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="7" tint="0.39997558519241921"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="8"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="8" tint="0.79998168889431442"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="8" tint="0.59999389629810485"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="8" tint="0.39997558519241921"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="9"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="9" tint="0.79998168889431442"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="9" tint="0.59999389629810485"/><bgColor indexed="65"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor theme="9" tint="0.39997558519241921"/><bgColor indexed="65"/></patternFill></fill></fills><borders count="10"><border><left/><right/><top/><bottom/><diagonal/></border><border><left/><right/><top/><bottom style="thick"><color theme="4"/></bottom><diagonal/></border><border><left/><right/><top/><bottom style="thick"><color theme="4" tint="0.499984740745262"/></bottom><diagonal/></border><border><left/><right/><top/><bottom style="medium"><color theme="4" tint="0.39997558519241921"/></bottom><diagonal/></border><border><left style="thin"><color rgb="FF7F7F7F"/></left><right style="thin"><color rgb="FF7F7F7F"/></right><top style="thin"><color rgb="FF7F7F7F"/></top><bottom style="thin"><color rgb="FF7F7F7F"/></bottom><diagonal/></border><border><left style="thin"><color rgb="FF3F3F3F"/></left><right style="thin"><color rgb="FF3F3F3F"/></right><top style="thin"><color rgb="FF3F3F3F"/></top><bottom style="thin"><color rgb="FF3F3F3F"/></bottom><diagonal/></border><border><left/><right/><top/><bottom style="double"><color rgb="FFFF8001"/></bottom><diagonal/></border><border><left style="double"><color rgb="FF3F3F3F"/></left><right style="double"><color rgb="FF3F3F3F"/></right><top style="double"><color rgb="FF3F3F3F"/></top><bottom style="double"><color rgb="FF3F3F3F"/></bottom><diagonal/></border><border><left style="thin"><color rgb="FFB2B2B2"/></left><right style="thin"><color rgb="FFB2B2B2"/></right><top style="thin"><color rgb="FFB2B2B2"/></top><bottom style="thin"><color rgb="FFB2B2B2"/></bottom><diagonal/></border><border><left/><right/><top style="thin"><color theme="4"/></top><bottom style="double"><color theme="4"/></bottom><diagonal/></border></borders><cellStyleXfs count="42"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="2" fillId="0" borderId="0" applyNumberFormat="0" applyFill="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="3" fillId="0" borderId="1" applyNumberFormat="0" applyFill="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="4" fillId="0" borderId="2" applyNumberFormat="0" applyFill="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="5" fillId="0" borderId="3" applyNumberFormat="0" applyFill="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="5" fillId="0" borderId="0" applyNumberFormat="0" applyFill="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="6" fillId="2" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="7" fillId="3" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="8" fillId="4" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="9" fillId="5" borderId="4" applyNumberFormat="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="10" fillId="6" borderId="5" applyNumberFormat="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="11" fillId="6" borderId="4" applyNumberFormat="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="12" fillId="0" borderId="6" applyNumberFormat="0" applyFill="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="13" fillId="7" borderId="7" applyNumberFormat="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="14" fillId="0" borderId="0" applyNumberFormat="0" applyFill="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="8" borderId="8" applyNumberFormat="0" applyFont="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="15" fillId="0" borderId="0" applyNumberFormat="0" applyFill="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="16" fillId="0" borderId="9" applyNumberFormat="0" applyFill="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="9" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="10" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="11" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="12" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="13" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="14" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="15" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="16" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="17" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="18" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="19" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="20" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="21" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="22" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="23" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="24" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="25" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="26" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="27" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="28" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="29" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="30" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="31" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="17" fillId="32" borderId="0" applyNumberFormat="0" applyBorder="0" applyAlignment="0" applyProtection="0"><alignment vertical="center"/></xf></cellStyleXfs><cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="19" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf></cellXfs><cellStyles count="42"><cellStyle name="20% - 强调文字颜色 1" xfId="19" builtinId="30" customBuiltin="1"/><cellStyle name="20% - 强调文字颜色 2" xfId="23" builtinId="34" customBuiltin="1"/><cellStyle name="20% - 强调文字颜色 3" xfId="27" builtinId="38" customBuiltin="1"/><cellStyle name="20% - 强调文字颜色 4" xfId="31" builtinId="42" customBuiltin="1"/><cellStyle name="20% - 强调文字颜色 5" xfId="35" builtinId="46" customBuiltin="1"/><cellStyle name="20% - 强调文字颜色 6" xfId="39" builtinId="50" customBuiltin="1"/><cellStyle name="40% - 强调文字颜色 1" xfId="20" builtinId="31" customBuiltin="1"/><cellStyle name="40% - 强调文字颜色 2" xfId="24" builtinId="35" customBuiltin="1"/><cellStyle name="40% - 强调文字颜色 3" xfId="28" builtinId="39" customBuiltin="1"/><cellStyle name="40% - 强调文字颜色 4" xfId="32" builtinId="43" customBuiltin="1"/><cellStyle name="40% - 强调文字颜色 5" xfId="36" builtinId="47" customBuiltin="1"/><cellStyle name="40% - 强调文字颜色 6" xfId="40" builtinId="51" customBuiltin="1"/><cellStyle name="60% - 强调文字颜色 1" xfId="21" builtinId="32" customBuiltin="1"/><cellStyle name="60% - 强调文字颜色 2" xfId="25" builtinId="36" customBuiltin="1"/><cellStyle name="60% - 强调文字颜色 3" xfId="29" builtinId="40" customBuiltin="1"/><cellStyle name="60% - 强调文字颜色 4" xfId="33" builtinId="44" customBuiltin="1"/><cellStyle name="60% - 强调文字颜色 5" xfId="37" builtinId="48" customBuiltin="1"/><cellStyle name="60% - 强调文字颜色 6" xfId="41" builtinId="52" customBuiltin="1"/><cellStyle name="标题" xfId="1" builtinId="15" customBuiltin="1"/><cellStyle name="标题 1" xfId="2" builtinId="16" customBuiltin="1"/><cellStyle name="标题 2" xfId="3" builtinId="17" customBuiltin="1"/><cellStyle name="标题 3" xfId="4" builtinId="18" customBuiltin="1"/><cellStyle name="标题 4" xfId="5" builtinId="19" customBuiltin="1"/><cellStyle name="差" xfId="7" builtinId="27" customBuiltin="1"/><cellStyle name="常规" xfId="0" builtinId="0"/><cellStyle name="好" xfId="6" builtinId="26" customBuiltin="1"/><cellStyle name="汇总" xfId="17" builtinId="25" customBuiltin="1"/><cellStyle name="计算" xfId="11" builtinId="22" customBuiltin="1"/><cellStyle name="检查单元格" xfId="13" builtinId="23" customBuiltin="1"/><cellStyle name="解释性文本" xfId="16" builtinId="53" customBuiltin="1"/><cellStyle name="警告文本" xfId="14" builtinId="11" customBuiltin="1"/><cellStyle name="链接单元格" xfId="12" builtinId="24" customBuiltin="1"/><cellStyle name="强调文字颜色 1" xfId="18" builtinId="29" customBuiltin="1"/><cellStyle name="强调文字颜色 2" xfId="22" builtinId="33" customBuiltin="1"/><cellStyle name="强调文字颜色 3" xfId="26" builtinId="37" customBuiltin="1"/><cellStyle name="强调文字颜色 4" xfId="30" builtinId="41" customBuiltin="1"/><cellStyle name="强调文字颜色 5" xfId="34" builtinId="45" customBuiltin="1"/><cellStyle name="强调文字颜色 6" xfId="38" builtinId="49" customBuiltin="1"/><cellStyle name="适中" xfId="8" builtinId="28" customBuiltin="1"/><cellStyle name="输出" xfId="10" builtinId="21" customBuiltin="1"/><cellStyle name="输入" xfId="9" builtinId="20" customBuiltin="1"/><cellStyle name="注释" xfId="15" builtinId="10" customBuiltin="1"/></cellStyles><dxfs count="0"/><tableStyles count="0" defaultTableStyle="TableStyleMedium9" defaultPivotStyle="PivotStyleLight16"/></styleSheet>',

    'xl/workbook.xml': '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><fileVersion appName="xl" lastEdited="4" lowestEdited="4" rupBuild="4505"/><workbookPr showInkAnnotation="0" autoCompressPictures="0" defaultThemeVersion="124226"/><bookViews><workbookView xWindow="0" yWindow="0" windowWidth="20730" windowHeight="11760" tabRatio="500"/></bookViews><sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets><calcPr calcId="124519"/></workbook>',

	'[Content_Types].xml': '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Override PartName="/xl/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/></Types>'
};


var generateExcel = function(data){
    var config={
        filename: data.filename,
        extension: '.xlsx',
        header: true,
        footer: false,
        titleAttr: 'Excel'
    };
    // Set the text
    var xml = '';
    var addRow = function (row, is_header) {
        var cells = [];

        for ( var i=0, ien=row.length ; i<ien ; i++ ) {
            if ( row[i] === null || row[i] === undefined ) {
                row[i] = '';
            }

            // Don't match numbers with leading zeros or a negative anywhere
            // but the start
            /*
            cells.push( typeof row[i] === 'number' || (row[i].match && row[i].match(/^-?[0-9\.]+$/) && row[i].charAt(0) !== '0') ?
                '<c t="n"><v>'+row[i]+'</v></c>' :
                '<c t="inlineStr"><is><t>'+(
                    ! row[i].replace ?
                        row[i] :
                        row[i]
                            .replace(/&(?!amp;)/g, '&amp;')
                            .replace(/[\x00-\x1F\x7F-\x9F]/g, ''))+ // remove control characters
                '</t></is></c>'                                    // they are not valid in XML
            );
            */
            cells.push('<c t="inlineStr"'+(is_header?' s="1"':'')+'><is><t>'+(
            // cells.push('<c t="inlineStr"><is><t>'+(
                    ! row[i].replace ?
                        row[i] :
                        row[i]
                            .replace(/&(?!amp;)/g, '&amp;')
                            .replace(/[\x00-\x1F\x7F-\x9F]/g, ''))+ // remove control characters
                '</t></is></c>'                                    // they are not valid in XML
            );
        }

        return '<row>'+cells.join('')+'</row>';
    };

    if ( config.header ) {
        xml += addRow( data.header, true);
    }

    for ( var i=0, ien=data.body.length ; i<ien ; i++ ) {
        xml += addRow( data.body[i] );
    }

    if ( config.footer ) {
        xml += addRow( data.footer );
    }

    var zip           = new window.JSZip();
    var _rels         = zip.folder("_rels");
    var xl            = zip.folder("xl");
    var xl_rels       = zip.folder("xl/_rels");
    var xl_theme      = zip.folder("xl/theme");
    var xl_worksheets = zip.folder("xl/worksheets");
    var xl_worksheets_rels = zip.folder("xl/worksheets/_rels");
    var docProps      = zip.folder('docProps');

    //设置各Excel报表列宽
    //xl\worksheets\sheet1.xml
    var cols_width = {
        'router': '<cols><col min="1" max="20" width="15.625" customWidth="1"/><col min="21" max="22" width="20.625" customWidth="1"/><col min="23" max="25" width="25.625" customWidth="1"/><col min="26" max="33" width="20.625" customWidth="1"/><col min="34" max="35" width="15.625" customWidth="1"/><col min="36" max="38" width="20.625" customWidth="1"/></cols>',
        'router_rx_m2m': '<cols><col min="1" max="1" width="15.625" customWidth="1"/><col min="2" max="2" width="16.625" customWidth="1"/><col min="3" max="3" width="15.375" customWidth="1"/><col min="4" max="4" width="32.875" customWidth="1"/><col min="5" max="5" width="16" customWidth="1"/><col min="6" max="6" width="26.625" customWidth="1"/><col min="7" max="7" width="16" customWidth="1"/><col min="8" max="9" width="22.875" customWidth="1"/><col min="10" max="11" width="21.625" customWidth="1"/><col min="12" max="12" width="17.125" customWidth="1"/><col min="13" max="13" width="16.625" customWidth="1"/><col min="14" max="14" width="20.375" customWidth="1"/><col min="15" max="16" width="19.125" customWidth="1"/><col min="17" max="17" width="12.875" customWidth="1"/><col min="18" max="18" width="16.625" customWidth="1"/><col min="19" max="19" width="10.375" customWidth="1"/><col min="20" max="21" width="23.625" customWidth="1"/><col min="22" max="23" width="21.5" customWidth="1"/><col min="24" max="25" width="30.375" customWidth="1"/><col min="26" max="26" width="17.125" customWidth="1"/><col min="27" max="27" width="22.625" customWidth="1"/><col min="28" max="28" width="17.125" customWidth="1"/><col min="29" max="29" width="19.125" customWidth="1"/><col min="30" max="30" width="17.875" customWidth="1"/><col min="31" max="31" width="12.875" style="1" customWidth="1"/><col min="32" max="32" width="16.625" customWidth="1"/><col min="33" max="33" width="22.625" customWidth="1"/><col min="34" max="34" width="16.625" customWidth="1"/><col min="35" max="35" width="19.125" customWidth="1"/><col min="36" max="36" width="17.875" customWidth="1"/><col min="37" max="37" width="12.875" customWidth="1"/><col min="38" max="39" width="20.625" customWidth="1"/></cols>',
        'flux': '<cols><col min="1" max="1" width="11.625" customWidth="1"/><col min="2" max="2" width="17.25" bestFit="1" customWidth="1"/><col min="3" max="3" width="14.125" bestFit="1" customWidth="1"/><col min="4" max="4" width="19.375" bestFit="1" customWidth="1"/><col min="5" max="5" width="9.75" bestFit="1" customWidth="1"/><col min="6" max="6" width="19.375" customWidth="1"/></cols>',
        'signal': '<cols><col customWidth="1" width="15.625" max="5" min="1"/></cols>',
        'netchange': '<cols><col min="1" max="2" width="15.625" customWidth="1"/><col min="3" max="3" width="23.625" customWidth="1"/><col min="4" max="4" width="21.625" bestFit="1" customWidth="1"/><col min="5" max="6" width="18.875" bestFit="1" customWidth="1"/><col min="7" max="8" width="22.625" bestFit="1" customWidth="1"/></cols>',
        'mobile': '<cols><col min="1" max="1" width="13.875" bestFit="1" customWidth="1"/><col min="2" max="2" width="21.625" bestFit="1" customWidth="1"/><col min="3" max="3" width="18.625" customWidth="1"/><col min="4" max="6" width="12.75" bestFit="1" customWidth="1"/><col min="7" max="7" width="17.25" bestFit="1" customWidth="1"/><col min="8" max="8" width="15" bestFit="1" customWidth="1"/><col min="9" max="9" width="12.75" bestFit="1" customWidth="1"/><col min="10" max="10" width="13.875" bestFit="1" customWidth="1"/><col min="11" max="11" width="18.375" bestFit="1" customWidth="1"/><col min="12" max="12" width="8.5" bestFit="1" customWidth="1"/></cols>',
        'offline_term': '<cols><col customWidth="1" width="17.625" max="1" min="1"/><col customWidth="1" width="14.875" max="2" min="2"/><col customWidth="1" width="14.125" max="3" min="3"/><col customWidth="1" width="17.875" max="5" min="4"/><col customWidth="1" width="21.625" max="8" min="6" bestFit="1"/></cols>',
        'stream_data': '<cols><col min="1" max="1" width="9.75" bestFit="1" customWidth="1"/><col min="2" max="2" width="5.75" bestFit="1" customWidth="1"/><col min="3" max="3" width="10.5" bestFit="1" customWidth="1"/><col min="4" max="4" width="11.25" bestFit="1" customWidth="1"/><col min="5" max="5" width="21.625" bestFit="1" customWidth="1"/></cols>',
        'alarm_record': '<cols><col min="1" max="3" width="17.625" customWidth="1"/><col min="4" max="4" width="21.625" customWidth="1"/><col min="5" max="5" width="36" customWidth="1"/><col min="6" max="6" width="21.625" customWidth="1"/><col min="7" max="8" width="17.625" customWidth="1"/><col min="9" max="12" width="21.625" customWidth="1"/></cols>',
        'rtu_history_data': '<cols><col min="1" max="2" width="21.5" customWidth="1"/><col min="3" max="3" width="11.5" customWidth="1"/><col min="4" max="4" width="13.75" customWidth="1"/><col min="5" max="5" width="19.375" customWidth="1"/><col min="6" max="6" width="9.875" customWidth="1"/><col min="7" max="7" width="11.75" customWidth="1"/><col min="8" max="8" width="17.125" customWidth="1"/><col min="9" max="9" width="10.625" customWidth="1"/><col min="10" max="10" width="12.25" customWidth="1"/><col min="11" max="11" width="14.375" customWidth="1"/><col min="12" max="12" width="17.75" customWidth="1"/><col min="13" max="13" width="9.375" customWidth="1"/><col min="14" max="14" width="9.75" customWidth="1"/><col min="15" max="15" width="11.5" customWidth="1"/><col min="16" max="16" width="13.75" customWidth="1"/><col min="17" max="17" width="11.5" customWidth="1"/><col min="18" max="20" width="9.75" customWidth="1"/><col min="21" max="23" width="21.5" customWidth="1"/><col min="24" max="24" width="12.625" customWidth="1"/><col min="25" max="25" width="17.125" customWidth="1"/><col min="26" max="26" width="22.625" customWidth="1"/><col min="27" max="27" width="17.125" customWidth="1"/><col min="28" max="29" width="9.375" customWidth="1"/><col min="30" max="30" width="12.625" customWidth="1"/><col min="31" max="31" width="11.5" customWidth="1"/><col min="32" max="32" width="8.375" customWidth="1"/><col min="33" max="33" width="13.125" customWidth="1"/><col min="34" max="34" width="21.5" customWidth="1"/><col min="35" max="35" width="9.375" customWidth="1"/><col min="36" max="36" width="21.875" customWidth="1"/><col min="37" max="37" width="10.125" customWidth="1"/></cols>',
        'import_sensor': '<cols><col min="1" max="3" width="15.625" customWidth="1"/><col min="4" max="4" width="25.625" customWidth="1"/><col min="5" max="7" width="15.625" customWidth="1"/><col min="8" max="8" width="25.625" customWidth="1"/><col min="9" max="9" width="15.625" customWidth="1"/></cols>',
        'lora': '<cols><col min="1" max="1" width="7.75" customWidth="1"/><col min="2" max="2" width="14" customWidth="1"/><col min="3" max="3" width="21.625" bestFit="1" customWidth="1"/><col min="4" max="5" width="15.625" customWidth="1"/></cols>',
        'logins': '<cols><col min="1" max="1" width="11.5" customWidth="1"/><col min="2" max="2" width="15" customWidth="1"/><col min="3" max="3" width="18.875" customWidth="1"/><col min="4" max="4" width="19.25" customWidth="1"/><col min="5" max="5" width="10.625" customWidth="1"/><col min="6" max="7" width="21.625" bestFit="1" customWidth="1"/><col min="8" max="8" width="10.625" customWidth="1"/><col min="9" max="9" width="13.5" customWidth="1"/></cols>',
        'offline_rate_report': '<cols><col min="1" max="2" width="20.625" style="1" customWidth="1"/><col min="3" max="3" width="39.125" style="1" customWidth="1"/><col min="4" max="4" width="32.875" style="1" customWidth="1"/><col min="5" max="5" width="26.625" style="1" customWidth="1"/></cols>',
        'mobile_report': '<cols><col min="1" max="1" width="20.625" customWidth="1"/><col min="2" max="6" width="15.625" customWidth="1"/><col min="7" max="8" width="20.625" customWidth="1"/><col min="9" max="20" width="15.625" customWidth="1"/><col min="21" max="22" width="20.625" customWidth="1"/><col min="23" max="25" width="25.625" customWidth="1"/><col min="26" max="33" width="20.625" customWidth="1"/><col min="34" max="35" width="15.625" customWidth="1"/><col min="36" max="38" width="20.625" customWidth="1"/></cols>'
    }

    _rels.file('.rels', excelStrings['_rels/.rels']);
    docProps.file('app.xml', excelStrings['docProps/app.xml']);
    docProps.file('core.xml', excelStrings['docProps/core.xml']);
    xl_rels.file('workbook.xml.rels', excelStrings['xl/_rels/workbook.xml.rels']);
    // xl_theme.file('theme1.xml', excelStrings['xl/theme/theme1.xml']);
    // xl_worksheets_rels.file('sheet1.xml.rels', excelStrings['xl/worksheets/_rels/sheet1.xml.rels']);
    xl_worksheets.file('sheet1.xml', excelStrings['xl/worksheets/sheet1.xml'].replace('__DATA__',xml).replace('__COLS_WIDTH__',cols_width[data.type]));
    xl.file('styles.xml', excelStrings['xl/styles.xml']);
    xl.file('workbook.xml', excelStrings['xl/workbook.xml']);
    zip.file('[Content_Types].xml', excelStrings['[Content_Types].xml']);


    _saveAs(
        zip.generate( {type:"blob"} ),
        _filename( config )
    );
};

var _filename = function ( config, incExtension )
{
	// Backwards compatibility
	var filename = config.filename === '*' && config.title !== '*' && config.title !== undefined ?
		config.title :
		config.filename;

	if ( filename.indexOf( '*' ) !== -1 ) {
		filename = filename.replace( '*', $('title').text() );
	}

	// Strip characters which the OS will object to
	filename = filename.replace(/[^a-zA-Z0-9_\u00A1-\uFFFF\.,\-_ !\(\)]/g, "");

	return incExtension === undefined || incExtension === true ?
		filename+config.extension :
		filename;
};