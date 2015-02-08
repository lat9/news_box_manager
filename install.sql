INSERT INTO `configuration` VALUES ('', 'News Box Character Count', 'NEWS_BOX_CHAR_COUNT', '200', 'Set the number of characters (bytes) that you want to display in the news preview box.', 19, 99, NULL, '2004-09-07 12:00:00', NULL, NULL);
INSERT INTO `configuration` VALUES ('', 'News Box Width in px', 'NEWS_BOX_WIDTH', '140px', 'Set the width of News Box to fine tune how it displays your news.', 19, 99, NULL, '2004-09-07 12:00:00', NULL, NULL);
INSERT INTO `configuration` VALUES ('', 'News Box Height in px', 'NEWS_BOX_HEIGHT', '160px', 'Set the height of News Box to fine tune how it displays your news.', 19, 99, NULL, '2004-09-07 12:00:00', NULL, NULL);
INSERT INTO `configuration` VALUES ('', 'Number of Shown News', 'NEWS_BOX_SHOW_NEWS', '10', 'Set the number of Shown News', 19, 99, NULL, '2004-09-07 12:00:00', NULL, NULL);

CREATE TABLE `box_news` (
  `box_news_id` int(11) NOT NULL auto_increment,
  `news_added_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `news_modified_date` datetime default NULL,
  `news_start_date` datetime default NULL,
  `news_end_date` datetime default NULL,
  `news_published_date` datetime default NULL,
  `news_status` tinyint(1) default '0',
  `more_news_page` tinyint(1) default '0',
  PRIMARY KEY  (`box_news_id`)
) TYPE=MyISAM AUTO_INCREMENT=1;

CREATE TABLE `box_news_content` (
  `box_news_id` int(11) NOT NULL default '0',
  `languages_id` int(11) NOT NULL default '1',
  `news_title` varchar(64) NOT NULL default '',
  `news_content` text NOT NULL,
  PRIMARY KEY  (`languages_id`,`box_news_id`)
) TYPE=MyISAM;