<?php
/*
Plugin Name: iSys Public Uploader
Plugin URI: http://www.isys.dk
Description: It allows for public posts
Version: 1.0
Author: David Njuguna
Author URI: http://www.davidnjuguna.com
*/

class isys_visitor_posts {
	
	public function create_post_type(){
		register_post_type('public-post',
			array(
					'label'			=> __('Visitor Posts'),
					'public'		=> true,
					'show_ui'		=> true,
					'query_var'		=> 'public-post',
					'rewrite'		=> array('slug' => 'public-posts'),
					'hierarchical'	=> true,
					'menu_position'	=> 5,
					'supports'		=> array(
							'title',
							'excerpts',
							'editor',
							'comments'
					),
			)
		);
		
		register_taxonomy('public-post-category', 'public-post', 
			array(
				'hierarchical'    => true,
				'label'           => __('Categories'),
				'query_var'       => 'public-post-category',
				'rewrite'         => array('slug' => 'categories' ),
			)
		);	
		
		register_taxonomy('public-post-company', 'public-post', 
			array(
				'hierarchical'	=> true,
				'label'			=> __('Companies'),
				'labels'		=> array(
						'name' => _x( 'Companies', 'taxonomy general name' ),
						'singular_name' => _x( 'Company', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Companies' ),
						'all_items' => __( 'All Companies' ),
						'parent_item' => __( 'Parent Company' ),
						'parent_item_colon' => __( 'Parent Company:' ),
						'edit_item' => __( 'Edit Company' ),
						'update_item' => __( 'Update Company' ),
						'add_new_item' => __( 'Add New Company' ),
						'new_item_name' => __( 'New Company Name' ),
						'menu_name' => __( 'Companies' ),
				),
				'query_var'       => 'public-post-company',
				'rewrite'         => array('slug' => 'companies' ),
			)
		);		
	}
	
	public function ajax_controller(){
		switch(filter_input(INPUT_POST, 'do', FILTER_SANITIZE_STRING)){
			case 'create-post':
				isys_visitor_posts::create_post();
				break;
			case 'create-comment':
				isys_visitor_posts::create_comment();
				break;
			default:
				break;
		}
	}
	
	private function create_post(){
		$postID = wp_insert_post(array(
				'post_title' => isys_visitor_posts::postString('title'),
				'post_content' => isys_visitor_posts::postString('description'),
				'post_status' => 'draft',
				'post_type' => 'public-post'
				));
		add_post_meta($postID, 'author_email', isys_visitor_posts::postString('email'));
		wp_set_post_terms($postID, isys_visitor_posts::postInteger('category'), 'public-post-category');
		wp_set_post_terms($postID, isys_visitor_posts::postInteger('company'), 'public-post-company');
		echo $postID;die();
	}
	
	public function postString($key){
		return filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
	}
	
	public function postInteger($key){
		return filter_var(filter_input(INPUT_POST, $key, FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
	}
	
	private function create_comment(){
		$commendID = wp_insert_comment();
	}
	
	private function store_upload(){
		
	}
	
	public function archive_template($template){
		global $post;
		$category_template = dirname( __FILE__ ).'/public-category.php';
		return $post->post_type == 'public-post' ? $category_template : is_null($post) ? $category_template :  $template; 
	}
	
	public function single_template($template){
		global $post;
		$single_template = dirname( __FILE__ ).'/public-single.php';
		return $post->post_type == 'public-post' ? $single_template : $template;
	}
	
	public function page_template($template){
		global $post;
		switch($post->ID){
			case 2:
				return dirname( __FILE__ ).'/public-categories.php';
			break;
			case 3030:
				return dirname( __FILE__ ).'/public-post-form.php';
			break;
			default:
				return $template;
			break;
		}
	}
	
	public function enqueue_scripts(){
		wp_register_style( 'isys-public-uploader-style', plugins_url('style.css', __FILE__) );
		wp_enqueue_style( 'isys-public-uploader-style' );
		wp_register_script('isys-public-uploader-script', plugins_url('js/isys_visitor_posts.js', __FILE__), array('jquery'));
		wp_enqueue_script('isys-public-uploader-script');
		wp_localize_script('isys-public-uploader-script', 'isys_public_uploader_the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
	
	public function category_add_meta_field(){
		?>
		<div class="form-field">
			<label for="term_meta[thumbnail_term_meta]"><?php echo __('Thumbnail'); ?></label>
			<input type="text" name="term_meta[thumbnail_term_meta]" id="term_meta[thumbnail_term_meta]" value=""/>
			<p class="description"><?php echo __('Enter thumbnail URL here'); ?></p>			
		</div>
		<div class="form-field">
			<label for="term_meta[weight_term_meta]"><?php echo __('Weight'); ?></label>
			<input type="text" name="term_meta[weight_term_meta]" id="term_meta[weight_term_meta]" value="" size="2" maxlength="2"/>
			<p class="description"><?php echo __('Enter sorting weight here'); ?></p>			
		</div>
		<?php
	}
	
	function category_edit_meta_field($term){
		$id = $term->term_id;
		$term_meta = get_option( "taxonomy_{$id}" );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[thumbnail_term_meta]"><?php echo __('Thumbnail'); ?></label></th>
			<td>
				<input type="text" name="term_meta[thumbnail_term_meta]" id="term_meta[thumbnail_term_meta]" value="<?php echo esc_attr( $term_meta['custom_term_meta'] ) ? esc_attr( $term_meta['custom_term_meta'] ) : ''; ?>">
				<p class="description"><?php echo __('Enter thumbnail source URL here'); ?></p>
			</td>
		</tr>		
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[weight_term_meta]"><?php echo __('Weight'); ?></label></th>
			<td>
				<input type="text" name="term_meta[weight_term_meta]" id="term_meta[weight_term_meta]" value="<?php echo esc_attr( $term_meta['custom_term_meta'] ) ? esc_attr( $term_meta['custom_term_meta'] ) : ''; ?>">
				<p class="description"><?php echo __('Enter sorting weight here'); ?></p>
			</td>
		</tr>		
		<?php
	}
	
	public function company_add_meta_field(){
		?>
		<div class="form-field">
			<label for="term_meta[thumbnail_term_meta]"><?php echo __('Thumbnail'); ?></label>
			<input type="text" name="term_meta[thumbnail_term_meta]" id="term_meta[thumbnail_term_meta]" value=""/>
			<p class="description"><?php echo __('Enter thumbnail URL here'); ?></p>			
		</div>
		<?php
	}
	
	public function company_edit_meta_field($term){
		$id = $term->term_id;
		$term_meta = get_option( "taxonomy_{$id}" );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[thumbnail_term_meta]"><?php echo __('Thumbnail'); ?></label></th>
			<td>
				<input type="text" name="term_meta[thumbnail_term_meta]" id="term_meta[thumbnail_term_meta]" value="<?php echo esc_attr( $term_meta['custom_term_meta'] ) ? esc_attr( $term_meta['custom_term_meta'] ) : ''; ?>">
				<p class="description"><?php echo __('Enter thumbnail source URL here'); ?></p>
			</td>
		</tr>
		<?php
	}
	
	function taxonomy_save_meta( $term_id ) {
		if(!isset( $_POST['term_meta'] )) return;
		$term_meta = get_option( "taxonomy_{$term_id}" );
		error_log(json_encode($term_meta) . ' : ' . $term_id);
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if(!isset ( $_POST['term_meta'][$key] )) continue;
			$term_meta[$key] = $_POST['term_meta'][$key];
		}
		update_option( "taxonomy_term_{$term_id}", $term_meta );
	}	
		
}

add_action('init', array('isys_visitor_posts', 'create_post_type'));

add_filter('archive_template', array('isys_visitor_posts', 'archive_template'));

add_filter('single_template', array('isys_visitor_posts', 'single_template'));

add_filter('page_template', array('isys_visitor_posts', 'page_template'));

add_action('wp_enqueue_scripts', array('isys_visitor_posts', 'enqueue_scripts'));

add_action('wp_ajax_isys_visitor_plugin', array('isys_visitor_posts', 'ajax_controller'));

add_action('wp_ajax_nopriv_isys_visitor_plugin', array('isys_visitor_posts', 'ajax_controller'));

add_action('public-post-category_add_form_fields', array('isys_visitor_posts', 'category_add_meta_field'), 10, 2);

add_action('public-post-category_edit_form_fields', array('isys_visitor_posts', 'category_edit_meta_field'), 10, 2);

add_action('public-post-company_add_form_fields', array('isys_visitor_posts', 'company_add_meta_field'), 10, 2);

add_action('public-post-company_edit_form_fields', array('isys_visitor_posts', 'company_edit_meta_field'), 10, 2);

add_action( 'edited_public-post-category', array('isys_visitor_posts', 'taxonomy_save_meta'), 10, 2 );

add_action( 'create_public-post-category', array('isys_visitor_posts', 'taxonomy_save_meta'), 10, 2 );

add_action( 'edited_public-post-company', array('isys_visitor_posts', 'taxonomy_save_meta'), 10, 2 );

add_action( 'create_public-post-company', array('isys_visitor_posts', 'taxonomy_save_meta'), 10, 2 );