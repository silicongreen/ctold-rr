window.Modernizr=function(e,t,n){function A(e){f.cssText=e}function O(e,t){return A(p.join(e+";")+(t||""))}function M(e,t){return typeof e===t}function _(e,t){return!!~(""+e).indexOf(t)}function D(e,t){for(var r in e)if(f[e[r]]!==n)return t=="pfx"?e[r]:!0;return!1}function P(e,t,r){for(var i in e){var s=t[e[i]];if(s!==n)return r===!1?e[i]:M(s,"function")?s.bind(r||t):s}return!1}function H(e,t,n){var r=e.charAt(0).toUpperCase()+e.substr(1),i=(e+" "+v.join(r+" ")+r).split(" ");if(M(t,"string")||M(t,"undefined"))return D(i,t);i=(e+" "+m.join(r+" ")+r).split(" ");return P(i,t,n)}function j(){i.input=function(n){for(var r=0,i=n.length;r<i;r++)w[n[r]]=n[r]in l;w.list&&(w.list=!!t.createElement("datalist")&&!!e.HTMLDataListElement);return w}("autocomplete autofocus list placeholder max min multiple pattern required step".split(" "));i.inputtypes=function(e){for(var r=0,i,s,u,a=e.length;r<a;r++){l.setAttribute("type",s=e[r]);i=l.type!=="text";if(i){l.value=c;l.style.cssText="position:absolute;visibility:hidden;";if(/^range$/.test(s)&&l.style.WebkitAppearance!==n){o.appendChild(l);u=t.defaultView;i=u.getComputedStyle&&u.getComputedStyle(l,null).WebkitAppearance!=="textfield"&&l.offsetHeight!==0;o.removeChild(l)}else if(!/^(search|tel)$/.test(s))if(/^(url|email)$/.test(s))i=l.checkValidity&&l.checkValidity()===!1;else if(/^color$/.test(s)){o.appendChild(l);o.offsetWidth;i=l.value!=c;o.removeChild(l)}else i=l.value!=c}b[e[r]]=!!i}return b}("search tel url email datetime date month week time datetime-local number range color".split(" "))}var r="2.5.3",i={},s=!0,o=t.documentElement,u="modernizr",a=t.createElement(u),f=a.style,l=t.createElement("input"),c=":)",h={}.toString,p=" -webkit- -moz- -o- -ms- ".split(" "),d="Webkit Moz O ms",v=d.split(" "),m=d.toLowerCase().split(" "),g={svg:"http://www.w3.org/2000/svg"},y={},b={},w={},E=[],S=E.slice,x,T=function(e,n,r,i){var s,a,f,l=t.createElement("div"),c=t.body,h=c?c:t.createElement("body");if(parseInt(r,10))while(r--){f=t.createElement("div");f.id=i?i[r]:u+(r+1);l.appendChild(f)}s=["&#173;","<style>",e,"</style>"].join("");l.id=u;h.innerHTML+=s;h.appendChild(l);if(!c){h.style.background="";o.appendChild(h)}a=n(l,e);c?l.parentNode.removeChild(l):h.parentNode.removeChild(h);return!!a},N=function(t){var n=e.matchMedia||e.msMatchMedia;if(n)return n(t).matches;var r;T("@media "+t+" { #"+u+" { position: absolute; } }",function(t){r=(e.getComputedStyle?getComputedStyle(t,null):t.currentStyle)["position"]=="absolute"});return r},C=function(){function r(r,i){i=i||t.createElement(e[r]||"div");r="on"+r;var s=r in i;if(!s){i.setAttribute||(i=t.createElement("div"));if(i.setAttribute&&i.removeAttribute){i.setAttribute(r,"");s=M(i[r],"function");M(i[r],"undefined")||(i[r]=n);i.removeAttribute(r)}}i=null;return s}var e={select:"input",change:"input",submit:"form",reset:"form",error:"img",load:"img",abort:"img"};return r}(),k={}.hasOwnProperty,L;!M(k,"undefined")&&!M(k.call,"undefined")?L=function(e,t){return k.call(e,t)}:L=function(e,t){return t in e&&M(e.constructor.prototype[t],"undefined")};Function.prototype.bind||(Function.prototype.bind=function(t){var n=this;if(typeof n!="function")throw new TypeError;var r=S.call(arguments,1),i=function(){if(this instanceof i){var e=function(){};e.prototype=n.prototype;var s=new e,o=n.apply(s,r.concat(S.call(arguments)));return Object(o)===o?o:s}return n.apply(t,r.concat(S.call(arguments)))};return i});var B=function(n,r){var s=n.join(""),o=r.length;T(s,function(n,r){var s=t.styleSheets[t.styleSheets.length-1],u=s?s.cssRules&&s.cssRules[0]?s.cssRules[0].cssText:s.cssText||"":"",a=n.childNodes,f={};while(o--)f[a[o].id]=a[o];i.touch="ontouchstart"in e||e.DocumentTouch&&t instanceof DocumentTouch||(f.touch&&f.touch.offsetTop)===9;i.csstransforms3d=(f.csstransforms3d&&f.csstransforms3d.offsetLeft)===9&&f.csstransforms3d.offsetHeight===3;i.generatedcontent=(f.generatedcontent&&f.generatedcontent.offsetHeight)>=1;i.fontface=/src/i.test(u)&&u.indexOf(r.split(" ")[0])===0},o,r)}(['@font-face {font-family:"font";src:url("https://")}',["@media (",p.join("touch-enabled),("),u,")","{#touch{top:9px;position:absolute}}"].join(""),["@media (",p.join("transform-3d),("),u,")","{#csstransforms3d{left:9px;position:absolute;height:3px;}}"].join(""),['#generatedcontent:after{content:"',c,'";visibility:hidden}'].join("")],["fontface","touch","csstransforms3d","generatedcontent"]);y.flexbox=function(){return H("flexOrder")};y["flexbox-legacy"]=function(){return H("boxDirection")};y.canvas=function(){var e=t.createElement("canvas");return!!e.getContext&&!!e.getContext("2d")};y.canvastext=function(){return!!i.canvas&&!!M(t.createElement("canvas").getContext("2d").fillText,"function")};y.webgl=function(){try{var r=t.createElement("canvas"),i;i=!(!e.WebGLRenderingContext||!r.getContext("experimental-webgl")&&!r.getContext("webgl"));r=n}catch(s){i=!1}return i};y.touch=function(){return i.touch};y.geolocation=function(){return!!navigator.geolocation};y.postmessage=function(){return!!e.postMessage};y.websqldatabase=function(){return!!e.openDatabase};y.indexedDB=function(){return!!H("indexedDB",e)};y.hashchange=function(){return C("hashchange",e)&&(t.documentMode===n||t.documentMode>7)};y.history=function(){return!!e.history&&!!history.pushState};y.draganddrop=function(){var e=t.createElement("div");return"draggable"in e||"ondragstart"in e&&"ondrop"in e};y.websockets=function(){for(var t=-1,n=v.length;++t<n;)if(e[v[t]+"WebSocket"])return!0;return"WebSocket"in e};y.rgba=function(){A("background-color:rgba(150,255,150,.5)");return _(f.backgroundColor,"rgba")};y.hsla=function(){A("background-color:hsla(120,40%,100%,.5)");return _(f.backgroundColor,"rgba")||_(f.backgroundColor,"hsla")};y.multiplebgs=function(){A("background:url(https://),url(https://),red url(https://)");return/(url\s*\(.*?){3}/.test(f.background)};y.backgroundsize=function(){return H("backgroundSize")};y.borderimage=function(){return H("borderImage")};y.borderradius=function(){return H("borderRadius")};y.boxshadow=function(){return H("boxShadow")};y.textshadow=function(){return t.createElement("div").style.textShadow===""};y.opacity=function(){O("opacity:.55");return/^0.55$/.test(f.opacity)};y.cssanimations=function(){return H("animationName")};y.csscolumns=function(){return H("columnCount")};y.cssgradients=function(){var e="background-image:",t="gradient(linear,left top,right bottom,from(#9f9),to(white));",n="linear-gradient(left top,#9f9, white);";A((e+"-webkit- ".split(" ").join(t+e)+p.join(n+e)).slice(0,-e.length));return _(f.backgroundImage,"gradient")};y.cssreflections=function(){return H("boxReflect")};y.csstransforms=function(){return!!H("transform")};y.csstransforms3d=function(){var e=!!H("perspective");e&&"webkitPerspective"in o.style&&(e=i.csstransforms3d);return e};y.csstransitions=function(){return H("transition")};y.fontface=function(){return i.fontface};y.generatedcontent=function(){return i.generatedcontent};y.video=function(){var e=t.createElement("video"),n=!1;try{if(n=!!e.canPlayType){n=new Boolean(n);n.ogg=e.canPlayType('video/ogg; codecs="theora"').replace(/^no$/,"");n.h264=e.canPlayType('video/mp4; codecs="avc1.42E01E"').replace(/^no$/,"");n.webm=e.canPlayType('video/webm; codecs="vp8, vorbis"').replace(/^no$/,"")}}catch(r){}return n};y.audio=function(){var e=t.createElement("audio"),n=!1;try{if(n=!!e.canPlayType){n=new Boolean(n);n.ogg=e.canPlayType('audio/ogg; codecs="vorbis"').replace(/^no$/,"");n.mp3=e.canPlayType("audio/mpeg;").replace(/^no$/,"");n.wav=e.canPlayType('audio/wav; codecs="1"').replace(/^no$/,"");n.m4a=(e.canPlayType("audio/x-m4a;")||e.canPlayType("audio/aac;")).replace(/^no$/,"")}}catch(r){}return n};y.localstorage=function(){try{localStorage.setItem(u,u);localStorage.removeItem(u);return!0}catch(e){return!1}};y.sessionstorage=function(){try{sessionStorage.setItem(u,u);sessionStorage.removeItem(u);return!0}catch(e){return!1}};y.webworkers=function(){return!!e.Worker};y.applicationcache=function(){return!!e.applicationCache};y.svg=function(){return!!t.createElementNS&&!!t.createElementNS(g.svg,"svg").createSVGRect};y.inlinesvg=function(){var e=t.createElement("div");e.innerHTML="<svg/>";return(e.firstChild&&e.firstChild.namespaceURI)==g.svg};y.smil=function(){return!!t.createElementNS&&/SVGAnimate/.test(h.call(t.createElementNS(g.svg,"animate")))};y.svgclippaths=function(){return!!t.createElementNS&&/SVGClipPath/.test(h.call(t.createElementNS(g.svg,"clipPath")))};for(var F in y)if(L(y,F)){x=F.toLowerCase();i[x]=y[F]();E.push((i[x]?"":"no-")+x)}i.input||j();i.addTest=function(e,t){if(typeof e=="object")for(var r in e)L(e,r)&&i.addTest(r,e[r]);else{e=e.toLowerCase();if(i[e]!==n)return i;t=typeof t=="function"?t():t;o.className+=" "+(t?"":"no-")+e;i[e]=t}return i};A("");a=l=null;(function(e,t){function o(e,t){var n=e.createElement("p"),r=e.getElementsByTagName("head")[0]||e.documentElement;n.innerHTML="x<style>"+t+"</style>";return r.insertBefore(n.lastChild,r.firstChild)}function u(){var e=l.elements;return typeof e=="string"?e.split(" "):e}function a(e){var t={},n=e.createElement,i=e.createDocumentFragment,s=i();e.createElement=function(e){var i=(t[e]||(t[e]=n(e))).cloneNode();return l.shivMethods&&i.canHaveChildren&&!r.test(e)?s.appendChild(i):i};e.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+u().join().replace(/\w+/g,function(e){t[e]=n(e);s.createElement(e);return'c("'+e+'")'})+");return n}")(l,s)}function f(e){var t;if(e.documentShived)return e;l.shivCSS&&!i&&(t=!!o(e,"article,aside,details,figcaption,figure,footer,header,hgroup,nav,section{display:block}audio{display:none}canvas,video{display:inline-block;*display:inline;*zoom:1}[hidden]{display:none}audio[controls]{display:inline-block;*display:inline;*zoom:1}mark{background:#FF0;color:#000}"));s||(t=!a(e));t&&(e.documentShived=t);return e}var n=e.html5||{},r=/^<|^(?:button|form|map|select|textarea)$/i,i,s;(function(){var e=t.createElement("a");e.innerHTML="<xyz></xyz>";i="hidden"in e;s=e.childNodes.length==1||function(){try{t.createElement("a")}catch(e){return!0}var n=t.createDocumentFragment();return typeof n.cloneNode=="undefined"||typeof n.createDocumentFragment=="undefined"||typeof n.createElement=="undefined"}()})();var l={elements:n.elements||"abbr article aside audio bdi canvas data datalist details figcaption figure footer header hgroup mark meter nav output progress section summary time video",shivCSS:n.shivCSS!==!1,shivMethods:n.shivMethods!==!1,type:"default",shivDocument:f};e.html5=l;f(t)})(this,t);i._version=r;i._prefixes=p;i._domPrefixes=m;i._cssomPrefixes=v;i.mq=N;i.hasEvent=C;i.testProp=function(e){return D([e])};i.testAllProps=H;i.testStyles=T;i.prefixed=function(e,t,n){return t?H(e,t,n):H(e,"pfx")};o.className=o.className.replace(/(^|\s)no-js(\s|$)/,"$1$2")+(s?" js "+E.join(" "):"");return i}(this,this.document);