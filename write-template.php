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
					<input type="text" name="title" onblur="if (this.value == '') { this.value = '<?php print isys_visitor_posts::translate('enter-title')?>';}" onfocus="if (this.value == '<?php print isys_visitor_posts::translate('enter-title')?>'){this.value = '';}" value="<?php print isys_visitor_posts::translate('enter-title')?>"/>
				</label>				
				<div id="postdivrich" class="postarea" style="float:left;width:100%;display:inline-block;padding:10px 0;">
					<?php wp_editor('', 'description', array('dfw' => true, 'media_buttons' => false, 'textarea_name' => 'description', 'textarea_rows' => 10, 'background-color' => '#e1e1e1'));?>
				</div>
				<label>
					<input type="text" name="author_name" onblur="if (this.value == '') { this.value = '<?php print isys_visitor_posts::translate('enter-name')?>';}" onfocus="if (this.value == '<?php print isys_visitor_posts::translate('enter-name')?>'){this.value = '';}" value="<?php print isys_visitor_posts::translate('enter-name')?>"/>
				</label>
				<label>
					<input type="text" name="author_email" onblur="if (this.value == '') { this.value = '<?php print isys_visitor_posts::translate('your-email')?>';}" onfocus="if (this.value == '<?php print isys_visitor_posts::translate('your-email')?>'){this.value = '';}" value="<?php print isys_visitor_posts::translate('your-email')?>"/>
				</label>
				<label class="attachments">
					<span><?php print isys_visitor_posts::translate('added-files')?></span>
					<span class="field">
						<span class="errorBox"></span>
						<?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') === true){?>
						<input type="file" name="file_upload" id="attachmentFiles" style="float:left;width:100%;margin:5px 0;" multiple="multiple"/>
						<?php }else{?>
						<iframe id="iframeuploader" name="iframeuploader" style="border:0;height:30px;" src="/wp-content/plugins/isys-public-uploader/uploader.php"></iframe>
						<?php }?>
						<span class="progress"></span>
					</span>
				</label>
				<?php if(!isys_visitor_posts::isAuthenticated()){?>
				<label>
					<input type="text" name="author_username" id="author_username" autocomplete="off" onblur="if (this.value == '') { this.value = '<?php print isys_visitor_posts::translate('enter-username')?>';}" onfocus="if (this.value == '<?php print isys_visitor_posts::translate('enter-username')?>'){this.value = '';}" value="<?php print isys_visitor_posts::translate('enter-username')?>"/>
				</label>
				<label>
					<input type="password" name="author_password" id="author_password" onblur="if (this.value == '') { this.value = '<?php print isys_visitor_posts::translate('enter-username')?>';}" onfocus="if (this.value == '<?php print isys_visitor_posts::translate('enter-password')?>'){this.value = '';}" value="<?php print isys_visitor_posts::translate('enter-password')?>" autocomplete="off"/>
				</label>
				<?php }?>
				<div class="label">
					<input type="submit" name="submit" class="isys-black-link" style="color:#fff;" value="<?php print isys_visitor_posts::translate('post')?>"/> <input type="reset" name="cancel" class="isys-black-link" style="color:#fff;" value="<?php print isys_visitor_posts::translate('cancel')?>"/>
				</div>
				<?php if(!isys_visitor_posts::isAuthenticated()){?>
				<label>
					For at kunne skrive en blogpost skal du være logget ind. Du modtager et login når du skriver under på Københavns Mangfoldighedscharter. Læs mere og underskriv chartret her: <a href="http://www.blanddigibyen.dk/skrivunder/">http://www.blanddigibyen.dk/skrivunder/</a>
				</label>
				<?php }?>
			</form>
			<?php $category_term = get_term_by('id', $category_id, 'blog-indlaeg-kategori')?>
			<input type="hidden" name="category_name" id="category_name" value="<?php echo $category_term->name?>"/>
	</div>

<?php

//get_sidebar();

get_footer();

?>