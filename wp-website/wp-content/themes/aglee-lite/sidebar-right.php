<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Aglee Lite
 */

if ( ! is_active_sidebar( 'aglee_right_sidebar' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'aglee_right_sidebar' ); ?>
</div><!-- #secondary -->
