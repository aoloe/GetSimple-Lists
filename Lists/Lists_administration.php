<?php

class Lists_administration {
    static protected $plugin_id = '';
    public static function set_plugin_id($id) {self::$plugin_id = $id;}
    static protected $page_list = '';
    public static function set_page_list($list) {self::$page_list = $list;}

    // dependencies
    protected $storage = null; // Lists_storage
    protected $settings = null; // Lists_settings
    protected $item = null; // Lists_item
    protected $message = null;

    protected $action = "";

    public static function factory() {
        if (!class_exists('Lists_message'))
            include(GSPLUGINPATH.self::$plugin_id.'/Lists_message.php');
        $message = Lists_message::get_instance();
        if (!class_exists('Lists_settings'))
            include(GSPLUGINPATH.self::$plugin_id.'/Lists_settings.php');
        $settings = Lists_settings::get_instance();
        $settings->read();
        if (!class_exists('Lists_storage'))
            include(GSPLUGINPATH.self::$plugin_id.'/Lists_storage.php');
        $storage = new Lists_storage();
        if (!class_exists('Lists_item_entity'))
            include(GSPLUGINPATH.self::$plugin_id.'/Lists_item_entity.php');
        $item_entity = Lists_item_entity::factory();
        if (!class_exists('Lists_item'))
            include(GSPLUGINPATH.self::$plugin_id.'/Lists_item.php');
        $item = new Lists_item($item_entity, $settings, $message);
        $admin = new Lists_administration($storage, $item, $settings, $message);
        return $admin;
    }

    public function Lists_administration($storage, $item, $settings, $message) {
        $this->storage = $storage;
        $this->item = $item;
        $this->settings = $settings;
        $this->message = $message;
    }

    public function process() {
        // debug('_REQUEST', $_REQUEST);
        $this->item->read($_REQUEST, LISTSREQUESTPREFIX);
        if (!$this->item->has_valid_id() && array_key_exists('Lists_id', $_REQUEST)) {
            // TODO: read from the corresponding xml file
            if (is_string($_REQUEST['Lists_id']) && $this->settings->has_list($_REQUEST['Lists_id'])) {
                // debug('item', $this->item);
                $this->item->read($_REQUEST['Lists_id']);
            }
        }
        // debug('item', $this->item);
        if (array_key_exists('Lists_settings', $_REQUEST)) {
            if (array_key_exists('save', $_REQUEST)) {
                if ($this->validate()) {
                    if ($this->item->has_valid_id() || $this->item->generate_id()) {
                        $this->settings->set_list($this->item);
                        if ($this->settings->write() && $this->item->write()) {
                            $this->message->add_success(i18n_r('Lists/SETTINGS_SAVED'));
                        }
                    }
                }
            } elseif (array_key_exists('delete', $_REQUEST)) {
                if ($this->item->has_valid_id()) {
                    // debug('_REQUEST', $_REQUEST);
                    $this->item->delete();
                    $success = $this->settings->delete_list($this->item);
                    $sucess = $this->settings->write() && $success;
                    if ($success) {
                        $this->message->add_success(i18n_r('Lists/SETTINGS_DELETED'));
                    }
                    $this->item->clear();
                    unset($_REQUEST['Lists_settings']);
                }
            }
            $this->render();
        }
    }

    // TODO: in the gsconfig it is possible to set a specific salt! it's probably a bad idea to use it, since dynamic generated salts are much better!

    /*
    public function write($item = null) {
        // TODO: if it's a new one, add it to the list of elements and store the list
        // TODO: store the definition of this list
        if (is_null($item)) $item = $this->item;
        debug('item', $item);
        $this->settings->set_list($item);
    }
    */

    public function render() {
        if (!class_exists('Template')) {
            include(GSPLUGINPATH.Lists::get_plugin_id().'/Template.php');
        }
        $navigation = array (
            array (
                'action' => 'all',
                'label' => i18n_r('Lists/SETTINGS_NAVIGATIONVIEWALL'),
            ),
            array (
                'action' => 'new',
                'label' => i18n_r('Lists/SETTINGS_NAVIGATIONNEW'),
            ),
            array (
                'action' => 'settings',
                'label' => i18n_r('Lists/SETTINGS_NAVIGATIONSETTINGS'),
            ),
        );
        if ($this->item->has_valid_id()) {
            $navigation[] = array (
                'action' => 'edit',
                'label' => i18n_r('Lists/SETTINGS_NAVIGATIONDETAILS'),
            );
        }
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
            set('message', $this->message->get())->
            set('navigation_current', $section);
        echo $template->fetch(GSPLUGINPATH.Lists::get_plugin_id().'/template/settings_header.php');
        switch ($section) {
            case 'all' :
                echo $template->clear()->
                    set('plugin_id', Lists::get_plugin_id())->
                    set('list', $this->settings->get_list())->
                    set('url_self', 'http://ww.getsimple.org/admin/load.php?id=Lists&Lists_settings=edit')->

                    fetch(GSPLUGINPATH.Lists::get_plugin_id().'/template/settings_list.php');
            break;
            case 'new' :
            case 'edit' :
                // debug('page_list', self::$page_list);
                echo $template->clear()->
                    set('plugin_id', Lists::get_plugin_id())->
                    set('list_name', $this->item->get_title())->
                    set('id', $this->item->get_id())->
                    set('title', $this->item->get_title())->
                    set('page_list', self::$page_list)->
                    set('page_show', $this->item->get()->get_page_show())->
                    set('page_edit', $this->item->get()->get_page_create())->
                    set('field_editable', array())->
                    fetch(GSPLUGINPATH.Lists::get_plugin_id().'/template/settings_edit.php');
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

    public function validate_mandatory($field, $label) {
        $result = true;
        if (!array_key_exists($field, $_REQUEST) || $_REQUEST[$field] === '') {
            $this->message->add_error(sprintf(i18n_r('Lists/ERROR_EMPTY_FIELD'), $label));
            $result = false;
        }
        return $result;
    }

    public function validate() {
        $result = true;
        // debug('$_REQUEST', $_REQUEST);
        $result = $this->validate_mandatory('lists_item_title', i18n_r('Lists/FORM_LABEL_TITLE')) && $result;
        // $result = $this->validate_mandatory('lists_item_id', i18n_r('Lists/FORM_LABEL_ID')) && $result;
        return $result;
    } // Lists_administration::validate()

} // Lists_administration
