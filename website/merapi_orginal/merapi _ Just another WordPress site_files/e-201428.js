function st_go(a){var i,u=document.location.protocol+'//stats.wordpress.com/g.gif?host='+escape(document.location.host)+'&rand='+Math.random();for(i in a){u=u+'&'+i+'='+escape(a[i]);}u=u+'&ref='+escape(document.referrer);document.open();document.write("<img id=\"wpstats\" src=\""+u+"\" alt=\"\" />");document.close();}
function wpcomAddEvent(el,ev,fn){var isIE=window.attachEvent?true:false;if(isIE)el.attachEvent('on'+ev,fn);else if(el.addEventListener)el.addEventListener(ev,fn,false);}
function linkmousedown(event){var isIE=window.attachEvent?true:false;event=event?event:(window.event?window.event:"");var m=isIE?window.event.srcElement:event.currentTarget;m.modo=true;}
function linkmouseout(event){var isIE=window.attachEvent?true:false;event=event?event:(window.event?window.event:"");var m=isIE?window.event.srcElement:event.currentTarget;m.modo=false;}
function linkmouseup(event){var isIE=window.attachEvent?true:false;event=event?event:(window.event?window.event:"");var m=isIE?window.event.srcElement:event.currentTarget;if(m.modo)linktracker_record(event);}
function linkclick(event){var isIE=window.attachEvent?true:false;event=event?event:(window.event?window.event:"");linktracker_record(event);}
function linktracker_init(b,p){_blog=b;_post=p;if(typeof document.location.host!='undefined')
var localserver=document.location.host;else
var localserver=document.location.toString().replace(/^[^\/]*\/+([^\/]*)(\/.*)?/,'$1');var els=document.getElementsByTagName('a');for(var i=0;i<els.length;i++){var href=els[i].href;if(href.match(eval('/^(http(s)?:\\/\\/)?'+localserver+'/')))continue;if(href.match(eval('/^javascript/')))continue;wpcomAddEvent(els[i],'mousedown',linkmousedown);wpcomAddEvent(els[i],'mouseout',linkmouseout);wpcomAddEvent(els[i],'mouseup',linkmouseup);}}
function linktracker_record(event){var isIE=window.attachEvent?true:false;event=event?event:(window.event?window.event:"");var b=isIE?window.event.srcElement:event.currentTarget;while(b.nodeName!="A"){if(typeof b.parentNode=='undefined')return;b=b.parentNode;}
var bh=b.href;var pr=document.location.protocol||'http:';var b=(typeof _blog!='undefined')?_blog:0;var p=(typeof _post!='undefined')?_post:0;var i=new Image(1,1);i.src=pr+'//stats.wordpress.com/c.gif?s=2&b='+b+'&p='+p+'&u='+escape(bh);i.onLoad=function(){cmcVoid();}}
function cmcVoid(){return;}