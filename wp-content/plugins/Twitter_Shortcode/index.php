<?php 
/*
Plugin Name: Twitter Shower
Plugin URI: http://localhost 
Description: Simple shortcode
Version: 1.0
Author: Mainul Hasan
Author URI: http://localhost
*/

// Plugin URI means where the plugin is located in or where the documentation of the plugin

// add_shortcode() excepts two parameter 
// first one is name of the shortcode
// second one is pass the function name 
// instead of passing function we can also use anonymous funciton  
//add_shortcode('twitter','jw-twitter');
// or we can write
// add_shortcode('twitter', function($atts, $content){
// 	// never use echo in the shortcode
// 	// echo 'hi';
// 	// instead use return
// 	// return 'hi';
// 	// [twitter username="envatowebdev" hello=world]
// 	// Here quatation for username are optional only one one condition is value in one line
// 	// print_r($atts);die();
// 	// if forget to add username with the twitter shortcode then default value
// 	// [twitter username="envato3d"]Please follow me everybody![/twitter] // [/twitter] like thml tag open it and close
// 	// [twitter] i.e. open the tag and [/twitter] means close the tag.
// 	// print_r($content);die(); // content will return "Please follow me everybody!"
// 	if(!isset($atts['username'])) $atts['username'] = 'envatowebdev';
// 	if(empty($content)) $content = 'Follw me on Twitter!';
// this is so much tedious to check every attribute and content better way is a function of wordpress name wordpress shortcode function shortcode_atts
// 	return '<a href="http://twitter.com/'.$atts['username'].'">'.$content.'</a>';
// });

// more easier way is 
add_shortcode('twitter', function($atts, $content) {
	// Some people uses defaults some uses options we use atts to overwrite the previous one
	$atts = shortcode_atts(
		array(
			'username' => 'envatowebdev',
			'content'  => !empty($content) ? $content : 'Follow me on Twitter!'	
		), $atts
	);

	// This function uses array keys as variable names and values as variable values. For each element it will create a variable in the current symbol table.
	extract($atts);

	return "<a href='http://twitter.com/$username'>$content</a>";
	// extract will extract all array key as variable like $atts['username'] change in $username
});

// Combines user shortcode attributes with known attributes and fills in defaults when needed. The result will contain every key from the known attributes, merged with values from shortcode attributes. 
// $pairs -- Entire list of supported attributes and their defaults 
// $atts -- User defined attributes in shortcode tag 
// $shortcode -- Shortcode name to be used by the shortcode_atts_{$shortcode} filter. If this is present, it makes a "shortcode_atts_$shortcode" filter available for other code to filter the attributes. It should always be included for maximum compatibility, however it is an optional variable. 
//shortcode_atts( $pairs , $atts, $shortcode ); 


