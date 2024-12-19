/*! jquery.cookie v1.4.1 | MIT */
/* 使用说明
a)设置新的cookie:

$.cookie('name'，'dumplings');  //设置一个值为'dumplings'的cookie
设置cookie的生命周期
 $.cookie('key', 'value', { expires: 7 }); //设置为7天，默认值：浏览器关闭

设置cookie的域名：
$.cookie('name'，'dumplings', {domain:'qq.com'});   //设置一个值为'dumplings'的在域名'qq.com'的cookie
设置cookie的路径：

$.cookie('name'，'dumplings', {domain:'qq.com'，path:'/'});
//设置一个值为'dumplings'的在域名'qq.com'的路径为'/'的cookie
b)删除cookie

$.removeCookie('name',{ path: '/'}); //path为指定路径，直接删除该路径下的cookie
$.cookie('name',null,{ path: '/'}); //将cookie名为‘openid’的值设置为空，实际已删除
c)获取cookie

$.cookie('name')   //dumplings
踩过的坑：
cookie的域名和路径都很重要，如果没有设置成一致，则会有不同域名下或者不同路径下的同名cookie，为了避免这种情况，建议在设置cookie和删除cookie的时候，配置路径和域名。
*/
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?a(require("jquery")):a(jQuery)}(function(a){function b(a){return h.raw?a:encodeURIComponent(a)}function c(a){return h.raw?a:decodeURIComponent(a)}function d(a){return b(h.json?JSON.stringify(a):String(a))}function e(a){0===a.indexOf('"')&&(a=a.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return a=decodeURIComponent(a.replace(g," ")),h.json?JSON.parse(a):a}catch(b){}}function f(b,c){var d=h.raw?b:e(b);return a.isFunction(c)?c(d):d}var g=/\+/g,h=a.cookie=function(e,g,i){if(void 0!==g&&!a.isFunction(g)){if(i=a.extend({},h.defaults,i),"number"==typeof i.expires){var j=i.expires,k=i.expires=new Date;k.setTime(+k+864e5*j)}return document.cookie=[b(e),"=",d(g),i.expires?"; expires="+i.expires.toUTCString():"",i.path?"; path="+i.path:"",i.domain?"; domain="+i.domain:"",i.secure?"; secure":""].join("")}for(var l=e?void 0:{},m=document.cookie?document.cookie.split("; "):[],n=0,o=m.length;o>n;n++){var p=m[n].split("="),q=c(p.shift()),r=p.join("=");if(e&&e===q){l=f(r,g);break}e||void 0===(r=f(r))||(l[q]=r)}return l};h.defaults={},a.removeCookie=function(b,c){return void 0===a.cookie(b)?!1:(a.cookie(b,"",a.extend({},c,{expires:-1})),!a.cookie(b))}});