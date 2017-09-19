<?php get_header(); ?>

<div class="nicdark_space160"></div>

<section class="nicdark_section">
    <div class="nicdark_container nicdark_clearfix">
        <div class="grid grid_12">

            <?php $nicdark_404_message = __('Oops 404, That page can not be found','babykids'); ?>
            
            <div class="nicdark_alerts nicdark_bg_yellow nicdark_radius nicdark_shadow">
                <p class="white nicdark_size_big"><i class="icon-cancel-circled-outline iconclose"></i>&nbsp;&nbsp;&nbsp;<strong>404:</strong>&nbsp;&nbsp;&nbsp;<?php echo $nicdark_404_message; ?></p>
                <i class="icon-warning-empty nicdark_iconbg right big yellow"></i>
            </div>
                 
        </div>    
    </div>
</section>

<div class="nicdark_space50"></div>

<?php get_footer(); ?>