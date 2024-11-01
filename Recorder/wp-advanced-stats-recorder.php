<?php
/*
Plugin Name: WP-Advanced-Stats-Recorder
Plugin URI: http://blog.noontide.ca/?p=58
Description: An Advanced Stats Tracking Plugin - Recording Side
Version: 1.0.0Beta
Author: J2000_ca
Author URI: http://blog.noontide.ca/
*/

if(!isset($wpdb->stats))
	$wpdb->stats = $table_prefix . "adstats";

if(isset($_GET['activate']) && $_GET['activate'] == 'true')
	add_action('init', 'advanced_stats_install');
add_action('wp_head', 'record_advanced_stats');

function advanced_stats_install() 
{
	global $user_level,$wpdb;

	get_currentuserinfo();
	if ($user_level > 8)
	{
  		if($wpdb->get_var("show tables like '$wpdb->stats'") != $wpdb->stats)
		{
			$sql = "CREATE TABLE `$wpdb->stats` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`username` varchar(255) NOT NULL default 'Guest',
				`ip` varchar(15) default NULL,
				`time` datetime NOT NULL default '0000-00-00 00:00:00',
				`url` varchar(100) default NULL,
				`referer` varchar(255) default NULL,
				`user_agent` varchar(255) default NULL,
				PRIMARY KEY  (`id`)) TYPE=MyISAM;";
			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);
		}
	}
}

function record_advanced_stats($url = null)
{
	global $wpdb, $HTTP_SERVER_VARS;
	
	$user_agent = $HTTP_SERVER_VARS["HTTP_USER_AGENT"];
	$referer = urldecode($HTTP_SERVER_VARS["HTTP_REFERER"]);
	
	if(is_null($referer) || $referer == "")
		$referer = "null";
	else
		$referer = "'".$referer."'";
	if(is_null($user_agent))
		$user_agent = "null";
	else
		$user_agent = "'".$user_agent."'";
	
	$username = getUsername($user_agent);
	$user_ip = getIPWPAS();
	if($url == null)
		$url = $HTTP_SERVER_VARS['REQUEST_URI'];
	
	$sql = "INSERT INTO `$wpdb->stats`(username, IP, time, url, referer, user_agent) VALUES('$username', '$user_ip', NOW(), '$url',$referer, $user_agent)";
	$wpdb->query($sql);
}

function getUsername($user_agent)
{
	global $cookiehash;
	if(isset($_COOKIE['wordpressuser_'.$cookiehash]))
		$memberonline = trim($_COOKIE['wordpressuser_'.$cookiehash]);
	elseif(isset($_COOKIE['comment_author_'.$cookiehash]))
		$memberonline = trim($_COOKIE['comment_author_'.$cookiehash]);
	else
		$memberonline = 'Guest';

	$bots = array('Google' => 'googlebot', 'Alex' => 'ia_archiver', 'Lycos' => 'lycos', 'Ask Jeeves' => 'ask jeeves', 'Altavista' => 'scooter', 'AllTheWeb' => 'fast-webcrawler', 'Inktomi' => 'inktomi', 'Turnitin.com' => 'turnitinbot');
	foreach ($bots as $name => $lookfor)
		if (strpos($user_agent, $lookfor) !== false)
			$memberonline = $name. " Bot";
	return $memberonline;
}

function getIPWPAS()
{
	$ip;
	if(getenv("HTTP_CLIENT_IP"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if(getenv("HTTP_X_FORWARDED_FOR")) 
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if(getenv("REMOTE_ADDR")) 
		$ip = getenv("REMOTE_ADDR");
	else 
		$ip = null;
	return $ip;
}

?>