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
							<span style="font-size:1.5em;font-family:Gill Sans W02;font-weight:900;">
								<a href="">INDLÆG</a>
								<br/>
							</span>
							<span style="font-family:Gill Sans W02 Light;">
								arkivet indeholder <?php print $kategori->count?> indlæg
							</span>
							<br/><br/><br/><br/>					
							<?php
							$page_number = (get_query_var('paged')) ? get_query_var('paged') : 1;
							$posts_from = (($page_number - 1) * get_option('posts_per_page'));
							?>
							<strong> | <?php print $posts_from?> – <?php print intval($posts_from + get_option('posts_per_page'))?> af <?php print intval($kategori->count)?> | <?php next_posts_link( __( 'FLERE', '' ) ); previous_posts_link( __( 'FLERE', '' ) )?> | </strong>
							<br/><br/>	
						</div>
						<div class="isys-half">
							<a class="isys-black-link" style="float:right;" href="<?php echo get_bloginfo('url') . '/' . isys_visitor_posts::$form_page_slug . '/?' . (intval($kategori->term_id) > 0 ? $kategori->term_id : 15)?>"><?php echo __('OPRET NYT INDLÆG')?></a>
						</div>
					</div>
				<?php
				query_posts(array('post_type'=>'blogindlaeg', 'blogindlaegs' => $slug, 'posts_per_page' => get_option('posts_per_page'), 'paged' => get_query_var('paged')));
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
							<div class="isys-half" style="font-family:Gill Sans W02,Arial,sans-serif;color:#9a9a9a;">
								<h2>
									<a href="<?php echo the_permalink()?>"><?php echo the_title()?></a>
								</h2>
								INDLÆG AF
								<br/>
								<span style="text-transform:uppercase;"><?php $author_email = get_post_meta(get_the_ID(), 'author_email')?> <a href="mailto:<?php print $author_email[0]?>"><?php print $author_email[0]?></a></span>
								<br/>
								<span style="text-transform:lowercase;"><?php print date('l j. F o', get_the_time('U'))?></span>
								<br/>
								kategori: <a href="<?php print get_term_link($kategori)?>"><?php print $kategori->name?></a>
								<br/>
								<br/>
							</div>
							<div class="isys-half">
								<?php if(count($companies)){?>
								<?php $logo = $companies[0]?>
								<img src="<?php print site_url() . '/' . $logo->path . '/thumbs/thumbs_' . $logo->filename?>" alt="<?php print $logo->alttext?>" class="isys-company-logo"/>
								<?php }?>
							</div>
						</div>	 
						<div class="entry-summary">
							<div class="row">
								<?php echo the_excerpt()?>
							</div>
							<div class="row">
								<?php $attachments = get_post_meta(get_the_ID(), 'attachments', true)?>
								<div class="isys-half" style="color:#9a9a9a;width:75%;font-family: Gill Sans Bold W02;font-weight:600;font-size:12px;">
									<p>
										DER ER <a href="<?php echo the_permalink()?>"><?php echo get_comments_number()?></a> KOMMENTARER
										<br/>
										DER ER  <a href=""><?php echo count($attachments)?></a> DOKUMENTER VEDHÆFTET
									</p>
								</div>
								<div class="isys-half"  style="width:25%;">
									<?php if(count($attachments)){?>
									<a class="isys-attachments-count">
										<span><?php echo count($attachments)?></span>
									</a>
									<?php }?>
								</div>
							</div>
						</div>
					</div>
				<?php	
					}
					wp_reset_query();
				}else{
					echo isys_visitor_posts::translate('no-posts');
				}
				?>
					<div class="row" style="font-family: Gill Sans W02 Light,Arial,sans-serif;color:#9a9a9a;margin:150px 0 0 0;">
						<strong style="font-family: Gill Sans W02 Bold,Arial,sans-serif;">ANSVAR</strong>
						<br/>
						Det er tanken, at VGM værktøjskassen skal være dynamisk, og indholdet skal komme fra brugerne på sitet. Københavns Kommune tager således ikke ansvar for indlæggene på sitet.
						Københavns Kommune forbeholder sig dog ret til at redigere og slette i indlæggene, hvis de vurderes at have stødende, fejlagtig eller injurierende karakter. Det håber vi dog ikke bliver tilfældet.						
					</div>
				</div>
			<div class="widget-area">
				<?php
				$category_meta = get_option("category_taxonomy_term_{$kategori->term_id}");
				$sideimage = $category_meta['sideimage_term_meta'];
				if(strlen($sideimage)) {
					?>
					<p><img src="<?php print $sideimage?>" width="300" style="border:1px solid #000;"/></p>
					<hr style="border:0 none;border-top:1px solid #c3c3c3;background:transparent;margin:20px 0;display:block;"/>
					<?php
				}
				?>
				<form class="search-form" method="GET" action="<?php echo site_url()?>">
					<input type="text" value="HVAD LEDER DU EFTER..." name="s" id="s" onblur="if (this.value == '') { this.value = 'HVAD LEDER DU EFTER...';}" onfocus="if (this.value == 'HVAD LEDER DU EFTER...') 			{this.value = '';}"/>
					<input type="hidden" id="searchsubmit"/>
					<input type="hidden" name="post_type" value="blogindlæg">
				</form>
				<p style="margin-left:45px;font-weight:900;font-family: Gill Sans W02,Arial,sans-serif;">
					<a href="/<?php print isys_visitor_posts::$landing_page_slug?>">TILBAGE TIL FORSIDEN</a>
				</p>
			</div>
		</div><!-- #container -->
<?php 
get_footer();
?>