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
			'content'  => !empty($content) ? $content : 'Follow me on Twitter!',
			'show_tweets' => false,
			// its ultimately translates every refresh the page after every 10 minutes grabs and posts twitter posts after every 10 minustes
			'tweet_reset_time' => 10,
			'num_tweets' => 5 	
		), $atts
	);

	// This function uses array keys as variable names and values as variable values. For each element it will create a variable in the current symbol table.
	extract($atts);

	if($show_tweets) {
		$tweets = fetch_tweets($num_tweets, $username, $tweet_reset_time);
	}

	return "<a href='http://twitter.com/$username'>$content</a>";
	// extract will extract all array key as variable like $atts['username'] change in $username
});

// Combines user shortcode attributes with known attributes and fills in defaults when needed. The result will contain every key from the known attributes, merged with values from shortcode attributes. 
// $pairs -- Entire list of supported attributes and their defaults 
// $atts -- User defined attributes in shortcode tag 
// $shortcode -- Shortcode name to be used by the shortcode_atts_{$shortcode} filter. If this is present, it makes a "shortcode_atts_$shortcode" filter available for other code to filter the attributes. It should always be included for maximum compatibility, however it is an optional variable. 
//shortcode_atts( $pairs , $atts, $shortcode ); 

// This is handy for knowing the user information
// http://twitter.com/statuses/user_timeline/envatowebdev.json


// Resource URL
// https://api.twitter.com/1.1/statuses/user_timeline.json


// $num_tweets how many posts fetch from twitter
function fetch_tweets($num_tweets, $username, $tweet_reset_time){
	// $tweets = curl("https://api.twitter.com/1/statuses/user_timeline/square44.json");
	// $tweets = curl("https://api.twitter.com/1/statuses/user_timeline/jbrooksuk.json");
	// var_dump($username);
	// $tweets = curl("http://api.twitter.com/1/statuses/user_timeline/$username.json");
	 $tweets = curl($username,$num_tweets);
	// $tweets = curl("https://twitter.com/statuses/user_timeline/$username.json");
	// var_dump('hi iam from fetch_tweets');
	// var_dump($tweets);
	// echo "<pre>";
	// print_r($tweets);
	// echo "</pre>";

	$data = array();
	foreach ($tweets as $tweet) {
		if($num_tweets-- === 0) break;
		$data[] = $tweet->text;
	}
}

function curl($username,$num_tweets){
	// $c = curl_init($url);

	$token = '3225959616-i357BgPVtiIPimz3qZqqMrFCqC2AwtzNoAmfaod';
	$token_secret = 'NfXGirZmKff75AvtoT6WcIdhfrxjwBFyAqWIZMlZ1kkC5';
	$consumer_key = 'qvLTtoeCp5DqDi7okj0gvUY8m';
	$consumer_secret = 'nHlCKKvSzS0UR5LvcCcBJQdx2o65VeEYpnZArRB1IKUysHCMAm';

	$host = 'api.twitter.com';
	$method = 'GET';
	$path = '/1.1/statuses/user_timeline.json'; // api call path

	$query = array( // query parameters
	    'screen_name' => $username,
	    'count' => $num_tweets
	);

	$oauth = array(
	    'oauth_consumer_key' => $consumer_key,
	    'oauth_token' => $token,
	    'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
	    'oauth_timestamp' => time(),
	    'oauth_signature_method' => 'HMAC-SHA1',
	    'oauth_version' => '1.0'
	);

	$oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
	$query = array_map("rawurlencode", $query);

	$arr = array_merge($oauth, $query); // combine the values THEN sort

	asort($arr); // secondary sort (value)
	ksort($arr); // primary sort (key)

	// http_build_query automatically encodes, but our parameters
	// are already encoded, and must be by this point, so we undo
	// the encoding step
	$querystring = urldecode(http_build_query($arr, '', '&'));

	$url = "https://$host$path";

	// mash everything together for the text to hash
	$base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);

	// same with the key
	$key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);

	// generate the hash
	$signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));

	// this time we're using a normal GET query, and we're only encoding the query params
	// (without the oauth params)
	$url .= "?".http_build_query($query);
	$url=str_replace("&amp;","&",$url); //Patch by @Frewuill

	$oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
	ksort($oauth); // probably not necessary, but twitter's demo does it

	// also not necessary, but twitter's demo does this too
	function add_quotes($str) { return '"'.$str.'"'; }
	$oauth = array_map("add_quotes", $oauth);

	// this is the full value of the Authorization line
	$auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

	$options = array( CURLOPT_HTTPHEADER => array("Authorization: $auth"),
	                  //CURLOPT_POSTFIELDS => $postfields,
	                  CURLOPT_HEADER => false,
	                  CURLOPT_URL => $url,
	                  CURLOPT_RETURNTRANSFER => true,
	                  CURLOPT_SSL_VERIFYPEER => false,
	                  CURLOPT_CONNECTTIMEOUT => 3,
	                  CURLOPT_TIMEOUT => 5);

	// // do our business
	$feed = curl_init();
	curl_setopt_array($feed, $options);
	$json = curl_exec($feed);
	curl_close($feed);

	$twitter_data = json_decode($json);


	// curl_setopt($c, CURLOPT_HTTPHEADER, array("Authorization: $auth"));
	// curl_setopt($c, CURLOPT_HEADER, false);	
	// curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);	
	// curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 3);	
	// // if have a problem disconnect after 5 minutes
	// curl_setopt($c, CURLOPT_TIMEOUT, 5);
	// curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
	// $res = curl_exec($c);

	return $twitter_data;	
}




// Extra code
// $token = 'YOUR_TOKEN';
// $token_secret = 'YOUR_TOKEN_SECRET';
// $consumer_key = 'CONSUMER_KEY';
// $consumer_secret = 'CONSUMER_SECRET';

// $host = 'api.twitter.com';
// $method = 'GET';
// $path = '/1.1/statuses/user_timeline.json'; // api call path

// $query = array( // query parameters
//     'screen_name' => 'twitterapi',
//     'count' => '5'
// );

// $oauth = array(
//     'oauth_consumer_key' => $consumer_key,
//     'oauth_token' => $token,
//     'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
//     'oauth_timestamp' => time(),
//     'oauth_signature_method' => 'HMAC-SHA1',
//     'oauth_version' => '1.0'
// );

// $oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
// $query = array_map("rawurlencode", $query);

// $arr = array_merge($oauth, $query); // combine the values THEN sort

// asort($arr); // secondary sort (value)
// ksort($arr); // primary sort (key)

// // http_build_query automatically encodes, but our parameters
// // are already encoded, and must be by this point, so we undo
// // the encoding step
// $querystring = urldecode(http_build_query($arr, '', '&'));

// $url = "https://$host$path";

// // mash everything together for the text to hash
// $base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);

// // same with the key
// $key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);

// // generate the hash
// $signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));

// // this time we're using a normal GET query, and we're only encoding the query params
// // (without the oauth params)
// $url .= "?".http_build_query($query);
// $url=str_replace("&amp;","&",$url); //Patch by @Frewuill

// $oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
// ksort($oauth); // probably not necessary, but twitter's demo does it

// // also not necessary, but twitter's demo does this too
// function add_quotes($str) { return '"'.$str.'"'; }
// $oauth = array_map("add_quotes", $oauth);

// // this is the full value of the Authorization line
// $auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

// // if you're doing post, you need to skip the GET building above
// // and instead supply query parameters to CURLOPT_POSTFIELDS
// $options = array( CURLOPT_HTTPHEADER => array("Authorization: $auth"),
//                   //CURLOPT_POSTFIELDS => $postfields,
//                   CURLOPT_HEADER => false,
//                   CURLOPT_URL => $url,
//                   CURLOPT_RETURNTRANSFER => true,
//                   CURLOPT_SSL_VERIFYPEER => false);

// // do our business
// $feed = curl_init();
// curl_setopt_array($feed, $options);
// $json = curl_exec($feed);
// curl_close($feed);

// $twitter_data = json_decode($json);


// foreach ($twitter_data as &$value) {
//    $tweetout .= preg_replace("/(http:\/\/|(www\.))(([^\s<]{4,68})[^\s<]*)/", '<a href="http://$2$3" target="_blank">$1$2$4</a>', $value->text);
//    $tweetout = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $tweetout);
//    $tweetout = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $tweetout);
// }

// echo $tweetout;


