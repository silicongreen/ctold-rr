<?php
$ci_key = (isset($ci_key)) ? $ci_key : 'index';
$widget = new Widget;
//$widget->run('menufooter');
?>
<a href="javascript:;" class="champs21_scrollToTop"><!--Scroll To Top--></a>

<div class="ym-wbox footer noPrint" style="padding:5px 35px 5px 15px;position:fixed;bottom:0px;display:none;">
    
    <div class="footerlink f5">
        <ul>
            <li><a href="<?php echo base_url('about-us'); ?>">About Us</a></li>
            <li>|</li>
            <li><a href="<?php echo base_url('terms'); ?>">Terms</a></li>
            <li>|</li>
            <li><a href="<?php echo base_url('privacy-policy'); ?>">Privacy Policy</a></li>
            <li>|</li>
            <li><a href="<?php echo base_url('copyright'); ?>">Copyright</a></li>
            <li>|</li>
            <li><a href="/contact-us">Contact Us</a></li>        
        </ul>
    </div>

    <div class="poweredby f5">    
        <p>Powered by <a href="http://www.team-creative.net" style="color:red;">Team Creative</a></p>
    </div>

</div>



<!--<script type="text/javascript">
//THIS IS FOR ONLY SNOW FALL EFFECT
//snowStorm.snowColor = '#fff'; // blue-ish snow!?
//snowStorm.autoStart = true;
//snowStorm.flakesMaxActive = 165;  // show more snow on screen at once
//snowStorm.useTwinkleEffect = true; // let the snow flicker in and out of view
//snowStorm.snowCharacter = '•';
</script>-->



<script type="text/javascript" src="<?php echo base_url('js/main-bottom.js'); ?>"></script>

<script type="text/javascript">
    (function () {
        $('.datepicker').datepicker();


        var po = document.createElement('script');
        po.type = 'text/javascript';
        po.async = true;
        po.src = 'https://apis.google.com/js/client:plusone.js?parsetags=explicit';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(po, s);
    })();


    $(document).ready(function () {
        var eventTrigger = false;
        $('body').click(function (event)
        {

            if (event.target.id != "menu-toggle")
            {

                if ($('#menu-toggle').prop('checked'))
                {
                    $('#menu-toggle').prop('checked', false);
                    eventTrigger = true;
                }
                else
                {
                    eventTrigger = false;
                }

            }
            else
            {
                if (eventTrigger == true)
                {
                    eventTrigger = false;
                    return false;
                }

            }
        });


        $("#s-auto").keyup(function ()
        {
            var loading = '<div class="display_box" align="left" style="float:left; clear:both; width:100%;"><span style="font-size:13px; color:black" class="name">Loading...</div></div>';
            var no_result = '<div class="display_box" align="left" style="float:left; clear:both; width:100%;"><span style="font-size:13px; color:black" class="name">No Result Found</div></div>'
            var inputSearch = $(this).val();
            var dataString = 'searchword=' + inputSearch;
            if (inputSearch != '' && inputSearch.length > 2)
            {
                $("#divResult").html(loading).show();
                $.ajax({
                    type: "POST",
                    url: $("#base_url").val() + "search_full_site",
                    data: dataString,
                    cache: false,
                    success: function (html)
                    {

                        if (html)
                        {
                            $("#divResult").html(html).show();
                        }
                        else
                        {
                            $("#divResult").html(no_result).show();
                        }
                    }
                });
            }
            else
            {
                $("#divResult").html("").hide();
            }
            return false;
        });









        $(window).scroll(function () {
            if ($(this).scrollTop() > 400) {
                $('.champs21_scrollToTop').fadeIn();
                $('.footer').fadeIn();
            } else {
                $('.champs21_scrollToTop').fadeOut();
                $('.footer').fadeOut();
            }
        });

        //Click event to scroll to top
        $('.champs21_scrollToTop').click(function () {
            $('html, body').animate({scrollTop: 0}, 800);
            return false;
        });
		
		//$('img').prop('src', function () { return this.src.replace('http://www.champs21.dev','http://www.champs21.com'); })
    });
</script>

<script>
    var isLoaded = false;
	window.fbAsyncInit = function () {
        FB.init({
            appId: '850059515022967',
            /* appId      : '164223470298622', */
            xfbml: false,
            version: 'v2.1'
        });
		isLoaded = true;
    };
	function checkIfLoaded() {
		if(isLoaded) console.log("LOADED!");
		else console.log("NOT YET!");

		return false;
	}
	
	// Load the SDK Asynchronously
	(function(d){
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement('script'); js.id = id; js.async = true;
		js.src = "//connect.facebook.net/en_US/all.js";
		ref.parentNode.insertBefore(js, ref);
	}(document));
    /*
	OLDER THAN 21.10.2015
	(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));*/
</script>
<style>
    .champs21_scrollToTop{
        width:40px; 
        height:40px;	
        background: whiteSmoke;	
        position:fixed;
        bottom:100px;
        right:4px;
        display:none;
        background: url('<?php echo base_url('styles/layouts/tdsfront/images/arrow_up.png'); ?>') no-repeat;
        background-size:40px;
    }
    .champs21_scrollToTop:hover{
        text-decoration:none;
        background: url('<?php echo base_url('styles/layouts/tdsfront/images/arrow_up_hover.png'); ?>') no-repeat;
        background-size:40px;
    }
    .container
    {
        min-height:650px !important;
    }
    a.summary_link:hover {
        color: #fb3c2d;
    }
    a.summary_link {
        color: #666;
    }
    @media screen and (max-width: 992px)
    {
        .col-sm-8 {
            width: 100%;
        }
    }
</style>
