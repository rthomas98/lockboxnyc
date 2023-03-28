<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

	// Get the theme data.
	$the_theme     = wp_get_theme();
	$theme_version = $the_theme->get( 'Version' );

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";
	
	$css_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_styles );

	wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $css_version );
	wp_enqueue_script( 'jquery' );
	
	$js_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_scripts );
	
	wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $js_version, true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );



/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );



/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );


// Add support for the Building CPT to be the parent of the Apartment CPT

function register_building_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Building',
            'singular_name' => 'Building',
            'plural_name' => 'Buildings',
            'add_new_item' => 'Add New Building',
            'edit_item' => 'Edit Building',
            'new_item' => 'New Building',
            'view_item' => 'View Building',
            'search_items' => 'Search Buildings',
            'not_found' => 'No Buildings found',
            'not_found_in_trash' => 'No Buildings found in Trash',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' )
    );
    register_post_type( 'building', $args );
}
add_action( 'init', 'register_building_post_type' );

function register_apartment_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Apartment',
            'singular_name' => 'Apartment',
            'plural_name' => 'Apartments',
            'parent_item_colon' => 'Building:',
            'all_items' => 'All Apartments',
            'add_new_item' => 'Add New Apartment',
            'edit_item' => 'Edit Apartment',
            'new_item' => 'New Apartment',
            'view_item' => 'View Apartment',
            'search_items' => 'Search Apartments',
            'not_found' => 'No Apartments found',
            'not_found_in_trash' => 'No Apartments found in Trash',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'rewrite' => array( 'slug' => 'building/apartment' ),
        'show_in_menu' => 'edit.php?post_type=building'
    );
    $args = apply_filters( 'register_post_type_args', $args, 'apartment' );
    register_post_type( 'apartment', $args );
}
add_action( 'init', 'register_apartment_post_type' );

function add_author_support_to_posts() {
    add_post_type_support( 'apartment', 'author' );
}
add_action( 'init', 'add_author_support_to_posts' );

// Add support for the Agent role to be able to edit Apartments
function restrict_agents_to_own_apartments($query) {
    global $pagenow;

    if ('edit.php' != $pagenow || !$query->is_admin || $query->get('post_type') != 'apartment') {
        return $query;
    }

    $current_user = wp_get_current_user();

    if (in_array('agent', $current_user->roles)) {
        $query->set('author', $current_user->ID);
    }

    return $query;
}
add_action('pre_get_posts', 'restrict_agents_to_own_apartments');


// Add support for the Agent role to be able to edit Apartments
function restrict_agent_add_apartment($allcaps, $cap, $args) {
    if ('edit_posts' == $args[0] && 'apartment' == get_post_type()) {
        $current_user = wp_get_current_user();

        if (in_array('agent', $current_user->roles)) {
            $allcaps['publish_posts'] = false;
            $allcaps['edit_others_posts'] = false;
            $allcaps['edit_published_posts'] = false;
            $allcaps['delete_others_posts'] = false;
            $allcaps['delete_published_posts'] = false;
        }
    }

    return $allcaps;
}
add_filter('user_has_cap', 'restrict_agent_add_apartment', 10, 3);

// Dynamically populate the LL Code field on the Apartment CPT based on the LL Code field on the Building CPT
function load_building_ll_code($value, $post_id, $field) {
    if ($field['name'] == 'll_code' && get_post_type($post_id) == 'apartment') {
        $building = get_field('building', $post_id); // Replace 'building' with the field name you used to create the relationship between "Building" and "Apartment" CPTs

        if ($building) {
            $building_id = $building->ID ?? $building[0]->ID; // Check if the building is an object or an array, and get the ID accordingly
            $value = get_field('ll_code', $building_id);
        }
    }

    return $value;
}
add_filter('acf/load_value', 'load_building_ll_code', 10, 3);

// Dynamically populate the LL Code field on the Apartment CPT based on the LL Code field on the Building CPT

function load_building_address($value, $post_id, $field) {
    if ($field['name'] == 'office_address' && get_post_type($post_id) == 'apartment') {
        $building = get_field('building', $post_id); // Replace 'building' with the field name you used to create the relationship between "Building" and "Apartment" CPTs

        if ($building) {
            $building_id = $building->ID ?? $building[0]->ID; // Check if the building is an object or an array, and get the ID accordingly
            $value = get_field('office_address', $building_id);
        }
    }

    return $value;
}
add_filter('acf/load_value', 'load_building_address', 10, 3);

// Add the custom column header to the Apartment CPT dashboard edit screen
function add_ll_code_column($columns) {
    $columns['ll_code'] = 'LL Code';
    return $columns;
}
add_filter('manage_apartment_posts_columns', 'add_ll_code_column');

// Fill the custom column with data from the associated Building CPT
// Add the custom column headers to the Apartment CPT dashboard edit screen
function add_custom_columns($columns) {
    $columns['ll_code'] = 'LL Code';
    $columns['office_address'] = 'Office Address';
    return $columns;
}
add_filter('manage_apartment_posts_columns', 'add_custom_columns');

// Fill the custom columns with data from the associated Building CPT
function display_custom_column_data($column, $post_id) {
    $building = get_field('building', $post_id); // Replace 'building' with the field name you used to create the relationship between "Building" and "Apartment" CPTs
    if ($building) {
        $building_id = $building->ID ?? $building[0]->ID; // Check if the building is an object or an array, and get the ID accordingly

        if ($column == 'll_code') {
            $ll_code = get_field('ll_code', $building_id);
            echo $ll_code;
        } elseif ($column == 'office_address') {
            $office_address = get_field('office_address', $building_id);
            echo $office_address;
        }
    }
}
add_action('manage_apartment_posts_custom_column', 'display_custom_column_data', 10, 2);

// ACF Options Page
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));

    acf_add_options_sub_page(array(
        'page_title'    => 'Theme Footer Settings',
        'menu_title'    => 'Footer',
        'parent_slug'   => 'theme-general-settings',
    ));

}

// Home search form

function apartment_search_filter($query) {
    if (!is_admin() && $query->is_main_query()) {
        if ($query->is_search && isset($_GET['post_type']) && $_GET['post_type'] == 'apartment') {
            $meta_query = array();

            if (!empty($_GET['zip_code'])) {
                $meta_query[] = array(
                    'key' => 'zip_code',
                    'value' => sanitize_text_field($_GET['zip_code']),
                    'compare' => 'LIKE',
                );
            }

            if (!empty($_GET['address'])) {
                $meta_query[] = array(
                    'key' => 'address',
                    'value' => sanitize_text_field($_GET['address']),
                    'compare' => 'LIKE',
                );
            }

            if (count($meta_query) > 1) {
                $meta_query['relation'] = 'AND';
            }

            if (count($meta_query) > 0) {
                $query->set('meta_query', $meta_query);
            }
        }
    }
}
add_action('pre_get_posts', 'apartment_search_filter');
