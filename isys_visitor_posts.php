<?php
/*
Plugin Name: iSys Public Uploader
Plugin URI: http://www.isys.dk
Description: It allows for public posts
Version: 1.0
Author: David Njuguna
Author URI: http://www.davidnjuguna.com
*/

require_once(dirname( __FILE__ ).'/libraries/recaptchalib.php');

class isys_visitor_posts {
	
	private static $recaptcha_public_key = '6Lfgi9gSAAAAAOBUxMtjJlSd8PNn1sxQbgH1OP6e';
	
	private static $recaptcha_private_key = '6Lfgi9gSAAAAABPWVmDvBqftb8A3FDQg9a3qp0qC';
	
	private static $landing_page_slug = 'visitor-posts';
	
	private static $landing_page_title = 'Public Posts';
	
	private static $form_page_slug = 'create-visitor-post';
	
	private static $form_page_title = 'Create Public Post';
	
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
		
	}
	
	public function ajax_controller(){
		switch(filter_input(INPUT_POST, 'do', FILTER_SANITIZE_STRING)){
			case 'create-post':
				self::create_post();
				break;
			case 'create-comment':
				self::create_comment();
				break;
			case 'get-post-maxsize':
				self:: doGetMaxSize();
				break;
			case 'upload-pdf':
				self::doFileUpload();
				break;
			case 'post-vote':
				self::doPostVote();
				break;
			default:
				break;
		}
	}
	
	private function doGetMaxSize(){
		$setting = ini_get('post_max_size');
		switch(strtolower($setting[strlen($setting)-1])){
			case 'g':
				echo ($setting * 1024 * 1024 * 1024);
				break;
			case 'm':
				echo ($setting * 1024 * 1024);
				break;
			case 'k':
				echo ($setting * 1024 * 1024);
				break;
		}
		die();
	}	
	
	private function create_post(){
		$response = recaptcha_check_answer(self::$recaptcha_private_key, $_SERVER["REMOTE_ADDR"], self::postString('recaptcha_challenge_field'), self::postString('recaptcha_response_field'));
		if(!$response->is_valid) {
			print json_encode(array('error' => "The reCAPTCHA was not entered correctly."));die();
			return;
		}
		$postID = wp_insert_post(array(
				'post_type' => 'public-post',
				'post_title' => self::postString('title'),
				'post_content' => self::postString('description'),
				'post_status' => 'draft',
				));
		wp_set_post_terms($postID, self::postInteger('category'), 'public-post-category');
		add_post_meta($postID, 'post_company', self::postInteger('post_company'));
		add_post_meta($postID, 'likes', 0);
		add_post_meta($postID, 'dislikes', 0);
		add_post_meta($postID, 'author_email', self::postString('author_email'));
		add_post_meta($postID, 'author_name', self::postString('author_name'));
		add_post_meta($postID, 'attachments', (isset($_POST['attachments']) ? $_POST['attachments'] : array()));
		print json_encode(array('success' => $postID));die();
	}
	
	private function doFileUpload(){
		$upload_dir = wp_upload_dir();
		$attachments = array();
		foreach($_FILES as $upload){
			$destination = $upload_dir['path'].'/'.$upload['name'];
			$filename = $upload['tmp_name'];
			move_uploaded_file($filename, $destination) or die("Could not move {$filename} to {$destination}");
			$wp_filetype =  wp_check_filetype($destination);
			$ID = wp_insert_attachment(array(
						'guid' => $destination,
						'post_mime_type' => $wp_filetype['type'],
						'post_title' => preg_replace('/\.[^.]+$/', '', $upload['name']),
						'post_content' => 'public file upload',
						'post_status' => 'draft'
					));
			$attachments[] = array('ID' => $ID, 'name' => $upload['name']);
		}
		print json_encode($attachments);
		die();
	}
	
	private function doPostVote(){
		$postID = self::postInteger('postID');
		switch(self::postString('vote')){
			case 'up':
				$meta = get_post_meta($postID, 'likes');
				$likes = count($meta) ? (intval($meta[0]) + 1) : 1;
				update_post_meta($postID, 'likes', $likes);
				print json_encode(intval(get_post_meta($postID, 'likes', true)));die();
				break;
			case 'down':
				$meta = get_post_meta($postID, 'dislikes');
				$dislikes = count($meta) ? (intval($meta[0]) + 1) : 1;
				update_post_meta($postID, 'dislikes', $dislikes);
				print json_encode(intval(get_post_meta($postID, 'dislikes', true)));die();
				break;
		}
	}
	
	public function postString($key){
		return filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
	}
	
	public function postInteger($key){
		return filter_var(filter_input(INPUT_POST, $key, FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
	}
	
	private function create_comment(){
		$response = recaptcha_check_answer(self::$recaptcha_private_key, $_SERVER["REMOTE_ADDR"], self::postString('recaptcha_challenge_field'), self::postString('recaptcha_response_field'));
		if(!$response->is_valid) {
			print json_encode(array('error' => "The reCAPTCHA was not entered correctly."));die();
		}
		$comment = array(
							'comment_post_ID' => self::postInteger('post_id'),
							'comment_author' => self::postString('author_name'),
							'comment_content' => self::postString('comment_content'),
							'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
							'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
							'comment_date' => current_time('mysql'),
							'comment_approved' => 0
						);
		print json_encode(array('success' => wp_insert_comment($comment)));die();
	}
	
	public function category_template($template){
		global $post;
		$category_template = dirname( __FILE__ ).'/category-template.php';
		return $post->post_type == 'public-post' ? $category_template : is_null($post) ? $category_template :  $template; 
	}
	
	public function single_template($template){
		global $post;
		if($post->post_type == 'public-post'){
			return dirname( __FILE__ ).'/single-template.php';
		}
		return $template;
	}
	
	public function page_template($template){
		$url = str_replace('/', '', $_SERVER['REQUEST_URI']);
		if(substr($url, 0, strlen(self::$landing_page_slug)) == self::$landing_page_slug){
			return dirname( __FILE__ ).'/home-template.php';
		}
		if(substr($url, 0, strlen(self::$form_page_slug)) == self::$form_page_slug){
			return dirname( __FILE__ ).'/write-template.php';
		}
		return $template;		
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
		$term_meta = get_option( "category_taxonomy_term_{$id}" );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[thumbnail_term_meta]"><?php echo __('Thumbnail'); ?></label></th>
			<td>
				<input type="text" name="term_meta[thumbnail_term_meta]" id="term_meta[thumbnail_term_meta]" value="<?php echo esc_attr( $term_meta['thumbnail_term_meta'] ) ? esc_attr( $term_meta['thumbnail_term_meta'] ) : ''; ?>">
				<p class="description"><?php echo __('Enter thumbnail source URL here'); ?></p>
			</td>
		</tr>		
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[weight_term_meta]"><?php echo __('Weight'); ?></label></th>
			<td>
				<input type="text" name="term_meta[weight_term_meta]" id="term_meta[weight_term_meta]" value="<?php echo esc_attr( $term_meta['weight_term_meta'] ) ? esc_attr( $term_meta['weight_term_meta'] ) : ''; ?>">
				<p class="description"><?php echo __('Enter sorting weight here'); ?></p>
			</td>
		</tr>		
		<?php
	}
	
	public function company_add_meta_field(){
		?>
		<div class="form-field">
			<label for="term_meta[thumbnail_term_meta]"><?php echo __('Logo'); ?></label>
			<input type="text" name="term_meta[thumbnail_term_meta]" id="term_meta[thumbnail_term_meta]" value=""/>
			<p class="description"><?php echo __('Enter company logo source URL here'); ?></p>			
		</div>
		<?php
	}
	
	public function company_edit_meta_field($term){
		$id = $term->term_id;
		$term_meta = get_option( "company_taxonomy_term_{$id}" );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[thumbnail_term_meta]"><?php echo __('Logo'); ?></label></th>
			<td>
				<input type="text" name="term_meta[thumbnail_term_meta]" id="term_meta[thumbnail_term_meta]" value="<?php echo esc_attr( $term_meta['thumbnail_term_meta'] ) ? esc_attr( $term_meta['thumbnail_term_meta'] ) : ''; ?>">
				<p class="description"><?php echo __('Enter company logo source URL here'); ?></p>
			</td>
		</tr>
		<?php
	}
	
	public function category_save_meta( $term_id ) {
		if(!isset( $_POST['term_meta'] )) return;
		$term_meta = get_option( "taxonomy_{$term_id}" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if(!isset ( $_POST['term_meta'][$key] )) continue;
			$term_meta[$key] = $_POST['term_meta'][$key];
		}
		update_option( "category_taxonomy_term_{$term_id}", $term_meta );
	}
	
	public function company_save_meta( $term_id ) {
		if(!isset( $_POST['term_meta'] )) return;
		$term_meta = get_option( "taxonomy_{$term_id}" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if(!isset ( $_POST['term_meta'][$key] )) continue;
			$term_meta[$key] = $_POST['term_meta'][$key];
		}
		update_option( "company_taxonomy_term_{$term_id}", $term_meta );
	}
	
	public function virtual_page(){
		$post = new stdClass();
		$post->post_type = 'page';
		$post->post_author = 1;
		$post->post_content = '';
		$post->post_status = 'publish';
		$post->comment_status = 'closed';
		$post->ping_status = 'closed';
		$post->post_date = current_time('mysql');
		$post->post_date_gmt = current_time('mysql', 1);
		return $post;
	}
	
	public function create_virtual_page($posts) {
		global $wp_query;
		$url = str_replace('/', '', $_SERVER['REQUEST_URI']);
		if(substr($url, 0, strlen(self::$landing_page_slug)) == self::$landing_page_slug){
			$virtual_page = self::virtual_page();
			$virtual_page->post_name = self::$landing_page_slug;
			$virtual_page->guid = site_url() . '/' . self::$landing_page_slug;
			$virtual_page->post_title = self::$landing_page_title;
			$virtual_page->ID = -10;
		}
		if(substr($url, 0, strlen(self::$form_page_slug)) == self::$form_page_slug){
			$virtual_page = self::virtual_page();
			$virtual_page->post_name = self::$form_page_slug;
			$virtual_page->guid = site_url() . '/' . self::$form_page_slug;
			$virtual_page->post_title = self::$form_page_title;
			$virtual_page->ID = -11;
			$virtual_page->post_parent = -10;
		}
		if(substr($url, 0, strlen(self::$landing_page_slug)) == self::$landing_page_slug || substr($url, 0, strlen(self::$form_page_slug)) == self::$form_page_slug){
			$qp_query->is_singular = true;
			$wp_query->is_page = true;
			$wp_query->is_home = false;
			$wp_query->is_archive = false;
			$wp_query->is_category = false;
			unset($wp_query->query["error"]);
			$wp_query->query_vars["error"] = "";
			$wp_query->is_404 = false;
			return array($virtual_page);
		}
		return $posts;
	}
	
	public function sidebar(){
		register_sidebar( array(
				'name' => 'Public Posts',
				'id' => 'public-posts',
				'description' => '',
				'class' => '',
				'before_widget' => '<div class="container">',
				'after_widget' => '</div>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>'
		));
	}
	
	public function add_meta_boxes(){
		add_meta_box('public-post-favourite', __('Favourite'), array('isys_visitor_posts', 'favourite_box'), 'public-post', 'side');
		add_meta_box('public-post-company', __('Company'), array('isys_visitor_posts', 'company_box'), 'public-post', 'side');
	}
	
	public function favourite_box(){
		global $post;
		wp_nonce_field( plugin_basename( __FILE__ ), 'favourite_nonce' );
		$favourite_box = get_post_meta($post->ID, 'favourite_box', true);
		?>
			<label>
				<input type="checkbox" id="favourite_box" name="favourite_box"<?php if($favourite_box == "Yes") {print ' checked="checked"';}?> value="Yes"/> <?php print __('Favourite')?>
			</label>
		<?php
	}
	
	public function company_box(){
		global $post;
		wp_nonce_field( plugin_basename( __FILE__ ), 'company_nonce' );
		$post_company = get_post_meta($post->ID, 'post_company', true);
		$companies = self::get_companies();
		if(!is_array($companies)) return;
		?>
			<label>
				<select name="post_company" id="post_company">
					<option value="0"><?php print __('Select Company')?></option>
					<?php foreach($companies as $company){?>
					<option value="<?php echo $company->pid?>"<?php if($post_company == $company->pid){print ' selected="selected"';}?>><?php print $company->alttext?></option>
					<?php }?>
				</select>
			</label>
		<?php
	}
	
	public function save_favourite($post_id){
		if ( !wp_verify_nonce( $_POST['favourite_nonce'], plugin_basename( __FILE__ ) ) ) return;
		if($_POST['post_type'] != 'public-post') return;
		if(!current_user_can( 'edit_post', $post_id )) return;
		$favourite_box = self::postString('favourite_box');
		$favourite_box = $favourite_box ? $favourite_box : 'No';
		update_post_meta($post_id, 'favourite_box', $favourite_box);
	}
	
	public function save_company($post_id){
		if ( !wp_verify_nonce( $_POST['company_nonce'], plugin_basename( __FILE__ ) ) ) return;
		if($_POST['post_type'] != 'public-post') return;
		if(!current_user_can( 'edit_post', $post_id )) return;
		$post_company = self::postInteger('post_company');
		if($post_company){
			update_post_meta($post_id, 'post_company', $post_company);
		} 
	}

	public static function get_companies(){
		$gallery = 52;
		global $wpdb;
		return $wpdb->get_results(sprintf("SELECT *, IF(LEFT(alttext, 1) BETWEEN '0' AND '9', 2, 1) AS sortOrder1 FROM wp_11_ngg_pictures RIGHT JOIN wp_11_ngg_gallery ON wp_11_ngg_pictures.galleryid=wp_11_ngg_gallery.gid WHERE galleryid=%d AND exclude=0 ORDER BY sortOrder1, alttext", $gallery));
	}

	public static function get_company($pid){
		global $wpdb;
		return $wpdb->get_results(sprintf("SELECT *, IF(LEFT(alttext, 1) BETWEEN '0' AND '9', 2, 1) AS sortOrder1 FROM wp_11_ngg_pictures RIGHT JOIN wp_11_ngg_gallery ON wp_11_ngg_pictures.galleryid=wp_11_ngg_gallery.gid WHERE pid=%d AND exclude=0 ORDER BY sortOrder1, alttext", $pid));
	}
	
}

add_action('init', array('isys_visitor_posts', 'create_post_type'));

add_action('widgets_init', array('isys_visitor_posts', 'sidebar'));

add_action('the_posts', array('isys_visitor_posts', 'create_virtual_page'));

add_action('wp_enqueue_scripts', array('isys_visitor_posts', 'enqueue_scripts'));

add_action('wp_ajax_isys_visitor_plugin', array('isys_visitor_posts', 'ajax_controller'));

add_action('wp_ajax_nopriv_isys_visitor_plugin', array('isys_visitor_posts', 'ajax_controller'));

add_action('public-post-category_add_form_fields', array('isys_visitor_posts', 'category_add_meta_field'), 10, 2);

add_action('public-post-category_edit_form_fields', array('isys_visitor_posts', 'category_edit_meta_field'), 10, 2);

add_action('public-post-company_add_form_fields', array('isys_visitor_posts', 'company_add_meta_field'), 10, 2);

add_action('public-post-company_edit_form_fields', array('isys_visitor_posts', 'company_edit_meta_field'), 10, 2);

add_action( 'edited_public-post-category', array('isys_visitor_posts', 'category_save_meta'), 10, 2 );

add_action( 'create_public-post-category', array('isys_visitor_posts', 'category_save_meta'), 10, 2 );

add_action( 'edited_public-post-company', array('isys_visitor_posts', 'company_save_meta'), 10, 2 );

add_action( 'create_public-post-company', array('isys_visitor_posts', 'company_save_meta'), 10, 2 );

add_filter('archive_template', array('isys_visitor_posts', 'category_template'));

add_filter('single_template', array('isys_visitor_posts', 'single_template'));

add_filter('single_template', array('isys_visitor_posts', 'page_template'));

add_filter('page_template', array('isys_visitor_posts', 'page_template'));

add_action('add_meta_boxes', array('isys_visitor_posts', 'add_meta_boxes'));

add_action( 'save_post', array('isys_visitor_posts', 'save_favourite'));

add_action( 'save_post', array('isys_visitor_posts', 'save_company'));