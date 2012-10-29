<?php
get_header();
?>
		<div id="container">
			<div id="content" role="main">
				<?php
					$categories = wp_get_post_terms($post->ID, 'public-post-category');
				?>
				<a href="<?php echo get_permalink(3030)?>/<?php echo $categories[0]->term_id?>/">Create new</a> | <a href="<?php echo get_permalink(2)?>">Back to main page</a>
				<br/><br/>
				<h1 class="page-title"><?php printf( __( '%s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );?></h1>
				<?php
				if(have_posts()){
					while(have_posts()){
						the_post();
						$companies = wp_get_post_terms($post->ID, 'public-post-company');
						$company_logo = $companies[0]->description;
				?>
					<h2 class="entry-title"><a href="<?php echo the_permalink()?>"><?php echo the_title()?></a> <img src="<?php echo $company_logo?>" style="height:30px;float:right;"/> </h2>
					<div class="entry-summary">
						<?php echo the_excerpt()?>
						<p style="text-align:right">
							<?php echo human_time_diff(get_the_time('U'), current_time('timestamp'))?> ago, by <?php echo get_the_author()?>
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

			</div><!-- #content -->
		</div><!-- #container -->
<?php 
get_sidebar();
get_footer();
?>