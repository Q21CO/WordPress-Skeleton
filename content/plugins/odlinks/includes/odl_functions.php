<?php

/*
 * odl_functions.php (eclipse)
 * wordpress plugin open directory project
 * @author Mohammad Forgani
 * @copyright Copyright 2008, Oh Jung-Su
 * @version 1.1.0
 * 2009-10-23 fixed for wp 2.8.5
 * @link http://www.forgani.com
 * last Changes  07/03/2013
 */

//if (!isset($_SESSION)) session_start();
require_once(ABSPATH . ODLSMARTY . '/Smarty.class.php');
require_once(ABSPATH . ODLINC . '/pagerank.php');
require_once(ABSPATH . ODLINC .'/odl_navigation.php');

if(defined('THUMBSHOT_INIT')) return;
define('THUMBSHOT_INIT', true);
require_once(ABSPATH . ODLINC .'/_thumbshots.class.php');
$Thumbshot = new Thumbshot();

class ODLTemplate extends Smarty {
  function ODLTemplate($cache = true, $cache_lifetime = 0){
    global $smarty_template_dir, $smarty_compile_dir, $smarty_cache_dir, $smarty_config_dir;
    $this->Smarty();
    $this->template_dir = $smarty_template_dir;
    $this->compile_dir = $smarty_compile_dir;
    $this->config_dir = $smarty_config_dir;
    $this->cache_dir = $smarty_cache_dir;
    $this->caching = $cache;
    $this->cache_lifetime = $cache_lifetime;
  }
}

function get_image($url)  {
  global $Thumbshot, $smarty_cache_dir;
  $odlinkssettings=get_option('odlinksdata');
  if( $odlinkssettings['thumbshots_access_id'] )  // The class may use it's own preset key
    $Thumbshot->access_key = $odlinkssettings['thumbshots_access_id'];
  $Thumbshot->thumbnails_url = plugins_url('odlinks').'/includes/Smarty/cache/';
  $Thumbshot->thumbnails_path = $smarty_cache_dir . '/';
  $GLOBALS['Thumbshot'] = $Thumbshot;
  if(empty($url)) return;
  if(!function_exists('gd_info'))  // GD is not installed
    return;
  if( strstr( $url, '|http' ) )  {
    $tmpurl = @explode( '|http', $url );
    $url = $tmpurl[0];
  }
  if( preg_match( '~[^(\x00-\x7F)]~', $url ) && function_exists('idna_encode') ) // Non ASCII URL, let's convert it to IDN:
    $idna_url = idna_encode($url);
  $Thumbshot->url = $url;
  $Thumbshot->link_url = isset($tmpurl[1]) ? 'http'.$tmpurl[1] : '';
  $Thumbshot->idna_url = isset($idna_url) ? $idna_url : '';
  $Thumbshot->width = 120;
  $Thumbshot->height = 90;
  $Thumbshot->original_image_size = 'S';
  $Thumbshot->cache_days = 365;
  $Thumbshot->link_noindex = false;
  $Thumbshot->link_nofollow = false;
  $Thumbshot->display_preview = 0;
  $lang = substr( get_locale(), 0, 2); 
  $lang = ($lang == 'ru') ? 'ru' : 'en';  // set Russian language if Russian locale and English language for all other locales.
  $Thumbshot->args['lang'] = $lang; 

  // Get the thumbshot
  return $Thumbshot->get();
}

function getNavigation($id){
  if (isset($id)){
    list($navigationLinks, $addurl, $desc)=odl_create_navigation($id,"","",""); // odl_main.php
    $navigationLinks=trim($navigationLinks);
    $last=$navigationLinks{strlen($navigationLinks)-1};
    if (!strcmp($last,":")){
      $navigationLinks=rtrim($navigationLinks, ':');
    }
    return array($navigationLinks, $addurl, $desc);
  }
  return;
}

function odlinksget_wp_mainversion(){
  global $odlinkswp_mainversion;
  if ($odlinkswp_mainversion==false){
    odlinksget_namefield();
  }
  return $odlinkswp_mainversion;
}

function odlinksget_pageinfo(){
  global $wpdb, $odlinkswp_pageinfo, $table_prefix;

  if ($odlinkswp_pageinfo==false){
    $odlinkswp_pageinfo = $wpdb->get_row("SELECT * FROM {$table_prefix}posts WHERE post_title = '[[ODLINKS]]'", ARRAY_A);
  }
  return $odlinkswp_pageinfo;
}

function odlinkscreate_page(){
  global $wpdb, $table_prefix, $wp_version;
  $dt = date("Y-m-d");
  $wpdb->query("INSERT INTO {$table_prefix}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type) VALUES ('1', '$dt', '$dt', '[[ODLINKS]]', '[[ODLINKS]]',  '[[ODLINKS]]', 'publish', 'closed', 'closed', '', 'odlinks', '', '', '$dt', '$dt', '[[ODLINKS]]', '0', '', '0', 'page')");

  return $wpdb->get_row("SELECT * FROM {$table_prefix}posts WHERE post_title = '[[ODLINKS]]'", ARRAY_A);
}


function odlinksrewrite_rules_wp($wp_rewrite){
  global $wp_rewrite;
  $odlinkssettings = get_option('odlinksdata');
  $odlinksslug = $odlinkssettings['odlinksslug'];
  $odlinksrules = array(
  $odlinksslug.'/([^/\(\)]*)/?([^/\(\)]*)/?([^/\(\)]*)/?' => '/'.$odlinksslug.'/index.php?pagename='.$odlinksslug.'&_action=$matches[1]&id=$matches[2]&parent=$matches[3]');
  $wp_rewrite->rules = $odlinksrules + $wp_rewrite->rules;
}

function odlinksquery_vars($vars){
  $vars[] = '_action';
  $vars[] = 'id';
  $vars[] = 'orderby';
  $vars[] = 'who';
  return $vars;
}

function odlinks_excerpt_text($length, $text){
  $text = strip_tags(odlinkscreate_post_html($text));
  if(strlen($text)>$length){
    $ret_strpos = strpos($text, ' ', $length);
    $ret = substr($text, 0, $ret_strpos)." ...";
  }else{
    $ret = $text;
  }
  return $ret;
}

function odlinkspage_handle_title($title){
  global $odl_breadcrumbs;
  if ($odl_breadcrumbs==""){
    $sidebar = 0;
    $odl_breadcrumbs = odlinksget_breadcrumbs($sidebar);
  }
  return str_replace("[[ODLINKS]]", $odl_breadcrumbs, $title);
}

function odlinkspage_handle_pagetitle($title){
  global $odl_pagetitle;
  return str_replace("[[ODLINKS]]", "ODLinks  &raquo; ", $title);
}

function odlinkspage_handle_content($content){
  if (preg_match('/\[\[ODLINKS\]\]/', $content)){
    odlinksprocess();
    return "";
  } else {
    return $content;
  }
}

function odlinkspage_handle_titlechange($title){
  global $odl_breadcrumbs;
  $sidebar = 0;
  $odl_breadcrumbs = odlinksget_breadcrumbs($sidebar);
  $odlinkssettings = get_option('odlinksdata');
  $title = str_replace($odl_breadcrumbs, $odlinkssettings["page_link_title"], $title);
  $title = str_replace("[[ODLINKS]]", $odlinkssettings["page_link_title"], $title);
  return $title;
}

function odlinksget_breadcrumbs($sidebar){
  global $_GET, $_POST, $_SERVER, $wp_version;
  $g__action = get_query_var("_action");
  $id = get_query_var("id");
  $f = get_query_var("parent");
  if (basename($_SERVER['PHP_SELF'])!='index.php'){
    return "[[ODLINKS]]";
  } else {
    $odlinkssettings = get_option('odlinksdata');
    if (!isset($_POST['search_terms']) && $sidebar=0) {
      $g__action = "sidebar";
    } elseif (!isset($_POST['search_terms'])) {
      $g__action = $g__action;
    } else {
      $g__action = "search";
    }
    switch ($g__action){
      default:
      case "index":
        //return '<strong class="odl_breadcrumb">'.$odlinkssettings['page_link_title'].'</strong>';
        return $odlinkssettings['page_link_title'];
        break;
    }
  }
}

function odlinkscreate_link($action, $vars){
  global $wp_rewrite;
  $odlinkssettings = get_option('odlinksdata');
  $pageinfo = odlinksget_pageinfo();
  if($wp_rewrite->using_permalinks()) $delim = "?";
  else $delim = "&amp;";
  $perm = get_permalink($pageinfo['ID']);
  $main_link = $perm.$delim;
  if ( isset($vars['name']) ) {
    $odl_vars_name = $vars['name'];
    $odl_vars_name = preg_replace('/[^A-Za-z0-9\s\.]/', "", $odl_vars_name);
    $odl_vars_name = preg_replace('/\s/', '-', $odl_vars_name);
    $odl_vars_name = preg_replace('/\./', '-', $odl_vars_name);
  } else $vars['name'] = '';

  $name = trim($vars['name']);
  
  switch ($action){
    case "topLink":
      return $main_link;
      break;
    case "index":
      return "<a href=\"".$main_link."_action=index\">".$name."</a>";
      break;
    case "category":
      if (!empty($vars['id']) && is_numeric($vars['id'])) {
        if (!empty($vars['parent']) && (int)$vars['parent'] > 0)
          return "<a href=\"".$main_link."_action=main&id=".(int)$vars["id"]."&amp;parent=".(int)$vars['parent']."\">".$name."</a>";
      else
        return "<a href=\"".$main_link."_action=main&id=".(int)$vars["id"]."\">".$name."</a>";
      } else {
        return "<a href=\"".$main_link."_action=index\">".$name."</a>";
      }
      break;
    case "postlink":
    if (!empty($vars['id']) && is_numeric($vars['id'])) {
      if (!empty($vars['parent']) && (int)$vars['parent'] > 0)
        return "<a href=\"".$main_link."_action=postlink&id=".(int)$vars["id"]."&amp;parent=".(int)$vars['parent'] ."\">".$name."</a>";
      else
        return "<a href=\"".$main_link."_action=postlink&id=".(int)$vars["id"]."\">".$name."</a>";
      } 
      break;
    case "editlink":
      if (!empty($vars['id']) && is_numeric($vars['id'])) {
        return "<a style=\"color:green\" href=\"".$main_link."_action=editlink&amp;id=".(int)$vars["id"]."\">".$name."</a>";
      } 
      break;
    case "deletelink":
      if (!empty($vars['id']) && is_numeric($vars['id'])) {
        return "<a style=\"color:green\" href=\"".$main_link."_action=deletelink&amp;id=".(int)$vars["id"]."\">".$name."</a>";
      } 
      break;
    case "searchlink":
      return "<a href=\"".$main_link."_action=searchlink" ."\">".$name."</a>";
      break;
    case "searchform":
      return $main_link."_action=searchlink";
      break;
    case "sendform":
      return $main_link."_action=sendlink";
      break;
    case "sendlink":
      if (!empty($vars['id']) && is_numeric($vars['id'])) {
        return "<a style=\"color:green\" href=\"".$main_link."_action=sendlink&amp;id=".(int)$vars["id"]."\">".$name."</a>";
      } 
      break;
  }
}

function odlinksprocess(){
  global $_GET, $_POST, $wp_version;
  if (!isset($msg)) $msg='';
  if (!isset($confirm)) $confirm='';
  $action = get_query_var("_action");
  $odlinkssettings = get_option('odlinksdata');
  switch ($action){
    default:
    case "main":
      odlinksdisplay_index($msg);
      break;
    case "searchlink":
      odlinksdisplay_search();
      break;
    case "postlink":
      if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
        odlinkspost_link($confirm);
      } else {
        odlinksdisplay_index(404);
      }
      break;
    case "sendlink":
    if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
        odlinkssend_link($confirm);
      } else {
        odlinksdisplay_index(404);
      }
      break;
    case "editlink":
    if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
        odlinksedit_link($confirm);
      } else {
        odlinksdisplay_index(404);
      }
      break;
    case "deletelink":
    if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
        odlinksdelete_link($confirm);
      } else {
        odlinksdisplay_index(404);
      }
      break;
    case "install";
    echo "Please install ODLinks by saving the Settings in the ODLinks Admin area.";
    break;
  }
}

function getOdlinks($results, $tpl, $page = 1) {
  global $odl_lang, $current_user, $user_level;
  $odlinkssettings=get_option('odlinksdata');

  if(!isset($page) || $page*1 < 1) $page=1;
  $page_navigation = new odl_navigation();
  $page_navigation->nr_rows = $odlinkssettings['odlinks_num_links'];

  if(empty($results)) {
    $tpl->assign('odl_search_error', $odl_lang['ODL_NOPOSTSEARCH']);
    return;
  }
  $links=array();
  $cnt = 0;
  $counter = 0;
  for($i = count($results) - 1;$i >= 0;$i--){ # recusive  ($i=0;$i<count($results);$i++){
    $cnt++;
    $counter++;
    if (($cnt < $page * $page_navigation->nr_rows) and ($cnt >= ($page-1) * $page_navigation->nr_rows)) {
      $result=$results[$i];
      $rankImage=false;
      if (isset($result->l_url) && $result->l_url != '') {
        $Url=trim($result->l_url);
        if (($Url!='') AND ($Url!='http://')) {
          if (isUrlValid($Url)) {
            $url = parse_url($Url, PHP_URL_HOST);
            $PageRank = ODLPagerank::getRank($url);
            if (!isset($PageRank) || PageRank == '' || $PageRank < 0) $PageRank='0';
            $rankImage='pr'.$PageRank.".gif";
            $rankText= $odl_lang['ODL_PAGERANK'] . $PageRank .'/10';
          }
        }
        $description = $result->l_description;
        $t = nl2br($description);
        $string = substr($t, 0, $odlinkssettings['odlinks_excerpt_length']);
        if ($odlinkssettings['odlinks_dispaly_html']) 
          $txt = $string;
        else
          $txt = odl_html2text($string);
        if (isset($current_user->user_login) && !empty($current_user->user_login) && $user_level > 8) {
           $editurl=odlinkscreate_link("editlink", array("name"=>'Edit', "id"=>$result->l_id));
           $deleteurl=odlinkscreate_link("deletelink", array("name"=>'Delete', "id"=>$result->l_id));
        }
        $sendlinkurl=odlinkscreate_link("sendlink", array("name"=>$odl_lang['ODL_SENDTOF'], "id"=>$result->l_id));
        $img = get_image($Url);
        $links[]=array('title'=>$result->l_title, 'url'=>$Url, 'date'=>$result->l_date, 'description'=>$txt, 'img'=>$img, 'sendlink'=>$sendlinkurl, 'deletelink'=>$deleteurl, 'editlink'=>$editurl, 'rank_img'=>$rankImage, 'rank_txt'=>$rankText); 
      }
    }
  }
  return array($links,$page_navigation,$counter);
}



function lastODLinksSidebar($num=10){
  global $wpdb;
  $tp = $wpdb->prefix;
  $sql="SELECT * FROM {$tp}odlinks l, {$tp}odcategories c 
    WHERE l.l_c_id = c.c_id AND l.l_hide='visible' 
    ORDER BY l.l_date DESC, l.l_title DESC LIMIT 0, ". $num;
  $lastAds=$wpdb->get_results($sql);
  
  for ($l=0; $l<count($lastAds); $l++){
    $result=$lastAds[$l];
    $titleLink = odl_create_link_by_title($result->c_title);
    $res = substr($result->l_title, 0, 15) . '...';
    echo '<li>' . $titleLink . ' ' . $res . '</li>';
  }
  echo '</ul></div>';
}

?>
