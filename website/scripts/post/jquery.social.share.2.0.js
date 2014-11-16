/*
 * DC jQuery Social Share Buttons
 * Copyright (c) 2011 Design Chemical
 * http://www.designchemical.com/blog/index.php/premium-jquery-plugins/jquery-social-share-buttons-plugin/
 * Version 2.0 (03-03-2012)
 *
 * Includes jQuery Easing v1.3
 * http://gsgd.co.uk/sandbox/jquery/easing/
 * Copyright (c) 2008 George McGinley Smith
 * jQuery Easing released under the BSD License.
 */
 
// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend( jQuery.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) {
		return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
	},
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});

(function($){

	$.fn.dcSocialShare = function(options) {

		var o = {
			buttons: 'twitter,facebook,plusone,linkedin,digg,stumbleupon,delicious,pinterest,buffer,print,email',
			size: 'vertical',
			txtPrint: 'Print',
			txtEmail: 'Email',
			twitterId: '',
			email: '',
			title: document.title,
			description: $('meta[name=description]').attr("content"),
			classWrapper: 'dcssb-float',
			classContent: 'dcssb-content',
			location: 'top',
			align: 'left',
			offsetLocation: 20,
			offsetAlign: 20,
			center: 0,
			speedFloat: 1500,
			speed: 600,
			floater: true,
                        fixed_location: false,
			autoClose: false,
			loadOpen: true,
			easing: 'easeOutQuint',
			classOpen: 'dc-open',
			classClose: 'dc-close',
			classToggle: 'dc-toggle'
		};

		var options = $.extend(o, options);
		 
		return this.each(function(options){
		
			var id = 'dcssb-'+$(this).index();
			$(this).addClass(o.classContent).wrap('<div id="'+id+'" class="'+o.classWrapper+' '+o.align+'" />');
			var $a = $('#'+id);
			var $c = $('.'+o.classContent,$a);
			var url = document.URL;
			var erl = encodeURI(url);
			var h_c = $c.outerHeight(true);
			var h_f = $a.outerHeight();
			
			var socialshare = {
				init: function(){
					socialshare.loading();
					if(o.floater){
						socialshare.floater();
						$(window).scroll(function(){
							socialshare.floater();
						});
					}
					if(o.autoClose){
						socialshare.autoClose();
					} else {
						$a.addClass('active');
					}
					socialshare.print();
					$('.pinItButton').click(function(){
						socialshare.pinit();
					});
					socialshare.tabClick();
				},
				loading: function(){
					var css = o.floater == true ? 'absolute' : (o.fixed_location == false) ? 'fixed' : '' ;
					$a.css({position: css, zIndex: 10000});
					if(o.location == 'top'){
						$a.css({top: o.offsetLocation});
					} else {
						$a.css({bottom: o.offsetLocation});
					}
					if(o.location == 'bottom'){
						$a.addClass(o.location);
					}
					if(o.center > 0){
						o.offsetAlign = '50%';
					}
					if(o.align == 'left'){
						$a.css({left: o.offsetAlign});
						if(o.center > 0){
							$a.css({marginLeft: -o.center+'px'});
						}
					} else {
						$a.css({right: o.offsetAlign});
						if(o.center > 0){
							$a.css({marginRight: -o.center+'px'});
						}
					}
					var ba = o.buttons.split(',');
					$.each(ba, function(index,value){
						socialshare.buttons(value);
					});
					if(o.autoClose){
						$a.hide();
					}
					if(o.loadOpen){
						socialshare.open();
					}
				},
				open: function(){
					$('.'+o.classWrapper).css({zIndex: 10000});
					$a.css({zIndex: 10001});
					if(o.location == 'bottom'){
						$a.animate({marginTop: '-'+h_c+'px'}, o.speed).slideDown(o.speed);
					} else {
						$a.slideDown(o.speed);
					}
					$a.addClass('active');
				},
				close: function(){
					$a.slideUp(o.speed, function(){
						$a.removeClass('active');
					});
				},
				floater: function(){
					var scroll = $(document).scrollTop();
					var x = o.location == 'bottom' ? $(window).height() - h_f : 0 ;
					var moveTo = o.offsetLocation + scroll + x;
					$a.stop().animate({top: moveTo}, o.speedFloat, o.easing);
				},
				tabClick: function(){
					$('.'+o.classOpen).click(function(e){
						if($a.not('active')){
							socialshare.open();
						}
						e.preventDefault();
					});
					$('.'+o.classClose).click(function(e){
						if($a.hasClass('active')){
							socialshare.close();
						}
						e.preventDefault();
					});
					$('.'+o.classToggle).click(function(e){
						if($a.hasClass('active')){
							socialshare.close();
						} else {
							socialshare.open();
						}
						e.preventDefault();
					});
				},
				autoClose: function(){
					$('body').mouseup(function(e){
						if($a.hasClass('active')){
							if(!$(e.target).parents('.'+o.classWrapper,$a).length){
								socialshare.close();
							}
						}
					});
				},
				print: function(){
					$('.link-print').click(function() {
						window.print();
						return false;
					});
				},
				email: function(){
					var domain = '//', name = '/';
					var email = o.email.replace(domain, '.').replace(name, '@');
					email = email.split('').reverse().join('');
					var mailto = 'mailto:'+email;
					return mailto;
				},
				pinit: function(){
					var e=document.createElement("script"); 
					e.setAttribute("type","text/javascript"); 
					e.setAttribute("charset","UTF-8"); 
					e.setAttribute("src","http://assets.pinterest.com/js/pinmarklet.js?r=" + Math.random()*99999999); 
					document.body.appendChild(e);
				},
				buttons: function(type){
					var b = '<div class="dcssb-btn dcssb-'+type+' size-'+o.size+'">';
					var btn = 'none';
					switch (type) {
						case 'twitter': 
						if(o.size == 'vertical'){
							btn = 'vertical';
						} else if(o.size == 'horizontal'){
							btn = 'horizontal';
						}
						b += '<a href="http://twitter.com/share" data-url="'+url+'" data-counturl="'+url+'" data-text="'+o.title+'" class="twitter-share-button" data-count="'+btn+'" data-via="'+o.twitterId+'"></a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
						break;
						
						case 'facebook':
						btn = 'standard';
						var w = 50;
						var h = 24;
						if(o.size == 'vertical'){
							btn = 'box_count';
							h = 62;
						} else if(o.size == 'horizontal'){
							btn = 'button_count';
							w = 80;
						}
						b += '<iframe src="http://www.facebook.com/plugins/like.php?app_id=&amp;href='+erl+'&amp;send=false&amp;layout='+btn+'&amp;width='+w+'&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height='+h+'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'+w+'px; height:'+h+'px;" allowTransparency="true"></iframe>';					
						break;
						
						case 'plusone': 
						btn = 'medium';
						var count = 'false';
						if(o.size == 'vertical'){
							btn = 'tall';
							count = 'true';
						} else if(o.size == 'horizontal'){
							btn = 'medium';
							count = 'true';
						}
						b += '<g:plusone size="'+btn+'" href="'+url+'" count="'+count+'"></g:plusone><script type="text/javascript">(function() {var po = document.createElement("script"); po.type = "text/javascript"; po.async = true; po.src = "https://apis.google.com/js/plusone.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s); })(); </script>';
						break;
						
						case 'linkedin': 
						if(o.size == 'vertical'){
							btn = 'top';
						} else if(o.size == 'horizontal'){
							btn = 'right';
						}
						b += '<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script><script type="in/share" data-url="'+url+'" data-counter="'+btn+'"></script>';
						break;
						
						case 'stumbleupon': 
						btn = '4';
						var w = '60px';
						var h = '24px';
						if(o.size == 'vertical'){
							btn = '5';
							w = '50px';
							h = '60px';
						} else if(o.size == 'horizontal'){
							btn = '1';
							var w = '80px';
						}
						b += '<iframe src="http://www.stumbleupon.com/badge/embed/'+btn+'/?url='+erl+'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'+w+'; height: '+h+';" allowTransparency="true"></iframe>';
						break;
						
						case 'digg': 
						btn = 'DiggIcon';
						if(o.size == 'vertical'){
							btn = 'DiggMedium';
						} else if(o.size == 'horizontal'){
							btn = 'DiggCompact';
						}
						b += '<script type="text/javascript">(function() {var s = document.createElement("SCRIPT"), s1 = document.getElementsByTagName("SCRIPT")[0]; s.type = "text/javascript"; s.async = true; s.src = "http://widgets.digg.com/buttons.js"; s1.parentNode.insertBefore(s, s1); })(); </script><a href="http://digg.com/submit?url='+erl+'&amp;title='+o.title+'" class="DiggThisButton '+btn+'"></a><span style="display: none;">'+o.description+'</span>';
						break;
						
						case 'delicious': 
						btn = 'wide';
						if(o.size == 'vertical'){
							btn = 'tall';
						} else if(o.size == 'horizontal'){
							btn = 'wide';
						}
						b += '<script type="text/javascript" src="http://delicious-button.googlecode.com/files/jquery.delicious-button-1.1.min.js"></script><a class="delicious-button" href="http://delicious.com/save"><!-- {url:"'+url+'",title:"'+o.title+'",button:"'+btn+'"} -->Delicious</a>';
						break;
						
						case 'pinterest': 
						var bc = 'size-small';
						if(o.size == 'vertical'){
							btn = 'vertical';
							bc = 'size-box';
						} else if(o.size == 'horizontal'){
							btn = 'horizontal';
						}
						var count = 0;
						jQuery.ajax({
							url: "http://api.pinterest.com/v1/urls/count.json?url="+url,
							dataType: 'jsonp',
							success: function(results){
								$('.pinterest-counter-count').html(results.count);
							}
						});
						b += '<div class="pinterest-counter-count '+bc+'">'+count+'</div><a href="#" class="pinItButton" title="Pin It on Pinterest">Pin it</a>';
						break;
						
						case 'buffer': 
						if(o.size == 'vertical'){
							btn = 'vertical';
						} else if(o.size == 'horizontal'){
							btn = 'horizontal';
						}
						b += '<a href="http://bufferapp.com/add" data-url="'+url+'" data-text="'+o.title+'" class="buffer-add-button" data-count="'+btn+'" data-via="'+o.twitterId+'">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>';
						break;
						
						case 'email': 
						mailto = socialshare.email();
						b += '<a href="'+mailto+'" class="link-email"><span class="icon"></span>'+o.txtEmail+'</a>';
						break;
						
						case 'print': 
						b += '<a href="#" class="link-print"><span class="icon"></span>'+o.txtPrint+'</a>';
						break;
						
					}
					b += '</div>';
					$c.append(b);
				}
			}
			socialshare.init();
		});
	};
})(jQuery);