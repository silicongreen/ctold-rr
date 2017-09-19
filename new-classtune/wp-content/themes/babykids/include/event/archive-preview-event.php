<?php

$post_id = get_the_ID();

//link
if ($redux_demo['metabox_event_linkurl'] == '') {
    $permalink = get_permalink( $post_id );
}else{
    $permalink = $redux_demo['metabox_event_linkurl'];
}

//get date info
$eventdate = new DateTime($redux_demo['metabox_event_date']);
$neweventdate = ( $redux_demo['metabox_event_date'] != '' ) ? ' <a href="'.$permalink.'" class="nicdark_btn nicdark_bg_greydark white medium nicdark_radius nicdark_absolute_left">'.$eventdate->format('j').'<br><small>'.$eventdate->format('M').'</small></a> ' : '';

//image src
$attachment_id = get_post_thumbnail_id( $post_id );
$image_attributes = wp_get_attachment_image_src( $attachment_id, 'large' );
$outputimagesrc = ( $image_attributes[0] != '' ) ? ' '.$neweventdate.'<img alt="" src="'.$image_attributes[0].'"> ' : '';

//if button
if ($redux_demo['metabox_event_linktitle'] == '') {
    $outputbutton = '';
}else{
    $outputbutton = '<div class="nicdark_space20"></div><a href="'.$permalink.'" class="white nicdark_btn nicdark_bg_'.$redux_demo['metabox_event_color'].'dark medium nicdark_radius nicdark_shadow nicdark_press">'.$redux_demo['metabox_event_linktitle'].'</a>';
}

//location hour date if
$outputlocation = ( $redux_demo['metabox_event_location'] != '' ) ? ' <h5 class="white"><i class="icon-pin-outline"></i> '.$redux_demo['metabox_event_location'].'</h5> ' : '';
$outputhour = ( $redux_demo['metabox_event_hour'] != '' ) ? ' <div class="nicdark_space10"></div><h5 class="white"><i class="icon-clock-1"></i> '.$redux_demo['metabox_event_hour'].'</h5> ' : '';
$outputdate = ( $redux_demo['metabox_event_date'] != '' ) ? ' <div class="nicdark_space10"></div><h5 class="white"><i class="icon-calendar"></i> '.$redux_demo['metabox_event_date'].'</h5> ' : '';

?>

<!--prevew-->
<div class="grid grid_3 nicdark_masonry_item">

    <!--archive1-->
    <div class="nicdark_archive1 nicdark_bg_<?php echo $redux_demo['metabox_event_color']; ?> nicdark_radius nicdark_shadow">

        <?php echo $outputimagesrc; ?>
        
        <div class="nicdark_textevidence nicdark_bg_greydark">
            <h4 class="white nicdark_margin20"><?php the_title(); ?></h4>
        </div>
        
        <div class="nicdark_margin20 nicdark_event_archive">
           
            <?php 
                echo $outputlocation;
                echo $outputhour;
                echo $outputdate;
            ?>

            <div class="nicdark_space20"></div>
            <div class="nicdark_divider left small"><span class="nicdark_bg_white nicdark_radius"></span></div>
            <div class="nicdark_space20"></div>
            <p class="white"><?php the_excerpt(); ?></p>
            <?php echo $outputbutton; ?>   
         </div>

    </div>
    <!--archive1-->

</div>
<!--prevew-->