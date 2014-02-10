<?php

class Lists_list_entity extends GS_Entity {
    protected $id = '';
    protected $title = '';
    protected $page = '';
    protected $label = '';
    protected $order = '';
    protected $multipagetag = '';
    protected $frontend_create = '';
    protected $frontend_append = '';
    protected $page_field = array();
    protected $field = array();
    protected $entry = array();
    static public function factory() {
        return new Lists_list_entity();
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
