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
// $Id: tpl_news_scroll_box.php,v 1.2 2004/08/26
//

// Begin of News Sidebox Config
$layout = 1;  // 0 = Java Script Fader | 1 = Static 10 last news
$shown_news = 10; // Number of Shown News
// End of News Sidebox Config

// No need to change anything under this line
$languages_id = zen_db_prepare_input((int)$_SESSION['languages_id']);
$news_box_query = $db->Execute("select n.box_news_id, nc.languages_id, 
                                nc.news_title, nc.news_content, n.more_news_page, n.news_added_date, n.news_start_date
								from " . TABLE_BOX_NEWS_CONTENT . " nc, " . TABLE_BOX_NEWS . " n
								where n.box_news_id = nc.box_news_id and nc.languages_id = " . $languages_id . " and n.news_status = 1 and now() between n.news_start_date and n.news_end_date 
								order by n.news_start_date desc, n.news_added_date desc
								limit " . $shown_news);

(int)$news_box_char_count = ((NEWS_BOX_CHAR_COUNT) ? NEWS_BOX_CHAR_COUNT : 0);
$p_class_open = '<div id="newsBox">';
$p_class_close = '</div>';
(int)$p_class_len = strlen(addslashes($p_class_open . $p_class_close));

function prepString($prep_string){
	$ret_string = '';
}
if ($layout==0) {
while(!$news_box_query->EOF) {
  if(($news_box_query ->fields['news_title']) || ($news_box_query ->fields['news_content'])){
    $i++;
    $char_cnt = strlen(strip_tags(ereg_replace("(\r\n|\n|\r)", "", $news_box_query->fields['news_title'] . $news_box_query->fields['news_content'])));
    $newsId = 'news_id=' . $news_box_query->fields['box_news_id'];
    $display_news = addslashes(ereg_replace("(\r\n|\n|\r)", " ", $p_class_open . $news_box_query->fields['news_title']). $p_class_close . '<br/>' . ereg_replace("(\r\n|\n|\r)", "", $p_class_open . $news_box_query ->fields['news_content']). $p_class_close);
    if($news_box_query->fields['more_news_page'] || $char_cnt > $news_box_char_count){
      $click_for_more = '<a class="newsBoxContent" href="' . zen_href_link(FILENAME_MORE_NEWS, $newsId) . '">' . TEXT_LINK_MORE . '</a><br />';
      $display_news = addslashes(ereg_replace("(\r\n|\n|\r)", "", $p_class_open . trim($news_box_query->fields['news_title'])) . $p_class_close . $click_for_more . '<br/>' . ereg_replace("(\r\n|\n|\r)", "", $p_class_open . trim($news_box_query ->fields['news_content'])). $p_class_close);
	  if($char_cnt > $news_box_char_count){
	    (int)$click_for_more_len = strlen(strip_tags($click_for_more));
	    (int)$true_click_for_more_len = strlen(addslashes($click_for_more));
        $display_news = addslashes(ereg_replace("(\r\n|\n|\r)", "", $p_class_open . strip_tags($news_box_query->fields['news_title'])) . $p_class_close . $click_for_more . '<br/>' . ereg_replace("(\r\n|\n|\r)", "", $p_class_open . strip_tags($news_box_query ->fields['news_content'])). $p_class_close);
        $display_news = substr($display_news, 0, $news_box_char_count + $true_click_for_more_len + $p_class_len);
	    $pos = strrpos($display_news, ' ');
        $display_news = trim(trim(substr($display_news, 0, $pos),'!:;\"\',.?')) . TEXT_TRAIL_STR;
      }
    }
    $box_news_content[$i] = $display_news;
  }
  $news_box_query->MoveNext();
}
$js_news = '';
if($box_news_content)
$js_news = implode('","',$box_news_content);
$content = '';
$content .= '<div class="sideBoxContent">';	
$content .= '

<script language="JavaScript1.2">

/*
Fading Scroller- By DynamicDrive.com
For full source code, and usage terms, visit http://www.dynamicdrive.com
This notice MUST stay intact for use
*/
//Modified by geeks4u.com

var delay=4000 //set delay between message change (in miliseconds)

var fcontent=new Array("' . $js_news . '");  // don\'t change
begintag=\'\' //set opening tag, such as font declarations
closetag=\'\'

var fwidth="' . NEWS_BOX_WIDTH . '" //set scroller width
var fheight="' . NEWS_BOX_HEIGHT . '" //set scroller height
var foverflow=\'hidden\' //set scroller overflow

var fadescheme=0 //set 0 to fade text color from (white to black), 1 for (black to white)
var fadelinks=0  //should links inside scroller content also fade like text? 0 for no, 1 for yes.

///No need to edit below this line//////////////////

var hex=(fadescheme==0)? 255 : 0
var startcolor=(fadescheme==0)? "rgb(255,255,255)" : "rgb(0,0,0)"
var endcolor=(fadescheme==0)? "rgb(0,0,0)" : "rgb(255,255,255)"

var ie4=document.all&&!document.getElementById
var ns4=document.layers
var DOM2=document.getElementById
var faderdelay=0
var index=0

if (DOM2)
faderdelay=2000

//function to change content
function changecontent(){
if (index>=fcontent.length)
index=0
if (DOM2){
document.getElementById("fscroller").style.color=startcolor
document.getElementById("fscroller").innerHTML=begintag+fcontent[index]+closetag
linksobj=document.getElementById("fscroller").getElementsByTagName("A")
if (fadelinks)
linkcolorchange(linksobj)
colorfade()
}
else if (ie4)
document.all.fscroller.innerHTML=begintag+fcontent[index]+closetag
else if (ns4){
document.fscrollerns.document.fscrollerns_sub.document.write(begintag+fcontent[index]+closetag)
document.fscrollerns.document.fscrollerns_sub.document.close()
}

index++
if (fcontent.length > 1)
setTimeout("changecontent()",delay+faderdelay)
}

// colorfade() partially by Marcio Galli for Netscape Communications.  ////////////
// Modified by Dynamicdrive.com

frame=20;

function linkcolorchange(obj){
if (obj.length>0){
for (i=0;i<obj.length;i++)
obj[i].style.color="rgb("+hex+","+hex+","+hex+")"
}
}

function colorfade() {	         	
// 20 frames fading process
if(frame>0) {	
hex=(fadescheme==0)? hex-12 : hex+12 // increase or decrease color value depd on fadescheme
document.getElementById("fscroller").style.color="rgb("+hex+","+hex+","+hex+")"; // Set color value.
if (fadelinks)
linkcolorchange(linksobj)
frame--;
setTimeout("colorfade()",60);	
}

else{
document.getElementById("fscroller").style.color=endcolor;
frame=20;
hex=(fadescheme==0)? 255 : 0
}   
}

if (ie4||DOM2)
document.write(\'<div id="fscroller" class="ScrollerFrame" style="overflow:\'+foverflow+\';width:\'+fwidth+\';height:\'+fheight+\'"></div>\')

window.onload=changecontent
</script>

';
$content .= '<hr>';
$content .= '<a href="' . zen_href_link('news_archiv', '', 'NONSSL') . '">News Archiv</a>';
$content .= '</div>';

} else {
	if ($news_box_query->RecordCount() >= 1) {
		$content ='';
		$content .= '<div class="sideBoxContent">';	
		$rows = 0;
			while (!$news_box_query->EOF) {
				$rows++;
				$news_list[$rows]['id'] = $news_box_query->fields['box_news_id'];
				$news_list[$rows]['title'] = $news_box_query->fields['news_title'];
				$news_box_query->MoveNext();
			};
			for ($i=1; $i<=sizeof($news_list); $i++) {
				$content .= '- <a href="' . zen_href_link('more_news&news_id=' . $news_list[$i]['id'], '', 'NONSSL') . '">' . $news_list[$i]['title'] . '</a><br />'; 
			};
		$content .= '<hr>';
		$content .= '<a href="' . zen_href_link('news_archiv', '', 'NONSSL') . '">News Archiv</a>';
		$content .= '</div>';
		};
};
?>