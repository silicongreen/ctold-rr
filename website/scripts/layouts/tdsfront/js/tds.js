/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    var disqus_shortname = 'thedailystar001';//'<?php echo $discus_short_name; ?>'; // required: replace example with your forum shortname
    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function() {
        var s = document.createElement('script');
        s.async = true;
        s.type = 'text/javascript';
        s.src = 'http://thedailystar001.disqus.com/count.js';
        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
    }());
    
    (function() {
        var s = document.createElement('script');
        s.async = true;
        s.type = 'text/javascript';
        s.src = 'js/jquery.js';
        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
    }()); 
});