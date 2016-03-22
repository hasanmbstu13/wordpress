<?php 
	
class Mainul_Post_Notice {
	public function initialize() {
		add_action('admin_enquence_scripts', array($this, 'enquence_styles'));
	}

	public function enquence_styles() {

		wp_enqueue_style( 
			'mainul-post-notice-admin',
			plugins_url('mainul-post-notice/assets/css/admin.css'), 
			array(), 
			'0.1.0'
		);

		// 1st param($handle): Named used as a handle for the stylesheet
		// 2nd param($src):  (optional) URL to the stylesheet.
		// 3rd param($deps): (optional) Array of handles of any stylesheet that this stylesheet depends on; stylesheets that must be loaded before this stylesheet. false if there are no dependencies. 
		// 4th param($ver): String specifying the stylesheet version number, if it has one.
		
	}
}

?>