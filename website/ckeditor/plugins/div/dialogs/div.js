﻿(function(){function p(a,k,o){if(!k.is||!k.getCustomData("block_processed"))k.is&&CKEDITOR.dom.element.setMarker(o,k,"block_processed",!0),a.push(k)}function n(a,k){function o(){this.foreach(function(a){if(/^(?!vbox|hbox)/.test(a.type)&&(a.setup||(a.setup=function(c){a.setValue(c.getAttribute(a.id)||"",1)}),!a.commit))a.commit=function(c){var g=this.getValue();"dir"==a.id&&c.getComputedStyle("direction")==g||(g?c.setAttribute(a.id,g):c.removeAttribute(a.id))}})}var n=function(){var f=CKEDITOR.tools.extend({},
CKEDITOR.dtd.$blockLimit);a.config.div_wrapTable&&(delete f.td,delete f.th);return f}(),q=CKEDITOR.dtd.div,l={},m=[];return{title:a.lang.div.title,minWidth:400,minHeight:165,contents:[{id:"info",label:a.lang.common.generalTab,title:a.lang.common.generalTab,elements:[{type:"hbox",widths:["50%","50%"],children:[{id:"elementStyle",type:"select",style:"width: 100%;",label:a.lang.div.styleSelectLabel,"default":"",items:[[a.lang.common.notSet,""]],onChange:function(){var f=["info:elementStyle","info:class",
"advanced:dir","advanced:style"],c=this.getDialog(),g=c._element&&c._element.clone()||new CKEDITOR.dom.element("div",a.document);this.commit(g,!0);for(var f=[].concat(f),b=f.length,i,e=0;e<b;e++)(i=c.getContentElement.apply(c,f[e].split(":")))&&i.setup&&i.setup(g,!0)},setup:function(f){for(var c in l)l[c].checkElementRemovable(f,!0,a)&&this.setValue(c,1)},commit:function(f){var c;(c=this.getValue())?l[c].applyToObject(f,a):f.removeAttribute("style")}},{id:"class",type:"text",requiredContent:"div(cke-xyz)",
label:a.lang.common.cssClass,"default":""}]}]},{id:"advanced",label:a.lang.common.advancedTab,title:a.lang.common.advancedTab,elements:[{type:"vbox",padding:1,children:[{type:"hbox",widths:["50%","50%"],children:[{type:"text",id:"id",requiredContent:"div[id]",label:a.lang.common.id,"default":""},{type:"text",id:"lang",requiredContent:"div[lang]",label:a.lang.common.langCode,"default":""}]},{type:"hbox",children:[{type:"text",id:"style",requiredContent:"div{cke-xyz}",style:"width: 100%;",label:a.lang.common.cssStyle,
"default":"",commit:function(a){a.setAttribute("style",this.getValue())}}]},{type:"hbox",children:[{type:"text",id:"title",requiredContent:"div[title]",style:"width: 100%;",label:a.lang.common.advisoryTitle,"default":""}]},{type:"select",id:"dir",requiredContent:"div[dir]",style:"width: 100%;",label:a.lang.common.langDir,"default":"",items:[[a.lang.common.notSet,""],[a.lang.common.langDirLtr,"ltr"],[a.lang.common.langDirRtl,"rtl"]]}]}]}],onLoad:function(){o.call(this);var f=this,c=this.getContentElement("info",
"elementStyle");a.getStylesSet(function(g){var b,i;if(g)for(var e=0;e<g.length;e++)i=g[e],i.element&&"div"==i.element&&(b=i.name,l[b]=i=new CKEDITOR.style(i),a.filter.check(i)&&(c.items.push([b,b]),c.add(b,b)));c[1<c.items.length?"enable":"disable"]();setTimeout(function(){f._element&&c.setup(f._element)},0)})},onShow:function(){"editdiv"==k&&this.setupContent(this._element=CKEDITOR.plugins.div.getSurroundDiv(a))},onOk:function(){if("editdiv"==k)m=[this._element];else{var f=[],c={},g=[],b,i=a.getSelection(),
e=i.getRanges(),l=i.createBookmarks(),h,j;for(h=0;h<e.length;h++)for(j=e[h].createIterator();b=j.getNextParagraph();)if(b.getName()in n&&!b.isReadOnly()){var d=b.getChildren();for(b=0;b<d.count();b++)p(g,d.getItem(b),c)}else{for(;!q[b.getName()]&&!b.equals(e[h].root);)b=b.getParent();p(g,b,c)}CKEDITOR.dom.element.clearAllMarkers(c);e=[];h=null;for(j=0;j<g.length;j++)b=g[j],d=a.elementPath(b).blockLimit,d.isReadOnly()&&(d=d.getParent()),a.config.div_wrapTable&&d.is(["td","th"])&&(d=a.elementPath(d.getParent()).blockLimit),
d.equals(h)||(h=d,e.push([])),e[e.length-1].push(b);for(h=0;h<e.length;h++){d=e[h][0];g=d.getParent();for(b=1;b<e[h].length;b++)g=g.getCommonAncestor(e[h][b]);j=new CKEDITOR.dom.element("div",a.document);for(b=0;b<e[h].length;b++){for(d=e[h][b];!d.getParent().equals(g);)d=d.getParent();e[h][b]=d}for(b=0;b<e[h].length;b++)if(d=e[h][b],!d.getCustomData||!d.getCustomData("block_processed"))d.is&&CKEDITOR.dom.element.setMarker(c,d,"block_processed",!0),b||j.insertBefore(d),j.append(d);CKEDITOR.dom.element.clearAllMarkers(c);
f.push(j)}i.selectBookmarks(l);m=f}f=m.length;for(c=0;c<f;c++)this.commitContent(m[c]),!m[c].getAttribute("style")&&m[c].removeAttribute("style");this.hide()},onHide:function(){"editdiv"==k&&this._element.removeCustomData("elementStyle");delete this._element}}}CKEDITOR.dialog.add("creatediv",function(a){return n(a,"creatediv")});CKEDITOR.dialog.add("editdiv",function(a){return n(a,"editdiv")})})();