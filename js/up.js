/*!
 * jQuery JavaScript Library v1.4
 * http://jquery.com/
 *
 * Copyright 2010, John Resig
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://docs.jquery.com/License
 *
 * Includes Sizzle.js
 * http://sizzlejs.com/
 * Copyright 2010, The Dojo Foundation
 * Released under the MIT, BSD, and GPL Licenses.
 *
 * Date: Wed Jan 13 15:23:05 2010 -0500
 */
(function(A,w){function oa(){if(!c.isReady){try{s.documentElement.doScroll("left")}catch(a){setTimeout(oa,1);return}c.ready()}}function La(a,b){b.src?c.ajax({url:b.src,async:false,dataType:"script"}):c.globalEval(b.text||b.textContent||b.innerHTML||"");b.parentNode&&b.parentNode.removeChild(b)}function $(a,b,d,f,e,i){var j=a.length;if(typeof b==="object"){for(var o in b)$(a,o,b[o],f,e,d);return a}if(d!==w){f=!i&&f&&c.isFunction(d);for(o=0;o<j;o++)e(a[o],b,f?d.call(a[o],o,e(a[o],b)):d,i);return a}return j?
e(a[0],b):null}function K(){return(new Date).getTime()}function aa(){return false}function ba(){return true}function pa(a,b,d){d[0].type=a;return c.event.handle.apply(b,d)}function qa(a){var b=true,d=[],f=[],e=arguments,i,j,o,p,n,t=c.extend({},c.data(this,"events").live);for(p in t){j=t[p];if(j.live===a.type||j.altLive&&c.inArray(a.type,j.altLive)>-1){i=j.data;i.beforeFilter&&i.beforeFilter[a.type]&&!i.beforeFilter[a.type](a)||f.push(j.selector)}else delete t[p]}i=c(a.target).closest(f,a.currentTarget);
n=0;for(l=i.length;n<l;n++)for(p in t){j=t[p];o=i[n].elem;f=null;if(i[n].selector===j.selector){if(j.live==="mouseenter"||j.live==="mouseleave")f=c(a.relatedTarget).closest(j.selector)[0];if(!f||f!==o)d.push({elem:o,fn:j})}}n=0;for(l=d.length;n<l;n++){i=d[n];a.currentTarget=i.elem;a.data=i.fn.data;if(i.fn.apply(i.elem,e)===false){b=false;break}}return b}function ra(a,b){return["live",a,b.replace(/\./g,"`").replace(/ /g,"&")].join(".")}function sa(a){return!a||!a.parentNode||a.parentNode.nodeType===
11}function ta(a,b){var d=0;b.each(function(){if(this.nodeName===(a[d]&&a[d].nodeName)){var f=c.data(a[d++]),e=c.data(this,f);if(f=f&&f.events){delete e.handle;e.events={};for(var i in f)for(var j in f[i])c.event.add(this,i,f[i][j],f[i][j].data)}}})}function ua(a,b,d){var f,e,i;if(a.length===1&&typeof a[0]==="string"&&a[0].length<512&&a[0].indexOf("<option")<0){e=true;if(i=c.fragments[a[0]])if(i!==1)f=i}if(!f){b=b&&b[0]?b[0].ownerDocument||b[0]:s;f=b.createDocumentFragment();c.clean(a,b,f,d)}if(e)c.fragments[a[0]]=
i?f:1;return{fragment:f,cacheable:e}}function T(a){for(var b=0,d,f;(d=a[b])!=null;b++)if(!c.noData[d.nodeName.toLowerCase()]&&(f=d[H]))delete c.cache[f]}function L(a,b){var d={};c.each(va.concat.apply([],va.slice(0,b)),function(){d[this]=a});return d}function wa(a){return"scrollTo"in a&&a.document?a:a.nodeType===9?a.defaultView||a.parentWindow:false}var c=function(a,b){return new c.fn.init(a,b)},Ma=A.jQuery,Na=A.$,s=A.document,U,Oa=/^[^<]*(<[\w\W]+>)[^>]*$|^#([\w-]+)$/,Pa=/^.[^:#\[\.,]*$/,Qa=/\S/,
Ra=/^(\s|\u00A0)+|(\s|\u00A0)+$/g,Sa=/^<(\w+)\s*\/?>(?:<\/\1>)?$/,P=navigator.userAgent,xa=false,Q=[],M,ca=Object.prototype.toString,da=Object.prototype.hasOwnProperty,ea=Array.prototype.push,R=Array.prototype.slice,V=Array.prototype.indexOf;c.fn=c.prototype={init:function(a,b){var d,f;if(!a)return this;if(a.nodeType){this.context=this[0]=a;this.length=1;return this}if(typeof a==="string")if((d=Oa.exec(a))&&(d[1]||!b))if(d[1]){f=b?b.ownerDocument||b:s;if(a=Sa.exec(a))if(c.isPlainObject(b)){a=[s.createElement(a[1])];
c.fn.attr.call(a,b,true)}else a=[f.createElement(a[1])];else{a=ua([d[1]],[f]);a=(a.cacheable?a.fragment.cloneNode(true):a.fragment).childNodes}}else{if(b=s.getElementById(d[2])){if(b.id!==d[2])return U.find(a);this.length=1;this[0]=b}this.context=s;this.selector=a;return this}else if(!b&&/^\w+$/.test(a)){this.selector=a;this.context=s;a=s.getElementsByTagName(a)}else return!b||b.jquery?(b||U).find(a):c(b).find(a);else if(c.isFunction(a))return U.ready(a);if(a.selector!==w){this.selector=a.selector;
this.context=a.context}return c.isArray(a)?this.setArray(a):c.makeArray(a,this)},selector:"",jquery:"1.4",length:0,size:function(){return this.length},toArray:function(){return R.call(this,0)},get:function(a){return a==null?this.toArray():a<0?this.slice(a)[0]:this[a]},pushStack:function(a,b,d){a=c(a||null);a.prevObject=this;a.context=this.context;if(b==="find")a.selector=this.selector+(this.selector?" ":"")+d;else if(b)a.selector=this.selector+"."+b+"("+d+")";return a},setArray:function(a){this.length=
0;ea.apply(this,a);return this},each:function(a,b){return c.each(this,a,b)},ready:function(a){c.bindReady();if(c.isReady)a.call(s,c);else Q&&Q.push(a);return this},eq:function(a){return a===-1?this.slice(a):this.slice(a,+a+1)},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},slice:function(){return this.pushStack(R.apply(this,arguments),"slice",R.call(arguments).join(","))},map:function(a){return this.pushStack(c.map(this,function(b,d){return a.call(b,d,b)}))},end:function(){return this.prevObject||
c(null)},push:ea,sort:[].sort,splice:[].splice};c.fn.init.prototype=c.fn;c.extend=c.fn.extend=function(){var a=arguments[0]||{},b=1,d=arguments.length,f=false,e,i,j,o;if(typeof a==="boolean"){f=a;a=arguments[1]||{};b=2}if(typeof a!=="object"&&!c.isFunction(a))a={};if(d===b){a=this;--b}for(;b<d;b++)if((e=arguments[b])!=null)for(i in e){j=a[i];o=e[i];if(a!==o)if(f&&o&&(c.isPlainObject(o)||c.isArray(o))){j=j&&(c.isPlainObject(j)||c.isArray(j))?j:c.isArray(o)?[]:{};a[i]=c.extend(f,j,o)}else if(o!==w)a[i]=
o}return a};c.extend({noConflict:function(a){A.$=Na;if(a)A.jQuery=Ma;return c},isReady:false,ready:function(){if(!c.isReady){if(!s.body)return setTimeout(c.ready,13);c.isReady=true;if(Q){for(var a,b=0;a=Q[b++];)a.call(s,c);Q=null}c.fn.triggerHandler&&c(s).triggerHandler("ready")}},bindReady:function(){if(!xa){xa=true;if(s.readyState==="complete")return c.ready();if(s.addEventListener){s.addEventListener("DOMContentLoaded",M,false);A.addEventListener("load",c.ready,false)}else if(s.attachEvent){s.attachEvent("onreadystatechange",
M);A.attachEvent("onload",c.ready);var a=false;try{a=A.frameElement==null}catch(b){}s.documentElement.doScroll&&a&&oa()}}},isFunction:function(a){return ca.call(a)==="[object Function]"},isArray:function(a){return ca.call(a)==="[object Array]"},isPlainObject:function(a){if(!a||ca.call(a)!=="[object Object]"||a.nodeType||a.setInterval)return false;if(a.constructor&&!da.call(a,"constructor")&&!da.call(a.constructor.prototype,"isPrototypeOf"))return false;var b;for(b in a);return b===w||da.call(a,b)},
isEmptyObject:function(a){for(var b in a)return false;return true},noop:function(){},globalEval:function(a){if(a&&Qa.test(a)){var b=s.getElementsByTagName("head")[0]||s.documentElement,d=s.createElement("script");d.type="text/javascript";if(c.support.scriptEval)d.appendChild(s.createTextNode(a));else d.text=a;b.insertBefore(d,b.firstChild);b.removeChild(d)}},nodeName:function(a,b){return a.nodeName&&a.nodeName.toUpperCase()===b.toUpperCase()},each:function(a,b,d){var f,e=0,i=a.length,j=i===w||c.isFunction(a);
if(d)if(j)for(f in a){if(b.apply(a[f],d)===false)break}else for(;e<i;){if(b.apply(a[e++],d)===false)break}else if(j)for(f in a){if(b.call(a[f],f,a[f])===false)break}else for(d=a[0];e<i&&b.call(d,e,d)!==false;d=a[++e]);return a},trim:function(a){return(a||"").replace(Ra,"")},makeArray:function(a,b){b=b||[];if(a!=null)a.length==null||typeof a==="string"||c.isFunction(a)||typeof a!=="function"&&a.setInterval?ea.call(b,a):c.merge(b,a);return b},inArray:function(a,b){if(b.indexOf)return b.indexOf(a);for(var d=
0,f=b.length;d<f;d++)if(b[d]===a)return d;return-1},merge:function(a,b){var d=a.length,f=0;if(typeof b.length==="number")for(var e=b.length;f<e;f++)a[d++]=b[f];else for(;b[f]!==w;)a[d++]=b[f++];a.length=d;return a},grep:function(a,b,d){for(var f=[],e=0,i=a.length;e<i;e++)!d!==!b(a[e],e)&&f.push(a[e]);return f},map:function(a,b,d){for(var f=[],e,i=0,j=a.length;i<j;i++){e=b(a[i],i,d);if(e!=null)f[f.length]=e}return f.concat.apply([],f)},guid:1,proxy:function(a,b,d){if(arguments.length===2)if(typeof b===
"string"){d=a;a=d[b];b=w}else if(b&&!c.isFunction(b)){d=b;b=w}if(!b&&a)b=function(){return a.apply(d||this,arguments)};if(a)b.guid=a.guid=a.guid||b.guid||c.guid++;return b},uaMatch:function(a){var b={browser:""};a=a.toLowerCase();if(/webkit/.test(a))b={browser:"webkit",version:/webkit[\/ ]([\w.]+)/};else if(/opera/.test(a))b={browser:"opera",version:/version/.test(a)?/version[\/ ]([\w.]+)/:/opera[\/ ]([\w.]+)/};else if(/msie/.test(a))b={browser:"msie",version:/msie ([\w.]+)/};else if(/mozilla/.test(a)&&
!/compatible/.test(a))b={browser:"mozilla",version:/rv:([\w.]+)/};b.version=(b.version&&b.version.exec(a)||[0,"0"])[1];return b},browser:{}});P=c.uaMatch(P);if(P.browser){c.browser[P.browser]=true;c.browser.version=P.version}if(c.browser.webkit)c.browser.safari=true;if(V)c.inArray=function(a,b){return V.call(b,a)};U=c(s);if(s.addEventListener)M=function(){s.removeEventListener("DOMContentLoaded",M,false);c.ready()};else if(s.attachEvent)M=function(){if(s.readyState==="complete"){s.detachEvent("onreadystatechange",
M);c.ready()}};if(V)c.inArray=function(a,b){return V.call(b,a)};(function(){c.support={};var a=s.documentElement,b=s.createElement("script"),d=s.createElement("div"),f="script"+K();d.style.display="none";d.innerHTML="   <link/><table></table><a href='/a' style='color:red;float:left;opacity:.55;'>a</a><input type='checkbox'/>";var e=d.getElementsByTagName("*"),i=d.getElementsByTagName("a")[0];if(!(!e||!e.length||!i)){c.support={leadingWhitespace:d.firstChild.nodeType===3,tbody:!d.getElementsByTagName("tbody").length,
htmlSerialize:!!d.getElementsByTagName("link").length,style:/red/.test(i.getAttribute("style")),hrefNormalized:i.getAttribute("href")==="/a",opacity:/^0.55$/.test(i.style.opacity),cssFloat:!!i.style.cssFloat,checkOn:d.getElementsByTagName("input")[0].value==="on",optSelected:s.createElement("select").appendChild(s.createElement("option")).selected,scriptEval:false,noCloneEvent:true,boxModel:null};b.type="text/javascript";try{b.appendChild(s.createTextNode("window."+f+"=1;"))}catch(j){}a.insertBefore(b,
a.firstChild);if(A[f]){c.support.scriptEval=true;delete A[f]}a.removeChild(b);if(d.attachEvent&&d.fireEvent){d.attachEvent("onclick",function o(){c.support.noCloneEvent=false;d.detachEvent("onclick",o)});d.cloneNode(true).fireEvent("onclick")}c(function(){var o=s.createElement("div");o.style.width=o.style.paddingLeft="1px";s.body.appendChild(o);c.boxModel=c.support.boxModel=o.offsetWidth===2;s.body.removeChild(o).style.display="none"});a=function(o){var p=s.createElement("div");o="on"+o;var n=o in
p;if(!n){p.setAttribute(o,"return;");n=typeof p[o]==="function"}return n};c.support.submitBubbles=a("submit");c.support.changeBubbles=a("change");a=b=d=e=i=null}})();c.props={"for":"htmlFor","class":"className",readonly:"readOnly",maxlength:"maxLength",cellspacing:"cellSpacing",rowspan:"rowSpan",colspan:"colSpan",tabindex:"tabIndex",usemap:"useMap",frameborder:"frameBorder"};var H="jQuery"+K(),Ta=0,ya={},Ua={};c.extend({cache:{},expando:H,noData:{embed:true,object:true,applet:true},data:function(a,
b,d){if(!(a.nodeName&&c.noData[a.nodeName.toLowerCase()])){a=a==A?ya:a;var f=a[H],e=c.cache;if(!b&&!f)return null;f||(f=++Ta);if(typeof b==="object"){a[H]=f;e=e[f]=c.extend(true,{},b)}else e=e[f]?e[f]:typeof d==="undefined"?Ua:(e[f]={});if(d!==w){a[H]=f;e[b]=d}return typeof b==="string"?e[b]:e}},removeData:function(a,b){if(!(a.nodeName&&c.noData[a.nodeName.toLowerCase()])){a=a==A?ya:a;var d=a[H],f=c.cache,e=f[d];if(b){if(e){delete e[b];c.isEmptyObject(e)&&c.removeData(a)}}else{try{delete a[H]}catch(i){a.removeAttribute&&
a.removeAttribute(H)}delete f[d]}}}});c.fn.extend({data:function(a,b){if(typeof a==="undefined"&&this.length)return c.data(this[0]);else if(typeof a==="object")return this.each(function(){c.data(this,a)});var d=a.split(".");d[1]=d[1]?"."+d[1]:"";if(b===w){var f=this.triggerHandler("getData"+d[1]+"!",[d[0]]);if(f===w&&this.length)f=c.data(this[0],a);return f===w&&d[1]?this.data(d[0]):f}else return this.trigger("setData"+d[1]+"!",[d[0],b]).each(function(){c.data(this,a,b)})},removeData:function(a){return this.each(function(){c.removeData(this,
a)})}});c.extend({queue:function(a,b,d){if(a){b=(b||"fx")+"queue";var f=c.data(a,b);if(!d)return f||[];if(!f||c.isArray(d))f=c.data(a,b,c.makeArray(d));else f.push(d);return f}},dequeue:function(a,b){b=b||"fx";var d=c.queue(a,b),f=d.shift();if(f==="inprogress")f=d.shift();if(f){b==="fx"&&d.unshift("inprogress");f.call(a,function(){c.dequeue(a,b)})}}});c.fn.extend({queue:function(a,b){if(typeof a!=="string"){b=a;a="fx"}if(b===w)return c.queue(this[0],a);return this.each(function(){var d=c.queue(this,
a,b);a==="fx"&&d[0]!=="inprogress"&&c.dequeue(this,a)})},dequeue:function(a){return this.each(function(){c.dequeue(this,a)})},delay:function(a,b){a=c.fx?c.fx.speeds[a]||a:a;b=b||"fx";return this.queue(b,function(){var d=this;setTimeout(function(){c.dequeue(d,b)},a)})},clearQueue:function(a){return this.queue(a||"fx",[])}});var za=/[\n\t]/g,fa=/\s+/,Va=/\r/g,Wa=/href|src|style/,Xa=/(button|input)/i,Ya=/(button|input|object|select|textarea)/i,Za=/^(a|area)$/i,Aa=/radio|checkbox/;c.fn.extend({attr:function(a,
b){return $(this,a,b,true,c.attr)},removeAttr:function(a){return this.each(function(){c.attr(this,a,"");this.nodeType===1&&this.removeAttribute(a)})},addClass:function(a){if(c.isFunction(a))return this.each(function(p){var n=c(this);n.addClass(a.call(this,p,n.attr("class")))});if(a&&typeof a==="string")for(var b=(a||"").split(fa),d=0,f=this.length;d<f;d++){var e=this[d];if(e.nodeType===1)if(e.className)for(var i=" "+e.className+" ",j=0,o=b.length;j<o;j++){if(i.indexOf(" "+b[j]+" ")<0)e.className+=
" "+b[j]}else e.className=a}return this},removeClass:function(a){if(c.isFunction(a))return this.each(function(p){var n=c(this);n.removeClass(a.call(this,p,n.attr("class")))});if(a&&typeof a==="string"||a===w)for(var b=(a||"").split(fa),d=0,f=this.length;d<f;d++){var e=this[d];if(e.nodeType===1&&e.className)if(a){for(var i=(" "+e.className+" ").replace(za," "),j=0,o=b.length;j<o;j++)i=i.replace(" "+b[j]+" "," ");e.className=i.substring(1,i.length-1)}else e.className=""}return this},toggleClass:function(a,
b){var d=typeof a,f=typeof b==="boolean";if(c.isFunction(a))return this.each(function(e){var i=c(this);i.toggleClass(a.call(this,e,i.attr("class"),b),b)});return this.each(function(){if(d==="string")for(var e,i=0,j=c(this),o=b,p=a.split(fa);e=p[i++];){o=f?o:!j.hasClass(e);j[o?"addClass":"removeClass"](e)}else if(d==="undefined"||d==="boolean"){this.className&&c.data(this,"__className__",this.className);this.className=this.className||a===false?"":c.data(this,"__className__")||""}})},hasClass:function(a){a=
" "+a+" ";for(var b=0,d=this.length;b<d;b++)if((" "+this[b].className+" ").replace(za," ").indexOf(a)>-1)return true;return false},val:function(a){if(a===w){var b=this[0];if(b){if(c.nodeName(b,"option"))return(b.attributes.value||{}).specified?b.value:b.text;if(c.nodeName(b,"select")){var d=b.selectedIndex,f=[],e=b.options;b=b.type==="select-one";if(d<0)return null;var i=b?d:0;for(d=b?d+1:e.length;i<d;i++){var j=e[i];if(j.selected){a=c(j).val();if(b)return a;f.push(a)}}return f}if(Aa.test(b.type)&&
!c.support.checkOn)return b.getAttribute("value")===null?"on":b.value;return(b.value||"").replace(Va,"")}return w}var o=c.isFunction(a);return this.each(function(p){var n=c(this),t=a;if(this.nodeType===1){if(o)t=a.call(this,p,n.val());if(typeof t==="number")t+="";if(c.isArray(t)&&Aa.test(this.type))this.checked=c.inArray(n.val(),t)>=0;else if(c.nodeName(this,"select")){var z=c.makeArray(t);c("option",this).each(function(){this.selected=c.inArray(c(this).val(),z)>=0});if(!z.length)this.selectedIndex=
-1}else this.value=t}})}});c.extend({attrFn:{val:true,css:true,html:true,text:true,data:true,width:true,height:true,offset:true},attr:function(a,b,d,f){if(!a||a.nodeType===3||a.nodeType===8)return w;if(f&&b in c.attrFn)return c(a)[b](d);f=a.nodeType!==1||!c.isXMLDoc(a);var e=d!==w;b=f&&c.props[b]||b;if(a.nodeType===1){var i=Wa.test(b);if(b in a&&f&&!i){if(e){if(b==="type"&&Xa.test(a.nodeName)&&a.parentNode)throw"type property can't be changed";a[b]=d}if(c.nodeName(a,"form")&&a.getAttributeNode(b))return a.getAttributeNode(b).nodeValue;
if(b==="tabIndex")return(b=a.getAttributeNode("tabIndex"))&&b.specified?b.value:Ya.test(a.nodeName)||Za.test(a.nodeName)&&a.href?0:w;return a[b]}if(!c.support.style&&f&&b==="style"){if(e)a.style.cssText=""+d;return a.style.cssText}e&&a.setAttribute(b,""+d);a=!c.support.hrefNormalized&&f&&i?a.getAttribute(b,2):a.getAttribute(b);return a===null?w:a}return c.style(a,b,d)}});var $a=function(a){return a.replace(/[^\w\s\.\|`]/g,function(b){return"\\"+b})};c.event={add:function(a,b,d,f){if(!(a.nodeType===
3||a.nodeType===8)){if(a.setInterval&&a!==A&&!a.frameElement)a=A;if(!d.guid)d.guid=c.guid++;if(f!==w){d=c.proxy(d);d.data=f}var e=c.data(a,"events")||c.data(a,"events",{}),i=c.data(a,"handle"),j;if(!i){j=function(){return typeof c!=="undefined"&&!c.event.triggered?c.event.handle.apply(j.elem,arguments):w};i=c.data(a,"handle",j)}if(i){i.elem=a;b=b.split(/\s+/);for(var o,p=0;o=b[p++];){var n=o.split(".");o=n.shift();d.type=n.slice(0).sort().join(".");var t=e[o],z=this.special[o]||{};if(!t){t=e[o]={};
if(!z.setup||z.setup.call(a,f,n,d)===false)if(a.addEventListener)a.addEventListener(o,i,false);else a.attachEvent&&a.attachEvent("on"+o,i)}if(z.add)if((n=z.add.call(a,d,f,n,t))&&c.isFunction(n)){n.guid=n.guid||d.guid;d=n}t[d.guid]=d;this.global[o]=true}a=null}}},global:{},remove:function(a,b,d){if(!(a.nodeType===3||a.nodeType===8)){var f=c.data(a,"events"),e,i,j;if(f){if(b===w||typeof b==="string"&&b.charAt(0)===".")for(i in f)this.remove(a,i+(b||""));else{if(b.type){d=b.handler;b=b.type}b=b.split(/\s+/);
for(var o=0;i=b[o++];){var p=i.split(".");i=p.shift();var n=!p.length,t=c.map(p.slice(0).sort(),$a);t=new RegExp("(^|\\.)"+t.join("\\.(?:.*\\.)?")+"(\\.|$)");var z=this.special[i]||{};if(f[i]){if(d){j=f[i][d.guid];delete f[i][d.guid]}else for(var B in f[i])if(n||t.test(f[i][B].type))delete f[i][B];z.remove&&z.remove.call(a,p,j);for(e in f[i])break;if(!e){if(!z.teardown||z.teardown.call(a,p)===false)if(a.removeEventListener)a.removeEventListener(i,c.data(a,"handle"),false);else a.detachEvent&&a.detachEvent("on"+
i,c.data(a,"handle"));e=null;delete f[i]}}}}for(e in f)break;if(!e){if(B=c.data(a,"handle"))B.elem=null;c.removeData(a,"events");c.removeData(a,"handle")}}}},trigger:function(a,b,d,f){var e=a.type||a;if(!f){a=typeof a==="object"?a[H]?a:c.extend(c.Event(e),a):c.Event(e);if(e.indexOf("!")>=0){a.type=e=e.slice(0,-1);a.exclusive=true}if(!d){a.stopPropagation();this.global[e]&&c.each(c.cache,function(){this.events&&this.events[e]&&c.event.trigger(a,b,this.handle.elem)})}if(!d||d.nodeType===3||d.nodeType===
8)return w;a.result=w;a.target=d;b=c.makeArray(b);b.unshift(a)}a.currentTarget=d;var i=c.data(d,"handle");i&&i.apply(d,b);var j,o;try{if(!(d&&d.nodeName&&c.noData[d.nodeName.toLowerCase()])){j=d[e];o=d["on"+e]}}catch(p){}i=c.nodeName(d,"a")&&e==="click";if(!f&&j&&!a.isDefaultPrevented()&&!i){this.triggered=true;try{d[e]()}catch(n){}}else if(o&&d["on"+e].apply(d,b)===false)a.result=false;this.triggered=false;if(!a.isPropagationStopped())(d=d.parentNode||d.ownerDocument)&&c.event.trigger(a,b,d,true)},
handle:function(a){var b,d;a=arguments[0]=c.event.fix(a||A.event);a.currentTarget=this;d=a.type.split(".");a.type=d.shift();b=!d.length&&!a.exclusive;var f=new RegExp("(^|\\.)"+d.slice(0).sort().join("\\.(?:.*\\.)?")+"(\\.|$)");d=(c.data(this,"events")||{})[a.type];for(var e in d){var i=d[e];if(b||f.test(i.type)){a.handler=i;a.data=i.data;i=i.apply(this,arguments);if(i!==w){a.result=i;if(i===false){a.preventDefault();a.stopPropagation()}}if(a.isImmediatePropagationStopped())break}}return a.result},
props:"altKey attrChange attrName bubbles button cancelable charCode clientX clientY ctrlKey currentTarget data detail eventPhase fromElement handler keyCode layerX layerY metaKey newValue offsetX offsetY originalTarget pageX pageY prevValue relatedNode relatedTarget screenX screenY shiftKey srcElement target toElement view wheelDelta which".split(" "),fix:function(a){if(a[H])return a;var b=a;a=c.Event(b);for(var d=this.props.length,f;d;){f=this.props[--d];a[f]=b[f]}if(!a.target)a.target=a.srcElement||
s;if(a.target.nodeType===3)a.target=a.target.parentNode;if(!a.relatedTarget&&a.fromElement)a.relatedTarget=a.fromElement===a.target?a.toElement:a.fromElement;if(a.pageX==null&&a.clientX!=null){b=s.documentElement;d=s.body;a.pageX=a.clientX+(b&&b.scrollLeft||d&&d.scrollLeft||0)-(b&&b.clientLeft||d&&d.clientLeft||0);a.pageY=a.clientY+(b&&b.scrollTop||d&&d.scrollTop||0)-(b&&b.clientTop||d&&d.clientTop||0)}if(!a.which&&(a.charCode||a.charCode===0?a.charCode:a.keyCode))a.which=a.charCode||a.keyCode;if(!a.metaKey&&
a.ctrlKey)a.metaKey=a.ctrlKey;if(!a.which&&a.button!==w)a.which=a.button&1?1:a.button&2?3:a.button&4?2:0;return a},guid:1E8,proxy:c.proxy,special:{ready:{setup:c.bindReady,teardown:c.noop},live:{add:function(a,b){c.extend(a,b||{});a.guid+=b.selector+b.live;c.event.add(this,b.live,qa,b)},remove:function(a){if(a.length){var b=0,d=new RegExp("(^|\\.)"+a[0]+"(\\.|$)");c.each(c.data(this,"events").live||{},function(){d.test(this.type)&&b++});b<1&&c.event.remove(this,a[0],qa)}},special:{}},beforeunload:{setup:function(a,
b,d){if(this.setInterval)this.onbeforeunload=d;return false},teardown:function(a,b){if(this.onbeforeunload===b)this.onbeforeunload=null}}}};c.Event=function(a){if(!this.preventDefault)return new c.Event(a);if(a&&a.type){this.originalEvent=a;this.type=a.type}else this.type=a;this.timeStamp=K();this[H]=true};c.Event.prototype={preventDefault:function(){this.isDefaultPrevented=ba;var a=this.originalEvent;if(a){a.preventDefault&&a.preventDefault();a.returnValue=false}},stopPropagation:function(){this.isPropagationStopped=
ba;var a=this.originalEvent;if(a){a.stopPropagation&&a.stopPropagation();a.cancelBubble=true}},stopImmediatePropagation:function(){this.isImmediatePropagationStopped=ba;this.stopPropagation()},isDefaultPrevented:aa,isPropagationStopped:aa,isImmediatePropagationStopped:aa};var Ba=function(a){for(var b=a.relatedTarget;b&&b!==this;)try{b=b.parentNode}catch(d){break}if(b!==this){a.type=a.data;c.event.handle.apply(this,arguments)}},Ca=function(a){a.type=a.data;c.event.handle.apply(this,arguments)};c.each({mouseenter:"mouseover",
mouseleave:"mouseout"},function(a,b){c.event.special[a]={setup:function(d){c.event.add(this,b,d&&d.selector?Ca:Ba,a)},teardown:function(d){c.event.remove(this,b,d&&d.selector?Ca:Ba)}}});if(!c.support.submitBubbles)c.event.special.submit={setup:function(a,b,d){if(this.nodeName.toLowerCase()!=="form"){c.event.add(this,"click.specialSubmit."+d.guid,function(f){var e=f.target,i=e.type;if((i==="submit"||i==="image")&&c(e).closest("form").length)return pa("submit",this,arguments)});c.event.add(this,"keypress.specialSubmit."+
d.guid,function(f){var e=f.target,i=e.type;if((i==="text"||i==="password")&&c(e).closest("form").length&&f.keyCode===13)return pa("submit",this,arguments)})}else return false},remove:function(a,b){c.event.remove(this,"click.specialSubmit"+(b?"."+b.guid:""));c.event.remove(this,"keypress.specialSubmit"+(b?"."+b.guid:""))}};if(!c.support.changeBubbles){var ga=/textarea|input|select/i;function Da(a){var b=a.type,d=a.value;if(b==="radio"||b==="checkbox")d=a.checked;else if(b==="select-multiple")d=a.selectedIndex>
-1?c.map(a.options,function(f){return f.selected}).join("-"):"";else if(a.nodeName.toLowerCase()==="select")d=a.selectedIndex;return d}function ha(a,b){var d=a.target,f,e;if(!(!ga.test(d.nodeName)||d.readOnly)){f=c.data(d,"_change_data");e=Da(d);if(e!==f){if(a.type!=="focusout"||d.type!=="radio")c.data(d,"_change_data",e);if(d.type!=="select"&&(f!=null||e)){a.type="change";return c.event.trigger(a,b,this)}}}}c.event.special.change={filters:{focusout:ha,click:function(a){var b=a.target,d=b.type;if(d===
"radio"||d==="checkbox"||b.nodeName.toLowerCase()==="select")return ha.call(this,a)},keydown:function(a){var b=a.target,d=b.type;if(a.keyCode===13&&b.nodeName.toLowerCase()!=="textarea"||a.keyCode===32&&(d==="checkbox"||d==="radio")||d==="select-multiple")return ha.call(this,a)},beforeactivate:function(a){a=a.target;a.nodeName.toLowerCase()==="input"&&a.type==="radio"&&c.data(a,"_change_data",Da(a))}},setup:function(a,b,d){for(var f in W)c.event.add(this,f+".specialChange."+d.guid,W[f]);return ga.test(this.nodeName)},
remove:function(a,b){for(var d in W)c.event.remove(this,d+".specialChange"+(b?"."+b.guid:""),W[d]);return ga.test(this.nodeName)}};var W=c.event.special.change.filters}s.addEventListener&&c.each({focus:"focusin",blur:"focusout"},function(a,b){function d(f){f=c.event.fix(f);f.type=b;return c.event.handle.call(this,f)}c.event.special[b]={setup:function(){this.addEventListener(a,d,true)},teardown:function(){this.removeEventListener(a,d,true)}}});c.each(["bind","one"],function(a,b){c.fn[b]=function(d,
f,e){if(typeof d==="object"){for(var i in d)this[b](i,f,d[i],e);return this}if(c.isFunction(f)){thisObject=e;e=f;f=w}var j=b==="one"?c.proxy(e,function(o){c(this).unbind(o,j);return e.apply(this,arguments)}):e;return d==="unload"&&b!=="one"?this.one(d,f,e,thisObject):this.each(function(){c.event.add(this,d,j,f)})}});c.fn.extend({unbind:function(a,b){if(typeof a==="object"&&!a.preventDefault){for(var d in a)this.unbind(d,a[d]);return this}return this.each(function(){c.event.remove(this,a,b)})},trigger:function(a,
b){return this.each(function(){c.event.trigger(a,b,this)})},triggerHandler:function(a,b){if(this[0]){a=c.Event(a);a.preventDefault();a.stopPropagation();c.event.trigger(a,b,this[0]);return a.result}},toggle:function(a){for(var b=arguments,d=1;d<b.length;)c.proxy(a,b[d++]);return this.click(c.proxy(a,function(f){var e=(c.data(this,"lastToggle"+a.guid)||0)%d;c.data(this,"lastToggle"+a.guid,e+1);f.preventDefault();return b[e].apply(this,arguments)||false}))},hover:function(a,b){return this.mouseenter(a).mouseleave(b||
a)},live:function(a,b,d){if(c.isFunction(b)){d=b;b=w}c(this.context).bind(ra(a,this.selector),{data:b,selector:this.selector,live:a},d);return this},die:function(a,b){c(this.context).unbind(ra(a,this.selector),b?{guid:b.guid+this.selector+a}:null);return this}});c.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error".split(" "),function(a,b){c.fn[b]=function(d){return d?
this.bind(b,d):this.trigger(b)};if(c.attrFn)c.attrFn[b]=true});A.attachEvent&&!A.addEventListener&&A.attachEvent("onunload",function(){for(var a in c.cache)if(c.cache[a].handle)try{c.event.remove(c.cache[a].handle.elem)}catch(b){}});(function(){function a(g){for(var h="",k,m=0;g[m];m++){k=g[m];if(k.nodeType===3||k.nodeType===4)h+=k.nodeValue;else if(k.nodeType!==8)h+=a(k.childNodes)}return h}function b(g,h,k,m,r,q){r=0;for(var v=m.length;r<v;r++){var u=m[r];if(u){u=u[g];for(var y=false;u;){if(u.sizcache===
k){y=m[u.sizset];break}if(u.nodeType===1&&!q){u.sizcache=k;u.sizset=r}if(u.nodeName.toLowerCase()===h){y=u;break}u=u[g]}m[r]=y}}}function d(g,h,k,m,r,q){r=0;for(var v=m.length;r<v;r++){var u=m[r];if(u){u=u[g];for(var y=false;u;){if(u.sizcache===k){y=m[u.sizset];break}if(u.nodeType===1){if(!q){u.sizcache=k;u.sizset=r}if(typeof h!=="string"){if(u===h){y=true;break}}else if(p.filter(h,[u]).length>0){y=u;break}}u=u[g]}m[r]=y}}}var f=/((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^[\]]*\]|['"][^'"]*['"]|[^[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?((?:.|\r|\n)*)/g,
e=0,i=Object.prototype.toString,j=false,o=true;[0,0].sort(function(){o=false;return 0});var p=function(g,h,k,m){k=k||[];var r=h=h||s;if(h.nodeType!==1&&h.nodeType!==9)return[];if(!g||typeof g!=="string")return k;for(var q=[],v,u,y,S,I=true,N=x(h),J=g;(f.exec(""),v=f.exec(J))!==null;){J=v[3];q.push(v[1]);if(v[2]){S=v[3];break}}if(q.length>1&&t.exec(g))if(q.length===2&&n.relative[q[0]])u=ia(q[0]+q[1],h);else for(u=n.relative[q[0]]?[h]:p(q.shift(),h);q.length;){g=q.shift();if(n.relative[g])g+=q.shift();
u=ia(g,u)}else{if(!m&&q.length>1&&h.nodeType===9&&!N&&n.match.ID.test(q[0])&&!n.match.ID.test(q[q.length-1])){v=p.find(q.shift(),h,N);h=v.expr?p.filter(v.expr,v.set)[0]:v.set[0]}if(h){v=m?{expr:q.pop(),set:B(m)}:p.find(q.pop(),q.length===1&&(q[0]==="~"||q[0]==="+")&&h.parentNode?h.parentNode:h,N);u=v.expr?p.filter(v.expr,v.set):v.set;if(q.length>0)y=B(u);else I=false;for(;q.length;){var E=q.pop();v=E;if(n.relative[E])v=q.pop();else E="";if(v==null)v=h;n.relative[E](y,v,N)}}else y=[]}y||(y=u);if(!y)throw"Syntax error, unrecognized expression: "+
(E||g);if(i.call(y)==="[object Array]")if(I)if(h&&h.nodeType===1)for(g=0;y[g]!=null;g++){if(y[g]&&(y[g]===true||y[g].nodeType===1&&F(h,y[g])))k.push(u[g])}else for(g=0;y[g]!=null;g++)y[g]&&y[g].nodeType===1&&k.push(u[g]);else k.push.apply(k,y);else B(y,k);if(S){p(S,r,k,m);p.uniqueSort(k)}return k};p.uniqueSort=function(g){if(D){j=o;g.sort(D);if(j)for(var h=1;h<g.length;h++)g[h]===g[h-1]&&g.splice(h--,1)}return g};p.matches=function(g,h){return p(g,null,null,h)};p.find=function(g,h,k){var m,r;if(!g)return[];
for(var q=0,v=n.order.length;q<v;q++){var u=n.order[q];if(r=n.leftMatch[u].exec(g)){var y=r[1];r.splice(1,1);if(y.substr(y.length-1)!=="\\"){r[1]=(r[1]||"").replace(/\\/g,"");m=n.find[u](r,h,k);if(m!=null){g=g.replace(n.match[u],"");break}}}}m||(m=h.getElementsByTagName("*"));return{set:m,expr:g}};p.filter=function(g,h,k,m){for(var r=g,q=[],v=h,u,y,S=h&&h[0]&&x(h[0]);g&&h.length;){for(var I in n.filter)if((u=n.leftMatch[I].exec(g))!=null&&u[2]){var N=n.filter[I],J,E;E=u[1];y=false;u.splice(1,1);if(E.substr(E.length-
1)!=="\\"){if(v===q)q=[];if(n.preFilter[I])if(u=n.preFilter[I](u,v,k,q,m,S)){if(u===true)continue}else y=J=true;if(u)for(var X=0;(E=v[X])!=null;X++)if(E){J=N(E,u,X,v);var Ea=m^!!J;if(k&&J!=null)if(Ea)y=true;else v[X]=false;else if(Ea){q.push(E);y=true}}if(J!==w){k||(v=q);g=g.replace(n.match[I],"");if(!y)return[];break}}}if(g===r)if(y==null)throw"Syntax error, unrecognized expression: "+g;else break;r=g}return v};var n=p.selectors={order:["ID","NAME","TAG"],match:{ID:/#((?:[\w\u00c0-\uFFFF-]|\\.)+)/,
CLASS:/\.((?:[\w\u00c0-\uFFFF-]|\\.)+)/,NAME:/\[name=['"]*((?:[\w\u00c0-\uFFFF-]|\\.)+)['"]*\]/,ATTR:/\[\s*((?:[\w\u00c0-\uFFFF-]|\\.)+)\s*(?:(\S?=)\s*(['"]*)(.*?)\3|)\s*\]/,TAG:/^((?:[\w\u00c0-\uFFFF\*-]|\\.)+)/,CHILD:/:(only|nth|last|first)-child(?:\((even|odd|[\dn+-]*)\))?/,POS:/:(nth|eq|gt|lt|first|last|even|odd)(?:\((\d*)\))?(?=[^-]|$)/,PSEUDO:/:((?:[\w\u00c0-\uFFFF-]|\\.)+)(?:\((['"]?)((?:\([^\)]+\)|[^\(\)]*)+)\2\))?/},leftMatch:{},attrMap:{"class":"className","for":"htmlFor"},attrHandle:{href:function(g){return g.getAttribute("href")}},
relative:{"+":function(g,h){var k=typeof h==="string",m=k&&!/\W/.test(h);k=k&&!m;if(m)h=h.toLowerCase();m=0;for(var r=g.length,q;m<r;m++)if(q=g[m]){for(;(q=q.previousSibling)&&q.nodeType!==1;);g[m]=k||q&&q.nodeName.toLowerCase()===h?q||false:q===h}k&&p.filter(h,g,true)},">":function(g,h){var k=typeof h==="string";if(k&&!/\W/.test(h)){h=h.toLowerCase();for(var m=0,r=g.length;m<r;m++){var q=g[m];if(q){k=q.parentNode;g[m]=k.nodeName.toLowerCase()===h?k:false}}}else{m=0;for(r=g.length;m<r;m++)if(q=g[m])g[m]=
k?q.parentNode:q.parentNode===h;k&&p.filter(h,g,true)}},"":function(g,h,k){var m=e++,r=d;if(typeof h==="string"&&!/\W/.test(h)){var q=h=h.toLowerCase();r=b}r("parentNode",h,m,g,q,k)},"~":function(g,h,k){var m=e++,r=d;if(typeof h==="string"&&!/\W/.test(h)){var q=h=h.toLowerCase();r=b}r("previousSibling",h,m,g,q,k)}},find:{ID:function(g,h,k){if(typeof h.getElementById!=="undefined"&&!k)return(g=h.getElementById(g[1]))?[g]:[]},NAME:function(g,h){if(typeof h.getElementsByName!=="undefined"){var k=[];
h=h.getElementsByName(g[1]);for(var m=0,r=h.length;m<r;m++)h[m].getAttribute("name")===g[1]&&k.push(h[m]);return k.length===0?null:k}},TAG:function(g,h){return h.getElementsByTagName(g[1])}},preFilter:{CLASS:function(g,h,k,m,r,q){g=" "+g[1].replace(/\\/g,"")+" ";if(q)return g;q=0;for(var v;(v=h[q])!=null;q++)if(v)if(r^(v.className&&(" "+v.className+" ").replace(/[\t\n]/g," ").indexOf(g)>=0))k||m.push(v);else if(k)h[q]=false;return false},ID:function(g){return g[1].replace(/\\/g,"")},TAG:function(g){return g[1].toLowerCase()},
CHILD:function(g){if(g[1]==="nth"){var h=/(-?)(\d*)n((?:\+|-)?\d*)/.exec(g[2]==="even"&&"2n"||g[2]==="odd"&&"2n+1"||!/\D/.test(g[2])&&"0n+"+g[2]||g[2]);g[2]=h[1]+(h[2]||1)-0;g[3]=h[3]-0}g[0]=e++;return g},ATTR:function(g,h,k,m,r,q){h=g[1].replace(/\\/g,"");if(!q&&n.attrMap[h])g[1]=n.attrMap[h];if(g[2]==="~=")g[4]=" "+g[4]+" ";return g},PSEUDO:function(g,h,k,m,r){if(g[1]==="not")if((f.exec(g[3])||"").length>1||/^\w/.test(g[3]))g[3]=p(g[3],null,null,h);else{g=p.filter(g[3],h,k,true^r);k||m.push.apply(m,
g);return false}else if(n.match.POS.test(g[0])||n.match.CHILD.test(g[0]))return true;return g},POS:function(g){g.unshift(true);return g}},filters:{enabled:function(g){return g.disabled===false&&g.type!=="hidden"},disabled:function(g){return g.disabled===true},checked:function(g){return g.checked===true},selected:function(g){return g.selected===true},parent:function(g){return!!g.firstChild},empty:function(g){return!g.firstChild},has:function(g,h,k){return!!p(k[3],g).length},header:function(g){return/h\d/i.test(g.nodeName)},
text:function(g){return"text"===g.type},radio:function(g){return"radio"===g.type},checkbox:function(g){return"checkbox"===g.type},file:function(g){return"file"===g.type},password:function(g){return"password"===g.type},submit:function(g){return"submit"===g.type},image:function(g){return"image"===g.type},reset:function(g){return"reset"===g.type},button:function(g){return"button"===g.type||g.nodeName.toLowerCase()==="button"},input:function(g){return/input|select|textarea|button/i.test(g.nodeName)}},
setFilters:{first:function(g,h){return h===0},last:function(g,h,k,m){return h===m.length-1},even:function(g,h){return h%2===0},odd:function(g,h){return h%2===1},lt:function(g,h,k){return h<k[3]-0},gt:function(g,h,k){return h>k[3]-0},nth:function(g,h,k){return k[3]-0===h},eq:function(g,h,k){return k[3]-0===h}},filter:{PSEUDO:function(g,h,k,m){var r=h[1],q=n.filters[r];if(q)return q(g,k,h,m);else if(r==="contains")return(g.textContent||g.innerText||a([g])||"").indexOf(h[3])>=0;else if(r==="not"){h=
h[3];k=0;for(m=h.length;k<m;k++)if(h[k]===g)return false;return true}else throw"Syntax error, unrecognized expression: "+r;},CHILD:function(g,h){var k=h[1],m=g;switch(k){case "only":case "first":for(;m=m.previousSibling;)if(m.nodeType===1)return false;if(k==="first")return true;m=g;case "last":for(;m=m.nextSibling;)if(m.nodeType===1)return false;return true;case "nth":k=h[2];var r=h[3];if(k===1&&r===0)return true;h=h[0];var q=g.parentNode;if(q&&(q.sizcache!==h||!g.nodeIndex)){var v=0;for(m=q.firstChild;m;m=
m.nextSibling)if(m.nodeType===1)m.nodeIndex=++v;q.sizcache=h}g=g.nodeIndex-r;return k===0?g===0:g%k===0&&g/k>=0}},ID:function(g,h){return g.nodeType===1&&g.getAttribute("id")===h},TAG:function(g,h){return h==="*"&&g.nodeType===1||g.nodeName.toLowerCase()===h},CLASS:function(g,h){return(" "+(g.className||g.getAttribute("class"))+" ").indexOf(h)>-1},ATTR:function(g,h){var k=h[1];g=n.attrHandle[k]?n.attrHandle[k](g):g[k]!=null?g[k]:g.getAttribute(k);k=g+"";var m=h[2];h=h[4];return g==null?m==="!=":m===
"="?k===h:m==="*="?k.indexOf(h)>=0:m==="~="?(" "+k+" ").indexOf(h)>=0:!h?k&&g!==false:m==="!="?k!==h:m==="^="?k.indexOf(h)===0:m==="$="?k.substr(k.length-h.length)===h:m==="|="?k===h||k.substr(0,h.length+1)===h+"-":false},POS:function(g,h,k,m){var r=n.setFilters[h[2]];if(r)return r(g,k,h,m)}}},t=n.match.POS;for(var z in n.match){n.match[z]=new RegExp(n.match[z].source+/(?![^\[]*\])(?![^\(]*\))/.source);n.leftMatch[z]=new RegExp(/(^(?:.|\r|\n)*?)/.source+n.match[z].source.replace(/\\(\d+)/g,function(g,
h){return"\\"+(h-0+1)}))}var B=function(g,h){g=Array.prototype.slice.call(g,0);if(h){h.push.apply(h,g);return h}return g};try{Array.prototype.slice.call(s.documentElement.childNodes,0)}catch(C){B=function(g,h){h=h||[];if(i.call(g)==="[object Array]")Array.prototype.push.apply(h,g);else if(typeof g.length==="number")for(var k=0,m=g.length;k<m;k++)h.push(g[k]);else for(k=0;g[k];k++)h.push(g[k]);return h}}var D;if(s.documentElement.compareDocumentPosition)D=function(g,h){if(!g.compareDocumentPosition||
!h.compareDocumentPosition){if(g==h)j=true;return g.compareDocumentPosition?-1:1}g=g.compareDocumentPosition(h)&4?-1:g===h?0:1;if(g===0)j=true;return g};else if("sourceIndex"in s.documentElement)D=function(g,h){if(!g.sourceIndex||!h.sourceIndex){if(g==h)j=true;return g.sourceIndex?-1:1}g=g.sourceIndex-h.sourceIndex;if(g===0)j=true;return g};else if(s.createRange)D=function(g,h){if(!g.ownerDocument||!h.ownerDocument){if(g==h)j=true;return g.ownerDocument?-1:1}var k=g.ownerDocument.createRange(),m=
h.ownerDocument.createRange();k.setStart(g,0);k.setEnd(g,0);m.setStart(h,0);m.setEnd(h,0);g=k.compareBoundaryPoints(Range.START_TO_END,m);if(g===0)j=true;return g};(function(){var g=s.createElement("div"),h="script"+(new Date).getTime();g.innerHTML="<a name='"+h+"'/>";var k=s.documentElement;k.insertBefore(g,k.firstChild);if(s.getElementById(h)){n.find.ID=function(m,r,q){if(typeof r.getElementById!=="undefined"&&!q)return(r=r.getElementById(m[1]))?r.id===m[1]||typeof r.getAttributeNode!=="undefined"&&
r.getAttributeNode("id").nodeValue===m[1]?[r]:w:[]};n.filter.ID=function(m,r){var q=typeof m.getAttributeNode!=="undefined"&&m.getAttributeNode("id");return m.nodeType===1&&q&&q.nodeValue===r}}k.removeChild(g);k=g=null})();(function(){var g=s.createElement("div");g.appendChild(s.createComment(""));if(g.getElementsByTagName("*").length>0)n.find.TAG=function(h,k){k=k.getElementsByTagName(h[1]);if(h[1]==="*"){h=[];for(var m=0;k[m];m++)k[m].nodeType===1&&h.push(k[m]);k=h}return k};g.innerHTML="<a href='#'></a>";
if(g.firstChild&&typeof g.firstChild.getAttribute!=="undefined"&&g.firstChild.getAttribute("href")!=="#")n.attrHandle.href=function(h){return h.getAttribute("href",2)};g=null})();s.querySelectorAll&&function(){var g=p,h=s.createElement("div");h.innerHTML="<p class='TEST'></p>";if(!(h.querySelectorAll&&h.querySelectorAll(".TEST").length===0)){p=function(m,r,q,v){r=r||s;if(!v&&r.nodeType===9&&!x(r))try{return B(r.querySelectorAll(m),q)}catch(u){}return g(m,r,q,v)};for(var k in g)p[k]=g[k];h=null}}();
(function(){var g=s.createElement("div");g.innerHTML="<div class='test e'></div><div class='test'></div>";if(!(!g.getElementsByClassName||g.getElementsByClassName("e").length===0)){g.lastChild.className="e";if(g.getElementsByClassName("e").length!==1){n.order.splice(1,0,"CLASS");n.find.CLASS=function(h,k,m){if(typeof k.getElementsByClassName!=="undefined"&&!m)return k.getElementsByClassName(h[1])};g=null}}})();var F=s.compareDocumentPosition?function(g,h){return g.compareDocumentPosition(h)&16}:function(g,
h){return g!==h&&(g.contains?g.contains(h):true)},x=function(g){return(g=(g?g.ownerDocument||g:0).documentElement)?g.nodeName!=="HTML":false},ia=function(g,h){var k=[],m="",r;for(h=h.nodeType?[h]:h;r=n.match.PSEUDO.exec(g);){m+=r[0];g=g.replace(n.match.PSEUDO,"")}g=n.relative[g]?g+"*":g;r=0;for(var q=h.length;r<q;r++)p(g,h[r],k);return p.filter(m,k)};c.find=p;c.expr=p.selectors;c.expr[":"]=c.expr.filters;c.unique=p.uniqueSort;c.getText=a;c.isXMLDoc=x;c.contains=F})();var ab=/Until$/,bb=/^(?:parents|prevUntil|prevAll)/,
cb=/,/;R=Array.prototype.slice;var Fa=function(a,b,d){if(c.isFunction(b))return c.grep(a,function(e,i){return!!b.call(e,i,e)===d});else if(b.nodeType)return c.grep(a,function(e){return e===b===d});else if(typeof b==="string"){var f=c.grep(a,function(e){return e.nodeType===1});if(Pa.test(b))return c.filter(b,f,!d);else b=c.filter(b,a)}return c.grep(a,function(e){return c.inArray(e,b)>=0===d})};c.fn.extend({find:function(a){for(var b=this.pushStack("","find",a),d=0,f=0,e=this.length;f<e;f++){d=b.length;
c.find(a,this[f],b);if(f>0)for(var i=d;i<b.length;i++)for(var j=0;j<d;j++)if(b[j]===b[i]){b.splice(i--,1);break}}return b},has:function(a){var b=c(a);return this.filter(function(){for(var d=0,f=b.length;d<f;d++)if(c.contains(this,b[d]))return true})},not:function(a){return this.pushStack(Fa(this,a,false),"not",a)},filter:function(a){return this.pushStack(Fa(this,a,true),"filter",a)},is:function(a){return!!a&&c.filter(a,this).length>0},closest:function(a,b){if(c.isArray(a)){var d=[],f=this[0],e,i=
{},j;if(f&&a.length){e=0;for(var o=a.length;e<o;e++){j=a[e];i[j]||(i[j]=c.expr.match.POS.test(j)?c(j,b||this.context):j)}for(;f&&f.ownerDocument&&f!==b;){for(j in i){e=i[j];if(e.jquery?e.index(f)>-1:c(f).is(e)){d.push({selector:j,elem:f});delete i[j]}}f=f.parentNode}}return d}var p=c.expr.match.POS.test(a)?c(a,b||this.context):null;return this.map(function(n,t){for(;t&&t.ownerDocument&&t!==b;){if(p?p.index(t)>-1:c(t).is(a))return t;t=t.parentNode}return null})},index:function(a){if(!a||typeof a===
"string")return c.inArray(this[0],a?c(a):this.parent().children());return c.inArray(a.jquery?a[0]:a,this)},add:function(a,b){a=typeof a==="string"?c(a,b||this.context):c.makeArray(a);b=c.merge(this.get(),a);return this.pushStack(sa(a[0])||sa(b[0])?b:c.unique(b))},andSelf:function(){return this.add(this.prevObject)}});c.each({parent:function(a){return(a=a.parentNode)&&a.nodeType!==11?a:null},parents:function(a){return c.dir(a,"parentNode")},parentsUntil:function(a,b,d){return c.dir(a,"parentNode",
d)},next:function(a){return c.nth(a,2,"nextSibling")},prev:function(a){return c.nth(a,2,"previousSibling")},nextAll:function(a){return c.dir(a,"nextSibling")},prevAll:function(a){return c.dir(a,"previousSibling")},nextUntil:function(a,b,d){return c.dir(a,"nextSibling",d)},prevUntil:function(a,b,d){return c.dir(a,"previousSibling",d)},siblings:function(a){return c.sibling(a.parentNode.firstChild,a)},children:function(a){return c.sibling(a.firstChild)},contents:function(a){return c.nodeName(a,"iframe")?
a.contentDocument||a.contentWindow.document:c.makeArray(a.childNodes)}},function(a,b){c.fn[a]=function(d,f){var e=c.map(this,b,d);ab.test(a)||(f=d);if(f&&typeof f==="string")e=c.filter(f,e);e=this.length>1?c.unique(e):e;if((this.length>1||cb.test(f))&&bb.test(a))e=e.reverse();return this.pushStack(e,a,R.call(arguments).join(","))}});c.extend({filter:function(a,b,d){if(d)a=":not("+a+")";return c.find.matches(a,b)},dir:function(a,b,d){var f=[];for(a=a[b];a&&a.nodeType!==9&&(d===w||!c(a).is(d));){a.nodeType===
1&&f.push(a);a=a[b]}return f},nth:function(a,b,d){b=b||1;for(var f=0;a;a=a[d])if(a.nodeType===1&&++f===b)break;return a},sibling:function(a,b){for(var d=[];a;a=a.nextSibling)a.nodeType===1&&a!==b&&d.push(a);return d}});var Ga=/ jQuery\d+="(?:\d+|null)"/g,Y=/^\s+/,db=/(<([\w:]+)[^>]*?)\/>/g,eb=/^(?:area|br|col|embed|hr|img|input|link|meta|param)$/i,Ha=/<([\w:]+)/,fb=/<tbody/i,gb=/<|&\w+;/,hb=function(a,b,d){return eb.test(d)?a:b+"></"+d+">"},G={option:[1,"<select multiple='multiple'>","</select>"],
legend:[1,"<fieldset>","</fieldset>"],thead:[1,"<table>","</table>"],tr:[2,"<table><tbody>","</tbody></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],col:[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"],area:[1,"<map>","</map>"],_default:[0,"",""]};G.optgroup=G.option;G.tbody=G.tfoot=G.colgroup=G.caption=G.thead;G.th=G.td;if(!c.support.htmlSerialize)G._default=[1,"div<div>","</div>"];c.fn.extend({text:function(a){if(c.isFunction(a))return this.each(function(b){var d=c(this);
return d.text(a.call(this,b,d.text()))});if(typeof a!=="object"&&a!==w)return this.empty().append((this[0]&&this[0].ownerDocument||s).createTextNode(a));return c.getText(this)},wrapAll:function(a){if(c.isFunction(a))return this.each(function(d){c(this).wrapAll(a.call(this,d))});if(this[0]){var b=c(a,this[0].ownerDocument).eq(0).clone(true);this[0].parentNode&&b.insertBefore(this[0]);b.map(function(){for(var d=this;d.firstChild&&d.firstChild.nodeType===1;)d=d.firstChild;return d}).append(this)}return this},
wrapInner:function(a){return this.each(function(){var b=c(this),d=b.contents();d.length?d.wrapAll(a):b.append(a)})},wrap:function(a){return this.each(function(){c(this).wrapAll(a)})},unwrap:function(){return this.parent().each(function(){c.nodeName(this,"body")||c(this).replaceWith(this.childNodes)}).end()},append:function(){return this.domManip(arguments,true,function(a){this.nodeType===1&&this.appendChild(a)})},prepend:function(){return this.domManip(arguments,true,function(a){this.nodeType===1&&
this.insertBefore(a,this.firstChild)})},before:function(){if(this[0]&&this[0].parentNode)return this.domManip(arguments,false,function(b){this.parentNode.insertBefore(b,this)});else if(arguments.length){var a=c(arguments[0]);a.push.apply(a,this.toArray());return this.pushStack(a,"before",arguments)}},after:function(){if(this[0]&&this[0].parentNode)return this.domManip(arguments,false,function(b){this.parentNode.insertBefore(b,this.nextSibling)});else if(arguments.length){var a=this.pushStack(this,
"after",arguments);a.push.apply(a,c(arguments[0]).toArray());return a}},clone:function(a){var b=this.map(function(){if(!c.support.noCloneEvent&&!c.isXMLDoc(this)){var d=this.outerHTML,f=this.ownerDocument;if(!d){d=f.createElement("div");d.appendChild(this.cloneNode(true));d=d.innerHTML}return c.clean([d.replace(Ga,"").replace(Y,"")],f)[0]}else return this.cloneNode(true)});if(a===true){ta(this,b);ta(this.find("*"),b.find("*"))}return b},html:function(a){if(a===w)return this[0]&&this[0].nodeType===
1?this[0].innerHTML.replace(Ga,""):null;else if(typeof a==="string"&&!/<script/i.test(a)&&(c.support.leadingWhitespace||!Y.test(a))&&!G[(Ha.exec(a)||["",""])[1].toLowerCase()])try{for(var b=0,d=this.length;b<d;b++)if(this[b].nodeType===1){T(this[b].getElementsByTagName("*"));this[b].innerHTML=a}}catch(f){this.empty().append(a)}else c.isFunction(a)?this.each(function(e){var i=c(this),j=i.html();i.empty().append(function(){return a.call(this,e,j)})}):this.empty().append(a);return this},replaceWith:function(a){if(this[0]&&
this[0].parentNode){c.isFunction(a)||(a=c(a).detach());return this.each(function(){var b=this.nextSibling,d=this.parentNode;c(this).remove();b?c(b).before(a):c(d).append(a)})}else return this.pushStack(c(c.isFunction(a)?a():a),"replaceWith",a)},detach:function(a){return this.remove(a,true)},domManip:function(a,b,d){function f(t){return c.nodeName(t,"table")?t.getElementsByTagName("tbody")[0]||t.appendChild(t.ownerDocument.createElement("tbody")):t}var e,i,j=a[0],o=[];if(c.isFunction(j))return this.each(function(t){var z=
c(this);a[0]=j.call(this,t,b?z.html():w);return z.domManip(a,b,d)});if(this[0]){e=a[0]&&a[0].parentNode&&a[0].parentNode.nodeType===11?{fragment:a[0].parentNode}:ua(a,this,o);if(i=e.fragment.firstChild){b=b&&c.nodeName(i,"tr");for(var p=0,n=this.length;p<n;p++)d.call(b?f(this[p],i):this[p],e.cacheable||this.length>1||p>0?e.fragment.cloneNode(true):e.fragment)}o&&c.each(o,La)}return this}});c.fragments={};c.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},
function(a,b){c.fn[a]=function(d){var f=[];d=c(d);for(var e=0,i=d.length;e<i;e++){var j=(e>0?this.clone(true):this).get();c.fn[b].apply(c(d[e]),j);f=f.concat(j)}return this.pushStack(f,a,d.selector)}});c.each({remove:function(a,b){if(!a||c.filter(a,[this]).length){if(!b&&this.nodeType===1){T(this.getElementsByTagName("*"));T([this])}this.parentNode&&this.parentNode.removeChild(this)}},empty:function(){for(this.nodeType===1&&T(this.getElementsByTagName("*"));this.firstChild;)this.removeChild(this.firstChild)}},
function(a,b){c.fn[a]=function(){return this.each(b,arguments)}});c.extend({clean:function(a,b,d,f){b=b||s;if(typeof b.createElement==="undefined")b=b.ownerDocument||b[0]&&b[0].ownerDocument||s;var e=[];c.each(a,function(i,j){if(typeof j==="number")j+="";if(j){if(typeof j==="string"&&!gb.test(j))j=b.createTextNode(j);else if(typeof j==="string"){j=j.replace(db,hb);var o=(Ha.exec(j)||["",""])[1].toLowerCase(),p=G[o]||G._default,n=p[0];i=b.createElement("div");for(i.innerHTML=p[1]+j+p[2];n--;)i=i.lastChild;
if(!c.support.tbody){n=fb.test(j);o=o==="table"&&!n?i.firstChild&&i.firstChild.childNodes:p[1]==="<table>"&&!n?i.childNodes:[];for(p=o.length-1;p>=0;--p)c.nodeName(o[p],"tbody")&&!o[p].childNodes.length&&o[p].parentNode.removeChild(o[p])}!c.support.leadingWhitespace&&Y.test(j)&&i.insertBefore(b.createTextNode(Y.exec(j)[0]),i.firstChild);j=c.makeArray(i.childNodes)}if(j.nodeType)e.push(j);else e=c.merge(e,j)}});if(d)for(a=0;e[a];a++)if(f&&c.nodeName(e[a],"script")&&(!e[a].type||e[a].type.toLowerCase()===
"text/javascript"))f.push(e[a].parentNode?e[a].parentNode.removeChild(e[a]):e[a]);else{e[a].nodeType===1&&e.splice.apply(e,[a+1,0].concat(c.makeArray(e[a].getElementsByTagName("script"))));d.appendChild(e[a])}return e}});var ib=/z-?index|font-?weight|opacity|zoom|line-?height/i,Ia=/alpha\([^)]*\)/,Ja=/opacity=([^)]*)/,ja=/float/i,ka=/-([a-z])/ig,jb=/([A-Z])/g,kb=/^-?\d+(?:px)?$/i,lb=/^-?\d/,mb={position:"absolute",visibility:"hidden",display:"block"},nb=["Left","Right"],ob=["Top","Bottom"],pb=s.defaultView&&
s.defaultView.getComputedStyle,Ka=c.support.cssFloat?"cssFloat":"styleFloat",la=function(a,b){return b.toUpperCase()};c.fn.css=function(a,b){return $(this,a,b,true,function(d,f,e){if(e===w)return c.curCSS(d,f);if(typeof e==="number"&&!ib.test(f))e+="px";c.style(d,f,e)})};c.extend({style:function(a,b,d){if(!a||a.nodeType===3||a.nodeType===8)return w;if((b==="width"||b==="height")&&parseFloat(d)<0)d=w;var f=a.style||a,e=d!==w;if(!c.support.opacity&&b==="opacity"){if(e){f.zoom=1;b=parseInt(d,10)+""===
"NaN"?"":"alpha(opacity="+d*100+")";a=f.filter||c.curCSS(a,"filter")||"";f.filter=Ia.test(a)?a.replace(Ia,b):b}return f.filter&&f.filter.indexOf("opacity=")>=0?parseFloat(Ja.exec(f.filter)[1])/100+"":""}if(ja.test(b))b=Ka;b=b.replace(ka,la);if(e)f[b]=d;return f[b]},css:function(a,b,d,f){if(b==="width"||b==="height"){var e,i=b==="width"?nb:ob;function j(){e=b==="width"?a.offsetWidth:a.offsetHeight;f!=="border"&&c.each(i,function(){f||(e-=parseFloat(c.curCSS(a,"padding"+this,true))||0);if(f==="margin")e+=
parseFloat(c.curCSS(a,"margin"+this,true))||0;else e-=parseFloat(c.curCSS(a,"border"+this+"Width",true))||0})}a.offsetWidth!==0?j():c.swap(a,mb,j);return Math.max(0,Math.round(e))}return c.curCSS(a,b,d)},curCSS:function(a,b,d){var f,e=a.style;if(!c.support.opacity&&b==="opacity"&&a.currentStyle){f=Ja.test(a.currentStyle.filter||"")?parseFloat(RegExp.$1)/100+"":"";return f===""?"1":f}if(ja.test(b))b=Ka;if(!d&&e&&e[b])f=e[b];else if(pb){if(ja.test(b))b="float";b=b.replace(jb,"-$1").toLowerCase();e=
a.ownerDocument.defaultView;if(!e)return null;if(a=e.getComputedStyle(a,null))f=a.getPropertyValue(b);if(b==="opacity"&&f==="")f="1"}else if(a.currentStyle){d=b.replace(ka,la);f=a.currentStyle[b]||a.currentStyle[d];if(!kb.test(f)&&lb.test(f)){b=e.left;var i=a.runtimeStyle.left;a.runtimeStyle.left=a.currentStyle.left;e.left=d==="fontSize"?"1em":f||0;f=e.pixelLeft+"px";e.left=b;a.runtimeStyle.left=i}}return f},swap:function(a,b,d){var f={};for(var e in b){f[e]=a.style[e];a.style[e]=b[e]}d.call(a);for(e in b)a.style[e]=
f[e]}});if(c.expr&&c.expr.filters){c.expr.filters.hidden=function(a){var b=a.offsetWidth,d=a.offsetHeight,f=a.nodeName.toLowerCase()==="tr";return b===0&&d===0&&!f?true:b>0&&d>0&&!f?false:c.curCSS(a,"display")==="none"};c.expr.filters.visible=function(a){return!c.expr.filters.hidden(a)}}var qb=K(),rb=/<script(.|\s)*?\/script>/gi,sb=/select|textarea/i,tb=/color|date|datetime|email|hidden|month|number|password|range|search|tel|text|time|url|week/i,O=/=\?(&|$)/,ma=/\?/,ub=/(\?|&)_=.*?(&|$)/,vb=/^(\w+:)?\/\/([^\/?#]+)/,
wb=/%20/g;c.fn.extend({_load:c.fn.load,load:function(a,b,d){if(typeof a!=="string")return this._load(a);else if(!this.length)return this;var f=a.indexOf(" ");if(f>=0){var e=a.slice(f,a.length);a=a.slice(0,f)}f="GET";if(b)if(c.isFunction(b)){d=b;b=null}else if(typeof b==="object"){b=c.param(b,c.ajaxSettings.traditional);f="POST"}c.ajax({url:a,type:f,dataType:"html",data:b,context:this,complete:function(i,j){if(j==="success"||j==="notmodified")this.html(e?c("<div />").append(i.responseText.replace(rb,
"")).find(e):i.responseText);d&&this.each(d,[i.responseText,j,i])}});return this},serialize:function(){return c.param(this.serializeArray())},serializeArray:function(){return this.map(function(){return this.elements?c.makeArray(this.elements):this}).filter(function(){return this.name&&!this.disabled&&(this.checked||sb.test(this.nodeName)||tb.test(this.type))}).map(function(a,b){a=c(this).val();return a==null?null:c.isArray(a)?c.map(a,function(d){return{name:b.name,value:d}}):{name:b.name,value:a}}).get()}});
c.each("ajaxStart ajaxStop ajaxComplete ajaxError ajaxSuccess ajaxSend".split(" "),function(a,b){c.fn[b]=function(d){return this.bind(b,d)}});c.extend({get:function(a,b,d,f){if(c.isFunction(b)){f=f||d;d=b;b=null}return c.ajax({type:"GET",url:a,data:b,success:d,dataType:f})},getScript:function(a,b){return c.get(a,null,b,"script")},getJSON:function(a,b,d){return c.get(a,b,d,"json")},post:function(a,b,d,f){if(c.isFunction(b)){f=f||d;d=b;b={}}return c.ajax({type:"POST",url:a,data:b,success:d,dataType:f})},
ajaxSetup:function(a){c.extend(c.ajaxSettings,a)},ajaxSettings:{url:location.href,global:true,type:"GET",contentType:"application/x-www-form-urlencoded",processData:true,async:true,xhr:A.XMLHttpRequest&&(A.location.protocol!=="file:"||!A.ActiveXObject)?function(){return new A.XMLHttpRequest}:function(){try{return new A.ActiveXObject("Microsoft.XMLHTTP")}catch(a){}},accepts:{xml:"application/xml, text/xml",html:"text/html",script:"text/javascript, application/javascript",json:"application/json, text/javascript",
text:"text/plain",_default:"*/*"}},lastModified:{},etag:{},ajax:function(a){function b(){e.success&&e.success.call(p,o,j,x);e.global&&f("ajaxSuccess",[x,e])}function d(){e.complete&&e.complete.call(p,x,j);e.global&&f("ajaxComplete",[x,e]);e.global&&!--c.active&&c.event.trigger("ajaxStop")}function f(r,q){(e.context?c(e.context):c.event).trigger(r,q)}var e=c.extend(true,{},c.ajaxSettings,a),i,j,o,p=e.context||e,n=e.type.toUpperCase();if(e.data&&e.processData&&typeof e.data!=="string")e.data=c.param(e.data,
e.traditional);if(e.dataType==="jsonp"){if(n==="GET")O.test(e.url)||(e.url+=(ma.test(e.url)?"&":"?")+(e.jsonp||"callback")+"=?");else if(!e.data||!O.test(e.data))e.data=(e.data?e.data+"&":"")+(e.jsonp||"callback")+"=?";e.dataType="json"}if(e.dataType==="json"&&(e.data&&O.test(e.data)||O.test(e.url))){i=e.jsonpCallback||"jsonp"+qb++;if(e.data)e.data=(e.data+"").replace(O,"="+i+"$1");e.url=e.url.replace(O,"="+i+"$1");e.dataType="script";A[i]=A[i]||function(r){o=r;b();d();A[i]=w;try{delete A[i]}catch(q){}B&&
B.removeChild(C)}}if(e.dataType==="script"&&e.cache===null)e.cache=false;if(e.cache===false&&n==="GET"){var t=K(),z=e.url.replace(ub,"$1_="+t+"$2");e.url=z+(z===e.url?(ma.test(e.url)?"&":"?")+"_="+t:"")}if(e.data&&n==="GET")e.url+=(ma.test(e.url)?"&":"?")+e.data;e.global&&!c.active++&&c.event.trigger("ajaxStart");t=(t=vb.exec(e.url))&&(t[1]&&t[1]!==location.protocol||t[2]!==location.host);if(e.dataType==="script"&&n==="GET"&&t){var B=s.getElementsByTagName("head")[0]||s.documentElement,C=s.createElement("script");
C.src=e.url;if(e.scriptCharset)C.charset=e.scriptCharset;if(!i){var D=false;C.onload=C.onreadystatechange=function(){if(!D&&(!this.readyState||this.readyState==="loaded"||this.readyState==="complete")){D=true;b();d();C.onload=C.onreadystatechange=null;B&&C.parentNode&&B.removeChild(C)}}}B.insertBefore(C,B.firstChild);return w}var F=false,x=e.xhr();if(x){e.username?x.open(n,e.url,e.async,e.username,e.password):x.open(n,e.url,e.async);try{if(e.data||a&&a.contentType)x.setRequestHeader("Content-Type",
e.contentType);if(e.ifModified){c.lastModified[e.url]&&x.setRequestHeader("If-Modified-Since",c.lastModified[e.url]);c.etag[e.url]&&x.setRequestHeader("If-None-Match",c.etag[e.url])}t||x.setRequestHeader("X-Requested-With","XMLHttpRequest");x.setRequestHeader("Accept",e.dataType&&e.accepts[e.dataType]?e.accepts[e.dataType]+", */*":e.accepts._default)}catch(ia){}if(e.beforeSend&&e.beforeSend.call(p,x,e)===false){e.global&&!--c.active&&c.event.trigger("ajaxStop");x.abort();return false}e.global&&f("ajaxSend",
[x,e]);var g=x.onreadystatechange=function(r){if(!x||x.readyState===0){F||d();F=true;if(x)x.onreadystatechange=c.noop}else if(!F&&x&&(x.readyState===4||r==="timeout")){F=true;x.onreadystatechange=c.noop;j=r==="timeout"?"timeout":!c.httpSuccess(x)?"error":e.ifModified&&c.httpNotModified(x,e.url)?"notmodified":"success";if(j==="success")try{o=c.httpData(x,e.dataType,e)}catch(q){j="parsererror"}if(j==="success"||j==="notmodified")i||b();else c.handleError(e,x,j);d();r==="timeout"&&x.abort();if(e.async)x=
null}};try{var h=x.abort;x.abort=function(){if(x){h.call(x);if(x)x.readyState=0}g()}}catch(k){}e.async&&e.timeout>0&&setTimeout(function(){x&&!F&&g("timeout")},e.timeout);try{x.send(n==="POST"||n==="PUT"||n==="DELETE"?e.data:null)}catch(m){c.handleError(e,x,null,m);d()}e.async||g();return x}},handleError:function(a,b,d,f){if(a.error)a.error.call(a.context||A,b,d,f);if(a.global)(a.context?c(a.context):c.event).trigger("ajaxError",[b,a,f])},active:0,httpSuccess:function(a){try{return!a.status&&location.protocol===
"file:"||a.status>=200&&a.status<300||a.status===304||a.status===1223||a.status===0}catch(b){}return false},httpNotModified:function(a,b){var d=a.getResponseHeader("Last-Modified"),f=a.getResponseHeader("Etag");if(d)c.lastModified[b]=d;if(f)c.etag[b]=f;return a.status===304||a.status===0},httpData:function(a,b,d){var f=a.getResponseHeader("content-type")||"",e=b==="xml"||!b&&f.indexOf("xml")>=0;a=e?a.responseXML:a.responseText;if(e&&a.documentElement.nodeName==="parsererror")throw"parsererror";if(d&&
d.dataFilter)a=d.dataFilter(a,b);if(typeof a==="string")if(b==="json"||!b&&f.indexOf("json")>=0)if(/^[\],:{}\s]*$/.test(a.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,"")))a=A.JSON&&A.JSON.parse?A.JSON.parse(a):(new Function("return "+a))();else throw"Invalid JSON: "+a;else if(b==="script"||!b&&f.indexOf("javascript")>=0)c.globalEval(a);return a},param:function(a,b){function d(e,i){i=
c.isFunction(i)?i():i;f[f.length]=encodeURIComponent(e)+"="+encodeURIComponent(i)}var f=[];if(b===w)b=c.ajaxSettings.traditional;c.isArray(a)||a.jquery?c.each(a,function(){d(this.name,this.value)}):c.each(a,function e(i,j){if(c.isArray(j))c.each(j,function(o,p){b?d(i,p):e(i+"["+(typeof p==="object"||c.isArray(p)?o:"")+"]",p)});else!b&&j!=null&&typeof j==="object"?c.each(j,function(o,p){e(i+"["+o+"]",p)}):d(i,j)});return f.join("&").replace(wb,"+")}});var na={},xb=/toggle|show|hide/,yb=/^([+-]=)?([\d+-.]+)(.*)$/,
Z,va=[["height","marginTop","marginBottom","paddingTop","paddingBottom"],["width","marginLeft","marginRight","paddingLeft","paddingRight"],["opacity"]];c.fn.extend({show:function(a,b){if(a!=null)return this.animate(L("show",3),a,b);else{a=0;for(b=this.length;a<b;a++){var d=c.data(this[a],"olddisplay");this[a].style.display=d||"";if(c.css(this[a],"display")==="none"){d=this[a].nodeName;var f;if(na[d])f=na[d];else{var e=c("<"+d+" />").appendTo("body");f=e.css("display");if(f==="none")f="block";e.remove();
na[d]=f}c.data(this[a],"olddisplay",f)}}a=0;for(b=this.length;a<b;a++)this[a].style.display=c.data(this[a],"olddisplay")||"";return this}},hide:function(a,b){if(a!=null)return this.animate(L("hide",3),a,b);else{a=0;for(b=this.length;a<b;a++){var d=c.data(this[a],"olddisplay");!d&&d!=="none"&&c.data(this[a],"olddisplay",c.css(this[a],"display"))}a=0;for(b=this.length;a<b;a++)this[a].style.display="none";return this}},_toggle:c.fn.toggle,toggle:function(a,b){var d=typeof a==="boolean";if(c.isFunction(a)&&
c.isFunction(b))this._toggle.apply(this,arguments);else a==null||d?this.each(function(){var f=d?a:c(this).is(":hidden");c(this)[f?"show":"hide"]()}):this.animate(L("toggle",3),a,b);return this},fadeTo:function(a,b,d){return this.filter(":hidden").css("opacity",0).show().end().animate({opacity:b},a,d)},animate:function(a,b,d,f){var e=c.speed(b,d,f);if(c.isEmptyObject(a))return this.each(e.complete);return this[e.queue===false?"each":"queue"](function(){var i=c.extend({},e),j,o=this.nodeType===1&&c(this).is(":hidden"),
p=this;for(j in a){var n=j.replace(ka,la);if(j!==n){a[n]=a[j];delete a[j];j=n}if(a[j]==="hide"&&o||a[j]==="show"&&!o)return i.complete.call(this);if((j==="height"||j==="width")&&this.style){i.display=c.css(this,"display");i.overflow=this.style.overflow}if(c.isArray(a[j])){(i.specialEasing=i.specialEasing||{})[j]=a[j][1];a[j]=a[j][0]}}if(i.overflow!=null)this.style.overflow="hidden";i.curAnim=c.extend({},a);c.each(a,function(t,z){var B=new c.fx(p,i,t);if(xb.test(z))B[z==="toggle"?o?"show":"hide":z](a);
else{var C=yb.exec(z),D=B.cur(true)||0;if(C){z=parseFloat(C[2]);var F=C[3]||"px";if(F!=="px"){p.style[t]=(z||1)+F;D=(z||1)/B.cur(true)*D;p.style[t]=D+F}if(C[1])z=(C[1]==="-="?-1:1)*z+D;B.custom(D,z,F)}else B.custom(D,z,"")}});return true})},stop:function(a,b){var d=c.timers;a&&this.queue([]);this.each(function(){for(var f=d.length-1;f>=0;f--)if(d[f].elem===this){b&&d[f](true);d.splice(f,1)}});b||this.dequeue();return this}});c.each({slideDown:L("show",1),slideUp:L("hide",1),slideToggle:L("toggle",
1),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"}},function(a,b){c.fn[a]=function(d,f){return this.animate(b,d,f)}});c.extend({speed:function(a,b,d){var f=a&&typeof a==="object"?a:{complete:d||!d&&b||c.isFunction(a)&&a,duration:a,easing:d&&b||b&&!c.isFunction(b)&&b};f.duration=c.fx.off?0:typeof f.duration==="number"?f.duration:c.fx.speeds[f.duration]||c.fx.speeds._default;f.old=f.complete;f.complete=function(){f.queue!==false&&c(this).dequeue();c.isFunction(f.old)&&f.old.call(this)};return f},easing:{linear:function(a,
b,d,f){return d+f*a},swing:function(a,b,d,f){return(-Math.cos(a*Math.PI)/2+0.5)*f+d}},timers:[],fx:function(a,b,d){this.options=b;this.elem=a;this.prop=d;if(!b.orig)b.orig={}}});c.fx.prototype={update:function(){this.options.step&&this.options.step.call(this.elem,this.now,this);(c.fx.step[this.prop]||c.fx.step._default)(this);if((this.prop==="height"||this.prop==="width")&&this.elem.style)this.elem.style.display="block"},cur:function(a){if(this.elem[this.prop]!=null&&(!this.elem.style||this.elem.style[this.prop]==
null))return this.elem[this.prop];return(a=parseFloat(c.css(this.elem,this.prop,a)))&&a>-10000?a:parseFloat(c.curCSS(this.elem,this.prop))||0},custom:function(a,b,d){function f(i){return e.step(i)}this.startTime=K();this.start=a;this.end=b;this.unit=d||this.unit||"px";this.now=this.start;this.pos=this.state=0;var e=this;f.elem=this.elem;if(f()&&c.timers.push(f)&&!Z)Z=setInterval(c.fx.tick,13)},show:function(){this.options.orig[this.prop]=c.style(this.elem,this.prop);this.options.show=true;this.custom(this.prop===
"width"||this.prop==="height"?1:0,this.cur());c(this.elem).show()},hide:function(){this.options.orig[this.prop]=c.style(this.elem,this.prop);this.options.hide=true;this.custom(this.cur(),0)},step:function(a){var b=K(),d=true;if(a||b>=this.options.duration+this.startTime){this.now=this.end;this.pos=this.state=1;this.update();this.options.curAnim[this.prop]=true;for(var f in this.options.curAnim)if(this.options.curAnim[f]!==true)d=false;if(d){if(this.options.display!=null){this.elem.style.overflow=
this.options.overflow;a=c.data(this.elem,"olddisplay");this.elem.style.display=a?a:this.options.display;if(c.css(this.elem,"display")==="none")this.elem.style.display="block"}this.options.hide&&c(this.elem).hide();if(this.options.hide||this.options.show)for(var e in this.options.curAnim)c.style(this.elem,e,this.options.orig[e]);this.options.complete.call(this.elem)}return false}else{e=b-this.startTime;this.state=e/this.options.duration;a=this.options.easing||(c.easing.swing?"swing":"linear");this.pos=
c.easing[this.options.specialEasing&&this.options.specialEasing[this.prop]||a](this.state,e,0,1,this.options.duration);this.now=this.start+(this.end-this.start)*this.pos;this.update()}return true}};c.extend(c.fx,{tick:function(){for(var a=c.timers,b=0;b<a.length;b++)a[b]()||a.splice(b--,1);a.length||c.fx.stop()},stop:function(){clearInterval(Z);Z=null},speeds:{slow:600,fast:200,_default:400},step:{opacity:function(a){c.style(a.elem,"opacity",a.now)},_default:function(a){if(a.elem.style&&a.elem.style[a.prop]!=
null)a.elem.style[a.prop]=(a.prop==="width"||a.prop==="height"?Math.max(0,a.now):a.now)+a.unit;else a.elem[a.prop]=a.now}}});if(c.expr&&c.expr.filters)c.expr.filters.animated=function(a){return c.grep(c.timers,function(b){return a===b.elem}).length};c.fn.offset="getBoundingClientRect"in s.documentElement?function(a){var b=this[0];if(!b||!b.ownerDocument)return null;if(a)return this.each(function(e){c.offset.setOffset(this,a,e)});if(b===b.ownerDocument.body)return c.offset.bodyOffset(b);var d=b.getBoundingClientRect(),
f=b.ownerDocument;b=f.body;f=f.documentElement;return{top:d.top+(self.pageYOffset||c.support.boxModel&&f.scrollTop||b.scrollTop)-(f.clientTop||b.clientTop||0),left:d.left+(self.pageXOffset||c.support.boxModel&&f.scrollLeft||b.scrollLeft)-(f.clientLeft||b.clientLeft||0)}}:function(a){var b=this[0];if(!b||!b.ownerDocument)return null;if(a)return this.each(function(t){c.offset.setOffset(this,a,t)});if(b===b.ownerDocument.body)return c.offset.bodyOffset(b);c.offset.initialize();var d=b.offsetParent,f=
b,e=b.ownerDocument,i,j=e.documentElement,o=e.body;f=(e=e.defaultView)?e.getComputedStyle(b,null):b.currentStyle;for(var p=b.offsetTop,n=b.offsetLeft;(b=b.parentNode)&&b!==o&&b!==j;){if(c.offset.supportsFixedPosition&&f.position==="fixed")break;i=e?e.getComputedStyle(b,null):b.currentStyle;p-=b.scrollTop;n-=b.scrollLeft;if(b===d){p+=b.offsetTop;n+=b.offsetLeft;if(c.offset.doesNotAddBorder&&!(c.offset.doesAddBorderForTableAndCells&&/^t(able|d|h)$/i.test(b.nodeName))){p+=parseFloat(i.borderTopWidth)||
0;n+=parseFloat(i.borderLeftWidth)||0}f=d;d=b.offsetParent}if(c.offset.subtractsBorderForOverflowNotVisible&&i.overflow!=="visible"){p+=parseFloat(i.borderTopWidth)||0;n+=parseFloat(i.borderLeftWidth)||0}f=i}if(f.position==="relative"||f.position==="static"){p+=o.offsetTop;n+=o.offsetLeft}if(c.offset.supportsFixedPosition&&f.position==="fixed"){p+=Math.max(j.scrollTop,o.scrollTop);n+=Math.max(j.scrollLeft,o.scrollLeft)}return{top:p,left:n}};c.offset={initialize:function(){var a=s.body,b=s.createElement("div"),
d,f,e,i=parseFloat(c.curCSS(a,"marginTop",true))||0;c.extend(b.style,{position:"absolute",top:0,left:0,margin:0,border:0,width:"1px",height:"1px",visibility:"hidden"});b.innerHTML="<div style='position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;'><div></div></div><table style='position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;' cellpadding='0' cellspacing='0'><tr><td></td></tr></table>";a.insertBefore(b,a.firstChild);
d=b.firstChild;f=d.firstChild;e=d.nextSibling.firstChild.firstChild;this.doesNotAddBorder=f.offsetTop!==5;this.doesAddBorderForTableAndCells=e.offsetTop===5;f.style.position="fixed";f.style.top="20px";this.supportsFixedPosition=f.offsetTop===20||f.offsetTop===15;f.style.position=f.style.top="";d.style.overflow="hidden";d.style.position="relative";this.subtractsBorderForOverflowNotVisible=f.offsetTop===-5;this.doesNotIncludeMarginInBodyOffset=a.offsetTop!==i;a.removeChild(b);c.offset.initialize=c.noop},
bodyOffset:function(a){var b=a.offsetTop,d=a.offsetLeft;c.offset.initialize();if(c.offset.doesNotIncludeMarginInBodyOffset){b+=parseFloat(c.curCSS(a,"marginTop",true))||0;d+=parseFloat(c.curCSS(a,"marginLeft",true))||0}return{top:b,left:d}},setOffset:function(a,b,d){if(/static/.test(c.curCSS(a,"position")))a.style.position="relative";var f=c(a),e=f.offset(),i=parseInt(c.curCSS(a,"top",true),10)||0,j=parseInt(c.curCSS(a,"left",true),10)||0;if(c.isFunction(b))b=b.call(a,d,e);d={top:b.top-e.top+i,left:b.left-
e.left+j};"using"in b?b.using.call(a,d):f.css(d)}};c.fn.extend({position:function(){if(!this[0])return null;var a=this[0],b=this.offsetParent(),d=this.offset(),f=/^body|html$/i.test(b[0].nodeName)?{top:0,left:0}:b.offset();d.top-=parseFloat(c.curCSS(a,"marginTop",true))||0;d.left-=parseFloat(c.curCSS(a,"marginLeft",true))||0;f.top+=parseFloat(c.curCSS(b[0],"borderTopWidth",true))||0;f.left+=parseFloat(c.curCSS(b[0],"borderLeftWidth",true))||0;return{top:d.top-f.top,left:d.left-f.left}},offsetParent:function(){return this.map(function(){for(var a=
this.offsetParent||s.body;a&&!/^body|html$/i.test(a.nodeName)&&c.css(a,"position")==="static";)a=a.offsetParent;return a})}});c.each(["Left","Top"],function(a,b){var d="scroll"+b;c.fn[d]=function(f){var e=this[0],i;if(!e)return null;if(f!==w)return this.each(function(){if(i=wa(this))i.scrollTo(!a?f:c(i).scrollLeft(),a?f:c(i).scrollTop());else this[d]=f});else return(i=wa(e))?"pageXOffset"in i?i[a?"pageYOffset":"pageXOffset"]:c.support.boxModel&&i.document.documentElement[d]||i.document.body[d]:e[d]}});
c.each(["Height","Width"],function(a,b){var d=b.toLowerCase();c.fn["inner"+b]=function(){return this[0]?c.css(this[0],d,false,"padding"):null};c.fn["outer"+b]=function(f){return this[0]?c.css(this[0],d,false,f?"margin":"border"):null};c.fn[d]=function(f){var e=this[0];if(!e)return f==null?null:this;return"scrollTo"in e&&e.document?e.document.compatMode==="CSS1Compat"&&e.document.documentElement["client"+b]||e.document.body["client"+b]:e.nodeType===9?Math.max(e.documentElement["client"+b],e.body["scroll"+
b],e.documentElement["scroll"+b],e.body["offset"+b],e.documentElement["offset"+b]):f===w?c.css(e,d):this.css(d,typeof f==="string"?f:f+"px")}});A.jQuery=A.$=c})(window);


/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 */

jQuery.easing['jswing']=jQuery.easing['swing'];jQuery.extend(jQuery.easing,{def:'easeOutQuad',swing:function(x,t,b,c,d){return jQuery.easing[jQuery.easing.def](x,t,b,c,d);},easeInQuad:function(x,t,b,c,d){return c*(t/=d)*t+b;},easeOutQuad:function(x,t,b,c,d){return-c*(t/=d)*(t-2)+b;},easeInOutQuad:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t+b;return-c/2*((--t)*(t-2)-1)+b;},easeInCubic:function(x,t,b,c,d){return c*(t/=d)*t*t+b;},easeOutCubic:function(x,t,b,c,d){return c*((t=t/d-1)*t*t+1)+b;},easeInOutCubic:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t+b;return c/2*((t-=2)*t*t+2)+b;},easeInQuart:function(x,t,b,c,d){return c*(t/=d)*t*t*t+b;},easeOutQuart:function(x,t,b,c,d){return-c*((t=t/d-1)*t*t*t-1)+b;},easeInOutQuart:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t+b;return-c/2*((t-=2)*t*t*t-2)+b;},easeInQuint:function(x,t,b,c,d){return c*(t/=d)*t*t*t*t+b;},easeOutQuint:function(x,t,b,c,d){return c*((t=t/d-1)*t*t*t*t+1)+b;},easeInOutQuint:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t*t+b;return c/2*((t-=2)*t*t*t*t+2)+b;},easeInSine:function(x,t,b,c,d){return-c*Math.cos(t/d*(Math.PI/2))+c+b;},easeOutSine:function(x,t,b,c,d){return c*Math.sin(t/d*(Math.PI/2))+b;},easeInOutSine:function(x,t,b,c,d){return-c/2*(Math.cos(Math.PI*t/d)-1)+b;},easeInExpo:function(x,t,b,c,d){return(t==0)?b:c*Math.pow(2,10*(t/d-1))+b;},easeOutExpo:function(x,t,b,c,d){return(t==d)?b+c:c*(-Math.pow(2,-10*t/d)+1)+b;},easeInOutExpo:function(x,t,b,c,d){if(t==0)return b;if(t==d)return b+c;if((t/=d/2)<1)return c/2*Math.pow(2,10*(t-1))+b;return c/2*(-Math.pow(2,-10*--t)+2)+b;},easeInCirc:function(x,t,b,c,d){return-c*(Math.sqrt(1-(t/=d)*t)-1)+b;},easeOutCirc:function(x,t,b,c,d){return c*Math.sqrt(1-(t=t/d-1)*t)+b;},easeInOutCirc:function(x,t,b,c,d){if((t/=d/2)<1)return-c/2*(Math.sqrt(1-t*t)-1)+b;return c/2*(Math.sqrt(1-(t-=2)*t)+1)+b;},easeInElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4;}
else var s=p/(2*Math.PI)*Math.asin(c/a);return-(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b;},easeOutElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4;}
else var s=p/(2*Math.PI)*Math.asin(c/a);return a*Math.pow(2,-10*t)*Math.sin((t*d-s)*(2*Math.PI)/p)+c+b;},easeInOutElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d/2)==2)return b+c;if(!p)p=d*(.3*1.5);if(a<Math.abs(c)){a=c;var s=p/4;}
else var s=p/(2*Math.PI)*Math.asin(c/a);if(t<1)return-.5*(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b;return a*Math.pow(2,-10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p)*.5+c+b;},easeInBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;return c*(t/=d)*t*((s+1)*t-s)+b;},easeOutBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;return c*((t=t/d-1)*t*((s+1)*t+s)+1)+b;},easeInOutBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;if((t/=d/2)<1)return c/2*(t*t*(((s*=(1.525))+1)*t-s))+b;return c/2*((t-=2)*t*(((s*=(1.525))+1)*t+s)+2)+b;},easeInBounce:function(x,t,b,c,d){return c-jQuery.easing.easeOutBounce(x,d-t,0,c,d)+b;},easeOutBounce:function(x,t,b,c,d){if((t/=d)<(1/2.75)){return c*(7.5625*t*t)+b;}else if(t<(2/2.75)){return c*(7.5625*(t-=(1.5/2.75))*t+.75)+b;}else if(t<(2.5/2.75)){return c*(7.5625*(t-=(2.25/2.75))*t+.9375)+b;}else{return c*(7.5625*(t-=(2.625/2.75))*t+.984375)+b;}},easeInOutBounce:function(x,t,b,c,d){if(t<d/2)return jQuery.easing.easeInBounce(x,t*2,0,c,d)*.5+b;return jQuery.easing.easeOutBounce(x,t*2-d,0,c,d)*.5+c*.5+b;}});


jQuery.fn.extend({
	fader: function(interval, callback) {
		return this.each(function() {
			$(this).fadeTo(interval, 0.01, function() {
				if (jQuery.isFunction(callback)) {
					callback();
				}
				$(this).fadeTo(interval, 1.0);
			});
		});
	},
});

$.fn.noSelect = function(p) {
	if (p == null) {
		prevent = true;
	} else {
		prevent = p;
	}

	if (prevent) {
		return this.each(function () {
			if ($.browser.msie || $.browser.safari) {
				$(this).bind('selectstart',function(){return false;});
			} else if ($.browser.mozilla) {
				$(this).css('MozUserSelect','none');
				$('body').trigger('focus');
			} else if ($.browser.opera) {
				$(this).bind('mousedown',function(){return false;});
			} else {
				$(this).attr('unselectable','on');
			}
		});
	} else {
		return this.each(function () {
			if ($.browser.msie || $.browser.safari) {
				$(this).unbind('selectstart');
			} else if ($.browser.mozilla) {
				$(this).css('MozUserSelect','inherit');
			} else if ($.browser.opera) {
				$(this).unbind('mousedown');
			} else {
				$(this).removeAttr('unselectable','on');
			}
		});
	}
};



/*
 * jQuery Color Animations
 * Copyright 2007 John Resig
 * Released under the MIT and GPL licenses.
 */
(function(d){function e(c){var a;if(c&&c.constructor==Array&&c.length==3)return c;if(a=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(c))return[parseInt(a[1]),parseInt(a[2]),parseInt(a[3])];if(a=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(c))return[parseFloat(a[1])*2.55,parseFloat(a[2])*2.55,parseFloat(a[3])*2.55];if(a=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(c))return[parseInt(a[1],16),parseInt(a[2], 16),parseInt(a[3],16)];if(a=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(c))return[parseInt(a[1]+a[1],16),parseInt(a[2]+a[2],16),parseInt(a[3]+a[3],16)];return f[d.trim(c).toLowerCase()]}function g(c,a){var b;do{b=d.curCSS(c,a);if(b!=""&&b!="transparent"||d.nodeName(c,"body"))break;a="backgroundColor"}while(c=c.parentNode);return e(b)}d.each(["backgroundColor","borderBottomColor","borderLeftColor","borderRightColor","borderTopColor","color","outlineColor"],function(c,a){d.fx.step[a]=function(b){if(b.state== 0){b.start=g(b.elem,a);b.end=e(b.end)}b.elem.style[a]="rgb("+[Math.max(Math.min(parseInt(b.pos*(b.end[0]-b.start[0])+b.start[0]),255),0),Math.max(Math.min(parseInt(b.pos*(b.end[1]-b.start[1])+b.start[1]),255),0),Math.max(Math.min(parseInt(b.pos*(b.end[2]-b.start[2])+b.start[2]),255),0)].join(",")+")"}});var f={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],darkgreen:[0, 100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128, 128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0]}})(jQuery);

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options.expires=-1;}
var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*60*60*1000));}else{date=options.expires;}
expires='; expires='+date.toUTCString();}
var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('');}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break;}}}
return cookieValue;}};



/*!
 * jQuery Form Plugin
 * version: 2.43 (12-MAR-2010)
 * @requires jQuery v1.3.2 or later
 *
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */

(function(b){function o(){if(b.fn.ajaxSubmit.debug){var a="[jquery.form] "+Array.prototype.join.call(arguments,"");if(window.console&&window.console.log)window.console.log(a);else window.opera&&window.opera.postError&&window.opera.postError(a)}}b.fn.ajaxSubmit=function(a){function e(){function s(){var q=h.attr("target"),n=h.attr("action");k.setAttribute("target",z);k.getAttribute("method")!="POST"&&k.setAttribute("method","POST");k.getAttribute("action")!=g.url&&k.setAttribute("action",g.url);g.skipEncodingOverride|| h.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"});g.timeout&&setTimeout(function(){C=true;t()},g.timeout);var m=[];try{if(g.extraData)for(var v in g.extraData)m.push(b('<input type="hidden" name="'+v+'" value="'+g.extraData[v]+'" />').appendTo(k)[0]);u.appendTo("body");u.data("form-plugin-onload",t);k.submit()}finally{k.setAttribute("action",n);q?k.setAttribute("target",q):h.removeAttr("target");b(m).remove()}}function t(){if(!D){var q=true;try{if(C)throw"timeout";var n,m;m=w.contentWindow? w.contentWindow.document:w.contentDocument?w.contentDocument:w.document;var v=g.dataType=="xml"||m.XMLDocument||b.isXMLDoc(m);o("isXml="+v);if(!v&&(m.body==null||m.body.innerHTML=="")){if(--G){o("requeing onLoad callback, DOM not available");setTimeout(t,250);return}o("Could not access iframe DOM after 100 tries.");return}o("response detected");D=true;j.responseText=m.body?m.body.innerHTML:null;j.responseXML=m.XMLDocument?m.XMLDocument:m;j.getResponseHeader=function(H){return{"content-type":g.dataType}[H]}; if(g.dataType=="json"||g.dataType=="script"){var E=m.getElementsByTagName("textarea")[0];if(E)j.responseText=E.value;else{var F=m.getElementsByTagName("pre")[0];if(F)j.responseText=F.innerHTML}}else if(g.dataType=="xml"&&!j.responseXML&&j.responseText!=null)j.responseXML=A(j.responseText);n=b.httpData(j,g.dataType)}catch(B){o("error caught:",B);q=false;j.error=B;b.handleError(g,j,"error",B)}if(q){g.success(n,"success");x&&b.event.trigger("ajaxSuccess",[j,g])}x&&b.event.trigger("ajaxComplete",[j,g]); x&&!--b.active&&b.event.trigger("ajaxStop");if(g.complete)g.complete(j,q?"success":"error");setTimeout(function(){u.removeData("form-plugin-onload");u.remove();j.responseXML=null},100)}}function A(q,n){if(window.ActiveXObject){n=new ActiveXObject("Microsoft.XMLDOM");n.async="false";n.loadXML(q)}else n=(new DOMParser).parseFromString(q,"text/xml");return n&&n.documentElement&&n.documentElement.tagName!="parsererror"?n:null}var k=h[0];if(b(":input[name=submit]",k).length)alert('Error: Form elements must not be named "submit".'); else{var g=b.extend({},b.ajaxSettings,a),r=b.extend(true,{},b.extend(true,{},b.ajaxSettings),g),z="jqFormIO"+(new Date).getTime(),u=b('<iframe id="'+z+'" name="'+z+'" src="'+g.iframeSrc+'" onload="(jQuery(this).data(\'form-plugin-onload\'))()" />'),w=u[0];u.css({position:"absolute",top:"-1000px",left:"-1000px"});var j={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(){this.aborted= 1;u.attr("src",g.iframeSrc)}},x=g.global;x&&!b.active++&&b.event.trigger("ajaxStart");x&&b.event.trigger("ajaxSend",[j,g]);if(r.beforeSend&&r.beforeSend(j,r)===false)r.global&&b.active--;else if(!j.aborted){var D=false,C=0;if(r=k.clk){var y=r.name;if(y&&!r.disabled){g.extraData=g.extraData||{};g.extraData[y]=r.value;if(r.type=="image"){g.extraData[y+".x"]=k.clk_x;g.extraData[y+".y"]=k.clk_y}}}g.forceSync?s():setTimeout(s,10);var G=100}}}if(!this.length){o("ajaxSubmit: skipping submit process - no element selected"); return this}if(typeof a=="function")a={success:a};var c=b.trim(this.attr("action"));if(c)c=(c.match(/^([^#]+)/)||[])[1];c=c||window.location.href||"";a=b.extend({url:c,type:this.attr("method")||"GET",iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},a||{});c={};this.trigger("form-pre-serialize",[this,a,c]);if(c.veto){o("ajaxSubmit: submit vetoed via form-pre-serialize trigger");return this}if(a.beforeSerialize&&a.beforeSerialize(this,a)===false){o("ajaxSubmit: submit aborted via beforeSerialize callback"); return this}var f=this.formToArray(a.semantic);if(a.data){a.extraData=a.data;for(var d in a.data)if(a.data[d]instanceof Array)for(var l in a.data[d])f.push({name:d,value:a.data[d][l]});else f.push({name:d,value:a.data[d]})}if(a.beforeSubmit&&a.beforeSubmit(f,this,a)===false){o("ajaxSubmit: submit aborted via beforeSubmit callback");return this}this.trigger("form-submit-validate",[f,this,a,c]);if(c.veto){o("ajaxSubmit: submit vetoed via form-submit-validate trigger");return this}d=b.param(f);if(a.type.toUpperCase()== "GET"){a.url+=(a.url.indexOf("?")>=0?"&":"?")+d;a.data=null}else a.data=d;var h=this,i=[];a.resetForm&&i.push(function(){h.resetForm()});a.clearForm&&i.push(function(){h.clearForm()});if(!a.dataType&&a.target){var p=a.success||function(){};i.push(function(s){var t=a.replaceTarget?"replaceWith":"html";b(a.target)[t](s).each(p,arguments)})}else a.success&&i.push(a.success);a.success=function(s,t,A){for(var k=0,g=i.length;k<g;k++)i[k].apply(a,[s,t,A||h,h])};d=b("input:file",this).fieldValue();l=false; for(c=0;c<d.length;c++)if(d[c])l=true;if(d.length&&a.iframe!==false||a.iframe||l||0)a.closeKeepAlive?b.get(a.closeKeepAlive,e):e();else b.ajax(a);this.trigger("form-submit-notify",[this,a]);return this};b.fn.ajaxForm=function(a){return this.ajaxFormUnbind().bind("submit.form-plugin",function(e){e.preventDefault();b(this).ajaxSubmit(a)}).bind("click.form-plugin",function(e){var c=e.target,f=b(c);if(!f.is(":submit,input:image")){c=f.closest(":submit");if(c.length==0)return;c=c[0]}var d=this;d.clk=c; if(c.type=="image")if(e.offsetX!=undefined){d.clk_x=e.offsetX;d.clk_y=e.offsetY}else if(typeof b.fn.offset=="function"){f=f.offset();d.clk_x=e.pageX-f.left;d.clk_y=e.pageY-f.top}else{d.clk_x=e.pageX-c.offsetLeft;d.clk_y=e.pageY-c.offsetTop}setTimeout(function(){d.clk=d.clk_x=d.clk_y=null},100)})};b.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")};b.fn.formToArray=function(a){var e=[];if(this.length==0)return e;var c=this[0],f=a?c.getElementsByTagName("*"):c.elements; if(!f)return e;for(var d=0,l=f.length;d<l;d++){var h=f[d],i=h.name;if(i)if(a&&c.clk&&h.type=="image"){if(!h.disabled&&c.clk==h){e.push({name:i,value:b(h).val()});e.push({name:i+".x",value:c.clk_x},{name:i+".y",value:c.clk_y})}}else if((h=b.fieldValue(h,true))&&h.constructor==Array)for(var p=0,s=h.length;p<s;p++)e.push({name:i,value:h[p]});else h!==null&&typeof h!="undefined"&&e.push({name:i,value:h})}if(!a&&c.clk){a=b(c.clk);f=a[0];if((i=f.name)&&!f.disabled&&f.type=="image"){e.push({name:i,value:a.val()}); e.push({name:i+".x",value:c.clk_x},{name:i+".y",value:c.clk_y})}}return e};b.fn.formSerialize=function(a){return b.param(this.formToArray(a))};b.fn.fieldSerialize=function(a){var e=[];this.each(function(){var c=this.name;if(c){var f=b.fieldValue(this,a);if(f&&f.constructor==Array)for(var d=0,l=f.length;d<l;d++)e.push({name:c,value:f[d]});else f!==null&&typeof f!="undefined"&&e.push({name:this.name,value:f})}});return b.param(e)};b.fn.fieldValue=function(a){for(var e=[],c=0,f=this.length;c<f;c++){var d= b.fieldValue(this[c],a);d===null||typeof d=="undefined"||d.constructor==Array&&!d.length||(d.constructor==Array?b.merge(e,d):e.push(d))}return e};b.fieldValue=function(a,e){var c=a.name,f=a.type,d=a.tagName.toLowerCase();if(typeof e=="undefined")e=true;if(e&&(!c||a.disabled||f=="reset"||f=="button"||(f=="checkbox"||f=="radio")&&!a.checked||(f=="submit"||f=="image")&&a.form&&a.form.clk!=a||d=="select"&&a.selectedIndex==-1))return null;if(d=="select"){var l=a.selectedIndex;if(l<0)return null;c=[];d= a.options;var h=(f=f=="select-one")?l+1:d.length;for(l=f?l:0;l<h;l++){var i=d[l];if(i.selected){var p=i.value;p||(p=i.attributes&&i.attributes.value&&!i.attributes.value.specified?i.text:i.value);if(f)return p;c.push(p)}}return c}return a.value};b.fn.clearForm=function(){return this.each(function(){b("input,select,textarea",this).clearFields()})};b.fn.clearFields=b.fn.clearInputs=function(){return this.each(function(){var a=this.type,e=this.tagName.toLowerCase();if(a=="text"||a=="password"||e=="textarea")this.value= "";else if(a=="checkbox"||a=="radio")this.checked=false;else if(e=="select")this.selectedIndex=-1})};b.fn.resetForm=function(){return this.each(function(){if(typeof this.reset=="function"||typeof this.reset=="object"&&!this.reset.nodeType)this.reset()})};b.fn.enable=function(a){if(a==undefined)a=true;return this.each(function(){this.disabled=!a})};b.fn.selected=function(a){if(a==undefined)a=true;return this.each(function(){var e=this.type;if(e=="checkbox"||e=="radio")this.checked=a;else if(this.tagName.toLowerCase()== "option"){e=b(this).parent("select");a&&e[0]&&e[0].type=="select-one"&&e.find("option").selected(false);this.selected=a}})}})(jQuery);

/**
 * jQuery.timers - Timer abstractions for jQuery
 * Written by Blair Mitchelmore (blair DOT mitchelmore AT gmail DOT com)
 * Licensed under the WTFPL (http://sam.zoy.org/wtfpl/).
 * Date: 2009/02/08
 *
 * @author Blair Mitchelmore
 * @version 1.1.2
 *
 **/
jQuery.fn.extend({everyTime:function(interval,label,fn,times,belay){return this.each(function(){jQuery.timer.add(this,interval,label,fn,times,belay)})},oneTime:function(interval,label,fn){return this.each(function(){jQuery.timer.add(this,interval,label,fn,1)})},stopTime:function(label,fn){return this.each(function(){jQuery.timer.remove(this,label,fn)})}});jQuery.event.special;jQuery.extend({timer:{global:[],guid:1,dataKey:"jQuery.timer",regex:/^([0-9]+(?:\.[0-9]*)?)\s*(.*s)?$/,powers:{ms:1,cs:10,ds:100,s:1000,das:10000,hs:100000,ks:1000000},timeParse:function(value){if(value==undefined||value==null){return null}var result=this.regex.exec(jQuery.trim(value.toString()));if(result[2]){var num=parseFloat(result[1]);var mult=this.powers[result[2]]||1;return num*mult}else{return value}},add:function(element,interval,label,fn,times,belay){var counter=0;if(jQuery.isFunction(label)){if(!times){times=fn}fn=label;label=interval}interval=jQuery.timer.timeParse(interval);if(typeof interval!="number"||isNaN(interval)||interval<=0){return}if(times&&times.constructor!=Number){belay=!!times;times=0}times=times||0;belay=belay||false;var timers=jQuery.data(element,this.dataKey)||jQuery.data(element,this.dataKey,{});if(!timers[label]){timers[label]={}}fn.timerID=fn.timerID||this.guid++;var handler=function(){if(belay&&this.inProgress){return}this.inProgress=true;if((++counter>times&&times!==0)||fn.call(element,counter)===false){jQuery.timer.remove(element,label,fn)}this.inProgress=false};handler.timerID=fn.timerID;if(!timers[label][fn.timerID]){timers[label][fn.timerID]=window.setInterval(handler,interval)}this.global.push(element)},remove:function(element,label,fn){var timers=jQuery.data(element,this.dataKey),ret;if(timers){if(!label){for(label in timers){this.remove(element,label,fn)}}else{if(timers[label]){if(fn){if(fn.timerID){window.clearInterval(timers[label][fn.timerID]);delete timers[label][fn.timerID]}}else{for(var fn in timers[label]){window.clearInterval(timers[label][fn]);delete timers[label][fn]}}for(ret in timers[label]){break}if(!ret){ret=null;delete timers[label]}}}for(ret in timers){break}if(!ret){jQuery.removeData(element,this.dataKey)}}}}});jQuery(window).bind("unload",function(){jQuery.each(jQuery.timer.global,function(index,item){jQuery.timer.remove(item)})});




/* menu */
jQuery(function() {
	var menuArrow = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAAPCAMAAADeWG8gAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAlQTFRF2trawMDA////FCoqwwAAACdJREFUeNpiYMIADFiEGKivihECUFTBRZDMgokgm8VIC3dhCgEEGAAduAIBgj6YfQAAAABJRU5ErkJggg==',
		menuArrowHover = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAAPCAMAAADeWG8gAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAlQTFRF2tra////QoznsMeHOwAAACpJREFUeNpiYGRkZEIFDFiEGKivihECUFTBRZDMgpuAZBYjLdyFKQQQYAAG5AHassa9ngAAAABJRU5ErkJggg==',
		menuArrowSelect = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAAPCAMAAADeWG8gAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAxQTFRF2traTJPp////5eXlhdWMxQAAACpJREFUeNpiYGJiYkYFDFiEGKivihECUFTBRZDMgokgm8VIC3dhCgEEGACQrwLSJGYb6gAAAABJRU5ErkJggg==';


	$("#nicemenu img.arrow").mousedown(function () {
		$("span.head_menu").removeClass('active');
		submenu = $(this).parent().parent().find("div.sub_menu");

		if (submenu.css('display') === 'block') {
			$(this).parent().removeClass("active");
			if (jQuery.browser.opera) {
			   submenu.hide();
			} else {
			    submenu.fadeOut(100);
			}
			$(this).attr('src', menuArrowHover);
		} else {
			$(this).parent().addClass("active");
			if (jQuery.browser.opera) {
			   submenu.show();
			} else {
			    submenu.fadeIn(100);
			}
			$(this).attr('src', menuArrowSelect);
		}

		$("div.sub_menu:visible").not(submenu).hide();
		$("#nicemenu img.arrow").not(this).attr('src', menuArrow);

	})
	.mouseover(function (){ $(this).attr('src', menuArrowHover); })
	.mouseout(function (){
		if ($(this).parent().parent().find("div.sub_menu").css('display') !== 'block') {
			$(this).attr('src',menuArrow);
		} else {
			$(this).attr('src',menuArrowSelect);
		}
	});

	$("#nicemenu span.head_menu")
		.mouseover(function () { $(this).addClass('over')})
		.mouseout(function () { $(this).removeClass('over') });

	$("#nicemenu div.sub_menu")
		.mouseover(function (){ $(this).show(); })
		.blur(function (){
			$(this).hide();
			$("span.head_menu").removeClass('active');
	});

 	$(document).bind('keydown click', function(e){
            if ( ((e.keyCode == 27) && !(e.ctrlKey || e.altKey)) || !$(e.target).is("#nicemenu *") ) {
                $("#nicemenu span.head_menu").removeClass('active');
				$("#nicemenu div.sub_menu").hide();

				$("#nicemenu img.arrow").each(function () {
					if ($(this).attr('src') != menuArrow) {
						$(this).attr('src', menuArrow);
					}
				});
            }
        })
});

/**
 * SWFObject v1.5.1: Flash Player detection and embed - http://blog.deconcept.com/swfobject/
 *
 * SWFObject is (c) 2007 Geoff Stearns and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
if(typeof deconcept == "undefined") var deconcept = {};
if(typeof deconcept.util == "undefined") deconcept.util = {};
if(typeof deconcept.SWFObjectUtil == "undefined") deconcept.SWFObjectUtil = {};
deconcept.SWFObject = function(swf, id, w, h, ver, c, quality, xiRedirectUrl, redirectUrl, detectKey) {
	if (!document.getElementById) { return; }
	this.DETECT_KEY = detectKey ? detectKey : 'detectflash';
	this.skipDetect = deconcept.util.getRequestParameter(this.DETECT_KEY);
	this.params = {};
	this.variables = {};
	this.attributes = [];
	if(swf) { this.setAttribute('swf', swf); }
	if(id) { this.setAttribute('id', id); }
	if(w) { this.setAttribute('width', w); }
	if(h) { this.setAttribute('height', h); }
	if(ver) { this.setAttribute('version', new deconcept.PlayerVersion(ver.toString().split("."))); }
	this.installedVer = deconcept.SWFObjectUtil.getPlayerVersion();
	if (!window.opera && document.all && this.installedVer.major > 7) {
		// only add the onunload cleanup if the Flash Player version supports External Interface and we are in IE
		// fixes bug in some fp9 versions see http://blog.deconcept.com/2006/07/28/swfobject-143-released/
		if (!deconcept.unloadSet) {
			deconcept.SWFObjectUtil.prepUnload = function() {
				__flash_unloadHandler = function(){};
				__flash_savedUnloadHandler = function(){};
				window.attachEvent("onunload", deconcept.SWFObjectUtil.cleanupSWFs);
			}
			window.attachEvent("onbeforeunload", deconcept.SWFObjectUtil.prepUnload);
			deconcept.unloadSet = true;
		}
	}
	if(c) { this.addParam('bgcolor', c); }
	var q = quality ? quality : 'high';
	this.addParam('quality', q);
	this.setAttribute('useExpressInstall', false);
	this.setAttribute('doExpressInstall', false);
	var xir = (xiRedirectUrl) ? xiRedirectUrl : window.location;
	this.setAttribute('xiRedirectUrl', xir);
	this.setAttribute('redirectUrl', '');
	if(redirectUrl) { this.setAttribute('redirectUrl', redirectUrl); }
}
deconcept.SWFObject.prototype = {
	useExpressInstall: function(path) {
		this.xiSWFPath = !path ? "expressinstall.swf" : path;
		this.setAttribute('useExpressInstall', true);
	},
	setAttribute: function(name, value){
		this.attributes[name] = value;
	},
	getAttribute: function(name){
		return this.attributes[name] || "";
	},
	addParam: function(name, value){
		this.params[name] = value;
	},
	getParams: function(){
		return this.params;
	},
	addVariable: function(name, value){
		this.variables[name] = value;
	},
	getVariable: function(name){
		return this.variables[name] || "";
	},
	getVariables: function(){
		return this.variables;
	},
	getVariablePairs: function(){
		var variablePairs = [];
		var key;
		var variables = this.getVariables();
		for(key in variables){
			variablePairs[variablePairs.length] = key +"="+ variables[key];
		}
		return variablePairs;
	},
	getSWFHTML: function() {
		var swfNode = "";
		if (navigator.plugins && navigator.mimeTypes && navigator.mimeTypes.length) { // netscape plugin architecture
			if (this.getAttribute("doExpressInstall")) {
				this.addVariable("MMplayerType", "PlugIn");
				this.setAttribute('swf', this.xiSWFPath);
			}
			swfNode = '<embed type="application/x-shockwave-flash" src="'+ this.getAttribute('swf') +'" width="'+ this.getAttribute('width') +'" height="'+ this.getAttribute('height') +'" style="'+ (this.getAttribute('style') || "") +'"';
			swfNode += ' id="'+ this.getAttribute('id') +'" name="'+ this.getAttribute('id') +'" ';
			var params = this.getParams();
			 for(var key in params){ swfNode += [key] +'="'+ params[key] +'" '; }
			var pairs = this.getVariablePairs().join("&");
			 if (pairs.length > 0){ swfNode += 'flashvars="'+ pairs +'"'; }
			swfNode += '/>';
		} else { // PC IE
			if (this.getAttribute("doExpressInstall")) {
				this.addVariable("MMplayerType", "ActiveX");
				this.setAttribute('swf', this.xiSWFPath);
			}
			swfNode = '<object id="'+ this.getAttribute('id') +'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+ this.getAttribute('width') +'" height="'+ this.getAttribute('height') +'" style="'+ (this.getAttribute('style') || "") +'">';
			swfNode += '<param name="movie" value="'+ this.getAttribute('swf') +'" />';
			var params = this.getParams();
			for(var key in params) {
			 swfNode += '<param name="'+ key +'" value="'+ params[key] +'" />';
			}
			var pairs = this.getVariablePairs().join("&");
			if(pairs.length > 0) {swfNode += '<param name="flashvars" value="'+ pairs +'" />';}
			swfNode += "</object>";
		}
		return swfNode;
	},
	write: function(elementId){
		if(this.getAttribute('useExpressInstall')) {
			// check to see if we need to do an express install
			var expressInstallReqVer = new deconcept.PlayerVersion([6,0,65]);
			if (this.installedVer.versionIsValid(expressInstallReqVer) && !this.installedVer.versionIsValid(this.getAttribute('version'))) {
				this.setAttribute('doExpressInstall', true);
				this.addVariable("MMredirectURL", escape(this.getAttribute('xiRedirectUrl')));
				document.title = document.title.slice(0, 47) + " - Flash Player Installation";
				this.addVariable("MMdoctitle", document.title);
			}
		}
		if(this.skipDetect || this.getAttribute('doExpressInstall') || this.installedVer.versionIsValid(this.getAttribute('version'))){
			var n = (typeof elementId == 'string') ? document.getElementById(elementId) : elementId;
			n.innerHTML = this.getSWFHTML();
			return true;
		}else{
			if(this.getAttribute('redirectUrl') != "") {
				document.location.replace(this.getAttribute('redirectUrl'));
			}
		}
		return false;
	}
}

/* ---- detection functions ---- */
deconcept.SWFObjectUtil.getPlayerVersion = function(){
	var PlayerVersion = new deconcept.PlayerVersion([0,0,0]);
	if(navigator.plugins && navigator.mimeTypes.length){
		var x = navigator.plugins["Shockwave Flash"];
		if(x && x.description) {
			PlayerVersion = new deconcept.PlayerVersion(x.description.replace(/([a-zA-Z]|\s)+/, "").replace(/(\s+r|\s+b[0-9]+)/, ".").split("."));
		}
	}else if (navigator.userAgent && navigator.userAgent.indexOf("Windows CE") >= 0){ // if Windows CE
		var axo = 1;
		var counter = 3;
		while(axo) {
			try {
				counter++;
				axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash."+ counter);
//				document.write("player v: "+ counter);
				PlayerVersion = new deconcept.PlayerVersion([counter,0,0]);
			} catch (e) {
				axo = null;
			}
		}
	} else { // Win IE (non mobile)
		// do minor version lookup in IE, but avoid fp6 crashing issues
		// see http://blog.deconcept.com/2006/01/11/getvariable-setvariable-crash-internet-explorer-flash-6/
		try{
			var axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
		}catch(e){
			try {
				var axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
				PlayerVersion = new deconcept.PlayerVersion([6,0,21]);
				axo.AllowScriptAccess = "always"; // error if player version < 6.0.47 (thanks to Michael Williams @ Adobe for this code)
			} catch(e) {
				if (PlayerVersion.major == 6) {
					return PlayerVersion;
				}
			}
			try {
				axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
			} catch(e) {}
		}
		if (axo != null) {
			PlayerVersion = new deconcept.PlayerVersion(axo.GetVariable("$version").split(" ")[1].split(","));
		}
	}
	return PlayerVersion;
}
deconcept.PlayerVersion = function(arrVersion){
	this.major = arrVersion[0] != null ? parseInt(arrVersion[0]) : 0;
	this.minor = arrVersion[1] != null ? parseInt(arrVersion[1]) : 0;
	this.rev = arrVersion[2] != null ? parseInt(arrVersion[2]) : 0;
}
deconcept.PlayerVersion.prototype.versionIsValid = function(fv){
	if(this.major < fv.major) return false;
	if(this.major > fv.major) return true;
	if(this.minor < fv.minor) return false;
	if(this.minor > fv.minor) return true;
	if(this.rev < fv.rev) return false;
	return true;
}
/* ---- get value of query string param ---- */
deconcept.util = {
	getRequestParameter: function(param) {
		var q = document.location.search || document.location.hash;
		if (param == null) { return q; }
		if(q) {
			var pairs = q.substring(1).split("&");
			for (var i=0; i < pairs.length; i++) {
				if (pairs[i].substring(0, pairs[i].indexOf("=")) == param) {
					return pairs[i].substring((pairs[i].indexOf("=")+1));
				}
			}
		}
		return "";
	}
}
/* fix for video streaming bug */
deconcept.SWFObjectUtil.cleanupSWFs = function() {
	var objects = document.getElementsByTagName("OBJECT");
	for (var i = objects.length - 1; i >= 0; i--) {
		objects[i].style.display = 'none';
		for (var x in objects[i]) {
			if (typeof objects[i][x] == 'function') {
				objects[i][x] = function(){};
			}
		}
	}
}
/* add document.getElementById if needed (mobile IE < 5) */
if (!document.getElementById && document.all) { document.getElementById = function(id) { return document.all[id]; }}

/* add some aliases for ease of use/backwards compatibility */
var getQueryParamValue = deconcept.util.getRequestParameter;
var FlashObject = deconcept.SWFObject; // for legacy support
var SWFObject = deconcept.SWFObject;



/**
 * jQuery.ScrollTo - Easy element scrolling using jQuery.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 5/25/2009
 * @author Ariel Flesler
 * @version 1.4.2
 *
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 */
;(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);

jQuery.fn.extend({
	fancyAnimate: function(startBG, finishBG, interval, callback) {
		if (interval === 'undefined') {
			interval = 450;
		}

		return this.each(function() {
			$(this).animate({backgroundColor: startBG}, interval, function () {
				$(this).animate({backgroundColor: finishBG}, interval, function () {
					if (jQuery.isFunction(callback)) {
						callback.apply($(this));
					}
				});
			});
		});
	},
});



if (typeof UP === "undefined" || !UP) {
	var UP = {};
}


UP.env = UP.env || {
	uploadWarnMsg: 'Идет закачка. Хотите остановить?',

	// Status MSG types
	msgInfo: 1,
	msgError: 2,
	msgWarn: 3,
	msgWait: 4,

	// AJAX backends
	ajaxBackend: '/script/ajax_backend.php',
	ajaxAdminBackend: '/script/ajax_admin_backend.php',

	// AJAX actions
	actionOwnerRemove: 1,
	actionOwnerUnRemove: 2,
	actionOwnerRename: 3,
	actionOwnerPassword: 4,
	actionOwnerIm: 5,
	actionSearch: 10,
	actionLive: 11,
	actionGetPage: 12,
	actionGetUploadUrl: 13,
	actionGetComments: 14,
	actionOwnerDeleteItem: 15,
	actionOwnerUnDeleteItem: 16,
	actionOwnerGetUpdatedItems: 17,

	actionAdminRemoveFeedbackMessage: 50,
	actionAdminRemoveComment: 51,

	// AJAX admin action
	actionAdminUnDeleteItem: 24,
	actionAdminDeleteItem: 25,
	actionAdminMarkItemAsSpam: 26,
	actionAdminUnMarkItemAsSpam: 27,
	actionAdminMarkItemAsAdult: 28,
	actionAdminUnMarkItemAsAdult: 29,
	actionAdminHideItem: 30,
	actionAdminUnHideItem: 31,
	debug: false,

	// cookie
	itemInfoStatusCookie: 'up_itemInfoStatusCookie'
};


// class for upload process
UP.uploadForm = function () {
	// private
	var active = false,
		startTime = null,
		bigTimeoutCount = 0,
		cProgress = 0;


	function getProgress(id) {
		if (!id || active == false) {
			return;
		}

		var numStr = '',
			speedTime = '',
			timeRemain = '',
			speed = '',
			avSpeed = 0,
			avSpeedText = '',
			text = '',
			timeGone = 0,
			reqStartTime = 0,
			reqTimeout = 0,
			movingAverageHistory = [];

		reqStartTime = UP.utils.gct();


		$.ajax({
			type: 'GET',
			url: '/progress?X-Progress-ID=' +id,
			dataType: 'json',
			error: function () {
				UP.statusMsg.show('Внимание: мониторинг закачки отключен из-за ошибки', UP.env.msgWarn, true);
				$('#upload_progress').hide(200);
				return;
			},

			success: function (upload) {
				reqTimeout = UP.utils.gct() - reqStartTime;
				if (upload.state === 'error') {
					return;
				}

				if (upload.state === 'done') {
					active = false;
					upload.percents = 100;
					$('#upload_progress').hide();
					$('#upload_status').html('Ожидайте, файл обрабатывается&hellip;');
				} else if (upload.state === 'uploading') {
					timeGone = UP.utils.gct() - startTime;


					upload.percents = parseInt(Math.floor(((upload.received / upload.size) * 100)), 10);

					if (isNaN(upload.percents)) {
						upload.percents = 0;
					}


					if (!isNaN(upload.received) && !isNaN(upload.size)) {
						numStr = UP.utils.formatSize(upload.received) +'&nbsp;из&nbsp;' +UP.utils.formatSize(upload.size);
					} else {
						numStr = '';
					}

					speedTime = upload.received / (timeGone / 1000); // bytes/s
					speed = (upload.received * 8) / (timeGone / 1000);

					// average speed
					movingAverageHistory.push(speed);
					if (movingAverageHistory.length > 10) {
						movingAverageHistory.shift();
					}

					avSpeed = calculateMovingAverage(movingAverageHistory);
					avSpeedText = UP.utils.formatSpeed(calculateMovingAverage(movingAverageHistory));

					timeRemain = UP.utils.formatTime((upload.size - upload.received) * 8 / avSpeed);

					// progress text result
					text = [numStr, '&nbsp;— ', timeRemain, ' (', avSpeedText, ')'].join('');

					// show progress
					if (upload.percents > cProgress && upload.percents > 1 && parseInt(upload.percents - cProgress, 10) > 1) {
						$("#num_progress").css('width', upload.percents +'%');
						$("#progress_text").html(text);
						cProgress = upload.percents;
					}
				}

				if ((upload.state === 'uploading') || (upload.state === 'starting')) {

					// compute timeout value
					if (reqTimeout < 500) {
						reqTimeout = 500;
					} else if (reqTimeout > 500 && reqTimeout < 750) {
						reqTimeout = 800;
					} else if (reqTimeout > 800 && reqTimeout < 1500) {
						reqTimeout = 1500;
					} else if (reqTimeout > 1500 && reqTimeout < 5000) {
						reqTimeout = 5000;
					} else {
						reqTimeout = 6000;
						bigTimeoutCount++;
					}

					reqTimeout = 5000;

					if (bigTimeoutCount === 5) {
						UP.statusMsg.show('Мониторинг закачки отключен из-за таймаутов. Загрузка файла продолжается&hellip;', UP.env.msgWarn, true);
						return;
					}

					// start new progress
					$('#uploadForm').oneTime(reqTimeout, 'progressTime', function () { getProgress(id); });
				}
			}
		});
	}


	function confirmExit() {
		if (active === true) {
			return UP.env.uploadWarnMsg;
		}
	}

	function calculateMovingAverage (history) {
		var vals = [], size, sum = 0.0, mean = 0.0, varianceTemp = 0.0, variance = 0.0, standardDev = 0.0;
		var i,
			mSum = 0,
			mCount = 0;

		size = history.length;

		// Check for sufficient data
		if (size >= 8) {
			// Clone the array and Calculate sum of the values
			for (i = 0; i < size; i++) {
				vals[i] = history[i];
				sum += vals[i];
			}

			mean = sum / size;

			// Calculate variance for the set
			for (i = 0; i < size; i++) {
				varianceTemp += Math.pow((vals[i] - mean), 2);
			}

			variance = varianceTemp / size;
			standardDev = Math.sqrt(variance);

			//Standardize the Data
			for (i = 0; i < size; i++) {
				vals[i] = (vals[i] - mean) / standardDev;
			}

			// Calculate the average excluding outliers
			var deviationRange = 2.0;
			for (i = 0; i < size; i++) {

				if (vals[i] <= deviationRange && vals[i] >= -deviationRange) {
					mCount++;
					mSum += history[i];
				}
			}

		} else {
			// Calculate the average (not enough data points to remove outliers)
			mCount = size;
			for (i = 0; i < size; i++) {
				mSum += history[i];
			}
		}

		return mSum / mCount;
	}


	//public
	return {
		start: function () {
			UP.statusMsg.clear(); 		// clear status area
			active = true; 	// set start

			$('#uploadSubmit').attr("disabled", "disabled");

			$('#upload_status')
				.html('Ожидайте, файл загружается&hellip; <a href="/" id="link_abort_upload">отменить</a>')
				.fadeIn(350);

			// set onunload event
			var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
			if (typeof root.onbeforeunload !== "undefined") {
				root.onbeforeunload = confirmExit;
			} else {
				window.onbeforeunload = function (o) {
					if (confirmExit()) {
						o.returnValue = confirmExit();
					}
				};
			}

			// start deffered upload progress
			var progressId = $('#progress_id').val();
			if (progressId && active) {
				$('#uploadForm')
					.stopTime('progressTime')
					.oneTime(2000, 'progressTime',
					function () { getProgress(progressId); });
			}

			$('#upload_progress').fadeIn(450);

			startTime = UP.utils.gct();
			return true;
		},


		//
		finish: function (id, pass) {
			active = false;
			$('#uploadFile').removeAttr("disabled");
			$('#uploadForm').stopTime('progressTime');

			var options = {
				pass: pass,
			}
			UP.utils.makePOSTRequest('/'+id+'/', options);
		},


		//
		error: function (msg) {
			active = false;
			$('#uploadFile').removeAttr('disabled').focus();
			$('#uploadForm').stopTime('progressTime');

			$('#upload_progress,#upload_status').hide();
			UP.formCheck.upload();

			if (msg && msg.length > 0) {
				UP.statusMsg.show(msg, UP.env.msgError, true);
			}
		}
	};
}();


// class for media functions
UP.media = {
	mp3: function (blockId, link) {
		var mp3obj = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"' +
			'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0">' +
			'<param name="allowScriptAccess" value="sameDomain"/>' +
			'<param name="movie" value="/player/mp3.swf?file='+link+'&startplay=true"/>' +
			'<param name="quality" value="high"/>' +
			'<param name="startplay" value="true"/>' +
			'<param name="bgcolor" value="#ffffff"/>' +
			'<embed src="/flash/mp3.swf?file='+link+'&startplay=true"' +
				'startplay="true" quality="high" bgcolor="#ffffff" width="96" height="20"' +
				'name="own_flashplayer" align="middle" allowScriptAccess="sameDomain"' +
				'type="application/x-shockwave-flash"' +
				'pluginspage="http://www.macromedia.com/go/getflashplayer"/>' +
		'</object>';

		$('#'+blockId).html(mp3obj);
	},


	flv: function (blockId, link) {
		var so = new SWFObject('/flash/player.swf',blockId, '512', '384','0','#FFFFFF');
		so.addParam('allowFullscreen', 'false');
		so.addVariable('bgColor', '#000000');
		so.addVariable('video', link);
		so.addVariable('css','/style/default.css');
		so.addVariable('skin','/flash/default.swf');
		so.addVariable('cover','');
		so.write(blockId);
	}
};


// class form owner functions
UP.owner = function () {
	var waitTime = 500,
		primary = null;

	function wait(label, timeout) {
		UP.statusMsg.clear();
		$(document)
			.stopTime(label)
			.oneTime(timeout, label, function () {
					UP.statusMsg.show('Ожидайте&hellip;', UP.env.msgWait, false);
				});
	}

	function startWait() {
		wait('waitTimer', waitTime);
	}

	function stopWait() {
		$(document).stopTime('waitTimer');
	}

	function onError(msg) {
		if (msg === undefined) {
			UP.statusMsg.show('<strong>Ошибка: </strong>AJAX запроса', UP.env.msgError, true);
		} else {
			UP.statusMsg.show('<strong>Ошибка: </strong>'+msg, UP.env.msgError, true);
		}
		$("#owner_delete_link, #owner_rename_link").attr("status", "on");
	}


	return {
		rename: function(id, magic) {
			if ($("#owner_rename_link").attr("status") === 'off') {
				return false;
			}

			var currentName = $('#item_info_filename').text(),
				newName = prompt("Введите новое имя для файла", currentName);

			if (newName === null) {
				return;
			}

			// same name
			if (currentName === newName) {
				return;
			}

			startWait();

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerRename, t_id: id, t_magic: magic, t_new_name: newName },
				dataType: 'json',
				complete: function () {
					stopWait();
				},
				error: function () {
					onError();
				},
				success: function(data) {
					if (parseInt(data.result, 10) === 1) {
						$('#item_info_filename').fadeOut(350, function() {
							$(this).text(data.message);
						}).fadeIn(250);
					} else {
						onError(data.message);
					}
				}
			});
		},


		changePassword: function(id, magic) {
			if ($("#owner_password_link").attr("status") === 'off') {
				return false;
			}

			UP.statusMsg.clear();

			var isChangeCurrent = parseInt($("#owner_password_link").attr("rel"), 10);

			if (isChangeCurrent === 1) {
				newPassword = prompt("Введите новый пароль для файла.\nПустая строка отключает пароль.");
			} else {
				newPassword = prompt("Введите пароль для файла");
			}

			if (newPassword === null) {
				return;
			}

			startWait();

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerPassword, t_id: id, t_magic: magic, t_password: newPassword },
				dataType: 'json',
				complete: function () {
					stopWait();
				},
				error: function () {
					onError();
				},
				success: function(data) {
					if (parseInt(data.result, 10) === 1) {

					    function hideShowItemsLables() {
						if ($('span[rel="item_label"]:visible').size() > 0) {
						    $('#item_labels').show(250);
						} else {
						    $('#item_labels').hide(250);
						}
					    }
						if (parseInt(data.message, 10) === 0) {
							var okMsg = (parseInt(isChangeCurrent, 10) === 1) ? 'Пароль отключен' : 'Пароль не установлен';
							$('#passwordLabel').hide(350, function () {
							    $("#owner_password_link").attr("rel", 0);
							    $("#owner_password_link").html("установить&nbsp;пароль").attr("title", 'Установить пароль на файл');
							    hideShowItemsLables();
							});
						} else {
							var okMsg = (parseInt(isChangeCurrent, 10) === 1) ? 'Пароль изменён' : 'Пароль установлен';
							$('#passwordLabel').show(350, function () {
							    $("#owner_password_link").attr("rel", 1);
							    $("#owner_password_link").html("изменить&nbsp;пароль").attr("title", 'Сменить пароль на файл');
							    hideShowItemsLables();
							});
						}



						UP.statusMsg.show(okMsg, UP.env.msgInfo, true);
					} else {
						onError(data.message);
					}
				}
			});
		},


		remove: function(id, magic) {
			if ($("#owner_delete_link").attr("status") === 'off') {
				return false;
			}

			primary = $('#primary').html();
			startWait();

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerRemove, t_id: id, t_magic: magic },
				dataType: 'json',
				beforeSend: function() {
					$("#owner_delete_link, #owner_rename_link").attr("status", "off");
				},
				complete: function() {
					stopWait();
				},
				error: function() {
					onError();
				},
				success: function(data)	{
					if (parseInt(data.result, 10) === 1) {
						$('#primary').fadeOut(350, function() {
							$('#primary').html('<div id="status">&nbsp;</div><div id="r1"><h2>Файл удалён</h2>' +
								'<p>Примечание: удалён владельцем файла</p></div>' +
								'<div id="unremoveBlock" style="margin-top: .6em;">' +
								'<a href="/">Перейти на главную страницу</a> или <span class="as_js_link" id="unremoveLink">отменить удаление</span>?</div>');

								$('#unremoveLink').mousedown(function() { UP.owner.unRemove(id, magic); });
							}).fadeIn(250);
					} else {
						onError(data.message);
					}
				}
			});
		},

		unRemove: function(id, magic) {
			startWait();

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerUnRemove, t_id: id, t_magic: magic },
				dataType: 'json',
				complete: function() {
					stopWait();
				},
				error: function() {
					onError();
				},
				success: function(data) {
					stopWait();
					if (parseInt(data.result, 10) === 1) {
						$('#primary').fadeOut(200, function() {
							$('#primary').html(primary);
							primary = '';
						}).fadeIn(300);
					} else {
						onError(data.message);
					}

					$(document).oneTime(500, 'z13', function () {
						$(".itemNotOwnerActions li span.as_js_link").click(function () { UP.utils.JSLinkListToggle($(this)); });
					});
				}
			});
		},

		makeMeOWner: function(user_id, item_id, magic) {
			startWait();

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerIm, t_id: item_id, t_magic: magic, t_uid: user_id },
				dataType: 'json',
				complete: function() {
					stopWait();
				},
				error: function() {
					onError();
				},
				success: function(data) {
					if (parseInt(data.result, 10) === 1) {
						$('#im_owner_block').hide(350, function () {
							UP.statusMsg.show(data.message, UP.env.msgInfo, true);
					});
					} else {
						onError(data.message);
					}
				}
			});
		}
	};
}();


// class for pretty messaging
UP.statusMsg = function () {
	// Remove message if mouse is moved or key is pressed
	function bindEvents() {
		jQuery(window).mousemove(UP.statusMsg.clear).click(UP.statusMsg.clear).keypress(UP.statusMsg.clear);
	}


	// publics
	return {
		// Unbind mouse & keyboard
		clear: function() {
			// clear
			$(document).stopTime('t1').stopTime('t2');

			// unbind events
			jQuery(window)
				.unbind('mousemove', this.clear)
				.unbind('click', this.clear)
				.unbind('keypress', this.clear);

			var empty = function () {
				$("#status").css({opacity: "0"}).html("&nbsp;");
				$("#status > span").attr("type", "default");
			};

			$("#status").stop();
			if ($("#status").css('opacity') > 0) {
				$("#status").animate({ opacity: "0.1" }, 150, empty);
			} else {
				empty();
			}
		},

		// show message
		show: function (msg, type, clearAfter) {
			var msgClass = '';

			// clearTimeouts
			$(document).stopTime('t1').stopTime('t2');
			$("#status").stop();

			jQuery(window)
				.unbind('mousemove', this.clear)
				.unbind('click', this.clear)
				.unbind('keypress', this.clear);

			switch (type) {
				case UP.env.msgError:
					msgClass = 'error';
					break;

				case UP.env.msgWarn:
					msgClass = 'warning';
					break;

				case UP.env.msgWait:
					msgClass = 'waiting';
					break;

				case UP.env.msgInfo:
					msgClass = 'info';
					break;

				default:
					msgClass = 'default';
			}

			// stop animation and show our msg
			$("#status")
				.html(['<span type="', msgClass, '">', msg, '</span>'].join(''))
				.css({opacity: "1.0"});

			if ((clearAfter === 'undefined') || (clearAfter === true)) {
				this.defferedClear();
			}
		},


		defferedClear: function() {
			// set mouse and keyboard
			$(document).stopTime('t1').oneTime(2000, 't1', function () { bindEvents(); });

			// set just timeout gone
			var that = this;
			$(document).stopTime('t2').oneTime(5000, 't2', function () { that.clear(); });
		}
	};
}();


UP.fancyLogin = function () {
	var form = $("form[name='fancyLogin']"),
		submit = form.find("input[type='submit']"),
		fancy = $('#fancyLogin');

	function close() {
		fancy.fadeOut(50);
		$('#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();
	}

	function show() {
		if (typeof document.body.style.maxHeight === "undefined") {//if IE 6
			$("body","html").css({height: "100%", width: "100%"});
			$("html").css("overflow","hidden");
			if (document.getElementById("TB_HideSelect") === null) {//iframe to hide select elements in ie6
				$("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div>");
				$("#TB_overlay").click(close);
			}
		} else {//all others
			if (document.getElementById("TB_overlay") === null){
				$("body").append("<div id='TB_overlay'></div>");
				$("#TB_overlay").click(close);
			}
		}


		$("#TB_overlay").addClass("TB_overlayBG");//use background and opacity

		// just show for Opera
		if (jQuery.browser.opera) {
			fancy.show();
		} else {
			fancy.fadeIn(100);
		}

		$(document).scrollTo(0, { duration:350, axis:'y' });
	}


	return {
		init: function () {
			if (!$('.mainMenuLogin')) {
				return;
			}

			UP.formCheck.register(form);

			$(form).find("input[required],textarea[required]")
				.change(function () { UP.formCheck.register(form); })
				.keyup(function () { UP.formCheck.register(form);	})

			$(document)
				.stopTime('checkFancyLoginFormTimer')
				.everyTime(500, 'checkFancyLoginFormTimer', function () { UP.formCheck.register(form); });

			$('.mainMenuLogin').click(function () {
				if (fancy.is(":visible")) {
					close();
				} else {
					show();
					UP.formCheck.register(form);
				}

				$(form).find("[required][value='']:first").focus();
				return false;
			}).addClass("as_js_link");

			$("input[name='close']").click(function () {
				close();
			});

			$(document).bind('keydown click', function (e) {
            	if ( ((e.keyCode == 27) && !(e.ctrlKey || e.altKey))) {
					if ($('#fancyLogin').is(":visible")) {
						close();
					}
				}
			});

			// form
			var options = {
				url:	'/login/',
				dataType: 'json',
				resetForm: false,
				cleanForm: false,
				data: { json: 1 },
				beforeSubmit: function (formArray, jqForm) {
					UP.wait.start();
					$(document).stopTime('checkFancyLoginFormTimer');
					submit.attr("disabled", "disabled");
					return true;
				},

				error: function () {
					UP.wait.stop();
					$(document).everyTime(500, 'checkFancyLoginFormTimer', function () { UP.formCheck.register(form); });
					UP.statusMsg.show('Невозможно авторизироваться. Попробуйте позже.', UP.env.msgError, false);
				},

				success: function (r) {
					UP.wait.stop();
					submit.removeAttr("disabled");

					if (r) {
						$("label").each(function () {
							$(this).removeClass('bad').addClass('good');
						});

						if (parseInt(r.error, 10) === 0) {
							form.clearForm().resetForm();
							$("#fancyLogin").fadeTo(300, 0.01, function() {
								location.reload();
								$("[required='1'][value='']:first").focus();
							});
						} else {
							UP.statusMsg.show(r.message, UP.env.msgError, true);

							if (r.fields) {
								var fields = r.fields.split(' ');
								jQuery.each(fields, function() {
									$("label#label_" + this).removeClass('good').addClass('bad');
								});
							}

							$(form).find(".bad:first").focus();
							$(document).everyTime(500, 'checkFancyLoginFormTimer', function () { UP.formCheck.register(form); });
						}
					} else {
						UP.statusMsg.show('Невозможно авторизироваться. Попробуйте позже.', UP.env.msgError, false);
					}
				}
			};

			$(form).submit(function () {
				$(this).ajaxSubmit(options);
				return false;
			});

			$(form).bind("reset", function () {
				$(document).oneTime(100, 'z', function () { $(form).find("[required='1'][value='']:first").focus(); });
			});
		}
	};
}();


//
UP.utils = function () {
	return {
		JSLinkListToggle: function (t) {
			var itemShowID = t.attr("rel"),
				list = t.parent().parent();

			// hide all
			list.children("li").each(function () {
				var itemHideID = $(this).children("span.as_js_link").attr("rel");
				if (itemHideID != itemShowID) {
					$("#"+itemHideID).hide();
				}
			});

			$("#"+itemShowID).toggle();

			if ($("#"+itemShowID).is(":visible")) {
				$.cookie(UP.env.itemInfoStatusCookie, itemShowID, { expires: 24, path: '/' });
				$("[required='1'][value='']:first").focus();
			} else {
				$.cookie(UP.env.itemInfoStatusCookie, '', { expires: 24, path: '/' });
			}
		},

		makePOSTRequest: function (url, options) {
			try {
			  	var form = $('<form/>');

			  	form.attr('action', url);
			  	form.attr('method', 'post');
			  	form.appendTo('body');

				 if (options) {
	             	for (var n in options) {
		                $('<input type="hidden" name="'+n+'" value="'+options[n]+'"/>').appendTo(form);
					}
				}

				form.submit();
			} finally {
				form.remove();
			}
		},

		formatSize: function(bytes) {
			// bytes
			if (bytes < 1024) {
				return [bytes, ' б'].join('');
			} else if (bytes < 1048576) {
				return [Math.round(bytes/1024), ' КБ'].join('');
			} else if (bytes < 1073741824) {
				return [Math.round(bytes/1048576), ' МБ'].join('');
			} else if (bytes < 1099511627776) {
				return [Math.round((bytes / 1073741824) * 100) / 100, ' ГБ'].join('');
			} else {
				return [Math.round((bytes/1099511627776) * 100) / 100, ' ТБ'].join('');
			}
		},


		formatSpeed: function(bit) {
			if (bit < 1000) {
				return Math.round(bit) +'&nbsp;б/с';
			} else if (bit < 1000000) {
				return Math.round(bit/1000) +'&nbsp;кб/с';
			} else if (bit < 1000000000) {
				return Math.round(bit/1000000) +'&nbsp;мб/с';
			} else if (bit < 1000000000000) {
				return Math.round(bit/1000000000) +'&nbsp;гб/с';
			} else {
				return Math.round(bit/1000000000000) +'&nbsp;тб/с';
			}
		},


		formatTime: function(sec) {
			var sec = parseInt(sec, 10) || 0,
				minutes = ["минут","минуты","минуту"],
				seconds = ["секунд","секунды","секунду"];

			if (sec < 11) {
				return 'меньше 10 секунд';
			} else if (sec < 91) {
				return [sec, '&nbsp;', UP.utils.getCase(parseInt(sec, 10),seconds[0],seconds[1],seconds[2])].join('');
			} else if (sec < 3601) {
				return [Math.round(sec/60), '&nbsp;', UP.utils.getCase(parseInt(Math.round(sec/60), 10),minutes[0],minutes[1],minutes[2])].join('');
			} else {
				return [Math.round(sec/3600), '&nbsp;часов'].join('');
			}
		},


		getCase: function (value, gen_pl, gen_sg, nom_sg)
		{
			if ((value % 100 >= 5) & (value % 100 <= 20)) {
				return gen_pl;
			}

			value = value % 10;
			if (((value >= 5) & (value <= 9)) | (value === 0)) {
				return gen_pl;
			}

			if ((value >= 2) & (value <= 4)) {
				return gen_sg;
			}

			if (value === 1) {
				return nom_sg;
			}
		},

		gct: function () {
			return new Date().getTime();
		},

		getPE: function () {
			$.ajax({
	   			type: 	'GET',
	   			url: 	UP.env.ajaxBackend,
	   			data: 	{ t_action: UP.env.actionLive },
				dataType: 'json',
				error: function() { UP.statusMsg.show('<strong>Ошибка: </strong>AJAX запроса', UP.env.msgError, true); },
	   			success: function(r) {
					if (parseInt(r.result, 10) === 1) {
						var chash = $.sha1(r.message),
							hash = $('#wrap').data('hash');

						if (hash !== chash) {
							$('#wrap').data('hash', chash);
							$('#result').html(r.message);
						}
					} else {
						UP.statusMsg.show(r.message, UP.env.msgError, true);
					}
				}
			 });
		}
	};
}();


UP.wait = function () {
	return {
		start: function (msg) {
			UP.statusMsg.clear();

			msg = msg || 'Ожидайте&hellip;';

			$(document)
				.stopTime('waitTimer')
				.oneTime(400, 'waitTimer', function () {
						UP.statusMsg.show(msg, UP.env.msgWait, false);
					});
		},

		stop: function () {
			$(document).stopTime('waitTimer');
			UP.statusMsg.clear();
		}
	};
}();

//
UP.formCheck = {
	upload: function () {
		var submit = $("input[type='file']").parent().find("input[type='submit']");
		if ($("input[type='file'][value!='']").size() != 0) {
			submit.removeAttr("disabled");
		} else {
			submit.attr("disabled", "disabled");
		}
	},


	search: function (s, e) {
		var elm = e || $(this),
			minLength = parseInt(elm.attr("minLength"), 10) || 1;

		$("input[type='submit']").attr("disabled", (elm.val().length < minLength ? 'disabled' : ''));
	},

	register: function (form) {
		var input = 0,
			checkbox = 0,
			all = 0,
			minRequired = 0;

		if (!form || !form.not(":visible")) {
			//UP.log.debug('no form or not visible');
			return;
		}

		// set minRequired
		minRequired = parseInt($(form).find("input[name='form_check_required_num']").val(), 10) || 0;

		$(form).find("input[required], textarea[required]").each(function () {
			var el = $(this),
				elMinLength = parseInt($(el).attr('minlength'), 10) || 0,
				label;

				if (elMinLength > ($(el).val().length)) {
					input++;

					$(el).removeClass('good').addClass('bad');
				} else {
					// check for pattern
					if ($(el).attr("pattern")) {
						var pattern = new RegExp($(el).attr("pattern").toString(), "i");

						if (pattern.test($(el).val())) {
							$(el).removeClass('bad').addClass('good');
						} else {
							$(el).removeClass('good').addClass('bad');
							input++;
						}
					} else {
						$(el).removeClass('bad').addClass('good');
					}
				}
		});

		form.find("input[type='checkbox'][required]").each(function () {
			var el = $(this);
			if ($(el).attr('checked') === false) {
				checkbox++;
			}
		});

		all = input + checkbox;
		form.find("input[type='submit']").attr("disabled", !!(all > minRequired));
	}
};


UP.comments = function () {
	var lastCommentID = 0;

	function updateNumComments() {
		$("#commentsNum").text($(".commentList").children("li").size());
	}

	return {
		remove: function (item_id) {
			var id = parseInt(item_id, 10);

			if (id < 0) {
				alert('Отсутствует номер комментария');
				return;
			}

			$.ajax({
				type: 	'GET',
				url: 	UP.env.ajaxAdminBackend,
				data: 	{ t_action: UP.env.actionAdminRemoveComment, t_id: id },
				dataType: 'json',
				error: function() {
					UP.wait.stop();
					UP.statusMsg.show('Невозможно удалить комментарий', UP.env.msgError, false);
				},
				success: function(data) {
					UP.wait.stop();
					var comment = $("#comment_"+id);

					if (parseInt(data.result, 10) === 1) {
						comment.hide(350, function () {
							$(this).remove();
							updateNumComments();
						});
					} else {
						//UP.utils.fancyAnimate(comment, "#fa9cac", "#ffffff");
						comment.fancyAnimate("#fa9cac", "#ffffff");
						UP.statusMsg.show(data.message, UP.env.msgError, false);
					}
				}
			});
		},

		loadCommentsList: function (item_id, owner_id) {
			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionGetComments, t_id: item_id, t_last_id: lastCommentID, t_owner_id: owner_id },
				dataType: 'json',
				beforeSend: function () {
					$(document).oneTime(250, 'commentAddWaitTimer', function () {
						$("#commentStatus").html('<span type="waiting">Ожидайте, загружаются новые комментарии&hellip;</span>').show(200);
					});
				},
				error: function () {
					$(document).stopTime('commentAddWaitTimer');
					$("#commentStatus").html('<span type="error">Невозможно загрузить комментарии</span>').show(200);
				},
				success: function (data) {
					$(document).stopTime('commentAddWaitTimer');
					$("#commentStatus").html('&nbsp;');
					if (parseInt(data.result, 10) === 1) {
						$(".commentList").append(data.message);
						// mark new comments
						if (lastCommentID != 0) {
							$(".commentList").children("li").each(function () {
								var item = $(this),
									item_id = parseInt(item.attr('id').split('comment_')[1], 10);

									//UP.log.debug("Flash new : #"+item_id)

								if (lastCommentID < item_id) {
									//UP.utils.fancyAnimate($("#comment_"+item_id), "#e4f2fd", "#ffffff");
									$("#comment_"+item_id).fancyAnimate("#e4f2fd", "#ffffff");
								}
							});
						}
						// get last comment ID
						if ($(".commentList").children("li").size() > 0) {
							lastCommentID = parseInt($(".commentList").children("li:last").attr('id').split('comment_')[1], 10) || 0;
						} else {
							lastCommentID = 1;
						}

						// update coments counter
						updateNumComments();
					} else {
						$("#commentStatus").html('<span type="error">'+data.message+'</span>').show(200);
					}
				}
			});
		}
	};
}();


UP.log = function () {
	return {
		debug: function () {
			 if (UP.env.debug === true && window.console && window.console.log) {
        		window.console.log('[АП] ' + Array.prototype.join.call(arguments,''));
			}
		}
	};
}();


// class for admin functions
UP.userFiles = function () {
	var waitTimeout = 400,
		maxItems = 100,

		// type of value of cb
		normal = 1,
		deleted = 2,
		hidden = 3;


	function getCheckedItemsID() {
		var items = [],
			i = 0;

		$(':checkbox:checked').each(function () {
				if (i >= maxItems) {
					return false;
				}
				var id = parseInt($(this).attr('id').split('item_cb_')[1], 10);

				if (! isNaN(id)) {
					items.push(id);
					i = i + 1;
				}
			}
		);

		return items.join(':');
	}

	function getAffectedItemsID(type) {
		var items = [],
			i = 0;

		$([':checkbox[value=', type, ']'].join(''))
			.each(function () {
				if (i >= maxItems) {
					return false;
				}
				var id = parseInt($(this).attr('id').split('item_cb_')[1], 10);

				if (! isNaN(id)) {
					items.push(id);
					i = i + 1;
				}
			}
		);

		return items.join(':');
	}


	function showNumCheckedCB() {
		return $(":checkbox:checked[value='1']:visible").size();
	}


	function cbResultSuccess(itemsAsString, undo, type) {
		var itemsOK = itemsAsString.split(':'),
		    ok_num = 0,
		    i = 0,
		    max = 0,
		    id;

		if (!undo) {
		    $(':checkbox[value='+type+']').each(function () {
			var hiddenID = parseInt($(this).attr('id').split('item_cb_')[1], 10);
			$('#row_item_'+hiddenID).remove();
			UP.log.debug('RemoveHidden: '+hiddenID);
		    });
		}

		for (i = 0, max = itemsOK.length; i <= max; i = i + 1) {
			id = parseInt(itemsOK[i], 10);

			if (!isNaN(id) && id > 0) {
				if (undo === true) {
					$('#item_cb_' + id).attr('value', 1).attr("checked", true);
					if (type == deleted) {
					    $('#row_item_' + id).addClass('selected').show();
					}
				} else {
					$('#item_cb_' + id).removeAttr('checked').attr('value', type);
					if (type == deleted) {
					    $('#row_item_' + id).removeClass('selected').hide();
					}
				}
				ok_num = ok_num + 1;
			}
		}

		checkButtonsDisabled();

		//
		return ok_num;
	}


	function wait(label, timeout) {
		$(document).stopTime(label).oneTime(timeout, label, function () {
			UP.statusMsg.show('Ожидайте&hellip;', UP.env.msgWait, false);
		});
	}

	function onComplete(timerLabel) {
		$(document).stopTime(timerLabel);
		$(':checkbox').removeAttr('disabled');
		$('#allCB').removeAttr('checked');
		checkButtonsDisabled();
	}

	function onError() {
		UP.statusMsg.show('<strong>Ошибка: </strong>AJAX запроса', UP.env.msgError, true);
	}

	function checkButtonsDisabled() {
		var m = showNumCheckedCB();
		$(':button').attr("disabled", (m < 1 ? 'disabled' : ''));
	}


	// public
	return {


		getUpdatedItems: function () {
			//wait('updateTimer', waitTimeout);

			var items = [];

			$(".row_item:visible").each(function () {
				var id = parseInt($(this).attr('id').split('row_item_')[1], 10);

				if (! isNaN(id)) {
					items.push(id);
				}
			});

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerGetUpdatedItems, t_ids: items.join(':') },
				dataType: 'json',
				error: 	function () {
					UP.log.debug("UP.env.actionOwnerGetUpdatedItems ERROR");
				},
				success: function (data) {
					if (parseInt(data.result, 10) === 1) {
						// PROCESS DELETED ITEMS
						var deleted = data.message.deleted.split(':');
						for (i = 0, max = deleted.length; i <= max; i = i + 1) {
							var id = parseInt(deleted[i], 10);
							if (!isNaN(id) && id > 0 && $("tr#row_item_"+id).is(":visible")) {
								$("tr#row_item_"+id).fancyAnimate("#fa9cac", "#ffffff", 350, function () {
									$(this).remove();
								});
							}
						}

						// PROCESS ADDED ITEMS
						$("#top_files_table tbody").prepend(data.message.added);
					} else {
						UP.log.debug("UP.env.actionOwnerGetUpdatedItems ERROR: "+data.message);
					}
				}
			});
		},



		//
		deleteItem: function (undo) {
			var items = getCheckedItemsID(),
				actions = UP.env.actionOwnerDeleteItem,
				undoLink = '<span class="as_js_link" onclick="UP.userFiles.deleteItem(true);" title="Отменить удаление">Отменить</span>';

			if (undo === true) {
				items = getAffectedItemsID(deleted);
				actions = UP.env.actionOwnerUnDeleteItem;
				undoLink = '';
			}

			if (!items) {
				return;
			}

			wait('deleteTimer', waitTimeout);

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: actions, t_ids: items },
				dataType: 'json',
				beforeSend: function () {
					$(':checkbox, :button').attr('disabled', 'disabled'); // disable all input
				},
				complete: function () {
					onComplete('deleteTimer');
				},
				error: 	function () {
					onError();
				},
				success: function (data) {
					if (parseInt(data.result, 10) === 1) {
						var ok_num = cbResultSuccess(data.message, undo, deleted);

						if (undo === true) {
							UP.statusMsg.clear();
						} else {
							var msgDelete = UP.utils.getCase((ok_num), 'Удалено ', 'Удалены ', 'Удалён '),
								msgOK = [msgDelete, ok_num, UP.utils.getCase((ok_num), ' файлов', ' файла', ' файл'), undoLink].join('');
							UP.statusMsg.show(msgOK, UP.env.msgInfo, false);
						}
					} else {
						UP.statusMsg.show(data.message, UP.env.msgError, true);
					}
				}
			});
		},

		cbStuffStart: function () {
			var state,
				n = 0,
				m = 0,
				box,
				id;
			$(':checkbox').attr('checked', false); 	// make all unchecked

			//
			$('#allCB').bind('change', function () {
				state = $(this).attr('checked');
				$(":checkbox[value='1']:visible").attr('checked', state);

				n = showNumCheckedCB();
				$(':button').attr("disabled", (n < 1 ? 'disabled' : ''));

				$("tr.row_item").toggleClass('selected', state);
			});

			//
			$(":checkbox[value='1']:visible").live('change', function () {
				m = showNumCheckedCB();
				$(':button').attr("disabled", (m < 1 ? 'disabled' : ''));

				// select
				box = $(this);
				id = parseInt(box.attr('id').split('item_cb_')[1], 10);

				if (!isNaN(id)) {
					if (box.attr('checked')) {
						$('#row_item_' + id).addClass('selected');
					} else {
						$('#row_item_' + id).removeClass('selected');
					}
				}
			});
		}

	};
}();



// On start on every page
jQuery(function () {
	UP.fancyLogin.init();
	$(".as_js_link, .head_menu > a, label").noSelect();
});




/*!
 * http://www.shamasis.net/projects/ga/
 * Refer jquery.ga.debug.js
 * Revision: 13
 */
(function($){$.ga={};$.ga.load=function(uid,callback){jQuery.ajax({type:'GET',url:(document.location.protocol=="https:"?"https://ssl":"http://www")+'.google-analytics.com/ga.js',cache:true,success:function(){if(typeof _gat==undefined){throw"_gat has not been defined";}t=_gat._getTracker(uid);bind();if($.isFunction(callback)){callback(t)}t._trackPageview()},dataType:'script',data:null})};var t;var bind=function(){if(noT()){throw"pageTracker has not been defined";}for(var $1 in t){if($1.charAt(0)!='_')continue;$.ga[$1.substr(1)]=t[$1]}};var noT=function(){return t==undefined}})(jQuery);


