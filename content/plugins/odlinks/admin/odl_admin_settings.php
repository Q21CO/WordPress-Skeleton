<?php

/*
 * odl_functions.php
 * wordpress plugin website directory project
 * This file contains the ODLinks functions
 * @author website: http://www.forgani.com
 * @copyright Copyright 2008, Mohammad Forgani
 * @version 1.0
 * @link 
 * 20-10-2012 update the default values
*/

function process_odlinkssettings(){
  global $_GET, $_POST, $wp_rewrite, $PHP_SELF, $wpdb, $table_prefix, $odlinksversion, $wp_version, $odl_lang ,$accountLevel;
  $msg = '';

  // dir setting checker
  $arr = array('templates_c', 'cache');
  foreach ($arr as $value) {
     $dir = ODL_PLUGIN_DIR . '/includes/Smarty/' . $value. '/';
     if( ! is_writable( $dir ) || ! is_readable( $dir ) ) {
        echo "<BR /><BR /><fieldset><legend style='font-weight: bold; color: #900;'>Directory Checker</legend>";
        echo "<p><font color='#FF0000'>Check directory permission:".$dir."</font><BR>" ;
        echo "Please used the command <code><font color='#FF0000'>\"sudo chmod 777 -R ".$dir."\"</font></code> to change the permissions of all files</p>";
        echo "</fieldset>";
     }
  }

  if (isset($_GET['odlinks_admin_action']))
    switch ($_GET['odlinks_admin_action']){
      case "savesettings":
        odlinkscheck_db();
        $odlinkswp_pageinfo = $wpdb->get_row("SELECT * FROM {$table_prefix}posts WHERE post_title = '[[ODLINKS]]'", ARRAY_A);
        if ($odlinkswp_pageinfo["post_title"]!="[[ODLINKS]]"){
          $odlinkswp_pageinfo = odlinkscreate_page();
        }
        $_POST['odlinksdata']['odlinksinstalled'] = 'y';
        $_POST['odlinksdata']['odlinksversion'] = $odlinksversion;
        $odl_new_slug = $_POST['odlinksdata']['odlinksslug'];
        $matching_slug = $wpdb->get_var("SELECT post_name FROM {$table_prefix}posts WHERE post_name = '".$wpdb->escape($odl_new_slug)."'");
        $odl_current_settings = get_option('odlinksdata');
        if($odl_new_slug!=$odl_current_settings['odlinksslug']){
          if($matching_slug!=$odl_new_slug){
            $wpdb->query("UPDATE {$table_prefix}posts SET post_name = '".$odl_new_slug."' WHERE post_title = '[[ODLINKS]]'");
          } else {
            $msg ="A slug exists with the name: ".$odl_new_slug."<br />"."try again.";
            $_POST['odlinksdata']['odlinksslug'] = $odl_current_settings['odlinksslug'];
          }
        }
        $data = array();
        foreach ($_POST['odlinksdata'] as $k=>$v) {
          $v = stripslashes($_POST['odlinksdata'][$k]);
          $data[$k]=$v;
        }
        update_option('odlinksdata', $data);
        $wp_rewrite->flush_rules();
        $msg = "Settings Updated.";
      break;
    }
    $odlinkssettings = array();
    $odlinkssettings = get_option('odlinksdata');
    if ($odlinkssettings['odlinksinstalled']!='y')
      $odlinkssettings = odlinksinstall($odlinkssettings);

    if ($msg!=''){
      ?>
      <div id="message" class="updated fade"><?php echo $msg;?></div>
      <?php
    }

    $selflink = ($wp_rewrite->get_page_permastruct()=="")?"<a href=\"".get_bloginfo('url')."/index.php?pagename=".$odlinkssettings['odlinksslug']."\">".get_bloginfo('url')."/index.php?pagename=".$odlinkssettings['odlinksslug']."</a>":"<a href=\"".get_bloginfo('url')."/".$odlinkssettings['odlinksslug']."/\">".get_bloginfo('url')."/".$odlinkssettings['odlinksslug']."/</a>";

    $pageinfo = odlinksget_pageinfo();
    odl_showCategoryImg();
    ?>
    <h2>General Settings</h2>
    <p>
    <form method="POST" id="odlSettings" name="odlSettings" action="<?php echo $PHP_SELF;?>?page=odlinkssettings&odlinks_admin_action=savesettings">
    <input type="hidden" name="odlinksdata[odlinksversion]" value="<?php echo $odlinksversion;?>">
    <table border="0" class="editform">
      <tr><th align="right">odlinks Version:</th><td><?php echo $odlinksversion;?></td></tr>
      <tr><th align="right">WordPress Version:</th><td><?php echo $wp_version;?></td></tr>
      <tr><th align="right">odlinks URL:</th><td><?php echo $selflink;?></td></tr>
      <tr><th align="right">odlinks Slug:</th>
      <?php if (!$odlinkssettings['odlinksslug']) $odlinkssettings['odlinksslug'] = 'odlinks'; ?>
      <td><input type="text" size="40" name="odlinksdata[odlinksslug]" value="<?php echo str_replace('"', "&quot;", stripslashes($odlinkssettings['odlinksslug']));?>">
      </td>
      </tr>
      <tr>
      <th align="right">&nbsp;</th>
      <?php if (!$odlinkssettings['odlinksslug']) $odlinkssettings['odlinksshow_credits'] = 'y'; ?>
      <td><input type="checkbox" name="odlinksdata[odlinksshow_credits]" value="y"<?php echo ($odlinkssettings['odlinksshow_credits']=='y')?" checked":"";?>> Display odlinks credit line at the bottom of ODLinks pages
      </td>
      </tr>
      <tr>
      <th align="right">ODLinks Page Link Name:</th>
      <?php if (!$odlinkssettings['page_link_title']) $odlinkssettings['page_link_title'] = 'Open Directory Links'; ?>
      <td><input type="text" size="40" name="odlinksdata[page_link_title]" value="<?php echo $odlinkssettings['page_link_title'];?>">
      </td>
      </tr>
      <?php if (!$odlinkssettings['description']) $odlinkssettings['description'] = $odl_lang{ODL_SUBMITTING_NOTE} . $odl_lang{ODL_SUBMITTING_TEXT}; ?>
      <tr><th valign="top" align="right">Description:</th>
      <td><textarea cols=60 rows=6 name="odlinksdata[description]"><?php echo str_replace("<", "&lt;", stripslashes($odlinkssettings['description']));?></textarea></td>
      </tr>
      <tr><th></th><td valign="top"><?php echo $odlinkssettings['description'] ?></td></tr>
      <tr><th colspan=2> &nbsp;</th></tr>
      <tr><th align="right">Number of recent posts to be displayed:</th>
      <td>
      <?php if (!$odlinkssettings['odlinks_last_links_num']) $odlinkssettings['odlinks_last_links_num'] = '10'; ?>
      <input type="text" size="4" name="odlinksdata[odlinks_last_links_num]" value="<?php echo ($odlinkssettings['odlinks_last_links_num']);?>" onchange="this.value=this.value*1;">
      </td>
      </tr>
      <tr><th align="right">&nbsp;</th>
      <?php if (!$odlinkssettings['odlinksslug']) $odlinkssettings['odlinks_last_links'] = 'y'; ?>
      <td><input type="checkbox" name="odlinksdata[odlinks_last_links]" value="y"<?php echo ($odlinkssettings['odlinks_last_links']=='y')?" checked":"";?>>  Display recent posts.
      </td>
      </tr>
      <tr><th align="right">string exceeded length:</th>
      <?php if (!$odlinkssettings['odlinks_excerpt_length']) $odlinkssettings['odlinks_excerpt_length'] = '500'; ?>
      <td><input type="text" size="4" name="odlinksdata[odlinks_excerpt_length]" value="<?php echo ($odlinkssettings['odlinks_excerpt_length']);?>" onchange="this.value=this.value*1;">
      </td>
      </tr>
      <tr><th align="right">&nbsp;</th>
      <?php if (!$odlinkssettings['odlinksslug']) $odlinkssettings['odlinks_dispaly_html'] = 'y'; ?>
      <td><input type="checkbox" name="odlinksdata[odlinks_dispaly_html]" value="y"<?php echo ($odlinkssettings['odlinks_dispaly_html']=='y')?" checked":"";?>>  Displaying HTML in Posts.
      </td>
      </tr>
      <tr><th align="right">Count of subcategories under each category:</th>
      <?php if (!$odlinkssettings['odlinks_sub_cat_num']) $odlinkssettings['odlinks_sub_cat_num'] = '50'; ?>
      <td><input type="text" size="4" name="odlinksdata[odlinks_sub_cat_num]" value="<?php echo ($odlinkssettings['odlinks_sub_cat_num']);?>" onchange="this.value=this.value*1;">
      </td>
      </tr>
      <tr><th align="right">Number of items to display per page:</th>
      <?php if (!$odlinkssettings['odlinks_num_links']) $odlinkssettings['odlinks_num_links'] = '10'; ?>
      <td><input type="text" size="4" name="odlinksdata[odlinks_num_links]" value="<?php echo ($odlinkssettings['odlinks_num_links']);?>" onchange="this.value=this.value*1;">
      </td>
      </tr>
      <tr><th align="right" valign="top">Top Image:</th>
        <td valign="top">
        <?php if (!$odlinkssettings['odlinks_top_image']) $odlinkssettings['odlinks_top_image'] = 'odlinks.gif'; ?>
        <input type=hidden name="odlinksdata[odlinks_top_image]" value="<?php echo ($odlinkssettings['odlinks_top_image']);?>">
        <?php
        echo "\n<select name=\"topImage\" onChange=\"showimage()\">";    
        $rep = ODL_PLUGIN_DIR . "/images/";
        $handle=opendir($rep);
        while ($file = readdir($handle)) {
          $filelist[] = $file;
        }
        asort($filelist);
        while (list ($key, $file) = each ($filelist)) {
          if (!ereg(".gif|.jpg|.png",$file)) {
            if ($file == "." || $file == "..") $a=1;
          } else {
            if ($file == $odlinkssettings['odlinks_top_image']) {
              echo "\n<option value=\"$file\" selected>$file</option>\n";
            } else {
              echo "\n<option value=\"$file\">$file</option>\n";
            }
          }
        }
        echo "\n</select><br /><span style='text-align: right;'>&nbsp;&nbsp;<img name=\"avatar\" src=\"". ODL_PLUGIN_URL . "/images/" . $odlinkssettings['odlinks_top_image'] ."\" class=\"imgMiddle\"></span><br />";
        ?>
        <span class="smallTxt">upload your own image under plugins/odlinks/images directory</span></td>
      </tr>
        
      <tr><th align="right">Show the confirmation code: </th>  
        <?php if (!isset ($odlinkssettings['odlinks_num_links']) ) $odlinkssettings['confirmation_code'] = 'y'; ?>
        <td><input type=checkbox name="odlinksdata[confirmation_code]" value="y"<?php echo ($odlinkssettings['confirmation_code']=='y')?" checked":"";?>></td>
      </tr>

      <tr><td colspan=2><hr /><strong>Website Thumbshots</strong><hr /></td></tr>
      <tr>
      <th align="right" valign="top">Thumbshots Access Key Id<span class="odl_Red"> *</span></th>
      <td>
        <input name="odlinksdata[thumbshots_access_id]" value="<?php echo ($odlinkssettings['thumbshots_access_id']);?>" id="odlinksdata[thumbshots_access_id]" type="text" size="40" />
        <span class="odl_smallTxt"><br><b>The Access key.</b> You can register for a FREE account here <a href="https://my.thumbshots.ru/auth/register.php?locale=en-US" target="_blank">Thumbshots Account Details</a>.</span>
      </td>
      </tr>
      <tr><th>&nbsp;</th>
      <td><p><input type="submit" value="Update ODLinks Settings" class="button-primary" name="submit" id="submit"></p></td>
      </tr>
    </table>
    </form>
    </p>
  <?php
}

function odlinksinstall($odlinkssettings){
  global $odlinksversion;
  $odlinkssettings['odlinksversion'] = $odlinksversion;
  $odlinkssettings['odlinksinstalled'] = 'y';
  $odlinkssettings['userfield'] = odlinksget_namefield();
  $odlinkssettings['odlinksadd_into_pages'] = 'y';
  $odlinkssettings['odlinksshow_credits'] = 'y';
  $odlinkssettings['odlinksread_blog'] = 'y';
  $odlinkssettings['odlinksslug'] = 'odlinks';
  $odlinkssettings['page_link_title'] = 'Open Directory Links';
  $odlinkssettings['odlinkstheme'] = 'default';
  $odlinkssettings['odlinks_display_titles'] = 'y';
  $odlinkssettings['odlinks_top_image'] = 'odlinks.gif';
  $odlinkssettings['odlinks_display_last_links'] = 'y';
  $odlinkssettings['odlinks_display_last_post_link'] = 'y';
  $odlinkssettings['odlinks_last_links_num'] = 10;
  $odlinkssettings['odlinks_excerpt_length'] = 500;
  $odlinkssettings['odlinks_last_links'] = "y";
  $odlinkssettings['confirmation_code']="y";
  $odlinkssettings['odlinks_sub_cat_num'] = 10;
  $odlinkssettings['odlinks_num_links'] = 10;
  $odlinkssettings['odlinks_search_log'] = 25;
  $odlinkssettings['odlinks_description'] = $odl_lang{ODL_SUBMITTING_NOTE} . $odl_lang{ODL_SUBMITTING_TEXT};
  $odlinkssettings['odlinks_keywords'] = 'dummy';
  $odlinkssettings['thumbshots_access_id']= '';
  $odlinkssettings['odlinks_dispaly_html'] = "y";
  update_option('odlinksdata', $odlinkssettings);
  return $odlinkssettings;
}

function odlinkscheck_db(){
  odlinksupdate_db();
}

function odlinksupdate_db(){
  global $wpdb, $table_prefix, $odlinksversion;

  $odlinkssql[$table_prefix.'odcategories'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}odcategories (
    c_id int(11) NOT NULL auto_increment,
    c_parent int(11) NOT NULL default '0',
    c_position int(11) NOT NULL default '0',
    c_name varchar(150) NOT NULL,
    c_title varchar(150) NOT NULL,
    c_description text NOT NULL,
    c_text text NOT NULL,
    c_date date,
    c_keywords text,
    c_status enum('active','inactive','readonly') NOT NULL default 'active',
    c_hide enum('hidden','visible') NOT NULL default 'visible',
    c_links int(11) NOT NULL default '0',
    c_posts int(11) NOT NULL default '0',
    c_rss text,
    PRIMARY KEY (c_id),
    KEY c_parent (c_parent)
  );";

  $odlinkssql[$table_prefix.'odbanned'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}odbanned (
    b_id int(11) NOT NULL auto_increment,
    c_domain text NOT NULL,
    PRIMARY KEY (b_id)
  );";

  $odlinkssql[$table_prefix.'odlinks'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}odlinks (
    l_id int(11) NOT NULL auto_increment,
    l_c_id int(11) NOT NULL default '0',
    l_date date,
    l_subject varchar(255) NOT NULL default '',
    l_description text NOT NULL default '',
    l_url varchar(255) NOT NULL default '',
    l_posts int(11) NOT NULL default '0',
    l_views int(11) NOT NULL default '0',
    l_sticky enum('y','n') NOT NULL default 'n',
    l_status enum('closed','deleted','open') NOT NULL default 'open',
    l_hide enum('hidden','visible') NOT NULL default 'visible',
    l_author_name varchar(100) NOT NULL default '',
    l_author_ip varchar(15) NOT NULL default '',
    l_author_mail varchar(64) NOT NULL default '',
    l_recip tinyint(4) NOT NULL default '0',
    l_title varchar(255),
    l_google_rank varchar(255),
    PRIMARY KEY (l_id) 
  );";

  $odlinkssql[$table_prefix.'odpages'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}odpages (
    p_id int(11) NOT NULL auto_increment,
    p_l_id int(11) NOT NULL default '0',
    p_url text NOT NULL,
    p_title text NOT NULL,
    p_description text NOT NULL,
    p_google_rank tinyint(4) NOT NULL default '0',
    p_recip tinyint(4) NOT NULL default '0',
    p_date int(20) NOT NULL default '0',
    p_size int(32) NOT NULL default '0',
    PRIMARY KEY (p_id)
  );";

  $odlinkssql[$table_prefix.'odnew_links'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}odnew_links (
    n_id int(11) NOT NULL auto_increment,
    n_url text NOT NULL,
    n_title text NOT NULL,
    n_description text NOT NULL,
    n_email tinytext NOT NULL,
    n_category int(11) NOT NULL default '0',
    PRIMARY KEY  (n_id)
  );";


  $tabs = $wpdb->get_results("SHOW TABLES", ARRAY_N);

  $tables = array();

  for ($i=0; $i<count($tabs); $i++){
    $tables[] = $tabs[$i][0];
  }

  @reset($odlinkssql);

  while (list($k, $v) = @each($odlinkssql)){
    if (!@in_array($k, $tables)){
      echo " - create table: " .  $k . "<br />"; 
      $wpdb->query($v);
    }
  }
}

function odlinksget_namefield(){
  global $wpdb, $table_prefix, $wp_version;
  if ($user_field == false){
    $tcols = $wpdb->get_results("SHOW COLUMNS FROM IF NOT EXISTS {$table_prefix}users", ARRAY_A);
    $cols = array();
    for ($i=0; $i<count($tcols); $i++){
      $cols[] = $tcols[$i]['Field'];
    }
    if (in_array("display_name", $cols)){
      $wpc_user_field = "display_name";
      $wp_version = "2";
    } else {
      $wpc_user_field = "user_nickname";
      $wp_version = "1";
    }
  }
  return $user_field;
}


function odl_showCategoryImg() {
  echo "<script type=\"text/javascript\">\n";
  echo "<!--\n\n";
  echo "function showimage() {\n";
  echo "if (!document.images)\n";
  echo "return\n";
  echo "document.images.avatar.src=\n";
  echo "'". ODL_PLUGIN_URL . "/images/' + document.odlSettings.topImage.options[document.odlSettings.topImage.selectedIndex].value;\n";
  echo 'document.odlSettings.elements["odlinksdata[odlinks_top_image]"].value = document.odlSettings.topImage.options[document.odlSettings.topImage.selectedIndex].value;';
  echo "}\n\n";

  echo "function showCatimage() {\n";
  echo "if (!document.images)\n";
  echo "return\n";
  echo "document.images.avatar.src=\n";
  echo "'".get_bloginfo('wpurl')."/wp-content/plugins/odlinks/' + document.admCatStructure.topImage.options[document.admCatStructure.topImage.selectedIndex].value;\n";
  echo 'document.admCatStructure.elements["odlinksdata[photo]"].value = document.admCatStructure.topImage.options[document.admCatStructure.topImage.selectedIndex].value;';
  echo "}\n\n";
  echo "//-->\n";
  echo "</script>\n"; 
}

/* Method that automatically checks the user's STWW account type and stores the information
* in the database, so that only the usable features can be used/displayed.
*/
function checkAccountType($access_id, $secret_key){
  $args['stwaccesskeyid'] = $access_id;
  $args['stwu'] = $secret_key;
  $fetchURL = urldecode("http://images.shrinktheweb.com/account.php?".http_build_query($args));
  $resp = wp_remote_get($fetchURL);
  if (is_wp_error($resp) || 200 != $resp['response']['code'] ) {
    echo ('Error fetching account details.' . $resp->get_error_message());
    return false;
  }
  $accountDetails = array();
  $accountStatus = false;
  $accountLevel = false;

  // Use SimpleXML if we have it.
  if (!extension_loaded('simplexml')) {
    $dom=new DOMDocument;
    $dom->loadXML($resp['body']);
    $xml=simplexml_import_dom($dom);
    $xmlLayout  = 'http://www.shrinktheweb.com/doc/stwacctresponse.xsd';
    $accountStatus = (string)$xml->children($xmlLayout)->Response->Status->StatusCode;
    $accountLevel = (string)$xml->children($xmlLayout)->Response->Account_Level->StatusCode;
  } else {
    $accountStatus = xml_getResponse('Status', $resp['body']);
    $accountLevel = xml_getResponse('Account_Level', $resp['body']);
  }
  if ($accountStatus == 'Success') {
    switch ($accountLevel) { 
      case 1: $accountDetails['account_type'] = 'basic'; break;
      case 2: $accountDetails['account_type'] = 'plus'; break;
      default:
        $accountDetails['account_type'] = 'FREE';
      break;
    }
  } else {
    $accountDetails['account_type'] = 'INVALID';
  }
  return $accountDetails;
}

function xml_getResponse($sSearch, $s)	{
  $sRegex = '/<[^:]*:' . $sSearch . '[^>]*>[^<]*<[^:]*:StatusCode[^>]*>([^<]*)<\//';
  if (preg_match($sRegex, $s, $sMatches)) {
     return $sMatches[1];
  }
  return false;
}
?>
