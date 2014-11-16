
<div class="ym-gbox sports-inner-news">   
    <?php if(isset($full_custom) && $full_custom==1):?>
        <?php echo $link_text;?>
    <?php else: ?>
    <?php if(isset($image) && $image!="" && $image!=NULL): ?>
    <div class="icon_image_div"><img src="<?php echo base_url().$image?>" alt="<?php echo $title; ?>" /></div>
    <?php else: ?>
    <h1 class="<?php echo strtolower($title); ?> title"><?php echo $title; ?></h1>
    <?php endif; ?>
    
     <?php 
        $extra_css_inner = "";
        if(isset($has_right) && $has_right==0)
        {
           $extra_css_inner = "style='padding: 14px 9px;width: 98%;'";

        } 
     ?>
    
    <div class="sports-inner-container" <?php echo $extra_css_inner; ?>>
        <div id="content" class="content-post">
            <p style="padding: 1px;">
             <?php echo $link_text;?>
            </p>
        </div>
    </div>
    <?php endif; ?>

</div>