<?php
/**
 * The Lists class is just a glue class, setting up the environment, getting the calls
 * from the main plugin's file and dispatching them to objects doing the real work.
 * It's also doing controller and routing tasks.
 *
 * There is some old (unused in this class). In the future it could become a class with common
 * code to the different parts... but, more likely, it will disappear
 */
class Lists {
    static protected $plugin_id = '';
    public static function set_plugin_id($id) {self::$plugin_id = $id;}
    public static function get_plugin_id() {return self::$plugin_id;}
    static protected $plugin_info = array();
    public static function set_plugin_info(& $plugin_info) {self::$plugin_info = & $plugin_info;}
    static protected $page_id = '';
    public static function set_page_id($id) {self::$page_id = $id;}
    public static function get_page_id() {return self::$page_id;}
    static protected $cache = null;
    public static function get_cache() {return self::$cache;}
    static protected $settings = null;

    // TODO: is it really needed?
    public static function initialize() {
        include(LISTS_PLUGIN_PATH.'/Lists_settings.php');
        if (!class_exists('Entity'))
            include(LISTS_PLUGIN_PATH.'/Entity.php');
        include(LISTS_PLUGIN_PATH.'/Lists_list_entity.php');
        self::$settings = Lists_settings::get_instance();
        self::$settings->read();
    } // Lists::initialize()

    /**
     * read the cache with the information needed by the Lists plugin to route itself
     */
    public static function read_cache($data = null) {
        if (is_null(self::$cache)) {
            if (isset($data) || is_readable(LISTS_CACHE_FILE)) {
                if (is_null($data)) {
                    $data = getXML(LISTS_CACHE_FILE);
                }
                // debug('data', $data);
                $cache = array (
                    'list' => array(),
                    'page' => array(),
                    'global' => array(),
                );
                foreach ($data->list->item as $item) {
                    $cache['list'][(string) $item->list_id] = (string) $item->title;
                }
                foreach ($data->page->item as $item) {
                    $cache['page'][(string) $item->page_id] = (string) $item->list_id;
                }
                foreach ($data->global->list_id as $item) {
                    $cache['global'][] = (string) $item;
                }
                // debug('cache', $cache);
                self::$cache = $cache;
            } else {
                self::write_cache();
            }
        }
        // debug('cache', self::$cache);
    } // Lists::read_cache()

    /**
     * create a cache with the information needed by the Lists plugin to route itself
     */
    public static function write_cache($list_name = null) {
        // debug('LISTS_CACHE_FILE', LISTS_CACHE_FILE);
        // debug('LISTS_CACHE_PATH', LISTS_CACHE_PATH);
        self::$cache = array(
            'list' => array(),
            'page' => array(),
            'global' => array(),
        );

        $data = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><cache></cache>');
        $data_list = $data->addChild('list'); // list of the lists (list_id => title)
        $data_page = $data->addChild('page'); // pages with a list (page_id => list_id)
        $data_global = $data->addChild('global'); // lists not attached to a page (list_id)

        include_once(LISTS_PLUGIN_PATH.'/Lists_settings.php');
        $settings = Lists_settings::get_instance();
        $settings->read();
        include_once(LISTS_PLUGIN_PATH.'/Lists_list.php');
        $list_list = Lists_list::factory();
        // debug('settings', $settings);
        $list_name = (
            is_null($list_name) ?
            array_keys($settings->get_list()) :
            (
                is_string($list_name) ?
                array($list_name) :
                $list_name
            )
        );
        foreach ($list_name as $item) {
            $list_list->read($item);
            $entity = $list_list->get();
            // debug('entity', $entity);
            $data_list = $data_list->addChild('item');
            $data_list->addChild('list_id')->addCData(htmlspecialchars($item));
            $data_list->addChild('title')->addCData(htmlspecialchars($entity->get_title()));
            if ($page = $entity->get_page()) {
                $data_list = $data_page->addChild('item');
                $data_list->addChild('page_id')->addCData(htmlspecialchars($page));
                $data_list->addChild('list_id')->addCData(htmlspecialchars($item));
            } else {
                $data_global->addChild('list_id')->addCData(htmlspecialchars($item));
            }
        }
        // debug('data', $data);
        GS_Debug('LISTS_CACHE_FILE', LISTS_CACHE_FILE);
        GS_Debug('is_writable(LISTS_CACHE_FILE)', is_writable(LISTS_CACHE_FILE));
        GS_Debug('LISTS_CACHE_PATH', LISTS_CACHE_PATH);
        GS_Debug('is_writable(LISTS_CACHE_PATH)', is_writable(LISTS_CACHE_PATH));
        if (is_writable(LISTS_CACHE_FILE) || is_writable(LISTS_CACHE_PATH)) {
            if (!XMLsave($data, LISTS_CACHE_FILE)) {
                trigger_error("Cannot write ".LISTS_CACHE_FILE);
                GS_Message::get_instance()->add_error(sprintf(i18n_r('Lists/ERROR_CACHENOWRITE')));
                self::$cache = null;
            }
        } else { // if is_writable cache
            trigger_error("Cannot write ".LISTS_CACHE_FILE);
            GS_Message::get_instance()->add_error(sprintf(i18n_r('Lists/ERROR_CACHENOWRITE')));
            self::$cache = null;
        } // else is_writable cache

        if (isset($data)) {
            self::read_cache($data);
        }
    } // Lists::write_cache()

    // TODO: very likely, move this to Lists_show.php
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

    public static function tabs_routing() {
        if (array_key_exists('Lists_settings', $_REQUEST) || array_key_exists('Lists_administration', $_REQUEST)) {
            self::$plugin_info['page_type'] = 'settings';
        }
    }
}
