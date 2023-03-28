<?php

if ( get_field( 'blocks' ) ) :

    while ( has_sub_field( 'blocks', get_the_ID() ) ) :

        if ( get_row_layout() == 'featured_apartments' ):
            include( get_stylesheet_directory() . '/acf-blocks/featured-apartments.php' );
        endif;

    endwhile;
endif;
?>