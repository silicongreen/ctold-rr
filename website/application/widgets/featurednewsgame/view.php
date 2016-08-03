<?php if ($obj_post_news && count($obj_post_news) > 0): ?>
    <script src="<?= base_url() ?>scripts/jquery/jquery.mCustomScrollbar.concat.min.js"></script>
    <link rel="stylesheet" href="<?= base_url() ?>styles/layouts/tdsfront/css/jquery.mCustomScrollbar.css">
    <div class='featured col-lg-12'>
        <h2 class="f2" style="color:#BFC1C0; margin:20px;" >Featured Games</h2>
        <ul  class="col-lg-8">
            <?php
            $i = 0;
            if ($obj_post_news)
                foreach ($obj_post_news as $news) :
                    ?>	
                    <?php $arCustomNews = getFormatedContentAll($news, 160); ?>
                    <?php $link_array = get_post_link_url($news); ?>        
                    <li id="carrosel_<?php echo $news->post_id; ?>" 
                        <?php if ($i > 0): ?> style="display:none;" <?php endif; ?>
                        class="carrosel_news carrosel_news_all">
                        <div class="carrosel-news">
                            <div class='carrosel-images'>

                                <a href="<?php echo $link_array['url']; ?>" target="<?php echo $link_array['target'] ?>" title="<?php echo $news->headline; ?>">
                                    <div class="post-thumb">
                                        <?php echo get_post_image_type_content($news, $arCustomNews, "width:309px; height:270px;"); ?>
                                    </div>
                                </a>    

                            </div>
                            <div class='carrosel-news-content'>
                                <div class="post-title">
                                    <h2 class="f2" style="font-size:33px;">
                                        <a href="<?php echo $link_array['url']; ?>" target="<?php echo $link_array['target'] ?>" title="<?php echo $news->headline; ?>">

                                            <?php if ($news->name != $news->headline) : ?>
                                                <span style="color: #ffffff;font-size:28px;"><?php echo $news->headline; ?></span><br/>
                                                <span style="color: #818489; font-size:17px;"><?php echo $news->name; ?></span>
                                            <?php else : ?>
                                                <span style="color: #ffffff;"><?php echo $news->headline; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </h2>
                                </div><!-- post-title --> 
                                <div class="akmanda-excerpt"> <?php echo $arCustomNews['content']; ?></div>
                                
                                    <div class="button_div">
                                        <a href="<?php echo $link_array['url']; ?>" target="<?php echo $link_array['target'] ?>" class="button-red" title="<?php echo $news->headline; ?>">
                                            Play Now
                                        </a>
                                    </div>
                            </div>
                        </div>                    
                    </li>               
                    <?php
                    $i++;
                endforeach;
            ?>
        </ul>  
        <div class="carrosel-left" id="clgame">
            <?php
            $i = 0;
            if ($obj_post_news)
                foreach ($obj_post_news as $news) :
                    ?>
                    <?php $arCustomNews = getFormatedContentAll($news, 300); ?>
                    <div class="game_thumb <?php if ($i > 0): ?> non-selected <?php endif; ?>" id="gamethumb_<?php echo $news->post_id; ?>" >
                        <?php if (strlen($news->lead_material) > 0) : ?>

                            <img src="<?php echo $arCustomNews['lead_material']; ?>" style="width:98%" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>">

                        <?php elseif (strlen(trim($arCustomNews['image'])) > 0) : ?>

                            <?php if ( count($arCustomNews['all_image']) == 1 ) : ?>
                        <img src="<?php echo $arCustomNews['image'];?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" style="width:98%">
                        <?php else : ?>
                        <div class="flex-wrapper">
                            <div id="slider" class="flexslider" style="border: 1px solid #Fff;">
                                <ul class="slides">
                                    <?php foreach( $arCustomNews['all_image'] as $image ): ?>
                                    <li>
                                        <img src="<?php echo $image;?>" alt="<?php echo $news->headline; ?>" style="width:98%;" />
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php endif; ?>
                    </div>    
                    <?php
                    $i++;
                endforeach;
            ?>
        </div>
        <div class="clearfix"></div>

    </div>
    <div style='clear: both;'></div>
    <style>
        .button_div
        {
            margin:20px;
        }
        .button-red {
            -moz-box-shadow:inset 0px -1px 0px 0px #f29c93;
            -webkit-box-shadow:inset 0px -1px 0px 0px #f29c93;
            box-shadow:inset 0px -1px 0px 0px #f29c93;
            background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #fe1a00), color-stop(1, #ce0100) );
            background:-moz-linear-gradient( center top, #fe1a00 5%, #ce0100 100% );
            filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fe1a00', endColorstr='#ce0100');
            background-color:#fe1a00;
            -webkit-border-top-left-radius:6px;
            -moz-border-radius-topleft:6px;
            border-top-left-radius:6px;
            -webkit-border-top-right-radius:6px;
            -moz-border-radius-topright:6px;
            border-top-right-radius:6px;
            -webkit-border-bottom-right-radius:6px;
            -moz-border-radius-bottomright:6px;
            border-bottom-right-radius:6px;
            -webkit-border-bottom-left-radius:6px;
            -moz-border-radius-bottomleft:6px;
            border-bottom-left-radius:6px;
            text-indent:0;
            border:1px solid #d83526;
            display:inline-block;
            color:#ffffff;
            font-family:Arial;
            font-size:15px;
            font-weight:bold;
            font-style:normal;
            height:30px;
            line-height:25px;
            width:100px;
            text-decoration:none;
            text-align:center;
            text-shadow:1px 1px 0px #b23e35;
        }
        .button-red:hover {
            background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ce0100), color-stop(1, #fe1a00) );
            background:-moz-linear-gradient( center top, #ce0100 5%, #fe1a00 100% );
            filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ce0100', endColorstr='#fe1a00');
            background-color:#ce0100;
            color:#ffffff !important;
        }.button-red:active {
            position:relative;
            top:1px;
        }
        .non-selected
        {
            opacity:0.5;
            filter:alpha(opacity=0.5);
        }
        .non-selected:hover
        {
            opacity:1;
            filter:alpha(opacity=1);
        }
        div.featured ul.col-lg-8 {
            height: 310px;
            width: 72% !important;
            left: 0px !important;
        }
        div.featured 
        {
            height: 374px !important;
        }
        .featured li.carrosel_news div.carrosel-news div.carrosel-news-content
        {
            background:#3D3D3B;
            margin-left: -4px;
            height: 270px;
        }
        .carrosel-news-content .akmanda-excerpt
        {

            margin:0px 20px;
            color:#ffffff;
            line-height: 20px;
        }
        .carrosel-left
        {
            width:24%;
            float:left;
            height: 270px;

        }


        .game_thumb
        {
            width:99%;
            float:left;
            clear: both;
            margin-bottom:7px;
            cursor: pointer;
        }
        .mCSB_container_wrapper > .mCSB_container
        {
            padding-right:0px !important;

        }
    </style>  
    <script>
        $(document).ready(function() {
            $.mCustomScrollbar.defaults.scrollButtons.enable = true; //enable scrolling buttons by default
            $.mCustomScrollbar.defaults.axis = "yx"; //enable 2 axis scrollbars by default
            $("#clgame").mCustomScrollbar({theme: "rounded-dark"});
            $(document).on("click", ".game_thumb", function()
            {
                $(".game_thumb").addClass("non-selected");
                $(this).removeClass("non-selected");
                var idsplit = this.id.split("_");

                $(".carrosel_news_all").hide();
                $("#carrosel_" + idsplit[1])
                        .css('opacity', 0)
                        .slideDown('slow')
                        .animate(
                                {opacity: 1},
                        {queue: false, duration: 'slow'}
                        );



            });
        });

    </script>  
<?php endif; ?>