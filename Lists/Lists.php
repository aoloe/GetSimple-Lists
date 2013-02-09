<?php
/**
 * There is some old (unused in this class). In the future it could become a class with common
 * code to the different parts... but, more likely, it will disappear
 *
 * The Lists class is just a glue class, setting up the environment, getting the calls
 * from the main plugin's file and dispatching them to objects doing the real work.
 * It's also doing controller and routing tasks.
 */
class Lists {
    static protected $plugin_id = '';
    public static function set_plugin_id($id) {self::$plugin_id = $id;}
    public static function get_plugin_id() {return self::$plugin_id;}
    static protected $plugin_info = array();
    public static function set_plugin_info(& $plugin_info) {self::$plugin_info = & $plugin_info;}
    static protected $storage = null;
    static protected $message = null;
    static protected $settings = null;
    static public function get_list() {
        $result = array();
        foreach (self::$settings->get_list() as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }

    public static function initialize() {
        include(GSPLUGINPATH.self::$plugin_id.'/Lists_message.php');
        include(GSPLUGINPATH.self::$plugin_id.'/Lists_storage.php');
        self::$storage = new Lists_storage();
        include(GSPLUGINPATH.self::$plugin_id.'/Lists_settings.php');
        if (!class_exists('Entity'))
            include(GSPLUGINPATH.Lists::get_plugin_id().'/Entity.php');
        include(GSPLUGINPATH.Lists::get_plugin_id().'/Lists_item_entity.php');
        self::$settings = Lists_settings::get_instance();
        self::$settings->read();
    }

    public static function process_admin() {
    }

    public static function process_show($content) {
        $result = '';
        $url = strval(get_page_slug(FALSE));
        $prepend = '';
        $append = '';
        $replace = '';
        foreach (self::$settings['list'] as $item) {
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

    public static function process_routing() {
        if (array_key_exists('Lists_settings', $_REQUEST)) {
            self::$plugin_info['page_type'] = 'settings';
        }
    }
}
