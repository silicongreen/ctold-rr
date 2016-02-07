<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
/*
  Template Name: user-register-bn
 */
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <div class="item content" id="content_section26">
            <div class="wrapper grey" >

                <div class="container">
                    <div class="col-md-12"  style="padding:0px; margin-top: 120px; margin-bottom:30px;">
                        <div style="margin:30px 100px; float:left; width:80%;background: white; ">

                            <div class="col-md-12"  style="padding:0px; ">
                                <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                                    <iframe id="iframe_change_height"  src="http://www.champs21.com/front/paid/select_school?back_url=<?php echo get_site_url(); ?>&user_type=<?php echo $_GET['user_type'];?>" style="border:1px solid white" style="border:0;" width="100%" scrolling="no"></iframe>
                                </h2>
                            </div>



                            <div class="col-md-12">


                            </div><!-- /.col-md-5 -->

                        </div> 
                    </div>      

                </div><!-- /.container -->

            </div><!-- /.wrapper -->
        </div>
        <style>
            .form-horizontal input
            {
                margin-top:0px;
            }
            .error
            {
                color:red;
            }

        </style>



    </main><!-- .site-main -->


</div><!-- .content-area -->

<script src="<?php bloginfo('template_url'); ?>/js/iframe-resizer/iframeResizer.min.js?v=1"></script>
<script type="text/javascript">



    iFrameResize({
        log: true, // Enable console logging
        inPageLinks: true,
        checkOrigin: false,
        bodyMargin: 20
    });

</script>

<?php get_footer('other'); ?>
