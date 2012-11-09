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


class hm_pullquote{

	function init(){
		$labels = array(
			'name' => _x( 'Pull Quote', 'post type general name' ), 
		);
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true, 
			'_builtin' => false,
			'rewrite' => array( 'slug' => 'pull_quote'),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 5,
			'supports' => array('title')
	  );

		register_post_type( 'pull_quote' , $args );
	}

	function render_admin_ui(){

	}


}

register_activation_hook( __FILE__, array('hm_pullquote', 'init') );

?>