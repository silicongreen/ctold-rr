(function(e){e.fn.hoverIntent=function(t,n){var r={sensitivity:7,interval:100,timeout:0};r=e.extend(r,n?{over:t,out:n}:t);var i,s,o,u,a=function(e){i=e.pageX;s=e.pageY},f=function(t,n){n.hoverIntent_t=clearTimeout(n.hoverIntent_t);if(Math.abs(o-i)+Math.abs(u-s)<r.sensitivity){e(n).unbind("mousemove",a);n.hoverIntent_s=1;return r.over.apply(n,[t])}o=i;u=s;n.hoverIntent_t=setTimeout(function(){f(t,n)},r.interval)},l=function(e,t){t.hoverIntent_t=clearTimeout(t.hoverIntent_t);t.hoverIntent_s=0;return r.out.apply(t,[e])},c=function(t){var n=jQuery.extend({},t),i=this;i.hoverIntent_t&&(i.hoverIntent_t=clearTimeout(i.hoverIntent_t));if(t.type=="mouseenter"){o=n.pageX;u=n.pageY;e(i).bind("mousemove",a);i.hoverIntent_s!=1&&(i.hoverIntent_t=setTimeout(function(){f(n,i)},r.interval))}else{e(i).unbind("mousemove",a);i.hoverIntent_s==1&&(i.hoverIntent_t=setTimeout(function(){l(n,i)},r.timeout))}};return this.live("hover",c)}})(jQuery);