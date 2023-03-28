<div class="container py-5">
    <?php
    // Query for "Apartment" CPT
    $args = array(
        'post_type' => 'apartment',
        'posts_per_page' => -1, // Display all posts
        'post_status' => 'publish',
        'order' => 'ASC'
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <?php the_excerpt(); ?>
                </div><!-- .entry-content -->
            </article><!-- #post-<?php the_ID(); ?> -->

            <?php
        }
        wp_reset_postdata(); // Reset the post data
    } else {
        ?>
        <p>No Apartments found.</p>
        <?php
    }
    ?>

</div>