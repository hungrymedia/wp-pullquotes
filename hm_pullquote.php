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
	}

	function render_admin_ui(){
		?>
		<script>
		jQuery('#postexcerpt h3 span').html('Link to:');
		jQuery('#edit-slug-box').hide();
		jQuery('#postexcerpt .inside p').hide();
		jQuery('#revisionsdiv').addClass('closed');
		</script>
		<?php
	}

	function get_quote(){
		$quotes = get_posts(
			array(
				'post_type' => 'hm_pull_quote'
			)
		);
		$rand = floor(mt_rand(0, count($quotes)-1));
		return $quotes[$rand];
	}

}

class HMPullQuotewidget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'hm_pull_quote_widget', // Base ID
			'Pull Quote Widget', // Name
			array( 'description' => __( 'Show a random pull quote', 'text_domain' ), ) // Args
		);
	}

 	public function form( $instance ) {
		// outputs the options form on admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		$quote = HMPullQuote::get_quote();
		?>
		<div id="hm-pullquote">
			<a href="<?php echo $quote->post_excerpt ?>"><?php echo $quote->post_title ?></a>
		</div>
		<?php
		echo $after_widget;
	}

}

add_action( 'init', array('HMPullQuote', 'init') );
add_action( 'edit_form_advanced', array('HMPullQuote', 'render_admin_ui') );
add_action( 'widgets_init', create_function( '', 'register_widget( "HMPullQuotewidget" );' ) );

?>