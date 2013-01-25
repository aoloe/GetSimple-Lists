<?php
class Lists {
    static protected $plugin_id = '';
    public static function set_plugin_id($id) {self::$plugin_id = $id;}
    public static function get_plugin_id() {return self::$plugin_id;}
    static protected $configuration = array();
    static public function get_list() {return self::$configuration['list'];}

    public static function initialize() {
		if(is_readable(LISTSDATACONFIGURATION)) {
            self::$configuration = getXML(LISTSDATACONFIGURATION);
        }
        
        // TODO: read the settings
        self::$configuration = array (
            'list' => array (
                'a' => array (
                    'id' => 'a',
                    'title' => 'Liste A',
                    'show' => array (
                        'page' => 'index',
                        'rule' => 'prepend', // prepend, append, replace
                    ),
                ),
                'b' => array (
                    'id' => 'b',
                    'title' => 'Liste B',
                    'show' => array (
                        'page' => 'index',
                        'rule' => 'append', // prepend, append, replace
                    ),
                ),
            ),
        );
    }

    public static function process_admin() {
        // debug('_REQUEST', $_REQUEST);


        if (array_key_exists('Lists_settings', $_REQUEST)) {
            include(GSPLUGINPATH.self::$plugin_id.'/Lists_settings.php');
            $settings = new Lists_settings();
            $settings->render();
        } else {
            echo '<p>When I grow up I\'ll let you create  '.(array_key_exists('lists_id_a', $_REQUEST) ? 'A' : 'B').' lists.</p>';
        }
    }

    public static function process_show($content) {
        $result = '';
        $url = strval(get_page_slug(FALSE));
        $prepend = '';
        $append = '';
        $replace = '';
        foreach (self::$configuration['list'] as $item) {
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
}
