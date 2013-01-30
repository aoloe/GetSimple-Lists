<?php

class Lists_item {
    protected $item = null; // Lists_item_entity

    public function Lists_item() {
    }

    /**
     * read the current item (if any), from the $_REQUEST or from the storoage
     */
    public function read($data, $data_prefix = '') {
        if (!class_exists('Entity')) {
            include(GSPLUGINPATH.Lists::get_plugin_id().'/Entity.php');
        }
        if (!class_exists('Lists_item_entity')) {
            include_once(GSPLUGINPATH.Lists::get_plugin_id().'/Lists_item_entity.php');
        }
        $this->item = Lists_item_entity::factory()->read($data, $data_prefix);
    }
}
