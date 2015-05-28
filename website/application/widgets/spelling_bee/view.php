<?php $arCustomNews = getFormatedContentAll($news, 125); ?>


<li  id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> <?php echo $s_post_class; ?> type-post post-content-showed status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text col-sm-8 <?php echo ($i < $count_show) ? "shown" : ""; ?>  post-boxes ">

    <?php
    $widget = new Widget;
    $widget->run('seenassessment', $news);
    ?>
    
    <div class="post-content clearfix spellingbee_post">
        <div class="intro-post spellingbee">
            <div class="col-lg-6" style="padding:5px 0px;">
                <div class="col-lg-12 float-4">
                    <img src="styles/layouts/tdsfront/images/spellingbee/logo_spellbee.png"  style="overflow:hidden;float: left; width:80%; height: 63%; border:0; margin-left:35px;" />
                </div>
                
                <div class="col-lg-12 float-4">
                    <a href="" style="border:0px;"><img src="styles/layouts/tdsfront/images/spellingbee/spell-home-play.png"  style="overflow:hidden;float: left; width:70%; height: 20%; border:0; margin:15px 0px 0px 57px;" /></a>
                </div>
            </div>  
            <div class="col-lg-6 links-spell">
                <div class="col-lg-12 leader leader_board1">
                    <div class="col-lg-3">
                        <img src="styles/layouts/tdsfront/images/spellingbee/spell-home1.png"  style="overflow:hidden;float: left; width:100%; height: 100%; border:0;" />
                    </div>
                    <div class="col-lg-8">
                        GUES THE WORD
                    </div>
                </div>
                <div class="col-lg-12 leader leader_board2">
                    <div class="col-lg-3">
                        <img src="styles/layouts/tdsfront/images/spellingbee/spell-home2.png"  style="overflow:hidden;float: left; width:100%; height: 100%; border:0;" />
                    </div>
                    <div class="col-lg-8">
                        LEADER BOARD
                    </div>

                </div>

                <?php if(count($news->related_news_spelling_bee)>0) : ?>
                <?php $i = 0; foreach( $news->related_news_spelling_bee as $newsrelated ): ?>
               
                <?php
                if ( $i == 2 )
                {
                break;
                }
                ?>
                <?php
                if ( $i == 1 ):
                ?>
                <div class="col-lg-12 float-5">
                    <hr style="margin:0px 0px 0px 0px; width:95%;"/>
                </div>
                <?php endif; ?>

                <div class="col-lg-12 float-10">

                    <?php
                    $image_related = "";
                    if(isset($newsrelated->crop_images) && count($newsrelated->crop_images)>0)
                    {
                    $image_related = $newsrelated->crop_images[0];


                    }
                   
                    ?>
                    <div class="col-lg-3">
                        <?php if($image_related): ?>
                        <a href="" style="border:0px;"><img src="<?php echo $image_related; ?>"  style="overflow:hidden;float: left; width:92%; height: 85%; border:0; margin-right:15px;" /></a>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-7">
                        <div class="col-lg-12" style="height: 65%; overflow: hidden;" >
                            <a href="<?php echo create_link_url("index",$newsrelated->title,$newsrelated->id)?>"><?php echo $newsrelated->title; ?></a>
                        </div>
                        <div class="col-lg-12 date-string">
                            <?php echo $newsrelated->published_date_string; ?> Ago
                        </div>
                    </div>
                </div>
                <?php $i++;
                endforeach; ?>
                <?php endif; ?>
            </div>


        </div> 

    </div> 

</li>
<style>
    .type-post .spellingbee_post
    {
        border-bottom: 6px solid #FFCA0B !important;
    }
    .date-string
    {
        float:left;
        clear:both;
        color:#C6C5CA;
        font-size: 12px;
    }
    .float-5
    {
        float:left;
        clear:both;
    }
    
    .float-4
    {
        float:left;
        clear:both;
    }
    
    .float-10
    {
        float:left;
        clear:both;
        margin-top:15px;
        height: 20%;
    }
    .spellingbee .links-spell
    {
        color: white;
    }
    .spellingbee .col-lg-6
    {
        padding: 20px 0px;
        height: 500px;
    }
    .spellingbee .col-lg-6 .leader
    {
        margin-top: 8px;
        float:left;
    }
    .spellingbee .col-lg-6 .leader_board1 .col-lg-3
    {
        
        height: 20%;
        background: 3C49664;

    }
    .spellingbee .col-lg-6 .leader_board2 .col-lg-3
    {
       
        height: 20%;

    }
    .spellingbee .col-lg-6 .leader_board1 .col-lg-8
    {
        background: #E6A96E;
        height: 40px;
        padding: 35px 0px 56px 12px;
        font-size: 22px;
      
        margin-top: 1px;
    }
    .spellingbee .col-lg-6 .leader_board2 .col-lg-8
    {
        background: #FFCA0B;
        height: 40px;
        padding: 35px 0px 57px 12px;
        font-size: 22px;
    }

</style>    
<script>
    $(document).ready(function () {

        $("#triangle-bottomright").css("border-left-width", $("#post-image").width() + "px");

    });
</script>