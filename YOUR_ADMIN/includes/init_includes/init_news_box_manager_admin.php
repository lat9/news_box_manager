<?php
// -----
// Part of the News Box Manager plugin, re-structured for Zen Cart v1.5.1 and later by lat9.
// Copyright (C) 2015, Vinos de Frutas Tropicales
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

define('NEWS_BOX_CURRENT_VERSION', '2.0.0');
define('NEWS_BOX_CURRENT_UPDATE_DATE', '2015-03-xx');
define('NEWS_BOX_CURRENT_VERSION_DATE', NEWS_BOX_CURRENT_VERSION . ' (' . NEWS_BOX_CURRENT_UPDATE_DATE . ')');

function init_nbm_next_sort ($menu_key) {
  global $db;
  $next_sort = $db->Execute('SELECT MAX(sort_order) as max_sort FROM ' . TABLE_ADMIN_PAGES . " WHERE menu_key='$menu_key'");
  return $next_sort->fields['max_sort'] + 1;
}

$configurationGroupTitle = 'News Box Manager';
$configuration = $db->Execute ("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = '$configurationGroupTitle' LIMIT 1");
if ($configuration->EOF) {
  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " 
                 (configuration_group_title, configuration_group_description, sort_order, visible) 
                 VALUES ('$configurationGroupTitle', '$configurationGroupTitle Settings', '1', '1');");
  $cgi = $db->Insert_ID(); 
  $db->Execute("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = $cgi WHERE configuration_group_id = $cgi;");
  
} else {
  $cgi = $configuration->fields['configuration_group_id'];
  
}

// ----
// Record the configuration's current version in the database.
//
if (!defined ('NEWS_BOX_MODULE_VERSION')) {
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) VALUES ('News Box Manager Version', 'NEWS_BOX_MODULE_VERSION', '" . NEWS_BOX_CURRENT_VERSION_DATE . "', 'The News Box Manager version number and release date.', $cgi, 10, now(), 'trim(')");
   
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'Items to Show in Sidebox', 'NEWS_BOX_SHOW_NEWS', '10', 'Set the maximum number of the latest-news titles to show in the &quot;Latest News&quot; sidebox.', $cgi, 40, now(), NULL, NULL)");
   
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'Items to Show in Home Page', 'NEWS_BOX_SHOW_CENTERBOX', '10', 'Set the maximum number of the latest-news titles to show in the &quot;Latest News&quot; section at the bottom of your home page.  Set the value to 0 to disable the news display.', $cgi, 45, now(), NULL, NULL)");
   
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'News Archive: Items to Display', 'NEWS_BOX_SHOW_ARCHIVE', '10', 'Set the maximum number of the latest-news titles to show on the split-page view of the &quot;News Archive&quot; page.', $cgi, 47, now(), NULL, NULL)");
  
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function ) VALUES ( 'News Archive: Date Format', 'NEWS_BOX_DATE_FORMAT', 'long', 'Choose the style of dates to be displayed for an article\'s start/end dates on the &quot;News Archive&quot; page.  Choose <em>short</em> to have dates displayed similar to <b>03/02/2015</b> or <em>long</em> to display the date like <b>Monday 02 March, 2015</b>.<br /><br />The date-related settings you have made in your primary language files are honoured using the built-in functions <code>zen_date_short</code> and <code>zen_date_long</code>, respectively.', $cgi, 50, now(), NULL, 'zen_cfg_select_option(array(\'short\', \'long\'),')");
  
  define ('NEWS_BOX_MODULE_VERSION', NEWS_BOX_CURRENT_VERSION_DATE);
  
}

// -----
// Update the configuration table to reflect the current version, if it's not already set.
//
if (NEWS_BOX_MODULE_VERSION != NEWS_BOX_CURRENT_VERSION) {
  $db->Execute ("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . NEWS_BOX_CURRENT_VERSION_DATE . "' WHERE configuration_key = 'NEWS_BOX_MODULE_VERSION'");
  
}

// ----
// Create each of the database tables for the news-box records.
//
$sql = "CREATE TABLE IF NOT EXISTS " . TABLE_BOX_NEWS . " (
  `box_news_id` int(11) NOT NULL auto_increment,
  `news_added_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `news_modified_date` datetime default NULL,
  `news_start_date` datetime default NULL,
  `news_end_date` datetime default NULL,
  `news_status` tinyint(1) default '0',
  PRIMARY KEY  (`box_news_id`)
) ENGINE=MyISAM";
$db->Execute($sql);

$sql = "CREATE TABLE IF NOT EXISTS " . TABLE_BOX_NEWS_CONTENT . " (
  `box_news_id` int(11) NOT NULL default '0',
  `languages_id` int(11) NOT NULL default '1',
  `news_title` varchar(255) NOT NULL default '',
  `news_content` text NOT NULL,
  PRIMARY KEY  (`languages_id`,`box_news_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET " . DB_CHARSET;
$db->Execute($sql);

// -----
// Register the admin-level pages for use.
//
if (!zen_page_key_exists ('localizationNewsBox')) {
  zen_register_admin_page('localizationNewsBox', 'BOX_NEWS_BOX_MANAGER', 'FILENAME_NEWS_BOX_MANAGER', '', 'localization', 'Y', init_nbm_next_sort('localization'));
  
}
if (!zen_page_key_exists ('configNewsBox')) {
  zen_register_admin_page('configNewsBox', 'BOX_NEWS_BOX_MANAGER', 'FILENAME_CONFIGURATION', "gID=$cgi", 'configuration', 'Y', init_nbm_next_sort('configuration'));
  
}
