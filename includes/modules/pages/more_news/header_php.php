<?php
// -----
// Part of the News Box Manager plugin, re-structured for Zen Cart v1.5.1 and later by lat9.
// Copyright (C) 2015, Vinos de Frutas Tropicales
//
// +----------------------------------------------------------------------+
// | Do Not Remove: Coded for Zen-Cart by geeks4u.com                     |
// | Dedicated to Memory of Amelita "Emmy" Abordo Gelarderes              |
// +----------------------------------------------------------------------+
//
$_SESSION['navigation']->remove_current_page();

require(DIR_WS_MODULES . zen_get_module_directory ('require_languages.php'));
$breadcrumb->add (NAVBAR_TITLE);

$news_id = (isset ($_GET['news_id'])) ? (int)zen_db_prepare_input ($_GET['news_id']) : 0;
$languages_id = (int)zen_db_prepare_input ($_SESSION['languages_id']);
$news_box_query = $db->Execute ("SELECT nc.news_title, nc.news_content, n.news_start_date, n.news_end_date
                                   FROM " . TABLE_BOX_NEWS_CONTENT . " nc, " . TABLE_BOX_NEWS . " n 
                                  WHERE nc.box_news_id = $news_id 
                                    AND n.box_news_id = nc.box_news_id 
                                    AND nc.languages_id = $languages_id 
                                    AND n.news_status = 1 
                                    AND now() >= n.news_start_date
                                    AND ( n.news_end_date = '0000-00-00 00:00:00' OR now() <= n.news_end_date) LIMIT 1");
if ($news_box_query->EOF) {
  $news_title = '';
  $news_content = '';
  $start_date = false;
  
} else {
  $news_title = nl2br ($news_box_query->fields['news_title']);
  $news_content = nl2br ($news_box_query->fields['news_content']);
  $start_date = (NEWS_BOX_DATE_FORMAT == 'short') ? zen_date_short ($news_box_query->fields['news_start_date']) : zen_date_long ($news_box_query->fields['news_start_date']);
  if ($news_box_query->fields['news_end_date'] != '0000-00-00 00:00:00') {
    $end_date = (NEWS_BOX_DATE_FORMAT == 'short') ? zen_date_short ($news_box_query->fields['news_start_date']) : zen_date_long ($news_box_query->fields['news_start_date']);
    
  }
  
}