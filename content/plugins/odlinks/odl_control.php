<?php

/*
Plugin Name: ODLinks (Open Directory Links)
Plugin URI: http://www.forgani.com/tools/opendirectorylinks/
Description: Before you upgrade save your local modifications if you have any and you'll need to check or reconfigure your setting after you upgrade! 
Author: Mohammad forgani
Version: 1.4.1-a
Author URI: http://www.forgani.com


Changes 1.1.2-a - May 25/05/2010
- new captcha routine. The previous methods have got problem with firefox
- updated to show ComboBox with subcategory names


Changes 1.1.2-c - May 25/05/2010
- fixed for wordpress 3.0


Changes 1.1.2-d - Aug 29/08/2010
- implement category's link in footer


Last Changes: Mar 30/03/2011
- added/changed new skin theme & added some further admin interface 
- made some tiny changes to fixe for wp 3.1 problems..


Last Changes: Oct 22/10/2011
- Bug Fix: fix pagerank issues
- update to check the character
- Bug Fix: the confirmation code has been fixed for "sending link to a friend".

- links be added by the administrator without having to do the captcha, and resubmit my email address


Last Changes: Feb 01/03/2013
- implement page navigation
- fied to Thumbshots works properly.
- Bug Fix: admin style


Last Changes: Feb 29/11/2014
- Fixed Security Vulnerabilities

*/

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

global $table_prefix, $wpdb;
$odlinkssettings = get_option('odlinksdata');

// Sets the version number.
$odlinksversion = "1.4.1-a";
// Sets the required user level.
$odlinksuser_level = 8;
// Sets the Management page tab name in WordPress Admin area.
$odlinksadmin_page_name = 'ODLinks';
// Sets the odlinks page link name in WordPress.
$odlinks_name = 'odlinks';
$odlinksuser_field = false;
// Sets $odlinkswp_mainversion to false.
$odlinkswp_mainversion = false;
// Sets $odlinkswp_pageinfo to false.
$odlinkswp_pageinfo = false;
// Admin links and their url args.

$odlinksadmin_links = array(
  //array('name'=>'Webpages','arg'=>'odlinksposts','prg'=>'process_odlinksposts'),
  array('name'=>'Settings','arg'=>'odlinkssettings','prg'=>'process_odlinkssettings'),
  array('name'=>'Categories','arg'=>'odlinksstructure','prg'=>'process_odlinksstructure'),
  array('name'=>'Utilities','arg'=>'odlinksutilities','prg'=>'process_odlinksutilities'),
);


if (!$table_prefix) $table_prefix = $wpdb->prefix;

define('ODL_PLUGIN_URL', get_bloginfo('wpurl') . '/wp-content/plugins/odlinks');
define('ODL_PLUGIN_DIR', ABSPATH  . 'wp-content/plugins/odlinks');
define('ODL', 'wp-content/plugins/odlinks');
define('ODLADMIN', 'wp-content/plugins/odlinks/admin/');
define('ODLINC', 'wp-content/plugins/odlinks/includes/');
define('ODLADMINTHEME', 'wp-content/plugins/odlinks/themes');
define('ODLTHEME', 'wp-content/plugins/odlinks/themes/default');
define('ODLSMARTY', 'wp-content/plugins/odlinks/includes/Smarty');
define('ODLANG', 'wp-content/plugins/odlinks/languages/');

$smarty_template_dir =  ABSPATH . ODLTHEME;
$smarty_compile_dir  = ABSPATH . ODLSMARTY . '/templates_c';
$smarty_cache_dir    = ABSPATH . ODLSMARTY . '/cache';
$smarty_config_dir   = ABSPATH . ODLSMARTY . '/configs';

require_once(ABSPATH . ODLINC . 'odl_functions.php');
require_once(ABSPATH . ODLINC . 'odl_securimage.php');
require_once(ABSPATH . ODLADMIN . 'odl_admin_functions.php');
require_once(ABSPATH . ODLADMIN . 'odl_admin.php');
require_once(ABSPATH . ODLADMIN . 'odl_admin_settings.php');
require_once(ABSPATH . ODLADMIN . 'odl_admin_structure.php');
require_once(ABSPATH . ODLADMIN . 'odl_admin_utilities.php');
require_once(ABSPATH . ODL . '/odl_posts.php');
require_once(ABSPATH . ODL . '/odl_search.php');
require_once(ABSPATH . ODL . '/odl_main.php');

add_filter("the_title", "odlinkspage_handle_title");
add_filter("wp_list_pages", "odlinkspage_handle_titlechange");
add_filter("single_post_title", "odlinkspage_handle_pagetitle");
add_filter("query_vars", "odlinksquery_vars");
add_filter('the_generator', 'rm_generator_filter');

function rm_generator_filter() { return ''; }

add_action("the_content", "odlinkspage_handle_content");
add_action('admin_head', 'add_admin_head');
add_action('wp_head', 'add_head');
add_filter('plugin_action_links', 'plugin_action_links', 10, 2);
add_action('admin_menu', 'odlinks_admin_page');

// Assigns each respective variable.
if (!isset($_GET)) $_GET = $HTTP_GET_VARS;
if (!isset($_POST)) $_POST = $HTTP_POST_VARS;
if (!isset($_SERVER)) $_SERVER = $HTTP_SERVER_VARS;
if (!isset($_COOKIE)) $_COOKIE = $HTTP_COOKIE_VARS;

// Format any data sent to odlinks.
if (isset($_REQUEST["odlinksaction"])){
  $_SERVER["REQUEST_URI"] = dirname(dirname($_SERVER["PHP_SELF"]))."/".$odlinkssettings['odlinksslug']."/";
  $_SERVER["REQUEST_URI"] = stripslashes($_SERVER["REQUEST_URI"]);
}

function add_admin_head() {
  echo '<link rel="stylesheet" href="' . plugins_url('odlinks') . '/themes/default/css/admin.css" type="text/css" />';
}

function add_head() {
  echo '<link rel="stylesheet" href="' . plugins_url('odlinks') . '/themes/default/css/odlinks.css" type="text/css" />';
}

function plugin_action_links($links, $file) {
  if ($file == plugin_basename(__FILE__)) {
    $links[] = '<a href="admin.php?page=odlinkssettings">' . __('Settings') . '</a>';
  }
  return $links;
}


/**
 * get_language() - Get HTTP header accept languages
*/
$locale = get_locale();
if(!empty($locale)) {
  $lng = preg_split ('/_/', $locale );
  $languageFile = ODL_PLUGIN_DIR . '/language/lang_'. $lng[0] . '.php';
}
if (!empty($languageFile) && file_exists($languageFile)) require_once($languageFile);
 else require_once(ODL_PLUGIN_DIR . '/language/lang_en.php');

?>
