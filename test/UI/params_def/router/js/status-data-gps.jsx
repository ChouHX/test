//<% nvram("gps_valid,gps_bds,gps_use,gps_date,gps_time,gps_latitude,gps_NS,gps_longitude,gps_EW,google_map"); %>

stats = { };

do {
	stats.gps_valid = nvram.gps_valid;
	stats.gps_bds = nvram.gps_bds;
	stats.gps_use = nvram.gps_use;
	stats.gps_use += ' <img src="bar' + MIN(MAX(Math.floor(nvram.gps_use / 2), 1), 6) + '.gif">';
	stats.gps_date = nvram.gps_date + ' - ' + nvram.gps_time;
	stats.gps_mesg = nvram.gps_latitude + nvram.gps_NS + ' - ' + nvram.gps_longitude + nvram.gps_EW;
	stats.gps_google_map = 'http://maps.google.com/maps?q=loc:'+nvram.google_map+'&hl=en&t=h&z=16output=html';
} while (0);

