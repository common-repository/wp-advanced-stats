<?php
/*
Plugin Name: WP-Advanced-Stats-Viewer
Plugin URI: http://blog.noontide.ca/?p=58
Description: An Advanced Stats Tracking Plugin - Viewing Side
Version: 1.0.0Beta
Author: J2000_ca
Author URI: http://blog.noontide.ca/
*/

if(!isset($wpdb->stats))
	$wpdb->stats = $table_prefix . "adstats";
$Stats = new Stats();

function getUsersOnline($format = "% Users Online", $before = "(", $after = ")")
{
	global $Stats;
	print $before.str_replace("%",$Stats->getOnline(),$format).$after;
}

function wasv_menu()
{
	add_submenu_page('index.php', 'Advanced Stats', 'Advanced Stats', 8, __FILE__, 'advanced_stats_viewer_dashboard');
}
add_action('admin_menu', 'wasv_menu');

function advanced_stats_viewer_dashboard()
{ 
	global $Stats; ?>
	<div class="wrap">
		<h2>Statistics</h2>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
		 <?php $Stat = $Stats->getStats(); $Info = $Stats->getInfo();?>
		 <tr>
		  <th width="33%" scope="row">Recording Since:</th>
		  <td><?php print mysql2date(get_option("date_format")." ".get_option("time_format"),$Info->Create_time);?></td>
		 </tr>
		 <tr>
		  <th scope="row">Hits:</th>
		  <td><?php print $Stat->hits;?></td>
		 </tr>
		 <tr>
		  <th scope="row">Vistors:</th>
		  <td><?php print $Stat->vistors;?></td>
		 </tr>
		 <tr>
		  <th scope="row">Database Size:</th>
		  <td><?php print round(($Info->Data_length + $Info->Index_length)/1024,2);?>kB</td>
		 </tr>
		</table>
		<fieldset class="options">
			<legend><strong>Daily Stats</strong></legend>
			<table style="text-align:center; width:100%;">
			 <thead>
			  <tr>
			   <th>Users Online</th>
			   <th>Hits</th>
			   <th>Vistors</th>
			   <th>Referers</th>
			   <th>Browsers</th>
			   <th>Date</th>
			  </tr>
			 </thead>
			 <tbody>
			 <?php foreach($Stats->getDays() as $key => $Day) { ?>
			 <tr<?php if(1&$key)  print ' class="alternate"';?>>
			  <td><?php print $Day->users;?></td>
			  <td><?php print $Day->hits;?></td>
			  <td><?php print $Day->vistors;?></td>
			  <td><?php print $Day->referers;?></td>
			  <td><?php print $Day->browsers;?></td>
			  <td><?php print mysql2date(get_option("date_format"),$Day->date);?></td>
			 </tr>
			 <?php } ?>
			 </tbody>
			</table>
		</fieldset>
		<fieldset class="options" style="width: 50%; float: left;">
			<legend><strong>Search Terms</strong></legend>
			<table style="text-align:center; width:100%;">
			 <thead>
			  <tr>
			   <th>Term</th>
			   <th>Search Engine</th>
			   <th>Time</th>
			  </tr>
			 </thead>
			 <tbody>
			 <?php if($Stats->getSearchTerms()) foreach($Stats->getSearchTerms() as $key => $Search_term) { ?>
			 <tr<?php if(1&$key)  print ' class="alternate"';?>>
			  <td><a href="/?s=<?php print $Search_term->term;?>"><?php print $Search_term->term;?></a></td>
			  <td><?php print $Search_term->engine;?></td>
			  <td><?php print mysql2date(get_option("date_format")." ".get_option("time_format"),$Search_term->time);?></td>
			 </tr>
			 <?php } ?>
			 </tbody>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><strong>Referers</strong></legend>
			<table style="text-align:center; width:100%;">
			 <thead>
			  <tr>
			   <th>Referer</th>
			   <th>Count</th>
			  </tr>
			 </thead>
			 <tbody>
			 <?php foreach($Stats->getReferers() as $key => $Referer) { ?>
			 <tr<?php if(1&$key)  print ' class="alternate"';?>>
			  <td><a href="<?php print $Referer->referer;?>"><?php $url = parse_url($Referer->referer); print $url["host"];?></a></td>
			  <td><?php print $Referer->num;?></td>
			 </tr>
			 <?php } ?>
			 </tbody>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><strong>Users</strong></legend>
			<table style="text-align:center; width:100%;">
			 <thead>
			  <tr>
			   <th>ID</th>
			   <th>Username</th>
			   <th>IP</th>
			   <th>Time</th>
			  </tr>
			 </thead>
			 <tbody>
			 <?php foreach($Stats->getUsers() as $key => $Users) { ?>
			 <tr<?php if(1&$key)  print ' class="alternate"';?>>
			  <td><?php print $Users->id;?></td>
			  <td><?php print $Users->username;?></td>
			  <td><?php print $Users->ip;?></td>
			  <td><?php print mysql2date(get_option("date_format")." ".get_option("time_format"),$Users->time);?></td>
			 </tr>
			 <?php } ?>
			 </tbody>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><strong>User Agents</strong></legend>
			<table style="text-align:center; width:100%;">
			 <thead>
			  <tr>
			   <th>User Agent</th>
			   <th>Count</th>
			  </tr>
			 </thead>
			 <tbody>
			 <?php foreach($Stats->getUserAgents() as $key => $User_agent) { ?>
			 <tr<?php if(1&$key)  print ' class="alternate"';?>>
			  <td>
			   <?php if(preg_match( '/(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}((:[0-9]{1,5})?(\/[a-z0-9.]*)*)/i' ,$User_agent->user_agent, $Matches)) { ?>
			   <a href="<?php print $Matches[0];?>"><?php print $User_agent->user_agent;?></a>
			   <?php } else print $User_agent->user_agent;?>
			  </td>
			  <td><?php print $User_agent->num;?></td>
			 </tr>
			 <?php } ?>
			 </tbody>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><strong>Top Posts</strong></legend>
			<table style="text-align:center; width:100%;">
			 <thead>
			  <tr>
			   <th>Post Title</th>
			   <th>ID</th>
			   <th>Hits</th>
			   <th>Vistors</th>
			   <th>Last Viewed</th>
			  </tr>
			 </thead>
			 <tbody>
			 <?php if($Stats->getPosts()) foreach($Stats->getPosts() as $key => $Posts) { ?>
			 <tr<?php if(1&$key)  print ' class="alternate"';?>>
			  <td><a href="/?p=<?php print $Posts->num;?>"><?php print $Posts->Post_Title;?></a></td>
			  <td><?php print $Posts->Post_ID;?></td>
			  <td><?php print $Posts->hits;?></td>
			  <td><?php print $Posts->vistors;?></td>
			  <td><?php print mysql2date(get_option("date_format")." ".get_option("time_format"),$Posts->time);?></td>
			 </tr>
			 <?php } ?>
			 </tbody>
			</table>
		</fieldset>
	</div>
<?php 
}

class Stats
{
	var $wpdb;
	function Stats()
	{
		global $wpdb;
		$this->wpdb = &$wpdb;
	}
	
	function getDays()
	{
		$sql = 'SELECT count(DISTINCT username) as users, count(ip) as hits, count(DISTINCT ip) as vistors, count(DISTINCT referer) as referers, count(DISTINCT user_agent) as browsers, substring(time,1,10) AS date FROM '.$this->wpdb->stats.' WHERE month(now()) = month(time) AND year(now()) = year(time) GROUP BY date ORDER BY time ASC';
		return $this->wpdb->get_results($sql);
	}
	
	function getStats()
	{
		$sql = 'SELECT count(DISTINCT username) as users, count(ip) as hits, count(DISTINCT ip) as vistors, count(DISTINCT referer) as referers, count(DISTINCT user_agent) as user_agent FROM '.$this->wpdb->stats;
		return $this->wpdb->get_row($sql);
	}
	
	function getInfo()
	{
		$sql = "SHOW TABLE STATUS LIKE 'adstats'";
		return $this->wpdb->get_row($sql);
	}
	
	function getSearchTerms()
	{
		$sql = "SELECT substring_index(url, '?s=',-1) as term, 'Blog Engine' as engine, time FROM `".$this->wpdb->stats."` WHERE substring(url,1,13) = '/index.php?s=' OR substring(url,1,4) = '/?s=' ORDER BY time DESC LIMIT 0,10";
		return $this->wpdb->get_results($sql);
	}
	
	function getReferers()
	{
		$sql = 'SELECT DISTINCT referer, count(referer) as num FROM '.$this->wpdb->stats.' WHERE substring(referer, 1,24) != "http://blog.noontide.ca/" GROUP BY referer ORDER BY num DESC LIMIT 0,10';
		return $this->wpdb->get_results($sql);
	}
	
	function getUserAgents()
	{
		$sql = 'SELECT DISTINCT user_agent, count(user_agent) as num FROM '.$this->wpdb->stats.' GROUP BY user_agent ORDER BY num DESC LIMIT 0,10';
		return $this->wpdb->get_results($sql);
	}
	
	function getOnline()
	{
		$sql = 'SELECT count(DISTINCT username) + (count(DISTINCT ip) - (count(DISTINCT username) - "1")) FROM '.$this->wpdb->stats.' WHERE time > Now() - INTERVAL 300 SECOND';
		return $this->wpdb->get_var($sql);
	}
	
	function getPosts()
	{
		$sql = 'SELECT count(ip) as hits, count(DISTINCT ip) as vistors, substring_index(url, "?p=",-1) as Post_ID, Post_Title, time FROM '.$this->wpdb->stats.' INNER JOIN '.$this->wpdb->posts.' ON substring_index(url, "?p=",-1) = '.$this->wpdb->posts.'.ID WHERE substring(url,1,4) = "/?p=" OR substring(url,1,4) = "/index.php?p="  GROUP BY Post_ID ORDER BY hits DESC, time DESC LIMIT 0,10';
		return $this->wpdb->get_results($sql);
	}
	
	function getUsers()
	{
		$sql = 'SELECT '.$this->wpdb->users.'.id, username, ip, time FROM '.$this->wpdb->stats.' LEFT JOIN '.$this->wpdb->users.' ON username = '.$this->wpdb->users.'.user_login WHERE username  != "Guest" GROUP BY ip, username ORDER BY id';
		return $this->wpdb->get_results($sql);
	}
}
?>