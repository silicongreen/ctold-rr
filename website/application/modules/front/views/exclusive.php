<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<?php if($config['special_category_banner_image']): ?>
<img width="100%"  src="<?php echo $config['special_category_banner_image'];?>" alt="<?php echo $config['special_category_title'];?>" />
<?php else: ?>
<h3 class="exclusive-headline"><?php echo $config['special_category_title']; ?></h3>
<?php endif;?>

<div class="ex-story-container ym-gl">

    <div class="ex-col1 exclusive-leftside">
        <?php $i=0; foreach($exclusive as $news) : ?>
        
        
         <h1 class="headline"><a href="<?php echo create_link_url($s_ci_key,$news->headline,$news->id);?>"><?php echo $news->headline; ?></a></h1>
        <?php
            if($news->lead_material && strpos("http", $news->lead_material)===false)
            {
                $news->lead_material = base_url().$news->lead_material;
            } 
            
            $thumb_url = str_replace("gallery/", "gallery/bigimage/", $news->lead_material);
            if(@getimagesize($thumb_url))
            {
                $news->lead_material = $thumb_url;
            }
            
            $gallery_html = "";
            if (file_exists('gallery/xml/post/post_' . $news->id . ".xml")) {

              $gallery_html = '<div  class="ym-grid"> 
                        <div style="text-align:center; width:95%;">
                            <div style="display:none;" class="html5gallery" data-skin="horizontal" data-thumbshowtitle="false" data-width="670" data-height="380"  data-showsocialmedia="false"  

                         data-resizemode="fill" 
                         data-xml="' . base_url() . 'gallery/xml/post/post_' . $news->id . '.xml" >
                        </div>
                        </div>
                        </div>';  
            }   
          
        ?>
        <?php if($gallery_html!=""): ?>
            <?php echo $gallery_html; ?>
        <?php else: ?>
        <br>
        <img width="100%"  src="<?php echo $news->lead_material;?>" alt="<?php echo $news->headline;?>" />
        <br> 
        <?php endif; ?>

        <p>
            <?php
            echo limit_words($news->content,50);
            ?>   

        </p>
        <?php break; endforeach; ?>

    </div>

    <div class="ex-col2 ym-gr exclusive-rightside">
        <div class="ex-right-content"> 
            <ul> 
                <?php $i=0; foreach($exclusive as $news) : ?>
                    <?php 
                    if($i==0)
                    {
                        $i++;
                        continue;
                    } 
                    
                    if($news->lead_material && strpos("http", $news->lead_material)===false)
                    {
                        $news->lead_material = base_url().$news->lead_material;
                    } 
                    $thumb_url = str_replace("gallery/", "gallery/otherRightFirst/", $news->lead_material);
                    if(@getimagesize($thumb_url))
                    {
                        $news->lead_material = $thumb_url;
                    }
                    ?>    
                <li>
                    <h3 class="headline"><a href="<?php echo create_link_url($s_ci_key,$news->headline,$news->id);?>"><?php echo $news->headline; ?></a></h3>
                    <?php if(isset($news->embedded) && $news->embedded!=""):?>
                        <?php
                            echo $news->embedded;
                        ?>
                        <a target="_blank" href="http://www.thedailystar.net/budget-2014-15" class="other-videos">All Stories»</a>
                    <?php else:?>
                        <img class="alignleft"  src="<?php echo $news->lead_material;?>" alt="<?php echo $news->headline;?>" width="120" height="72" />
                        <p>
                            <?php
                                echo limit_words($news->content,10);
                            ?> 
                        </p>
                    <?php endif;?>
                    
                   
                   
                    <div style="clear:both;"></div>
                    
                    
                </li> 
                
                <?php $i++; endforeach; ?>
                
                <li><a target="_blank" href="http://www.thedailystar.net/budget-2014-15" class="more-news">All Stories</a></li>
            </ul>
        </div> 
    </div> 
</div> 
<div style="clear: both;"></div>


<style>
    .exclusive-box p{
        color:<?php echo $config['special_category_font_color'] ?>;
        font-size: 12px;
        line-height: 1.5em;
    }
    
    .other-videos {
        width: 100%;
        display: block;
        text-align: left;
        margin: 0 auto;
        padding-top: 5px;
        color: <?php echo $config['special_category_font_color'] ?>;
        font-weight: bold;
        font-size: 19px;
        border-radius: 0px;
        font-style: italic;
        clear: both;
        margin-top: 5px;
     }

        .other-videos:hover {
        color: blue;
        text-decoration: underline;
        }
    .exclusive-box{
        border: 1px solid #ccc;
        margin-bottom: 10px;
        background: <?php echo $config['special_category_background_color'] ?>; 
    }
    .exclusive-headline{
        background: <?php echo $config['special_category_background_color'] ?>;
        color: <?php echo $config['special_category_font_color'] ?>;
        padding: 10px 50px;
        font-size: 40px;
        margin: 0 auto;
        text-align: center;
        margin-top: 10px;
        border-bottom: 1px solid <?php echo $config['special_category_font_color'] ?>;
    }
    .ex-story-container{
        padding: 10px;
    } 
    .exclusive-leftside h1.headline a{
        color: <?php echo $config['special_category_font_color'] ?> !important;
    }
    .exclusive-leftside h1.headline{
        font-size: 36px;
        color: <?php echo $config['special_category_font_color'] ?> !important;
        font-weight: normal;
    }
    .ex-right-content h3{

        font-size: 16px;
        font-weight: bold;
        line-height: 16px;
        padding-bottom:10px;
    }
    .ex-right-content h3 a{
        color: <?php echo $config['special_category_font_color'] ?> !important;
    }

    .exclusive-leftside{
        float: left;
        width: 68%;
        border-right: 1px solid <?php echo $config['special_category_font_color'] ?>; 
        padding-right: 10px;
    }
    .exclusive-rightside{
        float: right;
        width: 30%;  
    } 
    .more-news{
        
   background: <?php echo $config['special_category_font_color'] ?>;
width: 100%;
height: 30px;
display: block;
text-align: center;
margin: 0 auto;
padding-top: 5px;
color: <?php echo $config['special_category_background_color'] ?>;
font-weight: bold;
font-size: 20px;
    }
    .ex-right-content ul li{
        list-style: none;
    }

    .ex-right-content ul li {
        list-style: none;
        padding: 10px 0;
        border-bottom: 1px solid <?php echo $config['special_category_font_color'] ?>;
    } .ex-right-content ul li:last-child{ 
        border-bottom: 0px;
    }
     .ex-right-content .alignleft
     {
         float: left;
         margin-right: 10px;
     }


</style>
