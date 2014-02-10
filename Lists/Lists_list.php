<?php

class Lists_list {
    protected $list = null; // Lists_list_entity
    protected $settings = null; // Lists_settings
    protected $content_fields = null; // Lists_settings

    static function factory() {
        if (!class_exists('Lists_settings'))
            include(LISTS_PLUGIN_PATH.'/Lists_settings.php');
        $settings = Lists_settings::get_instance();
        $settings->read();
        if (!class_exists('Lists_list_entity'))
            include(LISTS_PLUGIN_PATH.'/Lists_list_entity.php');
        $list_entity = Lists_list_entity::factory();
        // include(GSPLUGINPATH.'ContentFields/ContentFields.php');
        ContentFields::initialize();
        $content_fields = new ContentFields();
        return new Lists_list($list_entity, $settings, $content_fields);
    }


    public function Lists_list($entity, $settings, $content_fields) {
        $this->list = $entity;
        $this->settings = $settings;
        $this->content_fields = $content_fields;
    }

    private function get_filename($id) {
        return LISTS_DATA_LIST_PATH.$id.'.xml';
    }

    /**
     * read the current list (if any), from the $_REQUEST or from the storage
     * @param string/array $data the id of the list to be read from the file (string) or the data to be
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
                    foreach (array('id', 'title', 'page', 'label', 'order', 'multipagetag', 'frontend_create', 'frontend_append') as $item) {
                        $setter = 'set_'.$item;
                        $this->list->$setter(property_exists($settings, $item) ? (string) $settings->$item : '');
                    }
                }
                if ($this->list->get_id() == $data) {
                    if (property_exists($list, 'field')) {
                        // debug('field', $list->field);
                    }
                    if (property_exists($list, 'entry')) {
                        // debug('entry', $list->entry);
                    }
                    $result = true;
                }
                $this->content_fields->read(Lists::get_plugin_id().'_'.$this->list->get_id());
            }
            if (!$result) {
                GS_Message::get_instance()->add_error(sprintf(i18n_r('Lists/SETTINGS_ERROR_NOREADLIST'), $data));
            }
        } elseif (is_array($data)) {
            // debug('data', $data);
            // debug('data_prefix', $data_prefix);
            $this->list->read($data, $data_prefix);
            $result = true;
        }
        // debug('this->list', $this->list);
    }

    // for now, i don't see a better way to solve this... let's return the entity
    public function get() {
        return $this->list;
    }

    public function clear() {
        $this->list->
            set_id('')->
            set_title('')->
            set_page('')->
            set_frontend_create('')->
            set_field(array())->
            set_entry(array());
    }

    function undo() {
        $result = false;
        // read LISTS_BACKUP_LIST and 
        if ($this->read(LISTS_BACKUP_LIST)) {
            // debug('list', $this->list);
            $filename = $this->get_filename($this->list->get_id());
            // debug('filename', $filename);
            $result = copy(LISTS_BACKUP_LIST, $filename);
        }
        return $result;
    }

    public function write() {
        // TODO: add the undo
        // if (!copy(GSDATAOTHERPATH . IM_CUSTOMFIELDS_FILE, GSBACKUPSPATH . 'other/' . IM_CUSTOMFIELDS_FILE)) return false;
        $result = false;
        // debug('LISTS_DATA_SETTINGS', LISTS_DATA_LIST_PATH);
        $filename = $this->get_filename($this->list->get_id());
        if (
            $this->has_valid_id() &&
            is_writable(
                file_exists($filename) ?
                $filename :
                LISTS_DATA_LIST_PATH
            )
        ) {
            $this->content_fields->write(Lists::get_plugin_id().'_'.$this->list->get_id());
            // TODO: check if the activating mechanism has a hook where the current install can be checked
            // and directories can be created in others/
            // TODO: move to storage and add the undo
            // debug('list', $this->list);
            $data = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><list></list>');
            $settings = $data->addChild('settings');
            $settings->addChild('id')->addCData(htmlspecialchars($this->list->get_id()));
            $settings->addChild('title')->addCData(htmlspecialchars($this->list->get_title()));
            $settings->addChild('page')->addCData(htmlspecialchars($this->list->get_page()));
            $settings->addChild('label')->addCData(htmlspecialchars($this->list->get_label()));
            $settings->addChild('order')->addCData(htmlspecialchars($this->list->get_order()));
            $settings->addChild('multipagetag')->addCData(htmlspecialchars($this->list->get_multipagetag()));
            $settings->addChild('frontend_create')->addCData(htmlspecialchars($this->list->get_frontend_create()));
            $settings->addChild('frontend_append')->addCData(htmlspecialchars($this->list->get_frontend_append()));

            $fields = $data->addChild('fields');
            $entries = $data->addChild('entries');

            /*
            $page_field = $data->addChild('page_field');
            foreach ($this->list->get_page_field() as $item) {
                $page_field->addChild($item);
            }
            */
            // debug('data', $data);
            $result =  XMLsave($data, $filename);
            // debug('result', $result);
        } else {
            trigger_error("Cannot write ".$filename);
            GS_Message::get_instance()->add_error(sprintf(i18n_r('Lists/SETTINGS_ERROR_NOWRITESETTINGS')));
        }
        return $result;
    }

    public function delete() {
        $result = false;
        // debug('list', $this->list);
        $filename = $this->get_filename($this->list->get_id());
        // debug('filename', $filename);
        if (is_writable($filename)) {
            $result = unlink($filename);
        }
        return $result;
    }

    public function has_valid_id() {
        $result = false;
        if (!$this->list->is_id('')) {
            if ($this->settings->has_list($this->list->get_id())) {
                $result = true;
            }
        }
        return $result;
    }

    // TODO: the methods below should be moved to a generic GS class
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
        if (!$this->list->is_id('')) {
            GS_Message::get_instance()->add_warning(sprintf(i18n_r('Lists/ITEM_WARNING_INVALIDID')));
        }
        $id = preg_replace("/[^a-z0-9]+/", "", strtolower($this->list->get_title()));
        $this->get_char_random();
        $i = 0;
        while ($this->settings->has_list($id) && ($i++ < 10)) {
            $id .= $this->get_char_random();
        } 
        if ($i < 10) {
            $this->list->set_id($id);
            $result = true;
        }
        return $result;
    }

    public function get_id() {
        return $this->list->get_id();
    }

    public function get_title() {
        return $this->list->get_title();
    }
}
