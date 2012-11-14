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
							$attachments = get_post_meta(get_the_ID(), 'attachments');
							$companies = isys_visitor_posts::get_company(get_post_meta(get_the_ID(), 'post_company', true));
						?>
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<div class="row" style="width:530px;margin-left:-70px;">
								<div class="isys-leftbar" style="padding:5px 0 0 0;">
									<strong class="likes-count"><?php print intval(get_post_meta(get_the_ID(), 'likes', true))?></strong>
									<a class="post-vote vote-up" vote="up" post="<?php the_ID()?>"><?php print isys_visitor_posts::translate('like')?></a>
									<?php if(get_post_meta(get_the_ID(), 'favourite_box', true) == 'Yes'){?>
									<a class="favourite_box"></a>
									<?php }?>
								</div>														
								<div class="isys-half" style="font-family:Gill Sans W02,Arial,sans-serif;">
									<h1 class="entry-title" style="color:#d92b82;"><?php the_title(); ?></h1>
									<br/>
									INDLÆG AF
									<br/>
									<span style="text-transform:uppercase;"><?php $author_email = get_post_meta(get_the_ID(), 'author_email')?> <a href="mailto:<?php print $author_email[0]?>"><?php print $author_email[0]?></a></span>
									<br/>
									<span style="text-transform:lowercase;"><?php print date('l j. F o', get_the_time('U'))?></span>
									<br/>
									godkendt af <a href=""><span style="text-transform:uppercase;"><?php the_modified_author()?></span></a>
									<br/>
									<span style="text-transform:lowercase;"><?php print date('l j. F o', get_the_modified_time('U'))?></span>
									<br/>
									kategori: <a href="<?php print get_term_link($categories[0])?>" style="text-transform:uppercase;"><?php print $categories[0]->name?></a>
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
							<div class="entry-content">
								<?php the_content(); ?>
							</div>
						</div>
						<div id="comments" style="padding:0 0 0 40px;">
							<div class="row">
								<div class="isys-half" style="color:#9a9a9a;">
									(<?php echo get_comments_number()?>) KOMMENTARER
									<br/>
									<br/>
								</div>
								<?php if(count($attachments)){?>
								<div class="isys-half">
									<?php foreach($attachments[0] as $attachment_id => $attachment_name){?>
										<br/><a href="<?php print wp_get_attachment_url($attachment_id)?>"><?php print $attachment_name?></a>
									<?php }?>
								</div>
								<?php }?>
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
								<input type="hidden" name="action" value="isys_visitor_plugin"/>
								<input type="hidden" name="do" value="create-comment"/>
								<input type="hidden" name="post_id" value="<?php the_ID()?>"/>
								<label>
									<input type="text" name="author_name" placeholder="<?php print isys_visitor_posts::translate('enter-name')?>"/>
								</label>
								<label>
									<textarea rows="3" cols="12" name="comment_content" placeholder="<?php print isys_visitor_posts::translate('add-comment')?>"></textarea>
								</label>
								<label id="recaptcha_widget">
									<span id="recaptcha_image" class="field"></span>
								</label>
								<label>
									<span><a href="javascript:Recaptcha.reload()"><?php print isys_visitor_posts::translate('switch-words')?></a></span>
									<input type="text" name="recaptcha_response_field" id="recaptcha_response_field" maxlength="128" placeholder="<?php print isys_visitor_posts::translate('enter-captcha')?>" id="recaptcha_response_field" />
								</label>								
								<input type="submit" class="button" value="<?php print isys_visitor_posts::translate('submit-comment')?>"/>
							</form>
				  			<script type="text/javascript" src="http://api.recaptcha.net/js/recaptcha_ajax.js"></script>
							<script type="text/javascript">
								var RecaptchaOptions = {
							    	theme : 'custom'
								};
							 </script>  							
							<script type="text/javascript">
								Recaptcha.create("6Lfgi9gSAAAAAOBUxMtjJlSd8PNn1sxQbgH1OP6e", document.getElementById('recaptcha_widget'), {theme: "custom"});
							</script>					
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
					<p><img src="<?php print $sideimage?>" width="300"/></p>
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
				<p><a href="<?php print isys_visitor_posts::$landing_page_slug?>">TILBAGE TIL FORSIDEN</a></p>
			</div>
		</div>

<?php get_footer(); ?>
