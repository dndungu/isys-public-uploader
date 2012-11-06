<?php
get_header();
?>
		<div id="container">
			<div id="content" role="main">
				<?php
					$categories = wp_get_post_terms($post->ID, 'public-post-category');
					global $wp_query;
				?>
				<a href="<?php echo get_bloginfo('url')?>/create-public-post/?<?php echo $wp_query->queried_object->term_id?>"><?php echo __('Create new')?></a> | <a href="<?php echo get_bloginfo('url')?>/public-posts"><?php echo __('Back to main page')?></a>
				<br/><br/>
				<h1 class="page-title">
					<?php printf( __( '%s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );?>
				</h1>
				<?php
				if(have_posts()){
					while(have_posts()){
						the_post();
						$companies = wp_get_post_terms($post->ID, 'public-post-company');
						$companies_meta = get_option("company_taxonomy_term_{$companies[0]->term_id}"); 
				?>
					<div style="display:inline-block;float:left;width:50%;">
						<h2><a href="<?php echo the_permalink()?>"><?php echo the_title()?></a></h2>
					</div>
					<div style="display:inline-block;float:left;width:50%;">
						<img src="<?php echo $companies_meta['thumbnail_term_meta']?>" style="height:30px;float:right;"/>
					</div>					 
					<div class="entry-summary">
						<?php echo the_excerpt()?>
						<p style="text-align:right">
							<?php $meta = get_post_meta($post->ID)?>
							<?php echo human_time_diff(get_the_time('U'), current_time('timestamp'))?> ago, by <?php print $meta['author_name'][0]?>
						</p>
					</div>
				<?php	
					}
				}else{
					echo 'no posts';
				}
				?>
				<?php
				if (  $wp_query->max_num_pages > 1 ) {
				?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
				</div><!-- #nav-below -->
				<?php 
				} 
				?>				
			</div>
			<div class="widget-area">
				
			</div>
		</div><!-- #container -->
<?php 
get_footer();
?>