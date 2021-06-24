<?php 
/*
Plugin Name: MH Twitter Widget
Pugin URI: http://localhost
Description: Display and cache tweets
Version: 1.0
Author: Mainul Hasan
Author URI: http://localhost
*/

class MH_Twitter_Widget extends WP_Widget {

	function __construct()
	{
		$options = array(
			'description' => 'Display and cache tweets',
			'name'		  => 'Display Tweets'
		);
		parent::__construct('MH_Twitter_Widget', '', $options);

	}

	// This responsible for handling form data
	public function form($instance)
	{
		extract($instance);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" 
			type="text" 
			id="<?php echo $this->get_field_id('title'); ?>" 
			name="<?php echo $this->get_field_name('title') ?>" 
			value="<?php if(isset($title)) echo esc_attr($title); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('username'); ?>">Twitter Username:</label>
			<input class="widefat" 
			type="text" 
			id="<?php echo $this->get_field_id('username'); ?>" 
			name="<?php echo $this->get_field_name('username') ?>" 
			value="<?php if(isset($username)) echo esc_attr($username); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('tweet_count'); ?>">Number of Tweets to Retrieve:</label>
			<input class="widefat"
			style="width: 40px;" 
			type="number" 
			id="<?php echo $this->get_field_id('tweet_count'); ?>" 
			name="<?php echo $this->get_field_name('tweet_count') ?>" 
			min="1"
			max="10"
			value="<?php echo !empty($tweet_count) ? $tweet_count : 5; ?>">
		</p>
		<?php
	}

	// this method is responisble for echoing out necessary data and html
	public function widget($args, $instance)
	{	
		// print_r($instance);
		// exit;
		extract($args);
		extract($instance);

		if(empty($title)) $title = 'Recent Tweets';

		// tweet_count, $username come from instance
		$data = $this->twitter($tweet_count, $username);

	}

	private function twitter($tweet_count, $username)
	{
		// return false means return immediatly don't do anything
		// because username is empty
		if(empty($username)) return false;

		$this->fetch_tweets($tweet_count, $username);

	}

	private function fetch_tweets($tweet_count, $username)
	{
		// $url = "https://api.twitter.com/1.1/statuses/user_timeline/$username.json";
		// $url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$username&count=$tweet_count";
		$token = '3225959616-i357BgPVtiIPimz3qZqqMrFCqC2AwtzNoAmfaod';
		$url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$username&access_token=$token";
		echo $url;
// 		$url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$username, array(
//     \"headers\" => array(
//         \"Authorization\" => \"Bearer \".$token
//     ),
// ))";

// echo $url; 
// var_dump($url); die();
		// $url = "http://twitter.com/statuses/user_timeline/$username.json";
		// $args = array(
		//   'headers' => array(
		//     'Authorization' => 'Basic ' . base64_encode( 'hasanmbstu13' . ':' .  '11309011008559'))
		// );
		// wp_remote_request( $url, $args );

		// $options = array(
		//         'username' => 'hasanmbstu13',
		//         'accessToken' => '3225959616-i357BgPVtiIPimz3qZqqMrFCqC2AwtzNoAmfaod',
		//         'oauthOptions' => array(
		//             'consumerKey' => 'qvLTtoeCp5DqDi7okj0gvUY8m',
		//             'consumerSecret' => 'nHlCKKvSzS0UR5LvcCcBJQdx2o65VeEYpnZArRB1IKUysHCMAm',
		//         )
		//     );

		// $tweets = wp_remote_get($url, $options);
		$tweets = wp_remote_get($url);
		echo '<pre>';
			print_r($tweets);
		echo '</pre>';
		die();
	}
}

add_action('widgets_init', 'register_mh_twitter_widget');
function register_mh_twitter_widget()
{
	register_widget('MH_Twitter_Widget');
}