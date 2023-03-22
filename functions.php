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

// Add support for the Agent role to be able to edit Buildings
function restrict_agent_building_editing($caps, $cap, $user_id, $args) {
    $current_user = get_userdata($user_id);

    if (in_array('agent', $current_user->roles)) {
        if ('edit_post' == $cap || 'delete_post' == $cap) {
            $post = get_post($args[0]);

            if ('building' == $post->post_type) {
                $caps[] = 'do_not_allow';
            }
        } elseif ('publish_posts' == $cap && 'building' == get_post_type()) {
            $caps[] = 'do_not_allow';
        }
    }

    return $caps;
}
add_filter('map_meta_cap', 'restrict_agent_building_editing', 10, 4);


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


