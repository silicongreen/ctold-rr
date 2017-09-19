<?php

$post_id = get_the_ID();

//image src
$attachment_id = get_post_thumbnail_id( $post_id );
$image_attributes = wp_get_attachment_image_src( $attachment_id, 'large' );
$outputimagesrc = '<img alt="" src="'.$image_attributes[0].'">';


//link
if ($redux_demo['metabox_excursion_linkurl'] == '') {

    $permalink = get_permalink( $post_id );

}else{

    $permalink = $redux_demo['metabox_excursion_linkurl'];

}


//if button
if ($redux_demo['metabox_excursion_linktitle'] == '') {

    $outputbutton = '';

}else{

    $outputbutton = '<div class="nicdark_space20"></div><a href="'.$permalink.'" class="white nicdark_btn nicdark_bg_'.$redux_demo['metabox_excursion_color'].' medium nicdark_radius nicdark_shadow nicdark_press">'.$redux_demo['metabox_excursion_linktitle'].'</a>';

}


//location hour date if
$outputhour = ( $redux_demo['metabox_excursion_hour'] != '' ) ? ' <a title="'.$redux_demo['metabox_excursion_hour'].'" href="'.$permalink.'" class="nicdark_tooltip nicdark_btn_icon nicdark_bg_greydark white medium nicdark_radius_circle nicdark_absolute_left"><i class="icon-clock"></i></a> ' : '';
$outputlocation = ( $redux_demo['metabox_excursion_location'] != '' ) ? ' <a title="'.$redux_demo['metabox_excursion_location'].'" href="'.$permalink.'" class="nicdark_tooltip nicdark_btn_icon nicdark_bg_greydark white medium nicdark_radius_circle nicdark_absolute_right"><i class="icon-location-outline"></i></a> ' : '';
$outputdate = ( $redux_demo['metabox_excursion_date'] != '' ) ? ' <div class="nicdark_textevidence nicdark_bg_'.$redux_demo['metabox_excursion_color'].' center"><h5 class="white nicdark_margin20">'.$redux_demo['metabox_excursion_date'].'</h5><i class="'.$redux_demo['metabox_excursion_icon'].' nicdark_iconbg right medium '.$redux_demo['metabox_excursion_color'].'"></i></div> ' : ''; 

?>

<!--prevew-->
<div class="grid grid_3 nicdark_masonry_item">
        
    <div class="nicdark_archive1 nicdark_bg_grey nicdark_radius nicdark_shadow">

        <?php  
            echo $outputhour;
            echo $outputlocation;
        ?>

        <?php echo $outputimagesrc; ?>

        <div class="nicdark_textevidence nicdark_bg_greydark center">
            <h4 class="white nicdark_margin20"><?php the_title(); ?></h4>
        </div>

        <?php echo $outputdate; ?>
        
        <div class="nicdark_textevidence center">
            <div class="nicdark_margin20">
                <p><?php the_excerpt(); ?></p>
                <?php echo $outputbutton; ?>
            </div>
        </div>

    </div>

</div>
<!--prevew-->