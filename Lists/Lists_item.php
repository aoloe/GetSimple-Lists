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

    /**
     * read the current item (if any), from the $_REQUEST or from the storoage
     */
    public function read($data, $data_prefix = '') {
        $this->item->read($data, $data_prefix);
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

    public function write() {
        debug('item', $this->item);
   		$data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><specialpage></specialpage>');
        $data->addChild('id', $this->item->get_id());
        $data->addChild('title', $this->item->get_title());
        $data->addChild('page_show', $this->item->get_page_show());
        $data->addChild('page_create', $this->item->get_page_create());
        /*
        $show = $data->addChild('show');
        $show->addChild('page', $vvalue['show']['page']);
        $show->addChild('rule', $vvalue['show']['rule']);
        */
        $page_field = $data->addChild('page_field');
        foreach ($this->item->get_page_field() as $item) {
            $page_field->addChild($item);
        }
        debug('data', $data);
   		// return XMLsave($data, $file);
    }
}
