=================================================
| News Box Manager Version 1.4 german/english   |
|                                               |
| based on News Box Manager 1.3.0               |      
| modified by MaleBorg <maleborg@gmx.de>        |
|                                               |
| Tested on Zen-Cart 1.3.5 & 1.3.6              | 
=================================================

The original readme & credits  for the unmodified contrib are in the file contrib_credits.txt

This Contrib gives you a News Manager. The news would be createt in the Admin Panel and saved in the Database.

The News are shown in a Sidebox. 
You can chose between a static list or a java fadeout effect.

Also included is a news archive for all published news.


Installation
=============
No core files will be overwritten, but some additions to the database are needed.
Please make a backup BEFORE the installation!

In the dir includes/templates/ you could change the dir TEMPLATE_DEFAULT to your template dir

Just copy the files into your Zen-Cart Installation.

Now go to your Adminpanel --> TOOLS --> SQL PATCHES an upload the included file INSTALL.SQL or copy the content of the file INSTALL.SQL into the Adminpanel.

Then you have to add these lines in your stylesheet.css:

.newsInfo {
  text-align: left;
  font-style: normal;
}

.newsContent {
font-size: 1.0em;
}

#newsArchivTitleHeading {
	text-align: left;
	}

#newsArchivDateHeading {
	text-align: right;
	}


Thats it!


Konfiguration
=============
Go to the Adminpanel --> MY SHOP --> LAYOUTSETTINGS, there are 3 new entries.

News Box Character Count = The number of characters which were shown in the Sidebox. 
If your News is longer than this settings, it was shorten and a link to the complete news appears.
These setting is only active when the java fadeout layout is chosen.

News Box Width & News Box Height in px are the Settings vor the Box

In the File \includes\templates\template_default\templates\tpl_more_news_default.php you will find the config for the layout and the number of shown news.

You could create the news unter TOOLS --> NEWS BOX MANAGER


If you had a problem or find a bug please mail (english | german only) or write into the forum.