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
// $Id: tpl_more_news_default.php,v 1.2 2004/08/26
//
?>
<?php 
$newsId = zen_db_prepare_input((int)$_GET['news_id']);
$languages_id = zen_db_prepare_input((int)$_SESSION['languages_id']);
$news_box_query = $db->Execute("select nc.news_title, nc.news_content, n.news_published_date, n.news_end_date
								from " . TABLE_BOX_NEWS_CONTENT . " nc, " . TABLE_BOX_NEWS . " n 
								where nc.box_news_id = " . $newsId . " and n.box_news_id = nc.box_news_id and nc.languages_id = " . $languages_id . " and n.news_status = 1 and now() between n.news_start_date and n.news_end_date");

$newsTitle = nl2br($news_box_query -> fields['news_title']);
$newsContent = nl2br($news_box_query -> fields['news_content']);
if($newsContent){
  $newsInfo = TEXT_NEWS_PUBLISHED_DATE . ' ' . zen_date_long($news_box_query -> fields['news_published_date']);
}
else{
  $newsInfo = TEXT_NO_NEWS_FOR_LANGUAGE;
}
?>

<div class="centerColumn" id="moreNewsDefault">

<h1 id="moreNewsHeading"><?php echo HEADING_TITLE . ' ' . $newsTitle; ?></h1>

<div class="newsInfo"><?php echo $newsInfo; ?></div>
<br class="clearBoth" />
<div class="newsContent"><?php echo $newsContent; ?></div>

<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>