<?php

add_action( 'genesis_meta', 'backcountry_home_genesis_meta' );
/**
 * Add widget support for homepage. If no widgets active, display the default loop.
 *
 */
function backcountry_home_genesis_meta() {

	if ( is_active_sidebar( 'home-left' ) || is_active_sidebar( 'home-right' ) || is_active_sidebar( 'home-bottom' ) ) {
	
		remove_action( 'genesis_loop', 'genesis_do_loop' );
		add_action( 'genesis_loop', 'backcountry_home_loop_helper' );
		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar' );
		add_filter( 'body_class', 'add_body_class' );
		
		function add_body_class( $classes ) {
   			$classes[] = 'backcountry';
  			return $classes;
		}

	}
}

function backcountry_home_loop_helper() {

	if ( is_active_sidebar( 'home-left' ) || is_active_sidebar( 'home-right' ) ) {
	
		echo '<div id="home">';

			echo '<div class="home-left">';
			dynamic_sidebar( 'home-left' );
			echo '</div><!-- end .home-left -->';

			echo '<div class="home-right">';
			dynamic_sidebar( 'home-right' );
			echo '</div><!-- end .home-right -->';
		
		echo '</div><!-- end #home -->';
		
	}
	
	if ( is_active_sidebar( 'home-bottom' ) ) {
	
		echo '<div id="home-bottom">';
		dynamic_sidebar( 'home-bottom' );
		echo '</div><!-- end #home-bottom -->';
		
	}

}

genesis();