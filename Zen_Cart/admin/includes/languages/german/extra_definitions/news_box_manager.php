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
//  $Id: news_box_manager.php,v 1.2 2004/08/26
//

define('BOX_NEWS_BOX_MANAGER', 'News Box Manager');

// define('HEADING_TITLE', 'News Box Manager'); // JTD
define('NEWS_BOX_HEADING_TITLE', 'News Box Manager');// JTD

define('TABLE_HEADING_NEWS', 'News');
define('TABLE_HEADING_SIZE', 'Titel + Inhalt Größe');
define('TABLE_HEADING_NEWS_START', 'Startdatum');
define('TABLE_HEADING_NEWS_END', 'Enddatum');
define('TABLE_HEADING_PUBLISHED', 'Veröffentlicht');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Aktion');

define('TEXT_NEWS_TITLE', 'News Titel:');
define('TEXT_NEWS_CONTENT', 'News Inhalt:');
define('TEXT_MORE_NEWS_PAGE', 'Link zur "News" Seite:');
define('TEXT__NEWS_CONTENT', 'News Inhalt:');

define('TEXT_NEWS_DATE_ADDED', 'Hinzugefügt am:');
define('TEXT_NEWS_DATE_MODIFIED', 'Verändert am:');
define('TEXT_NEWS_START_DATE', 'News startet am:');
define('TEXT_NEWS_END_DATE', 'News endet am:');
define('TEXT_NEWS_PUBLISHED_DATE', 'Veröffentlicht am:');

define('TEXT_NEWS_DELETE_INFO', 'Are you sure you want to delete this news?');

define('ERROR_NEWS_TITLE', 'News title not set!');
define('ERROR_NEWS_CONTENT', 'News content not set!');

define('TEXT_DISPLAY_NUMBER_OF_NEWS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> news)');
define('TEXT_NEWS_BOX_MANAGER_INFO', 'The News Box is set to display up to <b>%s</b> bytes (characters) not including HTML tags before link to "News" page is automatically shown. The # can be configured in Layout Settings.<br />To edit or delete news entry, it needs to be in unpublished mode.');

?>