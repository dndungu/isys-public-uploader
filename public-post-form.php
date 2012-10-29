<?php

get_header();

?>
<?php
$parts = explode("/", $_SERVER['REQUEST_URI']);
$category_id = $parts[(count($parts) - 2)];
?>

	<div id="container" class="isys_visitor_posts">
		<div id="content" role="main">
			<a href="<?php echo get_permalink(2)?>">Back to main page</a>
			<br/><br/>
			<h1 class="page-title"></h1>
			<form method="POST" id="isys_visitor_post_form">
				<input type="hidden" name="action" value="isys_visitor_plugin"/>
				<input type="hidden" name="do" value="create-post"/>
				<input type="hidden" name="category" value="<?php echo $category_id?>"/>
				<label>
					<span>Title</span>
					<input type="text" name="title" placeholder="My title"/>
				</label>
				<label>
					<span>Description</span>
					<textarea name="description" placeholder="My text about this issue"></textarea>
				</label>
				<label>
					<span>Your name</span>
					<input type="text" name="author" placeholder="My name"/>
				</label>
				<label>
					<span>Your email</span>
					<input type="text" name="email" placeholder="myname@mycompany.dk"/>
				</label>
				<label>
					<span>Your workplace</span>
					<?php 
						$companies = get_terms(array('taxonomy' => 'public-post-company'));
						if(count($companies)){
					?>
					<select name="company">
						<?php foreach($companies as $company){?>
							<option value="<?php echo $company->term_id?>"><?php echo $company->name?></option>
						<?php }?>
					</select>
					<?php
						} 
					?>
				</label>
				<label>
					<span>Added files</span>
					<input type="file" name="attachment"/>
				</label>
				<label>
					<input type="submit" name="submit" value="Post"/>
				</label>
			</form>
			<?php $category_term = get_term_by('id', $category_id, 'public-post-category')?>
			<input type="hidden" name="category_name" id="category_name" value="<?php echo $category_term->name?>"/>
		</div>
	</div>

<?php

get_sidebar();

get_footer();

?>