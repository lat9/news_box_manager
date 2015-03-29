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
  $news = $db->Execute ("SELECT news_content FROM " . TABLE_BOX_NEWS_CONTENT . " WHERE box_news_id = " . (int)$box_news_id . " AND languages_id = " . (int)$language_id . " LIMIT 1");
  
  return ($news->EOF) ? '' : $news->fields['news_content'];
  
}

require('includes/application_top.php');

$languages = zen_get_languages (); 

$action = (isset ($_GET['action']) ? $_GET['action'] : '');
$page_link = (isset ($_GET['page'])) ? ('&page=' . $_GET['page']) : '';
switch ($action) {
  case 'insert':
  case 'update': {
    $news_title = zen_db_prepare_input ($_POST['news_title']);
    $news_content = zen_db_prepare_input ($_POST['news_content']);
    $news_start_date = (($_POST['news_start_date'] == '') ? date ('Y-m-d') : zen_db_prepare_input ($_POST['news_start_date'])) . ' 00:00:00';
    $news_end_date = ($_POST['news_end_date'] == '') ? 'NULL' : (zen_db_prepare_input ($_POST['news_end_date']) . ' 23:59:59');
    if (isset ($_POST['nID'])) {
      $nID = zen_db_prepare_input ($_POST['nID']);
      
    }
    
    // -----
    // For the news article to be saved, it must have both a title and content ** IN AT LEAST ONE OF THE STORE'S LANGUAGES **
    //
    $news_error = array ();
    foreach ($languages as $current_language) {
      $language_id = $current_language['id'];
      if (empty ($news_title[$language_id]) || empty ($news_content[$language_id])) {
        $news_error[$language_id] = true;
   
      }
    }
    if (count ($news_error) != 0 && count ($news_error) == count ($languages)) {
      $action = 'new';
      $messageStack->add (ERROR_NEWS_TITLE_CONTENT, 'error');
      
    } elseif ($news_end_date != 'NULL' && $news_start_date > $news_end_date) {
      $action = 'new';
      $messageStack->add (ERROR_NEWS_DATE_ISSUES, 'error');
      
    } else {
      $sql_data_array = array ('news_start_date' => $news_start_date,
                               'news_end_date' => $news_end_date,

                              );

      if ($action == 'insert') {
        $sql_data_array['news_added_date'] = 'now()';
        $sql_data_array['news_status'] = 0;
        zen_db_perform (TABLE_BOX_NEWS, $sql_data_array);
        $nID = zen_db_insert_id();
         
      } else {
        $sql_data_array['news_modified_date'] = 'now()';
        zen_db_perform (TABLE_BOX_NEWS, $sql_data_array, 'update', "box_news_id = '" . (int)$nID . "'");
         
      }

      foreach ($languages as $current_language) {
        $language_id = $current_language['id'];
        if (zen_not_null ($news_title[$language_id]) && zen_not_null ($news_content[$language_id])) {
          
          $sql_data_array = array ('news_title' => zen_db_prepare_input ($news_title[$language_id]),
                                   'news_content' => zen_db_prepare_input ($news_content[$language_id]));

          if ($action == 'insert') {
            $sql_data_array['box_news_id'] = $nID;
            $sql_data_array['languages_id'] = $language_id;
            zen_db_perform (TABLE_BOX_NEWS_CONTENT, $sql_data_array);
            $change_type = NEWS_ARTICLE_CREATED;
            
          } else {
            zen_db_perform (TABLE_BOX_NEWS_CONTENT, $sql_data_array, 'update', "box_news_id = " . (int)$nID . " AND languages_id = $language_id");
            $change_type = NEWS_ARTICLE_UPDATED;
            
          }
        }
      }
      $messageStack->add_session (sprintf (SUCCESS_NEWS_ARTICLE_CHANGED, $change_type), 'success');
      zen_redirect (zen_href_link (FILENAME_NEWS_BOX_MANAGER, "nID=$nID$page_link"));
     
    }
    break;
    
  }
  case 'deleteconfirm':  {
    $nID = (int)zen_db_prepare_input ($_GET['nID']);
    $db->Execute ("DELETE FROM " . TABLE_BOX_NEWS . " WHERE box_news_id = $nID");
    $db->Execute ("DELETE FROM " . TABLE_BOX_NEWS_CONTENT . " WHERE box_news_id = $nID");
    zen_redirect (zen_href_link (FILENAME_NEWS_BOX_MANAGER, (isset ($_GET['page']) ? ('page=' . $_GET['page']) : '')));
    break;
     
  }
  case 'status': {
    $nID = (int)zen_db_prepare_input ($_GET['nID']);
    $news = $db->Execute ("SELECT news_status FROM " . TABLE_BOX_NEWS . " WHERE box_news_id = $nID LIMIT 1");
    if (!$news->EOF) {
      $news_status = ($news->fields['news_status'] == 0) ? 1 : 0;
      $db->Execute ("UPDATE " . TABLE_BOX_NEWS . " SET news_status = $news_status, news_modified_date = now() WHERE box_news_id = $nID LIMIT 1");

    }
    zen_redirect (zen_href_link (FILENAME_NEWS_BOX_MANAGER, "nID=$nID$page_link"));
    break;
    
  }
  case 'set_editor': {
    // Reset will be done by init_html_editor.php. Now we simply redirect to refresh page properly.
    $params = '';
    $separator = '';
    if (isset ($_GET['nID'])) {
      $params = 'nID=' . (int)$_GET['nID'];
      $separator = '&';
      
    }
    if (isset ($_GET['page'])) {
      $params .= $separator . 'page=' . (int)$_GET['page'];
      
    }
    zen_redirect (zen_href_link (FILENAME_NEWS_BOX_MANAGER, $params));
    break;
    
  }
  default: {
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
<style type="text/css">
<!--
.green { color: green; }
.red { color: red; }
-->
</style>
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
                        'news_end_date' => '');
  $nInfo = new objectInfo ($parameters);
  if (isset ($_GET['nID']) || isset ($_POST['nID'])) {
    $form_action = 'update';
    $nID = (int)(isset ($_POST['nID'])) ? $_POST['nID'] : ((isset ($_GET['nID'])) ? $_GET['nID'] : 0);
    $news = $db->Execute ("SELECT nc.news_title, nc.news_content, date_format(n.news_added_date, '%Y-%m-%d') as news_added_date, date_format(n.news_modified_date, '%Y-%m-%d') as news_modified_date,       date_format(n.news_start_date, '%Y-%m-%d') as news_start_date, date_format(n.news_end_date, '%Y-%m-%d') as news_end_date
                             FROM " . TABLE_BOX_NEWS_CONTENT . " nc, " . TABLE_BOX_NEWS . " n 
                            WHERE n.box_news_id = $nID LIMIT 1");
    if (!$news->EOF) {
      $nInfo->objectInfo ($news->fields);
      
    }
  } elseif (isset ($_POST) && is_array ($_POST)) {              
    $nInfo->objectInfo ($_POST);
   
  }
  if ($nInfo->news_end_date == '0000-00-00') {
    $nInfo->news_end_date = '';
    
  }
?>
        <tr>
          <td><?php echo TEXT_EDIT_INSERT_INFO; ?></td>
        </tr>
        
        <tr>
          <td><?php echo zen_draw_form('news', FILENAME_NEWS_BOX_MANAGER, 'action=' . $form_action . $page_link); if ($form_action == 'update') echo zen_draw_hidden_field ('nID', $nID); ?>
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
              <td class="main"><?php echo TEXT_NEWS_START_DATE; ?><br /><small>(YYYY-MM-DD)</small></td>
              <td class="main"><?php echo zen_draw_separator ('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><script language="javascript">dateNewsStart.writeControl(); dateNewsStart.dateFormat="yyyy-MM-dd";</script></td>
            </tr>
            <tr>
              <td class="main"><?php echo TEXT_NEWS_END_DATE; ?><br /><small>(YYYY-MM-DD)</small></td>
              <td class="main"><?php echo zen_draw_separator ('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><script language="javascript">dateNewsEnd.writeControl(); dateNewsEnd.dateFormat="yyyy-MM-dd";</script></td>
            </tr>
            <tr>
              <td class="main"><?php echo zen_draw_separator ('pixel_trans.gif', '1', '35'); ?></td>
            </tr>
<?php 
  $languages = zen_get_languages();
  $first_language = true;
  foreach ($languages as $current_language){
?>
            <tr>
              <td class="main">
<?php
    echo ($first_language) ? TEXT_NEWS_TITLE : '&nbsp;';
    $first_language = false;
?>
              </td>
              <td class="main"><?php echo zen_image (DIR_WS_CATALOG_LANGUAGES . $current_language['directory'] . '/images/' . $current_language['image'], $current_language['name']) . '&nbsp;' . zen_draw_input_field ('news_title[' . $current_language['id'] . ']', (isset ($news_title[$current_language['id']]) ? stripslashes ($news_title[$current_language['id']]) : zen_get_news_title ($_GET['nID'], $current_language['id'])), zen_set_field_length (TABLE_PRODUCTS_DESCRIPTION, 'products_name')); ?></td>
            </tr>
<?php
  }
?>
            <tr>
              <td colspan="2"><?php echo zen_draw_separator ('pixel_trans.gif', '1', '35'); ?></td>
            </tr>
<?php
  $first_language = true;
  foreach ($languages as $current_language){
?>
            <tr>
              <td class="main" valign="top">
<?php
    echo ($first_language) ? TEXT_NEWS_CONTENT : '&nbsp;';
    $first_language = false;
?>
              </td>
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="main" valign="top"><?php echo zen_image (DIR_WS_CATALOG_LANGUAGES . $current_language['directory'] . '/images/' . $current_language['image'], $current_language['name']); ?>&nbsp;</td>
                  <td class="main"><?php echo zen_draw_textarea_field ('news_content[' . $current_language['id'] . ']', 'soft', '100%', '20', (isset ($news_content[$current_language['id']]) ? stripslashes ($news_content[$current_language['id']]) : zen_get_news_content ($_GET['nID'], $current_language['id'])), 'id="ta' . $current_language['id'] . '"'); ?></td>
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
  $news_title = TEXT_NEWS_TITLE;
  foreach ($languages as $current_language){
?>
        <tr>
          <td class="main" colspan="2"><?php echo $news_title; ?></td>
        </tr>
        <tr>
          <td class="main" colspan="2"><?php echo zen_image (DIR_WS_CATALOG_LANGUAGES . $current_language['directory'] . '/images/' . $current_language['image'], $current_language['name']) . '&nbsp;' .  zen_get_news_title ($_GET['nID'], $current_language['id']); ?></td>
        </tr>
        <tr>
          <td class="main" style="width:<?php echo BOX_WIDTH_LEFT; ?>;">&nbsp;</td>
          <td class="main"><div style="height:100%; width:100%; overflow:visible; border:1px solid #ccc;"><?php echo nl2br (zen_get_news_title ($_GET['nID'], $current_language['id'])) . '<br /><br />' . nl2br (zen_get_news_content ($_GET['nID'], $current_language['id'])); ?></div></td>
          <td class="main" style="width:<?php echo BOX_WIDTH_RIGHT; ?>;">&nbsp;</td>
        </tr>
<?php
    $news_title = '&nbsp;';
    
  }
?>
        <tr>
          <td class="main" colspan="3" align="right"><?php echo '<a href="' . zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_GET['nID']) . $page_link . '">' . zen_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
        </tr>
<?php
} elseif ($action == 'confirm') {
  $nID = (int)zen_db_prepare_input ($_GET['nID']);
  $news = $db->Execute ("SELECT news_title, news_content FROM " . TABLE_BOX_NEWS_CONTENT . " WHERE box_news_id = $nID LIMIT 1");
  $nInfo = new objectInfo ($news->fields);

} else {
?>
        <tr>
          <td class="main" colspan="2"><?php echo TEXT_NEWS_BOX_MANAGER_INFO; ?></td>
        </tr>
        
        <tr>
          <td class="smallText" width="100%" align="right"><?php echo TEXT_EDITOR_INFO . zen_draw_form ('set_editor_form', FILENAME_NEWS_BOX_MANAGER, '', 'get') . '&nbsp;&nbsp;' . zen_draw_pull_down_menu ('reset_editor', $editors_pulldown, $current_editor_key, 'onchange="this.form.submit();"') . zen_hide_session_id() . ((isset ($_GET['nID'])) ? zen_draw_hidden_field ('nID', (int)$_GET['nID']) : '') . ((isset ($_GET['page'])) ? zen_draw_hidden_field ('page', $_GET['page']) : '') . zen_draw_hidden_field ('action', 'set_editor') . '</form>'; ?></td>
        </tr>
        
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr class="dataTableHeadingRow">
                  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NEWS; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_NEWS_START; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_NEWS_END; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_MODIFIED; ?></td>
                  <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                </tr>
<?php
  $news_query_raw = "select n.box_news_id, nc.news_title, nc.news_content, n.news_added_date, n.news_modified_date, date_format(n.news_start_date, '%Y-%m-%d') as news_start_date, date_format(n.news_end_date, '%Y-%m-%d') as news_end_date, n.news_status from " . TABLE_BOX_NEWS . " n, " . TABLE_BOX_NEWS_CONTENT . " nc where n.box_news_id = nc.box_news_id and nc.languages_id = '" . (int)$_SESSION['languages_id'] . "' order by news_start_date DESC";
  $news_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $news_query_raw, $news_query_numrows);
  $news = $db->Execute($news_query_raw);
  while (!$news->EOF){
    if((!isset($_GET['nID']) || (isset($_GET['nID']) && ($_GET['nID'] == $news->fields['box_news_id']))) &&   !isset($nInfo) && (substr($action, 0, 3) != 'new')){
      $nInfo = new objectInfo($news->fields);
    }
    if (isset($nInfo) && is_object($nInfo) && ($news->fields['box_news_id'] == $nInfo->box_news_id) ){
      echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page_link . '&action=preview') . '\'">' . "\n";
     
    } else {
      echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, '&nID=' . $news->fields['box_news_id'] . $page_link) . '\'">' . "\n";
     
    }
    $start_date_class = ($news->fields['news_start_date'] <= date('Y-m-d')) ? 'green' : 'red';
    $end_date_class = ($news->fields['news_end_date'] == '0000-00-00' || $news->fields['news_end_date'] >= date ('Y-m-d')) ? 'green' : 'red';
    $news_end_date = ($news->fields['news_end_date'] == '0000-00-00') ? TEXT_NONE : zen_date_short ($news->fields['news_end_date']);
?>
                  <td class="dataTableContent"><?php echo '<a href="' . zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $news->fields['box_news_id'] . '&action=preview' . $page_link) . '">' . zen_image (DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $news->fields['news_title']; ?></td>
                  <td class="dataTableContent" align="right"><span class="<?php echo $start_date_class; ?>"><?php echo zen_date_short ($news->fields['news_start_date']); ?></span></td>
                  <td class="dataTableContent" align="right"><span class="<?php echo $end_date_class; ?>"><?php echo $news_end_date; ?></span></td>
                  <td class="dateTableContent" align="right"><?php echo (($news->fields['news_modified_date'] == NULL) ? $news->fields['news_date_added'] : $news->fields['news_modified_date']); ?></td>
                  <td class="dataTableContent" align="center">
<?php
    echo zen_draw_form ('setstatus', FILENAME_NEWS_BOX_MANAGER, 'action=status&nID=' . $news->fields['box_news_id'] . $page_link);
    if ($news->fields['news_status'] == 0) {
      $icon_image = 'icon_red_on.gif';
      $icon_title = IMAGE_ICON_STATUS_OFF;
      
    } else {
      $icon_image = 'icon_green_on.gif';
      $icon_title = IMAGE_ICON_STATUS_ON;
      
    }
?>
                      <input type="image" src="<?php echo DIR_WS_IMAGES . $icon_image; ?>" alt="<?php echo $icon_title; ?>" />
                    </form>
                  </td>
                  <td class="dataTableContent" align="right"><?php if (isset ($nInfo) && is_object ($nInfo) && ($news->fields['box_news_id'] == $nInfo->box_news_id) ) { echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $news->fields['box_news_id']) . $page_link . '">' . zen_image (DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
                </tr>
<?php
    $news->MoveNext();
    
  }
?>
                <tr>
                  <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td class="smallText" valign="top"><?php echo $news_split->display_count ($news_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_NEWS); ?></td>
                      <td class="smallText" align="right"><?php echo $news_split->display_links ($news_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                    </tr>
                    <tr>
                      <td align="right" colspan="2"><?php echo '<a href="' . zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'action=new') . '">' . zen_image_button ('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
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
      $contents = array('form' => zen_draw_form('news', FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page_link . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_NEWS_DELETE_INFO);
      $contents[] = array('text' => '<br><b>' . $nInfo->news_title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link(FILENAME_NEWS_BOX_MANAGER, 'nID=' . $_GET['nID'] . $page_link) . '">' . zen_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    }
    default: {
      if (is_object ($nInfo)) {
        $heading[] = array('text' => '<b>' . $nInfo->news_title . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page_link . '&action=new') . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . zen_href_link (FILENAME_NEWS_BOX_MANAGER, 'nID=' . $nInfo->box_news_id . $page_link . '&action=delete') . '">' . zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_NEWS_DATE_ADDED . ' ' . $nInfo->news_added_date);
        if ($nInfo->news_modified_date != NULL) {
          $contents[] = array('text' => TEXT_NEWS_DATE_MODIFIED . ' ' . $nInfo->news_modified_date);
          
        }
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