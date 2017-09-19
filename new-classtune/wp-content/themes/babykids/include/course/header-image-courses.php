<!--start header parallax image-->
<?php if ($all_course_datas['metabox_course_header_img_display'] == 1){ ?>

    <section id="nicdark_singlecourse_parallax" class="nicdark_section nicdark_imgparallax" style="background:url(<?php echo esc_url( $all_course_datas['metabox_course_header_img']['url'] ); ?>) 50% 0 fixed; background-size:cover;">

        <div class="nicdark_filter <?php echo $all_course_datas['metabox_course_header_filter']; ?>">

            <!--start nicdark_container-->
            <div class="nicdark_container nicdark_clearfix">

                <div class="grid grid_12">
                    <div class="nicdark_space<?php echo $all_course_datas['metabox_course_header_margintop']; ?>"></div>
                    <h1 class="white subtitle"><?php echo $all_course_datas['metabox_course_header_title']; ?></h1>
                    <div class="nicdark_space10"></div>
                    <h3 class="subtitle white"><?php echo $all_course_datas['metabox_course_header_description']; ?></h3>
                    <div class="nicdark_space20"></div>
                    <?php if ( $all_course_datas['metabox_course_header_divider'] == 1 ){ ?> <div class="nicdark_divider left big"><span class="nicdark_bg_white nicdark_radius"></span></div> <?php } ?>
                    <div class="nicdark_space<?php echo $all_course_datas['metabox_course_header_marginbottom']; ?>"></div>
                </div>

            </div>
            <!--end nicdark_container-->

        </div>
         
    </section>

    <script type="text/javascript">(function($) { "use strict"; $("#nicdark_singlecourse_parallax").parallax("50%", 0.3); })(jQuery);</script>

<?php }else{ ?>

    <div class="nicdark_space160"></div>

<?php } ?>
<!--end header parallax image-->