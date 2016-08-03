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
    $cache_name = "INNER_CONTENT_CACHE_SLIDE_".$i_category_id_cache."_". str_replace(":", "-",  str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");
    
    ob_start();
?>
<div class="ym-grid">
<div class="ym-grid header-magazine">
        
        <?php $page_icon = '/images/magazin/logo/' . $ci_key . '.png'; ?>
        
        <?php echo show_magazine_image($page_icon, array(), 'ym-g33 ym-gl', true);?>
       
       <div class="ym-g33 ym-gl">
          <p>Published:&nbsp;<?php echo date("F d, Y", strtotime($issue_date));?></p>
       </div>
                      
       <div class="ym-g33 ym-gl" style="height: 30px; margin-top: 30px;">
            <script>
                (function() {
                    var cx = '016729079030925130795:opp53e_l4vm';
                    var gcse = document.createElement('script');
                    gcse.type = 'text/javascript';
                    gcse.async = true;
                    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//www.google.com/cse/cse.js?cx=' + cx;
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(gcse, s);
                })();
            </script>
            <gcse:search></gcse:search>
       </div>
    </div>
</div>

<!-- Magazine Menu -->
    <?php echo magazine_menu($ci_key); ?>
<!-- Magazine Menu -->

<div class="ym-grid magazine_main_content">
<div class="ym-grid">

    <?php /*var_dump($magazine_cover_news);exit; */ if(isset($magazine_cover_news) && !empty($magazine_cover_news)):?>
    
    <div class="ym-grid gallery-contain">
        <div class="ym-column">
            <?php echo show_magazine_image($magazine_cover_news, array('width' => 614, 'height' => 517), 'ym-col1 gallery-content-left', true);?>   
            <div class="ym-col2 gallery-content-right">
                <?php if(!empty($magazine_cover_news->shoulder)):?>
                    <h2><?php echo $magazine_cover_news->shoulder;?></h2>
                <?php endif;?>
                <a style="background: none; " href="<?php echo create_link_url($ci_key, $magazine_cover_news->headline, $magazine_cover_news->id)?>"><h1><?php echo $magazine_cover_news->headline;?></h1></a>
                <h3><?php echo $magazine_cover_byline->title;?></h3>
                <?php echo print_magazine_news($ci_key, $magazine_cover_news, true, false, 35 );?>
            </div>
        </div>
    
        <!--span class="prev"></span>
        <span class="next"></span-->
    </div>
   <?php endif;?>
      
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