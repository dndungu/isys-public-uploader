<?php
get_header();
?>
		<div id="container" class="isys_visitor_posts">
			<div id="content" role="main" class="isys-main">
				<?php
					$categories = wp_get_post_terms($post->ID, 'public-post-category');
					global $wp_query;
				?>
					<a href="<?php echo get_bloginfo('url')?>/create-visitor-post/?<?php echo $wp_query->queried_object->term_id?>"><?php echo __('Create new')?></a> | <a href="<?php echo get_bloginfo('url')?>/visitor-posts"><?php echo __('Back to main page')?></a>
					<br/><br/>
					<div class="row">
					<h1>
						<?php printf( __( '%s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );?>
					</h1>
					</div>
					<div class="row">
						<?php print nl2br($categories[0]->description, true)?>
						<br/><br/>
					</div>
					<div class="row" style="color:#6d6d6d;">
						<span style="font-size:1.5em;">
						<a href="">INDLÆG</a>
						<br/>
						<?php print single_cat_title( '', false )?>
						<br/>
						</span>
						arkivet indeholder <?php print $categories[0]->count?> indlæg fra <?php print $categories[0]->count?> forskellige medlemmer
						<br/><br/>
						<?php
						$page_number = (get_query_var('paged')) ? get_query_var('paged') : 1;
						$posts_from = (($page_number - 1) * get_option('posts_per_page')) + 1;
						?>
						<strong> | <?php print $posts_from?> – <?php print ($posts_from + get_option('posts_per_page') - 1)?> af <?php print $categories[0]->count?> | <?php next_posts_link( __( 'FLERE', '' ) ); previous_posts_link( __( 'FLERE', '' ) )?> | </strong>
						<br/><br/>
					</div>
				<?php
				if(have_posts()){
					while(have_posts()){
						the_post();
						$categories = wp_get_post_terms($post->ID, 'public-post-category');
						$companies = wp_get_post_terms($post->ID, 'public-post-company');
						$companies_meta = get_option("company_taxonomy_term_{$companies[0]->term_id}"); 
				?>
					<div class="post-item row">
						<div class="isys-half">
							<h2><a href="<?php echo the_permalink()?>"><?php echo the_title()?></a></h2>
							<span style="text-transform:uppercase;">Indlæg af</span> <?php $author_email = get_post_meta(get_the_ID(), 'author_email')?> <a href="mailto:<?php print $author_email[0]?>"><?php print $author_email[0]?></a>
							<br/>
							<?php print date('l j. F o', get_the_time('U'))?>
							<br/>
							godkendt af <a href=""><?php the_modified_author()?></a>
							<br/>
							kategori: <a href="<?php print get_term_link($categories[0])?>"><?php print $categories[0]->name?></a>
							<br/>
							<br/>
						</div>
						<div class="isys-half">
							<img src="<?php echo $companies_meta['thumbnail_term_meta']?>" style="height:40px;float:right;"/>
						</div>					 
						<div class="entry-summary">
							<?php echo the_excerpt()?>
							<p>DER ER <a href="<?php echo the_permalink()?>"><?php echo get_comments_number()?></a> KOMMENTARER FRA <a href=""><?php echo get_comments_number()?></a> MEDLEMMER</p>
							<!-- 
							<p style="text-align:right">
								<?php $meta = get_post_meta($post->ID)?>
								<?php echo human_time_diff(get_the_time('U'), current_time('timestamp'))?> ago, by <?php print $meta['author_name'][0]?>
							</p>
							 -->
						</div>
					</div>
				<?php	
					}
				}else{
					echo 'no posts';
				}
				?>
				</div>
			<div class="widget-area">
				<?php dynamic_sidebar('public-posts')?>
				<form class="search-form" method="GET" action="<?php echo site_url()?>">
					<input type="text" value="HVAD LEDER DU EFTER..." name="s" id="s" onblur="if (this.value == '') { this.value = 'HVAD LEDER DU EFTER...';}" onfocus="if (this.value == 'HVAD LEDER DU EFTER...') 			{this.value = '';}"/>
					<input type="hidden" id="searchsubmit"/>
					<input type="hidden" name="post_type" value="public-post">
				</form>
			</div>
		</div><!-- #container -->
<?php 
get_footer();
?>