<?php

get_header();

?>
<?php

$parts = explode("/", $_SERVER['REQUEST_URI']);
$category_id = str_replace('?', '', $parts[(count($parts) - 1)]);

?>

	<div id="container" class="isys_visitor_posts">
			
			<a href="/visitor-posts"><?php echo __('Back to main page')?></a>
			<br/><br/>
			<h1 class="page-title"></h1>
			<form method="POST" id="isys_visitor_post_form">
				<input type="hidden" name="action" value="isys_visitor_plugin"/>
				<input type="hidden" name="do" value="create-post"/>
				<input type="hidden" name="category" value="<?php echo intval($category_id)?>"/>
				<label>
					<span>Title</span>
					<input type="text" name="title" placeholder="My title"/>
				</label>				
				<div id="postdivrich" class="postarea" style="float:left;width:95%;display:inline-block;padding:10px 0;">
					<?php wp_editor('', 'description', array('media_buttons' => false, 'textarea_name' => 'description', 'textarea_rows' => 10))?>
				</div>
				<label>
					<span>Your name</span>
					<input type="text" name="author_name" placeholder="My name"/>
				</label>
				<label>
					<span>Your email</span>
					<input type="text" name="author_email" placeholder="myname@mycompany.dk"/>
				</label>
				<?php $companies = get_terms(array('taxonomy' => 'public-post-company'))?>
				<?php if(count($companies)){?>
				<label>
					<span>Your workplace</span>
					<select name="company">
						<?php foreach($companies as $company){?>
							<option value="<?php echo $company->term_id?>"><?php echo $company->name?></option>
						<?php }?>
					</select>
				</label>
				<?php }?>
				<p class="errorBox"></p>
				<label class="attachments">
					<span><?php echo __('Added files')?></span>
					<span class="field">
						<input type="file" name="file_upload" style="float:left;width:100%;margin:5px 0;"/>
					</span>
				</label>
  				<label style="display:none;">
  					<span class="progress"></span>
  				</label>
				<label id="recaptcha_widget">
					<span id="recaptcha_image" class="field"></span>
				</label>
				<label>
					<span><a href="javascript:Recaptcha.reload()">Switch words</a></span>
					<input type="text" name="recaptcha_response_field" id="recaptcha_response_field" maxlength="128" placeholder="Enter the words above" id="recaptcha_response_field" />
				</label>
				<span style="float:left;width:95%;display:inline-block;">
					<input type="reset" name="cancel" class="button" value="Cancel"/> <input type="submit" name="submit" class="button" value="Post"/>
				</span>
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
			<?php $category_term = get_term_by('id', $category_id, 'public-post-category')?>
			<input type="hidden" name="category_name" id="category_name" value="<?php echo $category_term->name?>"/>
	</div>

<?php

//get_sidebar();

get_footer();

?>