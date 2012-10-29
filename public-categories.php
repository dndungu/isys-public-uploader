<?php

/**
 * Template Name: Public Categories
 */

get_header();

?>

	<div id="container">
		<?php
			$terms_table = $wpdb->prefix . 'terms';
			$term_taxonomy_table = $wpdb->prefix . 'term_taxonomy';
			$categories = get_terms('public-post-category', array('hide_empty' => 0));
			//$categories = $wpdb->get_results(sprintf("SELECT `name`, `description`, `%s`.`term_id`, `slug`, `term_taxonomy_id`, `taxonomy` FROM `%s` RIGHT JOIN `%s` ON (`%s`.`term_id` = `%s`.`term_id`) WHERE `taxonomy` = 'public-post-category'", $terms_table, $terms_table, $term_taxonomy_table, $terms_table, $term_taxonomy_table));
			
		?>
		<?php foreach($categories as $category) {?>
		<?php
		$term_id = $category->term_id;
		$term_meta = get_option( "taxonomy_term_{$term_id}" );
		var_dump($term_meta);
		continue;
		?>
			<a href="<?php echo get_term_link($category)?>" style="width:25%;display:inline;margin:10px 4%;text-align:center;float:left;">
				<img src="<?php echo $category->description?>" style="width:100%;display:block;"/>
				<span style="font-size:1.25em;display:block;margin:10px 0 0 0;"><?php echo $category->name?></span>
			</a>
		<?php }?>
	</div>

<?php

get_footer();

?>