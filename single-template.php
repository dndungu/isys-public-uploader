<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>

		<div id="container" class="isys_visitor_posts">
			<div id="content" role="main">
				<?php if(have_posts()) {?>
					<?php while(have_posts()){?>
						<?php 
							the_post();
							$categories = wp_get_post_terms($post->ID, 'public-post-category');
							$companies = wp_get_post_terms($post->ID, 'public-post-company');
							$companies_meta = get_option("company_taxonomy_term_{$companies[0]->term_id}");
						?>
						<a href="<?php print get_bloginfo('url')?>/visitor-posts"><?php print __('Back to main page')?></a>
						<br/><br/>
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<div class="isys-half">
								<h1 class="entry-title" style="color:#d92b82;"><?php the_title(); ?></h1>
								<br/>
								Indlæg af <?php $author_email = get_post_meta(get_the_ID(), 'author_email')?> <a href="mailto:<?php print $author_email[1]?>"><?php print $author_email[1]?></a>
								<br/>
								<?php print date('l j.F o', get_the_time('U'))?>
								<br/>
								godkendt af <?php the_modified_author()?>
								<br/>
								kategori: <a href="<?php print get_term_link($categories[0])?>"><?php print $categories[0]->name?></a>
								<br/>
								<br/>
							</div>
							<div class="isys-half">
								<img src="<?php print $companies_meta['thumbnail_term_meta']?>" style="height:30px;float:right;"/>
							</div>
							<div class="entry-content">
								<?php the_content(); ?>
							</div>
						</div>
						<div class="row">
							<span class="likes-count"><?php print intval(get_post_meta(get_the_ID(), 'likes', true))?></span> likes, 
							<span class="dislikes-count"><?php print intval(get_post_meta(get_the_ID(), 'dislikes', true))?></span> dislikes,
							<span class="isys-button"> 
								<a class="post-vote vote-down" vote="down" post="<?php the_ID()?>"></a>
							</span>
							<span class="isys-button">
								<a class="post-vote vote-up" vote="up" post="<?php the_ID()?>"></a>
							</span>
						</div>
						<div id="comments">
							<?php $comments = get_comments(array('post_id' => get_the_ID()))?>
							<?php if(count($comments)){?>
								<?php foreach($comments as $comment){?>
									<div class="row" style="padding:5px 0;float:left;border-top:1px dotted #888;">
										<?php print $comment->comment_content?> - by <?php print $comment->comment_author?> <?php print human_time_diff(strtotime($comment->comment_date_gmt), current_time('timestamp'))?> ago
									</div>
								<?php }?>
							<?php }?>
							<form method="POST" id="isys_visitor_comment_form" style="border-top:1px dotted #888;padding:10px 0 0 0;">
								<input type="hidden" name="action" value="isys_visitor_plugin"/>
								<input type="hidden" name="do" value="create-comment"/>
								<input type="hidden" name="post_id" value="<?php the_ID()?>"/>
								<label>
									<span>Name</span>
									<input type="text" name="author_name" placeholder="<?php print __('Enter your name')?>"/>
								</label>
								<label>
									<span>Comment</span>
									<textarea rows="3" cols="12" name="comment_content" placeholder="Add a comment"></textarea>
								</label>
								<label id="recaptcha_widget">
									<span id="recaptcha_image" class="field"></span>
								</label>
								<label>
									<span><a href="javascript:Recaptcha.reload()">Switch words</a></span>
									<input type="text" name="recaptcha_response_field" id="recaptcha_response_field" maxlength="128" placeholder="Enter the words above" id="recaptcha_response_field" />
								</label>								
								<input type="submit" class="button" value="Submit Comment"/>
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
				
			</div>
		</div>

<?php get_footer(); ?>
