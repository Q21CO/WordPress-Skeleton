<?php

/*
 * odl_main.php
 * @author Mohammad Forgani
 * wordpress plugin website directory project
 * @copyright Copyright 2008, Oh Jung-Su
 * @version 1.0
 * @link http://www.forgani.com
*/



function odlinksadmin_page(){
  global $_GET, $_POST, $_REQUEST, $pagelabel;

  if (isset($_REQUEST['odlinks_admin_page_arg'])) 
    switch ($_REQUEST['odlinks_admin_page_arg']) {
      case "odlinkssettings":
        process_odlinkssettings();
      break;
      case "odlinksstructure":
        process_odlinksstructure();
      break;
      case "odlinksposts":
        process_odlinksposts();
      break;
      case "odlinksutilities":
        process_odlinksutilities();
      break;
    }
    process_odlinkssettings();
}



function odlinks_admin_page(){
  global $odlinksadmin_links, $odlinksadmin_page_name;   
  add_menu_page($odlinksadmin_page_name,$odlinksadmin_page_name,'manage_options',__FILE__,'process_odlinksposts', ODL_PLUGIN_URL . '/images/odl.gif', 6);
  //add_submenu_page(__FILE__, 'Webpages', 'Webpages', 10, 'odlinksposts', 'process_odlinksposts');
  for ($i=0; $i<count($odlinksadmin_links); $i++){
    $tlink = $odlinksadmin_links[$i];
    add_submenu_page(__FILE__,$tlink['name'],$tlink['name'],10,$tlink['arg'],$tlink['prg']);
  }
}




?>
