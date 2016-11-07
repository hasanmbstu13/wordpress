<?php 
error_reporting(E_ALL);
/*
Plugin Name: Messager Widget
Pugin URI: http://localhost
Description: Display any message designated.
Version: 1.0
Author: Mainul Hasan
Author URI: http://localhost 
*/

class Messager extends WP_Widget {

	function __construct()
	{
		$params = array(
			'description' => 'Display messages to readers',
			'name'		  => 'Messager'
		);

		// First parameter is id, Second parameter is name here we declare the name in child class
		// Third parameter is options
		parent::__construct('Messager', '', $params);

	}

	// This will show the widget form
	// $instance what will type user as input in form
	public function form($instance)
	{	
		// print_r($instance);
		extract($instance);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title: </label>
			<input
				class="widefat"
				id="<?php echo $this->get_field_id('title'); ?>"
				name="<?php echo $this->get_field_name('title'); ?>"
				value="<?php if(isset($title)) echo esc_attr($title); ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>">Description:</label>
			<textarea
				class="widefat"
				rows="10"
				id="<?php echo $this->get_field_id('description'); ?>"
				name="<?php echo $this->get_field_name('description'); ?>"><?php if(isset($description)) echo esc_attr($description); ?></textarea>
		</p>
		<?php
	}

	// This method will responsible for display input of the form page in the page or posts
	// accepts $args, $instance
	public function widget($args, $instance)
	{
		// print_r($instance);
		extract($args);
		extract($instance);

		$title = apply_filters('widget_title', $title);
		$description = apply_filters('widget_description', $description);

		if(empty($title)) $title = 'My Status';

		echo $before_widget;
			echo $before_title . $title . $after_title;
			echo "<p>$description</p>";
		echo $after_widget;

	}

}

// widgets_init - when initialized widget section we make sure this class is add
// we can use annonymous function instead of named function
add_action('widgets_init', 'mh_register_messager');
function mh_register_messager()
{
	register_widget('Messager');
}
