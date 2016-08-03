<?php
    $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; 

    $b_checked_cache = FALSE;
    if ( ( list($i_type_id_cache, $i_category_id_cache, $s_category_name_cache) = get_category_type(sanitize($ci_key)) ) ) 
    {
       $b_checked_cache = TRUE; 
    }
    
    if (isset($_GET['archive']) &&  strlen($_GET['archive']) != "0")
    {
        $b_checked_cache = FALSE;
    }
    $CI = & get_instance();
    $CI->load->driver('cache',array('adapter' => 'file'));
    $cache_name = "INNER_CONTENT_CACHE_MAG_".$i_category_id_cache."_". str_replace(":", "-",  str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");
    
    ob_start();
    
?>

   <!--  Start sports-other-news --> 
    <?php //array_shift($data[ 'common' ]);?>
    <input type="hidden" id="is_inner_page" value="true" />
<div class="ym-gbox magazin-content">    
    
    <?php
        if(count($data['common']) > 0){
            $i = 0;
            foreach($data['common'] as $row){
                
                if($ci_key != 'shout'){
                    if($i == 2){
                        break;
                    }
                }else{
                    if($i == 3){
                        break;
                    }
                }
                
            ?>
                <div class="ym-g80 magazin-main-news" <?php echo ($i == 0) ? 'style="border: none;"' : ''; ?>>
                    <?php echo show_magazine_image($row, array('width' => 330), 'floatLeft');?>
                    <div class="magazin-main-news-article">
                        <?php if(!empty($row->shoulder)): ?>
                            <h2><?php echo $row->shoulder; ?></h2>
                        <?php endif;?>
                        <a style="background: none;" href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><h1><?php echo $row->headline; ?></h1></a>
                        <?php if(!empty($row->sub_head)): ?>
                            <h3><?php echo $row->sub_head;?></h3>
                        <?php endif;?>
                        <?php echo print_magazine_news($ci_key, $row, false, false, 20);?>
                    </div>
                </div>
            <?php
                $i++;
                array_shift($data[ 'common' ]);
                
            }
        }
    ?>
        
        <div class="ym-g80 magazin-main-news">
            <div class="magazin-main-news-gallary">
                
                <!-- Gallery -->
                    <?php
                        
                        $ar_extra_gallery_config = $CI->config->config['magazin_extra_gallery_' . $ci_key];
                        
                        if(($ci_key == 'shout') && (!empty($data[$ar_extra_gallery_config['name']]))):
                            
                            $row = $data[$ar_extra_gallery_config['name']][0];        
                    ?>
                            <div class="media-header">
    
                                <div class="media-title"> 
                                    <h1> <?php echo $row->shoulder; ?> </h1>
                                </div>
                            
                            </div>
                            
                            <div class="ym-grid media-content-gallery mtab-0">
                                <div style="text-align:center;">
                                    <div style="display: none;" class="mediahtml5gallery" data-skin="vertical" data-showcarousel="false" data-width="630" data-height="298" data-showtitle="true" data-showsocialmedia="false"
                                         data-titleoverlay="true" data-titleautohide="true" data-socialurlforeach="true" data-bgcolor="#9F9F9F"
                                         data-resizemode="fill" >
                        
                                        <?php foreach($row->all_image as $image): ?>
                                        <?php
                                        
                                            $s_thumb_image = $image;
                                            
                                            list($width, $height, $type, $attr) = @getimagesize($s_thumb_image);
                                            if(!isset($width))
                                            {
                                               $s_thumb_image =  $image;
                                            }
                                            
                                        ?>
                                        <a href="<?php echo $image; ?>"><img src="<?php echo $s_thumb_image?>" alt="" /></a>
                                        <?php endforeach; ?>
                                        <!-- Add images to Gallery -->
                                    </div>
                                    
                                    <?php if($ci_key == 'shout'):?>
                                        <div class="content-news-body"><a href="<?php echo create_link_url($ci_key, $row->headline, $row->id);?>"><?php echo print_magazine_news($ci_key, $row, false, false, 30);?></a></div>
                                    <?php endif;?>
                                    
                                </div>
                            </div>
                            
                        <?php elseif(($ci_key == 'showbiz') && (!empty($data[$ar_extra_gallery_config['name']]))): ?>
                            <div class="media-header">
    
                                <div class="media-title"> 
                                    <h1> <?php echo 'Film Review'; ?> </h1>
                                </div>
                            
                            </div>
                            
                            <div class="ym-grid media-content-gallery mtab-0">
                                <div style="text-align:center;">
                                    <div style="display: none;" class="mediahtml5gallery" data-skin="vertical" data-showcarousel="false" data-width="630" data-height="298" data-showtitle="true" data-showsocialmedia="false"
                                         data-titleoverlay="true" data-titleautohide="true" data-socialurlforeach="true" data-bgcolor="#9F9F9F" data-onchange="onSlideChange" 
                                         data-resizemode="fill" >
                        
                                        <?php foreach($data[$ar_extra_gallery_config['name']] as $row): ?>
                                        <?php
                                        
                                            $s_thumb_image = $row->image;
                                            
                                            list($width, $height, $type, $attr) = @getimagesize($s_thumb_image);
                                            if(!isset($width))
                                            {
                                               $s_thumb_image =  $row->image;
                                            }
                                            
                                        ?>
                                        <a href="<?php echo $row->image; ?>"><img src="<?php echo $s_thumb_image?>" alt="" /></a>
                                        <?php endforeach; ?>
                                        <!-- Add images to Gallery -->
                                    </div>
                                    
                                    <?php
                                        $c = 0;
                                        $style = 'display: none;';
                                        foreach($data[$ar_extra_gallery_config['name']] as $row):
                                          if($i == 0){
                                            $style = 'display: block;';
                                          }  
                                    ?>
                                        <div class="content-news-body" id="<?php echo $c; ?>" style="<?php echo $style; ?>">
                                            <a href="<?php echo create_link_url($ci_key, $row->headline, $row->id);?>"><h1><?php echo trim($row->headline); ?></h1></a>
                                            <a href="<?php echo create_link_url($ci_key, $row->headline, $row->id);?>"><?php echo print_magazine_news($ci_key, $row, false, false, 30);?></a>
                                        </div>
                                    <?php $c++; endforeach; ?>
                                    
                                </div>
                            </div>
                            
                    <?php else: ?>
                        <?php show_media_gallery($ci_key, 'middle', 'top'); ?>
                    <?php endif; ?>
                    
                <!-- Gallery -->
                
            </div>
        </div>
      
        <?php
            if(count($data['common']) > 0){
                $j = 0;
                foreach($data['common'] as $row){
                    
//                    if( ( (stripos($row->category_id_string, ',226')) || (stripos($row->category_id_string, '226')) || (stripos($row->category_id_string, '226,')) ) && ($ci_key == 'shout')){
//                        break;
//                    }
//                    
//                    if( (stripos($row->category_id_string, ',227')) || (stripos($row->category_id_string, '227,')) || (stripos($row->category_id_string, '227')) && ($ci_key == 'shout')){
//                        break;
//                    }
//                    
//                    if( (stripos($row->category_id_string, ',228')) || (stripos($row->category_id_string, '228,')) || (stripos($row->category_id_string, '228')) && ($ci_key == 'shout')){
//                        break;
//                    }
                    
                ?>
                    <div class="ym-g80 magazin-main-news">
                        <?php echo show_magazine_image($row, array('width' => 330), 'floatLeft');?>
                        <div class="magazin-main-news-article">
                            <?php if(!empty($row->shoulder)): ?>
                                <h2><?php echo $row->shoulder; ?></h2>
                            <?php endif;?>
                            <a style="background: none;" href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><h1><?php echo $row->headline; ?></h1></a>
                            <?php if(!empty($row->sub_head)): ?>
                                <h3><?php echo $row->sub_head;?></h3>
                            <?php endif;?>
                            <?php echo print_magazine_news($ci_key, $row, false, false, 20);?>
                        </div>
                    </div>
                <?php
                    $j++;
                    
                    array_shift($data[ 'common' ]);
                    
                    if(($j == 3) && ($ci_key == 'the-star')){
                        break;
                    }
                    
                    if((sizeof($data[ 'common' ]) <= 4) && ($ci_key == 'lifestyle')){
                        break;
                    }
                    
                    if((sizeof($data[ 'common' ]) <= 6) && ($ci_key == 'showbiz')){
                        break;
                    }
                    
                }
            }
        ?>

      <div class="ym-g80 magazin-main-news magazin-main-news-dd">
        
         <dl class="magazin-main-news-list">
            <?php
                if(($ci_key !== 'shout') && ($ci_key !== 'showbiz')){
                    if(count($data['common']) > 0){
                        $k = 0;
                        foreach($data['common'] as $row){
                            
                            if(($k == 4) && ($ci_key !== 'the-star')){
                                break;
                            }
                        ?>
                            <dd>
                                <?php if(!empty($row->shoulder)): ?>
                                    <h2><?php echo $row->shoulder; ?></h2>
                                <?php endif;?>
                                <?php if(!empty($row->image)): ?>
                                    <?php echo show_magazine_image($row, array('width' => 305, 'height' => 230), 'imgLiquidFill imgLiquid', false, true);?>
                                <?php endif;?>
                                <a style="background: none;" href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><h1><?php echo $row->headline; ?></h1></a>
                                <?php if(!empty($row->sub_head)): ?>
                                    <h3><?php echo $row->sub_head;?></h3>
                                <?php endif;?>
                                <?php echo print_magazine_news($ci_key, $row, false, false, 13);?>
                            </dd>
                        <?php
                            $k++;
                            array_shift($data[ 'common' ]);
                            
                            
                        }
                    }
                }
            ?>
            
            
            <?php $ar_key = $CI->config->config['extra_category_' . $ci_key]; ?>
                
                <?php foreach($ar_key as $key): ?>
                    
                    <?php if(sizeof($data[$key['name']]) > 0): ?>
                
                        <dd>
                            <?php $o = 0; foreach($data[$key['name']] as $row): ?>
                                
                                <?php if($key['name'] == 'science_news'): ?>
                                    <h2>SCIENCE</h2>
                                <?php endif; ?>
                                
                                
                                <?php
                                    if($o == $key['number_of_news']){
                                        break;
                                    }
                                ?>
                                
                                <div class="magazin-main-news-list-content">
                                   <?php $row->image = (!empty($row->image)) ? $row->image : $no_image; ?>
                                   <?php $size = 45; if($ci_key == 'showbiz'){$size = 55; }?>
                                   <?php echo show_magazine_image($row, array('height' => $size), 'floatLeft'); ?>
                                  <div class="magazin-main-news-list-content-sub">
                                     <a <?php echo ($ci_key == 'lifestyle') ? 'style="background: none;" ' : ''; ?> href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><?php echo $row->headline; ?></a>
                                  </div>
                                
                               </div>
                                
                            <?php $o++; endforeach; ?>
                      </dd>
                      
                  <?php endif; ?>
                  
                <?php endforeach; ?>
                <?php //exit;?>
                
            
            <?php if(($ci_key !== 'shout') && (count($data['common']) > 0)){?>
            <dd class="last">
               <?php if(count($data['common']) > 0){ ?>
                    
                    <?php if($ci_key == 'lifestyle'): ?>
                        <h2>NEWS FLASH</h2>
                    <?php endif; ?>
                    
                    <?php
                        $l = 0;
                        foreach($data[ 'common' ] as $row){
                            
                            if( ($l == 4) && ($ci_key == 'lifestyle')){
                                break;
                            }
                            
                            if( ($l == 2) && ($ci_key == 'showbiz')){
                                break;
                            }
                            
                    ?>
                    
                       <div class="magazin-main-news-list-content">
                           <?php $row->image = (!empty($row->image)) ? $row->image : $no_image; ?>
                           <?php $size = 45; if($ci_key == 'showbiz'){$size = 60; }?>
                           <?php echo show_magazine_image($row, array('height' => $size), 'floatLeft'); ?>
                          <div class="magazin-main-news-list-content-sub">
                             <a <?php echo ($ci_key == 'lifestyle') ? 'style="background: none;" ' : ''; ?> href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><?php echo $row->headline; ?></a>
                          </div>
                        <?php
                            if($ci_key == 'showbiz'){
                                echo print_magazine_news($ci_key, $row, false, false, 5);
                            }
                        ?>
                       </div>
                        <?php
                            $l++;
                            array_shift($data[ 'common' ]);
                        }
                    }
                ?>
            </dd>
            
            <?php if($ci_key !== 'the-star'): ?>
                <dd class="last">
                   
                   <?php if(count($data['common']) > 0){ ?>
                        
                        <?php if($ci_key == 'lifestyle'): ?>
                            <h2>CHECK IT OUT</h2>
                        <?php endif; ?>
                        
                        <?php
                            $m = 0;
                            foreach($data[ 'common' ] as $row){
                                if( ($m == 3) && ($ci_key == 'lifestyle')){
                                    break;
                                }
                        ?>
                           <div class="magazin-main-news-list-content">
                               <?php $row->image = (!empty($row->image)) ? $row->image : $no_image; ?>
                               <?php $size = 45; if($ci_key == 'showbiz'){$size = 100; }?>
                               <?php echo show_magazine_image($row, array('height' => $size), 'floatLeft'); ?>
                              <div class="magazin-main-news-list-content-sub">
                                 <a <?php echo ($ci_key == 'lifestyle') ? 'style="background: none;" ' : ''; ?>href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><?php echo $row->headline; ?></a>
                              </div>
                           </div>
                            <?php
                                $m++;
                                array_shift($data[ 'common' ]);
                            }
                        }
                    ?>
                    
                    <?php if($ci_key == 'lifestyle'): ?>
                    
                       <div style="clear:both;border-top:1px solid #E0DFDF;margin: 20px 0px;"></div>   
                        
                        <?php if(count($data['common']) > 0){?>
                            <h2>POP UP</h2>
                       
                            <?php
                                $n = 0;
                                foreach($data[ 'common' ] as $row){
                                    
                                    if( ($n == 2) && ($ci_key == 'lifestyle')){
                                        break;
                                    }
                                ?>
                               <div class="magazin-main-news-list-content">
                                   <?php $row->image = (!empty($row->image)) ? $row->image : $no_image; ?>
                                   <?php echo show_magazine_image($row, array('height' => 45), 'floatLeft'); ?>
                                  <div class="magazin-main-news-list-content-sub">
                                     <a style="background: none;" href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><?php echo $row->headline; ?></a>
                                  </div>
                               </div>
                                <?php
                                    $n++;
                                    array_shift($data[ 'common' ]);
                                }
                            }
                        ?>
                                            
                    <?php endif; ?>
    
                </dd>
            <?php endif; ?>
        <?php } ?>

         </dl>


      </div>
      
      <?php if(($ci_key == 'shout') || ($ci_key == 'showbiz')):?>
        <div class="ym-g80 magazin-main-news">
            <div class="magazin-main-news-gallary">
                <!-- Gallery -->
                <?php show_media_gallery($ci_key, 'middle', 'top'); ?>
                <!-- Gallery -->
            </div>
        </div>
        
      <?php endif; ?>

   </div>                                      

   <!--  End sports-other-news --> 
<?php 
    $s_inner_content = ob_get_contents();
    ob_end_clean(); 
    if ( $b_checked_cache && ! isset($_GET['archive'])  )
    {
        $CI->cache->file->save($cache_name, $s_inner_content, 86400);
    }
    echo $s_inner_content;
  
?>
