<?php
/**
 * Manages the general settings of the plugin (none for now) and the list of lists
 */

class Lists_settings {
    // dependencies
    static protected $instance = null;

    protected $storage = null;
    protected $settings = null;

    public static function get_instance() {
        if (is_null(self::$instance)) {
            // $message = GS_Message::get_instance();
            $storage = GS_Storage::get_instance();
            self::$instance = new Lists_settings($storage);
        }
        return self::$instance;
    }

    public function Lists_settings($storage) {
        $this->storage = $storage;
    }

    public function read($force = false) {
        if ($force || is_null($this->settings)) {
            $this->settings = array();
            $this->settings = array(
                'list' => array()
            );
            // TODO: add the number of entries of each list in settings
            if(is_readable(LISTS_DATA_SETTINGS)) {
                $settings = getXML(LISTS_DATA_SETTINGS);
                // GS_Debug('read() settings', $settings);
                if (property_exists($settings, 'lists') && property_exists($settings->lists, 'item')) {
                    if ($settings->lists->item->count() >= 1) {
                        // GS_Debug('list[]', $settings->lists->list);
                        foreach ($settings->lists->item as $item) {
                            // GS_Debug('item::', $item);
                            if (property_exists($item, 'id') && property_exists($item, 'title')) {
                                $this->settings['list'][(string) $item->id] = (string) $item->title;
                            }
                        }
                    } else {
                        $list = $settings->lists->list->children();
                        if (property_exists($list, 'id') && property_exists($list, 'title')) {
                            // GS_Debug('list', $list);
                            $this->settings['list'][(string) $list->id] = (string) $list->title;
                        }
                    }
                }
            } else {
                // echo("<pre>".print_r(debug_backtrace(), 1)."</pre>");
                GS_Message::get_instance()->add_warning(i18n_r('Lists/SETTINGS_ERROR_NOSETTINGS'));
            }
            // GS_Debug('read() this->settings', $this->settings);
        }
    }

    function undo() {
      return copy(LISTS_BACKUP_SETTINGS, LISTS_DATA_SETTINGS);
    }

    public function write() {
        $result = false;
        // GS_Debug('LISTS_DATA_SETTINGS', LISTS_DATA_SETTINGS);
        if (is_writable(dirname(LISTS_DATA_SETTINGS))) {
            GS_Debug('settings', $this->settings);
            // self::$settings = getXML(LISTS_DATA_SETTINGS);
            $data = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><List_settings></List_settings>');
            $list = $data->addChild('lists');
            foreach ($this->settings['list'] as $key => $value) {
                $item = $list->addChild('item');
                $item->addChild('id')->addCData(htmlspecialchars($key));
                $item->addChild('title')->addCData(htmlspecialchars($value));
            }
            GS_Debug('data', $data);
            // $result =  XMLsave($data, LISTS_DATA_SETTINGS);
            $result = $this->storage->write($data, LISTS_DATA_SETTINGS);
            // GS_Debug('result', $result);
        } else {
            GS_Message::get_instance()->add_error(i18n_r('Lists/SETTINGS_ERROR_NOWRITESETTINGS'));
        }
        GS_Debug('result', $result);
        return $result;
    }

    /**
     * @param Lists_list $list
     */
    public function set_list($list) {
        $this->settings['list'][$list->get_id()] = $list->get_title();
    }

    /**
     * @param Lists_list $list
     */
    public function delete_list($list) {
        if (array_key_exists($list->get_id(), $this->settings['list'])) {
            unset($this->settings['list'][$list->get_id()]);
        }
        // GS_Debug('list', $this->settings['list']);
    }

    public function has_list($id) {
        return isset($this->settings['list']) && array_key_exists($id, $this->settings['list']);
    }

    public function get_list() {
        return $this->settings['list'];
    }
}
