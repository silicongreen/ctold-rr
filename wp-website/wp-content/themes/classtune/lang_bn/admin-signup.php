<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Classtune
 * @since Classtune
 */
/*
  Template Name: school-admin-signup-bn
 */
get_header();

$school_type = $_GET['local'];
$school_type = ($school_type != 'premium') ? 'free' : 'paid';
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main" style="margin-top: 120px;">
        <iframe id="iframe_change_height" src="http://wp.classtune.com/createschool/userregister/<?php echo $school_type; ?>?headless=1" style="border:0" style="border:0;" width="100%" scrolling="no"></iframe>
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
