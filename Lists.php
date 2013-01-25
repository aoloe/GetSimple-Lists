<?php
/*
Plugin Name: Lists
Description: Manage Lists and Forms
Version: 0.1
Author: Ale Rimoldi
Author URI: http://www.ideale.ch/
*/

# get correct id for plugin
$lists_plugin_id = basename(__FILE__, ".php");

// hack to get the link from the settings sidebar to go to the settings tab
$lists_admin_tab = 'pages';
if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == $lists_plugin_id) {
  if (array_key_exists('Lists_settings', $_REQUEST)) {
      $lists_admin_tab = 'settings';
  }
}

# register plugin
register_plugin(
  $lists_plugin_id, //Plugin id
  'Lists',  //Plugin name
  '0.1',    //Plugin version
  'Ale Rimoldi',  //Plugin author
  'http://www.ideale.ch/', //author website
  'Manage Lists and Forms', //Plugin description
  $lists_admin_tab, //page type - on which admin tab to display
  'lists_admin'  //main function (administration)
);

// debug('GSDATAOTHERPATH', GSDATAOTHERPATH);
define('LISTSDATAPATH', GSDATAOTHERPATH.$lists_plugin_id.'/');
define('LISTSDATACONFIGURATION', GSDATAOTHERPATH.$lists_plugin_id.'/'.'configuration.xml');

// Settings (TODO: get them from a real settings file, filled through the Lists-settings)
$lists_config = array (
);

if (!is_frontend()) {
    i18n_merge($lists_plugin_id, substr($LANG,0,2)); 
}

// debug('get_defined_vars', get_defined_vars());

include(GSPLUGINPATH.$lists_plugin_id.'/Lists.php');
Lists::initialize();
Lists::set_plugin_id($lists_plugin_id);

add_filter('content', 'Lists_show'); 
add_action('settings-sidebar', 'createSideMenu', array($lists_plugin_id, i18n_r('Lists/SIDEBAR_LABEL_LISTS'), $lists_plugin_id.'_settings'));

// add a link in the admin tab 'theme'
foreach (Lists::get_list() as $item) {
    add_action('pages-sidebar', 'createSideMenu', array($lists_plugin_id, $item['title'].' '.i18n_r('Lists/SIDEBAR_LABEL_MANAGER'), $lists_plugin_id.'_id_'.$item['id']));
}

function Lists_show($content) {
    return Lists::process_show($content);
}

function Lists_admin() {
    Lists::process_admin();
}

/**
 * @param mixed $id a string or array defining the list the promotions have to be taken from
 */
function Lists_promotion($id = null) {
    // TODO: echo the promoted items
}
