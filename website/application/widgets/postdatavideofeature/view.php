<?php if ($obj_post_news && count($obj_post_news) > 0): ?>
    <script src="<?= base_url() ?>scripts/jquery/jquery.mCustomScrollbar.concat.min.js"></script>
    <link rel="stylesheet" href="<?= base_url() ?>styles/layouts/tdsfront/css/jquery.mCustomScrollbar.css">
    <div class='featured col-lg-12'>
        <h2 class="f2" style="color:#BFC1C0; margin:20px;display:none;" >Featured Videos</h2>
        <ul  class="col-lg-12">
            <?php
            $i = 0;
            if ($obj_post_news)
                foreach ($obj_post_news as $news) :
                    //echo "<pre>";
					//print_r($news);
					
					?>	
                    <?php $arCustomNews = getFormatedContentAll($news, 160); ?>
                    <?php $link_array = get_post_link_url($news); ?>        
                    <li id="carrosel_<?php echo $news->post_id; ?>" 
                        <?php if ($i > 0): ?> style="display:none;" <?php endif; ?>
                        class="carrosel_news carrosel_news_all">
                        <div class="carrosel-news">
                            <div class='carrosel-images'>
								<a href="<?php echo $link_array['url']; ?>" target="<?php echo $link_array['target'] ?>" title="<?php echo $news->headline; ?>">
                                <img src="<?php echo $arCustomNews['lead_material']; ?>" alt="<?php echo $news->headline; ?>" class="carrosel_img_size" />
								<div class="play_btn_ch"></div></a>
                            </div>
                            <div class='carrosel-news-content'>
                                <div class="post-title" style="margin-left:0px!important;">
									<h2 class="f2" style="font-size:33px;">
											<a href="<?php echo $link_array['url']; ?>" target="<?php echo $link_array['target'] ?>" title="<?php echo $news->headline; ?>">
                                                                                            <strong style="color: #000;font-weight: 400;"><?php echo $news->headline; ?></strong>                                           
											</a>
										</h2>
										<span class="brown-subtitle">
									<?php echo get_post_time($news->published_date); ?> ago</span>
									<?php if($news->title!=""):?><div class="by_line" >By <span class="f4"><?php echo $news->title; ?></span></div><?php endif;?>
								</div><!-- post-title --> 							
							
                            <div class="akmanda-excerpt f5" style="margin-left:0px;height:135px;"> <?php echo $arCustomNews['content']; ?></div>
							<?php if($news->user_view_count>0):?>
							<div style="width:25%;float:right;">
								<div class="seen-image col-lg-3"><img src="<?php echo base_url("styles/layouts/tdsfront/images/social/seen.png"); ?>" /></div>
								<div class="seen col-lg-9"><span style="font-size:15px; color:#B1B8BA;margin-left:5px; "><b><?php echo $news->user_view_count; ?></b></span></div>
							</div>
							<?php endif;?>
                            </div>
                        </div>                    
                    </li>               
                    <?php
                    $i++;
                endforeach;
            ?>
        </ul>  
    </div>
    <div style='clear: both;'></div>
    <style>
        
        .carrosel_img_size{
            width:595px;
            height:340px; 
        }
        @media all and (min-width: 315px) and (max-width: 449px) {
            div.featured 
            {
                height: auto !important;
            }
            
            .carrosel_img_size{
            width:100%;
            height:100% !important; 
            }
            li.carrosel_news div.carrosel-news div.carrosel-images
            {
                width:100% !important;
                height:auto !important;
            }
            .featured li.carrosel_news div.carrosel-news div.carrosel-news-content
            {
                    clear:both;
                    width:100% !important;
                    height:auto !important;
                    margin-left:0px !important;
                    margin-top:75px;

            }
            .carrosel-news-content .post-title h2
            {
                font-size:20px !important;
            }
            .carrosel-news-content .post-title span
            {
                font-size:8px !important;
            }
            .carrosel-news-content .akmanda-excerpt
            {

                margin:1px 1px !important;
                line-height: 15px !important;
            }
            
        }
        @media all and (min-width: 450px) and (max-width: 599px) {
            div.featured 
            {
                height: auto !important;
            }
            
            .carrosel_img_size{
            width:100%;
            height:100% !important; 
            }
            li.carrosel_news div.carrosel-news div.carrosel-images
            {
                width:100% !important;
                height:auto !important;
            }
            .featured li.carrosel_news div.carrosel-news div.carrosel-news-content
            {
                    clear:both;
                    width:100% !important;
                    height:auto !important;
                    margin-left:0px !important;
                    margin-top:75px;

            }
            .carrosel-news-content .post-title h2
            {
                font-size:20px !important;
            }
            .carrosel-news-content .post-title span
            {
                font-size:8px !important;
            }
            .carrosel-news-content .akmanda-excerpt
            {

                margin:1px 1px !important;
                line-height: 15px !important;
            }
            
        }
        @media all and (min-width: 600px) and (max-width: 799px) {
            div.featured 
            {
                height: auto !important;
            }
            
            .carrosel_img_size{
            width:100%;
            height:100% !important; 
            }
            li.carrosel_news div.carrosel-news div.carrosel-images
            {
                width:100% !important;
                height:auto !important;
            }
            .featured li.carrosel_news div.carrosel-news div.carrosel-news-content
            {
                    clear:both;
                    width:100% !important;
                    height:auto !important;
                    margin-left:0px !important;
                    margin-top:75px;

            }
            .carrosel-news-content .post-title h2
            {
                font-size:20px !important;
            }
            .carrosel-news-content .post-title span
            {
                font-size:8px !important;
            }
            .carrosel-news-content .akmanda-excerpt
            {

                margin:1px 1px !important;
                line-height: 15px !important;
            }
            
        }
        @media all and (min-width: 800px) and (max-width: 992px) {
            div.featured 
            {
                height: auto !important;
            }
            
            .carrosel_img_size{
            width:100%;
            height:100% !important; 
            }
            li.carrosel_news div.carrosel-news div.carrosel-images
            {
                width:100% !important;
                height:auto !important;
            }
            .featured li.carrosel_news div.carrosel-news div.carrosel-news-content
            {
                    clear:both;
                    width:100% !important;
                    height:auto !important;
                    margin-left:0px !important;
                    margin-top:75px;

            }
            .carrosel-news-content .post-title h2
            {
                font-size:20px !important;
            }
            .carrosel-news-content .post-title span
            {
                font-size:8px !important;
            }
            .carrosel-news-content .akmanda-excerpt
            {

                margin:1px 1px !important;
                line-height: 15px !important;
            }
        }
        @media all and (min-width: 993px) and (max-width: 1199px) {
             div.featured 
            {
                height: auto !important;
            }
            
            .carrosel_img_size{
            width:100%;
            height:100% !important; 
            }
            li.carrosel_news div.carrosel-news div.carrosel-images
            {
                width:100% !important;
                height:auto !important;
            }
            .featured li.carrosel_news div.carrosel-news div.carrosel-news-content
            {
                    clear:both;
                    width:100% !important;
                    height:auto !important;
                    margin-left:0px !important;
                    margin-top:75px;

            }
            .carrosel-news-content .post-title h2
            {
                font-size:20px !important;
            }
            .carrosel-news-content .post-title span
            {
                font-size:8px !important;
            }
            .carrosel-news-content .akmanda-excerpt
            {

                margin:1px 1px !important;
                line-height: 15px !important;
            }
        }   
        @media all and (min-width: 1200px) and (max-width: 1300px) {
            .carrosel_img_size{
            width:100%;
            height:100% !important; 
            }
            .featured li.carrosel_news div.carrosel-news div.carrosel-news-content
            {
                    clear:both;
                    width:35% !important;
                    height:auto !important;
                    margin-left:0px !important;
                    

            }
        }
        .by_line span
		{
			
			font-style:italic;
		}
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
		div.featured
		{
			padding:15px !important;
		}
		div.featured ul
		{
			left:0px !important;
		}
        div.featured ul.col-lg-8 {
            height: 310px;
            width: 72% !important;
            left: 0px !important;
        }
        div.featured 
        {
            height: auto !important;
        }
		li.carrosel_news div.carrosel-news
		{
			width:100% !important;
		}
		li.carrosel_news div.carrosel-news div.carrosel-images
		{
			width:63%;
			height:auto;
			
		}
		.featured li.carrosel_news div.carrosel-news div.carrosel-news-content
		{
			width:34%;
			height:auto !important;
			background:none !important;
			
		}
        .featured li.carrosel_news div.carrosel-news div.carrosel-news-content
        {
            background:#3D3D3B;            
           height:auto !important;
        }
        .carrosel-news-content .akmanda-excerpt
        {

            margin:30px 20px;
            color:gray;
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