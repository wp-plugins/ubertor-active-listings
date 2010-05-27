<?php
/*
Plugin Name: Ubertor Active Listings
Plugin URI: http://www.splitmango.com/
Description: Display your Ubertor active listings on your Wordpress blog
Author: SplitMango Media Inc.
Version: 1.0
Author URI: http://www.splitmango.com/
*/

add_action( 'widgets_init', 'load_listing_widgets' );
function load_listing_widgets() {
	register_widget( 'Ubertor_Active_Listings' );
}

class Ubertor_Active_Listings extends WP_Widget {

	function Ubertor_Active_Listings() {
		$widget_ops = array( 'classname' => 'ubertor_listings', 'description' => __('Display your Ubertor Active Listings', 'example') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'listing_widget' );

		$this->WP_Widget( 'listing_widget', __('Ubertor Active Listings', 'example'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$website = $instance['website'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display website from widget settings if one was input. */
		if ( $website )
			$complete = $website . "/ActiveListings.php/xml";
			$xml = simplexml_load_file($complete);
			$count = count($xml->children());
			$random = (rand()%$count);
			$doc = new DOMDocument();
			$doc->loadXML($xml->marker[$random]->infowindow);
			$xpath = new DOMXpath($doc);
			$tagsWithStyle = $xpath->query("//*[@style]");
			foreach ($tagsWithStyle as $element) {
				$element->removeAttribute("style");
			}
			echo $doc->saveXML();
			echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['website'] = strip_tags( $new_instance['website'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Active Listings', 'example'), 'website' => __('http://www.yourwebsite.com', 'example'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p><?php _e('Title:', 'hybrid'); ?></label><br/><input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:250px;" />
		</p>

		<!-- Ubertor Website URL -->
		<p><?php _e('Your Ubertor Website:', 'example'); ?><br/><input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'website' ); ?>" value="<?php echo $instance['website']; ?>" style="width:250px" />
		</p>

	<?php
	}
}

?>