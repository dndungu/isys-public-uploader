<?php
/*
Plugin Name:  iSys AddDiversity Blog
Plugin URI: http://www.isys.dk
Description: It allows for public posts
Version: 1.0.0
Author: iSys ApS
Author URI: http://www.isys.dk
*/

session_start();

require_once(dirname( __FILE__ ) . '/libraries/recaptchalib.php');

require_once(dirname( __FILE__ ) . '/includes.php');

isys_visitor_posts::initTranslator(dirname( __FILE__ ) . '/dk.xml');

add_action('init', array('isys_visitor_posts', 'create_post_type'), 0);

add_action('widgets_init', array('isys_visitor_posts', 'sidebar'), 0);

add_action('the_posts', array('isys_visitor_posts', 'create_virtual_page'), 0);

add_action('wp_enqueue_scripts', array('isys_visitor_posts', 'enqueue_scripts'), 0);

add_action('wp_ajax_isys_visitor_plugin', array('isys_visitor_posts', 'ajax_controller'), 0);

add_action('wp_ajax_nopriv_isys_visitor_plugin', array('isys_visitor_posts', 'ajax_controller'), 0);

add_action('blog-indlaeg-kategori_add_form_fields', array('isys_visitor_posts', 'category_add_meta_field'), 0, 2);

add_action('blog-indlaeg-kategori_edit_form_fields', array('isys_visitor_posts', 'category_edit_meta_field'), 0, 2);

add_action( 'edited_blog-indlaeg-kategori', array('isys_visitor_posts', 'category_save_meta'), 0, 2 );

add_action( 'create_blog-indlaeg-kategori', array('isys_visitor_posts', 'category_save_meta'), 0, 2 );

add_filter('404_template', array('isys_visitor_posts', 'category_template'), 0);

add_filter('archive_template', array('isys_visitor_posts', 'category_template'), 0);

add_filter('single_template', array('isys_visitor_posts', 'single_template'), 0);

add_filter('single_template', array('isys_visitor_posts', 'page_template'), 0);

add_filter('page_template', array('isys_visitor_posts', 'page_template'), 0);

add_action('add_meta_boxes', array('isys_visitor_posts', 'add_meta_boxes'), 0);

add_action( 'save_post', array('isys_visitor_posts', 'save_favourite'), 0);

add_action( 'save_post', array('isys_visitor_posts', 'save_company'), 0);