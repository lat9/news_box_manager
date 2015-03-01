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
function zen_get_news_title ($box_news_id, $language_id = ''){
  global $db;
  if ($language_id == '') {
    $language_id = $_SESSION['languages_id'];
    
  }
  $news = $db->Execute ("SELECT news_title FROM " . TABLE_BOX_NEWS_CONTENT . "  WHERE box_news_id = " . (int)$box_news_id . " AND languages_id = " . (int)$language_id . " LIMIT 1");
  
  return ($news->EOF) ? '' : $news->fields['news_title'];
  
}

function zen_get_news_content ($box_news_id, $language_id = ''){
  global $db;
  if ($language_id == '') {
    $language_id = $_SESSION['languages_id'];
    
  }
  $news = $db->Execute ("SELECT news_content FROM " . TABLE_BOX_NEWS_CONTENT . " WHERE box_news_id = " . (int)$box_news_id . " AND languages_id = '" . (int)$language_id . " LIMIT 1");
  
  return ($news->EOF) ? '' : $news->fields['news_content'];
  
}

require('includes/application_top.php');

$action = (isset ($_GET['action']) ? $_GET['action'] : '');
$page = (isset ($_GET['page'])) ? ('&page=' . $_GET['page']) : '';
switch ($action){
  case 'insert':
  case 'update': {
    $news_title = zen_db_prepare_input ($_POST['news_title']);
    $news_content = zen_db_prepare_input ($_POST['news_content']);
    $news_start_date = zen_db_prepare_input ($_POST['news_start_date']);
    $news_end_date = zen_db_prepare_input ($_POST['news_end_date']);
    $more_news_page = zen_db_prepare_input ($_POST['more_news_page']);
    if( isset ($_POST['box_news_id'])) {
      $box_news_id = zen_db_prepare_input ($_POST['box_news_id']);
    }
    $news_error = false;
    if (empty($news_title)){
      $messageStack->add_session (ERROR_NEWS_TITLE, 'error');
      zen_redirect (zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_POST['box_news_id']));
 
    }
    if (empty ($news_content)){
      $messageStack->add(ERROR_NEWS_CONTENT, 'error');
      $news_error = true;
      
    }

    if ($news_error == true) {
      $action = 'new';
      
    } else {
      $sql_data_array = array ('news_start_date' => $news_start_date,
                               'news_end_date' => $news_end_date,
                               'more_news_page' => $more_news_page
                              );

      if( $action == 'insert') {
         $sql_data_array['news_added_date'] = 'now()';
         $sql_data_array['news_start_date'] = ($sql_data_array['news_start_date'] == NULL) ? 'now()' : '';
         $sql_data_array['news_end_date'] = ($sql_data_array['news_end_date'] == NULL) ? '2035-12-31' : '';
         $sql_data_array['news_status'] = 0;
         $sql_data_array['more_news_page'] = 0;
         zen_db_perform (TABLE_BOX_NEWS, $sql_data_array);
         $box_news_id = zen_db_insert_id();
         
       } else {
         $sql_data_array['news_modified_date'] = 'now()';
         zen_db_perform (TABLE_BOX_NEWS, $sql_data_array, 'update', "box_news_id = '" . (int)$box_news_id . "'");
         
       }

       $languages = zen_get_languages(); 
       $news_title_array = $_POST['news_title'];
       $news_content_array = $_POST['news_content'];
       for ($i=0, $n = sizeof ($languages); $i < $n; $i++){
        $language_id = $languages[$i]['id'];
        $sql_data_array = array ('news_title' => zen_db_prepare_input ($news_title_array[$language_id]),
                                 'news_content' => zen_db_prepare_input ($news_content_array[$language_id]));

        if($action == 'insert'){
          $insert_sql_data = array ('box_news_id' => $box_news_id, 'languages_id' => $language_id);
          $sql_data_array = array_merge ($sql_data_array, $insert_sql_data);
          zen_db_perform (TABLE_BOX_NEWS_CONTENT, $sql_data_array);
          
        } else {
          zen_db_perform (TABLE_BOX_NEWS_CONTENT, $sql_data_array, 'update', "box_news_id = " . (int)$box_news_id . " AND languages_id = $language_id");
          
       }
     }
     zen_redirect (zen_href_link (FILENAME_NEWS_BOX_MANAGER, "nID=$box_news_id$page"));
     
    }
    break;
    
  }
  case 'deleteconfirm':  {
    $nID = (int)zen_db_prepare_input ($_GET['nID']);
    $db->Execute ("DELETE FROM " . TABLE_BOX_NEWS . " WHERE box_news_id = $nID");
    $db->Execute ("DELETE FROM " . TABLE_BOX_NEWS_CONTENT . " WHERE box_news_id = $nID");
    zen_redirect (zen_href_link (FILENAME_NEWS_BOX_MANAGER, (isset ($_GET['page']) ? ('page=' . $_GET['page']) : '');
    break;
     
  }
  case 'confirm_publish': {
    $nID = (int)zen_db_prepare_input ($_GET['nID']);
    $news = $db->Execute ("SELECT news_status FROM " . TABLE_BOX_NEWS . " WHERE box_news_id = $nID LIMIT 1");
    if (!$news->EOF) {
      $news_status = ($news->fields['news_status'] == 0) ? 1 : 0;
      $db->Execute ("UPDATE " . TABLE_BOX_NEWS . " SET news_status = $news_status, news_published_date = 'now()' WHERE box_news_id = $nID LIMIT 1");

    }
    zen_redirect (zen_href_link (FILENAME_NEWS_BOX_MANAGER, "nID=$nID$page"));
    break;
    
  }
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script type="text/javascript" src="includes/menu.js"></script>
<script type="text/javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  if (typeof _editor_url == "string") HTMLArea.replaceAll();
  }
  // -->
</script>
<?php
if ($editor_handler != '') {
  include ($editor_handler);

}
?>
</head>
<body onload="init();">
  <!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo NEWS_BOX_HEADING_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
             </tr>
          </table></td>
        </tr>

        <tr>
         <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
<?php
if ($action == 'new') {
  $form_action = 'insert';
  $parameters = array ( 'news_title' => '',
                        'news_content' => '',
                        'news_added_date' => '',
                        'news_modified_date' => '',
                        'news_start_date' => '',
                        'news_end_date' => '',
                        'more_news_page' => '');
  $nInfo = new objectInfo ($parameters);
  if (isset ($_GET['nID'])) {
    $form_action = 'update';
    $nID = (int)zen_db_prepare_input ($_GET['nID']);
    $news = $db->Execute ("SELECT nc.news_title, nc.news_content, n.more_news_page, date_format(n.news_added_date, '%Y-%m-%d') as news_added_date, date_format(n.news_modified_date, '%Y-%m-%d') as news_modified_date,       date_format(n.news_start_date, '%Y-%m-%d') as news_start_date, date_format(n.news_end_date, '%Y-%m-%d') as news_end_date
                             FROM " . TABLE_BOX_NEWS_CONTENT . " nc, " . TABLE_BOX_NEWS . " n 
                            WHERE n.box_news_id = $nID LIMIT 1");
    if (!$news->EOF) {
      $nInfo->objectInfo ($news->fields);
      
    }
  } elseif (isset ($_POST) && is_array ($_POST)) {              
    $nInfo->objectInfo ($_POST);
   
  }
?>
        <tr>
          <td><?php echo zen_draw_form('news', FILENAME_NEWS_BOX_MANAGER, "action=$form_action$page"); if ($form_action == 'update') echo zen_draw_hidden_field ('box_news_id', $nID); ?>
            <div id="spiffycalendar" class="text"></div>
            <link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
            <script type="text/javascript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
            <script type="text/javascript">
            <!--
              var dateNewsStart = new ctlSpiffyCalendarBox("dateNewsStart", "news", "news_start_date", "btnDate1", "<?php echo $nInfo->news_start_date; ?>",scBTNMODE_CUSTOMBLUE);
              var dateNewsEnd = new ctlSpiffyCalendarBox("dateNewsEnd", "news", "news_end_date", "btnDate2", "<?php echo $nInfo->news_end_date; ?>",scBTNMODE_CUSTOMBLUE);
            //-->
            </script>
          <table border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td colspan="2"><?php echo zen_draw_separator ('pixel_trans.gif', '1', '10'); ?></td>
            </tr>
            <tr>
              <td class="main"><?php echo TEXT_MORE_NEWS_PAGE; ?></td>
              <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . zen_draw_checkbox_field('more_news_page','1',$nInfo->more_news_page); ?></td>
            </tr>
            <tr>
              <td class="main"><?php echo TEXT_NEWS_START_DATE; ?><br /><small>(YYYY-MM-DD)</small></td>
              <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><script language="javascript">dateNewsStart.writeControl(); dateNewsStart.dateFormat="yyyy-MM-dd";</script></td>
            </tr>
            <tr>
              <td class="main"><?php echo TEXT_NEWS_END_DATE; ?><br /><small>(YYYY-MM-DD)</small></td>
              <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><script language="javascript">dateNewsEnd.writeControl(); dateNewsEnd.dateFormat="yyyy-MM-dd";</script></td>
            </tr>
            <tr>
              <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '1', '35'); ?></td>
            </tr>
<?php 
  $languages = zen_get_languages();
  for ($i=0, $n=sizeof($languages); $i<$n; $i++){
?>
            <tr>
              <td class="main"><?php if($i == 0) echo TEXT_NEWS_TITLE; ?></td>
              <td class="main"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . zen_draw_input_field('news_title[' . $languages[$i]['id'] . ']', (isset($news_title[$languages[$i]['id']]) ? stripslashes($news_title[$languages[$i]['id']]) : zen_get_news_title($_GET['nID'], $languages[$i]['id'])), zen_set_field_length(TABLE_PRODUCTS_DESCRIPTION, 'products_name')); ?></td>
            </tr>
<?php
  }
?>
            <tr>
              <td colspan="2"><?php echo zen_draw_separator ('pixel_trans.gif', '1', '35'); ?></td>
            </tr>
<?php
  for ($i=0, $n=sizeof($languages); $i<$n; $i++){
?>
            <tr>
              <td class="main" valign="top"><?php if ($i == 0) echo TEXT__NEWS_CONTENT; ?></td>
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="main" valign="top"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                  <td class="main"><?php echo zen_draw_textarea_field('news_content[' . $languages[$i]['id'] . ']', 'soft', '100%', '35', (isset($news_content[$languages[$i]['id']]) ? stripslashes($news_content[$languages[$i]['id']]) : zen_get_news_content($_GET['nID'], $languages[$i]['id'])),'id="ta' . $i . '"'); ?></td>
                </tr>
              </table></td>
            </tr>
<?php
  }
?>
            <tr>
             <td><?php echo zen_draw_separator ('pixel_trans.gif', '1', '10'); ?></td>
            </tr>
            
            <tr>
             <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
               <tr>
                 <td class="main" align="right"><?php echo (($form_action == 'insert') ? zen_image_submit ('button_save.gif', IMAGE_SAVE) : zen_image_submit ('button_update.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;<a href="' . zen_href_link (FILENAME_NEWS_BOX_MANAGER, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . (isset($_GET['nID']) ? 'nID=' . $_GET['nID'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
               </tr>
             </table></td>
            </tr>
            
          </table></form></td>
        </tr>
<?php
} elseif ($action == 'preview') {
?>
        <tr>
          <td class="main" colspan="3" align="right"><?php echo '<a href="' . zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_GET['nID']) . $page . '">' . zen_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
        </tr>
<?php 
  $languages = zen_get_languages();
  for ($i=0, $n=sizeof($languages); $i<$n; $i++){
?>
        <tr>
          <td class="main" colspan="2"><?php if($i == 0) echo TEXT_NEWS_TITLE . '<br /><br />'; if($i > 0) echo zen_draw_separator('pixel_trans.gif', '1', '30'); ?></td>
        </tr>
        <tr>
          <td class="main" colspan="2"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .  zen_get_news_title($_GET['nID'], $languages[$i]['id']); ?></td>
        </tr>
        <tr>
          <td class="main" style="width:<?php echo BOX_WIDTH_LEFT ?>"></td><td class="main"><div style="height:100%; width:100%; overflow:visible; border:1px solid #ccc"><?php echo nl2br(zen_get_news_title($_GET['nID'], $languages[$i]['id'])) . '<br /><br />' . nl2br(zen_get_news_content($_GET['nID'], $languages[$i]['id'])); ?></div></td><td class="main" style="width:<?php echo BOX_WIDTH_LEFT ?>"></td>
        </tr>
<?php
  }
?>
        <tr>
          <td class="main" colspan="3" align="right"><?php echo '<a href="' . zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_GET['nID']) . $page . '">' . zen_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
        </tr>
<?php
} elseif ($action == 'publish') {
  $news = $db->Execute("SELECT news_status FROM " . TABLE_BOX_NEWS . " WHERE box_news_id = " . (int)$nID . " LIMIT 1");
  $nInfo = new objectInfo ($news->fields);
?>
        <tr>
          <td class="main" colspan="3" align="right">
            <a href="<?php echo zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_GET['nID'] . $page . '&action=confirm_publish'); ?>"><?php echo(($news->fields['news_status'] == 1) ? zen_image_button ('button_unpublish.gif', IMAGE_UNPUBLISH) : zen_image_button ('button_publish.gif', IMAGE_PUBLISH)); ?></a>&nbsp&nbsp;
            <a href="<?php echo zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_GET['nID'] . $page); ?>"><?php echo zen_image_button ('button_cancel.gif', IMAGE_CANCEL); ?></a>
          </td>
        </tr>

<?php 
  $languages = zen_get_languages();
  for ($i=0, $n=sizeof ($languages); $i<$n; $i++){
?>
        <tr>
          <td class="main" colspan="2"><?php if($i == 0) echo TEXT_NEWS_TITLE . '<br /><br />'; if($i > 0) echo zen_draw_separator('pixel_trans.gif', '1', '30'); ?></td>
        </tr>
        
        <tr>
          <td class="main" colspan="2"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .  zen_get_news_title($_GET['nID'], $languages[$i]['id']); ?></td>
        </tr>
        
        <tr>
          <td class="main" style="width:<?php echo BOX_WIDTH_LEFT ?>"></td><td class="main"><div style="height:100%; width:100%; overflow:visible; border:1px solid #ccc"><?php echo nl2br(zen_get_news_title($_GET['nID'], $languages[$i]['id'])) . '<br /><br />' . nl2br(zen_get_news_content($_GET['nID'], $languages[$i]['id'])); ?></div></td><td class="main" style="width:<?php echo BOX_WIDTH_LEFT ?>"></td>
        </tr>
<?php
  }
?>
        <tr>
          <td class="main" colspan="3" align="right">
            <a href="<?php echo zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_GET['nID'] . $page . '&action=confirm_publish'); ?>"><?php echo (($news->fields['news_status'] == 1) ? zen_image_button ('button_unpublish.gif', IMAGE_UNPUBLISH) : zen_image_button ('button_publish.gif', IMAGE_PUBLISH)); ?></a>&nbsp;&nbsp;
            <a href="<?php echo zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_GET['nID'] . $page); ?>"><?php echo zen_image_button ('button_cancel.gif', IMAGE_CANCEL); ?></a>
          </td>
        </tr>
<?php
} elseif ($action == 'confirm') {
  $nID = (int)zen_db_prepare_input ($_GET['nID']);
  $news = $db->Execute ("SELECT news_title, news_content FROM " . TABLE_BOX_NEWS_CONTENT . " WHERE box_news_id = $nID LIMIT 1");
  $nInfo = new objectInfo ($news->fields);

} else {
?>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr class="dataTableHeadingRow">
                  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NEWS; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_SIZE; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_NEWS_START; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_NEWS_END; ?></td>
                  <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PUBLISHED; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                </tr>
<?php
  $news_query_raw = "select n.box_news_id, nc.news_title, nc.news_content, n.news_added_date, n.news_modified_date, date_format(n.news_start_date, '%Y-%m-%d') as news_start_date, date_format(n.news_end_date, '%Y-%m-%d') as news_end_date, date_format(n.news_published_date, '%Y-%m-%d') as news_published_date, n.news_status from " . TABLE_BOX_NEWS . " n, " . TABLE_BOX_NEWS_CONTENT . " nc where n.box_news_id = nc.box_news_id and nc.languages_id = '" . (int)$_SESSION['languages_id'] . "' order by news_start_date";
  $news_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $news_query_raw, $news_query_numrows);
  $news = $db->Execute($news_query_raw);
  while (!$news->EOF){
   if((!isset($_GET['nID']) || (isset($_GET['nID']) && ($_GET['nID'] == $news->fields['box_news_id']))) &&   !isset($nInfo) && (substr($action, 0, 3) != 'new')){
     $nInfo = new objectInfo($news->fields);
   }
   if(isset($nInfo) && is_object($nInfo) && ($news->fields['box_news_id'] == $nInfo->box_news_id) ){
     echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page . '&action=preview') . '\'">' . "\n";
   } else {
     echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, '&nID=' . $news->fields['box_news_id'] . $page) . '\'">' . "\n";
   }
   $char_cnt_total = number_format (strlen (strip_tags (ereg_replace("(\r\n|\n|\r)", "", $news->fields['news_title'] . $news->fields['news_content']))));
?>
                  <td class="dataTableContent"><?php echo '<a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'page=' . $_GET['page'] . '&nID=' . $news->fields['box_news_id'] . '&action=preview') . '">' . zen_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . (($news->fields['news_title']) ? $news->fields['news_title'] : '<font color="red">' . ERROR_NEWS_TITLE) . '</font>'; ?></td>
                  <td class="dataTableContent" align="right"><?php echo (($news->fields['news_content']) ? $char_cnt_total . ' bytes' : '<font color="red">' . ERROR_NEWS_CONTENT) . '</font>'; ?></td>
                  <td class="dataTableContent" align="right"><?php echo (($news->fields['news_start_date'] <= date('Y-m-d')) ? '<font color="green">' . zen_date_short($news->fields['news_start_date']) . '<font>' : '<font color="red">' . zen_date_short($news->fields['news_start_date']) . '<font>'); ?></td>
                  <td class="dataTableContent" align="right"><?php echo (($news->fields['news_end_date'] > date('Y-m-d')) ? '<font color="green">' . zen_date_short($news->fields['news_end_date']) . '<font>' : '<font color="red">' . zen_date_short($news->fields['news_end_date']) . '<font>'); ?></td>
                  <td class="dataTableContent" align="center"><?php if($news->fields['news_status'] == '1'){ echo zen_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK); } else { echo zen_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS); } ?></td>
                  <td class="dataTableContent" align="right"><?php if(isset($nInfo) && is_object($nInfo) && ($news->fields['box_news_id'] == $nInfo->box_news_id) ){ echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'page=' . $_GET['page'] . '&nID=' . $news->fields['box_news_id']) . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
                </tr>
<?php
    $news->MoveNext();
    
  }
?>
                <tr>
                  <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td colspan="2" class="smallText" valign="top"><?php echo sprintf (TEXT_NEWS_BOX_MANAGER_INFO, NEWS_BOX_CHAR_COUNT); ?></td>
                    </tr>
                    <tr>
                      <td class="smallText" valign="top"><?php echo $news_split->display_count ($news_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_NEWS); ?></td>
                      <td class="smallText" align="right"><?php echo $news_split->display_links ($news_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                    </tr>
                    <tr>
                      <td align="right" colspan="2"><?php echo '<a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'action=new') . '">' . zen_image_button('button_new_news.gif', IMAGE_NEW_NEWS) . '</a>'; ?></td>
                    </tr>
                  </table></td>
                </tr>
               
              </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($action){
    case 'delete': {
      $heading[] = array('text' => '<b>' . $nInfo->news_title . '</b>');
      $contents = array('form' => zen_draw_form('news', FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_NEWS_DELETE_INFO);
      $contents[] = array('text' => '<br><b>' . $nInfo->news_title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_GET['nID'] . $page) . '">' . zen_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    }
    default: {
      if (is_object ($nInfo)) {
        $char_cnt_title = number_format (strlen (strip_tags (str_replace (array ("\r\n", "\n", "\r"), '', $nInfo->news_title))));
        $char_cnt_content = number_format (strlen (strip_tags (str_replace (array ("\r\n", "\n", "\r"), '', $nInfo->news_content))));
        $heading[] = array('text' => '<b>' . $nInfo->news_title . '</b>');
        if ($nInfo->news_status == 1){
          $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page . '&action=preview') . '">' . zen_image_button('button_preview.gif', IMAGE_PREVIEW) . '</a> <a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page . '&action=publish') . '">' . (($nInfo->news_status) ? zen_image_button('button_unpublish.gif', IMAGE_UNPUBLISH) : zen_image_button('button_publish.gif', IMAGE_PUBLISH)) . '</a>');
        } else {
          $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page . '&action=new') . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page . '&action=delete') . '">' . zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page . '&action=preview') . '">' . zen_image_button('button_preview.gif', IMAGE_PREVIEW) . '</a> <a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, '&nID=' . $nInfo->box_news_id . $page . '&action=publish') . '">' . (($nInfo->news_status) ? zen_image_button('button_unpublish.gif', IMAGE_UNPUBLISH) : zen_image_button('button_publish.gif', IMAGE_PUBLISH)) . '</a>');
        }
        $contents[] = array('text' => '<br>' . TEXT_NEWS_DATE_ADDED . ' ' . zen_date_short($nInfo->news_added_date));
        if (zen_date_short($nInfo->news_modified_date)) $contents[] = array('text' => TEXT_NEWS_DATE_MODIFIED . ' ' . zen_date_short($nInfo->news_modified_date));
        if ($nInfo->news_status == '1') $contents[] = array('text' => TEXT_NEWS_PUBLISHED_DATE . ' ' . zen_date_short($nInfo->news_published_date));
        $contents[] = array('text' => TEXT_NEWS_TITLE . ' ' . $char_cnt_title . ' bytes');
        $contents[] = array('text' => TEXT_NEWS_CONTENT . ' ' . $char_cnt_content . ' bytes');
      }
      break;
    }
  }
  if (zen_not_null ($heading) && zen_not_null ($contents)) {
    $box = new box;
?>
              <td width="25%" valign="top"><?php echo $box->infoBox ($heading, $contents); ?></td>
            </tr>
          </table></td>
        </tr>
<?php
} 
?>
      </table></td>
   <!-- body_text_eof //-->
    </tr>
  </table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>