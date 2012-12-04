<?php

/**
 * Template Name: Public Categories
 */

get_header();

?>

	<div id="container" class="isys_visitor_posts">
		<div class="row">
			<div class="sixoften"><h1 style="color:#000;"><?php echo __('VÆKST & MANGFOLDIGHED')?></h1></div>
			<div class="fouroften">
				<form class="search-form" method="GET" action="<?php echo site_url()?>">
						<input type="text" value="HVAD LEDER DU EFTER..." name="s" id="s" onblur="if (this.value == '') { this.value = 'HVAD LEDER DU EFTER...';}" onfocus="if (this.value == 'HVAD LEDER DU EFTER...'){this.value = '';}"/>
						<input type="hidden" id="searchsubmit"/>
						<input type="hidden" name="post_type" value="blogindlæg">
				</form>
			</div>
		</div>
		<?php
			$terms_table = $wpdb->prefix . 'terms';
			$term_taxonomy_table = $wpdb->prefix . 'term_taxonomy';
			$categories = get_terms('blog-indlaeg-kategori', array('hide_empty' => 0, 'orderby' => 'count', 'order' => 'DESC'));
			foreach($categories as $category){
				$term_meta = get_option( "category_taxonomy_term_{$category->term_id}" );
				$weight = $term_meta['weight_term_meta'];
				$rows[$weight] = array(
								'url' => get_term_link($category),
								'thumbnail' => $term_meta['thumbnail_term_meta'],
								'name' => $category->name
								);
			}
			ksort($rows);
		?>
		<?php 
			foreach($rows as $weight => $row) {
		?>
			<div class="public-category-item" weight="<?php print $weight?>">
				<a href="<?php echo $row['url']?>" class="public-category-link">
					<span class="public-category-image" style="background-image:url(<?php echo $row['thumbnail']?>);"></span>
					<span class="public-category-name"><?php echo $row['name']?></span>
				</a>
			</div>
		<?php }?>
	</div>

<?php

get_footer();

?>