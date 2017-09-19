<?php get_header(); ?>

<?php $post_id = get_the_ID(); ?> 

<!--get all datas-->
<?php $all_course_datas = redux_post_meta( 'redux_demo', $post_id ); ?>

<?php include 'include/course/header-image-courses.php'; ?>

<?php $nicdark_courselayout = $all_course_datas['layout_courses']; ?>

<!--FULL WIDTH PAGE-->
<?php if ($nicdark_courselayout == 0) { ?>

    <!--start nicdark_container-->
    <div class="nicdark_container nicdark_clearfix">

    <?php if(have_posts()) :
        while(have_posts()) : the_post(); ?>
            
            <!--#post-->
            <div style="float:left; width:100%;" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <!--start content-->
                <p><?php the_content(); ?></p>
                <!--end content-->
                
            </div>
            <!--#post-->
        
        <?php endwhile; ?>
    <?php endif; ?>

    </div>
    <!--end container-->

<?php } ?>


<!--RIGHT SIDEBAR PAGE PAGE-->
<?php if ($nicdark_courselayout == 1) { ?>

    <?php if(have_posts()) :
        while(have_posts()) : the_post(); ?>

            <div class="nicdark_space60"></div>
            <section class="nicdark_section">
                <div class="nicdark_container nicdark_clearfix">

                    <div class="grid grid_8 percentage nicdark_page_sidebar"><p><?php the_content(); ?></p></div>
                    <div class="grid grid_4 percentage  nicdark_sidebar"><?php if ( ! dynamic_sidebar( ''.$redux_demo['metabox_course_sidebar'].'' ) ) : ?><?php endif ?></div>
                
                </div>
            </section>
            <div class="nicdark_space50"></div>

        <?php endwhile; ?>
    <?php endif; ?>

<?php } ?>


<!--LEFT SIDEBAR PAGE PAGE-->
<?php if ($nicdark_courselayout == 2) { ?>

    <?php if(have_posts()) :
        while(have_posts()) : the_post(); ?>

            <div class="nicdark_space60"></div>
            <section class="nicdark_section">
                <div class="nicdark_container nicdark_clearfix">

                    <div class="grid grid_4 percentage  nicdark_sidebar"><?php if ( ! dynamic_sidebar( ''.$redux_demo['metabox_course_sidebar'].'' ) ) : ?><?php endif ?></div>
                    <div class="grid grid_8 percentage nicdark_page_sidebar"><p><?php the_content(); ?></p></div>
                    
                </div>
            </section>
            <div class="nicdark_space50"></div>

        <?php endwhile; ?>
    <?php endif; ?>

<?php } ?>
        


<?php get_footer(); ?>