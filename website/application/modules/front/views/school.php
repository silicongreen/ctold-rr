<?php
    $cover_image = base_url()."Profiler/images/right/banner.png";
    if($school_details->cover)
    {
        
        $cover_image_url = base_url().$school_details->cover;
        list($width, $height, $type, $attr) = @getimagesize($cover_image_url);
        if(isset($width))
        {
            $cover_image = $cover_image_url;
        }  
       
           
    }  
    $logo_image = base_url()."images/backgrounds/bg_content.png";
    if($school_details->logo)
    {
       
        $logo_image_url = base_url().$school_details->logo;
        list($width, $height, $type, $attr) = @getimagesize($logo_image_url);
        if(isset($width))
        {
            $logo_image = $logo_image_url;
        }  
        
    } 
   
 ?>
<div class="container" style="width: 73%;min-height:250px;">

<div style="margin:20px 0px;height:60px;">
		<div style="float:left">
			<h2 class="f2">School Information</h2>
		</div>
		<div class="header-bg" style="display:block;float:right;  margin-top:5px;">
            <div style="float: left;margin:5px;">                
                <form method="get" class="searchform" action="<?php echo base_url('search'); ?>" role="search">                    
                    <input class="field" name="s" id="s" class='search' placeholder="Search this site" type="search" style="border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; width: 220px; margin-top: 3px;">
                    <input class="submit search-button" value="" type="submit" />
                </form>                
            </div>
        </div>
</div>

<div class="banner_image">
	<div style="height:300px; width:100%;">
		<img src="<?php echo $cover_image;?>"  style="height:300px; width:100%;" />
    </div>	
</div>
<div class="school_info_box">
<!--    <div class="school_logo">
        <img src="<?php echo $logo_image;?>" width="120" height="120" />
    </div> -->
    <div class="school_details_and_menu">
        <div class="school_details">
            <div class="school_name" id="fitin">
                <div>
				<span class="f2" style="/*font-size;30px;*/color:#fff;
				text-shadow: 2px 4px 3px rgba(0,0,0,0.3);"><?php echo $school_details->name ?><?php if($school_details->district){ echo " , ".$school_details->district;} ?></span>
                <!--<span><?php #echo $school_details->views ?> Visits</span>-->
				</div>
            </div>
            <div class="school_like">
                <div class="fb-like" data-href="<?php echo base_url()."schools/".sanitize($school_details->name)."/" ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
            </div>
        </div> 
        <div class="headerlink f5">
            <div style="width:790px;height:60px;margin:0px 99px;padding:20px 20px 0px 20px;position:absolute;border-radius:7px;background:linear-gradient(to bottom, #FEFEFE , #D9D9D9);-webkit-box-shadow: 0 10px 25px -2px gray;
   -moz-box-shadow: 0 10px 25px -2px gray;
        box-shadow: 0 10px 25px -2px gray;">
				<ul>
					<?php 
					$count = count($schools_pages);
					$ci = 1;
					foreach($schools_pages as $value): ?>
					<li><a <?php if($menu_details->title == $value->title): ?> class="red_menu"<?php endif; ?> href="<?php echo base_url()."schools/".sanitize($school_details->name)."/".sanitize($value->title); ?>"><?php echo $value->title?></a></li>
						<?php if($ci < $count):?>
							<li>|</li>
						<?php endif; ?>
					<?php $ci++; endforeach; ?>
                                       <li>|</li>
                                       
                                       <li><a <?php if(isset($feeds)): ?> class="red_menu"<?php endif; ?> href="<?php echo base_url()."schools/".sanitize($school_details->name)."/feed"; ?>">Feeds</a></li>                 
								  
				</ul>
			</div>
        </div>
    </div>    
</div> 

<?php if($school_page_details): ?>
<div class="school_content_box">
    
    <?php if(isset($activity_link)): ?>
        <h2 class="f2"><?php echo $school_page_details->title; ?></h2>
    <?php endif; ?>
        <div class="f5"><?php echo $school_page_details->content ?></div>
    <?php if(count($gallery)>0): ?>
     <div style="clear:both; margin-left: 53px;
    margin-top: 20px; ">
        <div style="text-align:center;">


               <div style="display:none; margin: 0 auto;" class="html5gallery" data-skin="horizontal" data-width="700" data-height="398" data-showtitle="true" data-showsocialmedia="false"  
                    data-titleoverlay="true" data-titleautohide="true" data-socialurlforeach="true" 
                    data-resizemode="fit" data-responsive="true" data-thumbshowtitle="false"   data-bgcolor="#ECEDEF" >

                   <?php foreach($gallery as $value): ?>
                   <?php
                   $s_thumb_image = str_replace("gallery/", "gallery/weekly/", $value->material_url);

                   list($width, $height, $type, $attr) = @getimagesize(base_url().$s_thumb_image);
                   if(!isset($width))
                   {
                      $s_thumb_image =  $value->material_url;
                   }    


                   ?>
                   <a href="<?php echo base_url().$value->material_url?>"><img src="<?php echo base_url().$s_thumb_image?>" ></a>
                   <?php endforeach; ?>
                   <!-- Add images to Gallery -->


               </div>

        </div>
     </div>    
    
    <?php endif; ?>
</div>
<?php endif; ?>
<?php if(isset($activities) && count($activities)>0): ?>
<div class="school_activities_box" <?php if(!isset($school_page_details) || !$school_page_details): ?> style="margin-top:20px;" <?php endif; ?>>
	<div class="school_activity_title">
    <span class="f2" style="margin-left:70px;font-size:25px;">School Activity</span>
    <?php if(isset($school_page_details) && $school_page_details): ?> 
    <a href="<?php echo base_url()."schools/".sanitize($school_details->name)."/activities/" ?>" style="margin-top:10px;text-decoration:none;">See All</a>
    <?php endif; ?>
</div>
	<div class="school_activity_box" >
    
    
    <?php foreach($activities as $value): ?>

        <div class="activity">
            <div class="title"></div>
            <?php if($value['image']):?>
                <div class="left_img">
                  
                        <img style="float: left; margin-right:15px;" src="<?php echo $value['image']; ?>" width="120" height="120" />
                        
                        <div>
                            <p style="margin:0px; margin-top:-6px;">
                                <a class="activity_title" href="<?php echo base_url()."schools/".sanitize($school_details->name)."/activities/".$value['id']; ?>"><?php echo $value['title']; ?></a>
                            </p>
                            
							<p style="margin:0px; font-size:13px;"><?php echo $value['content']; ?></p>
                        </div> 
                   
                </div> 
            <?php else: ?>
                <div class="leftfull">
                    <p >
                        <a class="activity_title" href="<?php echo base_url()."schools/".sanitize($school_details->name)."/activities/".$value['id']; ?>"><?php echo $value['title']; ?></a>
                    </p>
                    <p style="margin:0px;font-size:13px;"><?php echo $value['content']; ?></p> 
                </div>
            <?php endif; ?>
            
        </div>
        <?php endforeach; ?>
      
</div>
</div>
<?php endif; ?>
    
<?php if(isset($feeds)): ?>
   <div class="school_feed_box"> 
       <?php $widget = new Widget; $widget->run('postdata', "school",$school_details->id, 'school'); ?>
   </div>
<?php endif; ?>    

</div>
<style>
    .action-box
    {
        background: none repeat scroll 0 0 #e7e7e7 !important;
    }
    .post-content
    {
       background: none repeat scroll 0 0 #e7e7e7 !important;
    }
    
	.school_activities_box
	{
		float:left;
		clear:both;
		margin-bottom:50px;
		border:1px solid gray;
	}
	.school_activity_title
    {
        float:left;
        clear:both;
        width:100%;        
        padding: 5px 0px;
        background: #88BAA1 url(<?php echo base_url('styles/layouts/tdsfront/images/sc_activity.png'); ?>) no-repeat 20px 10px;;
        color:white;     
		border:1px solid #fff	;	
    }
    
    .school_activity_title span
    {
        float:left;
        margin-left:20px;
    }
    .school_activity_title a
    {
        float:right;
        margin-right:20px;
        color:white;
        text-decoration: underline;
    }
    .school_logo img
    {
        height:120px;
    }
    .activity span
    {
        
        font-size:11px;
    }
	.activity_title
	{
		 font-size:15px;
		 color:#74A98D;
	}
    span.activity_title{
        
        float:left;
        clear: both;
        color:#FC5D51;
        font-size:14px;
    }
    
  .leftfull
  {
     float:left;
     width: 100%;
  }
  .left_img
  {
     float:left;
     width: 100%; 
  }

 .activity
 {
     float:left;
     width:44%;
     margin-bottom: 20px;
     margin-left:30px;
     height: 143px;
     
     overflow: hidden;
 }
.banner_image
{
    float:left;
    clear:both;
    width:100%;
}
.school_activity_box
{
    float:left;
    clear:both;    
    width:100%;    
    padding: 41px 20px;
    background: #fff;    
}
.school_feed_box
{
    float:left;
    clear:both;
    width:100%;    
    padding: 40px 30px;
    background: #fff;
    border:1px solid gray;
    margin-bottom:20px;
}
.school_content_box
{
    clear:both;
    width:100%;    
    padding: 40px 30px;
    background: #fff;
    border:1px solid gray;
	margin-bottom:20px;
}
.school_info_box
{
    width:100%;   
}
.school_logo
{
    float:left;
    background: #fff;
    padding: 22px;
    width: 18%;
    margin-top:-10%;
 
}
.school_details_and_menu
{
    position:absolute;
	top:333px;
    width: 73%;
}
.school_details
{   
   background: none; 
	margin:0px 99px;
}
.headerlink
{    
    height:60px;
    float: left;
    font-family: arial;
    font-size: 14px;
    margin-top: 5px;
}
.school_name
{    
    
    margin-top:20px;
	text-shadow: 0 1px 1px #4d4d4d;	
	display: inline-block;
}
#fitin
{
	width:790px;
	height:40px;
    font-size: 45px;
}
.school_like
{
    float:right;
    margin:40px 60px;
}
.school_details span
{  
   color:#92979B;
}
.school_details span:first-child
{    
    
}
.school_details_and_menu .headerlink ul li
{
	margin-bottom:8px;
        display: inline;
        padding: 10px 5px;
}
.headerlink ul li a
{
    font-size:17px;
    padding:16px 10px;
	
}
.headerlink ul li a.red_menu
{
    color:red;
    border-bottom: 4px solid red;
	
}
</style>
<script>
$(function() {    
	while( $('#fitin div').height() > $('#fitin').height() ) {		
        $('#fitin div').css('font-size', (parseInt($('#fitin div').css('font-size')) - 1) + "px" );
    }
		
	if($('.headerlink').width() > $('.headerlink div').width())
	{
		$('.headerlink div').css('margin-left', (parseInt($('.headerlink').width() - ($('.headerlink div').width() + 40))/2) + "px")
		$('.headerlink div').css('margin-right', (parseInt($('.headerlink').width() - ($('.headerlink div').width() + 40))/2) + "px")
	}
	
	$('.school_details_and_menu').css('top', (parseInt( 514 - $('.school_details_and_menu').height() )) + "px");
});
</script>