<?php
/*
Plugin Name: Responsive Portfolio Gallery
Plugin URI: http://responsive-portfolio-gallery-demo.rocketship.co.nz/
Description: A plugin designed to provide the easy creation of a responsive portfolio gallery from within WordPress.  The jQuery plugin Shuffle.js is used to provide wonderful css transitions to filter your portfolio gallery by categories. 
Version: 1.0
Author: Shane Watters
Author URI: http://www.rocketship.co.nz
License: GPL2
*/
	
if(!class_exists('ResponsivePortfolioGallery')){
	class ResponsivePortfolioGallery {
		/* Construct the plugin object */
		public function __construct() {
			// Register custom post types
			require_once(sprintf("%s/post-types/portfolio-item-post-type.php", dirname(__FILE__)));
			$PortfolioItemPostType = new PortfolioItemPostType();
			//Register JavaScript files
			add_action('wp_enqueue_scripts', array(&$this,'enqueue_frontend_files'));
			// Register shortcodes
			require_once(sprintf("%s/shortcodes/shortcodes.php", dirname(__FILE__)));
			require_once(sprintf("%s/shortcodes/two-column-portfolio.php", dirname(__FILE__)));
			require_once(sprintf("%s/shortcodes/three-column-portfolio.php", dirname(__FILE__)));
			require_once(sprintf("%s/shortcodes/four-column-portfolio.php", dirname(__FILE__)));
		} 
		/* Activate the plugin */
		public static function responsive_portfolio_gallery_activate() 
		{
			
		} 
		/* Deactivate the plugin */		
		public static function responsive_portfolio_gallery_deactivate()
		{
			ResponsivePortfolioGallery::unregister_post_type('portfolio-item');
			register_taxonomy('portfolio-category', array());
		} 
		/* unregister custom post type */
		public function unregister_post_type( $post_type ) {
			global $wp_post_types;
			if ( isset( $wp_post_types[ $post_type ] ) ) {
				unset( $wp_post_types[ $post_type ] );
				return true;
			}
			return false;
		}
		/* add any JavaScript files required for the plugin */
		public function enqueue_frontend_files() {  
			wp_register_script( 'shuffle', plugins_url( '/responsive-portfolio-gallery/js/jquery.shuffle.min.js' , dirname(__FILE__)), array( 'jquery' ) , '', true);
			wp_enqueue_script( 'shuffle' ); 
			wp_register_script( 'images-loaded', plugins_url( '/responsive-portfolio-gallery/js/imagesloaded.pkgd.min.js' , dirname(__FILE__)), array( 'jquery' ) , '', true);
			wp_enqueue_script( 'images-loaded' ); 
			wp_register_style( 'portfolio_style', plugins_url( '/responsive-portfolio-gallery/css/portfolio-style.css' , dirname(__FILE__)) );
			wp_enqueue_style( 'portfolio_style');
		}
	} 
} 
 
if(class_exists('ResponsivePortfolioGallery'))
{
	// Installation and uninstallation hooks
	register_activation_hook( __FILE__, array('ResponsivePortfolioGallery', 'responsive_portfolio_gallery_activate'));
	register_deactivation_hook( __FILE__, array('ResponsivePortfolioGallery', 'responsive_portfolio_gallery_deactivate' ));
	
	// instantiate the plugin class
	$ResponsivePortfolioGallery = new ResponsivePortfolioGallery();
}
