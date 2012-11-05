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
				<?php if(have_posts()) {?>
					<?php while(have_posts()){?>
						<?php 
							the_post();
							$companies = wp_get_post_terms($post->ID, 'public-post-company');
							$companies_meta = get_option("company_taxonomy_term_{$companies[0]->term_id}");
						?>
						<a href="<?php echo get_bloginfo('url')?>/public-posts"><?php echo __('Back to main page')?></a>
						<br/><br/>
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<div style="display:inline-block;float:left;width:50%;">
								<h1 class="entry-title"><?php the_title(); ?></h1>
							</div>
							<div style="display:inline-block;float:left;width:50%;">
								<img src="<?php echo $companies_meta['thumbnail_term_meta']?>" style="height:30px;float:right;"/>
							</div>
							<div class="entry-content">
								<?php the_content(); ?>
							</div>
						</div>
						<div class="row">
							<span class="likes-count"><?php echo intval(get_post_meta(get_the_ID(), 'likes', true))?></span> likes, 
							<span class="dislikes-count"><?php echo intval(get_post_meta(get_the_ID(), 'dislikes', true))?></span> dislikes,
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
										<?php echo $comment->comment_content?> - by <?php echo $comment->comment_author?> <?php echo human_time_diff(strtotime($comment->comment_date_gmt), current_time('timestamp'))?> ago
									</div>
								<?php }?>
							<?php }?>
							<form method="POST" id="isys_visitor_comment_form" style="border-top:1px dotted #888;padding:10px 0 0 0;">
								<input type="hidden" name="action" value="isys_visitor_plugin"/>
								<input type="hidden" name="do" value="create-comment"/>
								<input type="hidden" name="post_id" value="<?php the_ID()?>"/>
								<label>
									<span>Name</span>
									<input type="text" name="author_name" placeholder="<?php echo __('Enter your name')?>"/>
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
								<input type="submit" value="Submit Comment"/>
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

<?php get_footer(); ?>
