<?php
/**
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
    static protected $configuration = array();
    static public function get_list() {
        $result = array();
        foreach (self::$configuration->get_list() as $item) {
            $result[$item['id']] = $item['title'];
        }
        return $result;
    }

    public static function initialize() {
        include(GSPLUGINPATH.self::$plugin_id.'/Lists_message.php');
        include(GSPLUGINPATH.self::$plugin_id.'/Lists_storage.php');
        self::$storage = new Lists_storage();
        include(GSPLUGINPATH.self::$plugin_id.'/Lists_configuration.php');
        self::$configuration = new Lists_configuration(self::$storage);
        self::$configuration->read();
    }

    public static function process_admin() {
        // debug('_REQUEST', $_REQUEST);
        if (array_key_exists('Lists_settings', $_REQUEST)) {
            if (!class_exists('Entity')) {
                include(GSPLUGINPATH.Lists::get_plugin_id().'/Entity.php');
            }
            if (!class_exists('Lists_item_entity')) {
                include_once(GSPLUGINPATH.Lists::get_plugin_id().'/Lists_item_entity.php');
            }
            $item_entity = Lists_item_entity::factory();
            include(GSPLUGINPATH.self::$plugin_id.'/Lists_item.php');
            $item = new Lists_item($item_entity);
            include(GSPLUGINPATH.self::$plugin_id.'/Lists_administration.php');
            if (array_key_exists('save', $_REQUEST)) {
                $administration = new Lists_administration(self::$storage, self::$configuration, $item);
            } elseif (array_key_exists('save', $_REQUEST)) {
            }
            $administration->render();
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

    public static function process_routing() {
        if (array_key_exists('Lists_settings', $_REQUEST)) {
            self::$plugin_info['page_type'] = 'settings';
        }
    }
}
