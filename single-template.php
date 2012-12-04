<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header();

$categories = wp_get_post_terms($post->ID, 'blog-indlaeg-kategori');

?>


		<div id="container" class="isys_visitor_posts">
			<div id="content" role="main">
				<div class="row">
					<a class="isys-black-link" href="<?php echo get_bloginfo('url') . '/' . isys_visitor_posts::$form_page_slug . '/?'. $categories[0]->term_id?>"><?php echo __('OPRET NYT INDLÆG')?></a>
				</div>
				<?php if(have_posts()) {?>
					<?php while(have_posts()){?>
						<?php 
							the_post();
							$attachments = get_post_meta(get_the_ID(), 'attachments', true);
							$companies = isys_visitor_posts::get_company(get_post_meta(get_the_ID(), 'post_company', true));
						?>
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<div class="row" style="width:530px;margin-left:-70px;">
								<div class="isys-leftbar" style="padding:5px 0 0 0;">
									<strong class="likes-count" style="padding-bottom:0px;"><?php print intval(get_post_meta(get_the_ID(), 'likes', true))?></strong>
									<a class="post-vote vote-up" vote="up" post="<?php the_ID()?>"><?php print isys_visitor_posts::translate('like')?></a>
									<?php if(get_post_meta(get_the_ID(), 'favourite_box', true) == 'Yes'){?>
									<a class="favourite_box"></a>
									<?php }?>
								</div>														
								<div class="isys-half" style="font-family:Gill Sans W02,Arial,sans-serif;color:#9a9a9a;">
									<h2 class="entry-title" style="color:#d92b82;margin-bottom:-10px;">
										<?php the_title(); ?>
									</h2>
									<br/>
									<span style="font-family:Gill Sans W02 Bold;font-weight:600;font-size:12px;">
										INDLÆG AF
									</span>
									<?php $author_name = get_post_meta(get_the_ID(), 'author_name', true)?>
									<?php $author_name = strlen($author_name) ? $author_name : (count($companies) ? $companies[0]->organisation : false)?>
									<?php if($author_name){?>
									<br/>									
									<span style="text-transform:uppercase;font-size:12px;">									
										<?php print $author_name?>
									</span>
									<?php }?>
									<br/>
									<?php setlocale(LC_ALL, 'da_DK');?>
									<span style="text-transform:lowercase;font-size:12px;"><?php print strftime('%A %e. %B %Y', get_the_time('U'))?></span>
									<br/>
									<span style="font-size:12px;">
										kategori: <a href="<?php print get_term_link($categories[0])?>" style="text-transform:uppercase;"><?php print $categories[0]->name?></a>
									</span>
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
							<div class="entry-content" style="padding-top:20px;font-size:20px;line-height:24px;">
								<?php the_content(); ?>
							</div>
						</div>
						<div id="comments" style="padding:0 0 0 40px;font-family: Gill Sans W02,Arial,sans-serif;">
							<div class="row">
								<div class="isys-half" style="color:#9a9a9a;width:50%;font-family: Gill Sans Bold W02;font-weight:600;font-size:12px;">
									(<?php echo get_comments_number()?>) KOMMENTARER
									<br/>
									<br/>
								</div>
								<div class="isys-half" style="width:50%;text-align:right;">
									<?php if(count($attachments)){?>
									<?php foreach($attachments as $attachment_id => $attachment_name){?>
										<a class="pdf_link" title="<?php print $attachment_name?>" href="<?php print str_replace(realpath($_SERVER["DOCUMENT_ROOT"]), '', wp_get_attachment_url($attachment_id))?>"></a>
									<?php }?>
									<?php }?>
								</div>
							</div>
							<?php $comments = get_comments(array('post_id' => get_the_ID(), 'status' => 'approve'))?>
							<?php if(count($comments)){?>
								<?php foreach($comments as $comment){?>
									<div class="row isys-comment">
										<div class="isys-comment-header"> 
											<a href=""><?php print $comment->comment_author?></a> <span class="comment-ip">(<?php print $comment->comment_author_IP?>)</span> <?php print date('l j. F o', strtotime($comment->comment_date_gmt))?>
										</div>
										<div class="isys-comment-content">
											<?php print $comment->comment_content?>
										</div>
									</div>
								<?php }?>
							<?php }?>
							<form method="POST" id="isys_visitor_comment_form">
								<h2 style="font-family:Gill Sans W02,Arial,sans-serif;">TILFØJ KOMMENTAR</h2>
								<input type="hidden" name="action" value="isys_visitor_plugin"/>
								<input type="hidden" name="do" value="create-comment"/>
								<input type="hidden" name="post_id" value="<?php the_ID()?>"/>
								<label>
									<input type="text" name="author_name" onblur="if (this.value == '') { this.value = '<?php print isys_visitor_posts::translate('enter-name')?>';}" onfocus="if (this.value == '<?php print isys_visitor_posts::translate('')?>'){this.value = 'enter-name';}" value="<?php print isys_visitor_posts::translate('enter-name')?>"/>
								</label>
								<label>
									<textarea rows="3" cols="12" name="comment_content" onblur="if (this.value == '') { this.value = '<?php print isys_visitor_posts::translate('author_name')?>';}" onfocus="if (this.value == '<?php print isys_visitor_posts::translate('enter-comment')?>'){this.value = '';}"><?php print isys_visitor_posts::translate('enter-comment')?></textarea>
								</label>
								<?php if(!isys_visitor_posts::isAuthenticated()){?>
								<label>
									<input type="text" name="author_username" id="author_username" onblur="if (this.value == '') { this.value = '<?php print isys_visitor_posts::translate('enter-username')?>';}" onfocus="if (this.value == '<?php print isys_visitor_posts::translate('enter-username')?>'){this.value = '';}" value="<?php print isys_visitor_posts::translate('enter-username')?>" autocomplete="off"/>
								</label>
								<label>
									<input type="password" name="author_password" id="author_password" placeholder="<?php print isys_visitor_posts::translate('enter-password')?>" autocomplete="off"/>
								</label>
								<?php }?>								
								<div class="label">
									<input type="submit" class="isys-black-link" value="<?php print isys_visitor_posts::translate('submit-comment')?>"/>
								</div>
								<?php if(!isys_visitor_posts::isAuthenticated()){?>
								<label>
									For at kunne skrive en blogpost skal du være logget ind. Du modtager et login når du skriver under på Københavns Mangfoldighedscharter.<br/>Læs mere og underskriv chartret her: <a href="http://www.blanddigibyen.dk/skrivunder/">http://www.blanddigibyen.dk/skrivunder/</a>
								</label>
								<?php }?>
							</form>
						</div>
						<div class="row" style="font-family: Gill Sans W02 Light;color:#9a9a9a;margin:150px 0 0 0;font-size:10px;line-height:14px;">
							<strong style="font-family: Gill Sans W02 Bold,Arial,sans-serif;">ANSVAR</strong>
							<br/>
							Det er tanken, at VGM værktøjskassen skal være dynamisk, og indholdet skal komme fra brugerne på sitet. Københavns Kommune tager således ikke ansvar for indlæggene på sitet.
							Københavns Kommune forbeholder sig dog ret til at redigere og slette i indlæggene, hvis de vurderes at have stødende, fejlagtig eller injurierende karakter. Det håber vi dog ikke bliver tilfældet.						
						</div>
						
					<?php }?>
				<?php }?>
			</div>
			<div class="widget-area">
				<?php
				$category_meta = get_option("category_taxonomy_term_{$categories[0]->term_id}");
				$sideimage = $category_meta['sideimage_term_meta'];
				if(strlen($sideimage)) {
					?>
					<p><img src="<?php print $sideimage?>" width="300" style="border:1px solid #000;"/></p>
					<hr style="border:0 none;border-top:1px solid #c3c3c3;background:transparent;margin:20px 0;display:block;"/>
					<?php
				}
				?>
				<?php ?>				
				<form class="search-form" method="GET" action="<?php echo site_url()?>">
					<input type="text" value="HVAD LEDER DU EFTER..." name="s" id="s" onblur="if (this.value == '') { this.value = 'HVAD LEDER DU EFTER...';}" onfocus="if (this.value == 'HVAD LEDER DU EFTER...') 			{this.value = '';}"/>
					<input type="hidden" id="searchsubmit"/>
					<input type="hidden" name="post_type" value="blogindlæg">
				</form>
				<p style="font-family: Gill Sans W02,Arial,sans-serif;margin-left:45px;font-weight:900;">
					<a href="/<?php print isys_visitor_posts::$landing_page_slug?>">TILBAGE TIL FORSIDEN</a>
				</p>
			</div>
		</div>

<?php get_footer(); ?>
