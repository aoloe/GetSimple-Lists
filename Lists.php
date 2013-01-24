<?php
/*
Plugin Name: Lists
Description: Manage Lists and Forms
Version: 0.1
Author: Ale Rimoldi
Author URI: http://www.ideale.ch/
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

# register plugin
register_plugin(
  $thisfile, //Plugin id
  'Lists',  //Plugin name
  '0.1',    //Plugin version
  'Ale Rimoldi',  //Plugin author
  'http://www.ideale.ch/', //author website
  'Manage Lists and Forms', //Plugin description
  'pages', //page type - on which admin tab to display
  'lists_admin'  //main function (administration)
);

// Settings (TODO: get them from a real settings file, filled through the Lists-settings)
$lists_config = array (
    'list' => array (
        array (
            'id' => 'a',
            'title' => 'Liste A',
            'show' => array (
                'page' => 'index',
                'rule' => 'prepend', // prepend, append, replace
            ),
        ),
        array (
            'id' => 'b',
            'title' => 'Liste B',
            'show' => array (
                'page' => 'index',
                'rule' => 'append', // prepend, append, replace
            ),
        ),
    ),
);

// activate filter 
add_filter('content','lists_show'); 
add_action('pages-sidebar', 'createSideMenu', array($thisfile, 'Hello', 'list_c'));


// add a link in the admin tab 'theme'
foreach ($lists_config['list'] as $item) {
    add_action('pages-sidebar', 'createSideMenu', array($thisfile, $item['title'].' Manager', 'list_'.$item['id']));
}

function lists_show($content) {
    global $lists_config; // TODO: put it in a class in order to avoid globals!
    $result = '';
    $url = strval(get_page_slug(FALSE));
    $prepend = '';
    $append = '';
    $replace = '';
    foreach ($lists_config['list'] as $item) {
        if ($item['show']['page'] == $url) {
            switch ($item['show']['rule']) {
                case 'prepend' :
                    $prepend .= '<p>When I grow up I\'ll show a list of '.$item['title'].'.</p>'."\n";
                break;
                case 'append' :
                    $append .= '<p>When I grow up I\'ll show a list of '.$item['title'].'.</p>'."\n";
                break;
                case 'replace' :
                    $replace .= '<p>When I grow up I\'ll show a list of '.$item['title'].'.</p>'."\n";
                break;
            }
        }
    }
    if ($replace != '') {
        echo($replace);
    } else {
        $result = $prepend.$content.$append;
    }
    return $result;
}

function lists_admin() {
    echo '<p>When I grow up I\'ll let you create  '.(array_key_exists('list_a', $_REQUEST) ? 'A' : 'B').' lists.</p>';
}

/**
 * @param mixed $id a string or array defining the list the promotions have to be taken from
 */
function lists_promotion($id = null) {
    // TODO: echo the promoted items
}
