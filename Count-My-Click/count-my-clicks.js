/*	The following Javascript includes code from
 *		http://verens.com/archives/2005/03/21/tracking-external-links-with-ajax/
 *	and
 *		http://www.xml.com/pub/a/2005/02/09/xml-http-request.html
 *	with cleanup, tweaks and integration by
 *		http://www.skeltoac.com/
 */
function addEvent(el,ev,fn){
	var isIE=window.attachEvent?true:false;
	if(isIE)el.attachEvent('on'+ev,fn);
	else if(el.addEventListener)el.addEventListener(ev,fn,false);
}
function linkmousedown(event) {
	var isIE=window.attachEvent?true:false;
	event=event?event:(window.event?window.event:"");
	var m=isIE?window.event.srcElement:event.currentTarget;
	m.modo = true;
}
function linkmouseout(event) {
	var isIE=window.attachEvent?true:false;
	event=event?event:(window.event?window.event:"");
	var m=isIE?window.event.srcElement:event.currentTarget;
	m.modo = false;
}
function linkmouseup(event) {
	var isIE=window.attachEvent?true:false;
	event=event?event:(window.event?window.event:"");
	var m=isIE?window.event.srcElement:event.currentTarget;
	if (m.modo) linktracker_record(event);
}
function linktracker_init(){
	var localserver=document.location.toString().replace(/^[^\/]*\/+([^\/]*)(\/.*)?/,'$1');
	var els=document.getElementsByTagName('a');
	for(var i=0;i<els.length;i++){
		var href=els[i].href;
		if(href.match(eval('/^(http(s)?:\\/\\/)?'+localserver+'/')))continue;
		addEvent(els[i],'mousedown',linkmousedown);
		addEvent(els[i],'mouseout',linkmouseout);
		addEvent(els[i],'mouseup',linkmouseup);
	}
}
function linktracker_record(event){
	var isIE=window.attachEvent?true:false;
	event=event?event:(window.event?window.event:"");
	var b=isIE?window.event.srcElement:event.currentTarget;
	while (b.nodeName != "A") {
		if ( b.parentNode == undefined ) return;
		b = b.parentNode;
	}
	var bh=b.href;
	bh=bh.replace('://','/:/');
	var url=cmc_php+bh;
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
		req.open("GET", url, true);
		req.send(null);
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
		if (req) {
			req.open("GET", url, true);
			req.send();
		}
	}
}