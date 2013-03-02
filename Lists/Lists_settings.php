<?php

class Lists_settings {
    // dependencies
    static protected $instance = null;

    protected $storage = null;
    protected $message = null;

    protected $settings = null;

    public static function get_instance() {
        if (is_null(self::$instance)) {
            if (!class_exists('Lists_storage'))
                include(LISTS_PLUGIN_PATH.'/Lists_storage.php');
            $storage = new Lists_storage();
            if (!class_exists('Lists_message'))
                include(LISTS_PLUGIN_PATH.'/Lists_message.php');
            $message = Lists_message::get_instance();
            self::$instance = new Lists_settings($storage, $message);
        }
        return self::$instance;
    }

    public function Lists_settings($storage, $message) {
        $this->storage = $storage;
        $this->message = $message;
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
                // debug('read() settings', $settings);
                if (property_exists($settings, 'list')) {
                    if ($settings->list->item->count() > 1) {
                        // debug('item[]', $settings->list->item);
                        foreach ($settings->list->item as $item) {
                            // debug('item::', $item);
                            if (property_exists($item, 'id') && property_exists($item, 'title')) {
                                $this->settings['list'][(string) $item->id] = (string) $item->title;
                            }
                        }
                    } else {
                        $item = $settings->list->item->children();
                        if (property_exists($item, 'id') && property_exists($item, 'title')) {
                            // debug('item', $item);
                            $this->settings['list'][(string) $item->id] = (string) $item->title;
                        }
                    }
                }
            } else {
                // debug('message', $this->message);
                // echo("<pre>".print_r(debug_backtrace(), 1)."</pre>");
                $this->message->add_warning(i18n_r('Lists/SETTINGS_ERROR_NOSETTINGS'));
            }
            // debug('read() this->settings', $this->settings);
        }

        /*
        $this->settings = array (
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
        */

    }

    function undo() {
      return copy(LISTS_BACKUP_SETTINGS, LISTS_DATA_SETTINGS);
    }

    public function write() {
        $result = false;
        // debug('LISTS_DATA_SETTINGS', LISTS_DATA_SETTINGS);
        if (is_writable(dirname(LISTS_DATA_SETTINGS))) {
            // debug('settings', $this->settings);
            // self::$settings = getXML(LISTS_DATA_SETTINGS);
            $data = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><List_settings></List_settings>');
            $list = $data->addChild('list');
            foreach ($this->settings['list'] as $key => $value) {
                $item = $list->addChild('item');
                $item->addChild('id')->addCData(htmlspecialchars($key));
                $item->addChild('title')->addCData(htmlspecialchars($value));
            }
            // debug('data', $data);
            // $result = true;
            $result =  XMLsave($data, LISTS_DATA_SETTINGS);
            // debug('result', $result);
        } else {
            $this->message->add_error(i18n_r('Lists/SETTINGS_ERROR_NOWRITESETTINGS'));
        }
        return $result;
    }

    /**
     * @param Lists_item $item
     */
    public function set_list($item) {
        $this->settings['list'][$item->get_id()] = $item->get_title();
    }

    /**
     * @param Lists_item $item
     */
    public function delete_list($item) {
        if (array_key_exists($item->get_id(), $this->settings['list'])) {
            unset($this->settings['list'][$item->get_id()]);
        }
        // debug('list', $this->settings['list']);
    }

    public function has_list($id) {
        return isset($this->settings['list']) && array_key_exists($id, $this->settings['list']);
    }

    public function get_list() {
        return $this->settings['list'];
    }
}
