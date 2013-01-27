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
}
