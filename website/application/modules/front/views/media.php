<?php 
    $b_checked_cache = TRUE;
    if (isset($_GET['archive']) &&  strlen($_GET['archive']) != "0")
    {
        $b_checked_cache = FALSE;
    }
    $CI = & get_instance();
   //$b_checked_cache = FALSE;
    $cache_name = "ALL_GALLERY_CACHE_".$ci_key."_" . str_replace(":", "-",  str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");
    if ( $b_checked_cache && $s_gallery_content =  $CI->cache->file->get($cache_name) )
    {
        echo $s_gallery_content;
    }
    else
    {
        ob_start();
?>
<style>
    .media-content .html5gallery-container-0
   {
        -webkit-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	   -moz-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	        box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	background: rgb(50,50,50);
	background: -moz-linear-gradient(top, rgb(68,68,68) 0%, rgb(52,52,52) 50%, rgb(41,41,41) 50%, rgb(51,51,51) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(68,68,68)), color-stop(50%,rgb(52,52,52)), color-stop(50%,rgb(41,41,41)), color-stop(100%,rgb(51,51,51)));
	background: -webkit-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -o-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -ms-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#444444', endColorstr='#222222',GradientType=0 );
    }
    .media-content .html5gallery-container-1
   {
        -webkit-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	   -moz-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	        box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	background: rgb(50,50,50);
	background: -moz-linear-gradient(top, rgb(68,68,68) 0%, rgb(52,52,52) 50%, rgb(41,41,41) 50%, rgb(51,51,51) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(68,68,68)), color-stop(50%,rgb(52,52,52)), color-stop(50%,rgb(41,41,41)), color-stop(100%,rgb(51,51,51)));
	background: -webkit-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -o-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -ms-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#444444', endColorstr='#222222',GradientType=0 );
    }
    .media-content .html5gallery-container-2
   {
        -webkit-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	   -moz-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	        box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	background: rgb(50,50,50);
	background: -moz-linear-gradient(top, rgb(68,68,68) 0%, rgb(52,52,52) 50%, rgb(41,41,41) 50%, rgb(51,51,51) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(68,68,68)), color-stop(50%,rgb(52,52,52)), color-stop(50%,rgb(41,41,41)), color-stop(100%,rgb(51,51,51)));
	background: -webkit-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -o-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -ms-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#444444', endColorstr='#222222',GradientType=0 );
    }
    .media-content .html5gallery-container-3
   {
        -webkit-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	   -moz-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	        box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	background: rgb(50,50,50);
	background: -moz-linear-gradient(top, rgb(68,68,68) 0%, rgb(52,52,52) 50%, rgb(41,41,41) 50%, rgb(51,51,51) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(68,68,68)), color-stop(50%,rgb(52,52,52)), color-stop(50%,rgb(41,41,41)), color-stop(100%,rgb(51,51,51)));
	background: -webkit-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -o-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -ms-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#444444', endColorstr='#222222',GradientType=0 );
    }
    .media-content .html5gallery-container-4
   {
        -webkit-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	   -moz-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	        box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	background: rgb(50,50,50);
	background: -moz-linear-gradient(top, rgb(68,68,68) 0%, rgb(52,52,52) 50%, rgb(41,41,41) 50%, rgb(51,51,51) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(68,68,68)), color-stop(50%,rgb(52,52,52)), color-stop(50%,rgb(41,41,41)), color-stop(100%,rgb(51,51,51)));
	background: -webkit-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -o-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -ms-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#444444', endColorstr='#222222',GradientType=0 );
    }
   .media-content .html5gallery-container-5
   {
        -webkit-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	   -moz-box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	        box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
	background: rgb(50,50,50);
	background: -moz-linear-gradient(top, rgb(68,68,68) 0%, rgb(52,52,52) 50%, rgb(41,41,41) 50%, rgb(51,51,51) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(68,68,68)), color-stop(50%,rgb(52,52,52)), color-stop(50%,rgb(41,41,41)), color-stop(100%,rgb(51,51,51)));
	background: -webkit-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -o-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: -ms-linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	background: linear-gradient(top, rgb(68,68,68) 0%,rgb(52,52,52) 50%,rgb(41,41,41) 50%,rgb(51,51,51) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#444444', endColorstr='#222222',GradientType=0 );
    }
</style>
<div class="ym-grid media-content">

    <!-- Start media-content-gallery   photo-gallery   -->                           

    <?php if ( has_separate_gallery_data($ci_key, $s_date)) : ?>
    
    <?php
        $data_gallery = get_gallery_data($ci_key, $s_date);
        
    ?>
    <div   class="ym-grid media-content-gallery mtab-0">



        <div style="text-align:center;">


            <div style="display:none; margin: 0 auto;" class="mediahtml5gallery" data-skin="vertical" data-width="500" data-height="298" data-showtitle="true" data-showsocialmedia="true"  
                 data-thumbwidth="80" data-titleoverlay="true" data-titleautohide="true" data-thumbheight="61" data-thumbgap="13" data-socialurlforeach="true"   data-bgcolor="#9F9F9F"
                 data-resizemode="fill" data-thumbshowtitle="false" >

                <?php foreach($data_gallery as $value): ?>
                <?php
                $s_thumb_image = str_replace("gallery/", "gallery/weekly/", $value->material_url);
                
                list($width, $height, $type, $attr) = @getimagesize(base_url().$s_thumb_image);
                if(!isset($width))
                {
                   $s_thumb_image =  $value->material_url;
                }    
                
                
                ?>
                <a href="<?php echo base_url().$value->material_url?>"><img src="<?php echo base_url().$s_thumb_image?>" alt="<?php echo $value->caption; ?>"></a>
                <?php endforeach; ?>
                <!-- Add images to Gallery -->


            </div>

        </div>




    </div>
    <?php endif; ?>
    <!-- End media-content-gallery   photo-gallery   -->

    <div style="clear: both;"> </div>


    <!-- Start media-content-gallery   video-gallery   -->        
    <?php if (has_separate_gallery_data($ci_key, $s_date,"video" ) ) : ?>
    <?php
        $data_gallery_video = get_gallery_data_video($ci_key, $s_date);
        
    ?>
    <div   class="ym-grid media-content-gallery <?php echo ( $s_image_exists ) ? "mtab-1" : "mtab-0"; ?>" >

   

        <div style="display:none;" class="mediahtml5gallery" data-skin="vertical" data-width="500" data-height="298" data-showtitle="true" data-showsocialmedia="false"  
             data-thumbwidth="80" data-thumbheight="61" data-titleoverlay="true" data-titleautohide="true"  data-thumbgap="13"    data-bgcolor="#9F9F9F"
             data-resizemode="fit" data-thumbshowtitle="false" data-autoplayvideo="false"  >
            
            <!-- Add images to Gallery -->
            <?php foreach($data_gallery_video as $value): ?>
               <?php
                $s_thumb_image = str_replace("url:", "", $value->thumb_small);
                
                $s_thumb_image_array = explode(",", $s_thumb_image);
                
                $s_thumb_image = $s_thumb_image_array[0];
                
               
                ?> 
                <a href="<?php echo $value->url?>"><img src="<?php echo $s_thumb_image ?>"></a>
            <?php endforeach; ?>

        </div>  


    </div>
    <?php endif; ?>
    <!-- End media-content-gallery   video-gallery   -->           


    <!-- Start media-content-gallery   podcast   -->      


    <?php if (has_separate_gallery_data($ci_key, $s_date,"podcast" ) ) : ?>
    <?php 
        $s_podcast_class = "mtab-2";
        if ( $s_image_exists && ! $s_video_exists )
        {
            $s_podcast_class = "mtab-1";
        }
        else if ( !$s_image_exists && $s_video_exists )
        {
            $s_podcast_class = "mtab-1";
        }
        else if ( !$s_image_exists && ! $s_video_exists )
        {
            $s_podcast_class = "mtab-0";
        }
    ?>
    <div   class="ym-grid media-content-gallery <?php echo $s_podcast_class; ?>">


        <div style="display:none;" class="mediahtml5gallery" data-skin="vertical" data-width="600" data-height="120" data-showsocialmedia="false"  
             data-thumbwidth="160" data-titleoverlay="true" data-titleautohide="true"  data-thumbheight="52" data-thumbgap="7"  
             data-resizemode="fit" data-showcarousel="false"
             data-xml="<?php echo  base_url();?>gallery/xml/<?php echo $ci_key;?>-podcast-<?php echo date("Ymd", strtotime($s_date));?>.xml"  data-titleheight="30" data-titlecss="{color:#ffffff; font-size:12px; float:left; font-family:sans-serif, Arial; overflow:hidden; white-space:nowrap;}" >

            <!-- Add images to Gallery -->


        </div> 

    </div> 
    <?php endif; ?>
    <!-- End media-content-gallery      -->      


</div>

<?php 
    $s_gallery_content = ob_get_contents();
    ob_end_clean(); 
    if ( ! isset($_GET['archive'])  )
    {
        $CI->cache->file->save($cache_name, $s_gallery_content, 86400);
    }
    echo $s_gallery_content;
    }
?>
<?php if ( $show_ad ) : ?>
<div style="float: right; width: 300px; height: 410px; ">
    <?php
        $adplace_helper = new Adplace;
        $adplace_helper->printAds( $ad_plan_id, null, true, $ci_key );
    ?>
</div>
<?php endif; ?>