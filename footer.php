<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>

<?php get_template_part( 'sidebar-templates/sidebar', 'footerfull' ); ?>

<footer class="footer">
    <div class="container py-5">
        <?php if ( have_rows( 'newsletter', 'option' ) ) : ?>
        <div class="row d-flex align-items-center">
        <?php while ( have_rows( 'newsletter', 'option' ) ) : the_row(); ?>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                <p>
                    <?php the_sub_field( 'sub_title' ); ?>
                </p>
                <h2>
                    <?php the_sub_field( 'title' ); ?>
                </h2>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                <p>
                    <?php the_sub_field( 'notes' ); ?>
                </p>
                <?php the_sub_field( 'form' ); ?>
            </div>
        <?php endwhile; ?>
            <hr class="mt-3">
        </div>
        <?php endif; ?>
        <div class="row">
            <?php if ( have_rows( 'social_box', 'option' ) ) : ?>
                <?php while ( have_rows( 'social_box', 'option' ) ) : the_row(); ?>
            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                <h3><?php the_sub_field( 'sub_title' ); ?></h3>
                <p><?php the_sub_field( 'title' ); ?></p>

                <?php if ( have_rows( 'social_links' ) ) : ?>
                <ul class="nav">
                    <?php while ( have_rows( 'social_links' ) ) : the_row(); ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php the_sub_field( 'link' ); ?>">
                                <?php the_sub_field( 'icon' ); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php else : ?>
                    <?php // No rows found ?>
                <?php endif; ?>


            </div>
                <?php endwhile; ?>
            <?php endif; ?>

            <?php if ( have_rows( 'company_info', 'option' ) ) : ?>
            <?php while ( have_rows( 'company_info', 'option' ) ) : the_row(); ?>
            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                <h3><?php the_sub_field( 'title' ); ?></h3>
                <address>
                    <?php the_sub_field( 'address' ); ?>
                </address>
                <p>
                    <a href="tel:<?php the_sub_field( 'phone_number' ); ?>">
                        <?php the_sub_field( 'phone_number' ); ?>
                    </a>
                </p>
                <p>
                    <a href="mailto:<?php the_sub_field( 'email' ); ?>">
                        <?php the_sub_field( 'email' ); ?>
                    </a>
                </p>
            </div>
                <?php endwhile; ?>
            <?php endif; ?>

            <?php if ( have_rows( 'contat_us', 'option' ) ) : ?>
            <?php while ( have_rows( 'contat_us', 'option' ) ) : the_row(); ?>

            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                <h3><?php the_sub_field( 'title' ); ?></h3>
                <p><?php the_sub_field( 'content' ); ?></p>
                <p>
                    <a href="<?php the_sub_field( 'link' ); ?>">
                        <?php the_sub_field( 'link_label' ); ?>
                    </a>
                </p>
            </div>

                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>
    <div class="copyright py-4">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                    <p>
                       &copy; <?php echo date( 'Y' ); ?> <?php echo get_bloginfo( 'name' ); ?>. All Rights Reserved.
                    </p>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                    <p class="text-lg-end">
                        <a href="">Privacy Policy</a> | <a href="">Terms &amp; Conditions</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php // Closing div#page from header.php. ?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>

