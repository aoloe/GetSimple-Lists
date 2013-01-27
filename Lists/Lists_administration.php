<?php

class Lists_administration {
    // dependencies
    protected $storage = null; // Lists_storage
    protected $settings = null; // Lists_settings

    protected $message = array();
    protected $action = "";
    protected $item = null; // Lists_item_entity

    public function Lists_administration($storage, $settings) {
        $this->storage = $storage;
        $this->settings = $settings;
    }


    public function execute() {
        $this->read();
        if (array_key_exists('save', $_REQUEST)) {
            if ($this->validate()) {
                $this->write();
            }
        }
    }

    public function render() {
        if (!class_exists('Template')) {
            include(GSPLUGINPATH.Lists::get_plugin_id().'/Template.php');
        }
        $navigation = array (
            array (
                'action' => 'all',
                'label' => i18n_r('Lists/SETTINGS_VIEWALL'),
            ),
            array (
                'action' => 'new',
                'label' => i18n_r('Lists/SETTINGS_NEW'),
            ),
            array (
                'action' => 'settings',
                'label' => i18n_r('Lists/SETTINGS_SETTINGS'),
            ),
        );
        $section = '';
        if (array_key_exists(Lists::get_plugin_id().'_settings', $_REQUEST)) {
            $section = $_REQUEST[Lists::get_plugin_id().'_settings'];
        }
        if ($section == '') {
            $section = $navigation[0]['action'];
        }
        $template = Template::factory()->
            set('plugin_id', Lists::get_plugin_id())->
            set('navigation', $navigation)->
            set('success', empty($this->message))->
            set('message', $this->message)->
            set('navigation_current', $section);
        echo $template->fetch(GSPLUGINPATH.Lists::get_plugin_id().'/template/settings_header.php');
        switch ($section) {
            case 'all' :
                echo $template->clear()->
                    set('plugin_id', Lists::get_plugin_id())->
                    fetch(GSPLUGINPATH.Lists::get_plugin_id().'/template/settings_list.php');
            break;
            case 'new' :
                echo $template->clear()->
                    set('plugin_id', Lists::get_plugin_id())->
                    set('id', '')->
                    set('title', '')->
                    set('field_editable', array())->
                    fetch(GSPLUGINPATH.Lists::get_plugin_id().'/template/settings_new.php');
            break;
        }
        /*
        global $contentfields; // TODO: use an object parameter as soon as we have an object...
		$ImClass = new ItemsManager;
		if(file_exists(ITEMDATAFILE))
		{
		  $category_file = getXML(ITEMDATAFILE);
			$file_url = $category_file->item->pageurl;
			$file_title = $category_file->item->title;
			$file_page = $category_file->item->pageurl;
			$file_page_details = $category_file->item->detailspage;
			$file_label_field = $category_file->item->labelfield;
			$file_results_page = $category_file->item->resultspage;
		}
        */
    } // Lists_settings::render()

    /**
     * read the current item (if any), from the $_REQUEST or from the storoage
     */
    public function read() {
        $result = false;
        if (array_key_exists('save', $_REQUEST)) {
            if (!class_exists('Entity')) {
                include(GSPLUGINPATH.Lists::get_plugin_id().'/Entity.php');
            }
            if (!class_exists('Lists_item_entity')) {
                include_once(GSPLUGINPATH.Lists::get_plugin_id().'/Lists_item_entity.php');
            }
            $this->item = Lists_item_entity::factory()->read($_REQUEST, 'lists_item_');
            $result = true;
        } elseif (array_key_exists('lists_id', $_REQUEST)) {
        }
        if (isset($this->item) && $this->item->is_id("") && !$this->item->is_title("")) {
            $id = preg_replace('/^[A-Za-z0-9]+$/', '', $this->item->get_title());
            while ($id == "" || array_key_exists($id, $this->list)) {
                $id = "";
            }
            $this->item->set_id($id);
        }
        return $result;
    }

    public function write($item = null) {
        debug('item', $this->item);
   		$data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><specialpage></specialpage>');
        $data->addChild('id', $this->item->get_id());
        $data->addChild('title', $this->item->get_title());
        $data->addChild('page_show', $this->item->get_page_show());
        $data->addChild('page_create', $this->item->get_page_create());
        $page_field = $data->addChild('page_field');
        foreach ($this->item->get_page_field() as $item) {
            $page_field->addChild($item);
        }
        debug('data', $data);
   		// return XMLsave($data, $file);
    }

    public function validate_mandatory($field, $label) {
        $result = true;
        if (!array_key_exists($field, $_REQUEST) || $_REQUEST[$field] === '') {
            $this->message[] = sprintf(i18n_r('Lists/ERROR_EMPTY_FIELD'), $label);
            $result = false;
        }
        return $result;
    }

    public function validate() {
        $result = true;
        debug('$_REQUEST', $_REQUEST);
        $result = $this->validate_mandatory('lists_item_title', i18n_r('Lists/FORM_LABEL_TITLE')) && $result;
        // $result = $this->validate_mandatory('lists_item_id', i18n_r('Lists/FORM_LABEL_ID')) && $result;
        return $result;
    } // Lists_administration::validate()

} // Lists_administration
