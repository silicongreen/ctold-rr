<div id="fb-root"></div>
<!--<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=210972602426467&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>-->

<?php $widget = new Widget; ?>
<?php if ($featured == 1) : ?>

<?php else : ?>


<style>
    #masonry-ordered div
    {
        border:1px solid black;
        width:33%;
    }
    .word-of-the-day {
        text-align: center;
    }
    .word-of-the-day-sound {
        height: 75px;
        margin: -5px auto auto 105px;
        width: 75px;
    }
    .word-of-the-day-sound button {
        background: url("styles/layouts/tdsfront/image/Words-ofthe-day-logo-sound.png") no-repeat scroll 0 0 / 55px auto;
        border: medium none;
        color: #de3427;
        margin: 0;
        padding: 30px;
        position: absolute;
    }
    .word-of-the-day-sound button:hover {
        background: url("styles/layouts/tdsfront/image/Words-ofthe-day-logo-sound-hover.png") no-repeat scroll 0 0 / 55px auto;
    }
    
</style>    





    <div style="position: relative; width: 100%;margin:0px auto;background:#fff;margin-bottom:30px;"> 
        <?php $j = -1; ?>    
        <?php $news_to_show = count($obj_post_news); ?>   
   

        <ul class="video_list" style="position: relative; width:100%;margin:2px;">
            <?php $is_breaking_found = false; $count_show = 3; ?>
            <?php $found_slider = 0; ?>
            <?php $ar_slider_amount = array(); ?>
            <?php $i = 0; $ka = 0; if ($obj_post_news) foreach ($obj_post_news as $news) :?>
			<?php $arCustomNews = getFormatedContentAll($news, 125); ?>
             
							<li>
								<div style="width:100%;">
								<img src="<?php echo $arCustomNews['lead_material']; ?>" alt="<?php echo $news->headline; ?>" style="width:215px;height:130px; ">
								<div style="height:80px;overflow:hidden;">
									<span class="f5">
											<a href="<?php echo $link_array['url']; ?>" target="<?php echo $link_array['target'] ?>" title="<?php echo $news->headline; ?>">
													<span  style="font-size:13px;line-height:1 !;color:red;">
													<?php 
													if (strlen($news->headline) >= 40):
														echo substr_with_unicode($news->headline."wqwwqwe qweqwe qwe qweqwe q", false, 40) . " ... ";
													else:
														echo $news->headline;
													endif;
													?></span>                                           
											</a>
										</span>
										<br />
										<span class="brown-subtitle f5">
									<?php echo get_post_time($news->published_date); ?> ago</span>
									<?php if($news->title!=""):?><div class="by_line f5" style="font-size:12px;" >By <span class="f5"><?php echo $news->title; ?></span></div><?php endif;?>
								</div><!-- post-title --> 
								
								<div style="width:100%;">									
									<div class="seen col-lg-12 f5"><span style="font-size:12px;color:#B1B8BA;"><?php echo $news->user_view_count; ?>&nbsp;views</span></div>
								</div>
								</div>
							</li>
			<?php
                $i++;
                endforeach;
            ?>

        </ul>
        <div style="clear:both;"></div>

        <style>
            .video_list li
			{
				display: inline-block;
				width: 22.5%;  
				margin: 10px !important;
  
			}
			.video_list li:befor
			{
				content:none !important;margin: 10px !important;
			}
			.video_list li:after
			{
				content:none !important;margin: 10px !important;
			}
        </style>
        
       
    </div>    
   
    <?php if ($total_data > $page_size) : ?>
        <div class="loading-box" style="">  
            <div class="loading"></div>
        </div>
    <?php endif; ?>
<?php endif; ?>
