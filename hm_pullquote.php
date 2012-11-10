<?php
/*
Plugin Name: HM Pull Quotes
Plugin URI: http://hungry-media.com
Description: Provides a repository of linkable pull quotes for your site
Version: 0.1
Author: Warren Harrison
Author URI: http://hungry-media.com
License: MIT
*/


class HMPullQuote{


/*	function register(){
		register_activation_hook( __FILE__, array('HMPullQuote', 'init') );
	}
*/
	function init(){
	  $labels = array(
	    'name' => _x('Pull Quotes', 'post type general name', 'your_text_domain'),
	    'singular_name' => _x('Pull Quote', 'post type singular name', 'your_text_domain'),
	    'add_new' => _x('Add New', 'pull quote', 'your_text_domain'),
	    'add_new_item' => __('Add New Pull Quote', 'your_text_domain'),
	    'edit_item' => __('Edit Pull Quote', 'your_text_domain'),
	    'new_item' => __('New Pull Quote', 'your_text_domain'),
	    'all_items' => __('All Pull Quotes', 'your_text_domain'),
	    'view_item' => __('View Pull Quote', 'your_text_domain'),
	    'search_items' => __('Search Pull Quotes', 'your_text_domain'),
	    'not_found' =>  __('No pull quotes found', 'your_text_domain'),
	    'not_found_in_trash' => __('No pull quotes found in Trash', 'your_text_domain'), 
	    'parent_item_colon' => '',
	    'menu_name' => __('Pull Quotes', 'your_text_domain')

	  );		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'menu_position' => 5,
			'supports' => array('title', 'excerpt', 'revisions')
	  );

		$registered = register_post_type( 'hm_pull_quote' , $args );
		error_log($registered);
	}

	function render_admin_ui(){

	}


}

add_action( 'init', array('HMPullQuote', 'init') );


?>