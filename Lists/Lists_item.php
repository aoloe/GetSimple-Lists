<?php

class Lists_item {
    protected $item = null; // Lists_item_entity

    public function Lists_item($entity) {
        $this->item = $entity;
    }

    /**
     * read the current item (if any), from the $_REQUEST or from the storoage
     */
    public function read($data, $data_prefix = '') {
        $this->item->read($data, $data_prefix);
    }

    public function write() {
        debug('item', $this->item);
   		$data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><specialpage></specialpage>');
        $data->addChild('id', $this->item->get_id());
        $data->addChild('title', $ithis->tem->get_title());
        $data->addChild('page_show', $ithis->tem->get_page_show());
        $data->addChild('page_create', $ithis->tem->get_page_create());
        $page_field = $data->addChild('page_field');
        foreach ($this->item->get_page_field() as $item) {
            $page_field->addChild($item);
        }
        debug('data', $data);
   		// return XMLsave($data, $file);
    }
}
