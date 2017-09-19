<?php 

$post_id = get_the_ID();

//image src
$attachment_id = get_post_thumbnail_id( $post_id );
$image_attributes = wp_get_attachment_image_src( $attachment_id, 'large' );
$outputimagesrc = 'background:url('.$image_attributes[0].');';

//if price and currency are set
if ($redux_demo['metabox_course_price'] == ''){ 
    $outputpricecurrency = '<div class="nicdark_space80"></div><div class="nicdark_space5"></div>'; 
}else{  
    $outputpricecurrency = '<a href="#" class="nicdark_zoom white nicdark_btn_icon nicdark_bg_greydark big nicdark_radius_circle">'.$redux_demo['metabox_course_price'].'<br><small>'.$redux_demo['metabox_course_currency'].'</small></a>';
}

//link
if ($redux_demo['metabox_course_linkurl'] == '') {

    $permalink = get_permalink( $post_id );

}else{

    $permalink = $redux_demo['metabox_course_linkurl'];

}


//if button
if ($redux_demo['metabox_course_linktitle'] == '') {

    $outputbutton = '';

}else{

    $outputbutton = '<div class="nicdark_space20"></div><a href="'.$permalink.'" class="white nicdark_btn nicdark_bg_'.$redux_demo['metabox_course_color'].' medium nicdark_radius nicdark_shadow nicdark_press">'.$redux_demo['metabox_course_linktitle'].'</a>';

}

?>

<!--prevew-->
<div class="grid grid_3 nicdark_masonry_item">
    <div class="nicdark_archive1 nicdark_bg_grey nicdark_radius nicdark_shadow center"> 
        <div style="<?php echo $outputimagesrc; ?> background-size:cover;" class="nicdark_archive1 nicdark_radius_top">
            <div class="nicdark_filter <?php echo $redux_demo['metabox_course_color']; ?> nicdark_radius_top">
                <div class="nicdark_space60"></div>
                <?php echo $outputpricecurrency; ?>
                <div class="nicdark_space60"></div>
            </div>
        </div>
        <div class="nicdark_textevidence nicdark_bg_greydark">
            <h4 class="white nicdark_margin20"><?php the_title(); ?></h4>
        </div>
        <div class="nicdark_margin20">
            <p><?php the_excerpt(); ?></p>
            
            <?php echo $outputbutton; ?>
        </div>
    </div>

</div>
<!--prevew-->