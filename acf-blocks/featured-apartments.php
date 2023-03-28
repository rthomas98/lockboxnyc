
<div class="container">
    <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="row g-3 d-flex align-items-end">

        <input type="hidden" name="post_type" value="apartment">

        <div class="form-group col-auto">
            <label for="zip_code">Zip code:</label>
            <input type="text" name="zip_code" id="zip_code" class="form-control" placeholder="Enter zip code">
        </div>

        <div class="form-group col-auto">
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" class="form-control" placeholder="Enter address">
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary m-0">Search Apartments</button>
        </div>
    </form>
</div>


<section class="featured-apartment py-5">
    <div class="container">

        <div class="row mb-4">
            <div class="col">
                <p class="lead">
                    <?php the_sub_field( 'sub_title' ); ?>
                </p>
                <h2>
                    <?php the_sub_field( 'title' ); ?>
                </h2>
            </div>
        </div>

        <?php
        // Query for 3 featured "Apartment" CPTs
        $args = array(
            'post_type' => 'apartment',
            'posts_per_page' => 3, // Display 3 posts
            'post_status' => 'publish',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'featured_post',
                    'value' => '1',
                    'compare' => '=',
                ),
            ),
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<div class="row">'; // Start the div container

            while ($query->have_posts()) {
                $query->the_post();
                ?>

                <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <div class="small-apartment">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="apartment-img mb-4">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('featured-apartment'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <div class="apartment-content">
                                <h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                                <p>
                                    <span>
                                        <i class="fa-regular fa-bed-front"></i> <?php the_field( 'bedrooms' ); ?> Bedrooms
                                    </span>
                                    <span>
                                        <i class="fa-regular fa-shower"></i> <?php the_field( 'bathrooms' ); ?> 1.5 Bath
                                    </span>
                                </p>
                            </div>
                        </div>
                    </article><!-- #post-<?php the_ID(); ?> -->
                </div>

                <?php
            }
            echo '</div>'; // End the div container

            wp_reset_postdata(); // Reset the post data
        } else {
            ?>
            <p>No Featured Apartments found.</p>
            <?php
        }
        ?>

    </div>

    </div>
</section>