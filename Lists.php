<?php

// error_reporting(E_ALL);
// ini_set('display_errors', '1');


/*
Plugin Name: Lists
Description: Manage Lists and Forms
Version: 0.1
Author: Ale Rimoldi
Author URI: ideale.ch
*/

# get correct id for plugin
$lists_plugin_id = basename(__FILE__, ".php");

// hack to get the link from the settings sidebar to go to the settings tab
/*
$lists_admin_tab = 'pages';
if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == $lists_plugin_id) {
  if (array_key_exists('Lists_settings', $_REQUEST)) {
      $lists_admin_tab = 'settings';
  }
}
*/

# register plugin
register_plugin(
  $lists_plugin_id, //Plugin id
  'Lists',  //Plugin name
  '0.1',    //Plugin version
  'Ale Rimoldi',  //Plugin author
  'http://www.ideale.ch/', //author website
  'Manage Lists and Forms', //Plugin description
  'pages', //page type - on which admin tab to display
  'lists_admin'  //main function (administration)
);

/**
 * Define the plugin's wide settings. You may adapt them to match your GS install.
 */
// debug('GSDATAOTHERPATH', GSDATAOTHERPATH);
define('LISTSDATAPATH', GSDATAOTHERPATH.$lists_plugin_id.'/');
define('LISTSDATASETTINGS', GSDATAOTHERPATH.$lists_plugin_id.'/'.'settings.xml');
define('LISTS_DATALISTSPATH', GSDATAOTHERPATH.$lists_plugin_id.'/'.'list/');
define('LISTSREQUESTPREFIX', 'lists_item_');

// Settings (TODO: get them from a real settings file, filled through the Lists-settings)
if (!is_frontend()) {
    i18n_merge($lists_plugin_id, substr($LANG,0,2)); 
}

// debug('get_defined_vars', get_defined_vars());

include(GSPLUGINPATH.$lists_plugin_id.'/Lists.php');
Lists::set_plugin_id($lists_plugin_id);
Lists::set_plugin_info($plugin_info[$lists_plugin_id]);
Lists::initialize();

add_filter('content', 'Lists_show'); 
if (!is_frontend()) {
    add_action('settings-sidebar', 'createSideMenu', array($lists_plugin_id, i18n_r('Lists/SIDEBAR_LABEL_LISTS'), $lists_plugin_id.'_settings'));

    // TODO: remove the link to the page (show/edit in frontend) if the page gets deleted
    // (should there be a way to give a warning?)
    // add_action('page-delete', 'myplugin_function');

    // add a link in the admin tab 'theme'
    foreach (Lists::get_list() as $key => $value) {
        add_action('pages-sidebar', 'createSideMenu', array($lists_plugin_id, $value.' '.i18n_r('Lists/SIDEBAR_LABEL_MANAGER'), $lists_plugin_id.'_id_'.$key));
    }
    add_action('header','Lists_routing');
}

// class Lists_storage ?

function Lists_routing(){
    Lists::process_routing();
} 

function Lists_show($content) {
    // return Lists::process_show($content);
}

function Lists_admin() {
    global $lists_plugin_id;
    getPagesXmlValues(); // this populates $pagesArray, which is a global...
    global $pagesArray;

    include(GSPLUGINPATH.$lists_plugin_id.'/Lists_administration.php');
    Lists_administration::set_plugin_id($lists_plugin_id);
    Lists_administration::set_page_list($pagesArray);
    $admin = Lists_administration::factory();
    $admin->process();
}

/**
 * @param mixed $id a string or array defining the list the promotions have to be taken from
 */
function Lists_promotion($id = null) {
    // TODO: echo the promoted items
}
