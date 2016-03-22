<?php 
/*
Plugin Name: JW Filter
Plugin URI: localhost
Description: Just for demo purposes.
Author: Mainul Hasan
Author URI: http://net.tutsplus.com
Version: 1.0
*/

// Here first one is what we are filtering
// // second one is custom defined method
// add_filter('the_title','jw_modify_title_cb');
// We can also use anonymous function
add_filter('the_title', function($content) {
	return ucwords($content);
});

// More simpler
add_filter('the_title',ucwords);