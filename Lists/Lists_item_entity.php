<?php

class Lists_item_entity extends Entity {
    protected $id = '';
    protected $title = '';
    protected $page_show = '';
    protected $page_create = '';
    protected $page_field = array();
    static public function factory() {
        return new Lists_item_entity();
    }

    public function read() {
        parent::read();
        if ($this->item->is_id("") && !$this->item->is_title("")) {
            $id = preg_replace('/^[A-Za-z0-9]+$/', '', $this->item->get_title());
            while ($id == "" || array_key_exists($id, $this->list)) {
                $id = "";
            }
            $this->item->set_id($id);
        }
    }
}
