<?php 	
	/**
	 * Plugin Name: Mainul Post Notice
	 * Plugin URI: http://localhost/wordpress/wp-content/plugins/mainul-post-notice
	 * Description: Display a short notice above the post content.
	 * Version: 0.1.0
	 * Author: Mainul Hasan
	 * Author URI: http://mywordpress.com/
	*/

	// If this file is called directly, abort.
	if (!defined('WPINC')) {
		die;
	}

	require_once( plugin_dir_path(__FILE__).'class-mainul-post-notice.php' );
	function new_post_notice_start() {

		$post_notice = new Mainul_Post_Notice();
		$post_notice->initialize();
	}
	new_post_notice_start();
?>