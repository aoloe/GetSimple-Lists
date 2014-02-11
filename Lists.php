<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

/**
 * This file is automatically loaded by GetSimple CMS
 * - It reigister itself as a plugin
 * - It defines the constants used inside of the Lists plugin 
 * Plugin Name: Lists
 * Description: Manage Lists and Forms
 * Version: 0.1
 * Author: Ale Rimoldi
 * Author URI: ideale.ch
 */

# get correct id for plugin
$lists_plugin_id = 'Lists';
// $lists_plugin_id = basename(__FILE__, ".php");
$current_script_id = basename($_SERVER['SCRIPT_NAME'], ".php"); // TODO: define what exactly this is and does

if (method_exists('GS', 'load_plugin')) {
    GS::load_plugin('PHPFived');
} elseif (!defined('PHPFIVED') && array_key_exists('PHPFived.php', $live_plugins) && $live_plugins['PHPFived.php'])  {
    include_once(GSPLUGINPATH.'PHPFived.php');
    PHPFived::initialize();
}
GS::load_plugin('ContentFields', '0.1');

# register plugin
GS::register_plugin(array(
    'id' => $lists_plugin_id,
    'name' => 'Lists',
    'version' => '0.1',
    'author' => 'Ale Rimoldi',
    'url' => 'http://www.ideale.ch/',
    'description' => 'Manage Lists and Forms',
    'page_type' => 'pages', // on which admin tab to display; may be overwritten by Lists_tab_routing()
    'main_function' => 'Lists_routing'  //main function (administration)
));

// debug('_REQUEST', $_REQUEST);

/**
 * Define the plugin's wide settings. You may adapt them to match your GS install.
 */
define('LISTS_PLUGIN_PATH', GS_PLUGIN_PATH.$lists_plugin_id.'/');
define('LISTS_DATA_PATH', GS_DATA_OTHER_PATH.$lists_plugin_id.'/');
define('LISTS_BACKUP_PATH', GS_BACKUP_PATH.$lists_plugin_id.'/');
define('LISTS_CACHE_PATH', LISTS_DATA_PATH.'cache/');
define('LISTS_CACHE_FILE', LISTS_CACHE_PATH.$lists_plugin_id.'.xml');
define('LISTS_TEMPLATE_PATH', LISTS_PLUGIN_PATH.'template/');
define('LISTS_DATA_SETTINGS', LISTS_DATA_PATH.'settings.xml');
define('LISTS_BACKUP_SETTINGS', LISTS_BACKUP_PATH.'settings.xml');
define('LISTS_DATA_LIST_PATH', LISTS_DATA_PATH.'list/');
define('LISTS_BACKUP_LIST', LISTS_BACKUP_PATH.$lists_plugin_id.'.xml');
define('LISTS_REQUEST_PREFIX', strtolower($lists_plugin_id).'_item_');

// Settings (TODO: get them from a real settings file, filled through the Lists-settings)
if (!is_frontend()) {
    i18n_merge($lists_plugin_id, substr($LANG,0,2)); 
}

include(LISTS_PLUGIN_PATH.'/Lists.php');
Lists::set_plugin_id($lists_plugin_id);
Lists::set_plugin_info($plugin_info[$lists_plugin_id]);
/**
 * The goal of the cache is mainly to avoid that further files have to be loaded, just
 * to check if the plugin has to do anything
 * TODO: check that this is the case! (or at least that the number of files to be read is minimized)
 */
Lists::read_cache();
if (in_array($current_script_id, array('pages', 'edit')) && array_key_exists('id', $_REQUEST)) {
    Lists::set_page_id($_REQUEST['id']);
}
$list_cache = Lists::get_cache();
// debug('list_cache', $list_cache);

/**
 * Define the actions that the plugin is providing
 */
if (!is_frontend()) {
    add_action('settings-sidebar', 'createSideMenu', array($lists_plugin_id, i18n_r('Lists/SIDEBAR_LABEL_LISTS'), $lists_plugin_id.'_administration'));

    // if we're editing a page
    if (array_key_exists('id', $_REQUEST) && array_key_exists($_REQUEST['id'], $list_cache['page'])) {
        add_action('edit-content', 'Lists_edit'); // if the current page has a list, add a trigger
        // TODO: remove the link to the page (show/edit in frontend) if the page gets deleted
        // (should there be a way to give a warning?)
        // add_action('page-delete', 'myplugin_function');
    }

    // add a link in the admin tab 'theme'
    foreach ($list_cache['global'] as $item) {
        add_action('pages-sidebar', 'createSideMenu', array($lists_plugin_id, $list_cache['list'][$item].' '.i18n_r('Lists/SIDEBAR_LABEL_MANAGER'), $lists_plugin_id.'_id_'.$item));
    }
    add_action('header','Lists_tab_routing');
} else {
    if (array_key_exists('id', $_REQUEST) && array_key_exists($_REQUEST['id'], $list_cache['page'])) {
        // TODO: not sure if this work... it has not been tested yet
        add_filter('content', 'Lists_show');  // TODO: only add a filter/action for the pages that have a list
    }
}

/**
 * Define which tab to show in the admin part
 */
function Lists_tab_routing(){
    Lists::tabs_routing();
} 

/**
 * Define which part is managing the information
 */
function Lists_routing(){
    if (array_key_exists('Lists_edit', $_REQUEST)) {
        Lists_edit();
    } else {
        Lists_admin();
    }
}

function Lists_admin() {
    getPagesXmlValues(); // this populates $pagesArray, which is a global...
    global $pagesArray;

    include_once(LISTS_PLUGIN_PATH.'/Lists_administration.php');
    Lists_administration::set_plugin_id(Lists::get_plugin_id());
    Lists_administration::set_page_list($pagesArray);
    $admin = Lists_administration::factory();
    $admin->process();
}

function Lists_edit() {
    include_once(LISTS_PLUGIN_PATH.'/Lists_edit.php');
    Lists_edit::set_plugin_id(Lists::get_plugin_id());
    // Lists_edit::set_page_list($pagesArray);
    $edit = Lists_edit::factory();
    $edit->process();
}

function Lists_show($content) {
    // return Lists::process_show($content);
}

/**
 * @param mixed $id a string or array defining the list the promotions have to be taken from
 */
function Lists_promotion($id = null) {
    // TODO: echo the promoted items
}
