<?php
/** Start the engine */
include_once( get_template_directory() . '/lib/init.php' );

/** Create additional color style options */
add_theme_support( 'genesis-style-selector', array( 'backcountry-blue' => 'Blue', 'backcountry-green' => 'Green', 'backcountry-red' => 'Red' ) );

/** Child theme (do not remove) */
define( 'CHILD_THEME_NAME', 'Backcountry Child Theme' );
define( 'CHILD_THEME_URL', 'http://my.studiopress.com/themes/backcountry' );
define( 'CHILD_THEME_VERSION', '1.0.2' );

$content_width = apply_filters( 'content_width', 470, 400, 910 );

/** Add new image sizes */
add_image_size( 'home-bottom', 170, 90, TRUE );
add_image_size( 'home-middle', 265, 150, TRUE );
add_image_size( 'home-mini', 50, 50, TRUE );

/** Add support for custom background */
add_theme_support( 'custom-background' );

/** Add support for custom header */
add_theme_support( 'genesis-custom-header', array( 
	'width'     => 960, 
	'height'    => 120 
) );

/** Reposition the primary navigation */
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_before_header', 'genesis_do_nav' );

/** Add description to secondary navigation */
add_filter( 'walker_nav_menu_start_el', 'add_description', 10, 4 ); 
function add_description( $item_output, $item, $depth, $args ) {

	$args = (array) $args;
       
	if ( $args['theme_location'] != 'primary' )  {
		return preg_replace( '/(<a.*?>[^<]*?)</', '$1' . "<span class=\"menu-description\">{$item->post_content}</span><", $item_output ); 
	}
	else {
		return $item_output;
	}

}

/** Add no nav class if primary navigation has no menu assigned */
add_filter( 'body_class', 'backcountry_no_nav_class' );
function backcountry_no_nav_class( $classes ) {

	$menu_locations = get_theme_mod( 'nav_menu_locations' );

	if ( empty( $menu_locations['primary'] ) ) {
		$classes[] = 'no-nav';
	}
	return $classes;
}

/** Add home top section to homepage */
add_action( 'genesis_before_content_sidebar_wrap', 'backcountry_home_top' );
function backcountry_home_top() {

	if ( is_front_page() && is_active_sidebar( 'home-top' ) ) {
		echo '<div id="home-top">';
		dynamic_sidebar( 'home-top' );
		echo '</div><!-- end #home-top -->';
	}

}

/** Customize the post info function */
add_filter( 'genesis_post_info', 'backcountry_post_info_filter' );
function backcountry_post_info_filter( $post_info ) {

    return '[post_date] by [post_author_posts_link] &middot; [post_comments] [post_edit]';

}

/** Customize the post meta function */
add_filter( 'genesis_post_meta', 'backcountry_post_meta_filter' );
function backcountry_post_meta_filter( $post_meta ) {
    
	return '[post_categories before="Filed Under: "] &middot; [post_tags before="Tagged: "]';

}

/** Add after post ad section */
add_action( 'genesis_after_post_content', 'backcountry_after_post_ad', 9 ); 
function backcountry_after_post_ad() {

    if ( is_single() && is_active_sidebar( 'after-post-ad' ) ) {
    	echo '<div class="after-post-ad">';
		dynamic_sidebar( 'after-post-ad' );
		echo '</div><!-- end .after-post-ad -->';
	}

}

/** Add support for 4-column footer widgets */
add_theme_support( 'genesis-footer-widgets', 4 );

/** Register widget areas */
genesis_register_sidebar( array(
	'id'			=> 'home-top',
	'name'			=> __( 'Home Top', 'backcountry' ),
	'description'	=> __( 'This is the home top section.', 'backcountry' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-left',
	'name'			=> __( 'Home Left', 'backcountry' ),
	'description'	=> __( 'This is the home left section.', 'backcountry' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-right',
	'name'			=> __( 'Home Right', 'backcountry' ),
	'description'	=> __( 'This is the home right section.', 'backcountry' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-bottom',
	'name'			=> __( 'Home Bottom', 'backcountry' ),
	'description'	=> __( 'This is the home bottom section.', 'backcountry' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'after-post-ad',
	'name'			=> __( 'After Post Ad', 'magazine' ),
	'description'	=> __( 'This is the after post ad section.', 'backcountry' ),
) );