<?php
/**
 * Single apartment partial template
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


        <?php
        $building = get_field('building'); // Replace 'building' with the field name you used to create the relationship between "Building" and "Apartment" CPTs
        if ($building) {
            $building_id = $building->ID ?? $building[0]->ID; // Check if the building is an object or an array, and get the ID accordingly
            $address = get_field('office_address', $building_id);
            if ($address) {
                echo '<p>Address: ' . $address . '</p>';
            }
        }
        ?>


        <?php
        $building = get_field('building'); // Replace 'building' with the field name you used to create the relationship between "Building" and "Apartment" CPTs
        if ($building) {
            $building_id = $building->ID ?? $building[0]->ID; // Check if the building is an object or an array, and get the ID accordingly
            $ll_code = get_field('ll_code', $building_id);
            if ($ll_code) {
                echo '<p>LL Code: ' . $ll_code . '</p>';
            }
        }
        ?>


    </div><!-- .entry-content -->

    <footer class="entry-footer">

        <?php understrap_entry_footer(); ?>

    </footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->