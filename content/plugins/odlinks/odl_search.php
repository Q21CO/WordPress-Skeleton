<?php

/*
 * odl_search.php
 * wordpress plugin website directory project
 * @author Mohammad Forgani
 * @copyright Copyright 2008, Oh Jung-Su
 * @version 0.2
 * @link http://www.forgani.com
 * last Changes  07/03/2013
 */


function odlinksdisplay_search(){
  global $_POST, $table_prefix, $wpdb, $odl_lang;
   
  $odlinkssettings = get_option('odlinksdata');
  $page = get_query_var("page");
  
  $tpl = new ODLTemplate();
  $tpl->assign('lang', $odl_lang);
  $tpl->assign('odl_images', get_bloginfo('wpurl') . "/wp-content/plugins/odlinks");
  $odl_main_link = odlinkscreate_link("index", array("name"=>"Main"));
  $results_limit = 10;
  $tpl->assign('odl_lang', $odl_lang);
  $tpl->assign('results_limit', $results_limit);
  $tpl->assign('odl_main_link', $odl_main_link); 
  $tpl->assign('odl_wpurl', get_bloginfo('url'));
  $type = $_POST['type'];
  $search_terms =  stripslashes($_POST['search_terms']);
  $search_terms = addslashes(htmlspecialchars($search_terms));
  $links=array();
  $tpl->assign('search_terms',$search_terms);
  if(empty($search_terms)){
    $tpl->assign('odl_search_error', $odl_lang['ODL_EMPTYSEARCH']);
  } else {
    if($type == "links"){
      $sql = "SELECT * FROM {$table_prefix}odlinks WHERE l_url like '%".$search_terms."%'"; 
      $results=$wpdb->get_results($sql);
      list ($links,$page_navigation, $counter) = getOdlinks($results, $tpl, $page);
    } elseif($type == "desc"){
      $sql = "SELECT * FROM {$table_prefix}odlinks WHERE l_title like '%".$search_terms."%' OR l_description like '%".$search_terms."%'"; 
      $results = $wpdb->get_results($sql);
	  list ($links,$page_navigation, $counter) = getOdlinks($results, $tpl, $page);
    } else {
      $sql = "SELECT * FROM {$table_prefix}odlinks WHERE l_description like '%". $search_terms."%' OR l_url like '%" . $search_terms. "%' OR l_title like '%" . $search_terms . "%'";
      $results = $wpdb->get_results($sql);
      list ($links, $page_navigation, $counter) = getOdlinks($results, $tpl, $page);
    }
  }
  
  $tpl->assign('results_num', count($results));

  $result=$wpdb->get_results("SELECT * FROM {$table_prefix}odcategories"); 
  $tpl->assign('categories_total', count($result)); 
  $results=$wpdb->get_results("SELECT * FROM {$table_prefix}odlinks WHERE l_hide ='visible'");
  $tpl->assign("links_total", count($results)); 

  $page_navigation->all=$counter;
  if ($counter > $page_navigation->nr_rows) {
    if (!empty($_GET['parent']) && (int)$_GET['parent'] > 0)
      $page_navigation->params="_action=main&id=".$_GET['id']."&parent=".$_GET['parent'];
    else
      $page_navigation->params="_action=main&id=".$_GET['id'];
    list ($current_page, $navi) = $page_navigation->get_navigation($page);
    $p = '<span class="title">Page '. $current_page. ' of '. $page_navigation->nr_rows . '</span>' . $navi;
  }
  if (count($results) > 0) {
    $tpl->assign('links', $links);
    $tpl->assign('navigation_page', $p);
  }
	  
  odlinks_get_footer($tpl);
  $tpl->display('search.tpl');
}




?>
