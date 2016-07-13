<?php

/*
 * odl_main.php
 * @author Mohammad Forgani
 * wordpress plugin website directory project
 * @copyright Copyright 2008, Oh Jung-Su
 * @version 1.1.2-d
 * 2009-10-23 fixed for wp 2.8.5
 * @link http://www.forgani.com
 * last Changes  07/03/2013
*/


function odlinksdisplay_index($msg, $cInput = 0){
  global $_GET, $_POST, $table_prefix, $wpdb, $PHP_SELF, $odl_lang, $current_user, $user_level;
  $odlinkssettings=get_option('odlinksdata');
  
  $page = get_query_var("page");
  odl_cleanUp(); // FIXME please check!

  $tpl=new ODLTemplate();
  $tpl->assign('odl_images', plugins_url('odlinks'));
  $tpl->assign('odl_lang', $odl_lang);
  $odl_search_link=odlinkscreate_link("searchform", array());
  $tpl->assign('odl_search_link', $odl_search_link);
  
  $tpl->assign('odl_wpurl', plugins_url('odlinks'));
  
  if ( (isset($_GET['id']) && !is_numeric($_GET['id'])) || 
       (isset($_GET['parent']) && !is_numeric($_GET['parent'])) ) {
    $msg=404;
  } else {
    $id = 0;
    $id=(int)$_GET['id'];
    $tpl->assign('cat_id',$id);
  }

  if ($msg==404) 
    $msg="<h2><font color=\"#800000\">Oops, 404: Page not found!</font></h2>";
  if ($msg) $tpl->assign('error',$msg);
  

  list($navigationLinks, $addurl, $desc) = getNavigation($id);
  if (!isset($navigationLinks) || $navigationLinks == '') list($navigationLinks, $addurl, $desc) = getNavigation($cInput);
  $tpl->assign('addurl_link', $addurl); 
  $tpl->assign('navigation_link', $navigationLinks);
  $tpl->assign('odlinks_dispaly_html', $odlinkssettings['odlinks_dispaly_html']);
  $tpl->assign('odl_navigation_description', $desc);
  $sql="SELECT * FROM {$table_prefix}odlinks WHERE l_c_id='".$id."' AND l_hide ='visible'";
  $results=$wpdb->get_results($sql);
  list ($links,$page_navigation, $counter) = getOdlinks($results, $tpl, $page);
  
  if(isset($page_navigation))
    $page_navigation->all=$counter;
  if ($counter > $page_navigation->nr_rows) {
    if ( !empty($_GET['parent']) && is_numeric($_GET['parent']) ) 
      $page_navigation->params="_action=main&id=".$id."&parent=".$_GET['parent'];
    else
      $page_navigation->params="_action=main&id=".$id;
    list ($current_page, $navi) = $page_navigation->get_navigation($page);
    $c = ceil($counter / $page_navigation->nr_rows);
    $p = '<span class="title">Page '. $current_page. ' of '. $c . '</span>' . $navi;
  }

  if (count($results) > 0) {
    $tpl->assign('links', $links);
    $tpl->assign('navigation_page', $p);
  }
  $tpl->assign("links_total", number_format(odl_total_links(0)));
  $tpl->assign("page", $page);
  
  $result_cats=$wpdb->get_results("SELECT * FROM {$table_prefix}odcategories ORDER BY c_title ASC");
  $sub_cats=array();
  $cats=array();
  $c_total=count($result_cats);
  if ($c_total*1 > 0) {
    foreach ($result_cats as $result_cat) { 
    if($result_cat->c_parent == $id){
      $result_cat->c_links=odl_total_links($result_cat->c_id);
      $title=trim($result_cat->c_title);
      $odl_category_link=odlinkscreate_link("category", array("name"=>$title, "id"=>$result_cat->c_id, "parent"=>$result_cat->c_parent));
      $cats[]=array(
        'c_id'=>$result_cat->c_id,
        'c_title'=>$title,
        'c_links'=>$result_cat->c_links ,
        'cat_link'=>$odl_category_link);
      $sql="SELECT * FROM {$table_prefix}odcategories WHERE c_parent=".$result_cat->c_id." ORDER BY c_title ASC";
      $result_subs=$wpdb->get_results($sql);
      if (!empty($result_subs)){
        $no=0;
        foreach ($result_subs as $result_sub) {  
          $no++;
          $title=trim($result_sub->c_title);
          $subcategory_link=odlinkscreate_link("category", array("name"=>$title, "id"=>$result_sub->c_id,"parent"=>$result_sub->c_parent));
          $lnk=odl_total_links($result_sub->c_id);
          if ( !($result_sub == end($result_subs)) ) $subcategory_link=$subcategory_link;
          $sub_cats[]=array (
            'c_parent'=>$result_sub->c_parent,
            'c_title'=>$title,
            'c_links'=>$lnk,
            'c_path'=>$subcategory_link,
            'c_no'=>$no);
          };
        }
      }
    }
  }
  $tpl->assign('categories_total', number_format($c_total));
  $tpl->assign('categories',$cats);
  $tpl->assign('subcategories',$sub_cats);
  odlinks_get_footer($tpl);
  include_once (ODL_PLUGIN_DIR . '/includes/odl_rss.php');
  return $tpl->display('body.tpl');
}


function odl_create_link_by_title($title){
  global $table_prefix, $wpdb, $odl_lang;
  $sql="SELECT * FROM {$table_prefix}odcategories WHERE c_title='$title' ";
  $result=$wpdb->get_row($sql, ARRAY_A);
  $name=trim($result['c_title']);
  $odl_link=odlinkscreate_link("category", array('name'=>$name, 'id'=>$result['c_id'], 'parent'=>$result['c_parent']));
  return $odl_link;
}

/*
 * Feel free to make and change your own style on jCarousel in the footer
 * note: update/upgrade the plugin will overwrite your changes. 
 *       saving your data when upgrading
*/
function odlinks_get_footer($tpl) {
  global $wpdb, $table_prefix, $odl_lang, $odlinksversion, $smarty_cache_dir;
?> 
<script type="text/javascript" src="<?php echo plugins_url('odlinks') ?>/jcarousel/lib/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo plugins_url('odlinks') ?>/jcarousel/lib/jquery.jcarousel.min.js"></script>

<style type="text/css">
.jcarousel-skin .jcarousel-container {-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;border:1px solid #eee;}
.jcarousel-skin .jcarousel-direction-rtl {direction:rtl;}
.jcarousel-skin .jcarousel-container-horizontal {width:685px;padding:10px 20px;}
.jcarousel-skin .jcarousel-clip {overflow: hidden;}
.jcarousel-skin .jcarousel-clip-horizontal {width:685px;height:100px;}
.jcarousel-skin .jcarousel-item-horizontal {margin-left:0;margin-right:10px;}
.jcarousel-skin .jcarousel-direction-rtl .jcarousel-item-horizontal {margin-left:10px;margin-right:0;}
.jcarousel-skin .jcarousel-item-placeholder {background:#fff;color:#000;}
.jcarousel-skin {font-family:arial;color:#777;font-size:xx-small;}
</style>

<script type="text/javascript">
    <!--
    function pop(file,name){
    rsswindow = window.open (file,name,"location=1,status=1,scrollbars=1,width=680,height=800");
    rsswindow.moveTo(0,0);
    rsswindow.focus();
    return false;
    }
    
    function mycarousel_initCallback(carousel){
      carousel.clip.hover(function() {
        carousel.stopAuto();
      }, function() {
        carousel.startAuto();
      });
    };
    jQuery(document).ready(function() {
      jQuery('#mycarousel').jcarousel({
        visible: 5,
        auto: 2,
        wrap: 'last',
        initCallback: mycarousel_initCallback
      });
    });
    //  end script hiding -->
</script> 
<?php

  $odlinkssettings=get_option('odlinksdata');
  if ( strlen($odlinkssettings['odlinks_top_image']) > 3 ) {
    $img = plugins_url('odlinks') . '/images/' . $odlinkssettings['odlinks_top_image'];
    $tpl->assign('top_image', '<img src="' . $img . '">' );
    $tpl->assign('top_imageII', $img );
  }
  $odl_top_link=odlinkscreate_link("topLink", '');
  $tpl->assign('odl_top_link', $odl_top_link);
  $odl_main_link=odlinkscreate_link("index", array("name"=>"TOP"));
  $tpl->assign('odl_main_link', $odl_main_link);
  $tpl->assign('odl_top_description', $odlinkssettings['description']);

  // footer
  if (!$odlinkssettings['odlinks_last_links_num']) $odlinkssettings['odlinks_last_links_num'] = 8;
  $start=0;
  $tpl->assign("linksNum", $odlinkssettings['odlinks_last_links_num']);
  $sql="SELECT * FROM {$table_prefix}odlinks l, {$table_prefix}odcategories c WHERE l.l_c_id = c.c_id AND l.l_hide='visible' ORDER BY l.l_date DESC, l.l_title DESC LIMIT ".($start).", ".($odlinkssettings['odlinks_last_links_num']);
  $lastAds=$wpdb->get_results($sql);
  $new_links=array();
  for ($l=0;$l<count($lastAds);$l++){
    $result=$lastAds[$l];
    $titleLink=odl_create_link_by_title($result->c_title);
    $new_links[]=array ('date'=>$result->l_date, 'title'=>$result->l_title, 'url'=>$result->l_url, 'description'=>$result->l_description, 'category'=>$titleLink);
  }
  $tpl->assign('new_links', $new_links);
  $tpl->assign("linksNum", $odlinkssettings['odlinks_last_links_num']);
  
  $myCarousel='';
  foreach (odl_listFiles($smarty_cache_dir) as $key=>$file){
    $img = plugins_url('odlinks') . '/includes/Smarty/cache/' . $file;
    $myCarousel .='<li><img border=1 src="'.$img.'" /></li>';
  }
  $tpl->assign('mycarousel', '<div id="wrap"><ul id="mycarousel" class="jcarousel-skin">'.$myCarousel.'</ul></div>');

  $filename = ODL_PLUGIN_URL . '/includes/Smarty/cache/odlinks.xml';

  $rssLink = '<div class="odl_footer"><img src="' . ODL_PLUGIN_URL . '/images/rss.png" />';
  $rssLink .= '<b><a href="'. $filename . '" target="_blank" onclick="return pop('.$filename.',' .  $odlinkssettings['odlinksslug'] . ');">' . $odlinkssettings['page_link_title'] . ' RSS </a></b><br />';
  $odl_top_link=odlinkscreate_link("index", array("name"=>$odlinkssettings['page_link_title']));

  if ($odlinkssettings['odlinksshow_credits'] == 'y') {
      $credit='Open Directory Links Powered By <a href="http://www.forgani.com/" target="_blank">4gani</a>';
      $rssLink .= '<span class="smallTxt">&nbsp;' . $credit . ' (v. '.$odlinksversion.', 2009. All rights reserved.)</span>';
   } 
   
  $rssLink .= '</div>';
  $odlFbLike = odlFbLike();
  $tpl->assign('odlFbLike', $odlFbLike);
  $tpl->assign('rssLink', $rssLink);

}


function odl_create_navigation($id, $links, $addurl, $desc){
  global $table_prefix, $wpdb, $odl_lang, $tpl;
  $again='';
  $sql="SELECT * FROM {$table_prefix}odcategories WHERE c_id=".$id;
  $result=$wpdb->get_results($sql);
  for ($i=0;$i<count($result);$i++){
    $row=$result[$i];
    $name=trim($row->c_title);
    $odl_link=odlinkscreate_link("category", array('name'=>$name, 'id'=>$row->c_id, 'parent'=>$row->c_parent));
    if (isset($odl_link) && !empty($odl_link))
      $links=$odl_link . ": " . $links;
    $addurl=odlinkscreate_link("postlink", array('name'=>$odl_lang['ODL_SUBMITSITE'], 'id'=>$row->c_id, "parent"=>$row->c_parent));
    if (isset($row->c_description) && !empty($row->c_description))
      $description = $desc . '<br />' . $row->c_description;
    $again=odl_create_navigation($row->c_parent, $links, $addurl, $description);
  }
   if ($id <> "0") {
    return $again;
  } else {
    $out=array($links, $addurl, $desc);
    return $out;
  }
}

function odl_total_links($cat_id){
  global $table_prefix, $odl_lang, $wpdb;
  $out="";
  $all_cats=odl_get_categories($cat_id);
  for($a=0;$a<=count($all_cats)-1;$a++){
    $out .= $all_cats[$a].",";
  }
  $sql="SELECT COUNT(l_id) as count FROM {$table_prefix}odlinks WHERE l_hide='visible' AND l_c_id IN (".trim($out,',').")";
  $result=$wpdb->get_row($sql, ARRAY_A);
  return $result['count'];
}

function odl_get_categories($parent) {
  global $table_prefix, $wpdb;
  $sql="SELECT c_id FROM {$table_prefix}odcategories WHERE c_parent='$parent'";
  $result=$wpdb->get_results($sql);
  $arr[]=$parent;
  for ($x=0;$x<count($result);$x++){
    $row=$result[$x];
    $arr=odl_combine_arrays($arr, odl_get_categories($row->c_id));
  }
   return $arr;
}

function odl_combine_arrays($arr1, $arr2) {
  foreach ($arr2 as $elem) {
    $arr1[]=$elem;
  }
  return $arr1;
}


function odl_html2text( $badStr ) {
  //remove PHP if it exists
  while( substr_count( $badStr, '<'.'?' ) && substr_count( $badStr, '?'.'>' ) && strpos( $badStr, '?'.'>', strpos( $badStr, '<'.'?' ) ) > strpos( $badStr, '<'.'?' ) ) {
    $badStr=substr( $badStr, 0, strpos( $badStr, '<'.'?' ) ) . substr( $badStr, strpos( $badStr, '?'.'>', strpos( $badStr, '<'.'?' ) ) + 2 );}
  //remove comments
  while( substr_count( $badStr, '<!--' ) && substr_count( $badStr, '-->' ) && strpos( $badStr, '-->', strpos( $badStr, '<!--' ) ) > strpos( $badStr, '<!--' ) ) {
    $badStr=substr( $badStr, 0, strpos( $badStr, '<!--' ) ) . substr( $badStr, strpos( $badStr, '-->', strpos( $badStr, '<!--' ) ) + 3 );}
  //now make sure all HTML tags are correctly written (> not in between quotes)
  for( $x=0, $goodStr='', $is_open_tb=false, $is_open_sq=false, $is_open_dq=false;strlen( $chr=$badStr{$x} );$x++ ) {
    //take each letter in turn and check if that character is permitted there
    switch( $chr ) {
      case '<':
        if( !$is_open_tb && strtolower( substr( $badStr, $x + 1, 5 ) ) == 'style' ) {
          $badStr=substr( $badStr, 0, $x ) . substr( $badStr, strpos( strtolower( $badStr ), '</style>', $x ) + 7 );$chr='';
        } elseif( !$is_open_tb && strtolower( substr( $badStr, $x + 1, 6 ) ) == 'script' ) {
          $badStr=substr( $badStr, 0, $x ) . substr( $badStr, strpos( strtolower( $badStr ), '</script>', $x ) + 8 );$chr='';
        } elseif( !$is_open_tb ) { $is_open_tb=true;} else { $chr='&lt;';}
          break;
     case '>':
       if( !$is_open_tb || $is_open_dq || $is_open_sq ) { $chr='&gt;';} else { $is_open_tb=false;}
         break;
     case '"':
       if( $is_open_tb && !$is_open_dq && !$is_open_sq ) { $is_open_dq=true;}
         elseif( $is_open_tb && $is_open_dq && !$is_open_sq ) { $is_open_dq=false;}
         else { $chr='&quot;';}
         break;
     case "'":
       if( $is_open_tb && !$is_open_dq && !$is_open_sq ) { $is_open_sq=true;}
       elseif( $is_open_tb && !$is_open_dq && $is_open_sq ) { $is_open_sq=false;}
    } $goodStr .= $chr;
  }
  //now that the page is valid (I hope) for strip_tags, strip all unwanted tags
  $goodStr=strip_tags( $goodStr, '<title><hr><h1><h2><h3><h4><h5><h6><div><p><pre><sup><ul><ol><br><dl><dt><table><caption><tr><li><dd><th><td><a><area><img><form><input><textarea><button><select><option>' );
  //strip extra whitespace except between <pre> and <textarea> tags
  $badStr=preg_split( "/<\/?pre[^>]*>/i", $goodStr );
  for( $x=0;is_string( $badStr[$x] );$x++ ) {
    if( $x % 2 ) { $badStr[$x]='<pre>'.$badStr[$x].'</pre>';} 
    else {
      $goodStr=preg_split( "/<\/?textarea[^>]*>/i", $badStr[$x] );
      for( $z=0;is_string( $goodStr[$z] );$z++ ) {
        if( $z % 2 ) { $goodStr[$z]='<textarea>'.$goodStr[$z].'</textarea>';} 
        else { $goodStr[$z]=preg_replace( "/\s+/", ' ', $goodStr[$z] );} 
      }
      $badStr[$x]=implode('',$goodStr);
    } 
  }
  $goodStr=implode('',$badStr);
  //remove all options from select inputs
  $goodStr=preg_replace( "/<option[^>]*>[^<]*/i", '', $goodStr );
  //replace all tags with their text equivalents
  $goodStr=preg_replace( "/<(\/title|hr)[^>]*>/i", "\n-----------------------------------------------\n", $goodStr );
  $goodStr=preg_replace( "/<(h|div|p)[^>]*>/i", "\n\n", $goodStr );
  $goodStr=preg_replace( "/<sup[^>]*>/i", '^', $goodStr );
  $goodStr=preg_replace( "/<(ul|ol|dl|dt|table|caption|\/textarea|tr[^>]*>\s*<(td|th))[^>]*>/i", "\n", $goodStr );
  $goodStr=preg_replace( "/<li[^>]*>/i", "\n· ", $goodStr );
  $goodStr=preg_replace( "/<dd[^>]*>/i", "\n\t", $goodStr );
  $goodStr=preg_replace( "/<(th|td)[^>]*>/i", "\t", $goodStr );
  $goodStr=preg_replace( "/<a[^>]* href=(\"((?!\"|#|javascript:)[^\"#]*)(\"|#)|'((?!'|#|javascript:)[^'#]*)('|#)|((?!'|\"|>|#|javascript:)[^#\"'> ]*))[^>]*>/i", "[LINK: $2$4$6] ", $goodStr );
  $goodStr=preg_replace( "/<img[^>]* alt=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "[IMAGE: $2$3$4] ", $goodStr );
  $goodStr=preg_replace( "/<form[^>]* action=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "\n[FORM: $2$3$4] ", $goodStr );
  $goodStr=preg_replace( "/<(input|textarea|button|select)[^>]*>/i", "[INPUT] ", $goodStr );
  //strip all remaining tags (mostly closing tags)
  //$goodStr=strip_tags( $goodStr );
  //convert HTML entities
  $goodStr=strtr( $goodStr, array_flip( get_html_translation_table( HTML_ENTITIES ) ) );
  preg_replace( "/&#(\d+);/me", "chr('$1')", $goodStr );
      
  //wordwrap
  $goodStr=wordwrap( $goodStr );
  
  //make sure there are no more than 3 linebreaks in a row and trim whitespace
  return preg_replace( "/^\n*|\n*$/", '', preg_replace( "/[ \t]+(\n|$)/", "$1", preg_replace( "/\n(\s*\n){2}/", "\n\n\n", preg_replace( "/\r\n?|\f/", "\n", str_replace( chr(160), ' ', $goodStr ) ) ) ) );
}

function isUrlValid($Url) {
  return (strpos(strtolower($Url),'http://')===0);
}

function odl_cleanUp() {
  global $smarty_cache_dir;
  $deleteTimeDiff= 7 * 24 * 60 * 60;// 7 days
  if ( !($dh = opendir( $smarty_cache_dir )) )
    echo 'Unable to open cache directory "' . $smarty_cache_dir . '"';

  while ( $file = readdir($dh) ) {
    if ( ($file != '.') && ($file != '..') ) {
      $file2 = $smarty_cache_dir . $file;
      if (isset($file2) && is_file($file2)) {
        $diff = mktime() - @filemtime($file2);
        //if ($diff > $deleteTimeDiff) @unlink( $file2 );
      }
    }
  }
}

function odlRssFilter($text){echo convert_chars(ent2ncr($text));} 

function odlRssLink($vars) {
  global $wpdb, $table_prefix, $wp_rewrite;
  $odlinkssettings=get_option('odlinksdata');
  $pageinfo = odlinksget_pageinfo();
  if($wp_rewrite->using_permalinks()) $delim = "?";
  else $delim = "&amp;";
  $perm = get_permalink($pageinfo['ID']);
  $main_link = $perm.$delim;

  return $main_link."_action=main&amp;id=".$vars["id"]."&amp;parent=".$vars['parent'];
}


function odlFbLike() {
  $odlinkssettings=get_option('odlinksdata');
  $layout = 'standard';// button_count standard
  $show_faces = 'false';// TODO
  $font = 'arial';
  $colorscheme = 'light';// dark
  $action = 'like';//  recommend
  $width = '450';
  $height = '25';
  $perm = get_permalink($pageinfo['ID']);
  $url = get_bloginfo('wpurl').'/?page_id=' . $pageinfo["ID"];
  $permalink = urlencode($url);
  $output = '<div style="margin:5px 0">';
  $output .= '<iframe src="http://www.facebook.com/plugins/like.php?href='.str_replace('&', '&amp;', $url).'&amp;layout='.$layout.'&amp;show_faces='.$show_faces.'&amp;width='.$width.'&amp;action='.$action.'&amp;font='.$font.'&amp;colorscheme='.$colorscheme.'" scrolling="no" frameborder="0" allowTransparency="true" style="border:none;overflow:hidden;width:'.$width.'px;height:'.$height.'px"></iframe>';
  return $output . '</div>';
}

function odl_listFiles($dir) {
  if($dh = opendir($dir)) {
    $files = Array();
    $inner_files = Array();
    while($file = readdir($dh)) {
      if($file != "." && $file != ".." && $file[0] != '.') {
        if(is_dir($dir . "/" . $file)) {
          $inner_files = odl_listFiles($dir . "/" . $file);
          if(is_array($inner_files)) $files = array_merge($files, $inner_files);
        } else {
          $ff = $dir . '/' . $file;
          if(preg_match('/90\.jpg$/',$file) &&
             filesize($ff) != 5635 &&
             filesize($ff) != 5816 &&
             filesize($ff) != 5849 &&
             filesize($ff) != 6182 &&
             filesize($ff) != 6104 &&
             filesize($ff) != 6615 &&
             filesize($ff) != 5993) {
             $d = substr($dir, -4, 4); 
             array_push($files, $d . '/' . $file);
          }
        }
      }
      if (sizeof($files) > 40) return $files;
    }
    closedir($dh);
    return $files;
  }
}


?>
