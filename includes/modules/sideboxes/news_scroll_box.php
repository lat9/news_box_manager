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
$languages_id = zen_db_prepare_input((int)$_SESSION['languages_id']);
$news_box_query = $db->Execute("SELECT nc.news_title, nc.news_content
                                  FROM " . TABLE_BOX_NEWS_CONTENT . " nc, " . TABLE_BOX_NEWS . " n
                                 WHERE n.box_news_id = nc.box_news_id AND nc.languages_id = $languages_id AND n.news_status = 1 AND now() BETWEEN n.news_start_date AND n.news_end_date");
while (!$news_box_query->EOF) {
  if (zen_not_null ($news_box_query->fields['news_title']) || zen_not_null ($news_box_query->fields['news_content'])){
    require $template->get_template_dir ('tpl_news_scroll_box.php', DIR_WS_TEMPLATE, $current_page_base,'sideboxes') . '/tpl_news_scroll_box.php';

    $title = BOX_HEADING_SCROLL_BOX;
    $left_corner = false;
    $right_corner = false;
    $right_arrow = false;
    $title_link = false;

    require $template->get_template_dir ($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default;
    break;
  }
  $news_box_query->MoveNext();

}