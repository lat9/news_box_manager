<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// | Do Not Remove: Coded for Zen-Cart by geeks4u.com                     |
// | Dedicated to Memory of Amelita "Emmy" Abordo Gelarderes		  |
// +----------------------------------------------------------------------+
// $Id: news_scroll_box.php,v 1.2 2004/08/26
//

$languages_id = zen_db_prepare_input((int)$_SESSION['languages_id']);
$news_box_query = $db->Execute("select nc.news_title, nc.news_content
								from " . TABLE_BOX_NEWS_CONTENT . " nc, " . TABLE_BOX_NEWS . " n
								where n.box_news_id = nc.box_news_id and nc.languages_id = " . $languages_id . " and n.news_status = 1 and now() between n.news_start_date and n.news_end_date");
while (!$news_box_query->EOF) {
  if(($news_box_query->fields['news_title']) || ($news_box_query->fields['news_content'])){
    require($template->get_template_dir('tpl_news_scroll_box.php', DIR_WS_TEMPLATE, $current_page_base,'sideboxes') . '/tpl_news_scroll_box.php');

    $title =  BOX_HEADING_SCROLL_BOX;
    $left_corner = false;
    $right_corner = false;
    $right_arrow = false;
    $title_link = false;

    require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);
    break;
  } 
  else{
    $news_box_query->MoveNext();
  }
}
?>