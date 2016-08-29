<?php 
/**
* Plugin Name: CT Theme Switcher
* Plugin URI: http://localhost
* Description: A custom plugin that switch theme depend on mobile & web platform for example.
* Author: Mainul Hasan
* Author URI: http://codetrio.com
* Version: 1.0
**/


function maybe_switch_theme($host = false)
{

	$host = ($host) ? $host : str_replace('www.', '', $_SERVER['SERVER_NAME']);
	$themes = array(
	'm.mywordpress.local' => array('twentyfifteen','twentyfifteen'),
	'mywordpress.local' => array('twentyfourteen','twentyfourteen') );

	if (isset($themes[$host])){
		$themes = $themes[$host];
		switch_theme($themes[0],$themes[1]);
	}
}

add_action('setup_theme','maybe_switch_theme');


