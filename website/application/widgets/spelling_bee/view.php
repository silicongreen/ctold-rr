<?php $arCustomNews = getFormatedContentAll($news, 125); ?>


<li  id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> <?php echo $s_post_class; ?> type-post post-content-showed status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text col-sm-8 <?php echo ($i < $count_show) ? "shown" : ""; ?>  post-boxes ">
 
    <?php
        $widget = new Widget;
        $widget->run('seenassessment', $news);
    ?>
    <div class="post-content clearfix">
        <div class="intro-post spellingbee">
            <div class="col-lg-6">
               
            </div>  
            <div class="col-lg-6 links-spell">
                <div class="col-lg-12 leader leader_board1">
                    <div class="col-lg-3">
                        
                    </div>
                    <div class="col-lg-8">
                        GUES THE WORD
                    </div>
                </div>
                <div class="col-lg-12 leader leader_board2">
                    <div class="col-lg-3">
                        
                    </div>
                    <div class="col-lg-8">
                        LEADER BOARD
                    </div>
                    
                </div>
               
            </div> 
            
        </div> 
    </div>
       
</li>
<style>
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
        background: #C99567;
        height: 20%;
        
    }
    .spellingbee .col-lg-6 .leader_board2 .col-lg-3
    {
        background: #E9B60A;
        height: 20%;
        
    }
    .spellingbee .col-lg-6 .leader_board1 .col-lg-8
    {
        background: #E6A96E;
        height: 40px;
        padding: 35px 0px 57px 12px;
        font-size: 22px;
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
    $(document).ready(function(){
        
        $("#triangle-bottomright").css("border-left-width", $("#post-image").width() + "px");
       
    });
</script>