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
            <div class="sub-categories f5">
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
    <?php $widget->run('postdatagamefeature', "featured", $s_category_ids, 'inner', true, 1, "index", 0, 9, 1); ?>
    <div style="clear: both; margin-bottom:20px;"></div> 
    <?php if (empty($a_category_ids)): ?> 
        <?php
        $CI->load->config("huffas");
        $category_config = $CI->config->config[sanitize($parent_category)];
        $ar_extra_config = $category_config['3rd-column'];
        ?>

        <div style="clear: both;"></div>
        <div class="middle">
            <div class="col-lg-9" style="margin-top:-11px;" >
                <div class="col-lg-12">
                    <div class="col-lg-12">
                        <div class="col-lg-2 f2 geme_type">
                            Web Games
                        </div>
                        <div class="col-lg-8">
                            <?php if ($has_categories) : ?>
                                <div class="sub-categories f2">
                                    <ul id="web_category">

                                        <?php
                                        $i = 0;
                                        foreach ($obj_child_categories as $categories) :
                                            if ($categories->id == $ar_extra_config['category_id'])
                                            {
                                                continue;
                                            }
                                            ?>
                                            <li class="button-white <?php if ($i == 0): ?>selected-button<?php endif; ?>">
                                                <a href="javascript:void(0);" id="category-<?php echo $categories->id; ?>" >
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
                                            $i++;
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                            <?php endif; ?>



                        </div>
                        <div class="col-lg-2 f2 geme_type">
                            <div class="web-pagination">
                                <a href="javascript:void(0);" class="web-previous">◀</a>
                                <a href="javascript:void(0);" class="web-next">▶</a>
                            </div>
                        </div>

                        <!--                        <div class="loading-box" style="">  
                                                    <div class="loading web-loading"></div>
                                                </div>-->
                    </div> 
                    <div id="web-game-list"  class="col-lg-12 game-list"  style="float: left; clear:both;">
                        <input type="hidden" id="has_next_web" name="has_next_web" value="0" />
                        <input type="hidden" id="has_prev_web" name="has_prev_web" value="0" />
                        <input type="hidden" id="current_page_web" name="current_page_web" value="0" />

                    </div>
                </div>

                <div class="col-lg-12" style="float: left; clear:both;">
                    <div class="col-lg-12" style="float: left; clear:both;">
                        <div class="col-lg-2 f2 geme_type">
                            Mobile Games
                        </div>
                        <div class="col-lg-8">
                            <?php if ($has_categories) : ?>
                                <div class="sub-categories f2">
                                    <ul id="mobile_category">
                                        <?php
                                        $i = 0;
                                        foreach ($obj_child_categories as $categories) :
                                            if ($categories->id == $ar_extra_config['category_id'])
                                            {
                                                continue;
                                            }
                                            ?>
                                            <li class="button-white <?php if ($i == 0): ?>selected-button<?php endif; ?>">
                                                <a href="javascript:void(0);" id="category-<?php echo $categories->id; ?>" >
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
                                            $i++;
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                            <?php endif; ?>



                        </div>
                        <div class="col-lg-2 f2 geme_type">
                            <div class="mobile-pagination">
                                <a href="javascript:void(0);" class="mobile-previous">◀</a>
                                <a href="javascript:void(0);" class="mobile-next">▶</a>
                            </div>
                        </div>
                    </div>
                    <div id="mobile-game-list" class="col-lg-12 game-list" style="float: left; clear:both;"> 
                        <input type="hidden" id="has_next_mobile" name="has_next_mobile" value="0" />
                        <input type="hidden" id="has_prev_mobile" name="has_prev_mobile" value="0" />
                        <input type="hidden" id="current_page_mobile" name="current_page_mobile" value="0" />
                    </div>
                    <!--                    <div class="loading-box" style="">  
                                                <div class="loading mobile-loading"></div>
                                        </div>-->
                </div>

            </div>
            <div class="col-lg-3">
                <?php
                $ar_post_news_additional = $this->post->gePostNews(0, "inner", "smaller", "DATE(tds_post.published_date)", $ar_extra_config['category_id'], $ar_extra_config['count'], 0, false, 0);
                $ar_3rd_column_extra_data = $ar_post_news_additional['data'];


                $extra_column_name = $ar_post_news_additional['data'][0]->name;
                $widget->run('thirdcolumngame', $ar_3rd_column_extra_data, $extra_column_name, $ar_extra_config);
                ?>

            </div>
        </div>
    <?php else: ?>

        <div class="inner-news">
            <div id="article-title" style="display: inline-block; width: 40%;">
                <h1 class="title noPrint f2">Articles for all</h1>
            </div>
            <div id="article-button" style="display: inline-block;
                 float: right;
                 margin-right: 31px;
                 margin-top: 23px;
                 text-align: right;
                 width: 55%;">
                <div class="button <?php echo (!$popular ) ? 'button-selected' : ''; ?>" onclick="location.href = '<?php echo ( strtolower($parent_category) == strtolower($name)) ? base_url(sanitize(strtolower($parent_category))) : base_url(sanitize(strtolower($parent_category)) . "/" . sanitize(strtolower($name))); ?>'">
                    Most Recent
                </div>
                <div class="button <?php echo ( $popular ) ? 'button-selected' : ''; ?>" onclick="location.href = '<?php echo ( strtolower($parent_category) == strtolower($name)) ? base_url(sanitize(strtolower($parent_category)) . "/popular") : base_url(sanitize(strtolower($parent_category)) . "/" . sanitize(strtolower($name)) . "/popular"); ?>'">
                    Most Popular
                </div>
                <div class="button hidden">
                    Set Preference
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>

        <?php $widget->run('postdata', $category_name, $s_category_ids, $ci_key); ?>
        <?php //$widget->run('postdata', "featured", $s_category_ids, 'inner', true, 2); ?>
        <?php //$widget->run('mediagallery', $ci_key, "inner", 21, date("Y-m-d"), false);
        ;
        ?>
        <?php //$widget->run('postdata', "featured", $s_category_ids, 'inner', true, 3); ?>
<?php endif; ?>
</div> 
<style>
   .sub-categories {
   
    float: none;
    width: 100%;
}

    .sub-categories li.button-white:after
    {
        padding: 0px !important;
    }
    .geme_type
    {
        font-weight: lighter;
        font-size:20px;
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