<?php

class Lists_item {
    protected $item = null; // Lists_item_entity
    protected $settings = null; // Lists_settings
    protected $message = null; // Lists_message

    public function Lists_item($entity, $settings, $message) {
        $this->item = $entity;
        $this->settings = $settings;
        $this->message = $message;
    }

    private function get_filename($id) {
        return LISTS_DATALISTSPATH.$id.'.xml';
    }

    /**
     * read the current item (if any), from the $_REQUEST or from the storoage
     * @param string/array $data the id of the item to be read from the file (string) or the data to be
     * read from the $_REQUEST (array)
     */
    public function read($data, $data_prefix = '') {
        $result = false;
        if (is_string($data)) {
            // debug('data', $data);
            $filename = $this->get_filename($data);
            // debug('filename', $filename);
            if (file_exists($filename)) {
                $list = getXML($filename);
                if (property_exists($list, 'settings')) {
                    $settings = $list->settings;
                    // debug('settings', $settings);
                    foreach (array('id', 'title', 'page_create', 'page_show') as $item) {
                        $setter = 'set_'.$item;
                        $this->item->$setter(property_exists($settings, $item) ? (string) $settings->$item : '');
                    }
                }
                if ($this->item->get_id() == $data) {
                    if (property_exists($list, 'fields')) {
                        // debug('fields', $list->fields);
                    }
                    if (property_exists($list, 'entries')) {
                        // debug('entries', $list->entries);
                    }
                    $result = true;
                }
            }
            if (!$result) {
                $this->message->add_error(sprintf(i18n_r('Lists/SETTINGS_ERROR_NOREADLIST'), $data));
            }
        } elseif (is_array($data)) {
            $this->item->read($data, $data_prefix);
            $result = true;
        }
        // debug('this->item', $this->item);
    }

    public function write() {
        $result = false;
        // debug('LISTSDATASETTINGS', LISTS_DATALISTSPATH);
        $filename = $this->get_filename($data);
        if (
            $this->has_valid_id() &&
            is_writable(
                file_exists($filename) ?
                $filename :
                LISTS_DATALISTSPATH
            )
        ) {
            // TODO: check if the activating mechanism has a hook where the current install can be checked
            // and directories can be created in others/
            // TODO: move to storage and add the undo
            // debug('item', $this->item);
            $data = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><list></list>');
            $settings = $data->addChild('settings');
            $settings->addChild('id', $this->item->get_id());
            $settings->addChild('title', $this->item->get_title());
            $settings->addChild('page_show', $this->item->get_page_show());
            $settings->addChild('page_create', $this->item->get_page_create());

            $fields = $data->addChild('fields');
            $entries = $data->addChild('entries');

            /*
            $page_field = $data->addChild('page_field');
            foreach ($this->item->get_page_field() as $item) {
                $page_field->addChild($item);
            }
            */
            // debug('data', $data);
            $result =  XMLsave($data, $filename);
            // debug('result', $result);
        } else {
            $this->message->add_error(sprintf(i18n_r('Lists/SETTINGS_ERROR_NOWRITESETTINGS')));
        }
        return $result;
    }

    public function has_valid_id() {
        $result = false;
        if (!$this->item->is_id('')) {
            if ($this->settings->has_list($this->item->get_id())) {
                $result = true;
            }
        }
        return $result;
    }

    private function get_char_random() {
        $result = '';
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_n = 62 - 1; // strlen($characters) - 1;
        // debug('characters_n', $characters_n);
        $result = $characters[mt_rand(0, $characters_n)];
        return $result;
    }

    private function get_string_random($length) {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $this->get_char_random();
        }
        return $result;
    }

    /**
     * generate an id, starting from the title, making it lower string, removing all the characters
     * that are not alphanumeric (only a-z and 0-9 allowed) and then adding random chars until an
     * id is found that is not yet in the settings (only 10 tries, it should be enough for now!)
     */
    public function generate_id() {
        $result = false;
        if (!$this->item->is_id('')) {
            $this->message->add_warning(sprintf(i18n_r('Lists/ITEM_WARNING_INVALIDID')));
        }
        $id = preg_replace("/[^a-z0-9]+/", "", strtolower($this->item->get_title()));
        $this->get_char_random();
        $i = 0;
        while ($this->settings->has_list($id) && ($i++ < 10)) {
            $id .= $this->get_char_random();
        } 
        if ($i < 10) {
            $this->item->set_id($id);
            $result = true;
        }
        return $result;
    }

    public function get_id() {
        return $this->item->get_id();
    }

    public function get_title() {
        return $this->item->get_title();
    }
}
