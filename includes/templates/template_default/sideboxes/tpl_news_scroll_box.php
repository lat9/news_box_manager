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
// Begin of News Sidebox Config
$layout = 1;  // 0 = Java Script Fader | 1 = Static 10 last news
$shown_news = 10; // Number of Shown News
// End of News Sidebox Config

// No need to change anything under this line
$languages_id = zen_db_prepare_input ((int)$_SESSION['languages_id']);
$news_box_query = $db->Execute ("SELECT n.box_news_id, nc.languages_id, nc.news_title, nc.news_content, n.more_news_page, n.news_added_date, n.news_start_date
                                   FROM " . TABLE_BOX_NEWS_CONTENT . " nc, " . TABLE_BOX_NEWS . " n
                                  WHERE n.box_news_id = nc.box_news_id AND nc.languages_id = $languages_id AND n.news_status = 1 AND now() BETWEEN n.news_start_date AND n.news_end_date 
                               ORDER BY n.news_start_date desc, n.news_added_date desc
                                  LIMIT $shown_news");

(int)$news_box_char_count = ((NEWS_BOX_CHAR_COUNT) ? NEWS_BOX_CHAR_COUNT : 0);
$p_class_open = '<div id="newsBox">';
$p_class_close = '</div>';
$p_class_len = strlen (addslashes ($p_class_open . $p_class_close));

$news_box_content = array ();
if ($layout == 0) {
  while (!$news_box_query->EOF) {
    if (($news_box_query->fields['news_title']) || ($news_box_query ->fields['news_content'])) {
      $char_cnt = strlen(strip_tags(ereg_replace("(\r\n|\n|\r)", "", $news_box_query->fields['news_title'] . $news_box_query->fields['news_content'])));
      $newsId = 'news_id=' . $news_box_query->fields['box_news_id'];
      $display_news = addslashes(ereg_replace("(\r\n|\n|\r)", " ", $p_class_open . $news_box_query->fields['news_title']). $p_class_close . '<br/>' . ereg_replace("(\r\n|\n|\r)", "", $p_class_open . $news_box_query ->fields['news_content']). $p_class_close);
      if ($news_box_query->fields['more_news_page'] || $char_cnt > $news_box_char_count) {
        $click_for_more = '<a class="newsBoxContent" href="' . zen_href_link(FILENAME_MORE_NEWS, $newsId) . '">' . TEXT_LINK_MORE . '</a><br />';
        $display_news = addslashes(ereg_replace("(\r\n|\n|\r)", "", $p_class_open . trim($news_box_query->fields['news_title'])) . $p_class_close . $click_for_more . '<br/>' . ereg_replace("(\r\n|\n|\r)", "", $p_class_open . trim($news_box_query ->fields['news_content'])). $p_class_close);
        if ($char_cnt > $news_box_char_count) {
          $click_for_more_len = strlen(strip_tags($click_for_more));
          $true_click_for_more_len = strlen(addslashes($click_for_more));
          $display_news = addslashes(ereg_replace("(\r\n|\n|\r)", "", $p_class_open . strip_tags($news_box_query->fields['news_title'])) . $p_class_close . $click_for_more . '<br/>' . ereg_replace("(\r\n|\n|\r)", "", $p_class_open . strip_tags($news_box_query ->fields['news_content'])). $p_class_close);
          $display_news = substr($display_news, 0, $news_box_char_count + $true_click_for_more_len + $p_class_len);
          $pos = strrpos($display_news, ' ');
          $display_news = trim(trim(substr($display_news, 0, $pos),'!:;\"\',.?')) . TEXT_TRAIL_STR;
        }
      }
      $box_news_content[] = $display_news;
      
    }
    $news_box_query->MoveNext();
    
  }
  $js_news = '';
  if ($box_news_content) {
    $js_news = implode('","',$box_news_content);
    
  }
  $content = '';
  $content .= '<div class="sideBoxContent">';  
  $content .= '<script type="text/javascript" src="xxxx"></script>';
  $content .= '<hr />';
  $content .= '<a href="' . zen_href_link (FILENAME_NEWS_ARCHIVE, '', 'NONSSL') . '">' . TEXT_ALL_NEWS . '</a>';
  $content .= '</div>';

} elseif (!$news_box_query->EOF) {
  $content = '<div class="sideBoxContent">';  
  while (!$news_box_query->EOF) {
    $content .= '- <a href="' . zen_href_link (FILENAME_MORE_NEWS, 'news_id=' . $news_box_query->fields['box_news_id'], '', 'NONSSL') . '">' . $news_box_query->fields['news_title']. '</a><br />'; 
    $news_box_query->MoveNext();
    
  }
  $content .= '<hr />';
  $content .= '<a href="' . zen_href_link(FILENAME_NEWS_ARCHIVE, '', 'NONSSL') . '">' . TEXT_ALL_NEWS . '</a>';
  $content .= '</div>';
}