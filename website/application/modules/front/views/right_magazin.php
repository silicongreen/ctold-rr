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
    $cache_name = "INNER_CONTENT_CACHE_MAG_RIGHT_".$i_category_id_cache."_". str_replace(":", "-",  str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");
    
    ob_start();
    
?>

<div class="ym-gbox right-content">
    
    <dl>
        <dd>
            <?php
                $widget = new Widget;
                $widget->run( 'morenewsblock', $ci_key, true );
            ?>
        </dd>
       
            <?php if( ($ci_key == 'showbiz') && (!empty($magazine_right_data['events']) )): ?>
                     <dd>
                    <h2>EVENTS</h2>
                        <ul>
                        <?php
                            $n = 0;
                            foreach($magazine_right_data['events'] as $row){
                            ?>
                            <li>
                               <div class="magazin-main-news-list-content-list">
                                   <?php $row->image = (!empty($row->image)) ? $row->image : $page_icon; ?>
                                   <?php if(!empty($row->channel_id)): ?>
                                        <h2><?php echo $row->channel_id; ?></h2>
                                    <?php endif;?>
                                   <?php echo show_magazine_image($row, array('height' => 60), 'floatLeft'); ?>
                                  
                                  <?php echo print_magazine_news($ci_key, $row, false, false, 5);?>
                               </div>
                               </li>
                            <?php
                                $n++;
                            }
                        ?>
                    </ul>
                    </dd> 
            <?php endif; ?>
            
            <?php if( ($ci_key == 'lifestyle') || ($ci_key == 'the-star') ): ?>
             <dd>
                <?php
                    
                    $s_published_date = date('Y-m-d', strtotime($magazine_right_data['issue_date']['s_issue_date']));
                    $i_category_id = $magazine_right_data['i_category_id'];
                    $i_category_type_id = $magazine_right_data['i_category_type_id'];
                    $s_category_name = $magazine_right_data['s_category_name'];
                    
                    echo show_magazine_image((int)$i_category_id, array('width' => 304), null, false, false, true);
                ?>
                <!-- Add -->
                    <img src="images/magazin/add-berger.png" alt="Right site adds one" />
                <!-- Add -->
                
                <?php echo show_download($i_category_id, $s_published_date, $s_category_name, $ci_key, $i_category_type_id); ?>
             </dd>
            <?php endif; ?>
            
            <?php if( ($ci_key == 'the-star') && (!empty($magazine_right_data['voices']) )): ?>
            
                <dd class="whatson">
                     <div class="whatson_div">
                     
                        <div class="buletin-news">
                            <img src="images/magazin/voice_box.png" alt="Voice Box" />
                        </div>
                        
                        <ul class="bxslider4">
                            <?php
                                $n = 0;
                                foreach($magazine_right_data['voices'] as $row){
                                ?>
                                <li>            
                                   <div class="watch-container" style="padding: 20px;overflow: hidden;">
                                       <div>
                                            <img src="images/magazin/code.png" />
                                                <p><?php echo $row->voice; ?></p>
                                            <img src="images/magazin/un-code.png" />
                                       </div>
                                       
                                       <?php if(!empty($row->personality_name)): ?>
                                            <div class="details"><?php echo '--' . $row->personality_name; ?></div>
                                       <?php endif;?>
                                       
                                       <?php if(!empty($row->personality_description)): ?>
                                             <p class="channel"><?php echo $row->personality_description.' '.$row->topic; ?></p>
                                        <?php endif;?>
                                   </div>            
                                </li>

                                <?php
                                    $n++;
                                }
                            ?>
                        </ul>
                     </div>
                </dd>
        <?php endif; ?>
            
        <?php if(isset($magazine_right_data['common_widget'])) foreach($magazine_right_data['common_widget'] as $value): ?>
           
        <?php if(isset($magazine_right_data[$value['name']]) && count($magazine_right_data[$value['name']]) > 0  ) : ?>
            <dd>
                
                <?php if($value['number_of_news']==1): ?>
                    <?php if(isset($value['top_image']) && $value['top_image']): ?>
                            <div class="top-image">
                                <img src="images/magazin/<?php echo $value['top_image'];?>.png" alt="Buletin" />
                            </div>
                    <?php endif; ?>
                    
                    <?php $i=0; foreach($magazine_right_data[$value['name']] as $row): ?>
                        <?php foreach($value['field'] as $fields): ?>
                            <?php if($fields == 'shoulder'): ?>
                                <?php if(!empty($row->shoulder)): ?>
                                    <h2><?php echo $row->shoulder; ?></h2>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if($fields == 'image'): ?>
                                <a style="background: none;" href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><?php echo show_magazine_image($row, array('width' => $value['image_size']),$value['image_class']);?></a>
                            <?php endif; ?>
                            <?php if($fields == 'headline'): ?>
                                <a class="untranslatable" style="background: none;" href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><h1><?php echo $row->headline; ?></h1></a>
                            <?php endif; ?>
                            <?php if($fields == 'news'): ?>
                                <a style="background: none;" href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><?php echo print_magazine_news($ci_key, $row);?></a>
                            <?php endif; ?>
                        <?php  endforeach; ?>
                       
                    <?php $i++; if($i >= $value['number_of_news']) break; ?> 
                    <?php endforeach; ?>
                    
                <?php else: ?>
                    <div class="buletin_div" >
                        <?php if(isset($value['top_image']) && $value['top_image']): ?>
                        <div class="buletin-news">
                            <img src="images/magazin/<?php echo $value['top_image'];?>.png" alt="Buletin" />
                        </div>
                        <?php endif; ?>
                        
                        <?php
                            $list_type = 'ul';
                            if($value['name'] == 'letters'){
                                $list_type = 'ul';
                            }
                        ?>
                        <<?php echo $list_type; ?>>
                            <?php foreach($magazine_right_data[$value['name']] as $row):?>
                                <li>
                                   <?php foreach($value['field'] as $fields): ?> 
                                    <?php if($fields == 'image'): ?>
                                        <?php echo show_magazine_image($row, array('width' => $value['image_size']),$value['image_class']);?>
                                    <?php endif; ?>
                                    <?php if($fields == 'headline'): ?>
                                       <a href="<?php echo create_link_url($ci_key, $row->headline, $row->id)?>"><?php echo $row->headline; ?></a>
                                    <?php endif; ?>
                                   <?php  endforeach; ?>
                                </li>
                            <?php endforeach;?>
                        </<?php echo $list_type; ?>>
                        
                        <?php if(($ci_key == 'the-star') && ($value['name'] == 'letters')):?>
                            <input type="button" class="submission-guideline"  />
                        <?php endif; ?>
                        
                    </div> 
                <?php endif; ?>
            </dd>
        <?php endif; ?>    
        <?php endforeach; ?>
        
            <?php if( ($ci_key == 'shout') || ($ci_key == 'showbiz') ): ?>
              <dd>      
                <?php    $s_published_date = date('Y-m-d', strtotime($magazine_right_data['issue_date']['s_issue_date']));
                    $i_category_id = $magazine_right_data['i_category_id'];
                    $i_category_type_id = $magazine_right_data['i_category_type_id'];
                    $s_category_name = $magazine_right_data['s_category_name'];
                    
                    echo show_magazine_image((int)$i_category_id, array('width' => 304), null, false, false, true);
            ?>
                <!-- Add -->
                    
                <!-- Add -->
                
                <?php echo show_download($i_category_id, $s_published_date, $s_category_name, $ci_key, $i_category_type_id); ?>
                 </dd>
            <?php endif; ?>
                 
            <?php if( ($ci_key == 'lifestyle') && (!empty($magazine_right_data['events']) )): ?>
                     <dd class="whatson">
                         <div class="whatson_div">
                            <h2>THANK GOD IT'S FRIDAY</h2>
                                <ul class="bxslider3">
                                <?php
                                    $n = 0;
                                    foreach($magazine_right_data['events'] as $row){
                                    ?>
                                    <li>            
                                       <div class="watch-container" style="padding: 20px;overflow: hidden;">

                                           <div><?php echo show_magazine_image($row, array('width' => 262), ''); ?></div>
                                           <?php if(!empty($row->channel_id)): ?>
                                                 <p class="channel"><?php echo $row->channel_id; ?></p>
                                            <?php endif;?>
                                           <div class="details"><?php echo print_magazine_news($ci_key, $row, false, false, 5);?></div>
                                       </div>            
                                    </li>

                                    <?php
                                        $n++;
                                    }
                                ?>
                            </ul>
                         </div>
                      </dd>
                 <?php endif; ?>          
            
             
        
        <!--dd>
            <a href="<?php //echo base_url() . $ci_key . '?archive=2014-02-19'; ?>"><img src="images/magazin/right-5.jpg" width="301" alt="Right site adds one" /></a>
        </dd-->
        
        <dd>
            <img src="images/magazin/right-6.jpg" alt="Right site adds one" />
        </dd>
        
        <?php if( $ci_key == 'lifestyle'):?>
            
            <dd>
                <!-- Add -->
                    <img src="images/magazin/right-7.jpg" alt="Right site adds one" />
                <!-- Add -->
            </dd>
            
            <dd>
                <!-- Add -->
                    <img src="images/magazin/right-8.jpg" alt="Right site adds one" />
                <!-- Add -->
            </dd>
        <?php endif; ?>
    </dl>
    
</div>

<?php 
    $s_inner_content = ob_get_contents();
    ob_end_clean(); 
    if ( $b_checked_cache && ! isset($_GET['archive'])  )
    {
        $CI->cache->file->save($cache_name, $s_inner_content, 86400);
    }
    echo $s_inner_content;
  
?>