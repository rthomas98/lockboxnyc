<?php
/**
 * Single building partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <header class="entry-header">

        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>


    </header><!-- .entry-header -->

    <?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

    <div class="entry-content">

        <?php $checkboxes_for_building_amenities_checked_values = get_field( 'checkboxes_for_building_amenities' ); ?>
        <?php if ( $checkboxes_for_building_amenities_checked_values ) : ?>
        <ul>
            <?php foreach ( $checkboxes_for_building_amenities_checked_values as $checkboxes_for_building_amenities_value ): ?>
                <li>
                    <?php echo esc_html( $checkboxes_for_building_amenities_value ); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>



    </div><!-- .entry-content -->

    <footer class="entry-footer">

        <?php understrap_entry_footer(); ?>

    </footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->