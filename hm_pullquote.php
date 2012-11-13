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
			'supports' => array('title', 'revisions', 'custom_fields')
	  );
		add_option('HMPullQuote_max_weight', 0);
		$registered = register_post_type( 'hm_pull_quote' , $args );
	}

	function set_max_weight(){
		$max = 0;
		$quotes = get_posts(
			array(
				'post_type' => 'hm_pull_quote'
			)
		);
		foreach( $quotes as $quote ){
			$custom = get_post_custom($quote->ID);
			if( $custom["weight"][0]  > $max ){
				$max = $custom["weight"][0];
			}
		}
		update_option( 'HMPullQuote_max_weight', $max );
	}

	function render_admin_ui(){
		?>
		<script>
		jQuery('#titlewrap label').html('Enter quote here');
		jQuery('#postexcerpt h3 span').html('Link to:');
		jQuery('#edit-slug-box').hide();
		var excerpt = jQuery('#postexcerpt textarea').text();
		jQuery('#postexcerpt .inside').empty().html('<input size="30" name="excerpt" value="'+excerpt+'" />');
		jQuery('#revisionsdiv').addClass('closed');
		</script>
		<?php
	}

	function front_scripts(){
		wp_enqueue_script('jquery');
	}

	function type_quote(){
		?>
		<script>
		type_speed = jQuery('#hm-pullquote').data('speed');
		if(jQuery('#hm-pullquote a').length > 0){
			var el = jQuery('#hm-pullquote a');
		}else{
			var el = jQuery('#hm-pullquote');
		}
		quote_text = jQuery(el).html();
		jQuery(el).empty();
		quote_chr = 0;
		quote_int = setInterval( function(){
			jQuery(el).append(quote_text.charAt(quote_chr));
			quote_chr++;
			if( quote_chr >= quote_text.length){ clearInterval(quote_int); }
		}, type_speed);
		</script>
		<?php
	}

	function get_quote(){
		$this->type_speed = $speed;
		$bias = rand(0, get_option( 'HMPullQuote_max_weight', 10 ) );
		$quotes = get_posts(
			array(
				'post_type' => 'hm_pull_quote'
			)
		);
		$weighted_quotes = array();
		foreach( $quotes as $quote ){
			$custom = get_post_custom($quote->ID);
			if( $custom["weight"][0]  >= $bias ){
				array_push($weighted_quotes, $quote);
			}
		}
		$rand = floor(rand(0, count($weighted_quotes)-1));
		return $weighted_quotes[$rand];
	}

	function hm_weight(){
	  global $post;
	  $custom = get_post_custom($post->ID);
	  $weight = isset($custom["weight"][0]) ? $custom["weight"][0] : 5;
	  $impressions = isset($custom["impressions"][0]) ? $custom["impressions"][0] : 0;
	  $link_to = isset($custom["link_to"][0]) ? $custom["link_to"][0] : '';
	  ?>
	  <p><label>Link to:</label>
	  <input type="text" name="link_to" value="<?php echo $link_to; ?>" /></p>
	  <p><label>Display Weighting:</label>
	  <input type="number" name="weight" value="<?php echo $weight; ?>" min="1", max="10" /></p>
	  <p>Impressions: <?php echo $impressions; ?></p>
	  <?php
	}

	function hm_weight_admin_init(){
  	add_meta_box(
  		"hm_pullquote-meta", 
  		"Options", 
  		array('HMPullQuote','hm_weight'), 
  		"hm_pull_quote", 
  		"side", 
  		"high"
  	);
	}

	function custom_admin_save(){
		global $post;
		update_post_meta($post->ID, "weight", $_POST["weight"]);
		update_post_meta($post->ID, "link_to", $_POST["link_to"]);
		HMPullQuote::set_max_weight();
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
 		$speed = 100;
 		if( isset( $instance['speed'] ) ){
 			$speed = $instance['speed'];
 		}
 		?>
		<label for="<?php echo $this->get_field_id( 'speed' ); ?>"><?php _e( 'Typing Speed:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'speed' ); ?>" name="<?php echo $this->get_field_name( 'speed' ); ?>" type="number" value="<?php echo esc_attr( $speed ); ?>" min="10" max="1000" step="10"/>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['speed'] = intval($new_instance['speed']);
		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		$speed = isset($instance['speed']) ? $instance['speed'] : 100;
		$quote = HMPullQuote::get_quote();
		$custom = get_post_custom($quote->ID);
		$output = $quote->post_title;
		if( !empty( $custom['link_to'][0] ) ){
			$output = '<a href="' . $custom['link_to'][0] . '">' . $output . '</a>';
		}
		?>
		<div id="hm-pullquote" data-speed="<?php echo $speed; ?>"><?php echo $output; ?></div>
		<?php
		echo $after_widget;
		$impressions = isset($custom['impressions'][0]) ? $custom['impressions'][0] : 0;
		update_post_meta($quote->ID, 'impressions', $impressions+1, $custom['impressions'][0]);
	}

}


add_action( 'init', array('HMPullQuote', 'init') );
add_action( 'wp_enqueue_scripts', array('HMPullQuote', 'front_scripts') );
add_action( 'wp_footer', array('HMPullQuote', 'type_quote') );
add_action( 'edit_form_advanced', array('HMPullQuote', 'render_admin_ui') );
add_action( 'widgets_init', create_function( '', 'register_widget( "HMPullQuotewidget" );' ) );
add_action( 'admin_init', array('HMPullQuote', 'hm_weight_admin_init') );
add_action( 'save_post', array('HMPullQuote','custom_admin_save') );

 


?>