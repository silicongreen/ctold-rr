function loadHtml5Gallery(e) {
   
    jQuery.easing.jswing = jQuery.easing.swing;
    jQuery.extend(jQuery.easing, {def: "easeOutQuad", swing: function(e, t, n, r, i) {
            return jQuery.easing[jQuery.easing.def](e, t, n, r, i)
        }, easeInQuad: function(e, t, n, r, i) {
            return r * (t /= i) * t + n
        }, easeOutQuad: function(e, t, n, r, i) {
            return-r * (t /= i) * (t - 2) + n
        }, easeInOutQuad: function(e, t, n, r, i) {
            return 1 > (t /= i / 2) ? r / 2 * t * t + n : -r / 2 * (--t * (t - 2) - 1) + n
        }, easeInCubic: function(e, t, n, r, i) {
            return r * (t /= i) * t * t + n
        }, easeOutCubic: function(e, t, n, r, i) {
            return r * ((t = t / i - 1) * t * t + 1) + n
        }, easeInOutCubic: function(e, t, n, r, i) {
            return 1 > (t /= i / 2) ? r / 2 * t * t * t + n : r / 2 * ((t -= 2) * t * t + 2) + n
        }, easeInQuart: function(e, t, n, r, i) {
            return r * (t /= i) * t * t * t + n
        }, easeOutQuart: function(e, t, n, r, i) {
            return-r * ((t = t / i - 1) * t * t * t - 1) + n
        }, easeInOutQuart: function(e, t, n, r, i) {
            return 1 > (t /= i / 2) ? r / 2 * t * t * t * t + n : -r / 2 * ((t -= 2) * t * t * t - 2) + n
        }, easeInQuint: function(e, t, n, r, i) {
            return r * (t /= i) * t * t * t * t + n
        }, easeOutQuint: function(e, t, n, r, i) {
            return r * ((t = t / i - 1) * t * t * t * t + 1) + n
        }, easeInOutQuint: function(e, t, n, r, i) {
            return 1 > (t /= i / 2) ? r / 2 * t * t * t * t * t + n : r / 2 * ((t -= 2) * t * t * t * t + 2) + n
        }, easeInSine: function(e, t, n, r, i) {
            return-r * Math.cos(t / i * (Math.PI / 2)) + r + n
        }, easeOutSine: function(e, t, n, r, i) {
            return r * Math.sin(t / i * (Math.PI / 2)) + n
        }, easeInOutSine: function(e, t, n, r, i) {
            return-r / 2 * (Math.cos(Math.PI * t / i) - 1) + n
        }, easeInExpo: function(e, t, n, r, i) {
            return 0 == t ? n : r * Math.pow(2, 10 * (t / i - 1)) + n
        }, easeOutExpo: function(e, t, n, r, i) {
            return t == i ? n + r : r * (-Math.pow(2, -10 * t / i) + 1) + n
        }, easeInOutExpo: function(e, t, n, r, i) {
            return 0 == t ? n : t == i ? n + r : 1 > (t /= i / 2) ? r / 2 * Math.pow(2, 10 * (t - 1)) + n : r / 2 * (-Math.pow(2, -10 * --t) + 2) + n
        }, easeInCirc: function(e, t, n, r, i) {
            return-r * (Math.sqrt(1 - (t /= i) * t) - 1) + n
        }, easeOutCirc: function(e, t, n, r, i) {
            return r * Math.sqrt(1 - (t = t / i - 1) * t) + n
        }, easeInOutCirc: function(e, t, n, r, i) {
            return 1 > (t /= i / 2) ? -r / 2 * (Math.sqrt(1 - t * t) - 1) + n : r / 2 * (Math.sqrt(1 - (t -= 2) * t) + 1) + n
        }, easeInElastic: function(e, t, n, r, i) {
            e = 1.70158;
            var s = 0, o = r;
            if (0 == t)
                return n;
            if (1 == (t /= i))
                return n + r;
            s || (s = .3 * i);
            o < Math.abs(r) ? (o = r, e = s / 4) : e = s / (2 * Math.PI) * Math.asin(r / o);
            return-(o * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * i - e) * 2 * Math.PI / s)) + n
        }, easeOutElastic: function(e, t, n, r, i) {
            e = 1.70158;
            var s = 0, o = r;
            if (0 == t)
                return n;
            if (1 == (t /= i))
                return n + r;
            s || (s = .3 * i);
            o < Math.abs(r) ? (o = r, e = s / 4) : e = s / (2 * Math.PI) * Math.asin(r / o);
            return o * Math.pow(2, -10 * t) * Math.sin((t * i - e) * 2 * Math.PI / s) + r + n
        }, easeInOutElastic: function(e, t, n, r, i) {
            e = 1.70158;
            var s = 0, o = r;
            if (0 == t)
                return n;
            if (2 == (t /= i / 2))
                return n + r;
            s || (s = i * .3 * 1.5);
            o < Math.abs(r) ? (o = r, e = s / 4) : e = s / (2 * Math.PI) * Math.asin(r / o);
            return 1 > t ? -.5 * o * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * i - e) * 2 * Math.PI / s) + n : .5 * o * Math.pow(2, -10 * (t -= 1)) * Math.sin((t * i - e) * 2 * Math.PI / s) + r + n
        }, easeInBack: function(e, t, n, r, i, s) {
            void 0 == s && (s = 1.70158);
            return r * (t /= i) * t * ((s + 1) * t - s) + n
        }, easeOutBack: function(e, t, n, r, i, s) {
            void 0 == s && (s = 1.70158);
            return r * ((t = t / i - 1) * t * ((s + 1) * t + s) + 1) + n
        }, easeInOutBack: function(e, t, n, r, i, s) {
            void 0 == s && (s = 1.70158);
            return 1 > (t /= i / 2) ? r / 2 * t * t * (((s *= 1.525) + 1) * t - s) + n : r / 2 * ((t -= 2) * t * (((s *= 1.525) + 1) * t + s) + 2) + n
        }, easeInBounce: function(e, t, n, r, i) {
            return r - jQuery.easing.easeOutBounce(e, i - t, 0, r, i) + n
        }, easeOutBounce: function(e, t, n, r, i) {
            return(t /= i) < 1 / 2.75 ? r * 7.5625 * t * t + n : t < 2 / 2.75 ? r * (7.5625 * (t -= 1.5 / 2.75) * t + .75) + n : t < 2.5 / 2.75 ? r * (7.5625 * (t -= 2.25 / 2.75) * t + .9375) + n : r * (7.5625 * (t -= 2.625 / 2.75) * t + .984375) + n
        }, easeInOutBounce: function(e, t, n, r, i) {
            return t < i / 2 ? .5 * jQuery.easing.easeInBounce(e, 2 * t, 0, r, i) + n : .5 * jQuery.easing.easeOutBounce(e, 2 * t - i, 0, r, i) + .5 * r + n
        }});
    var t = jQuery;
    t.fn.touchSwipe = function(e) {
        var n = {preventWebBrowser: !1, swipeLeft: null, swipeRight: null, swipeTop: null, swipeBottom: null};
        e && t.extend(n, e);
        return this.each(function() {
            function e(e) {
                var t = e.originalEvent;
                1 <= t.targetTouches.length ? (o = t.targetTouches[0].pageX, u = t.targetTouches[0].pageY) : s(e)
            }
            function r(e) {
                n.preventWebBrowser && e.preventDefault();
                var t = e.originalEvent;
                1 <= t.targetTouches.length ? (a = t.targetTouches[0].pageX, f = t.targetTouches[0].pageY) : s(e)
            }
            function i(e) {
                if (0 < a || 0 < f)
                    a > o ? n.swipeRight && n.swipeRight.call() : n.swipeLeft && n.swipeLeft.call(), f > u ? n.swipeBottom && n.swipeBottom.call() : n.swipeTop && n.swipeTop.call();
                s(e)
            }
            function s() {
                f = a = u = o = -1
            }
            var o = -1, u = -1, a = -1, f = -1;
            try {
                t(this).bind("touchstart", e), t(this).bind("touchmove", r), t(this).bind("touchend", i), t(this).bind("touchcancel", s)
            } catch (l) {
            }
        })
    };
    var n = jQuery;
    n.fn.drag = function(e, t, r) {
        var i = "string" == typeof e ? e : "", s = n.isFunction(e) ? e : n.isFunction(t) ? t : null;
        0 !== i.indexOf("drag") && (i = "drag" + i);
        r = (e == s ? t : r) || {};
        return s ? this.bind(i, r, s) : this.trigger(i)
    };
    var r = n.event, i = r.special, s = null, s = i.drag = {defaults: {which: 1, distance: 0, not: ":input", handle: null, relative: !1, drop: !0, click: !1}, datakey: "dragdata", livekey: "livedrag", add: function(e) {
            var t = n.data(this, s.datakey), i = e.data || {};
            t.related += 1;
            !t.live && e.selector && (t.live = !0, r.add(this, "draginit." + s.livekey, s.delegate));
            n.each(s.defaults, function(e) {
                void 0 !== i[e] && (t[e] = i[e])
            })
        }, remove: function() {
            n.data(this, s.datakey).related -= 1
        }, setup: function() {
            if (!n.data(this, s.datakey)) {
                var e = n.extend({related: 0}, s.defaults);
                n.data(this, s.datakey, e);
                r.add(this, "mousedown", s.init, e);
                this.attachEvent && this.attachEvent("ondragstart", s.dontstart)
            }
        }, teardown: function() {
            n.data(this, s.datakey).related || (n.removeData(this, s.datakey), r.remove(this, "mousedown", s.init), r.remove(this, "draginit", s.delegate), s.textselect(!0), this.detachEvent && this.detachEvent("ondragstart", s.dontstart))
        }, init: function(e) {
            var t = e.data, o;
            if (!(0 < t.which && e.which != t.which) && !n(e.target).is(t.not) && (!t.handle || n(e.target).closest(t.handle, e.currentTarget).length))
                if (t.propagates = 1, t.interactions = [s.interaction(this, t)], t.target = e.target, t.pageX = e.pageX, t.pageY = e.pageY, t.dragging = null, o = s.hijack(e, "draginit", t), t.propagates) {
                    if ((o = s.flatten(o)) && o.length)
                        t.interactions = [], n.each(o, function() {
                            t.interactions.push(s.interaction(this, t))
                        });
                    t.propagates = t.interactions.length;
                    !1 !== t.drop && i.drop && i.drop.handler(e, t);
                    s.textselect(!1);
                    r.add(document, "mousemove mouseup", s.handler, t);
                    return!1
                }
        }, interaction: function(e, t) {
            return{drag: e, callback: new s.callback, droppable: [], offset: n(e)[t.relative ? "position" : "offset"]() || {top: 0, left: 0}}
        }, handler: function(e) {
            var t = e.data;
            switch (e.type) {
                case!t.dragging && "mousemove":
                    if (Math.pow(e.pageX - t.pageX, 2) + Math.pow(e.pageY - t.pageY, 2) < Math.pow(t.distance, 2))
                        break;
                    e.target = t.target;
                    s.hijack(e, "dragstart", t);
                    t.propagates && (t.dragging = !0);
                case"mousemove":
                    if (t.dragging) {
                        s.hijack(e, "drag", t);
                        if (t.propagates) {
                            !1 !== t.drop && i.drop && i.drop.handler(e, t);
                            break
                        }
                        e.type = "mouseup"
                    }
                    ;
                case"mouseup":
                    r.remove(document, "mousemove mouseup", s.handler), t.dragging && (!1 !== t.drop && i.drop && i.drop.handler(e, t), s.hijack(e, "dragend", t)), s.textselect(!0), !1 === t.click && t.dragging && (jQuery.event.triggered = !0, setTimeout(function() {
                        jQuery.event.triggered = !1
                    }, 20), t.dragging = !1)
                }
        }, delegate: function(e) {
            var t = [], i, o = n.data(this, "events") || {};
            n.each(o.live || [], function(o, u) {
                if (0 === u.preType.indexOf("drag") && (i = n(e.target).closest(u.selector, e.currentTarget)[0]))
                    r.add(i, u.origType + "." + s.livekey, u.origHandler, u.data), 0 > n.inArray(i, t) && t.push(i)
            });
            return!t.length ? !1 : n(t).bind("dragend." + s.livekey, function() {
                r.remove(this, "." + s.livekey)
            })
        }, hijack: function(e, t, i, o, u) {
            if (i) {
                var a = e.originalEvent, f = e.type, c = t.indexOf("drop") ? "drag" : "drop", d, v = o || 0, m, g;
                o = !isNaN(o) ? o : i.interactions.length;
                e.type = t;
                e.originalEvent = null;
                i.results = [];
                do
                    if ((m = i.interactions[v]) && !("dragend" !== t && m.cancelled))
                        g = s.properties(e, i, m), m.results = [], n(u || m[c] || i.droppable).each(function(o, u) {
                            d = (g.target = u) ? r.handle.call(u, e, g) : null;
                            !1 === d ? ("drag" == c && (m.cancelled = !0, i.propagates -= 1), "drop" == t && (m[c][o] = null)) : "dropinit" == t && m.droppable.push(s.element(d) || u);
                            "dragstart" == t && (m.proxy = n(s.element(d) || m.drag)[0]);
                            m.results.push(d);
                            delete e.result;
                            if ("dropinit" !== t)
                                return d
                        }), i.results[v] = s.flatten(m.results), "dropinit" == t && (m.droppable = s.flatten(m.droppable)), "dragstart" == t && !m.cancelled && g.update();
                while (++v < o);
                e.type = f;
                e.originalEvent = a;
                return s.flatten(i.results)
            }
        }, properties: function(e, t, n) {
            var r = n.callback;
            r.drag = n.drag;
            r.proxy = n.proxy || n.drag;
            r.startX = t.pageX;
            r.startY = t.pageY;
            r.deltaX = e.pageX - t.pageX;
            r.deltaY = e.pageY - t.pageY;
            r.originalX = n.offset.left;
            r.originalY = n.offset.top;
            r.offsetX = e.pageX - (t.pageX - r.originalX);
            r.offsetY = e.pageY - (t.pageY - r.originalY);
            r.drop = s.flatten((n.drop || []).slice());
            r.available = s.flatten((n.droppable || []).slice());
            return r
        }, element: function(e) {
            if (e && (e.jquery || 1 == e.nodeType))
                return e
        }, flatten: function(e) {
            return n.map(e, function(e) {
                return e && e.jquery ? n.makeArray(e) : e && e.length ? s.flatten(e) : e
            })
        }, textselect: function(e) {
            n(document)[e ? "unbind" : "bind"]("selectstart", s.dontstart).attr("unselectable", e ? "off" : "on").css("MozUserSelect", e ? "" : "none")
        }, dontstart: function() {
            return!1
        }, callback: function() {
        }};
    s.callback.prototype = {update: function() {
            i.drop && this.available.length && n.each(this.available, function(e) {
                i.drop.locate(this, e)
            })
        }};
    i.draginit = i.dragstart = i.dragend = s;
    var o = jQuery;
    o.fn.html5boxTransition = function(e, t, n, r, i) {
        $parent = this;
        e = r.effect;
        var s = r.easing, u = r.duration, a = r.direction, f = null;
        e && (e = e.split(","), f = e[Math.floor(Math.random() * e.length)], f = o.trim(f.toLowerCase()));
        f && r[f] && (r[f].duration && (u = r[f].duration), r[f].easing && (s = r[f].easing));
        "fade" == f ? (n.show(), t.fadeOut(u, s, function() {
            t.remove();
            i()
        })) : "crossfade" == f || "fadeout" == f ? (n.hide(), t.fadeOut(u / 2, s, function() {
            n.fadeIn(u / 2, s, function() {
                t.remove();
                i()
            })
        })) : "slide" == f ? ($parent.css({overflow: "hidden"}), a ? (n.css({left: "100%"}), n.animate({left: "0%"}, u, s), t.animate({left: "-100%"}, u, s, function() {
            t.remove();
            i()
        })) : (n.css({left: "-100%"}), n.animate({left: "0%"}, u, s), t.animate({left: "100%"}, u, s, function() {
            t.remove();
            i()
        }))) : (n.show(), t.remove(), i())
    };
    var u = jQuery, a = 0;
    u.fn.html5gallery = function(t) {
        
        var n = function(e, t, n) {
            this.container = e;
            this.options = t;
            this.id = n;
            //this.options.googlefonts && 0 < this.options.googlefonts.length && (e = ("https:" == document.location.protocol ? "https" : "http") + "://fonts.googleapis.com/css?family=" + this.options.googlefonts, t = document.createElement("link"), t.setAttribute("rel", "stylesheet"), t.setAttribute("type", "text/css"), t.setAttribute("href", e), document.getElementsByTagName("head")[0].appendChild(t));
            this.options.flashInstalled = !1;
            try {
                new ActiveXObject("ShockwaveFlash.ShockwaveFlash") && (this.options.flashInstalled = !0)
            } catch (r) {
                navigator.mimeTypes["application/x-shockwave-flash"] && (this.options.flashInstalled = !0)
            }
            this.options.html5VideoSupported = !!document.createElement("video").canPlayType;
            this.options.isChrome = null != navigator.userAgent.match(/Chrome/i);
            this.options.isFirefox = null != navigator.userAgent.match(/Firefox/i);
            this.options.isOpera = null != navigator.userAgent.match(/Opera/i) || null != navigator.userAgent.match(/OPR\//i);
            this.options.isSafari = null != navigator.userAgent.match(/Safari/i);
            this.options.isIE = null != navigator.userAgent.match(/MSIE/i) && !this.options.isOpera;
            this.options.isIE9 = this.options.isIE && this.options.html5VideoSupported;
            this.options.isIE678 = this.options.isIE && !this.options.html5VideoSupported;
            this.options.isIE7 = null != navigator.userAgent.match(/MSIE 7/i) && !this.options.isOpera;
            this.options.isIE6 = null != navigator.userAgent.match(/MSIE 6/i) && !this.options.isOpera;
            this.options.isAndroid = null != navigator.userAgent.match(/Android/i);
            this.options.isIPad = null != navigator.userAgent.match(/iPad/i);
            this.options.isIPhone = null != navigator.userAgent.match(/iPod/i) || null != navigator.userAgent.match(/iPhone/i);
            this.options.isIOS = this.options.isIPad || this.options.isIPhone;
            this.options.isMobile = this.options.isAndroid || this.options.isIPad || this.options.isIPhone;
            this.slideTimer = this.slideshowTimeout = null;
            this.looptimes = this.slideTimerCount = 0;
            this.elemArray = [];
            this.container.children().hide();
            this.container.css({display: "block", position: "relative"});
            this.initData(this.init)
        };
        n.prototype = {getParams: function() {
                for (var e = {}, t = window.location.search.substring(1).split("&"), n = 0; n < t.length; n++) {
                    var r = t[n].split("=");
                    r && 2 == r.length && (e[r[0].toLowerCase()] = unescape(r[1]))
                }
                return e
            }, init: function(e) {
                if (e.options.random)
                    for (var t = e.elemArray.length - 1; 0 < t; t--) {
                        var n = Math.floor(Math.random() * t), r = e.elemArray[t];
                        e.elemArray[t] = e.elemArray[n];
                        e.elemArray[n] = r
                    }
                e.initYoutubeApi();
                e.options.showcarousel = 1 < e.elemArray.length && e.options.showcarousel;
                e.options.watermarkcode = e.options.freeversion ? "<a style='text-decoration:none;' target='_blank' href='" + e.options.freelink + "' >" : "";
                e.options.watermarkcode += "<div class='html5gallery-watermark-" + e.id + "'" + (e.options.freeversion ? " style='line-height:20px;'" : "") + ">";
                e.options.freeversion ? e.options.watermarkcode += e.options.freemark : 0 < e.options.watermark.length && (e.options.watermarkcode += "<img src='" + e.options.watermark + "' />");
                e.options.watermarkcode += "</div>";
                e.options.watermarkcode += e.options.freeversion ? "</a>" : "";
                e.createStyle();
                e.createMarkup();
                e.createImageToolbox();
                0 >= e.elemArray.length || (e.createEvents(), e.loadCarousel(), e.savedElem = -1, e.curElem = -1, e.nextElem = -1, e.prevElem = -1, e.isPaused = !e.options.autoslide, e.isFullscreen = !1, t = e.getParams(), e.slideRun(t.html5galleryid && t.html5galleryid in e.elemArray ? t.html5galleryid : 0), e.options.responsive && (e.resizeGallery(), u(window).resize(function() {
                    e.resizeGallery()
                })))
            }, resizeGallery: function() {
                switch (this.options.skin) {
                    case"vertical":
                        this.resizeStyleVertical();
                        break;
                    default:
                        this.resizeStyleDefault()
                }
                this.resizeImageToolbox();
                this.isFullscreen && this.resizeFullscreen()
            }, initData: function(e) {
                if (this.options.src && 0 < this.options.src.length) {
                    var t = this.options.mediatype ? this.options.mediatype : this.checkType(this.options.src);
                    this.elemArray.push([0, "", this.options.src, this.options.webm, this.options.ogg, "", "", this.options.title ? this.options.title : "", this.options.title ? this.options.title : "", t, this.options.width, this.options.height]);
                    this.readTags();
                    e(this)
                } else if (this.options.xml && 0 < this.options.xml.length) {
                    this.options.xmlnocache && (this.options.xml += 0 > this.options.xml.indexOf("?") ? "?" : "&", this.options.xml += Math.random());
                    var n = this;
                    u.ajax({type: "GET",async: false, url: this.options.xml, dataType: "xml", success: function(t) {
                            u(t).find("slide").each(function(e) {
                                var t = u(this).find("title").text(), r = u(this).find("description").text() ? u(this).find("description").text() : u(this).find("information").text();
                                t || (t = "");
                                r || (r = "");
                                var i = u(this).find("mediatype").text() ? u(this).find("mediatype").text() : n.checkType(u(this).find("file").text());
                                n.elemArray.push([u(this).find("id").length ? u(this).find("id").text() : e, u(this).find("thumbnail").text(), u(this).find("file").text(), u(this).find("file-ogg").text(), u(this).find("file-webm").text(), u(this).find("link").text(), u(this).find("linktarget").text(), t, r, i, u(this).find("width").length && !isNaN(parseInt(u(this).find("width").text())) ? parseInt(u(this).find("width").text()) : n.options.width, u(this).find("height").length && !isNaN(parseInt(u(this).find("height").text())) ? parseInt(u(this).find("height").text()) : n.options.height, u(this).find("poster").text()])
                            });
                            n.readTags();
                            e(n)
                        }})
                } else
                    this.options.remote && 0 < this.options.remote.length ? (n = this, u.getJSON(this.options.remote, function(t) {
                        for (var r = 0; r < t.length; r++) {
                            var i = t[r].mediatype ? t[r].mediatype : n.checkType(t[r].file);
                            n.elemArray.push([r, t[r].thumbnail, t[r].file, t[r].fileogg, t[r].filewebm, t[r].link, t[r].linktarget, t[r].title, t[r].description, i, t[r].width && !isNaN(parseInt(t[r].width)) ? parseInt(t[r].width) : n.options.width, t[r].height && !isNaN(parseInt(t[r].height)) ? parseInt(t[r].height) : n.options.height, t[r].poster])
                        }
                        n.readTags();
                        e(n)
                    })) : (this.readTags(), e(this))
            }, readTags: function() {
                var e = this;
                u("img", this.container).each(function() {
                    var t = u(this).attr("src"), n = u(this).attr("alt"), r = u(this).data("description") ? u(this).data("description") : u(this).data("information");
                    n || (n = "");
                    r || (r = "");
                    var i = e.options.width, s = e.options.height, o = null, a = null, f = null, l = null, h = null;
                    u(this).parent().is("a") && (t = u(this).parent().attr("href"), o = u(this).parent().data("ogg"), a = u(this).parent().data("webm"), f = u(this).parent().data("link"), l = u(this).parent().data("linktarget"), h = u(this).parent().data("poster"), isNaN(u(this).parent().data("width")) || (i = u(this).parent().data("width")), isNaN(u(this).parent().data("height")) || (s = u(this).parent().data("height")));
                    var p = u(this).parent().data("mediatype") ? u(this).parent().data("mediatype") : e.checkType(t);
                    e.elemArray.push([e.elemArray.length, u(this).attr("src"), t, o, a, f, l, n, r, p, i, s, h])
                })
            }, createMarkup: function() {
                this.$gallery = jQuery("<div class='html5gallery-container-" + this.id + "'><div class='html5gallery-box-" + this.id + "'><div class='html5gallery-elem-" + this.id + "'></div><div class='html5gallery-title-" + this.id + "'></div><div class='html5gallery-timer-" + this.id + "'></div><div class='html5gallery-viral-" + this.id + "'></div><div class='html5gallery-toolbox-" + this.id + "'><div class='html5gallery-toolbox-bg-" + this.id + "'></div><div class='html5gallery-toolbox-buttons-" + this.id + "'><div class='html5gallery-play-" + this.id + "'></div><div class='html5gallery-pause-" + this.id + "'></div><div class='html5gallery-left-" + this.id + "'></div><div class='html5gallery-right-" + this.id + "'></div><div class='html5gallery-lightbox-" + this.id + "'></div></div></div></div><div class='html5gallery-car-" + this.id + "'><div class='html5gallery-car-list-" + this.id + "'><div class='html5gallery-car-mask-" + this.id + "'><div class='html5gallery-thumbs-" + this.id + "'></div></div><div class='html5gallery-car-slider-bar-" + this.id + "'><div class='html5gallery-car-slider-bar-top-" + this.id + "'></div><div class='html5gallery-car-slider-bar-middle-" + this.id + "'></div><div class='html5gallery-car-slider-bar-bottom-" + this.id + "'></div></div><div class='html5gallery-car-left-" + this.id + "'></div><div class='html5gallery-car-right-" + this.id + "'></div><div class='html5gallery-car-slider-" + this.id + "'></div></div></div></div>");
                this.$gallery.appendTo(this.container);
                this.options.socialurlforeach || this.createSocialMedia();
                this.options.googleanalyticsaccount && !window._gaq && (window._gaq = window._gaq || [], window._gaq.push(["_setAccount", this.options.googleanalyticsaccount]), window._gaq.push(["_trackPageview"]), u.getScript(("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js"))
            }, createSocialMedia: function() {
                u(".html5gallery-viral-" + this.id, this.$gallery).empty();
                if (typeof this.elemArray[this.curElem] === "undefined")
                {
                    
                }
                else
                {   
                    var image_url= this.elemArray[this.curElem][1];
                    var a_image_url = image_url.split("/");

                    var main_image_url = a_image_url.join("-d-");
                    
                    var e = u("#base_url").val()+"socialPage/"+main_image_url;  
                   
                    //this.options.socialurlforeach && (e += (0 > window.location.href.indexOf("?") ? "?" : "&") + "html5galleryid=" + this.elemArray[this.curElem][0]);
                    if (this.options.showsocialmedia && this.options.showfacebooklike) {
                        var t = "<div style='display:block; float:left; width:110px; height:21px;'><iframe src='http://www.facebook.com/plugins/like.php?href=", t = this.options.facebooklikeurl && 0 < this.options.facebooklikeurl.length ? t + encodeURIComponent(this.options.facebooklikeurl) : t + e;
                        u(".html5gallery-viral-" + this.id, this.$gallery).append(t + "&send=false&layout=button_count&width=450&show_faces=false&action=like&colorscheme=light&font&height=21' scrolling='no' frameborder='0' style='border:none;;overflow:hidden; width:110px; height:21px;' allowTransparency='true'></iframe></div>")
                    }
                    this.options.showsocialmedia && this.options.showtwitter && (t = "<div style='display:block; float:left; width:110px; height:21px;'><a href='https://twitter.com/share' class='twitter-share-button'", t = this.options.twitterurl && 0 < this.options.twitterurl.length ? t + (" data-url='" + this.options.twitterurl + "'") : t + (" data-url='" + e + "'"), this.options.twitterusername && 0 < this.options.twitterusername.length && (t += " data-via='" + this.options.twittervia + "' data-related='" + this.options.twitterusername + "'"), u(".html5gallery-viral-" + this.id, this.$gallery).append(t + ">Tweet</a></div>"), u.getScript("http://platform.twitter.com/widgets.js"));
                    this.options.showsocialmedia && this.options.showgoogleplus && (t = "<div style='display:block; float:left; width:100px; height:21px;'><div class='g-plusone' data-size='medium'", t = this.options.googleplusurl && 0 < this.options.googleplusurl.length ? t + (" data-href='" + this.options.googleplusurl + "'") : t + (" data-href='" + e + "'"), u(".html5gallery-viral-" + this.id, this.$gallery).append(t + "></div></div>"), u.getScript("https://apis.google.com/js/plusone.js"))
                }
            }, createEvents: function() {
                var e = this;
                u(".html5gallery-play-" + this.id, this.$gallery).click(function(t) {
                    t.stopPropagation();
                    u(".html5gallery-play-" + e.id, e.$gallery).hide();
                    u(".html5gallery-pause-" + e.id, e.$gallery).show();
                    e.isPaused = !1;
                    e.slideshowTimeout = setTimeout(function() {
                        e.slideRun(-1)
                    }, e.options.slideshowinterval);
                    u(".html5gallery-timer-" + e.id, e.$gallery).css({width: 0});
                    e.slideTimerCount = 0;
                    e.options.showtimer && (e.slideTimer = setInterval(function() {
                        e.showSlideTimer()
                    }, 50))
                });
                u(".html5gallery-pause-" + this.id, this.$gallery).click(function(t) {
                    t.stopPropagation();
                    u(".html5gallery-play-" + e.id, e.$gallery).show();
                    u(".html5gallery-pause-" + e.id, e.$gallery).hide();
                    e.isPaused = !0;
                    clearTimeout(e.slideshowTimeout);
                    u(".html5gallery-timer-" + e.id, e.$gallery).css({width: 0});
                    clearInterval(e.slideTimer);
                    e.slideTimerCount = 0
                });
                u(".html5gallery-lightbox-" + this.id, this.$gallery).click(function(t) {
                    t.stopPropagation();
                    e.goFullscreen()
                });
                var obj_this = this;
                u(".html5gallery-title-" + this.id).hide();
                u(".html5gallery-left-" + this.id, this.$gallery).click(function(t) {
                    t.stopPropagation();
                    e.slideRun(-2, !0);
                    if (u(".html5gallery-title-text-" + obj_this.id, obj_this.$gallery).html()) 
                    {
                       u(".html5gallery-title-" + obj_this.id).css("height","auto");
                       u(".html5gallery-title-" + obj_this.id).fadeIn(); 
                    }
                    else
                    {
                       u(".html5gallery-title-" + obj_this.id).fadeOut();
                    }
                });
                
                u(".html5gallery-right-" + this.id, this.$gallery).click(function(t) {
                    t.stopPropagation();
                    e.slideRun(-1, !0);
                    if (u(".html5gallery-title-text-" + obj_this.id, obj_this.$gallery).html()) 
                    {
                       u(".html5gallery-title-" + obj_this.id).css("height","auto");
                       u(".html5gallery-title-" + obj_this.id).fadeIn(); 
                    }
                    else
                    {
                       u(".html5gallery-title-" + obj_this.id).fadeOut();
                    }
                });
                e.options.enabletouchswipe && u(".html5gallery-box-" + this.id, this.$gallery).touchSwipe({preventWebBrowser: !0, swipeLeft: function() {
                        e.slideRun(-1, !0)
                    }, swipeRight: function() {
                        e.slideRun(-2, !0)
                    }});
                
                u(".html5gallery-box-" + this.id, this.$gallery).hover(function() {
                    if (u(".html5gallery-title-text-" + obj_this.id, obj_this.$gallery).html()) 
                    {
                       u(".html5gallery-title-" + obj_this.id).css("height","auto");
                       u(".html5gallery-title-" + obj_this.id).fadeIn(); 
                    }
                    else
                    {
                       u(".html5gallery-title-" + obj_this.id).fadeOut();
                    }    
                    e.onSlideshowOver();
                    var t = e.elemArray[e.curElem][9];
                    ("always" == e.options.showimagetoolbox || "image" == e.options.showimagetoolbox && 1 == t) && e.showimagetoolbox(t)
                }, function() {
                  
                    u(".html5gallery-title-" + obj_this.id).fadeOut(); 
                    e.hideimagetoolbox()
                });
                u(".html5gallery-container-" + this.id + " .html5gallery-box-" + this.id).click(function(t) {
                    t.stopPropagation();
                    e.goFullscreen()
                });
//                u(".html5gallery-container-" + this.id).hover(function(e) {
//                    if (u(".html5gallery-title-text-" + e.id, e.$gallery).html()) {
//                        e.options.titleoverlay && e.options.titleautohide && u(".html5gallery-title-" + e.id, e.$gallery).fadeIn()
//                    } else {
//                        e.options.titleoverlay && e.options.titleautohide && u(".html5gallery-title-" + e.id, e.$gallery).fadeOut()
//                    }
//                }, function() {
//                    e.options.titleoverlay && e.options.titleautohide && u(".html5gallery-title-" + e.id, e.$gallery).fadeOut()
//                });
                u(".html5gallery-car-left-" + this.id, this.$gallery).css({"background-position": "-64px 0px", cursor: ""});
                u(".html5gallery-car-left-" + this.id, this.$gallery).data("disabled", !0);
                u(".html5gallery-car-right-" + this.id, this.$gallery).css({"background-position": "0px 0px"});
                u(".html5gallery-car-left-" + this.id, this.$gallery).click(function() {
                    u(this).data("disabled") || e.carouselPrev()
                });
                u(".html5gallery-car-right-" + this.id, this.$gallery).click(function() {
                    u(this).data("disabled") || e.carouselNext()
                });
                u(".html5gallery-car-slider-" + this.id, this.$gallery).bind("drag", function(t, n) {
                    e.carouselSliderDrag(t, n)
                });
                u(".html5gallery-car-slider-bar-" + this.id, this.$gallery).click(function(t) {
                    e.carouselBarClicked(t)
                });
                u(".html5gallery-car-left-" + this.id, this.$gallery).hover(function() {
                    u(this).data("disabled") || u(this).css({"background-position": "-32px 0px"})
                }, function() {
                    u(this).data("disabled") || u(this).css({"background-position": "0px 0px"})
                });
                u(".html5gallery-car-right-" + this.id, this.$gallery).hover(function() {
                    u(this).data("disabled") || u(this).css({"background-position": "-32px 0px"})
                }, function() {
                    u(this).data("disabled") || u(this).css({"background-position": "0px 0px"})
                })
            }, createStyle: function() {
                switch (this.options.skin) {
                    case"vertical":
                        this.createStyleVertical();
                        break;
                    default:
                        this.createStyleDefault()
                    }
            }, resizeStyleVertical: function() {
                if (this.container.parent() && this.container.parent().width()) {
                    this.options.containerWidth = this.container.parent().width();
                    this.options.totalWidth = this.options.containerWidth;
                    this.options.width = this.options.totalWidth - (this.options.carouselWidth + this.options.carouselmargin + 2 * this.options.padding);
                    this.options.responsivefullscreen && 0 < this.container.parent().height() ? (this.options.containerHeight = this.container.parent().height(), this.options.totalHeight = this.options.containerHeight, this.options.height = this.options.totalHeight - (this.options.headerHeight + 2 * this.options.padding)) : (this.options.height = Math.round(this.options.width * this.options.originalHeight / this.options.originalWidth), this.options.totalHeight = this.options.containerHeight, this.options.containerHeight = this.options.totalHeight);
                    this.container.css({width: this.options.containerWidth, height: this.options.containerHeight});
                    this.options.boxWidth = this.options.width;
                    this.options.boxHeight = this.options.height + this.options.headerHeight;
                    this.options.showcarousel && (this.options.carouselWidth = this.options.thumbwidth, this.options.carouselHeight = this.options.height + this.options.headerHeight, this.options.carTop = 0, this.options.carBottom = 0, this.options.carAreaLength = this.options.carouselHeight - this.options.carTop - this.options.carBottom, this.options.carouselSlider = Math.floor(this.options.carAreaLength / (this.options.thumbheight + this.options.thumbgap)) < this.elemArray.length, this.options.carouselSlider && (this.options.carouselWidth += 20), "left" == this.options.carouselposition ? (this.options.boxLeft = this.options.padding + this.options.carouselWidth + this.options.carouselmargin, this.options.carouselLeft = this.options.padding) : this.options.carouselLeft = this.options.padding + this.options.width + this.options.carouselmargin, this.options.carouselTop = this.options.padding);
                    u(".html5gallery-container-" + this.id).css({width: this.options.totalWidth + "px", height: this.options.totalHeight + "px"});
                    u(".html5gallery-box-" + this.id).css({width: this.options.boxWidth + "px", height: this.options.boxHeight + "px"});
                    var e = this.elemArray[this.curElem][9];
                    if (1 == e) {
                        var e = this.elemArray[this.curElem][10], t = this.elemArray[this.curElem][11], n;
                        this.isFullscreen ? (n = Math.min(this.fullscreenWidth / e, this.fullscreenHeight / t), n = 1 < n ? 1 : n) : n = "fill" == this.options.resizemode ? Math.max(this.options.width / e, this.options.height / t) : Math.min(this.options.width / e, this.options.height / t);
                        e = Math.round(n * e);
                        n = Math.round(n * t);
                        var t = this.isFullscreen ? e : this.options.width, r = this.isFullscreen ? n : this.options.height, i = Math.round(t / 2 - e / 2), s = Math.round(r / 2 - n / 2);
                        this.isFullscreen && this.adjustFullscreen(t, r, !0);
                        u(".html5gallery-elem-" + this.id).css({width: t + "px", height: r + "px"});
                        u(".html5gallery-elem-img-" + this.id).css({width: t + "px", height: r + "px"});
                        u(".html5gallery-elem-image-" + this.id).css({width: e + "px", height: n + "px", top: s + "px", left: i + "px"})
                    } else if (5 == e || 6 == e || 7 == e || 8 == e || 9 == e || 10 == e)
                        t = this.elemArray[this.curElem][10], e = this.elemArray[this.curElem][11], this.isFullscreen ? (n = Math.min(this.fullscreenWidth / t, this.fullscreenHeight / e), n = 1 < n ? 1 : n, t = Math.round(n * t), r = Math.round(n * e), this.adjustFullscreen(t, r, !0)) : (t = this.options.width, r = this.options.height), u(".html5gallery-elem-" + this.id).css({width: t + "px", height: r + "px"}), u(".html5gallery-elem-video-" + this.id).css({width: t + "px", height: r + "px"}), u("#html5gallery-elem-video-" + this.id).css({width: t + "px", height: r + "px"});
                    t = e = 0;
                    "bottom" == this.options.headerpos && (e = this.options.titleoverlay ? this.options.height - this.options.titleheight : this.options.height, t = this.options.titleoverlay ? this.options.height : this.options.height + this.options.titleheight);
                    u(".html5gallery-title-" + this.id).css({width: this.options.boxWidth + "px"});
                    this.options.titleoverlay || u(".html5gallery-title-" + this.id).css({top: e + "px"});
                    u(".html5gallery-viral-" + this.id).css({top: t + "px"});
                    u(".html5gallery-timer-" + this.id).css({top: String(this.options.elemTop + this.options.height - 2) + "px"});
                    this.options.showcarousel && (u(".html5gallery-car-" + this.id).css({width: this.options.carouselWidth + "px", height: this.options.carouselHeight + "px", top: this.options.carouselTop + "px", left: this.options.carouselLeft + "px", top:this.options.carouselTop + "px"}), u(".html5gallery-car-list-" + this.id).css({top: this.options.carTop + "px", height: String(this.options.carAreaLength) + "px", width: this.options.carouselWidth + "px"}), this.options.thumbShowNum = Math.floor(this.options.carAreaLength / (this.options.thumbheight + this.options.thumbgap)), this.options.thumbMaskHeight = this.options.thumbShowNum * this.options.thumbheight + (this.options.thumbShowNum - 1) * this.options.thumbgap, this.options.thumbTotalHeight = this.elemArray.length * this.options.thumbheight + (this.elemArray.length - 1) * this.options.thumbgap, this.options.carouselSlider && (this.options.carouselSliderMin = 0, this.options.carouselSliderMax = this.options.thumbMaskHeight - 54, u(".html5gallery-car-slider-bar-" + this.id).css({height: this.options.thumbMaskHeight + "px"}), u(".html5gallery-car-slider-bar-middle-" + this.id).css({height: String(this.options.thumbMaskHeight - 32) + "px"}), this.options.isMobile && u(".html5gallery-car-right-" + this.id).css({top: String(this.options.thumbMaskHeight - 35) + "px"}), u(".html5gallery-car-slider-bar-" + this.id).css({display: "block"}), u(".html5gallery-car-left-" + this.id).css({display: "block"}), u(".html5gallery-car-right-" + this.id).css({display: "block"}), u(".html5gallery-car-slider-" + this.id).css({display: "block"})), e = 0, this.options.carouselNavButton && (e = Math.round(this.options.carAreaLength / 2 - this.options.thumbMaskHeight / 2)), u(".html5gallery-car-mask-" + this.id).css({top: e + "px", height: this.options.thumbMaskHeight + "px"}), this.carouselHighlight(this.curElem))
                }
            }, createStyleVertical: function() {
                this.options.thumbimagewidth = this.options.thumbheight - 2 * this.options.thumbimageborder - 4;
                this.options.thumbimageheight = this.options.thumbheight - 2 * this.options.thumbimageborder - 4;
                this.options.showtitle || (this.options.titleheight = 0);
                if (!this.options.showsocialmedia || !this.options.showfacebooklike && !this.options.showtwitter && !this.options.showgoogleplus)
                    this.options.socialheight = 0;
                this.options.headerHeight = this.options.titleoverlay ? this.options.socialheight : this.options.titleheight + this.options.socialheight;
                this.options.boxWidth = this.options.width;
                this.options.boxHeight = this.options.height + this.options.headerHeight;
                this.options.boxLeft = this.options.padding;
                this.options.boxTop = this.options.padding;
                this.options.showcarousel ? (this.options.carouselWidth = this.options.thumbwidth, this.options.carouselHeight = this.options.height + this.options.headerHeight, this.options.carTop = 0, this.options.carBottom = 0, this.options.carAreaLength = this.options.carouselHeight - this.options.carTop - this.options.carBottom, this.options.carouselSlider = Math.floor(this.options.carAreaLength / (this.options.thumbheight + this.options.thumbgap)) < this.elemArray.length, this.options.carouselSlider && (this.options.carouselWidth += 20), "left" == this.options.carouselposition ? (this.options.boxLeft = this.options.padding + this.options.carouselWidth + this.options.carouselmargin, this.options.carouselLeft = this.options.padding) : this.options.carouselLeft = this.options.padding + this.options.width + this.options.carouselmargin, this.options.carouselTop = this.options.padding) : (this.options.carouselWidth = 0, this.options.carouselHeight = 0, this.options.carouselLeft = 0, this.options.carouselTop = 0, this.options.carouselmargin = 0);
                this.options.totalWidth = this.options.width + this.options.carouselWidth + this.options.carouselmargin + 2 * this.options.padding;
                this.options.totalHeight = this.options.height + this.options.headerHeight + 2 * this.options.padding;
                this.options.containerWidth = this.options.totalWidth;
                this.options.containerHeight = this.options.totalHeight;
                this.options.responsive ? (this.options.originalWidth = this.options.width, this.options.originalHeight = this.options.height, this.container.css({"max-width": "100%"})) : this.container.css({width: this.options.containerWidth, height: this.options.containerHeight});
                var e = 0, t = 0;
                this.options.elemTop = 0;
                "top" == this.options.headerpos ? (t = 0, e = this.options.socialheight, this.options.elemTop = this.options.headerHeight) : "bottom" == this.options.headerpos && (this.options.elemTop = 0, e = this.options.titleoverlay ? this.options.height - this.options.titleheight : this.options.height, t = this.options.titleoverlay ? this.options.height : this.options.height + this.options.titleheight);
                var n = " .html5gallery-container-" + this.id + " { display:block; position:absolute; left:0px; top:0px; width:" + this.options.totalWidth + "px; height:" + this.options.totalHeight + "px; background-color:" + this.options.bgcolor + ";}";
                this.options.galleryshadow && (n += " .html5gallery-container-" + this.id + " { -moz-box-shadow: 0px 2px 5px #aaa; -webkit-box-shadow: 0px 2px 5px #aaa; box-shadow: 0px 2px 5px #aaa;}");
                var n = n + (" .html5gallery-box-" + this.id + " {display:block; position:absolute; text-align:center; left:" + this.options.boxLeft + "px; top:" + this.options.boxTop + "px; width:" + this.options.boxWidth + "px; height:" + this.options.boxHeight + "px; }"), r = Math.round(this.options.socialheight / 2 - 12), n = n + (" .html5gallery-title-text-" + this.id + " " + this.options.titlecss + " .html5gallery-title-text-" + this.id + " " + this.options.titlecsslink + " .html5gallery-error-" + this.id + " " + this.options.errorcss), n = n + (" .html5gallery-description-text-" + this.id + " " + this.options.descriptioncss + " .html5gallery-description-text-" + this.id + " " + this.options.descriptioncsslink), n = n + (" .html5gallery-fullscreen-title-" + this.id + "" + this.options.titlefullscreencss + " .html5gallery-fullscreen-title-" + this.id + "" + this.options.titlefullscreencsslink), n = n + (" .html5gallery-viral-" + this.id + " {display:block; overflow:hidden; position:absolute; text-align:left; top:" + t + "px; left:0px; width:" + this.options.boxWidth + "px; height:" + this.options.socialheight + "px; padding-top:" + r + "px;}"), n = n + (" .html5gallery-title-" + this.id + " {display:" + (this.options.titleoverlay && this.options.titleautohide ? "none" : "block") + "; overflow:hidden; position:absolute; left:0px; width:" + this.options.boxWidth + "px; "), n = this.options.titleoverlay ? "top" == this.options.headerpos ? n + "top:0px; height:auto; }" : n + "bottom:0px; height:auto; }" : n + ("top:" + e + "px; height:" + this.options.titleheight + "px; }"), n = n + (" .html5gallery-timer-" + this.id + " {display:block; position:absolute; top:" + String(this.options.elemTop + this.options.height - 2) + "px; left:0px; width:0px; height:2px; background-color:#ccc; filter:alpha(opacity=60); opacity:0.6; }"), n = n + (" .html5gallery-elem-" + this.id + " {display:block; overflow:hidden; position:absolute; top:" + this.options.elemTop + "px; left:0px; width:" + this.options.boxWidth + "px; height:" + this.options.height + "px;}");
                this.options.isIE7 || this.options.isIE6 ? (n += " .html5gallery-loading-" + this.id + " {display:none; }", n += " .html5gallery-loading-center-" + this.id + " {display:none; }") : (n += " .html5gallery-loading-" + this.id + " {display:block; position:absolute; top:4px; right:4px; width:100%; height:100%; background:url('" + this.options.skinfolder + "loading.gif') no-repeat top right;}", n += " .html5gallery-loading-center-" + this.id + " {display:block; position:absolute; top:0px; left:0px; width:100%; height:100%; background:url('" + this.options.skinfolder + "loading_center.gif') no-repeat center center;}");
                0 < this.options.borderradius && (n += " .html5gallery-elem-" + this.id + " { overflow:hidden; border-radius:" + this.options.borderradius + "px; -moz-border-radius:" + this.options.borderradius + "px; -webkit-border-radius:" + this.options.borderradius + "px;}");
                this.options.slideshadow && (n += " .html5gallery-title-" + this.id + " { padding:4px;}", n += " .html5gallery-timer-" + this.id + " { margin:4px;}", n += " .html5gallery-elem-" + this.id + " { overflow:hidden; padding:4px; -moz-box-shadow: 0px 2px 5px #aaa; -webkit-box-shadow: 0px 2px 5px #aaa; box-shadow: 0px 2px 5px #aaa;}");
                this.options.showcarousel ? (n += " .html5gallery-car-" + this.id + " { position:absolute; display:block; overflow:hidden; width:" + this.options.carouselWidth + "px; height:" + this.options.carouselHeight + "px; left:" + this.options.carouselLeft + "px; top:" + this.options.carouselTop + "px; }", n += " .html5gallery-car-list-" + this.id + " { position:absolute; display:block; overflow:hidden; top:" + this.options.carTop + "px; height:" + String(this.options.carAreaLength) + "px; left:0px; width:" + this.options.carouselWidth + "px; }", n += ".html5gallery-thumbs-" + this.id + " {margin-top:0px; height:" + String(this.elemArray.length * (this.options.thumbheight + this.options.thumbgap)) + "px;}", this.options.thumbShowNum = Math.floor(this.options.carAreaLength / (this.options.thumbheight + this.options.thumbgap)), this.options.thumbMaskHeight = this.options.thumbShowNum * this.options.thumbheight + (this.options.thumbShowNum - 1) * this.options.thumbgap, this.options.thumbTotalHeight = this.elemArray.length * this.options.thumbheight + (this.elemArray.length - 1) * this.options.thumbgap, this.options.carouselSliderMin = 0, this.options.carouselSliderMax = this.options.thumbMaskHeight - 54, n += " .html5gallery-car-slider-bar-" + this.id + " { position:absolute; display:" + (this.options.carouselSlider ? "block" : "none") + "; overflow:hidden; top:0px; height:" + this.options.thumbMaskHeight + "px; left:" + String(this.options.thumbwidth + 6) + "px; width:14px;}", n += " .html5gallery-car-slider-bar-top-" + this.id + " { position:absolute; display:block; top:0px; left:0px; width:14px; height:16px; background:url('" + this.options.skinfolder + "bartop.png')}", n += " .html5gallery-car-slider-bar-middle-" + this.id + " { position:absolute; display:block; top:16px; left:0px; width:14px; height:" + String(this.options.thumbMaskHeight - 32) + "px; background:url('" + this.options.skinfolder + "bar.png')}", n += " .html5gallery-car-slider-bar-bottom-" + this.id + " { position:absolute; display:block; bottom:0px; left:0px; width:14px; height:16px; background:url('" + this.options.skinfolder + "barbottom.png')}", n = this.options.isMobile ? n + (" .html5gallery-car-left-" + this.id + " { position:absolute; display:" + (this.options.carouselSlider ? "block" : "none") + "; cursor:pointer; overflow:hidden; width:16px; height:35px; left:" + String(this.options.thumbwidth + 5) + "px; top:0px; background:url('" + this.options.skinfolder + "slidertop.png')}  .html5gallery-car-right-" + this.id + " { position:absolute; display:" + (this.options.carouselSlider ? "block" : "none") + "; cursor:pointer; overflow:hidden; width:16px; height:35px; left:" + String(this.options.thumbwidth + 5) + "px; top:" + String(this.options.thumbMaskHeight - 35) + "px; background:url('" + this.options.skinfolder + "sliderbottom.png')} ") : n + (" .html5gallery-car-slider-" + this.id + " { position:absolute; display:" + (this.options.carouselSlider ? "block" : "none") + "; overflow:hidden; cursor:pointer; top:0px; height:54px; left:" + String(this.options.thumbwidth + 5) + "px; width:16px; background:url('" + this.options.skinfolder + "slider.png');}"), e = 0, this.options.carouselNavButton && (e = Math.round(this.options.carAreaLength / 2 - this.options.thumbMaskHeight / 2)), n += " .html5gallery-car-mask-" + this.id + " { position:absolute; display:block; overflow:hidden; top:" + e + "px; height:" + this.options.thumbMaskHeight + "px; left:0px; width:" + this.options.thumbwidth + "px;} ", e = this.options.thumbheight, this.options.isIE || (e = this.options.thumbheight - 2), n += " .html5gallery-tn-" + this.id + " { display:block; margin-bottom:" + this.options.thumbgap + "px; text-align:center; cursor:pointer; width:" + this.options.thumbwidth + "px;height:" + e + "px;overflow:hidden;", this.options.carouselbgtransparent ? n += "background-color:transparent;" : (this.options.isIE || (n += "border-top:1px solid " + this.options.carouseltopborder + "; border-bottom:1px solid " + this.options.carouselbottomborder + ";"), n += "background-color: " + this.options.carouselbgcolorend + "; background: " + this.options.carouselbgcolorend + " -webkit-gradient(linear, left top, left bottom, from(" + this.options.carouselbgcolorstart + "), to(" + this.options.carouselbgcolorend + ")) no-repeat; background: " + this.options.carouselbgcolorend + " -moz-linear-gradient(top, " + this.options.carouselbgcolorstart + ", " + this.options.carouselbgcolorend + ") no-repeat; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=" + this.options.carouselbgcolorstart + ", endColorstr=" + this.options.carouselbgcolorend + ") no-repeat; -ms-filter: 'progid:DXImageTransform.Microsoft.gradient(startColorstr=" + this.options.carouselbgcolorstart + ", endColorstr=" + this.options.carouselbgcolorend + ")' no-repeat;"), this.options.carouselbgimage && (n += ""), n = n + "}" + (" .html5gallery-tn-selected-" + this.id + " { display:block; margin-bottom:" + this.options.thumbgap + "px;text-align:center; cursor:pointer; width:" + this.options.thumbwidth + "px;height:" + e + "px;overflow:hidden;"), this.options.carouselbgtransparent ? n += "background-color:transparent;" : (this.options.isIE || (n += "border-top:1px solid " + this.options.carouselhighlighttopborder + "; border-bottom:1px solid " + this.options.carouselhighlightbottomborder + ";"), n += "background-color: " + this.options.carouselhighlightbgcolorend + "; background: " + this.options.carouselhighlightbgcolorend + " -webkit-gradient(linear, left top, left bottom, from(" + this.options.carouselhighlightbgcolorstart + "), to(" + this.options.carouselhighlightbgcolorend + ")) no-repeat; background: " + this.options.carouselhighlightbgcolorend + " -moz-linear-gradient(top, " + this.options.carouselhighlightbgcolorstart + ", " + this.options.carouselhighlightbgcolorend + ") no-repeat; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=" + this.options.carouselhighlightbgcolorstart + ", endColorstr=" + this.options.carouselhighlightbgcolorend + ") no-repeat; -ms-filter: 'progid:DXImageTransform.Microsoft.gradient(startColorstr=" + this.options.carouselhighlightbgcolorstart + ", endColorstr=" + this.options.carouselhighlightbgcolorend + ")' no-repeat;"), this.options.carouselbgimage && (n += ""), n += "}", n += " .html5gallery-tn-selected-" + this.id + " .html5gallery-tn-img-" + this.id + " {background-color:" + this.options.thumbimagebordercolor + ";} .html5gallery-tn-" + this.id + " { filter:alpha(opacity=" + Math.round(100 * this.options.thumbopacity) + "); opacity:" + this.options.thumbopacity + "; }  .html5gallery-tn-selected-" + this.id + " { filter:alpha(opacity=100); opacity:1; } ", e = this.options.thumbwidth - 3 * this.options.thumbmargin, this.options.thumbshowimage ? (e -= this.options.thumbimagewidth + 2 * this.options.thumbimageborder, t = this.options.thumbshowtitle ? this.options.thumbmargin : this.options.thumbwidth / 2 - (this.options.thumbimagewidth + 2 * this.options.thumbimageborder) / 2, r = Math.round((this.options.thumbheight - 2) / 2 - (this.options.thumbimageheight + 2 * this.options.thumbimageborder) / 2), n += " .html5gallery-tn-img-" + this.id + " {display:block; overflow:hidden; float:left; margin-top:" + r + "px; margin-left:" + t + "px; width:" + String(this.options.thumbimagewidth + 2 * this.options.thumbimageborder) + "px;height:" + String(this.options.thumbimageheight + 2 * this.options.thumbimageborder) + "px;}") : n += " .html5gallery-tn-img-" + this.id + " {display:none;}", this.options.thumbshowtitle ? (n += " .html5gallery-tn-title-" + this.id + " {display:block; overflow:hidden; float:left; margin-top:0px; margin-left:" + this.options.thumbmargin + "px; width:" + e + "px;height:" + String(this.options.thumbheight - 2) + "px;line-height:" + String(this.options.thumbheight - 2) + "px;}", n += " .html5gallery-tn-title-" + this.id + this.options.thumbtitlecss) : n += " .html5gallery-tn-title-" + this.id + " {display:none;}", this.carouselHighlight = function(e) {
                    u("#html5gallery-tn-" + this.id + "-" + e, this.$gallery).removeClass("html5gallery-tn-" + this.id).addClass("html5gallery-tn-selected-" + this.id);
                    if (!(this.options.thumbShowNum >= this.elemArray.length)) {
                        e = Math.floor(e / this.options.thumbShowNum) * this.options.thumbShowNum * (this.options.thumbheight + this.options.thumbgap);
                        e >= this.options.thumbTotalHeight - this.options.thumbMaskHeight && (e = this.options.thumbTotalHeight - this.options.thumbMaskHeight);
                        var t = e / (this.elemArray.length * (this.options.thumbheight + this.options.thumbgap) - this.options.thumbMaskHeight), t = t * (this.options.carouselSliderMax - this.options.carouselSliderMin);
                        u(".html5gallery-car-slider-" + this.id, this.$gallery).stop(!0).animate({top: t}, 300);
                        u(".html5gallery-thumbs-" + this.id, this.$gallery).stop(!0).animate({marginTop: -1 * e}, 300);
                        this.updateCarouseButtons(-e)
                    }
                }, this.carouselBarClicked = function(e) {
                    var t = u(".html5gallery-thumbs-" + this.id, this.$gallery);
                    e.pageY > u(".html5gallery-car-slider-" + this.id, this.$gallery).offset().top ? (e = -1 * parseInt(t.css("margin-top")) + this.options.thumbShowNum * (this.options.thumbheight + this.options.thumbgap), e >= this.options.thumbTotalHeight - this.options.thumbMaskHeight && (e = this.options.thumbTotalHeight - this.options.thumbMaskHeight)) : (e = -1 * parseInt(t.css("margin-top")) - this.options.thumbShowNum * (this.options.thumbheight + this.options.thumbgap), 0 > e && (e = 0));
                    t.stop(!0).animate({marginTop: -e}, 500);
                    this.updateCarouseButtons(-e);
                    e = e * (this.options.carouselSliderMax - this.options.carouselSliderMin) / (this.elemArray.length * (this.options.thumbheight + this.options.thumbgap) - this.options.thumbMaskHeight);
                    e < this.options.carouselSliderMin && (e = this.options.carouselSliderMin);
                    e > this.options.carouselSliderMax && (e = this.options.carouselSliderMax);
                    u(".html5gallery-car-slider-" + this.id, this.$gallery).stop(!0).animate({top: e}, 500)
                }, this.carouselSliderDrag = function(e, t) {
                    var n = t.offsetY - u(".html5gallery-car-slider-bar-" + this.id, this.$gallery).offset().top;
                    n < this.options.carouselSliderMin && (n = this.options.carouselSliderMin);
                    n > this.options.carouselSliderMax && (n = this.options.carouselSliderMax);
                    u(".html5gallery-car-slider-" + this.id, this.$gallery).css({top: n});
                    var r = this.elemArray.length * (this.options.thumbheight + this.options.thumbgap) - this.options.thumbMaskHeight, r = r * n / (this.options.carouselSliderMax - this.options.carouselSliderMin), r = Math.round(r / (this.options.thumbheight + this.options.thumbgap)), r = -1 * r * (this.options.thumbheight + this.options.thumbgap);
                    u(".html5gallery-thumbs-" + this.id, this.$gallery).stop(!0).animate({marginTop: r}, 300)
                }, this.carouselPrev = function() {
                    var e = u(".html5gallery-thumbs-" + this.id, this.$gallery);
                    if (0 != parseInt(e.css("margin-top"))) {
                        var t = -1 * parseInt(e.css("margin-top")) - this.options.thumbShowNum * (this.options.thumbheight + this.options.thumbgap);
                        0 > t && (t = 0);
                        e.animate({marginTop: -t}, 500, this.options.carouseleasing);
                        this.updateCarouseButtons(-t)
                    }
                }, this.carouselNext = function() {
                    var e = u(".html5gallery-thumbs-" + this.id, this.$gallery);
                    if (parseInt(e.css("margin-top")) != -(this.options.thumbTotalHeight - this.options.thumbMaskHeight)) {
                        var t = -1 * parseInt(e.css("margin-top")) + this.options.thumbShowNum * (this.options.thumbheight + this.options.thumbgap);
                        t >= this.options.thumbTotalHeight - this.options.thumbMaskHeight && (t = this.options.thumbTotalHeight - this.options.thumbMaskHeight);
                        e.animate({marginTop: -t}, 500, this.options.carouseleasing);
                        this.updateCarouseButtons(-t)
                    }
                }, this.updateCarouseButtons = function(e) {
                    var t = u(".html5gallery-car-left-" + this.id, this.$gallery), n = u(".html5gallery-car-right-" + this.id, this.$gallery), r = -1 * (this.options.thumbTotalHeight - this.options.thumbMaskHeight);
                    0 == e ? (t.css({"background-position": "-64px 0px", cursor: ""}), t.data("disabled", !0)) : t.data("disabled") && (t.css({"background-position": "0px 0px", cursor: "pointer"}), t.data("disabled", !1));
                    e == r ? (n.css({"background-position": "-64px 0px", cursor: ""}), n.data("disabled", !0)) : n.data("disabled") && (n.css({"background-position": "0px 0px", cursor: "pointer"}), n.data("disabled", !1))
                }) : n += " .html5gallery-car-" + this.id + " { display:none; }";
                n = this.options.freeversion ? n + (" .html5gallery-watermark-" + this.id + " {display:block; position:absolute; top:4px; left:4px; width:120px; text-align:center; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; filter:alpha(opacity=60); opacity:0.6; background-color:#333333; color:#ffffff; font:12px Armata, sans-serif, Arial;}") : 0 < this.options.watermark.length ? n + (" .html5gallery-watermark-" + this.id + " {display:block; position:absolute; top:0px; left:0px;}") : n + (" .html5gallery-watermark-" + this.id + " {display:none;}");
                u("head").append("<style type='text/css'>" + n + "</style>")
            }, resizeImageToolbox: function() {
                if ("center" != this.options.imagetoolboxstyle) {
                    var e = Math.round(("bottom" == this.options.headerpos ? 0 : this.options.headerHeight) + this.options.height / 2 - 24), t = e + Math.round(this.options.height / 2) - 24, n = this.options.width - 48, r = this.options.showfullscreenbutton ? n - 48 : n;
                    u(".html5gallery-play-" + this.id).css({top: t + "px", left: r + "px"});
                    u(".html5gallery-pause-" + this.id).css({top: t + "px", left: r + "px"});
                    u(".html5gallery-left-" + this.id).css({top: e + "px"});
                    u(".html5gallery-right-" + this.id).css({top: e + "px", left: n + "px"});
                    u(".html5gallery-lightbox-" + this.id).css({top: t + "px", left: n + "px"})
                }
            }, createImageToolbox: function() {
                1 >= this.elemArray.length && (this.options.showplaybutton = this.options.showprevbutton = this.options.shownextbutton = !1);
                if ("never" != this.options.showimagetoolbox) {
                    var e;
                    if ("center" == this.options.imagetoolboxstyle)
                        e = " .html5gallery-toolbox-" + this.id + " {display:none; overflow:hidden; position:relative; margin:0px auto; text-align:center; height:40px;}", e += " .html5gallery-toolbox-bg-" + this.id + " {display:block; left:0px; top:0px; width:100%; height:100%; position:absolute; filter:alpha(opacity=60); opacity:0.6; background-color:#222222; }", e += " .html5gallery-toolbox-buttons-" + this.id + " {display:block; margin:0px auto; height:100%;}", e += " .html5gallery-play-" + this.id + " { position:relative; float:left; display:none; cursor:pointer; overflow:hidden; width:32px; height:32px; margin-left:2px; margin-right:2px; margin-top:" + Math.round(4) + "px; background:url('" + this.options.skinfolder + "play.png') no-repeat top left; } ", e += " .html5gallery-pause-" + this.id + " { position:relative; float:left; display:none; cursor:pointer; overflow:hidden; width:32px; height:32px; margin-left:2px; margin-right:2px; margin-top:" + Math.round(4) + "px; background:url('" + this.options.skinfolder + "pause.png') no-repeat top left; } ", e += " .html5gallery-left-" + this.id + " { position:relative; float:left; display:none; cursor:pointer; overflow:hidden; width:32px; height:32px; margin-left:2px; margin-right:2px; margin-top:" + Math.round(4) + "px; background:url('" + this.options.skinfolder + "prev.png') no-repeat top left; } ", e += " .html5gallery-right-" + this.id + " { position:relative; float:left; display:none; cursor:pointer; overflow:hidden; width:32px; height:32px; margin-left:2px; margin-right:2px; margin-top:" + Math.round(4) + "px; background:url('" + this.options.skinfolder + "next.png') no-repeat top left; } ", e += " .html5gallery-lightbox-" + this.id + " {position:relative; float:left; display:none; cursor:pointer; overflow:hidden; width:32px; height:32px; margin-left:2px; margin-right:2px; margin-top:" + Math.round(4) + "px; background:url('" + this.options.skinfolder + "lightbox.png') no-repeat top left; } ";
                    else {
                        var t = Math.round(("bottom" == this.options.headerpos ? 0 : this.options.headerHeight) + this.options.height / 2 - 24), n = t + Math.round(this.options.height / 2) - 24, r = this.options.width - 48, i = this.options.showfullscreenbutton ? r - 48 : r;
                        e = " .html5gallery-toolbox-" + this.id + " {display:none;}";
                        e += " .html5gallery-toolbox-bg-" + this.id + " {display:none;}";
                        e += " .html5gallery-toolbox-buttons-" + this.id + " {display:block;}";
                        e += " .html5gallery-play-" + this.id + " { position:absolute; display:none; cursor:pointer; top:" + n + "px; left:" + i + "px; width:48px; height:48px; background:url('" + this.options.skinfolder + "side_play.png') no-repeat top left;} ";
                        e += " .html5gallery-pause-" + this.id + " { position:absolute; display:none; cursor:pointer; top:" + n + "px; left:" + i + "px; width:48px; height:48px; background:url('" + this.options.skinfolder + "side_pause.png') no-repeat top left;} ";
                        e += " .html5gallery-left-" + this.id + " { position:absolute; display:none; cursor:pointer; top:" + t + "px; left:0px; width:48px; height:48px; background:url('" + this.options.skinfolder + "side_prev.png') no-repeat center center;} ";
                        e += " .html5gallery-right-" + this.id + " { position:absolute; display:none; cursor:pointer; top:" + t + "px; left:" + r + "px; width:48px; height:48px; background:url('" + this.options.skinfolder + "side_next.png')  no-repeat center center;} ";
                        e += " .html5gallery-lightbox-" + this.id + " {position:absolute; display:none; cursor:pointer; top:" + n + "px; left:" + r + "px; width:48px; height:48px; background:url('" + this.options.skinfolder + "side_lightbox.png') no-repeat top left;} "
                    }
                    u(".html5gallery-play-" + this.id, this.$gallery).hover(function() {
                        u(this).css({"background-position": "right top"})
                    }, function() {
                        u(this).css({"background-position": "left top"})
                    });
                    u(".html5gallery-pause-" + this.id, this.$gallery).hover(function() {
                        u(this).css({"background-position": "right top"})
                    }, function() {
                        u(this).css({"background-position": "left top"})
                    });
                    u(".html5gallery-left-" + this.id, this.$gallery).hover(function() {
                        u(this).css({"background-position": "right top"})
                    }, function() {
                        u(this).css({"background-position": "left top"})
                    });
                    u(".html5gallery-right-" + this.id, this.$gallery).hover(function() {
                        u(this).css({"background-position": "right top"})
                    }, function() {
                        u(this).css({"background-position": "left top"})
                    });
                    u(".html5gallery-lightbox-" + this.id, this.$gallery).hover(function() {
                        u(this).css({"background-position": "right top"})
                    }, function() {
                        u(this).css({"background-position": "left top"})
                    });
                    u("head").append("<style type='text/css'>" + e + "</style>")
                }
                this.showimagetoolbox = function(e) {
                    if (this.options.showplaybutton || this.options.showprevbutton || this.options.shownextbutton || this.options.showfullscreenbutton) {
                        if ("center" == this.options.imagetoolboxstyle) {
                            var t = Math.round(("bottom" == this.options.headerpos ? 0 : this.options.headerHeight) + this.options.height / 2);
                            if (6 == e || 7 == e || 8 == e || 9 == e || 10 == e)
                                t += 45;
                            u(".html5gallery-toolbox-" + this.id, this.$gallery).css({top: t});
                            t = 0;
                            this.options.showplaybutton && 1 == e ? (t += 36, this.isPaused ? (u(".html5gallery-play-" + this.id, this.$gallery).show(), u(".html5gallery-pause-" + this.id, this.$gallery).hide()) : (u(".html5gallery-play-" + this.id, this.$gallery).hide(), u(".html5gallery-pause-" + this.id, this.$gallery).show())) : (u(".html5gallery-play-" + this.id, this.$gallery).hide(), u(".html5gallery-pause-" + this.id, this.$gallery).hide());
                            this.options.showprevbutton ? (t += 36, u(".html5gallery-left-" + this.id, this.$gallery).show()) : u(".html5gallery-left-" + this.id, this.$gallery).hide();
                            this.options.shownextbutton ? (t += 36, u(".html5gallery-right-" + this.id, this.$gallery).show()) : u(".html5gallery-right-" + this.id, this.$gallery).hide();
                            this.options.showfullscreenbutton && 1 == e ? (t += 36, u(".html5gallery-lightbox-" + this.id, this.$gallery).show()) : u(".html5gallery-lightbox-" + this.id, this.$gallery).hide();
                            u(".html5gallery-toolbox-" + this.id, this.$gallery).css({width: t + 16});
                            u(".html5gallery-toolbox-buttons-" + this.id, this.$gallery).css({width: t})
                        } else
                            this.options.showplaybutton && 1 == e ? this.isPaused ? (u(".html5gallery-play-" + this.id, this.$gallery).show(), u(".html5gallery-pause-" + this.id, this.$gallery).hide()) : (u(".html5gallery-play-" + this.id, this.$gallery).hide(), u(".html5gallery-pause-" + this.id, this.$gallery).show()) : (u(".html5gallery-play-" + this.id, this.$gallery).hide(), u(".html5gallery-pause-" + this.id, this.$gallery).hide()), this.options.showprevbutton ? u(".html5gallery-left-" + this.id, this.$gallery).show() : u(".html5gallery-left-" + this.id, this.$gallery).hide(), this.options.shownextbutton ? u(".html5gallery-right-" + this.id, this.$gallery).show() : u(".html5gallery-right-" + this.id, this.$gallery).hide(), this.options.showfullscreenbutton && 1 == e ? u(".html5gallery-lightbox-" + this.id, this.$gallery).show() : u(".html5gallery-lightbox-" + this.id, this.$gallery).hide();
                        this.options.isIE678 ? u(".html5gallery-toolbox-" + this.id, this.$gallery).show() : u(".html5gallery-toolbox-" + this.id, this.$gallery).fadeIn()
                    }
                };
                this.hideimagetoolbox = function() {
                    this.options.isIE678 ? u(".html5gallery-toolbox-" + this.id, this.$gallery).hide() : u(".html5gallery-toolbox-" + this.id, this.$gallery).fadeOut()
                }
            }, resizeStyleDefault: function() {
                if (this.container.parent() && this.container.parent().width()) {
                    this.options.containerWidth = this.container.parent().width();
                    this.options.totalWidth = this.options.containerWidth;
                    this.options.width = this.options.totalWidth - 2 * this.options.padding;
                    this.options.responsivefullscreen && 0 < this.container.parent().height() ? (this.options.containerHeight = this.container.parent().height(), this.options.totalHeight = this.options.containerHeight, this.options.height = this.options.totalHeight - (this.options.carouselHeight + this.options.carouselmargin + this.options.headerHeight + 2 * this.options.padding)) : (this.options.height = Math.round(this.options.width * this.options.originalHeight / this.options.originalWidth), this.options.totalHeight = this.options.height + this.options.carouselHeight + this.options.carouselmargin + this.options.headerHeight + 2 * this.options.padding, this.options.containerHeight = this.options.totalHeight);
                    this.container.css({width: this.options.containerWidth, height: this.options.containerHeight});
                    this.options.boxWidth = this.options.width;
                    this.options.boxHeight = this.options.height + this.options.headerHeight;
                    this.options.showcarousel && (this.options.carouselWidth = this.options.width, this.options.carouselHeight = this.options.thumbheight + 2 * this.options.thumbmargin, this.options.carouselLeft = this.options.padding, this.options.carouselTop = this.options.padding + this.options.boxHeight + this.options.carouselmargin);
                    u(".html5gallery-container-" + this.id).css({width: this.options.totalWidth + "px", height: this.options.totalHeight + "px"});
                    u(".html5gallery-box-" + this.id).css({width: this.options.boxWidth + "px", height: this.options.boxHeight + "px"});
                    var e = this.elemArray[this.curElem][9];
                    if (1 == e) {
                        var e = this.elemArray[this.curElem][10], t = this.elemArray[this.curElem][11], n;
                        this.isFullscreen ? (n = Math.min(this.fullscreenWidth / e, this.fullscreenHeight / t), n = 1 < n ? 1 : n) : n = "fill" == this.options.resizemode ? Math.max(this.options.width / e, this.options.height / t) : Math.min(this.options.width / e, this.options.height / t);
                        e = Math.round(n * e);
                        n = Math.round(n * t);
                        var t = this.isFullscreen ? e : this.options.width, r = this.isFullscreen ? n : this.options.height, i = Math.round(t / 2 - e / 2), s = Math.round(r / 2 - n / 2);
                        this.isFullscreen && this.adjustFullscreen(t, r, !0);
                        u(".html5gallery-elem-" + this.id).css({width: t + "px", height: r + "px"});
                        u(".html5gallery-elem-img-" + this.id).css({width: t + "px", height: r + "px"});
                        u(".html5gallery-elem-image-" + this.id).css({width: e + "px", height: n + "px", top: s + "px", left: i + "px"})
                    } else if (5 == e || 6 == e || 7 == e || 8 == e || 9 == e || 10 == e)
                        t = this.elemArray[this.curElem][10], e = this.elemArray[this.curElem][11], this.isFullscreen ? (n = Math.min(this.fullscreenWidth / t, this.fullscreenHeight / e), n = 1 < n ? 1 : n, t = Math.round(n * t), r = Math.round(n * e), this.adjustFullscreen(t, r, !0)) : (t = this.options.width, r = this.options.height), u(".html5gallery-elem-" + this.id).css({width: t + "px", height: r + "px"}), u(".html5gallery-elem-video-" + this.id).css({width: t + "px", height: r + "px"}), u("#html5gallery-elem-video-" + this.id).css({width: t + "px", height: r + "px"});
                    t = e = 0;
                    "bottom" == this.options.headerpos && (e = this.options.titleoverlay ? this.options.height - this.options.titleheight : this.options.height, t = this.options.titleoverlay ? this.options.height : this.options.height + this.options.titleheight);
                    u(".html5gallery-title-" + this.id).css({width: this.options.boxWidth + "px"});
                    this.options.titleoverlay || u(".html5gallery-title-" + this.id).css({top: e + "px"});
                    u(".html5gallery-viral-" + this.id).css({top: t + "px"});
                    u(".html5gallery-timer-" + this.id).css({top: String(this.options.elemTop + this.options.height - 2) + "px"});
                    this.options.showcarousel && (u(".html5gallery-car-" + this.id).css({width: this.options.width + "px", top: this.options.carouselTop + "px"}), e = 4, this.options.slideshadow && (e += 12), u(".html5gallery-car-list-" + this.id).css({width: String(this.options.width - e - 4) + "px"}), t = 0, this.options.carouselNavButton && (t = 72), this.options.thumbShowNum = Math.floor((this.options.width - e - 4 - t) / (this.options.thumbwidth + this.options.thumbgap)), this.options.thumbMaskWidth = this.options.thumbShowNum * this.options.thumbwidth + this.options.thumbShowNum * this.options.thumbgap, this.options.thumbTotalWidth = this.elemArray.length * this.options.thumbwidth + (this.elemArray.length - 1) * this.options.thumbgap, t = 0, this.options.thumbMaskWidth > this.options.thumbTotalWidth && (t = this.options.thumbMaskWidth / 2 - this.options.thumbTotalWidth / 2), u(".html5gallery-thumbs-" + this.id).css({"margin-left": t + "px", width: String(this.elemArray.length * (this.options.thumbwidth + this.options.thumbgap)) + "px"}), e = Math.round((this.options.width - e - 4) / 2 - this.options.thumbMaskWidth / 2), u(".html5gallery-car-mask-" + this.id).css({left: e + "px", width: this.options.thumbMaskWidth + "px"}), this.carouselHighlight(this.curElem, !0))
                }
            }, createStyleDefault: function() {
                this.options.thumbimagewidth = this.options.thumbwidth - 2 * this.options.thumbimageborder;
                this.options.thumbimageheight = this.options.thumbheight - 2 * this.options.thumbimageborder;
                this.options.thumbshowtitle && (this.options.thumbheight += this.options.thumbtitleheight);
                this.options.showtitle || (this.options.titleheight = 0);
                if (!this.options.showsocialmedia || !this.options.showfacebooklike && !this.options.showtwitter && !this.options.showgoogleplus)
                    this.options.socialheight = 0;
                this.options.headerHeight = this.options.titleoverlay ? this.options.socialheight : this.options.titleheight + this.options.socialheight;
                this.options.boxWidth = this.options.width;
                this.options.boxHeight = this.options.height + this.options.headerHeight;
                this.options.boxLeft = this.options.padding;
                this.options.boxTop = this.options.padding;
                this.options.showcarousel ? (this.options.carouselWidth = this.options.width, this.options.carouselHeight = this.options.thumbheight + 2 * this.options.thumbmargin, this.options.carouselLeft = this.options.padding, this.options.carouselTop = this.options.padding + this.options.boxHeight + this.options.carouselmargin) : (this.options.carouselWidth = 0, this.options.carouselHeight = 0, this.options.carouselLeft = 0, this.options.carouselTop = 0, this.options.carouselmargin = 0);
                this.options.totalWidth = this.options.width + 2 * this.options.padding;
                this.options.totalHeight = this.options.height + this.options.carouselHeight + this.options.carouselmargin + this.options.headerHeight + 2 * this.options.padding;
                this.options.containerWidth = this.options.totalWidth;
                this.options.containerHeight = this.options.totalHeight;
                this.options.responsive ? (this.options.originalWidth = this.options.width, this.options.originalHeight = this.options.height, this.container.css({"max-width": "100%"})) : this.container.css({width: this.options.containerWidth, height: this.options.containerHeight});
                var e = 0, t = 0;
                this.options.elemTop = 0;
                "top" == this.options.headerpos ? (t = 0, e = this.options.socialheight, this.options.elemTop = this.options.headerHeight) : "bottom" == this.options.headerpos && (this.options.elemTop = 0, e = this.options.titleoverlay ? this.options.height - this.options.titleheight : this.options.height, t = this.options.titleoverlay ? this.options.height : this.options.height + this.options.titleheight);
                var n = " .html5gallery-container-" + this.id + " { display:block; position:absolute; left:0px; top:0px; width:" + this.options.totalWidth + "px; height:" + this.options.totalHeight + "px; background-color:" + this.options.bgcolor + ";}";
                this.options.galleryshadow && (n += " .html5gallery-container-" + this.id + " { -moz-box-shadow: 0px 2px 5px #aaa; -webkit-box-shadow: 0px 2px 5px #aaa; box-shadow: 0px 2px 5px #aaa;}");
                var n = n + (" .html5gallery-box-" + this.id + " {display:block; position:absolute; text-align:center; left:" + this.options.boxLeft + "px; top:" + this.options.boxTop + "px; width:" + this.options.boxWidth + "px; height:" + this.options.boxHeight + "px;}"), r = Math.round(this.options.socialheight / 2 - 12), n = n + (" .html5gallery-title-text-" + this.id + " " + this.options.titlecss + " .html5gallery-title-text-" + this.id + " " + this.options.titlecsslink + " .html5gallery-error-" + this.id + " " + this.options.errorcss), n = n + (" .html5gallery-description-text-" + this.id + " " + this.options.descriptioncss + " .html5gallery-description-text-" + this.id + " " + this.options.descriptioncsslink), n = n + (" .html5gallery-fullscreen-title-" + this.id + "" + this.options.titlefullscreencss + " .html5gallery-fullscreen-title-" + this.id + "" + this.options.titlefullscreencsslink), n = n + (" .html5gallery-viral-" + this.id + " {display:block; overflow:hidden; position:absolute; text-align:left; top:" + t + "px; left:0px; width:" + this.options.boxWidth + "px; height:" + this.options.socialheight + "px; padding-top:" + r + "px;}"), n = n + (" .html5gallery-title-" + this.id + " {display:" + (this.options.titleoverlay && this.options.titleautohide ? "none" : "block") + "; overflow:hidden; position:absolute; left:0px; width:" + this.options.boxWidth + "px; "), n = this.options.titleoverlay ? "top" == this.options.headerpos ? n + "top:0px; height:auto; }" : n + "bottom:0px; height:auto; }" : n + ("top:" + e + "px; height:" + this.options.titleheight + "px; }"), n = n + (" .html5gallery-timer-" + this.id + " {display:block; position:absolute; top:" + String(this.options.elemTop + this.options.height - 2) + "px; left:0px; width:0px; height:2px; background-color:#ccc; filter:alpha(opacity=60); opacity:0.6; }"), n = n + (" .html5gallery-elem-" + this.id + " {display:block; overflow:hidden; position:absolute; top:" + this.options.elemTop + "px; left:0px; width:" + this.options.width + "px; height:" + this.options.height + "px;}");
                this.options.isIE7 || this.options.isIE6 ? (n += " .html5gallery-loading-" + this.id + " {display:none; }", n += " .html5gallery-loading-center-" + this.id + " {display:none; }") : (n += " .html5gallery-loading-" + this.id + " {display:block; position:absolute; top:4px; right:4px; width:100%; height:100%; background:url('" + this.options.skinfolder + "loading.gif') no-repeat top right;}", n += " .html5gallery-loading-center-" + this.id + " {display:block; position:absolute; top:0px; left:0px; width:100%; height:100%; background:url('" + this.options.skinfolder + "loading_center.gif') no-repeat center center;}");
                0 < this.options.borderradius && (n += " .html5gallery-elem-" + this.id + " {overflow:hidden; border-radius:" + this.options.borderradius + "px; -moz-border-radius:" + this.options.borderradius + "px; -webkit-border-radius:" + this.options.borderradius + "px;}");
                this.options.slideshadow && (n += " .html5gallery-title-" + this.id + " { padding:4px;}", n += " .html5gallery-timer-" + this.id + " { margin:4px;}", n += " .html5gallery-elem-" + this.id + " { overflow:hidden; padding:4px; -moz-box-shadow: 0px 2px 5px #aaa; -webkit-box-shadow: 0px 2px 5px #aaa; box-shadow: 0px 2px 5px #aaa;}");
                this.options.showcarousel ? (n += " .html5gallery-car-" + this.id + " { position:absolute; display:block; overflow:hidden; left:" + this.options.carouselLeft + "px; top:" + this.options.carouselTop + "px; width:" + this.options.width + "px; height:" + this.options.carouselHeight + "px;", n = this.options.carouselbgtransparent ? n + "background-color:transparent;" : n + ("border-top:1px solid " + this.options.carouseltopborder + ";border-bottom:1px solid " + this.options.carouselbottomborder + ";background-color: " + this.options.carouselbgcolorend + "; background: " + this.options.carouselbgcolorend + " -webkit-gradient(linear, left top, left bottom, from(" + this.options.carouselbgcolorstart + "), to(" + this.options.carouselbgcolorend + ")) no-repeat; background: " + this.options.carouselbgcolorend + " -moz-linear-gradient(top, " + this.options.carouselbgcolorstart + ", " + this.options.carouselbgcolorend + ") no-repeat; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=" + this.options.carouselbgcolorstart + ", endColorstr=" + this.options.carouselbgcolorend + ") no-repeat; -ms-filter: 'progid:DXImageTransform.Microsoft.gradient(startColorstr=" + this.options.carouselbgcolorstart + ", endColorstr=" + this.options.carouselbgcolorend + ")' no-repeat;"), this.options.carouselbgimage && (n += "background:url('" + this.options.skinfolder + this.options.carouselbgimage + "') center top repeat;"), e = 4, this.options.slideshadow && (e += 12), n = n + "}" + (" .html5gallery-car-list-" + this.id + " { position:absolute; display:block; overflow:hidden; left:" + e + "px; width:" + String(this.options.width - e - 4) + "px; top:0px; height:" + this.options.carouselHeight + "px; }"), t = 0, this.options.carouselNavButton = !1, Math.floor((this.options.width - e - 4) / (this.options.thumbwidth + this.options.thumbgap)) < this.elemArray.length && (this.options.carouselNavButton = !0), this.options.carouselNavButton && (n += " .html5gallery-car-left-" + this.id + " { position:absolute; display:block; overflow:hidden; width:32px; height:32px; left:0px; top:" + String(this.options.carouselHeight / 2 - 16) + "px; background:url('" + this.options.skinfolder + "carousel_left.png') no-repeat 0px 0px;}  .html5gallery-car-right-" + this.id + " { position:absolute; display:block; overflow:hidden; width:32px; height:32px; right:0px; top:" + String(this.options.carouselHeight / 2 - 16) + "px; background:url('" + this.options.skinfolder + "carousel_right.png') no-repeat 0px 0px;} ", t = 72), this.options.thumbShowNum = Math.floor((this.options.width - e - 4 - t) / (this.options.thumbwidth + this.options.thumbgap)), this.options.thumbMaskWidth = this.options.thumbShowNum * this.options.thumbwidth + this.options.thumbShowNum * this.options.thumbgap, this.options.thumbTotalWidth = this.elemArray.length * this.options.thumbwidth + (this.elemArray.length - 1) * this.options.thumbgap, t = 0, this.options.thumbMaskWidth > this.options.thumbTotalWidth && (t = this.options.thumbMaskWidth / 2 - this.options.thumbTotalWidth / 2), n += ".html5gallery-thumbs-" + this.id + " { position:relative; display:block; margin-left:" + t + "px; width:" + String(this.elemArray.length * (this.options.thumbwidth + this.options.thumbgap)) + "px; top:" + Math.round(this.options.carouselHeight / 2 - this.options.thumbheight / 2) + "px; }", e = Math.round((this.options.width - e - 4) / 2 - this.options.thumbMaskWidth / 2), n += " .html5gallery-car-mask-" + this.id + " { position:absolute; display:block; text-align:left; overflow:hidden; left:" + e + "px; width:" + this.options.thumbMaskWidth + "px; top:0px; height:" + this.options.carouselHeight + "px;} ", n += " .html5gallery-tn-" + this.id + " { display:block; float:left; margin-left:" + Math.round(this.options.thumbgap / 2) + "px; margin-right:" + Math.round(this.options.thumbgap / 2) + "px; text-align:center; cursor:pointer; width:" + this.options.thumbwidth + "px;height:" + this.options.thumbheight + "px;overflow:hidden;}", this.options.thumbshadow && (n += " .html5gallery-tn-" + this.id + " { -moz-box-shadow: 0px 2px 5px #aaa; -webkit-box-shadow: 0px 2px 5px #aaa; box-shadow: 0px 2px 5px #aaa;}"), n += " .html5gallery-tn-selected-" + this.id + " { display:block; float:left; margin-left:" + Math.round(this.options.thumbgap / 2) + "px; margin-right:" + Math.round(this.options.thumbgap / 2) + "px;text-align:center; cursor:pointer; width:" + this.options.thumbwidth + "px;height:" + this.options.thumbheight + "px;overflow:hidden;}", this.options.thumbshadow && (n += " .html5gallery-tn-selected-" + this.id + " { -moz-box-shadow: 0px 2px 5px #aaa; -webkit-box-shadow: 0px 2px 5px #aaa; box-shadow: 0px 2px 5px #aaa;}"), n += " .html5gallery-tn-" + this.id + " {background-color:" + this.options.thumbimagebordercolor + ";} .html5gallery-tn-" + this.id + " { filter:alpha(opacity=" + Math.round(100 * this.options.thumbopacity) + "); opacity:" + this.options.thumbopacity + "; }  .html5gallery-tn-selected-" + this.id + " { filter:alpha(opacity=100); opacity:1; } ", n += " .html5gallery-tn-img-" + this.id + " {display:block; overflow:hidden; width:" + String(this.options.thumbimagewidth + 2 * this.options.thumbimageborder) + "px;height:" + String(this.options.thumbimageheight + 2 * this.options.thumbimageborder) + "px;}", this.options.thumbunselectedimagebordercolor && (n += " .html5gallery-tn-selected-" + this.id + " {background-color:" + this.options.thumbunselectedimagebordercolor + ";}"), this.options.thumbshowtitle ? (n += " .html5gallery-tn-title-" + this.id + " {display:block; overflow:hidden; float:top; height:" + this.options.thumbtitleheight + "px;width:" + String(this.options.thumbwidth - 2) + "px;line-height:" + this.options.thumbtitleheight + "px;}", n += " .html5gallery-tn-title-" + this.id + this.options.thumbtitlecss) : n += " .html5gallery-tn-title-" + this.id + " {display:none;}", this.carouselHighlight = function(e, t) {
                    u("#html5gallery-tn-" + this.id + "-" + e, this.$gallery).removeClass("html5gallery-tn-" + this.id).addClass("html5gallery-tn-selected-" + this.id);
                    if (this.options.thumbShowNum >= this.elemArray.length)
                        u(".html5gallery-car-left-" + this.id, this.$gallery).css({"background-position": "-64px 0px", cursor: ""}), u(".html5gallery-car-left-" + this.id, this.$gallery).data("disabled", !0), u(".html5gallery-car-right-" + this.id, this.$gallery).css({"background-position": "-64px 0px", cursor: ""}), u(".html5gallery-car-right-" + this.id, this.$gallery).data("disabled", !0);
                    else {
                        var n = Math.floor(e / this.options.thumbShowNum) * this.options.thumbShowNum * (this.options.thumbwidth + this.options.thumbgap);
                        n >= this.options.thumbTotalWidth - this.options.thumbMaskWidth + this.options.thumbgap && (n = this.options.thumbTotalWidth - this.options.thumbMaskWidth + this.options.thumbgap);
                        n = -n;
                        t ? u(".html5gallery-thumbs-" + this.id, this.$gallery).css({marginLeft: n}) : u(".html5gallery-thumbs-" + this.id, this.$gallery).animate({marginLeft: n}, 500);
                        this.updateCarouseButtons(n)
                    }
                }, this.carouselPrev = function() {
                    var e = u(".html5gallery-thumbs-" + this.id, this.$gallery);
                    if (0 != parseInt(e.css("margin-left"))) {
                        var t = -1 * parseInt(e.css("margin-left")) - this.options.thumbShowNum * (this.options.thumbwidth + this.options.thumbgap);
                        0 > t && (t = 0);
                        e.animate({marginLeft: -t}, 500, this.options.carouseleasing);
                        this.updateCarouseButtons(-t)
                    }
                }, this.carouselNext = function() {
                    var e = u(".html5gallery-thumbs-" + this.id, this.$gallery);
                    if (parseInt(e.css("margin-left")) != -(this.options.thumbTotalWidth - this.options.thumbMaskWidth + this.options.thumbgap)) {
                        var t = -1 * parseInt(e.css("margin-left")) + this.options.thumbShowNum * (this.options.thumbwidth + this.options.thumbgap);
                        t >= this.options.thumbTotalWidth - this.options.thumbMaskWidth + this.options.thumbgap && (t = this.options.thumbTotalWidth - this.options.thumbMaskWidth + this.options.thumbgap);
                        e.animate({marginLeft: -t}, 500, this.options.carouseleasing);
                        this.updateCarouseButtons(-t)
                    }
                }, this.updateCarouseButtons = function(e) {
                    var t = u(".html5gallery-car-left-" + this.id, this.$gallery), n = u(".html5gallery-car-right-" + this.id, this.$gallery), r = -1 * (this.options.thumbTotalWidth - this.options.thumbMaskWidth + this.options.thumbgap);
                    0 == e ? (t.css({"background-position": "-64px 0px", cursor: ""}), t.data("disabled", !0)) : t.data("disabled") && (t.css({"background-position": "0px 0px", cursor: "pointer"}), t.data("disabled", !1));
                    e == r ? (n.css({"background-position": "-64px 0px", cursor: ""}), n.data("disabled", !0)) : n.data("disabled") && (n.css({"background-position": "0px 0px", cursor: "pointer"}), n.data("disabled", !1))
                }) : n += " .html5gallery-car-" + this.id + " { display:none; }";
                n = this.options.freeversion ? n + (" .html5gallery-watermark-" + this.id + " {display:block; position:absolute; top:4px; left:4px; width:120px; text-align:center; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; filter:alpha(opacity=60); opacity:0.6; background-color:#333333; color:#ffffff; font:12px Armata, sans-serif, Arial;}") : 0 < this.options.watermark.length ? n + (" .html5gallery-watermark-" + this.id + " {display:block; position:absolute; top:0px; left:0px; }") : n + (" .html5gallery-watermark-" + this.id + " {display:none;}");
                u("head").append("<style type='text/css'>" + n + "</style>")
            }, loadCarousel: function() {
                for (var e = this, t = u(".html5gallery-thumbs-" + this.id, this.$gallery), n = 0; n < this.elemArray.length; n++) {
                    var r = u("<div id='html5gallery-tn-" + this.id + "-" + n + "' class='html5gallery-tn-" + this.id + "' data-index=" + n + " ></div>");
                    r.appendTo(t);
                    r.unbind("click").click(function(t) {
                        e.slideRun(u(this).data("index"));
                        t.preventDefault()
                    });
                    r.hover(function() {
                        e.onThumbOver(u(this).data("index"));
                        u(this).removeClass("html5gallery-tn-" + e.id).addClass("html5gallery-tn-selected-" + e.id)
                    }, function() {
                        e.onThumbOut();
                        u(this).data("index") !== e.curElem && u(this).removeClass("html5gallery-tn-selected-" + e.id).addClass("html5gallery-tn-" + e.id)
                    });
                    r = new Image;
                    r.data = n;
                    u(r).load(function() {
                        var n = Math.max(e.options.thumbimagewidth / this.width, e.options.thumbimageheight / this.height), r = Math.round(n * this.width), n = Math.round(n * this.height), i = e.options.thumbshowplayonvideo && 1 != e.elemArray[this.data][9] ? "<div class='html5gallery-tn-img-play-" + e.id + "' style='display:block; overflow:hidden; position:absolute; width:" + e.options.thumbimagewidth + "px;height:" + e.options.thumbimageheight + "px; top:" + e.options.thumbimageborder + "px; left:" + e.options.thumbimageborder + 'px;background:url("' + e.options.skinfolder + "playvideo.png\") no-repeat center center;'></div>" : "";
                        u("#html5gallery-tn-" + e.id + "-" + this.data, t).append("<div class='html5gallery-tn-img-" + e.id + "' style='position:relative;'><div style='display:block; overflow:hidden; position:absolute; width:" + e.options.thumbimagewidth + "px;height:" + e.options.thumbimageheight + "px; top:" + e.options.thumbimageborder + "px; left:" + e.options.thumbimageborder + "px;'><img class='html5gallery-tn-image-" + e.id + "' style='border:none !important; padding:0px !important; margin:0px !important; max-width:none !important; max-height:none !important; width:" + r + "px !important; height:" + n + "px !important;' src='" + e.elemArray[this.data][1] + "' /></div>" + i + "</div><div class='html5gallery-tn-title-" + e.id + "'>" + e.elemArray[this.data][7] + "</div>")
                    });
                    r.src = this.elemArray[n][1]
                }
            }, goNormal: function() {
                clearTimeout(this.slideshowTimeout);
                u(document).unbind("keyup.html5gallery");
                u(".html5gallery-timer-" + this.id, this.$gallery).css({width: 0});
                clearInterval(this.slideTimer);
                this.slideTimerCount = 0;
                this.isFullscreen = !1;
                var e = u(".html5gallery-elem-" + this.id, this.$fullscreen).empty().css({top: this.options.elemTop});
                u(".html5gallery-box-" + this.id, this.$gallery).prepend(e);
                this.slideRun(this.curElem);
                this.$fullscreen.remove();
                this.hideimagetoolbox()
            }, resizeFullscreen: function() {
                var e = this.elemArray[this.curElem][10], t = this.elemArray[this.curElem][11];
                this.fullscreenWidth = u(window).width() - 2 * this.fullscreenMargin;
                var n = window.innerHeight ? window.innerHeight : u(window).height();
                this.fullscreenHeight = n - 2 * this.fullscreenMargin - this.fullscreenBarH;
                e = Math.min(this.fullscreenWidth / e, this.fullscreenHeight / t);
                1 > e && (t *= e);
                var e = u(window).width(), r = Math.max(n, u(document).height()), t = u(window).scrollTop() + Math.round((n - (t + 2 * this.fullscreenMargin + this.fullscreenBarH)) / 2);
                u(".html5gallery-fullscreen-" + this.id).css({width: e + "px", height: r + "px"});
                u(".html5gallery-fullscreen-box-" + this.id).css({top: t + "px"})
            }, createSocialMediaFullscreen: function() {
                u(".html5gallery-viral-fullscreen-" + this.id).empty();
                if (typeof this.elemArray[this.curElem] === "undefined")
                {
                    
                }
                else
                {   
                    var image_url= this.elemArray[this.curElem][1];
                    var a_image_url = image_url.split("/");

                    var main_image_url = a_image_url.join("-d-");
                    
                    var e = u("#base_url").val()+"socialPage/"+main_image_url; 
                    
                    
                    //this.options.socialurlforeach && (e += (0 > window.location.href.indexOf("?") ? "?" : "&") + "html5galleryid=" + this.elemArray[this.curElem][0]);
                    if (this.options.showsocialmedia && this.options.showfacebooklike) {
                        var t = "<div style='display:block; float:left; width:110px; height:21px;'><iframe src='http://www.facebook.com/plugins/like.php?href=", t = this.options.facebooklikeurl && 0 < this.options.facebooklikeurl.length ? t + encodeURIComponent(this.options.facebooklikeurl) : t + e;
                        u(".html5gallery-viral-fullscreen-" + this.id).append(t + "&send=false&layout=button_count&width=300&show_faces=false&action=like&colorscheme=light&font&height=21' scrolling='no' frameborder='0' style='border:none;;overflow:hidden; width:90px; height:21px;' allowTransparency='true'></iframe></div>")
                    }
                    this.options.showsocialmedia && this.options.showtwitter && (t = "<div style='display:block; float:left; width:90px; height:21px;'><a href='https://twitter.com/share' class='twitter-share-button'", t = this.options.twitterurl && 0 < this.options.twitterurl.length ? t + (" data-url='" + this.options.twitterurl + "'") : t + (" data-url='" + e + "'"), this.options.twitterusername && 0 < this.options.twitterusername.length && (t += " data-via='" + this.options.twittervia + "' data-related='" + this.options.twitterusername + "'"), u(".html5gallery-viral-fullscreen-" + this.id).append(t + ">Tweet</a></div>"), u.getScript("http://platform.twitter.com/widgets.js"));
                    this.options.showsocialmedia && this.options.showgoogleplus && (t = "<div style='display:block; float:left; width:90px; height:21px;'><div class='g-plusone' data-size='medium'", t = this.options.googleplusurl && 0 < this.options.googleplusurl.length ? t + (" data-href='" + this.options.googleplusurl + "'") : t + (" data-href='" + e + "'"), u(".html5gallery-viral-fullscreen-" + this.id).append(t + "></div></div>"), u.getScript("https://apis.google.com/js/plusone.js"))
                }
                
                var e = window.location.href;
               
            }, goFullscreen: function() {
                this.hideimagetoolbox();
                clearTimeout(this.slideshowTimeout);
                u(".html5gallery-fullscreen-timer-" + this.id, this.$fullscreen).css({width: 0});
                clearInterval(this.slideTimer);
                this.slideTimerCount = 0;
                this.isFullscreen = !0;
                this.fullscreenInitial = 20;
                this.fullscreenMargin = 10;
                this.fullscreenBarH = 40;
                var e = this.elemArray[this.curElem][10], t = this.elemArray[this.curElem][11];
                this.fullscreenWidth = u(window).width() - 2 * this.fullscreenMargin;
                var n = window.innerHeight ? window.innerHeight : u(window).height();
                this.fullscreenHeight = n - 2 * this.fullscreenMargin - this.fullscreenBarH;
                var r = Math.min(this.fullscreenWidth / e, this.fullscreenHeight / t);
                1 > r && (e *= r, t *= r);
                var r = Math.max(u(window).width(), u(document).width()), i = Math.max(n, u(document).height()), n = u(window).scrollTop() + Math.round((n - (t + 2 * this.fullscreenMargin + this.fullscreenBarH)) / 2);
                this.$fullscreen = u("<div class='html5gallery-fullscreen-" + this.id + "' style='position:absolute;top:0px;left:0px;width:" + r + "px;height:" + i + "px;text-align:center;z-index:999;'><div class='html5gallery-fullscreen-overlay-" + this.id + "' style='display:block;position:absolute;top:0px;left:0px;width:100%;height:100%;background-color:#000000;opacity:0.9;filter:alpha(opacity=80);'></div><div class='html5gallery-fullscreen-box-" + this.id + "' style='display:block;overflow:hidden;position:relative;margin:0px auto;top:" + n + "px;width:" + this.fullscreenInitial + "px;height:" + this.fullscreenInitial + "px;background-color:#ffffff;'><div class='html5gallery-fullscreen-elem-" + this.id + "' style='display:block;position:absolute;overflow:hidden;width:" + e + "px;height:" + t + "px;left:" + this.fullscreenMargin + "px;top:" + this.fullscreenMargin + "px;'><div class='html5gallery-viral-fullscreen-" + this.id + "'style='display:block;position: absolute;float:left;height:auto; padding:3px;bottom:0px;left:0px;text-align:left;'></div><div class='html5gallery-fullscreen-title-" + this.id + "' style='display:block;position: absolute;background: none repeat scroll 0 0 rgba(102, 102, 102, 0.6);width:97%;font-size: 13px; line-height:20px;color:#FFFFFF; padding:5px;top:0px;left:0px;text-align:left;'></div><div class='html5gallery-fullscreen-timer-" + this.id + "' style='display:block; position:absolute; top:" + String(t - 4) + "px; left:0px; width:0px; height:4px; background-color:#666; filter:alpha(opacity=60); opacity:0.6;'></div></div><div class='html5gallery-fullscreen-bar-" + this.id + "' style='display:block;position:absolute;width:" + e + "px;height:" + this.fullscreenBarH + "px;left:" + this.fullscreenMargin + "px;top:" + String(t + this.fullscreenMargin) + "px;'><div class='html5gallery-fullscreen-close-" + this.id + "' style='display:block;position:relative;float:right;cursor:pointer;width:32px;height:32px;top:" + Math.round(this.fullscreenBarH - 32) + 'px;background-image:url("' + this.options.skinfolder + "lightbox_close.png\");'></div><div class='html5gallery-fullscreen-play-" + this.id + "' style='display:" + (this.isPaused && 1 < this.elemArray.length && 1 == this.elemArray[this.curElem][9] ? "block" : "none") + ";position:relative;float:right;cursor:pointer;width:32px;height:32px;top:" + Math.round(this.fullscreenBarH - 32) + 'px;background-image:url("' + this.options.skinfolder + "lightbox_play.png\");'></div><div class='html5gallery-fullscreen-pause-" + this.id + "' style='display:" + (this.isPaused || 1 >= this.elemArray.length || 1 != this.elemArray[this.curElem][9] ? "none" : "block") + ";position:relative;float:right;cursor:pointer;width:32px;height:32px;top:" + Math.round(this.fullscreenBarH - 32) + 'px;background-image:url("' + this.options.skinfolder + "lightbox_pause.png\");'></div></div><div class='html5gallery-fullscreen-next-" + this.id + "' style='display:none;position:absolute;cursor:pointer;width:48px;height:48px;right:" + this.fullscreenMargin + "px;top:" + Math.round(t / 2) + 'px;background-image:url("' + this.options.skinfolder + "lightbox_next.png\");'></div><div class='html5gallery-fullscreen-prev-" + this.id + "' style='display:none;position:absolute;cursor:pointer;width:48px;height:48px;left:" + this.fullscreenMargin + "px;top:" + Math.round(t / 2) + 'px;background-image:url("' + this.options.skinfolder + "lightbox_prev.png\");'></div></div></div>");
                this.$fullscreen.appendTo("body");
                this.createSocialMediaFullscreen();
                var s = this;
                u(window).scroll(function() {
                    var e = u(".html5gallery-fullscreen-box-" + s.id, s.$fullscreen), t = window.innerHeight ? window.innerHeight : u(window).height(), t = u(window).scrollTop() + Math.round((t - e.height()) / 2);
                    e.css({top: t})
                });
                var o = u(".html5gallery-elem-" + this.id, this.$gallery).empty().css({top: 0});
                u(".html5gallery-fullscreen-box-" + this.id, this.$fullscreen).animate({height: t + 2 * this.fullscreenMargin}, "slow", function() {
                    u(this).animate({width: e + 2 * s.fullscreenMargin}, "slow", function() {
                        u(this).animate({height: "+=" + s.fullscreenBarH}, "slow", function() {
                            u(".html5gallery-fullscreen-elem-" + s.id, s.$fullscreen).prepend(o);
                            s.slideRun(s.curElem)
                        })
                    })
                });
                u(".html5gallery-fullscreen-overlay-" + this.id, this.$fullscreen).click(function() {
                    s.goNormal()
                });
                u(".html5gallery-fullscreen-box-" + this.id, this.$fullscreen).hover(function() {
                    1 < s.elemArray.length && (u(".html5gallery-fullscreen-next-" + s.id, s.$fullscreen).fadeIn(), u(".html5gallery-fullscreen-prev-" + s.id, s.$fullscreen).fadeIn())
                }, function() {
                    u(".html5gallery-fullscreen-next-" + s.id, s.$fullscreen).fadeOut();
                    u(".html5gallery-fullscreen-prev-" + s.id, s.$fullscreen).fadeOut()
                });
                u(".html5gallery-fullscreen-box-" + this.id, this.$fullscreen).touchSwipe({preventWebBrowser: !0, swipeLeft: function() {
                        s.slideRun(-1)
                    }, swipeRight: function() {
                        s.slideRun(-2)
                    }});
                u(".html5gallery-fullscreen-close-" + this.id, this.$fullscreen).click(function() {
                    s.goNormal()
                });
                u(".html5gallery-fullscreen-next-" + this.id, this.$fullscreen).click(function() {
                    s.slideRun(-1);
                    s.createSocialMediaFullscreen()
                });
                u(".html5gallery-fullscreen-prev-" + this.id, this.$fullscreen).click(function() {
                    s.slideRun(-2);
                    s.createSocialMediaFullscreen()
                });
                u(".html5gallery-fullscreen-play-" + this.id, this.$fullscreen).click(function() {
                    u(".html5gallery-fullscreen-play-" + s.id, s.$fullscreen).hide();
                    u(".html5gallery-fullscreen-pause-" + s.id, s.$fullscreen).show();
                    s.isPaused = !1;
                    s.slideshowTimeout = setTimeout(function() {
                        s.slideRun(-1)
                    }, s.options.slideshowinterval);
                    u(".html5gallery-fullscreen-timer-" + s.id, s.$fullscreen).css({width: 0});
                    s.slideTimerCount = 0;
                    s.options.showtimer && (s.slideTimer = setInterval(function() {
                        s.showSlideTimer()
                    }, 50))
                });
                u(".html5gallery-fullscreen-pause-" + this.id, this.$fullscreen).click(function() {
                    u(".html5gallery-fullscreen-play-" + s.id, s.$fullscreen).show();
                    u(".html5gallery-fullscreen-pause-" + s.id, s.$fullscreen).hide();
                    s.isPaused = !0;
                    clearTimeout(s.slideshowTimeout);
                    u(".html5gallery-fullscreen-timer-" + s.id, s.$fullscreen).css({width: 0});
                    clearInterval(s.slideTimer);
                    s.slideTimerCount = 0
                });
                u(document).bind("keyup.html5gallery", function(e) {
                    27 == e.keyCode ? s.goNormal() : 39 == e.keyCode ? s.slideRun(-1) : 37 == e.keyCode && s.slideRun(-2)
                })
            }, calcIndex: function(e) {
                this.savedElem = this.curElem;
                -2 == e ? (this.nextElem = this.curElem, this.curElem = this.prevElem, this.prevElem = 0 > this.curElem - 1 ? this.elemArray.length - 1 : this.curElem - 1) : -1 == e ? (this.prevElem = this.curElem, this.curElem = this.nextElem, this.nextElem = this.curElem + 1 >= this.elemArray.length ? 0 : this.curElem + 1) : 0 <= e && (this.curElem = e, this.prevElem = 0 > this.curElem - 1 ? this.elemArray.length - 1 : this.curElem - 1, this.nextElem = this.curElem + 1 >= this.elemArray.length ? 0 : this.curElem + 1)
            }, showSlideTimer: function() {
                this.slideTimerCount++;
                this.isFullscreen ? u(".html5gallery-fullscreen-timer-" + this.id, this.$fullscreen).width(Math.round(50 * u(".html5gallery-fullscreen-elem-" + this.id, this.$fullscreen).width() * (this.slideTimerCount + 1) / this.options.slideshowinterval)) : u(".html5gallery-timer-" + this.id, this.$gallery).width(Math.round(50 * this.options.boxWidth * (this.slideTimerCount + 1) / this.options.slideshowinterval))
            }, slideRun: function(e, t) {
                clearTimeout(this.slideshowTimeout);
                this.isFullscreen ? u(".html5gallery-fullscreen-timer-" + this.id, this.$fullscreen).css({width: 0}) : u(".html5gallery-timer-" + this.id, this.$gallery).css({width: 0});
                clearInterval(this.slideTimer);
                this.slideTimerCount = 0;
                this.options.showcarousel && 0 <= this.curElem && u("#html5gallery-tn-" + this.id + "-" + this.curElem, this.$gallery).removeClass("html5gallery-tn-selected-" + this.id).addClass("html5gallery-tn-" + this.id);
                this.calcIndex(e);
                this.options.socialurlforeach && this.createSocialMedia();
                !this.isFullscreen && this.options.showcarousel && this.carouselHighlight(this.curElem);
                if (this.options.showtitle) {
                    var n = this.elemArray[this.curElem][7], r = this.elemArray[this.curElem][8];
                    this.options.shownumbering && (n = this.options.numberingformat.replace("%NUM", this.curElem + 1).replace("%TOTAL", this.elemArray.length) + " " + n);
                    this.isFullscreen ? u(".html5gallery-fullscreen-title-" + this.id, this.$fullscreen).html(n) : (n = "<div class='html5gallery-title-text-" + this.id + "'>" + n + "</div>", this.options.showdescription && r && (n += "<div class='html5gallery-description-text-" + this.id + "'>" + r + "</div>"), u(".html5gallery-title-" + this.id, this.$gallery).html(n));
                    if (n == "") {
                        u(".html5gallery-fullscreen-title-" + this.id, this.$fullscreen).hide()
                    } else {
                        u(".html5gallery-fullscreen-title-" + this.id, this.$fullscreen).show()
                    }
                }
                r = this.elemArray[this.curElem][9];
                if (!(0 > r)) {
                    !this.isFullscreen && t ? "always" == this.options.showimagetoolbox ? this.showimagetoolbox(r) : "image" == this.options.showimagetoolbox && 1 != r && this.hideimagetoolbox() : this.hideimagetoolbox();
                    this.onChange();
                    n = u(".html5gallery-elem-" + this.id, i);
                    n.find("iframe").each(function() {
                        u(this).attr("src", "")
                    });
                    var i = this.isFullscreen ? this.$fullscreen : this.$gallery, s = !1;
                    (5 == r || 6 == r || 7 == r || 8 == r || 9 == r || 10 == r) && !this.options.autoplayvideo && this.elemArray[this.curElem][12] ? (s = !0, this.showPoster()) : (u(".html5gallery-video-play-" + this.id, i).length && u(".html5gallery-video-play-" + this.id, i).remove(), 3 == r ? this.showMp3() : 1 == r ? this.showImage() : 5 == r || 6 == r || 7 == r || 8 == r ? this.showVideo(this.options.autoplayvideo) : 9 == r ? this.showYoutube(this.options.autoplayvideo) : 10 == r ? this.showVimeo(this.options.autoplayvideo) : 2 == r && this.showSWF());
                    this.prevElem in this.elemArray && 1 == this.elemArray[this.prevElem][9] && ((new Image).src = this.elemArray[this.prevElem][2]);
                    this.nextElem in this.elemArray && 1 == this.elemArray[this.nextElem][9] && ((new Image).src = this.elemArray[this.nextElem][2]);
                    this.prevElem in this.elemArray && !this.options.autoplayvideo && this.elemArray[this.prevElem][12] && ((new Image).src = this.elemArray[this.prevElem][12]);
                    this.nextElem in this.elemArray && !this.options.autoplayvideo && this.elemArray[this.nextElem][12] && ((new Image).src = this.elemArray[this.nextElem][12]);
                    this.curElem == this.elemArray.length - 1 && this.looptimes++;
                    var o = this;
                    if ((1 == r || s) && !this.isPaused && 1 < this.elemArray.length && (!this.options.loop || this.looptimes < this.options.loop))
                        this.slideshowTimeout = setTimeout(function() {
                            o.slideRun(-1)
                        }, this.options.slideshowinterval), this.isFullscreen ? u(".html5gallery-fullscreen-timer-" + this.id, this.$fullscreen).css({width: 0}) : u(".html5gallery-timer-" + this.id, this.$gallery).css({width: 0}), this.slideTimerCount = 0, this.options.showtimer && (this.slideTimer = setInterval(function() {
                            o.showSlideTimer()
                        }, 50));
                    this.elemArray[this.curElem][5] ? (n.css({cursor: "pointer"}), n.unbind("click").bind("click", function() {
                        o.elemArray[o.curElem][6] ? window.open(o.elemArray[o.curElem][5], o.elemArray[o.curElem][6]) : window.open(o.elemArray[o.curElem][5])
                    })) : (n.css({cursor: ""}), n.unbind("click"))
                }
            }, showImage: function() {
                var e = u(".html5gallery-elem-" + this.id, this.isFullscreen ? this.$fullscreen : this.$gallery);
                $preloading = "" === e.html() ? u("<div class='html5gallery-loading-center-" + this.id + "'></div>").appendTo(e) : u("<div class='html5gallery-loading-" + this.id + "'></div>").appendTo(e);
                var t = this, n = new Image;
                u(n).load(function() {
                    $preloading.remove();
                    t.elemArray[t.curElem][10] = this.width;
                    t.elemArray[t.curElem][11] = this.height;
                    var n;
                    t.isFullscreen ? (n = Math.min(t.fullscreenWidth / this.width, t.fullscreenHeight / this.height), n = 1 < n ? 1 : n) : n = "fill" == t.options.resizemode ? Math.max(t.options.width / this.width, t.options.height / this.height) : Math.min(t.options.width / this.width, t.options.height / this.height);
                    var r = Math.round(n * this.width);
                    n = Math.round(n * this.height);
                    var i = t.isFullscreen ? r : t.options.width, s = t.isFullscreen ? n : t.options.height, o = Math.round(i / 2 - r / 2), a = Math.round(s / 2 - n / 2);
                    t.isFullscreen && t.adjustFullscreen(i, s);
                    e.css({width: i, height: s});
                    r = u("<div class='html5gallery-elem-img-" + t.id + "' style='display:block; position:absolute; overflow:hidden; width:" + i + "px; height:" + s + "px; left:0px; margin-left:" + (t.options.slideshadow && !t.isFullscreen ? 4 : 0) + "px; top:0px; margin-top:" + (t.options.slideshadow && !t.isFullscreen ? 4 : 0) + "px;'><img class='html5gallery-elem-image-" + t.id + "' style='border:none; position:absolute; opacity:inherit; filter:inherit; padding:0px; margin:0px; left:" + o + "px; top:" + a + "px; max-width:none; max-height:none; width:" + r + "px; height:" + n + "px;' src='" + t.elemArray[t.curElem][2] + "' />" + t.options.watermarkcode + "</div>");
                    n = u(".html5gallery-elem-img-" + t.id, e);
                    n.length ? (e.prepend(r), e.html5boxTransition(t.id, n, r, {effect: t.options.effect, easing: t.options.easing, duration: t.options.duration, direction: t.curElem >= t.savedElem, slide: t.options.slide}, function() {
                    })) : e.html(r);
                    t.options.googleanalyticsaccount && window._gaq.push(["_trackEvent", "Image", "Play", t.elemArray[t.curElem][7]])
                });
                u(n).error(function() {
                    $preloading.remove();
                    t.isFullscreen && t.adjustFullscreen(t.options.width, t.options.height);
                    e.html("<div class='html5gallery-elem-error-" + t.id + "' style='display:block; position:absolute; overflow:hidden; text-align:center; width:" + t.options.width + "px; left:0px; top:" + Math.round(t.options.height / 2 - 10) + "px; margin:4px;'><div class='html5gallery-error-" + t.id + "'>The requested content cannot be found</div>");
                    t.options.googleanalyticsaccount && window._gaq.push(["_trackEvent", "Image", "Error", t.elemArray[t.curElem][7]])
                });
                n.src = this.elemArray[this.curElem][2]
            }, adjustFullscreen: function(e, t, n) {
                var r = window.innerHeight ? window.innerHeight : u(window).height(), r = u(window).scrollTop() + Math.round((r - (t + 2 * this.fullscreenMargin + this.fullscreenBarH)) / 2);
                n ? (u(".html5gallery-fullscreen-box-" + this.id, this.$fullscreen).css({width: e + 2 * this.fullscreenMargin, height: t + 2 * this.fullscreenMargin + this.fullscreenBarH, top: r}), u(".html5gallery-fullscreen-elem-" + this.id, this.$fullscreen).css({width: e, height: t}), u(".html5gallery-fullscreen-bar-" + this.id, this.$fullscreen).css({width: e, top: t + this.fullscreenMargin})) : (u(".html5gallery-fullscreen-box-" + this.id, this.$fullscreen).animate({width: e + 2 * this.fullscreenMargin, height: t + 2 * this.fullscreenMargin + this.fullscreenBarH, top: r}, "slow"), u(".html5gallery-fullscreen-elem-" + this.id, this.$fullscreen).animate({width: e, height: t}, "slow"), u(".html5gallery-fullscreen-bar-" + this.id, this.$fullscreen).animate({width: e, top: t + this.fullscreenMargin}, "slow"));
                u(".html5gallery-fullscreen-next-" + this.id, this.$fullscreen).css({top: Math.round(t / 2)});
                u(".html5gallery-fullscreen-prev-" + this.id, this.$fullscreen).css({top: Math.round(t / 2)});
                u(".html5gallery-fullscreen-play-" + this.id, this.$fullscreen).css("display", this.isPaused && 1 < this.elemArray.length && 1 == this.elemArray[this.curElem][9] ? "block" : "none");
                u(".html5gallery-fullscreen-pause-" + this.id, this.$fullscreen).css("display", this.isPaused || 1 >= this.elemArray.length || 1 != this.elemArray[this.curElem][9] ? "none" : "block");
                u(".html5gallery-elem-" + this.id, this.$fullscreen).css({width: e, height: t})
            }, showPoster: function() {
                var e = this.isFullscreen ? this.$fullscreen : this.$gallery, t = u(".html5gallery-elem-" + this.id, e);
                $preloading = "" === t.html() ? u("<div class='html5gallery-loading-center-" + this.id + "'></div>").appendTo(t) : u("<div class='html5gallery-loading-" + this.id + "'></div>").appendTo(t);
                var n = this, r = this.elemArray[this.curElem][10], i = this.elemArray[this.curElem][11], s = new Image;
                u(s).load(function() {
                    $preloading.remove();
                    var s, o, a;
                    n.isFullscreen ? (s = Math.max(r / this.width, i / this.height), s = 1 < s ? 1 : s, o = r, a = i) : (s = "fill" == n.options.resizemode ? Math.max(n.options.width / this.width, n.options.height / this.height) : Math.min(n.options.width / this.width, n.options.height / this.height), o = n.options.width, a = n.options.height);
                    var f = Math.round(s * this.width);
                    s = Math.round(s * this.height);
                    var l = Math.round(o / 2 - f / 2), h = Math.round(a / 2 - s / 2);
                    n.isFullscreen && n.adjustFullscreen(o, a);
                    t.css({width: o, height: a});
                    o = u("<div class='html5gallery-elem-img-" + n.id + "' style='display:block; position:absolute; overflow:hidden; width:" + o + "px; height:" + a + "px; left:0px; margin-left:" + (n.options.slideshadow && !n.isFullscreen ? 4 : 0) + "px; top:0px; margin-top:" + (n.options.slideshadow && !n.isFullscreen ? 4 : 0) + "px;'><img class='html5gallery-elem-image-" + n.id + "' style='border:none; position:absolute; opacity:inherit; filter:inherit; padding:0px; margin:0px; left:" + l + "px; top:" + h + "px; max-width:none; max-height:none; width:" + f + "px; height:" + s + "px;' src='" + n.elemArray[n.curElem][12] + "' />" + n.options.watermarkcode + "</div>");
                    a = u(".html5gallery-elem-img-" + n.id, t);
                    a.length ? (t.prepend(o), t.html5boxTransition(n.id, a, o, {effect: n.options.effect, easing: n.options.easing, duration: n.options.duration, direction: n.curElem >= n.savedElem, slide: n.options.slide}, function() {
                    })) : t.html(o);
                    u(".html5gallery-video-play-" + n.id, e).length || u("<div class='html5gallery-video-play-" + n.id + "' style='position:absolute;display:block;cursor:pointer;top:0px;left:0px;width:100%;height:100%;background:url(\"" + n.options.skinfolder + "playvideo_64.png\") no-repeat center center;'></div>").appendTo(t).unbind("click").click(function() {
                        u(this).remove();
                        clearTimeout(n.slideshowTimeout);
                        u(".html5gallery-timer-" + n.id, n.$gallery).css({width: 0});
                        clearInterval(n.slideTimer);
                        n.slideTimerCount = 0;
                        var e = n.elemArray[n.curElem][9];
                        5 == e || 6 == e || 7 == e || 8 == e ? n.showVideo(!0) : 9 == e ? n.showYoutube(!0) : 10 == e && n.showVimeo(!0)
                    })
                });
                u(s).error(function() {
                    $preloading.remove();
                    n.isFullscreen && n.adjustFullscreen(n.options.width, n.options.height);
                    t.html("<div class='html5gallery-elem-error-" + n.id + "' style='display:block; position:absolute; overflow:hidden; text-align:center; width:" + n.options.width + "px; left:0px; top:" + Math.round(n.options.height / 2 - 10) + "px; margin:4px;'><div class='html5gallery-error-" + n.id + "'>The requested content cannot be found</div>");
                    n.options.googleanalyticsaccount && window._gaq.push(["_trackEvent", "Image", "Error", n.elemArray[n.curElem][7]])
                });
                s.src = this.elemArray[this.curElem][12]
            }, showMp3: function(e) {
                var t = this.isFullscreen ? this.$fullscreen : this.$gallery, n = this.elemArray[this.curElem][10], r = this.elemArray[this.curElem][11];
                this.isFullscreen ? this.adjustFullscreen(n, r) : (u(".html5gallery-elem-" + this.id, this.$gallery).css({width: this.options.width, height: this.options.height}), n = this.options.width, r = this.options.height);
                u(".html5gallery-elem-" + this.id, t).html("<div class='html5gallery-loading-center-" + this.id + "'></div><div class='html5gallery-elem-video-" + this.id + "' style='display:block;position:absolute;overflow:hidden;top:47px;left:" + (this.options.slideshadow && !this.isFullscreen ? 4 : 0) + "px;width:" + n + "px;height:" + r + "px;'></div>" + this.options.watermarkcode);
                g = this.elemArray[this.curElem][2];
                u(".html5gallery-elem-video-" + this.id, t).html('<object type="application/x-shockwave-flash" data="http://flash-mp3-player.net/medias/player_mp3_mini.swf" width="300" height="30"><param name="movie" value="http://flash-mp3-player.net/medias/player_mp3_mini.swf" /><param name="bgcolor" value="#000000" /><param name="FlashVars" value="mp3=' + g + '" /></object>')
            }, showVideo: function(e) {
                var t = this.isFullscreen ? this.$fullscreen : this.$gallery, n = this.elemArray[this.curElem][10], r = this.elemArray[this.curElem][11];
                this.isFullscreen ? this.adjustFullscreen(n, r) : (u(".html5gallery-elem-" + this.id, this.$gallery).css({width: this.options.width, height: this.options.height}), n = this.options.width, r = this.options.height);
                u(".html5gallery-elem-" + this.id, t).html("<div class='html5gallery-loading-center-" + this.id + "'></div><div class='html5gallery-elem-video-" + this.id + "' style='display:block;position:absolute;overflow:hidden;top:" + (this.options.slideshadow && !this.isFullscreen ? 4 : 0) + "px;left:" + (this.options.slideshadow && !this.isFullscreen ? 4 : 0) + "px;width:" + n + "px;height:" + r + "px;'></div>" + this.options.watermarkcode);
                var i = !1;
                if (this.options.isMobile)
                    i = !0;
                else if ((this.options.html5player || !this.options.flashInstalled) && this.options.html5VideoSupported)
                    if (!this.options.isFirefox && !this.options.isOpera || (this.options.isFirefox || this.options.isOpera) && (this.elemArray[this.curElem][3] || this.elemArray[this.curElem][4]))
                        i = !0;
                if (i) {
                    i = this.elemArray[this.curElem][2];
                    if (this.options.isFirefox || this.options.isOpera || !i)
                        i = this.elemArray[this.curElem][4] ? this.elemArray[this.curElem][4] : this.elemArray[this.curElem][3];
                    this.embedHTML5Video(u(".html5gallery-elem-video-" + this.id, t), n, r, i, e)
                } else
                    i = this.elemArray[this.curElem][2], "/" != i.charAt(0) && "http:" != i.substring(0, 5) && "https:" != i.substring(0, 6) && (i = this.options.htmlfolder + i), this.embedFlash(u(".html5gallery-elem-video-" + this.id, t), "100%", "100%", this.options.jsfolder + "html5boxplayer.swf", "transparent", {width: n, height: r, videofile: i, autoplay: e ? "1" : "0", errorcss: ".html5box-error" + this.options.errorcss, id: this.id});
                this.options.googleanalyticsaccount && window._gaq.push(["_trackEvent", "Video", "Play", this.elemArray[this.curElem][7]])
            }, showSWF: function() {
                var e = this.isFullscreen ? this.$fullscreen : this.$gallery, t = this.elemArray[this.curElem][10], n = this.elemArray[this.curElem][11];
                this.isFullscreen ? this.adjustFullscreen(t, n) : u(".html5gallery-elem-" + this.id, this.$gallery).css({width: this.options.width, height: this.options.height});
                var r = this.isFullscreen ? 0 : Math.round((this.options.height - n) / 2) + (this.options.slideshadow ? 4 : 0), i = this.isFullscreen ? 0 : Math.round((this.options.width - t) / 2) + (this.options.slideshadow ? 4 : 0);
                u(".html5gallery-elem-" + this.id, e).html("<div class='html5gallery-elem-flash-" + this.id + "' style='display:block;position:absolute;overflow:hidden;top:" + r + "px;left:" + i + "px;width:" + t + "px;height:" + n + "px;'></div>" + this.options.watermarkcode);
                this.embedFlash(u(".html5gallery-elem-flash-" + this.id, e), t, n, this.elemArray[this.curElem][2], "window", {});
                this.options.googleanalyticsaccount && window._gaq.push(["_trackEvent", "Flash", "Play", this.elemArray[this.curElem][7]])
            }, prepareYoutubeHref: function(e) {
                var t = "", n = e.match(/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/);
                n && n[7] && 11 == n[7].length && (t = n[7]);
                t = "http://www.youtube.com/embed/" + t;
                e = this.getYoutubeParams(e);
                var n = !0, r;
                for (r in e)
                    n ? (t += "?", n = !1) : t += "&", t += r + "=" + e[r];
                return t
            }, getYoutubeParams: function(e) {
                var t = {};
                if (0 > e.indexOf("?"))
                    return t;
                e = e.substring(e.indexOf("?") + 1).split("&");
                for (var n = 0; n < e.length; n++) {
                    var r = e[n].split("=");
                    r && 2 == r.length && "v" != r[0].toLowerCase() && (t[r[0].toLowerCase()] = r[1])
                }
                return t
            }, initYoutubeApi: function() {
                var e, t = !1;
                for (e = 0; e < this.elemArray.length; e++)
                    if (9 == this.elemArray[e][9]) {
                        t = !0;
                        break
                    }
                t && (e = document.createElement("script"), e.src = ("https:" == document.location.protocol ? "https" : "http") + "://www.youtube.com/iframe_api", t = document.getElementsByTagName("script")[0], t.parentNode.insertBefore(e, t))
            }, showYoutube: function(e) {
                var t = this.isFullscreen ? this.$fullscreen : this.$gallery, n = this.elemArray[this.curElem][10], r = this.elemArray[this.curElem][11];
                this.isFullscreen ? this.adjustFullscreen(n, r) : (u(".html5gallery-elem-" + this.id, this.$gallery).css({width: this.options.width, height: this.options.height}), n = this.options.width, r = this.options.height);
                var i = this.elemArray[this.curElem][2];
                u(".html5gallery-elem-" + this.id, t).html("<div class='html5gallery-loading-center-" + this.id + "'></div><div id='html5gallery-elem-video-" + this.id + "' style='display:block;position:absolute;overflow:hidden;top:" + (this.options.slideshadow && !this.isFullscreen ? 4 : 0) + "px;left:" + (this.options.slideshadow && !this.isFullscreen ? 4 : 0) + "px;width:" + n + "px;height:" + r + "px;'></div>" + this.options.watermarkcode);
                var s = this;
                if (!ASYouTubeIframeAPIReady && (ASYouTubeTimeout += 100, 3e3 > ASYouTubeTimeout)) {
                    setTimeout(function() {
                        s.showYoutube(e)
                    }, 100);
                    return
                }
                if (ASYouTubeIframeAPIReady && !this.options.isIOS && !this.options.isIE6 && !this.options.isIE7) {
                    t = this.elemArray[this.curElem][2].match(/(\?v=|\/\d\/|\/embed\/|\/v\/|\.be\/)([a-zA-Z0-9\-\_]+)/)[2];
                    i = null;
                    e && (i = function(e) {
                        e.target.playVideo()
                    });
                    var o = this.getYoutubeParams(this.elemArray[this.curElem][2]), o = u.extend({autoplay: e ? 1 : 0, rel: 0, wmode: "transparent"}, o);
                    new YT.Player("html5gallery-elem-video-" + this.id, {width: n, height: r, videoId: t, playerVars: o, events: {onReady: i, onStateChange: function(e) {
                                e.data == YT.PlayerState.ENDED && (s.onVideoEnd(), s.isPaused || s.slideRun(-1))
                            }}})
                } else
                    i = this.prepareYoutubeHref(i), e && (i = 0 > i.indexOf("?") ? i + "?autoplay=1" : i + "&autoplay=1"), i = 0 > i.indexOf("?") ? i + "?wmode=transparent&rel=0" : i + "&wmode=transparent&rel=0", u("#html5gallery-elem-video-" + this.id, t).html("<iframe width='" + n + "' height='" + r + "' src='" + i + "' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>");
                this.options.googleanalyticsaccount && window._gaq.push(["_trackEvent", "Video", "Play", this.elemArray[this.curElem][7]])
            }, showVimeo: function(e) {
                var t = this.isFullscreen ? this.$fullscreen : this.$gallery, n = this.elemArray[this.curElem][10], r = this.elemArray[this.curElem][11];
                this.isFullscreen ? this.adjustFullscreen(n, r) : (u(".html5gallery-elem-" + this.id, this.$gallery).css({width: this.options.width, height: this.options.height}), n = this.options.width, r = this.options.height);
                var i = this.elemArray[this.curElem][2];
                e && (i = 0 > i.indexOf("?") ? i + "?autoplay=1" : i + "&autoplay=1");
                u(".html5gallery-elem-" + this.id, t).html("<div class='html5gallery-loading-center-" + this.id + "'></div><div class='html5gallery-elem-video-" + this.id + "' style='display:block;position:absolute;overflow:hidden;top:" + (this.options.slideshadow && !this.isFullscreen ? 4 : 0) + "px;left:" + (this.options.slideshadow && !this.isFullscreen ? 4 : 0) + "px;width:" + n + "px;height:" + r + "px;'></div>" + this.options.watermarkcode);
                u(".html5gallery-elem-video-" + this.id, t).html("<iframe width='" + n + "' height='" + r + "' src='" + i + "' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>");
                this.options.googleanalyticsaccount && window._gaq.push(["_trackEvent", "Video", "Play", this.elemArray[this.curElem][7]])
            }, checkType: function(e) {
                return!e ? -1 : e.match(/\.(jpg|gif|png|bmp|jpeg)(.*)?$/i) ? 1 : e.match(/[^\.]\.(swf)\s*$/i) ? 2 : e.match(/[^\.]\.(mp3)\s*$/i) ? 3 : e.match(/[^\.]\.(pdf)\s*$/i) ? 4 : e.match(/\.(flv)(.*)?$/i) ? 5 : e.match(/\.(mp4|m4v)(.*)?$/i) ? 6 : e.match(/\.(ogv|ogg)(.*)?$/i) ? 7 : e.match(/\.(webm)(.*)?$/i) ? 8 : e.match(/\:\/\/.*(youtube\.com)/i) || e.match(/\:\/\/.*(youtu\.be)/i) ? 9 : e.match(/\:\/\/.*(vimeo\.com)/i) ? 10 : 0
            }, onChange: function() {
                if (this.options.onchange && window[this.options.onchange] && "function" == typeof window[this.options.onchange])
                    window[this.options.onchange](this.elemArray[this.curElem])
            }, onSlideshowOver: function() {
                if (this.options.onslideshowover && window[this.options.onslideshowover] && "function" == typeof window[this.options.onslideshowover])
                    window[this.options.onslideshowover](this.elemArray[this.curElem])
            }, onThumbOver: function(element) {
               
                if (this.options.onthumbover && window[this.options.onthumbover] && "function" == typeof window[this.options.onthumbover])
                    window[this.options.onthumbover](this.elemArray[element])
            },
            onThumbOut: function() {
               if (this.options.onthumbout && window[this.options.onthumbout] && "function" == typeof window[this.options.onthumbout])
                    window[this.options.onthumbout](this.elemArray[this.curElem])
            }, onVideoEnd: function() {
                if (this.options.onvideoend && window[this.options.onvideoend] && "function" == typeof window[this.options.onvideoend])
                    window[this.options.onvideoend](this.elemArray[this.curElem])
            }, embedHTML5Video: function(e, t, n, r, i) {
                e.html("<div style='position:absolute;display:block;width:100%;height:100%;'><video width='100%' height='100%'" + (i ? " autoplay='autoplay'" : "") + " controls='controls' ></div>");
                var s = this;
                this.options.isAndroid && (u("<div id='android-play-" + this.id + "' style='position:absolute;display:block;cursor:pointer;width:" + t + "px;height:" + n + 'px;background:url("' + this.options.skinfolder + "playvideo_64.png\") no-repeat center center;'></div>").appendTo(e).unbind("click").click(function() {
                    u(this).hide();
                    u("video", e)[0].play()
                }), u("video", e).unbind("play").bind("play", function() {
                    u("#android-play-" + s.id, e).hide()
                }));
                u("video", e).unbind("ended").bind("ended", function() {
                    s.onVideoEnd();
                    s.isPaused || s.slideRun(-1)
                });
                u("video", e).get(0).setAttribute("src", r)
            }, embedFlash: function(e, t, n, r, i, s) {
                if (this.options.flashInstalled) {
                    var o = {pluginspage: "http://www.adobe.com/go/getflashplayer", quality: "high", allowFullScreen: "true", allowScriptAccess: "always", type: "application/x-shockwave-flash"};
                    o.width = t;
                    o.height = n;
                    o.src = r;
                    o.wmode = i;
                    o.flashVars = u.param(s);
                    t = "";
                    for (var a in o)
                        t += a + "=" + o[a] + " ";
                    e.html("<embed " + t + "/>")
                } else
                    e.html("<div class='html5gallery-elem-error-" + this.id + "' style='display:block; position:absolute; text-align:center; width:" + this.options.width + "px; left:0px; top:" + Math.round(this.options.height / 2 - 10) + "px;'><div class='html5gallery-error-" + this.id + "'><div>The required Adobe Flash Player plugin is not installed</div><div style='display:block;position:relative;text-align:center;width:112px;height:33px;margin:0px auto;'><a href='http://www.adobe.com/go/getflashplayer'><img src='http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' width='112' height='33'></img></a></div></div>")
            }};
        this.each(function() {
            var r = u(this);
            t = t || {};
            for (var i in t)
                i.toLowerCase() !== i && (t[i.toLowerCase()] = t[i], delete t[i]);
            this.options = u.extend({}, t);
            var s = this;
            u.each(r.data(), function(e, t) {
                s.options[e.toLowerCase()] = t
            });
            "skin"in this.options && (this.options.skin = this.options.skin.toLowerCase());
            i = {skinfolder: "/gallery/skins/horizontal/", padding: 6, bgcolor: "#ffffff", bgimage: "bg.png", galleryshadow: !0, slideshadow: !1, showsocialmedia: !1, headerpos: "top", showdescription: !0, titleoverlay: !0, titleautohide: !0, titlecss: " {color:#ffffff; font-size:14px; font-family:Armata, sans-serif, Arial; overflow:hidden; text-align:left; padding:10px 0px 10px 10px; background:rgb(102, 102, 102) transparent; background: rgba(102, 102, 102, 0.6); filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; -ms-filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; }", titlecsslink: " a {color:#ffffff;}", descriptioncss: " {color:#ffffff; font-size:13px; font-family:Armata, sans-serif, Arial; overflow:hidden; text-align:left; padding:0px 0px 10px 10px; background:rgb(102, 102, 102) transparent; background: rgba(102, 102, 102, 0.6); filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; -ms-filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; }", descriptioncsslink: " a {color:#ffffff;}", showcarousel: !0, carouselmargin: 0, carouselbgtransparent: !1, carouselbgcolorstart: "#494f54", carouselbgcolorend: "#292c31", carouseltopborder: "#666666", carouselbottomborder: "#111111", thumbwidth: 64, thumbheight: 48, thumbgap: 4, thumbmargin: 6, thumbunselectedimagebordercolor: "", thumbimageborder: 1, thumbimagebordercolor: "#ffffff", thumbshowplayonvideo: !0, thumbshadow: !1, thumbopacity: .8};
            var o = {padding: 12, skinfolder: "/gallery/skins/light/", bgcolor: "", bgimage: "bg.png", galleryshadow: !1, slideshadow: !0, showsocialmedia: !1, headerpos: "top", showdescription: !0, titleoverlay: !0, titleautohide: !0, titlecss: " {color:#ffffff; font-size:14px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:normal; text-align:left; padding:10px 0px 10px 10px;  background:rgb(102, 102, 102) transparent; background: rgba(102, 102, 102, 0.6); filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; -ms-filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; }", titlecsslink: " a {color:#ffffff;}", descriptioncss: " {color:#ffffff; font-size:12px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:normal; text-align:left; padding:0px 0px 10px 10px;  background:rgb(102, 102, 102) transparent; background: rgba(102, 102, 102, 0.6); filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; -ms-filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; }", descriptioncsslink: " a {color:#ffffff;}", showcarousel: !0, carouselmargin: 10, carouselbgtransparent: !0, thumbwidth: 48, thumbheight: 48, thumbgap: 8, thumbmargin: 12, thumbunselectedimagebordercolor: "#fff", thumbimageborder: 2, thumbimagebordercolor: "#fff", thumbshowplayonvideo: !0, thumbshadow: !0, thumbopacity: .8}, f = {padding: 12, skinfolder: "skins/gallery/", bgcolor: "", bgimage: "bg.png", galleryshadow: !1, slideshadow: !0, showsocialmedia: !1, headerpos: "top", showdescription: !0, titleoverlay: !0, titleautohide: !0, titlecss: " {color:#ffffff; font-size:14px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:normal; text-align:left; padding:10px 0px 10px 10px;  background:rgb(102, 102, 102) transparent; background: rgba(102, 102, 102, 0.6); filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; -ms-filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; }", titlecsslink: " a {color:#ffffff;}", descriptioncss: " {color:#ffffff; font-size:12px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:normal; text-align:left; padding:0px 0px 10px 10px;  background:rgb(102, 102, 102) transparent; background: rgba(102, 102, 102, 0.6); filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; -ms-filter:'progid:DXImageTransform.Microsoft.gradient(startColorstr=#99666666, endColorstr=#99666666)'; }", descriptioncsslink: " a {color:#ffffff;}", showcarousel: !0, carouselmargin: 10, carouselbgtransparent: !0, thumbwidth: 120, thumbheight: 60, thumbgap: 8, thumbmargin: 12, thumbunselectedimagebordercolor: "#fff", thumbimageborder: 2, thumbimagebordercolor: "#fff", thumbshowplayonvideo: !0, thumbshadow: !0, thumbopacity: .8, thumbshowtitle: !0, thumbtitlecss: "{text-align:center; color:#000; font-size:12px; font-family:Armata,Arial,Helvetica,sans-serif; overflow:hidden; white-space:nowrap;}", thumbtitleheight: 18}, l = {skinfolder: "/gallery/skins/darkness/", padding: 12, bgcolor: "#444444", bgimage: "background.jpg", galleryshadow: !1, slideshadow: !1, headerpos: "bottom", showdescription: !1, titleoverlay: !1, titleautohide: !1, titlecss: " {color:#ffffff; font-size:16px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:normal; text-align:left; padding:10px 0px;}", titlecsslink: " a {color:#ffffff;}", descriptioncss: " {color:#ffffff; font-size:12px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:normal; text-align:left; padding:0px 0px 10px 0px;}", descriptioncsslink: " a {color:#ffffff;}", showcarousel: !0, carouselmargin: 8, carouselbgtransparent: !1, carouselbgcolorstart: "#494f54", carouselbgcolorend: "#292c31", carouseltopborder: "#666666", carouselbottomborder: "#111111", thumbwidth: 64, thumbheight: 48, thumbgap: 4, thumbmargin: 6, thumbunselectedimagebordercolor: "", thumbimageborder: 1, thumbimagebordercolor: "#cccccc", thumbshowplayonvideo: !0, thumbshadow: !1, thumbopacity: .8}, p = {skinfolder: "/gallery/skins/vertical/", padding: 12, bgcolor: "#444444", bgimage: "background.jpg", galleryshadow: !1, slideshadow: !1, headerpos: "bottom", showdescription: !1, titleoverlay: !1, titleautohide: !1, titlecss: " {color:#ffffff; font-size:16px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:normal; text-align:left; padding:10px 0px;}", titlecsslink: " a {color:#ffffff;}", descriptioncss: " {color:#ffffff; font-size:12px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:normal; text-align:left; padding:0px 0px 10px 0px;}", descriptioncsslink: " a {color:#ffffff;}", showcarousel: !0, carouselmargin: 8, carouselposition: "right", carouselbgtransparent: !1, carouselbgcolorstart: "#494f54", carouselbgcolorend: "#292c31", carouseltopborder: "#666666", carouselbottomborder: "#111111", carouselhighlightbgcolorstart: "#999999", carouselhighlightbgcolorend: "#666666", carouselhighlighttopborder: "#cccccc", carouselhighlightbottomborder: "#444444", carouselhighlightbgimage: "", thumbwidth: 148, thumbheight: 48, thumbgap: 2, thumbmargin: 6, thumbunselectedimagebordercolor: "", thumbimageborder: 1, thumbimagebordercolor: "#cccccc", thumbshowplayonvideo: !0, thumbshadow: !1, thumbopacity: .8, thumbshowimage: !0, thumbshowtitle: !0, thumbtitlecss: "{text-align:center; color:#ffffff; font-size:12px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:nowrap;}"}, m = {skin: "horizontal", googlefonts: "Armata", enabletouchswipe: !0, responsive: !1, responsivefullscreen: !1, screenquery: {}, src: "", xml: "", xmlnocache: !0, autoslide: !1, slideshowinterval: 6e3, random: !1, borderradius: 0, loop: 0, autoplayvideo: !0, html5player: !1, effect: "fade", easing: "easeOutCubic", duration: 1500, slide: {duration: 1e3, easing: "easeOutExpo"}, width: 480, height: 270, showtimer: !0, resizemode: "fill", showtitle: !0, titleheight: 45, errorcss: " {text-align:center; color:#ff0000; font-size:14px; font-family:Arial, sans-serif;}", titlefullscreencss: " {color:#333333; font-size:16px; font-family:Armata, sans-serif, Arial; overflow:hidden; white-space:normal; line-height:40px;}", titlefullscreencsslink: " a {color:#333333;}", shownumbering: !1, numberingformat: "%NUM / %TOTAL", googleanalyticsaccount: "", showsocialmedia: !0, socialheight: 30, socialurlforeach: !1, showfacebooklike: !0, facebooklikeurl: "", showtwitter: !0, twitterurl: "", twitterusername: "", twittervia: "html5box", showgoogleplus: !0, googleplusurl: "", showimagetoolbox: "always", imagetoolboxstyle: "side", showplaybutton: !0, showprevbutton: !0, shownextbutton: !0, showfullscreenbutton: !0, carouselbgtransparent: !0, carouselbgcolorstart: "#ffffff", carouselbgcolorend: "#ffffff", carouseltopborder: "#ffffff", carouselbottomborder: "#ffffff", carouselbgimage: "bg.png", carouseleasing: "easeOutCirc", version: "2.9", freeversion: !0, freemark: "", freelink: "", watermark: ""}, m = "vertical" == this.options.skin ? u.extend(m, p) : "light" == this.options.skin ? u.extend(m, o) : "gallery" == this.options.skin ? u.extend(m, f) : "horizontal" == this.options.skin ? u.extend(m, i) : "darkness" == this.options.skin ? u.extend(m, l) : u.extend(m, i);
            this.options = u.extend(m, this.options);
            this.options.htmlfolder = "http://www.champs21.com/";
            this.options.jsfolder = e;
            "/" != this.options.skinfolder.charAt(0) && "http:" != this.options.skinfolder.substring(0, 5) && "https:" != this.options.skinfolder.substring(0, 6) && (this.options.skinfolder = e + this.options.skinfolder);
            if (-1 != this.options.htmlfolder.indexOf("://champs21.com") || -1 != this.options.htmlfolder.indexOf("://www.champs21.com"))
                this.options.freeversion = !1;
            i = u(window).width();
            if (this.options.screenquery)
                for (var g in this.options.screenquery)
                    i <= this.options.screenquery[g].screenwidth && (this.options.screenquery[g].gallerywidth && (this.options.width = this.options.screenquery[g].gallerywidth), this.options.screenquery[g].galleryheight && (this.options.height = this.options.screenquery[g].galleryheight), this.options.screenquery[g].thumbwidth && (this.options.thumbwidth = this.options.screenquery[g].thumbwidth), this.options.screenquery[g].thumbheight && (this.options.thumbheight = this.options.screenquery[g].thumbheight));
            g = new n(r, this.options, a++);
            r.data("html5galleryobject", g);
            r.data("html5galleryid", a);
            html5GalleryObjects.addObject(g)
        });
        return this
    };
    jQuery(document).ready(function() {
        jQuery(".html5gallery").html5gallery()
    })
}
(function() {
    for (var e = document.getElementsByTagName("script"), t = "", n = 0; n < e.length; n++)
        e[n].src && e[n].src.match(/html5gallery\.js/i) && (t = e[n].src.substr(0, e[n].src.lastIndexOf("/") + 1));
    if ("undefined" == typeof jQuery || 1.6 > parseFloat(/^\d\.\d+/i.exec(jQuery.fn.jquery))) {
        var e = document.getElementsByTagName("head")[0], r = document.createElement("script");
        r.setAttribute("type", "text/javascript");
        r.readyState ? r.onreadystatechange = function() {
            if ("loaded" == r.readyState || "complete" == r.readyState)
                r.onreadystatechange = null, loadHtml5Gallery(t)
        } : r.onload = function() {
            loadHtml5Gallery(t)
        };
        r.setAttribute("src", t + "jquery.js");
        e.appendChild(r)
    } else
        loadHtml5Gallery(t)
})();
var html5GalleryObjects = new function() {
    this.objects = [];
    this.addObject = function(e) {
        this.objects.push(e)
    };
    this.loadNext = function(e) {
        this.objects[e].onVideoEnd();
        this.objects[e].isPaused || this.objects[e].slideRun(-1)
    };
    this.gotoSlide = function(e, t) {
        "undefined" === typeof t && (t = 0);
        this.objects[t] && this.objects[t].slideRun(e)
    }
};
if ("undefined" === typeof ASYouTubeIframeAPIReady)
    var ASYouTubeIframeAPIReady = !1, ASYouTubeTimeout = 0, onYouTubeIframeAPIReady = function() {
    ASYouTubeIframeAPIReady = !0
}