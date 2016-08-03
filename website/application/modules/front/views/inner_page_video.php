<?php
$s_ci_key = (isset($ci_key)) ? $ci_key : NULL;
?>
<?php
$b_checked_cache = FALSE;
if (( list($i_type_id_cache, $i_category_id_cache, $s_category_name_cache) = get_category_type(sanitize($ci_key))))
{
    $b_checked_cache = TRUE;
}

if ((isset($_GET['archive']) && strlen($_GET['archive']) != "0") || (isset($_GET['date']) && strlen($_GET['date']) != "0" && $_GET['date'] != date("Y-m-d")))
{
    $b_checked_cache = FALSE;
}
$CI = & get_instance();
$CI->load->driver('cache', array('adapter' => 'file'));
$cache_name = "INNER_CONTENT_CACHE_" . $i_category_id_cache . "_" . str_replace(":", "-", str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");

ob_start();

$widget = new Widget;

?>
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<div class="container" style="width: 77%;min-height:250px;">
    <div class="sports-inner-news yesPrint" style="padding: 5px 31px 0;">    
        <div style="float:left;">
            <a href="<?php echo create_link_url(sanitize($name)); ?>"><h1 class="<?php echo strtolower($name); ?> title noPrint f2" style="color:#93989C;">
                    <?php
                    if (isset($display_name) && $display_name != "")
                    {
                        echo $display_name;
                    }
                    else
                    {
                        echo $name;
                    }
                    ?>

                </h1></a>
        </div>
        <div style="float:right;" >
            <center>		
                <!-- News Detials headline above -->
                <?php
                if (sanitize($name) == "eca.")
                {
                    ?>
                    <!--<img src="<?php echo base_url('/upload/ads/banners/eca.jpg'); ?>" width="350"  >-->
                    <?php
                }
                else
                {
                    ?>
                    <!--<img src="<?php echo base_url('/upload/ads/banners/' . sanitize($name) . '.jpg'); ?>" width="350"  >-->

                <?php } ?>
                <?php
                //$adplace_helper = new Adplace;
                //$adplace_helper->printAds( 27, null, FALSE,"0",'details' );
                ?>

            </center>
        </div>
        <div style="clear:both;"></div>
        <?php $parent_category = $name; ?>
        <?php if ($has_categories) : ?>
            <div class="sub-categories f5" style="display:block;">
                <li class="<?php echo ( empty($a_category_ids) ) ? "selected" : ""; ?>">
                    <a href="<?php echo base_url(sanitize($parent_category)); ?>" class="<?php echo ( empty($a_category_ids) ) ? "selected" : ""; ?>" style="<?php echo ( empty($a_category_ids) ) ? "" : "color: #93989C;"; ?>">
                        All
                    </a>
                </li>
                <?php foreach ($obj_child_categories as $categories) : ?>
                    <li>
                        <a href="<?php echo base_url(sanitize($parent_category) . "/" . sanitize($categories->name)); ?>" class="<?php echo ( in_array($categories->id, $a_category_ids) ) ? "selected" : ""; ?>" style="<?php echo ( in_array($categories->id, $a_category_ids) ) ? "" : "color: #93989C;"; ?>">
                            <?php
                            if (isset($categories->display_name) && $categories->display_name != "")
                            {
                                echo $categories->display_name;
                            }
                            else
                            {
                                echo $categories->name;
                            }
                            ?>
                        </a>
                    </li>
                    <?php if (in_array($categories->id, $a_category_ids)) : ?>
                        <?php $name = $categories->name; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div style="clear: both;"></div>
    <?php $widget->run('postdatavideofeature', "featured", $s_category_ids, 'inner', true, 1, "index", 0, 9, 1); ?>
    <div style="clear: both; margin-bottom:20px;"></div> 
    <?php if (empty($a_category_ids)): ?> 
        <?php
        $CI->load->config("huffas");
        $category_config = $CI->config->config[sanitize($parent_category)];
        $ar_extra_config = $category_config['3rd-column'];
        ?>

        <div style="clear: both;"></div>
        <div class="middle">
            <div class="col-lg-12" style="margin-top:-11px;" >
                <div class="col-lg-12">
                    <div class="col-lg-12">                        
                        <div class="col-lg-12">
                            <?php if ($has_categories) :?>
                                <div class="f2">
                                    <ul id="web_category">

                                        <?php
                                        $i = 0;
                                        foreach ($obj_child_categories as $categories) :
                                            if ($categories->id == $ar_extra_config['category_id'])
                                            {
                                                continue;
                                            }
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(sanitize($parent_category) . "/" . sanitize($categories->name)); ?>"  style="font-size:20px;">
                                                    <?php
                                                    if (isset($categories->display_name) && $categories->display_name != "")
                                                    {
                                                        echo $categories->display_name;
                                                    }
                                                    else
                                                    {
                                                        echo $categories->name;
                                                    }
                                                    ?>
                                                </a>
												
                                            </li>
                                            <?php if (in_array($categories->id, $a_category_ids)) : ?>
                                                <?php $name = $categories->name; ?>
                                            <?php endif; ?>
											
											<?php
													$widget->run('postvideos', $categories->display_name, $categories->id, $ci_key);
												?>
                                            <?php
                                            $i++;
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
							<?php endif; ?>



                        </div>
                        
                    </div> 
                    
                </div>

                

            </div>
            
        </div>
<?php else:?>
	<div style="clear: both;"></div>
        <div class="middle">
            <div class="col-lg-12" style="margin-top:-11px;" >
                <div class="col-lg-12">
                    <div class="col-lg-12">                        
                        <div class="col-lg-12">
			<?php

					$widget->run('postvideos',$category_name, $s_category_ids, $ci_key);
			?>
			 </div>
                        
                    </div> 
                    
                </div>

                

            </div>
            
        </div>
<?php endif; ?>
</div> 
<style>
   .sub-categories {
   
    float: none;
    width: 100%;
}
#web_category li{margin:25px 0px;}
    .sub-categories li.button-white:after
    {
        padding: 0px !important;
    }
    .geme_type
    {
        font-weight: lighter;
        font-size:25px;
        margin-top: -4px;
        color:#95989D;

    }

    .middle
    {
        width:94%;
        margin:0 auto;
    }
    .sub-categories li.button-white:after
    {
        content:"" !important;
        padding-left: 0px !important;
    }
    li.selected-button a
    {
        color:white !important; 
    }

    li.selected-button
    {
        -moz-box-shadow:inset 0px -1px 0px 0px #ff0033;
        -webkit-box-shadow:inset 0px -1px 0px 0px #ff0033;
        box-shadow:inset 0px -1px 0px 0px #ff0033;
        background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ff0033), color-stop(1, #ff0033) );
        background:-moz-linear-gradient( center top, #ff0033 5%, #ff0033 100% );
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0033', endColorstr='#ff0033');
        background-color:#ff0033;
        color:white !important;
    }
    li.selected-button:hover {
        background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ff0000), color-stop(1, #ff0033) );
        background:-moz-linear-gradient( center top, #ff0000 5%, #ff0033 100% );
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0000', endColorstr='#ff0033');
        background-color:#ff0000;
    }
    li.button-white a
    {
        color:#666666;
    }

    .button-white {
        -moz-box-shadow:inset 0px -1px 0px 0px #ffffff;
        -webkit-box-shadow:inset 0px -1px 0px 0px #ffffff;
        box-shadow:inset 0px -1px 0px 0px #ffffff;
        background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ffffff), color-stop(1, #ffffff) );
        background:-moz-linear-gradient( center top, #ffffff 5%, #ffffff 100% );
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#ffffff');
        background-color:#ffffff;
        -webkit-border-top-left-radius:5px;
        -moz-border-radius-topleft:5px;
        border-top-left-radius:5px;
        -webkit-border-top-right-radius:5px;
        -moz-border-radius-topright:5px;
        border-top-right-radius:5px;
        -webkit-border-bottom-right-radius:5px;
        -moz-border-radius-bottomright:5px;
        border-bottom-right-radius:5px;
        -webkit-border-bottom-left-radius:5px;
        -moz-border-radius-bottomleft:5px;
        border-bottom-left-radius:5px;
        text-indent:0;
        border:1px solid #dcdcdc;
        color:#666666;
        font-family:Arial;
        font-size:15px;
        font-weight:normal;
        height:30px;
        line-height:25px;
        padding: 0 10px;
        text-decoration:none;
        text-align:center;
        text-shadow:1px 1px 0px #ffffff;
        cursor: pointer;
    }
    .button-white:hover {
        background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #e9e9e9), color-stop(1, #f9f9f9) );
        background:-moz-linear-gradient( center top, #e9e9e9 5%, #f9f9f9 100% );
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#e9e9e9', endColorstr='#f9f9f9');
        background-color:#e9e9e9;
    }.button-white:active {
        position:relative;
        top:1px;
    }
	li.carrosel_news
	{
		height:auto;
	}
	#web_category
	{
		margin-left:0px;
	}
	div.featured ul
	{
		margin-left:0px;
	}
</style>
<script>
    $(document).ready(function() {
        if ($("#mobile-game-list").length > 0)
        {
            getDataMobile();
            getDataWeb();
            $(document).on("click", "#mobile_category li", function() {

                $("#mobile_category li").removeClass("selected-button");
                $(this).addClass("selected-button");
                $("#current_page_mobile").val(0)
                getDataMobile();
            });

            $(document).on("click", "#web_category li", function() {

                $("#web_category li").removeClass("selected-button");
                $(this).addClass("selected-button");
                $("#current_page_web").val(0);
                getDataWeb();
            });

            $(document).on("click", ".mobile-pagination .mobile-previous", function() {

                var currentpage = parseInt($("#current_page_mobile").val());
                if (currentpage > 0)
                {
                    $("#current_page_mobile").val(currentpage - 1);
                }
                getDataMobile();
            });
            $(document).on("click", ".mobile-pagination .mobile-next", function() {

                var currentpage = parseInt($("#current_page_mobile").val());

                $("#current_page_mobile").val(currentpage + 1);
                getDataMobile();
            });

            $(document).on("click", ".web-pagination .web-previous", function() {
                var currentpage = parseInt($("#current_page_web").val());
                if (currentpage > 0)
                {
                    $("#current_page_web").val(currentpage - 1);
                }

                getDataWeb();
            });
            $(document).on("click", ".web-pagination .web-next", function() {

                var currentpage = parseInt($("#current_page_web").val());
                $("#current_page_web").val(currentpage + 1);
                getDataWeb();
            });
        }

    });
    function getDataMobile()
    {
        var category_split = $("#mobile_category li.selected-button a").attr("id");
        var category_array = category_split.split("-");
        var current_page_mobile = $("#current_page_mobile").val();
        var category_id = category_array[1];
        var $mobileloading = '<div class="loading-box"><div class="loading mobile-loading"></div></div>';
        $("#mobile-game-list").html($mobileloading);
        $(".mobile-loading").show();

        $.ajax({
            type: "GET",
            url: $("#base_url").val() + 'front/ajax/getPostsGame/' + category_id + "/2/" + current_page_mobile,
            data: {},
            async: true,
            success: function(data) {

                $(".mobile-loading").hide();
                $("#mobile-game-list").html(data);
                $("#grid").masonry('reload');
                if (parseInt($("#has_next_mobile").val()) == 0)
                {
                    $(".mobile-pagination .mobile-next").hide();
                }
                else
                {
                    $(".mobile-pagination .mobile-next").show();
                }
                if (parseInt($("#has_prev_mobile").val()) == 0)
                {
                    $(".mobile-pagination .mobile-previous").hide();
                }
                else
                {
                    $(".mobile-pagination .mobile-previous").show();
                }
                $('.flex-wrapper .flexslider').flexslider({
                    slideshow: true,
                    animation: 'fade',
                    pauseOnHover: true,
                    animationSpeed: 400,
                    smoothHeight: true,
                    directionNav: true,
                    controlNav: false

                });
            }
        });


    }
    function getDataWeb()
    {
        var category_split = $("#web_category li.selected-button a").attr("id");
        var category_array = category_split.split("-");
        var current_page_web = $("#current_page_web").val();
        var category_id = category_array[1];
        var $webloading = '<div class="loading-box"><div class="loading web-loading"></div></div>';
        $("#web-game-list").html($webloading);
        $(".web-loading").show();
        $.ajax({
            type: "GET",
            url: $("#base_url").val() + 'front/ajax/getPostsGame/' + category_id + "/1/" + current_page_web,
            data: {},
            async: true,
            success: function(data) {

                $(".web-loading").hide();
                $("#web-game-list").html(data);
                $("#grid").masonry('reload');
                if (parseInt($("#has_next_web").val()) == 0)
                {
                    $(".web-pagination .web-next").hide();
                }
                else
                {
                    $(".web-pagination .web-next").show();
                }
                if (parseInt($("#has_prev_web").val()) == 0)
                {
                    $(".web-pagination .web-previous").hide();
                }
                else
                {
                    $(".web-pagination .web-previous").show();
                }
                $('.flex-wrapper .flexslider').flexslider({
                    slideshow: true,
                    animation: 'fade',
                    pauseOnHover: true,
                    animationSpeed: 400,
                    smoothHeight: true,
                    directionNav: true,
                    controlNav: false

                });

            }
        });


    }

</script>    