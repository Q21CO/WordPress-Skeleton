<?php
/**
 * The template for displaying a single portfolio
 *
 * @package WordPress
 */

get_header(); ?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="single-portfolio-page">
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title();?></h1>
					</header>
					<?php the_post_thumbnail('full'); ?>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				<?php $infos = get_post_custom_values('_url'); 
					if($infos[0] != "" && get_post_type(get_the_ID()) == "portfolio-item"){
						?>
						<div id="portfolio-item-link">
							<a href="http://<?php echo $infos[0]; ?>" target="_blank">Visit Website</a>
						</div>
						<?php 
					}
				?>
				</article>
			<?php endwhile; ?>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>
