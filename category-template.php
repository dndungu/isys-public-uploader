<?php
get_header();
?>
		<div id="container" class="isys_visitor_posts">
			<div id="content" role="main" class="isys-main">
				<?php
					global $wp_query;
					$slug = str_replace('blogindlaegs/', '', $wp_query->query['pagename']);
					$slug = strlen($slug) ? $slug : $wp_query->query['blogindlaegs'];
					$kategori = get_term_by('slug', $slug, 'blog-indlaeg-kategori', OBJECT);
				?>
					<div class="row">
						<h1>
							<?php printf( __( '%s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );?>
						</h1>
					</div>
					<div class="row">
						<?php print nl2br($kategori->description, true)?>
						<br/><br/>
					</div>
					<div class="row" style="color:#6d6d6d;">
						<div class="isys-half">
							<span style="font-size:1.5em;">
								<a href="">INDLÆG</a>
								<br/>
							</span>
							arkivet indeholder <?php print $kategori->count?> indlæg
							<br/><br/><br/><br/>					
							<?php
							$page_number = (get_query_var('paged')) ? get_query_var('paged') : 1;
							$posts_from = (($page_number - 1) * get_option('posts_per_page'));
							?>
							<strong> | <?php print $posts_from?> – <?php print ($posts_from + get_option('posts_per_page'))?> af <?php print $kategori->count?> | <?php next_posts_link( __( 'FLERE', '' ) ); previous_posts_link( __( 'FLERE', '' ) )?> | </strong>
							<br/><br/>	
						</div>
						<div class="isys-half">
							<a class="isys-black-link" style="float:right;" href="<?php echo get_bloginfo('url') . '/' . isys_visitor_posts::$form_page_slug . '/?' . (intval($kategori->term_id) > 0 ? $kategori->term_id : 15)?>"><?php echo __('OPRET NYT INDLÆG')?></a>
						</div>
					</div>
				<?php
				//$args = array_merge( $wp_query->query_vars, array( 'post_type' => 'blogindlaeg', 'blog-indlaeg-kategori' => $slug, 'post_status' => 'any', 'posts_per_page'=> 5) );
				//query_posts($args);
				if(have_posts()){
					while(have_posts()){
						the_post();
						$categories = wp_get_post_terms($post->ID, 'blog-indlaeg-kategori');
						$companies = isys_visitor_posts::get_company(get_post_meta(get_the_ID(), 'post_company', true));
				?>
					<div class="post-item row">

						<div class="row" style="margin-left:-70px;width:530px;padding-top:10px;">
							<div class="isys-leftbar">
								<strong class="likes-count"><?php print intval(get_post_meta(get_the_ID(), 'likes', true))?></strong>
								<a class="post-vote vote-up" vote="up" post="<?php the_ID()?>"><?php print isys_visitor_posts::translate('like')?></a>
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
								kategori: <a href="<?php print get_term_link($kategori)?>"><?php print $kategori->name?></a>
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
					//wp_reset_query();
				}else{
					echo isys_visitor_posts::translate('no-posts');
				}
				?>
				</div>
			<div class="widget-area">
				<?php
				$category_meta = get_option("category_taxonomy_term_{$kategori->term_id}");
				$sideimage = $category_meta['sideimage_term_meta'];
				if(strlen($sideimage)) {
					?>
					<p><img src="<?php print $sideimage?>" width="300"/></p>
					<hr style="border:0 none;border-top:1px solid #c3c3c3;background:transparent;margin:20px 0;display:block;"/>
					<?php
				}
				?>
				<form class="search-form" method="GET" action="<?php echo site_url()?>">
					<input type="text" value="HVAD LEDER DU EFTER..." name="s" id="s" onblur="if (this.value == '') { this.value = 'HVAD LEDER DU EFTER...';}" onfocus="if (this.value == 'HVAD LEDER DU EFTER...') 			{this.value = '';}"/>
					<input type="hidden" id="searchsubmit"/>
					<input type="hidden" name="post_type" value="blogindlæg">
				</form>
			</div>
		</div><!-- #container -->
<?php 
get_footer();
?>