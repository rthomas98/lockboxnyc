<?php
/**
 * Template Name: Flexible Content Template
 *
 * Template for displaying a contact us page.
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );

while ( have_posts() ) {
    the_post();
    get_template_part( 'loop-templates/content', 'flexible-content' );
}



get_footer();