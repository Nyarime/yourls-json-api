<?php
/*
Plugin Name: JSON API for Yourls
Plugin URI: https://github.com/Nyarime/yourls-json-api
Description: Add .json (or a custom string/character) to the short URL to get info about the link
Version: 1.0
Author: Nyarime
Author URI: https://www.idc.moe
*/

// Set the conditions for triggering JSON
if (!defined('JSON_TRIGGER'))
{
	define('JSON_TRIGGER', '\.json');
}

// Handle failed loader request
yourls_add_action('redirect_keyword_not_found', 'json_response', 1);
yourls_add_action('loader_failed', 'json_response', 1);

// Check for the trigger

function json_response($args)
{
	$pattern = yourls_make_regexp_pattern(yourls_get_shorturl_charset());

	if (preg_match("@^([$pattern]+)".JSON_TRIGGER."$@", $args[0], $matches))
	{
		$keyword = isset($matches[1]) ? $matches[1] : '';
		$keyword = yourls_sanitize_keyword($keyword);

		// Only do something, if shorturl exists
		if (yourls_is_shorturl($keyword))
		{
			// Generate the json response
			generate_json_response($keyword);
			die();
		}
	}
}

function generate_json_response($keyword)
{
	header('Content-type: application/json; charset=utf-8');

	$json = array(
		'url'      => yourls_get_keyword_longurl($keyword),
		'title'    => yourls_get_keyword_title($keyword),
		'keyword'  => $keyword,
		'shorturl' => YOURLS_SITE.'/'.$keyword
	);
    // Add support for Chinese in JSON parsing content
	echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
