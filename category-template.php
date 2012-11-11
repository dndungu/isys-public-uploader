<?php
get_header();
?>
		<div id="container" class="isys_visitor_posts">
			<div id="content" role="main" class="isys-main">
				<?php
					$categories = wp_get_post_terms($post->ID, 'public-post-category');
					global $wp_query;
				?>
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
						<div class="isys-half">
							<span style="font-size:1.5em;">
								<a href="">INDLÆG</a>
								<br/>
							</span>
							arkivet indeholder <?php print $categories[0]->count?> indlæg
							<br/><br/><br/><br/>					
							<?php
							$page_number = (get_query_var('paged')) ? get_query_var('paged') : 1;
							$posts_from = (($page_number - 1) * get_option('posts_per_page')) + 1;
							?>
							<strong> | <?php print $posts_from?> – <?php print ($posts_from + get_option('posts_per_page') - 1)?> af <?php print $categories[0]->count?> | <?php next_posts_link( __( 'FLERE', '' ) ); previous_posts_link( __( 'FLERE', '' ) )?> | </strong>
							<br/><br/>	
						</div>
						<div class="isys-half">
							<a class="isys-black-link" style="float:right;" href="<?php echo get_bloginfo('url')?>/create-visitor-post/?<?php echo $wp_query->queried_object->term_id?>"><?php echo __('OPRET NYT INDLÆG')?></a>
						</div>
					</div>
				<?php
				if(have_posts()){
					while(have_posts()){
						the_post();
						$categories = wp_get_post_terms($post->ID, 'public-post-category');
						$companies = isys_visitor_posts::get_company(get_post_meta(get_the_ID(), 'post_company', true));
				?>
					<div class="post-item row">

						<div class="row" style="margin-left:-70px;width:530px;padding-top:10px;">
							<div class="isys-leftbar">
								<strong class="likes-count"><?php print intval(get_post_meta(get_the_ID(), 'likes', true))?></strong>
								<a class="post-vote vote-up" vote="up" post="<?php the_ID()?>">LIKE</a>
								<?php if(get_post_meta(get_the_ID(), 'favourite_box', true) == 'Yes'){?>
								<a class="favourite_box"></a>
								<?php }?>
							</div>						
							<div class="isys-half">
								<h2>
									<a href="<?php echo the_permalink()?>"><?php echo the_title()?></a>
								</h2>
								<span style="text-transform:uppercase;">Indlæg af</span> <?php $author_email = get_post_meta(get_the_ID(), 'author_email')?> <a href="mailto:<?php print $author_email[0]?>"><?php print $author_email[0]?></a>
								<br/>
								<?php print date('l j. F o', get_the_time('U'))?>
								<br/>
								godkendt af <a href=""><?php the_modified_author()?></a>
								<br/>
								<?php print date('l j. F o', get_the_modified_time('U'))?>
								<br/>
								kategori: <a href="<?php print get_term_link($categories[0])?>"><?php print $categories[0]->name?></a>
								<br/>
								<br/>
							</div>
							<div class="isys-half">
								<?php if(count($companies)){?>
								<?php $logo = $companies[0]?>
								<img src="<?php print $logo->path . '/thumbs/thumbs_' . $logo->filename?>" alt="<?php print $logo->alttext?>" class="isys-company-logo"/>
								<?php }?>
							</div>
						</div>	 
						<div class="entry-summary">
							<?php echo the_excerpt()?>
							<div class="isys-half"  style="width:75%;">
								<p>DER ER <a href="<?php echo the_permalink()?>"><?php echo get_comments_number()?></a> KOMMENTARER
								<br/>
								FRA <a href=""><?php echo count(get_post_meta(get_the_ID(), 'attachments'))?></a> DOKUMENTER VEDHÆFTET</p>
							</div>
							<?php if(count(get_post_meta(get_the_ID(), 'attachments'))){?>
							<div class="isys-half"  style="width:25%;">
								<a class="isys-attachments-count">
									<span><?php echo count(get_post_meta(get_the_ID(), 'attachments'))?></span>
								</a>
							</div>
							<?php }?>
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