<?php

abstract class Responsive_Portfolio_Gallery_Shortcodes {
 
	var $shortcode = 'shortcode';
	var $content = false;
	 
	public function __construct() {
		add_shortcode($this->shortcode, array(&$this, 'shortcode'));
	}
	 
	abstract function shortcode();
	
	public function content($columns){
		$terms = get_terms("portfolio-category");  
		$count = count($terms);  
		
		$html = '<div id="filter-sorter">
				<div id="filter-buttons">
				<h3>Filter:</h3>';
		$html .= '<ul id="portfolio-filter-list">
				<li class="selected"><a href="#" data-group="*" class="active">All</a></li>';
		if ( $count > 0 ) {     
			foreach ( $terms as $term ) {  
				$termname = strtolower($term->name);  
				$termname = str_replace(' ', '-', $termname);  
				$html .= '<li><a href="#" data-group="' . $termname . '">' . $term->name . '</a></li>'; 
			}  
		}  
		$html .= '</ul>';
		echo $html;
		$html = '<select id="portfolio-filter-select">
				<option data-group="*">All</option>';
		if ( $count > 0 ) {     
			foreach ( $terms as $term ) {  
				$termname = strtolower($term->name);  
				$termname = str_replace(' ', '-', $termname);  
				$html .= '<option data-group="' . $termname . '">' . $term->name . '</option>'; 
			}  
		}
		$html .= '</select>';  
		echo $html;
		$gridButton = plugins_url( '/assets/images/grid-view-32.png' , dirname(__FILE__));
		$listButton =plugins_url( '/assets/images/list-view-32.png' , dirname(__FILE__));;
		$hybridButton =plugins_url( '/assets/images/hybrid-grid-view-32.png' , dirname(__FILE__));;
		echo '</div>  
			<div id="view-sorter">
				<span id="view-sorter-title">View:</span> 
				<span title="Grid View" class="grid_btn 2-col-grid"><img src="'.$gridButton.'" alt="Grid View" /></span>
				<span title="Hybrid View" class="hybrid_btn 2-col-hybrid"><img src="'.$hybridButton.'" alt="Hybrid View" /></span> 
				<span title="List View" class="list_btn 2-col-list"><img src="'.$listButton.'" alt="List View" /></span> 
			</div>
		</div>';
		$loop = new WP_Query(array('post_type' => 'portfolio-item', 'posts_per_page' => -1));  
		$count =0;
		?>
		<div class="clearboth"></div>
		<div id="container portfolio-wrapper">
			<div id="portfolio-list" class="shuffle">  						  
				<?php if ( $loop ) :   								   
					while ( $loop->have_posts() ) : $loop->the_post(); ?>  						
						<?php 
						$postid = get_the_ID();
						$terms = get_the_terms( $postid, 'portfolio-category' );  												  
						if ( $terms && ! is_wp_error( $terms ) ) :   
							$links = array();  		  
							$links[] = "*";
							foreach ( $terms as $term )   
							{  
								$links[] = $term->slug;  
							}  
							$tax = json_encode($links);
						else :    
							$tax = '';    
						endif;  
						?>  
									  
						<?php 
						$infos = get_post_custom_values('_url'); 
						$image_full = wp_get_attachment_image_src( get_post_thumbnail_id( $postid ), 'full' ); 
						?>  
									  
						<div class="module-container portfolio-item" data-groups='<?php echo strtolower($tax); ?>'>
							<div class="module-img"><a href="<?php the_permalink() ?>"><img src="<?php echo $image_full[0]; ?>"></a></div>  
							<div class="module-meta">
								<h3 class="item-header"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>  
								<p class="excerpt "><?php $this->the_excerpt_max_charlength(45); ?></p>  
								<p class="links">
								<?php 
								if($infos[0] != ""){
									?><a href="<?php the_permalink() ?>">Read More</a>
									  <a class="visit-site" href="http://<?php echo $infos[0]; ?>" target="_blank">Visit Site</a>
								<?php 
								}
								else {
									?><a href="<?php the_permalink() ?>">Read More</a>
								<?php 
								}
								?>
								</p>  
							</div>
						</div>		  
					<?php endwhile; else: ?>
					<div class="error-not-found">Sorry, no portfolio entries to show.</div>
				<?php endif; ?>
			</div>
			<div class="clearboth"></div>
		</div> <!-- end #project-wrapper-->
		
		<script>  
			jQuery(document).ready(
				function(){
					jQuery("#portfolio-list .portfolio-item").addClass('columns-' + <?php echo $columns ?>);
					jQuery("#portfolio-list .module-meta").slideUp().css('height', 0);
					jQuery(".grid_btn").css("opacity","0.5");
					
					var $grid = jQuery('#portfolio-list');
					$grid.shuffle({
						itemSelector: '.portfolio-item' // the selector for the items in the grid
					});
					jQuery('#portfolio-filter-list a').on(
						'click',
						function(event){ 
							event.preventDefault();
							var selector = jQuery(this).attr('data-group'); 
							var parent = jQuery(this).parent();
							parent.siblings().removeClass('selected');
							parent.addClass('selected');
							jQuery('#portfolio-filter-list a').removeClass('active');
							jQuery(this).addClass('active');
							$grid.shuffle('shuffle', selector );
							return false; 
						}
					);
				
					jQuery('#portfolio-filter-select').on(
						'change',
						function(){ 
							var selector = jQuery("option:selected").attr('data-group'); 
							var parent = jQuery(this).parent();
							$grid.shuffle('shuffle', selector );
							return false; 
						}
					);
					imagesLoaded( 
						document.querySelector('#portfolio-list'), 
						function( instance ) {
							jQuery("span.2-col-grid").trigger('click');
						}
					);
				}
			);
			
			 // Two Column Buttons Actions
			jQuery("span.2-col-grid").on(
				'click',
				function () {
					jQuery("#portfolio-list .portfolio-item, #portfolio-list .module-img, #portfolio-list .module-meta")
					.removeClass("full-page-view");
					jQuery("#portfolio-list .module-meta").slideUp().css('height', 0);
					jQuery(".list_btn").css("opacity","1");
					jQuery(".hybrid_btn").css("opacity","1");
					jQuery(".grid_btn").css("opacity","0.5");
					jQuery("#portfolio-filter-list a.active").trigger('click'); 
				}
			);

			jQuery("span.2-col-hybrid").on(
				'click',
				function () {
					jQuery("#portfolio-list .portfolio-item, #portfolio-list .module-img, #portfolio-list .module-meta")
					.removeClass("full-page-view");
					jQuery("#portfolio-list .module-meta").slideDown().css('float', 'left').css('height', 'auto');
					jQuery(".list_btn").css("opacity","1");
					jQuery(".hybrid_btn").css("opacity","0.5");  
					jQuery(".grid_btn").css("opacity","1");
					jQuery("#portfolio-list .module-container").css('height', 'auto');
					jQuery("#portfolio-filter-list a.active").trigger('click'); 
				}
			); 

			jQuery("span.2-col-list").on(
				'click',
				function () {
					jQuery("#portfolio-list .portfolio-item, #portfolio-list .module-img").addClass("full-page-view");
					jQuery("#portfolio-list .module-meta").addClass("full-page-view").slideDown().css('float', 'left').css('height', 'auto');
					jQuery(".list_btn").css("opacity","0.5"); 
					jQuery(".hybrid_btn").css("opacity","1");
					jQuery(".grid_btn").css("opacity","1");
					jQuery("#portfolio-list .module-container").css('height', 'auto');
					jQuery("#portfolio-filter-list a.active").trigger('click');  
				}
			);
		</script>
		<?php
	} 
	public function the_excerpt_max_charlength($charlength) {
		$excerpt = get_the_excerpt();
		$charlength++;

		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				echo mb_substr( $subex, 0, $excut );
			} else {
				echo $subex;
			}
			echo '[...]';
		} else {
			echo $excerpt;
		}
	}
}
 
