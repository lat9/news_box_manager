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
define('NEWS_BOX_HEADING_TITLE', 'News Box Manager');

define('TABLE_HEADING_NEWS', 'News');
define('TABLE_HEADING_NEWS_START', 'Start Date');
define('TABLE_HEADING_NEWS_END', 'End Date');
define('TABLE_HEADING_MODIFIED', 'Last Modified');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_NEWS_TITLE', 'News Title:');
define('TEXT_NEWS_CONTENT', 'News Content:');
define('TEXT_MORE_NEWS_PAGE', 'Link to "News" page:');
define('TEXT__NEWS_CONTENT', 'News Content:');

define('TEXT_NEWS_DATE_ADDED', 'Date Added:');
define('TEXT_NEWS_DATE_MODIFIED', 'Date Modified:');
define('TEXT_NEWS_START_DATE', 'News Will Start:');
define('TEXT_NEWS_END_DATE', 'News Will End:');

define('TEXT_NEWS_DELETE_INFO', 'Are you sure you want to delete this news article?');

define('ERROR_NEWS_TITLE_CONTENT', 'The <em>News Title</em> and <em>News Content</em> must both be non-blank for at least one language');
define('ERROR_NEWS_DATE_ISSUES', 'The <em>Start Date</em> must be on or before the <em>End Date</em>.');
define('SUCCESS_NEWS_ARTICLE_CHANGED', 'The news article has been %s.');
  define('NEWS_ARTICLE_UPDATED', 'updated');
  define('NEWS_ARTICLE_CREATED', 'created');

define('TEXT_DISPLAY_NUMBER_OF_NEWS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> news)');
define('TEXT_NEWS_BOX_MANAGER_INFO', 'The News Box is set to display up to <b>%s</b> bytes (characters) not including HTML tags before link to "News" page is automatically shown. That value can be configured in <em>Configuration-&gt;News Box Manager</em>. To edit or delete news entry, it needs to be in unpublished mode.');