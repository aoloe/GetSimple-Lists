<?php

class Lists_item_entity extends Entity {
    protected $id = '';
    protected $title = '';
    protected $page_show = '';
    protected $page_create = '';
    protected $page_field = array();
    protected $field = array();
    protected $entry = array();
    static public function factory() {
        return new Lists_item_entity();
    }

    /*
    public function read($e, $prefix = '') {
        parent::read($e, $prefix);
        if ($this->is_id("") && !$this->is_title("")) {
            $id = preg_replace('/^[A-Za-z0-9]+$/', '', $this->get_title());
            while ($id == "" || array_key_exists($id, $this->list)) {
                $id = "";
            }
            $this->item->set_id($id);
        }
    }
    */
}
