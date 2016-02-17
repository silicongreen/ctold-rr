// Just in case...
/*if (typeof console === "undefined") {
	this.console = {log: function() {} };
}*/
var alreadyScroll = false;
var mywindow = $(window);
var htmlbody = $('html,body');

// global variables
var startscrolltop = mywindow.scrollTop();
var windowwidth = mywindow.width();
var windowheight = mywindow.height();
var cronheight = windowheight;
var maxscrolltop = 1050;
var maxscrolltopIpad = 1050;
var cao = (windowwidth - 1200) / 2; // Global left offset
var hidestat = 0;
var ie = false;
var ie9 = false;
var ie7 = false;
var actscrollid = 1;

if ($.browser.msie && $.browser.version.substr(0, 1) < 9) {
	ie = true;
} else if ($.browser.msie && $.browser.version.substr(0, 1) < 10) {
	ie9 = true;
} else if ($.browser.msie && $.browser.version.substr(0, 1) < 8) {
	ie7 = true;
}

function getQueryParams(qs) {
    qs = qs.split("+").join(" ");
    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])]
            = decodeURIComponent(tokens[2]);
    }

    return params;
}
var $_GET = getQueryParams(document.location.search);

var ipad = false;
if (navigator.platform.indexOf("iPhone") != -1 || navigator.platform.indexOf("iPod") != -1 || navigator.userAgent.match(/iPad/i) != null) {
    ipad = true;
} else if ($_GET["simulateipad"] === '1') {
    ipad = true;
}

function getLocaleFromHash(url) {
    var match = url.match(/.*[?&]locale=([^&]+)(&|$)/);
    return(match ? match[1] : "");
}

$(document).ready(function () {
	// Cache some variables for stellar
	var links = $('.navigation').find('li');
	var slide = $('.slide');
	var button = $('.button');
        
        
        setTimeout(function(){
            var current_url = window.location.href;
            var hasvalue = getLocaleFromHash(current_url);
            if(hasvalue)
            {
                if(hasvalue == "about")
                {
                    $('#mainnav li.start').trigger('click');
                } 
                else if(hasvalue == "feature")
                {
                    $('#mainnav li.cron').trigger('click');
                }
                else if(hasvalue == "contact")
                {
                    $('#mainnav li.images').trigger('click');
                }
            }
            
        }, 1600)

	if (windowheight < 600) {
		$('#cronWrap').css('height', '600px');
		cronheight = 600;
		if (windowheight < 750) {
			$('#cronnav').css('top', 'auto').css('bottom', '10px');
		}
		maxscrolltop = 1050 + cronheight - windowheight;
	} else {
		$('#cronWrap').css('height', windowheight+'px');
	}

	if (ipad) {
		$('#startinfotext').html($('#ipadinfo .ipadinfoPad').html());
		$('#windowimg').css('left', '134px');
		maxscrolltopIpad = maxscrolltop;
		maxscrolltop = 1050;
	}
	$('#startinfotext').show();

	$('body').css('overflow', 'visible');
	$(window).scrollTop(0);
	startscrolltop = mywindow.scrollTop();
	//console.log('windowheight: ' + windowheight + ' / maxscrolltop: ' + maxscrolltop);

	var scrollspeed = 550;
	htmlbody.bind('mousewheel', function(event, delta) {
		event.preventDefault();
	});

	if (ie) {
		$('#secnav CUFON CUFONCANVAS').last().css('top', '-17px');
	}

	$(document).keydown(function(e){
		//console.log('Actscrollid: ' + actscrollid + ' / topmode: ' + topmode + ' / Menu: ' + actmmitem + ' NSP: ' + newscrollpos + ' / cw4scroll: ' + cw4scroll);
		if (e.keyCode === 38) {
			// up
			e.preventDefault();
			if (actmmitem !== 2 || newscrollpos <= 0) {
				$('#startWrap').trigger("mousewheel", 1);
			} else {
				$('#cronWrap').trigger("mousewheel", 1);
			}
		} else if (e.keyCode === 40) {
			// down
			e.preventDefault();
			if (actmmitem !== 2 || newscrollpos >= cw4scroll) {
				$('#startWrap').trigger("mousewheel", -1);
			} else {
				$('#cronWrap').trigger("mousewheel", -1);
			}
		}
	});

	// Ipad scrolling history
	$('#ww_button_left').on('click touchend', function () {
		//$('#cronWrap').trigger("mousewheel", 1);
		scrolling = true;
		var scrollto = $('#cronWrap').scrollLeft() - mspeed;
		if (scrollto < 0) {
			scrollto = 0;
		}

		$('#cronWrap').animate( {scrollLeft: scrollto}, 2000, function () {
			newscrollpos = $('#cronWrap').scrollLeft();
			scrolling = false;
		});

		$.each(cni, function(index, value) {
			var key = parseInt(index);

			if ((parseInt(key + mspeed) > (newscrollpos+300))  && (parseInt(key - mspeed) < (newscrollpos+300))) {
				cronnavitems.removeClass('act');
                                
                                var data_value = $('#' + value).data('value');
                                $(".text_layer").hide();
                                $("div[data-value='" + data_value + "_layer']").show();
				$('#' + value).addClass('act');
			}
		});

 		if (newscrollpos < cw4scroll) {
			$('#ww_button_down').hide();
			$('#ww_button_right').fadeIn(1000);
		}

		if (newscrollpos > 6500) {
			switchelements('bottom');
		} else {
			switchelements('top');
		}
	});
	$('#ww_button_right').on('click touchend', function () {
		//$('#cronWrap').trigger("mousewheel", -1);
		scrolling = true;
		var scrollto = $('#cronWrap').scrollLeft() + mspeed;
		if (scrollto < 0) {
			scrollto = 0;
		}

		$('#cronWrap').animate( {scrollLeft: scrollto}, 2000, function () {
			newscrollpos = $('#cronWrap').scrollLeft();
			scrolling = false;
		});

		$.each(cni, function(index, value) {
			var key = parseInt(index);
			//console.log('K: '+key+' / I: '+index+' / V: '+value+' / M: '+mspeed+' / N: '+newscrollpos);
			if ((parseInt(key + mspeed) > (newscrollpos+300)) && (parseInt(key - mspeed) < (newscrollpos+300))) {
				cronnavitems.removeClass('act');
                                
                                var data_value = $('#' + value).data('value');
                                $(".text_layer").hide();
                                $("div[data-value='" + data_value + "_layer']").show();
                                $('#' + value).addClass('act');
			}
		});

 		if (newscrollpos >= cw4scroll) {
			$('#ww_button_right').hide();
			$('#ww_button_down').fadeIn(1000);
		} else {
			$('#ww_button_down').hide();
			$('#ww_button_right').fadeIn(1000);
		}

		if (newscrollpos > 6500) {
			switchelements('bottom');
		} else {
			switchelements('top');
		}
	});
	$('#ww_button_down').on('click touchend', function () {
		$('#mainnav LI.images').trigger('click');
	});

	// Imprint
	$('#imprint_show').click(function () {
		$('#imprint').css('left', '50%');
		if (ie) {
			$('#imprint').show();
		} else {
			$('#imprint').fadeIn('slow');
		}
	});
	$('#imprint_close').click(function () {
		if (ie) {
			$('#imprint').hide();
			$('#imprint').css('left', '-10000px');
		} else {
			$('#imprint').fadeOut('slow', function () {
				$('#imprint').css('left', '-10000px');
			});
		}
	});

	// Mainnav
	var scrollpoints = [];
	scrollpoints["before"] = [];
	scrollpoints["before"]["pos"] = 0;
	scrollpoints["before"]["dir"] = 'top';
	scrollpoints["start"] = [];
	scrollpoints["start"]["pos"] = 420;
	scrollpoints["start"]["dir"] = 'top';
	scrollpoints["cron"] = [];
	scrollpoints["cron"]["pos"] = maxscrolltop;
	scrollpoints["cron"]["dir"] = 'top';
	scrollpoints["cronend"] = [];
	scrollpoints["cronend"]["pos"] = 8200;
	scrollpoints["cronend"]["dir"] = 'left';
	scrollpoints["images"] = [];
	scrollpoints["images"]["pos"] = cronheight;
	scrollpoints["images"]["dir"] = 'top';

	function switchelements(dir) {
		if (dir === 'bottom' && hidestat !== 1) {
			// Hide top, show bottom
			$('#imagesWrap, #thanks_endWrap').show();
			$('#beforeWrap, #startWrap').hide();
			$('#cronWrap').css('top', '0px');
			$('#imagesWrap').css('top', cronheight+'px');
			if (!ie && !ipad) {
				//$('#thanks_endWrap').css('top', (cronheight + 900)+'px');
			}
			$(window).scrollTop(maxscrolltop-1050);
			hidestat = 1;
		} else if (dir === 'top' && hidestat !== 0) {
			// Hide bottom, show top
			$('#imagesWrap, #thanks_endWrap').hide();
			$('#beforeWrap, #startWrap').show();
			$('#cronWrap').css('top', '1050px');
			$('#imagesWrap').css('top', (1050 + cronheight)+'px');
			if (!ie && !ipad) {
				//$('#thanks_endWrap').css('top', (2700 + cronheight)+'px');
			}
			if (ipad) {
				$(window).scrollTop(2100);
			} else {
				$(window).scrollTop(maxscrolltop);
			}
			hidestat = 0;
		}
	}

	$('#mainnav LI').click(function () {
		var scrolltoid = $(this).attr('class');
		var actscrolltop = $(window).scrollTop();
		
		if (hidestat === 1) {
			actscrolltop = actscrolltop + 1050;
		}
		var menuanitime = 1000;
		var menuhisanitime = 1000;
		if (actscrolltop < 1050) {
			actscrollid = 1;
		} else if (actscrolltop > 2000) {
			actscrollid = 3;
		} else {
			actscrollid = 2;
		}
		
		//console.log('Scrollto: '+scrolltoid+' / Actscrolltop: '+actscrolltop+' -> Scrollid: '+actscrollid);
		if (actscrollid === 1 && (scrolltoid === 'before' || scrolltoid === 'start' || scrolltoid === 'cron')) {
			$('html, body').animate({scrollTop: scrollpoints[scrolltoid]["pos"]}, menuanitime, function () {
				if (scrolltoid === 'start' || scrolltoid === 'before' || scrolltoid === 'images') {
					$('#cronnav').hide();
					$('#ww_button_left').hide();
					$('#ww_button_right').hide();
					$('#ww_button_down').hide();
				}
				if (scrolltoid === 'start' || scrolltoid === 'before') {
					topmode = 0
				}
			});
			
		} else if (actscrollid === 2 && (scrolltoid === 'before' || scrolltoid === 'start' || scrolltoid === 'cron')) {
			switchelements('top');
			scrollpoints["cron"]["pos"] = maxscrolltop;
			$('#cronWrap').animate({scrollLeft: 0}, menuhisanitime, function () {
				if (scrolltoid === 'start' || scrolltoid === 'before') {
					$('#cronnav').hide();
					$('#ww_button_left').hide();
					$('#ww_button_right').hide();
					$('#ww_button_down').hide();
					$('html, body').animate({scrollTop: scrollpoints[scrolltoid]["pos"]}, menuanitime, function () {
						topmode = 0
					});
				}
			});
		} else if (actscrollid === 3 && (scrolltoid === 'before' || scrolltoid === 'start' || scrolltoid === 'cron')) {
			$('html, body').scrollTop(0);
			scrollpoints["cron"]["pos"] = maxscrolltop;
			$('#cronWrap').scrollLeft(0);
			switchelements('top');
			newscrollpos = 0;
			if (scrolltoid === 'start' || scrolltoid === 'before') {
				$('html, body').scrollTop(scrollpoints[scrolltoid]["pos"]);
				$('#cronnav').hide();
				$('#ww_button_left').hide();
				$('#ww_button_right').hide();
				$('#ww_button_down').hide();
				topmode = 0
			}
		} else if (actscrollid === 1 && scrolltoid === 'images') {			
			$('html, body').scrollTop(scrollpoints["cron"]["pos"]);
			switchelements('bottom');
			$('#cronWrap').scrollLeft(scrollpoints["cronend"]["pos"]);
			$('html, body').scrollTop(scrollpoints[scrolltoid]["pos"]-80);
			scrollpoints["cron"]["pos"] = 0;
			$('#cronnav').hide();
			$('#ww_button_left').hide();
			$('#ww_button_right').hide();
			$('#ww_button_down').hide();
			newscrollpos = cw4scroll;
			$('#cronWrap').unbind('mousewheel');
		} else if (actscrollid === 2 && scrolltoid === 'images') {
			switchelements('bottom');
			
			$('#cronWrap').scrollLeft(scrollpoints["cronend"]["pos"]);
			$('html, body').scrollTop(scrollpoints[scrolltoid]["pos"]-80);
			scrollpoints["cron"]["pos"] = 0;
			$('#cronnav').hide();
			$('#ww_button_left').hide();
			$('#ww_button_right').hide();
			$('#ww_button_down').hide();
			newscrollpos = cw4scroll;
			$('#cronWrap').unbind('mousewheel');
		} else if (actscrollid === 3 && scrolltoid === 'images') {			
			$('html, body').animate({scrollTop: scrollpoints[scrolltoid]["pos"]-80});
			$('#cronnav').hide();
			$('#ww_button_left').hide();
			$('#ww_button_right').hide();
			$('#ww_button_down').hide();
			newscrollpos = cw4scroll;
		}

		if (ipad) {
			cronnavitems.removeClass('act');
                        var data_value = $('#cnt_75').data('value');
                        $(".text_layer").hide();
                        $("div[data-value='" + data_value + "_layer']").show();
			$('#cni_75').addClass('act');
		}

        var galabel = '';
        if (scrolltoid === 'images') {
            galabel = 'Weltweit';
        } else if (scrolltoid === 'before') {
            galabel = 'Erleben';
        } else if (scrolltoid === 'start') {
            galabel = 'Startseite';
        } else if (scrolltoid === 'cron') {
            galabel = 'Geschichte';
        }

        //_gaq.push(['_trackEvent', 'Mainnavi', 'click', galabel]);
	});

	// Secnav
	$('#sec1').click(function () {
		$('html, body').animate({scrollTop: scrollpoints["before"]["pos"]}, 500 );
		$(document).oneTime(600, 'openBox', function () {
			$('#box_105').trigger('click');
			$('#cc_147').trigger('click');
		});
	});
	$('#sec2').click(function () {
		$('html, body').animate({scrollTop: scrollpoints["cron"]["pos"]});
	});
	$('#sec3').click(function () {
		$('#mainnav LI.images').trigger('click');
	});
	$('#sec4').click(function () {
		$('html, body').animate({scrollTop: scrollpoints["before"]["pos"]}, 500 );
		$(document).oneTime(600, 'openBox', function () {
			$('#box_101').trigger('click');
			$('#cc_112').trigger('click');
		});
	});

	// Gewinnspiel
	$('#competition').click(function() {
		$('html, body').animate({scrollTop: scrollpoints["before"]["pos"]}, 500 );
		$(document).oneTime(600, 'openBox', function () {
			$('#box_102').trigger('click');
			$('#cc_139').trigger('click');
		});
	});

	// Set correct offsets for parallax
	var ho, ho_calculated = 0;
	slide.each(function() {
		ho = parseInt($(this).attr('data-stellar-horizontal-offset'));
		ho_calculated = ho + cao;
		$(this).attr('data-stellar-horizontal-offset', ho_calculated);
	});

	// History
	var htt = 500;
	var lo = false;
	$('.texticon, .text_l').mouseenter(function() {
            if (!ipad) {
                $(this).css('backgroundPosition', '0px -45px');
		if (ie) {
			$(this).find('.text_l').show();
		} else {
			$(this).find('.text_l').fadeIn(htt);
		}
            }
	}).mouseleave(function () {
            if (!ipad) {
		$(this).css('backgroundPosition', '0px 11px');
		if (ie) {
			$(this).find('.text_l').hide();
		} else {
			$(this).find('.text_l').fadeOut(htt);
		}
            }
	});
        $('.texticon, .text_l').on('touchend', function () {
            if (!$(this).hasClass('open')) {
                $(this).addClass('open');
                $(this).css('backgroundPosition', '0px -45px');
                $(this).find('.text_l').fadeIn(htt);
            } else {
                $(this).removeClass('open');
                $(this).css('backgroundPosition', '0px 11px');
                $(this).find('.text_l').fadeOut(htt);
            }
        });

	$('.videoicon').mouseenter(function() {
		if (!ipad) {
                    $(this).css('backgroundPosition', '0px -45px');
                }
	}).mouseleave(function () {
		if (!ipad) {
                    $(this).css('backgroundPosition', '0px 11px');
                }
	}).click(function () {
            if (!ipad) {
                if (ie) {
			$(this).find('.video_layer').show();
		} else {
			$(this).find('.video_layer').fadeIn(htt);
		}
		var vid = $(this).parent().attr('id').replace('hitem_', '');
		if ($("geze_video_" + vid + "_html5_api").length > 0) {
			_V_("geze_video_" + vid + "_html5_api").ready(function () {
				this.play();
			});
		}
                $(this).find('.video_layer_close').focus();
            }
	});
	$('.video_layer_close').click(function (event) {
            if (!ipad) {
                event.stopPropagation();

		var vid = $(this).parents('.hitem').attr('id').replace('hitem_', '');
		if ($("geze_video_" + vid).length > 0) {
			_V_("geze_video_" + vid + "_html5_api").ready(function () {
				this.pause();
			});
		} else if ($('#hitem_' + vid).find('EMBED').length > 0 && !ie && !ie9) {
			var ytplayer = document.getElementById("yt_" + vid);
			ytplayer.pauseVideo();
		}

		if (ie) {
			$(this).parent().hide();
		} else {
			$(this).parent().fadeOut(htt);
		}
            }
	});

        $('.videoicon').on('touchend', function () {
            $(this).find('.video_layer_close').hide();
            if (!$(this).hasClass('open')) {
                $(this).addClass('open');
                $(this).css('backgroundPosition', '0px -45px');
                $(this).find('.video_layer').fadeIn(htt);
            } else {
                $(this).removeClass('open');
                $(this).css('backgroundPosition', '0px 11px');
                $(this).find('.video_layer').fadeOut(htt);
            }
        });

	// History scrolling
	$('#cronWrap').scrollLeft(0);
	var newcronwidth = parseInt($('#cron').css('width').replace('px', '')) + cao + cao;
	$('#cron').css('width', newcronwidth+'px');
	var cw4scroll = $('#cron').width()-windowwidth;
	var lastscrollpos = 0;
	var lastscrolltop = $(window).scrollTop();
	var mspeed = 500;
	var topmode = 0;
	var cronnavitems = $('#cronnav').find('li');
	var cni = {};
	cronnavitems.each(function() {
		var iid = $(this).attr('id');
		var ioffset = parseInt(iid.replace('cni_', ''));
		cni[ioffset] = iid;
	});

	var newscrollpos = 0;
	var actmmitem = 1;
	var scrolling = false;
	$(document).everyTime(150, 'scrollTop', function () {
		var actscrolltop = $(window).scrollTop();

		// history
		//console.log('AST: ' + actscrolltop + ' / topmode: ' + topmode + ' / maxscrolltop: ' + maxscrolltop + ' / hidestat: ' + hidestat);
		/*if (ie) {
			$('#cursor').html('<!-- AST: ' + actscrolltop + ' / topmode: ' + topmode + ' / maxscrolltop: ' + maxscrolltop + ' / hidestat: ' + hidestat + '-->');
		}*/
		if ( (actscrolltop >= maxscrolltop && hidestat === 0) || (actscrolltop <= (maxscrolltop - 1050) && hidestat === 1) ) {
			topmode = 1;
			$('#cronnav').fadeIn(1000, function(){
                            if ( alreadyScroll == false )
                            {
                                var data_value = $('#cni_75').data('value');
                                $(".text_layer").hide();
                                $("div[data-value='" + data_value + "_layer']").show();
                                alreadyScroll = true;
                            }
                            already_scroll = true;
                        });
                        
			if (ipad) {
				if (newscrollpos !== 0) {
					$('#ww_button_left').fadeIn(1000);
				} else {
					$('#ww_button_left').fadeOut(1000);
				}
				if (hidestat === 0) {
					$('#ww_button_down').hide();
					$('#ww_button_right').fadeIn(1000);
				} else if (hidestat === 1 && newscrollpos >= cw4scroll) {
					$('#ww_button_right').hide();
					$('#ww_button_down').fadeIn(1000);
				} else if (hidestat === 1 && newscrollpos < cw4scroll) {
					$('#ww_button_down').hide();
					$('#ww_button_right').fadeIn(1000);
				}
			}

			$('#cronWrap').unbind('mousewheel');
			$('#cronWrap').bind('mousewheel', function(event, delta) {
				if (!scrolling) {
					if (delta < 0) {
						newscrollpos += mspeed;
					} else {
						if (newscrollpos > 0 && newscrollpos < 500) {
							newscrollpos = 500;
						}
						newscrollpos -= mspeed;
					}
					if (newscrollpos < 0) {
						newscrollpos = -mspeed;
					}

					//console.log('NSP: '+newscrollpos+' / LSP: '+lastscrollpos+' / cw4s: '+cw4scroll+' / delta: '+delta);
					if (newscrollpos >= 0 && newscrollpos <= cw4scroll) {
						event.preventDefault();
						scrolling = true;

						$('#cronWrap').animate( {scrollLeft: newscrollpos + 'px'}, 1500, function () {
							scrolling = false;
						});
						lastscrollpos = newscrollpos;

						$.each(cni, function(index, value) {
							var key = parseInt(index);

							if ((parseInt(key + mspeed) > lastscrollpos)  && (parseInt(key - mspeed) < lastscrollpos)) {
								cronnavitems.removeClass('act');
                                                                var data_value = $('#' + value).data('value');
                                                                $(".text_layer").hide();
                                                                $("div[data-value='" + data_value + "_layer']").show();
                                                                $('#' + value).addClass('act');
							}
						});

						if (newscrollpos === 2000) {
							if (hidestat === 0) {
								// Hide top, show bottom
								$('#imagesWrap, #thanks_endWrap').show();
								$('#beforeWrap, #startWrap').hide();
								$('#cronWrap').css('top', '0px');
								$('#imagesWrap').css('top', cronheight + 'px');
								$(window).scrollTop(maxscrolltop - 1050);
								hidestat = 1;
							} else {
								// Hide bottom, show top
								$('#imagesWrap, #thanks_endWrap').hide();
								$('#beforeWrap, #startWrap').show();
								$('#cronWrap').css('top', '1050px');
								$('#imagesWrap').css('top', (1050+cronheight)+'px');
								$(window).scrollTop(maxscrolltop);
								hidestat = 0;
							}
						} else if (newscrollpos < 1000 && hidestat === 1) {
							// Hide bottom, show top
							$('#imagesWrap, #thanks_endWrap').hide();
							$('#beforeWrap, #startWrap').show();
							$('#cronWrap').css('top', '1050px');
							$('#imagesWrap').css('top', (1050+cronheight)+'px');
							$(window).scrollTop(maxscrolltop);
							hidestat = 0;
						} else if (newscrollpos > 3000 && hidestat === 0) {
							// Hide top, show bottom
							$('#imagesWrap, #thanks_endWrap').show();
							$('#beforeWrap, #startWrap').hide();
							$('#cronWrap').css('top', '0px');
							$('#imagesWrap').css('top', cronheight+'px');
							$(window).scrollTop(maxscrolltop - 1050);
							hidestat = 1;
						}
					} else if (newscrollpos <= 0) {
						$('#cronnav').hide();
						$('#ww_button_left').hide();
						$('#ww_button_right').hide();
						$('#ww_button_down').hide();

						topmode = 0;
						$('#cronWrap').unbind('mousewheel');
						/*$('#cronWrap').bind('mousewheel', function(event, delta) {
							mousewheel(event, delta);
						});*/
					}
				} else {
					event.preventDefault();
				}
			});
		} else {
			if (ie) {
				$('#cronnav').hide();
				$('#ww_button_left').hide();
				$('#ww_button_right').hide();
				$('#ww_button_down').hide();
			}
			$('#cronWrap').unbind('mousewheel');
			$('#cronWrap').bind('mousewheel', function(event, delta) {
				mousewheel(event, delta);
			});
		}

		// Set navi active
		if (actmmitem !== 0 && actscrolltop < 100 && hidestat === 0) {
			$('#mainnav LI').removeClass('act');
			$('#mainnav .before').addClass('act');
			$('#cronnav').hide();
			$('#ww_button_left').hide();
			$('#ww_button_right').hide();
			$('#ww_button_down').hide();
			actmmitem = 0;
			if (typeof(Cufon) !== 'undefined') {
				Cufon.refresh('#mainnav LI');
			}
		} else if (actmmitem !== 1 && actscrolltop > 400 && actscrolltop < 950 && hidestat === 0) {
			$('#mainnav LI').removeClass('act');
			$('#mainnav .start').addClass('act');
			$('#cronnav').hide();
			$('#ww_button_left').hide();
			$('#ww_button_right').hide();
			$('#ww_button_down').hide();
			actmmitem = 1;
			if (typeof(Cufon) !== 'undefined') {
				Cufon.refresh('#mainnav LI');
			}

			if (box_open) {
				$('#clayer_' + box_open).find('.nav').find('SPAN').first().trigger('click');
				$('.clayer').fadeOut(box_atime);
				$('#beforeheader').fadeIn(box_atime);
				$('#cronnav').hide();
				$('#ww_button_left').hide();
				$('#ww_button_right').hide();
				$('#ww_button_down').hide();
				
			}
		} else if (actmmitem !== 2 && ((actscrolltop > 950 && hidestat === 0) || (actscrolltop === 0 && hidestat === 1))) {
			$('#mainnav LI').removeClass('act');
			$('#mainnav .cron').addClass('act');
			actmmitem = 2;
			if (typeof(Cufon) !== 'undefined') {
				Cufon.refresh('#mainnav LI');
			}
		} else if (actmmitem !== 3 && actscrolltop > 0 && actscrolltop > 550 && hidestat === 1) {
			
			$('#mainnav LI').removeClass('act');
			$('#mainnav .images').addClass('act');
			$('#cronnav').hide();
			$('#ww_button_left').hide();
			$('#ww_button_right').hide();
			$('#ww_button_down').hide();
			actmmitem = 3;
			if (typeof(Cufon) !== 'undefined') {
				Cufon.refresh('#mainnav LI');
			}
		}
	});
	cronnavitems.click(function() {
		scrolling = true;
		var id = parseInt($(this).attr('id').replace('cni_', ''));
		var scrollto = id - 360;
		if (scrollto < 0) {
			scrollto = 0;
		}

		$('#cronWrap').animate( {scrollLeft: scrollto}, 2000, function () {
			newscrollpos = $('#cronWrap').scrollLeft();
			scrolling = false;
		});
		cronnavitems.removeClass('act');
                
                var data_value = $(this).data('value');
                $(".text_layer").hide();
                $("div[data-value='" + data_value + "_layer']").show();
		$(this).addClass('act');

		if (id > 6500) {
			switchelements('bottom');
		} else {
			switchelements('top');
		}
	});

	// Worldmap
	var actterscrollpos = 0;
	var actterretory = 0;
	function scrollter(iso) {
		var scrolltoleft = $('#ter_'+iso).css('left');
		actterscrollpos = scrolltoleft;
		actterretory = iso;

		if (ie) {
			$('.terretory .detail').hide();
			$('.terretory .title').show();
		} else {
			$('.terretory .detail').fadeOut('slow');
			$('.terretory .title').fadeIn('slow');
		}

		if (actterscrollpos < scrolltoleft) {
			$('#mapimgscroller .terretory').animate( {left: '+='+scrolltoleft}, 1000 );
		} else {
			$('#mapimgscroller .terretory').animate( {left: '-='+scrolltoleft}, 1000 );
		}

		// Menu
		$('#wp_nav LI').removeClass('act');
		$('#wp_nav #nav_'+iso).addClass('act');

		// Buttons
		if (parseInt(iso) === 0) {
			$('#button_left').fadeOut('slow');
		} else {
			$('#button_left').fadeIn('slow');
		}
		if (parseInt(iso) === 9) {
			$('#button_right').fadeOut('slow');
		} else {
			$('#button_right').fadeIn('slow');
		}

		var textleft = $('#wp_nav #nav_'+actterretory).prev('LI').find('.displaynone').html();
		$('#bl_text').html('<span>'+textleft+'</span>');
		var textright = $('#wp_nav #nav_'+actterretory).next('LI').find('.displaynone').html();
		$('#br_text').html('<span>'+textright+'</span>');
	}
	var textright = $('#wp_nav #nav_'+actterretory).next('LI').find('.displaynone').html();
	$('#br_text').html('<span>'+textright+'</span>');

	$('#wpimap AREA').hover(function () {
		if ($(this).hasClass('hov_19')) {
			$('#mapimghover').css('background-position', '-960px 0px');
			$('#lay_19').show();
		} else if ($(this).hasClass('hov_150')) {
			$('#mapimghover').css('background-position', '-1920px 0px');
			$('#lay_150').show();
		} else if ($(this).hasClass('hov_2')) {
			$('#mapimghover').css('background-position', '-2880px 0px');
			$('#lay_2').show();
		} else if ($(this).hasClass('hov_142')) {
			$('#mapimghover').css('background-position', '-3840px 0px');
			$('#lay_142').show();
		} else if ($(this).hasClass('hov_9')) {
			$('#mapimghover').css('background-position', '-4800px 0px');
			$('#lay_9').show();
		}
	}, function () {
		$('#mapimghover').css('background-position', '0px 0px');
		$('#ter_0 .text_layer').hide();
	}).click(function () {
		if (ie) {
			$('#ww_infolayer').hide();
		} else {
			$('#ww_infolayer').fadeOut('slow');
		}
		var iso = $(this).attr('class').replace('hov_', '');
		scrollter(iso);
	});

	$('#wp_nav LI').click(function() {
		if (ie) {
			$('#ww_infolayer').hide();
		} else {
			$('#ww_infolayer').fadeOut('slow');
		}
		var iso = $(this).attr('id').replace('nav_', '');
		scrollter(iso);
	});
	$('#button_left').click(function() {
		if (ie) {
			$('#ww_infolayer').hide();
		} else {
			$('#ww_infolayer').fadeOut('slow');
		}
		var iso = $('#wp_nav #nav_'+actterretory).prev('LI').attr('id').replace('nav_', '');
		scrollter(iso);
	});
	$('#button_right').click(function() {
		if (ie) {
			$('#ww_infolayer').hide();
		} else {
			$('#ww_infolayer').fadeOut('slow');
		}
		var iso = $('#wp_nav #nav_'+actterretory).next('LI').attr('id').replace('nav_', '');
		scrollter(iso);
	});

	$('.subcompany').mouseenter(function() {
		var scid = $(this).attr('id').replace('sc_', '');
		$('#scl_' + scid).show();
	}).mouseout(function () {
		$('.text_layer').hide();
	}).click(function () {
		var id = $(this).attr('id').replace('sc_', '');
		$(this).parent().find('.title').hide();
		$(this).parent().find('.detail').hide();
		if (ie) {
			$(this).parent().find('#detail_' + id).show().find('.acc_header').first().trigger('click');
		} else {
			$(this).parent().find('#detail_' + id).fadeIn('slow', function () {
				$(this).find('.acc_header').first().trigger('click');
			});
		}
                if (ipad) {
                    $('.text_layer').hide();
                }
	});

	$('.salelocation').mouseenter(function() {
		var slid = $(this).attr('id').replace('sl_', '');
		$('#sll_' + slid).show();
	}).mouseout(function () {
		$('.text_layer').hide();
	}).click(function () {
		$(this).parent().find('.title').hide();
		$(this).parent().find('.detail').hide();
		var scid = $(this).attr('data-ww-sc');
		var locid = $(this).attr('data-ww-loc');
		if ($(this).attr('data-ww')) {
			var terid = parseInt($(this).attr('data-ww').replace('ter_', ''));
			scrollter(terid);
			$(document).oneTime(1100, 'showdetail', function () {
				$('#ter_'+terid).find('.title').hide();
				if (ie) {
					$('#ter_'+terid).find('#detail_' + scid).show().find('#acc_' + locid).trigger('click');
				} else {
					$('#ter_'+terid).find('#detail_' + scid).fadeIn('slow', function () {
						$('#acc_' + locid).trigger('click');
					});
				}
			});
		} else {
			var slid = $(this).attr('id').replace('sl_', '');
			if (ie) {
				$(this).parent().find('#detail_' + scid).show();
			} else {
				$(this).parent().find('#detail_' + scid).fadeIn('slow');
			}
			$('#acc_' + slid).trigger('click');
		}
                if (ipad) {
                    $('.text_layer').hide();
                }
	});

	$('.close_detail').click(function () {
		if (ie) {
			$(this).parent().hide();
		} else {
			$(this).parent().fadeOut('slow');
		}
		$(this).parents('.terretory').find('.title').show();
	});

	var slidetime = 500;
	$('.acc_header').click(function () {
		if ($(this).next().is(':hidden')) {
			$('.acc_text').slideUp(slidetime);
			$(this).next().slideDown(slidetime);
			$(this).css('background-position', '295px 11px');
		} else {
			$(this).next().slideUp(slidetime);
			$(this).css('background-position', '295px -36px');
		}
	});

	$('.openlightbox').click(function () {
		var lbid = $(this).attr('id').replace('img_', '');
		if (ie) {
			$('#lb_' + lbid).show();
		} else {
			$('#lb_' + lbid).fadeIn('slow');
		}
	});
	$('.lightbox_close').click(function () {
		if (ie) {
			$(this).parents('.lightboxSoft').hide();
		} else {
			$(this).parents('.lightboxSoft').fadeOut('slow');
		}
	});
	$('.lightboxSoft').click(function () {
		if (ie) {
			$(this).hide();
		} else {
			$(this).fadeOut('slow');
		}
	});

	// Infolayer
	$('#ww_infolayer_close').click(function () {
		if (ie) {
			$('#ww_infolayer').hide();
		} else {
			$('#ww_infolayer').fadeOut('slow');
		}
	});
	/*var ww_info = $.cookie("geze_ww_info");
	if (!ww_info) {
		$('#wp_nav').waypoint(function () {
			if (ie) {
				$('#ww_infolayer').show();
			} else {
				$('#ww_infolayer').fadeIn('slow');
			}
			$.cookie("geze_ww_info", "true");
			ww_info = true;
		});
	}*/

	var text_layers = $('.terretory .repos');
	text_layers.each(function() {
		var tlheight = parseInt($(this).height());
		var newtop = parseInt($(this).css('top').replace('px', '')) - tlheight;
		$(this).css('top', newtop + 'px');
	});
	$('.text_layer').hide();

	// Counter
	var cact = 0;
	if (!ie) {
		$('#preloader .counter').show();
	}
	$('#preloader').everyTime(300, 'counter', function () {
		if (cact < 147) {
			var x = Math.round(Math.random() * 4) + 1;
			if (x !== 4 && x !== 5) {
				cact += x;
				$('#count').html(cact);
			}
		}
	});

	// Boxes
	var box_open = false;
	var box_atime = 1000;
	var box_ani = false;
	
	
	$('.clayer .boxclose').click(function () {
		$(this).parent().find('.nav').find('SPAN').first().trigger('click');

		var ytplayer = $(this).parents('.clayer').find('OBJECT').find('EMBED');
		if (ytplayer.length > 0 && ytplayer.attr('allowfullscreen') === true && !ie && !ie9) {
			var tempid = $(this).parents('.clayer').attr('id').replace('clayer_', '')
			ytplayer.attr('id', 'yte_' + tempid);
			var ytplayer2 = document.getElementById("yte_" + tempid);
			ytplayer2.pauseVideo();
		}

		$('.clayer').fadeOut(box_atime);
		$('#beforeheader').fadeIn(box_atime);
		$('#boxesBG').animate( {height: '-=430', top: '+=170'}, box_atime, function() {
			$('#boxesBG .hover_white').css('left', '-10000px');
			box_open = false;
		});
	});
	$('.clayer .nav SPAN').click(function() {
		if (!$(this).hasClass('act')) {
			var cidtoshow = $(this).attr('id').replace('cc_', '');

			var ytplayer = $(this).parents('.clayer').find('OBJECT').find('EMBED');
			if (ytplayer.length > 0 && !ie && !ie9) {
				var tempid = $(this).parents('.clayer').attr('id').replace('clayer_', '')
				ytplayer.attr('id', 'yte_' + tempid);
				ytplayer = document.getElementById("yte_" + tempid);
				ytplayer.pauseVideo();
			}

			$('#clayer_'+box_open+' .nav SPAN').removeClass('act');
			$(this).addClass('act');

			if (ie) {
				$('#clayer_'+box_open+' .clayercontents .clayercontent').hide();
				$('.clayer').oneTime(200, 'temp', function () {
					$('#clc_'+cidtoshow).fadeIn(500);
				});
			} else {
				$('#clayer_'+box_open+' .clayercontents .clayercontent').fadeOut(500);
				$('.clayer').oneTime(200, 'temp', function () {
					$('#clc_'+cidtoshow).fadeIn(500);
				});
			}
		}
	});
	$('P.internal_link').click(function () {
		var linkto = $(this).attr('data-site');
		if (linkto === 136) {
			// Imprint
			$('#imprint').css('left', '50%');
			if (ie) {
				$('#imprint').show();
			} else {
				$('#imprint').fadeIn('slow');
			}
		} else if ($('#clayer_'+linkto).length > 0) {
			// Box
			$('#box_'+linkto).trigger('click');
		} else if ($('#clc_'+linkto).length > 0) {
			// Content
			var boxid = $('#clc_'+linkto).parents('DIV.clayer').attr('id').replace('clayer_', '');
			if (boxid === box_open) {
				$('#cc_'+linkto).trigger('click');
			} else {
				$('#box_'+boxid).trigger('click');
				$('#cc_'+linkto).trigger('click');
			}
		}
	});

	var imgcont = $('.imagecontainer');
	var imgcontconf = [];
	var imgtime = 800;
        var imgslide = false;
	imgcont.each(function() {
		imgcontconf[$(this).attr('id')] = 1;
	});
	$('.arrow_left').click(function () {
            if (!imgslide) {
                imgslide = true;
		var icid = $(this).parent().attr('id');
		var act = imgcontconf[icid];

		$('#' + icid + ' .img_' + act).fadeOut(imgtime, function () {
			$('#' + icid + ' .img_' + (act - 1)).fadeIn(imgtime, function() {
                            imgslide = false;
                        });
		});
		$('#' + icid + ' .text').fadeOut(imgtime, function () {
			$('.descriptions').oneTime(100, 'temp', function () {
				$('#' + icid + ' .ic').html(act - 1);
				$('#' + icid + ' .text_' + (act - 1)).fadeIn(imgtime);
			});
		});

		if ((act - 1) === 1) {
			$(this).fadeOut();
			$('#' + icid + ' .arrow_right').fadeIn(imgtime);
		} else {
			$(this).fadeIn();
			$('#' + icid + ' .arrow_right').fadeIn(imgtime);
		}

		imgcontconf[icid]--;
            }
	});
	$('.arrow_right').click(function () {
            if (!imgslide) {
                imgslide = true;
		var icid = $(this).parent().attr('id');
		var act = imgcontconf[icid];

		$('#' + icid + ' .img_' + act).fadeOut(imgtime, function () {
			$('#' + icid + ' .img_' + (act + 1)).fadeIn(imgtime, function() {
                            imgslide = false;
                        });
		});
		$('#' + icid + ' .text').fadeOut(imgtime, function () {
			$('.descriptions').oneTime(100, 'temp', function () {
				$('#' + icid + ' .ic').html(act + 1);
				$('#' + icid + ' .text_' + (act + 1)).fadeIn(imgtime);
			});
		});

		if ((act + 1) === $('#' + icid + ' .image').length) {
			$(this).fadeOut(imgtime);
			$('#' + icid + ' .arrow_left').fadeIn(imgtime);
		} else {
			$(this).fadeIn();
			$('#' + icid + ' .arrow_left').fadeIn(imgtime);
		}

		imgcontconf[icid]++;
            }
	});

	// COMPETITION
	$('#opentandc').click(function () {
		if (ie) {
			$('#tandc').show();
		} else {
			$('#tandc').fadeIn('slow');
		}
	});
	$('#opendataprivacy').click(function () {
		if (ie) {
			$('#dataprivacy').show();
		} else {
			$('#dataprivacy').fadeIn('slow');
		}
	});
	$('.close').click(function () {
		if (ie) {
			$(this).parent().parent().hide();
		} else {
			$(this).parent().parent().fadeOut('slow');
		}
	});

	$('#competitionform #submit').click(function () {
		var first_name = $('input[name=first_name]');
		var last_name = $('input[name=last_name]');
		var email = $('input[name=email]');
		var city = $('input[name=city]');
		var country = $('input[name=country]');
		var company = $('input[name=company]');
		var eligibilityrequirements = $('input[name=eligibilityrequirements]');
		var tandc = $('input[name=tandc]');
		var answer = $('input[name=answer]:checked');

		var data_string =	'compsend=1' +
							'&title=' + $("input:radio[name='title']:checked").val() +
							'&first_name=' + first_name.val() +
							'&last_name=' + last_name.val() +
							'&email=' + email.val() +
							'&city=' + city.val() +
							'&country=' + country.val() +
							'&company=' + company.val() +
							'&eligibilityrequirements=' + (eligibilityrequirements.prop('checked') ? '1' : '0') +
							'&tandc=' + (tandc.prop('checked') ? '1' : '0') +
							'&answer=' + answer.val();

		$('#competitionform INPUT').prop('disabled', true);
		$('#competitionform #loading_icon').show();

		// send request
		$.ajax({
			url: 'http://'+$(location).attr('host')+"/typo3conf/ext/px_competition/pi1/ajax_request.php",
			type: "GET",
			data: data_string,

			success: function (reqCode) {
				//console.log(reqCode);
				$('#error').hide();
				$('#error_double').hide();
				$('#competitionform INPUT').prop('disabled', false);
				$('#competitionform #loading_icon').hide();
				if (reqCode === '1') {
					if (ie) {
						$('#thanks').show();
					} else {
						$('#thanks').fadeIn('slow');
					}
				} else if (reqCode === 'double') {
					// user exists
					$('#error_double').fadeIn('slow');
				} else {
					// error
					$('#error').fadeIn('slow');
				}
			}
		});

		return false;
	});

	// PARALLAX
	if (!ie7) {
		if (!ie && !ipad) {
			$.stellar({
				hideDistantElements: false,
				horizontalScrolling: true,
				verticalScrolling: true
			});
		} else {
			//$('#thanks_endWrap').removeAttr('data-stellar-vertical-offset').removeAttr('data-stellar-horizontal-offset').removeAttr('data-stellar-ratio').css('top', '750px');
			//$('#imagesWrap').css('height', '2600px');
		}
		if (!ipad) {
			$('#cronWrap').stellar({
				hideDistantElements: false,
				horizontalScrolling: true,
				verticalScrolling: false
			});
		}
	}

	// RESIZING
	$(window).resize(function() {
		//window.location = window.location;
	});

	// WINDOW LOAD
	var current_os = navigator.platform;
	if (current_os.toLowerCase().indexOf("mac") >= 0) {
		$('#startWrap').oneTime(30000, 'windowload', function () {
			windowload();
		});
	}
	$('#startWrap').oneTime(90000, 'windowload_backup', function () {
		windowload();
	});
	
	$('#cni_up').click(function () {
		$('html, body').animate({scrollTop: 0}, 500 );
	});
	$('#cni_down').click(function () {
		$('#imagesWrap, #thanks_endWrap').show();
		$('#beforeWrap, #startWrap').hide();
		$('#cronWrap').css('top', '0px');
		$('#imagesWrap').css('top', cronheight+'px');		
		$(window).scrollTop(maxscrolltop-450);
		hidestat = 1;
	});
	
	
	
	
	
});

/*if (ie) {
	var scrollspeed = 100;
	var newscrollpostop = 900;
	var startscrollanitime = 1200;
} else {*/
	var scrollspeed = 550;
	var newscrollpostop = 900;
	var startscrollanitime = 1000;
//}
var scrolldefault = false;
function mousewheel(event, delta) {
	if ($(event.target).parents('.text').length === 0) {
		event.preventDefault();
		if (!scrolldefault) {
			scrolldefault = true;
			newscrollpostop = $(window).scrollTop();

			if (delta < 0) {
				newscrollpostop += scrollspeed;
			} else {
				newscrollpostop -= scrollspeed;
			}
			//console.log('MOUSEWHEEL: '+delta+' / new: '+newscrollpostop+' / W: '+$(window).scrollTop());

			htmlbody.animate( {scrollTop: newscrollpostop + 'px'}, startscrollanitime, function () {
				scrolldefault = false;
			});
		}
	}
}

// WINDOW LOAD
var scrollinfotime = 500;
function windowload() {
	// Preloader
	$('#startWrap').stopTime('windowload');
	$('#startWrap').stopTime('windowload_backup');
	$('#preloader').stopTime('counter');
	$('#count').html('150');
	$('#preloader').fadeOut(100);

	// Scrollinfo
	$('#scrollinfo').oneTime(500, 'scrollinfoIN', function () {
		$('#scrollinfo').fadeIn(scrollinfotime, function () {
			$('#scrollinfo').oneTime(1000, 'scrollinfoCursorIN', function () {
				$('#cursor').fadeIn(scrollinfotime, function() {
					var icount = 0;
					$('#scrollinfo').everyTime(400, 'cursorcbp', function () {
						icount++;
						if (icount === 1 || icount === 3) {
							ipos = -97;
						} else if (icount%2 === 0) {
							ipos = 0;
						} else if (icount === 5 || icount === 7) {
							ipos = -194;
						}

						$('#cursor').css('background-position', '0px '+ipos+'px');
						if (icount === 8) {
							$('#scrollinfo').stopTime('cursorcbp');
						}
					});
				});
			});
			$('#scrollinfo').oneTime(500, 'scrollinfoOUT', function () {
				$('#scrollinfo').stopTime('checkscrolling');
				$('#scrollinfo').fadeOut(scrollinfotime);
				$('#cursor').fadeOut(scrollinfotime);
			});
		});
	});

	// Scrollinfo
	$('#scrollinfo').everyTime(100, 'checkscrolling', function () {
		// Stop scrollinfo on scrolling
		var actscrolltop = $(window).scrollTop();
		if (startscrolltop !== actscrolltop) {
			$('#scrollinfo').stopTime('scrollinfoIN');
			$('#scrollinfo').stopTime('scrollinfoCursorIN');
			$('#scrollinfo').stopTime('cursorcbp');
			$('#scrollinfo').fadeOut(scrollinfotime);
			$('#cursor').fadeOut(scrollinfotime);

			$('#scrollinfo').stopTime('checkscrolling');
		}
	});

	// Ipdas Info
	if (ipad) {
		/*$(document).oneTime(1000, 'ipadinfobox1', function () {
			$('#ipadinfo').show();
			$(document).oneTime(6000, 'ipadinfobox2', function () {
				$('#ipadinfo').hide();
			});
		});*/
	}
	$('#ipadinfo').on('click touchend', function() {
		$('#ipadinfo').hide();
		$(document).stopTime('ipadinfobox2');
	});

	htmlbody.unbind('mousewheel');
	$('#startWrap, #beforeWrap, #cronWrap, #imagesWrap').bind('mousewheel', function(event, delta) {
		//mousewheel(event, delta);
	});

	$('#imagesWrap, #thanks_endWrap').hide();
}

$(window).load(function() {
	windowload();
});