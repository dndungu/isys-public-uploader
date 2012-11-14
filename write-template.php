<?php

get_header();

$parts = explode("/", $_SERVER['REQUEST_URI']);
$category_id = str_replace('?', '', $parts[(count($parts) - 1)]);

?>

	<div id="container" class="isys_visitor_posts">
			
			<a href="/<?php print isys_visitor_posts::$landing_page_slug?>" class="isys-black-link"><?php echo isys_visitor_posts::translate('back-main-page')?></a>
			<br/><br/>
			<h1 class="page-title"></h1>
			<form method="POST" id="isys_visitor_post_form">
				<input type="hidden" name="action" value="isys_visitor_plugin"/>
				<input type="hidden" name="do" value="create-post"/>
				<input type="hidden" name="category" value="<?php print $category_id?>"/>
				<label>
					<span><?php print isys_visitor_posts::translate('title')?></span>
					<input type="text" name="title" placeholder="<?php print isys_visitor_posts::translate('enter-title')?>"/>
				</label>				
				<div id="postdivrich" class="postarea" style="float:left;width:95%;display:inline-block;padding:10px 0;">
					<?php wp_editor('', 'description', array('media_buttons' => false, 'textarea_name' => 'description', 'textarea_rows' => 10))?>
				</div>
				<label>
					<span><?php print isys_visitor_posts::translate('name')?></span>
					<input type="text" name="author_name" placeholder="<?php print isys_visitor_posts::translate('enter-name')?>"/>
				</label>
				<label>
					<span><?php print isys_visitor_posts::translate('your-email')?></span>
					<input type="text" name="author_email" placeholder="<?php print isys_visitor_posts::translate('email-sample')?>"/>
				</label>
				<?php
				$companies = isys_visitor_posts::get_companies();
				?>
				<label>
					<span>Your workplace</span>
					<select name="post_company">
						<option value="0"><?php print isys_visitor_posts::translate('select-company')?></option>
						<?php if(count($companies)){?>
						<?php foreach($companies as $company){?>
						<?php if(strlen($company->alttext)) {?>
							<option value="<?php echo $company->pid?>"><?php echo $company->alttext?></option>
						<?php }?>
						<?php }?>
						<?php }?>
					</select>
				</label>
				<label class="attachments">
					<span><?php print isys_visitor_posts::translate('added-files')?></span>
					<span class="field">
						<span class="errorBox"></span>
						<input type="file" name="file_upload" style="float:left;width:100%;margin:5px 0;"/>
						<span class="progress"></span>
					</span>
				</label>
				<label id="recaptcha_widget">
					<span>
						<input type="text" name="recaptcha_response_field" id="recaptcha_response_field" maxlength="128" placeholder="<?php print isys_visitor_posts::translate('added-files')?>" id="recaptcha_response_field" />
					</span>
					<span id="recaptcha_image" class="field"></span>
				</label>
				<label>
					<span>&nbsp;</span>
					<span class="field">
						<a href="javascript:Recaptcha.reload()"><?php print isys_visitor_posts::translate('switch-words')?></a>
					</span>
				</label>
				<span style="float:left;width:95%;display:inline-block;">
					<input type="reset" name="cancel" class="button" value="<?php print isys_visitor_posts::translate('cancel')?>"/> <input type="submit" name="submit" class="button" value="<?php print isys_visitor_posts::translate('post')?>"/>
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
			<?php $category_term = get_term_by('id', $category_id, 'blog-indlaeg-kategori')?>
			<input type="hidden" name="category_name" id="category_name" value="<?php echo $category_term->name?>"/>
	</div>

<?php

//get_sidebar();

get_footer();

?>