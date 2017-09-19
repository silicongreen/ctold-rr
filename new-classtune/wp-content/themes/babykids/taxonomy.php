<?php get_header(); ?>

<?php 

if (is_tax( 'typology-course' )){ include 'include/taxonomy-course.php'; } 
if (is_tax( 'typology-event' )){ include 'include/taxonomy-event.php'; } 
if (is_tax( 'typology-excursion' )){ include 'include/taxonomy-excursion.php'; } 

?>

<?php get_footer(); ?>