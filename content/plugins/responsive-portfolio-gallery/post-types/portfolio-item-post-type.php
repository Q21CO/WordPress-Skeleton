<?php
if(!class_exists('PortfolioItemPostType')){
	/** A PostTypeTemplate class that provides 3 additional meta fields **/
	class PortfolioItemPostType {
		const POST_TYPE = "portfolio-item";
		private $_meta	= array(
			'_url',
		);
		/** The Constructor **/
		public function __construct() {
			//register actions
			add_action('init', array(&$this, 'init'));
			add_action('admin_init', array(&$this, 'admin_init'));
			add_post_type_support( 'page', 'page-attributes' );
			
			add_filter( 'single_template', array(&$this, 'insert_my_template'));
		} // END public function __construct()

		/**  hook into WP's init action hook **/
		public function init(){
			// Initialize Post Type
			$this->create_portfolio_item_post_type();
			add_action('save_post', array(&$this, 'save_post'));
		}

		/** Create the post type **/
		public function create_portfolio_item_post_type(){
			$labels = array(  
				'name' => _x('Portfolio Items', 'post type general name'),  
				'singular_name' => _x('Porfolio Item', 'post type singular name'),  
				'add_new' => _x('Add New', 'project'),  
				'add_new_item' => __('Add New Portfolio Item'),  
				'edit_item' => __('Edit Portfolio Item'),  
				'new_item' => __('New Portfolio Item'),  
				'view_item' => __('View Portfolio Item'),  
				'search_items' => __('Search Portfolio Items'),  
				'not_found' =>  __('No portfolio items found'),  
				'not_found_in_trash' => __('No portfolio items found in Trash'),  
				'parent_item_colon' => '',  
				'menu_name' => 'Portfolio Items'  
			);
			$args = array(
				'labels' => $labels,  
				'public' => true,  
				'publicly_queryable' => true,  
				'show_ui' => true,  
				'show_in_menu' => true,  
				'query_var' => true,  
				'rewrite' => true,  
				'capability_type' => 'post',  
				'has_archive' => false,  
				'hierarchical' => false,  
				'menu_position' => null,  
				'supports' => array('title','editor','author','thumbnail','excerpt')  
			);
			// The following is the main step where we register the post.  
			register_post_type(self::POST_TYPE,$args);  
			
			// Initialize New Taxonomy Labels  
			$labels = array(  
				'name' => _x( 'Categories', 'taxonomy general name' ),  
				'singular_name' => _x( 'Category', 'taxonomy singular name' ),  
				'search_items' =>  __( 'Search Types' ),  
				'all_items' => __( 'All Categories' ),  
				'parent_item' => __( 'Parent Category' ),  
				'parent_item_colon' => __( 'Parent Category:' ),  
				'edit_item' => __( 'Edit Categoriess' ),  
				'update_item' => __( 'Update Category' ),  
				'add_new_item' => __( 'Add New Category' ),  
				'new_item_name' => __( 'New Category Name' ),  
			);  
			// Custom taxonomy for Project Tags  
			register_taxonomy('portfolio-category',array('portfolio-item'), array(  
				'hierarchical' => true,  
				'labels' => $labels,  
				'show_ui' => true,  
				'query_var' => true,  
				'rewrite' => array( 'slug' => 'portfolio-category' ),  
			));
			flush_rewrite_rules();
		}
		/** Save the metaboxes for this custom post type **/
		public function save_post($post_id) {
			// check nonce  
			if (!isset($_POST['meta_noncename']) || !wp_verify_nonce($_POST['meta_noncename'], __FILE__)) {  
				return $post_id;  
			} 
			// verify if this is an auto save routine. 
			// If it is our form has not been submitted, so we dont want to do anything
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			
			if($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id)) {
				foreach($this->_meta as $field_name){
					// Update the post's meta field
					update_post_meta($post_id, $field_name, $_POST[$field_name]);
				}
			}
			else {
				return;
			}
		}
		
		/* hook into WP's admin_init action hook */
		public function admin_init(){			
			// Add metaboxes
			add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
		}
		/** hook into WP's add_meta_boxes action hook */
		public function add_meta_boxes(){
			// Add this metabox to every selected post
			add_meta_box('portfolio-meta', 'Portfolio Information', array(&$this, 'portfolio_meta_setup'), self::POST_TYPE, 'normal', 'high');  			
		} 

		public function portfolio_meta_setup() {  
			global $post;  
			?>  
			<div class="portfolio_meta_control">  
				<label>Website URL </label>  
				<p>  
					http://<input type="text" name="_url" value="<?php echo get_post_meta($post->ID,'_url',TRUE); ?>" style="width: 90%;" />  
				</p>  
			</div>  
			<?php  
			// create for validation
			echo '<input type="hidden" name="meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';  
		}

		public function insert_my_template($template){
			global $post;
			if(get_post_type( $post ) == "portfolio-item"){
				$template = sprintf("%s/../templates/single-portfolio-item.php", dirname(__FILE__), self::POST_TYPE);
			}
			return $template;
		}
	}
}
