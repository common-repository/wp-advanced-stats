<?php
/*
Plugin Name: Count My Clicks
Plugin URI: http://www.skeltoac.com/category/wordpress/plugins/count-my-clicks/
Description: This plugin keeps a record of link clicks in your wp-Advanced-Stats database. For information on using the plugin and its database table, see the <a href="http://www.skeltoac.com/2005/03/27/count-my-clicks/">documentation</a>. 
Version: 1.7.1
Author: Andy Skelton
Author URI: http://www.skeltoac.com/
*/

function count_my_clicks_install() 
{
	global $user_level,$wpdb;

	get_currentuserinfo();
	if ($user_level > 8)
	{
		if(in_array('wp-advanced-stats.php', get_settings('active_plugins')) && file_exists(ABSPATH . '/wp-content/plugins/wp-advanced-stats.php'))
		{
			$cmc_php = get_settings('siteurl') . "?url=";
			add_option("cmc_php",$cmc_php,"","YES");
			add_option("cmc_table",$wpdb->stats,"","YES");
		}
	}
}
function printHeadScript()
{
	echo '<script type="text/javascript" src="' . get_settings('siteurl') . '/wp-content/plugins/count-my-clicks.js"></script>';
}
function printFooterScript()
{ 
	echo '<script type="text/javascript">var cmc_php="'.get_option('cmc_php').'";linktracker_init();</script>';
}

add_action('template_redirect','count_my_click_template');
function count_my_click_template()
{
	if(isset($_GET['url']))
	{
		$url = $_GET['url'];
		header('Status: 204 No Content');
		ob_end_flush();
		$url = str_replace('/:/', '://', $url);	// We must reverse the obfuscation (see below)
		$url = wp_specialchars($url);
		record_advanced_stats($url);
	}
}

if(isset($_GET['activate']) && $_GET['activate'] == 'true')
	add_action('init', 'count_my_clicks_install');
add_action('wp_head', 'printHeadScript');
add_action('wp_footer', 'printFooterScript');

do_action('cmc_loaded');	// I think so.

?>