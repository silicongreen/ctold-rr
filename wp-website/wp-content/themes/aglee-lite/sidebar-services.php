<?php
/**
 * The sidebar containing Services contents.
 *
 * @package Aglee Lite
 */
 
if ( ! is_active_sidebar( 'aglee_services_section' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'aglee_services_section' ); ?>
</div><!-- #secondary -->
