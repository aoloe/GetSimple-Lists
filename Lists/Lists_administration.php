<?php

/**
 * This is the controller for the Lists administration.
 */

class Lists_administration {
    static protected $plugin_id = '';
    public static function set_plugin_id($id) {self::$plugin_id = $id;}
    static protected $page_list = '';
    public static function set_page_list($list) {self::$page_list = $list;}

    // dependencies
    protected $settings = null; // Lists_settings
    protected $list = null; // Lists_list
    protected $message = null;
    protected $content_fields = null;

    protected $action = "";

    public static function factory() {
        $message = GS_Message::get_instance();
        if (!class_exists('Lists_settings'))
            include(LISTS_PLUGIN_PATH.'/Lists_settings.php');
        $settings = Lists_settings::get_instance();
        $settings->read();
        if (!class_exists('Lists_list_entity'))
            include(LISTS_PLUGIN_PATH.'/Lists_list_entity.php');
        $list_entity = Lists_list_entity::factory();
        // include(CONTENTFIELDS_PLUGIN_PATH.'ContentFields.php');
        ContentFields::initialize();
        $content_fields = new ContentFields($message);
        if (!class_exists('Lists_list'))
            include(LISTS_PLUGIN_PATH.'/Lists_list.php');
        $list = new Lists_list($list_entity, $settings, $content_fields, $message);
        $admin = new Lists_administration($list, $settings, $content_fields, $message);
        return $admin;
    }

    public function Lists_administration($list, $settings, $content_fields, $message) {
        $this->list = $list;
        $this->settings = $settings;
        $this->content_fields = $content_fields;
        $this->message = $message;
    }

    public function process() {
        // GS_Debug('_REQUEST', $_REQUEST);
        $this->list->read($_REQUEST, LISTS_REQUEST_PREFIX);
        if (!$this->list->has_valid_id() && array_key_exists('Lists_id', $_REQUEST)) {
            // TODO: read from the corresponding xml file
            if (is_string($_REQUEST['Lists_id']) && $this->settings->has_list($_REQUEST['Lists_id'])) {
                // GS_Debug('list', $this->list);
                $this->list->read($_REQUEST['Lists_id']);
            }
        }
        // GS_Debug('list', $this->list);
        if (array_key_exists('Lists_administration', $_REQUEST)) {
            if (array_key_exists('save', $_REQUEST)) {
                if ($this->validate()) {
                    if ($this->list->has_valid_id() || $this->list->generate_id()) {
                        $this->settings->set_list($this->list);
                        // GS_Debug('list', $this->list);
                        if ($this->settings->write() && $this->list->write()) {
                            // TODO: only offer the undo if a backup file has been created (that is, there was already a settings file!
                            $this->message->add_success(i18n_r('Lists/ADMINISTRATION_SAVED').' <a href="load.php?id='.self::$plugin_id.'&Lists_administration&List_id='.$this->list->get_id().'&undo">'.i18n_r('UNDO').'</a>');
                        }
                    }
                }
            } elseif (array_key_exists('delete', $_REQUEST)) {
                if ($this->list->has_valid_id()) {
                    // GS_Debug('_REQUEST', $_REQUEST);
                    $this->list->delete();
                    $success = $this->settings->delete_list($this->list);
                    $sucess = $this->settings->write() && $success;
                    if ($success) {
                        $this->message->add_success(i18n_r('Lists/SETTINGS_DELETED'));
                    }
                    $this->list->clear();
                    unset($_REQUEST['Lists_administration']);
                }
            } elseif (array_key_exists('undo', $_REQUEST)) {
                if ($this->settings->write() && $this->list->undo()) {
                    $this->message->add_success(i18n_r('Lists/SETTINGS_RESTORED'));
                } else {
                    $this->message->add_success(i18n_r('Lists/SETTINGS_RESTOREFAILED'));
                }
            }
            $this->render();
        }
    } // Lists_administration::process()

    public function render() {
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
        if ($this->list->has_valid_id()) {
            $navigation[] = array (
                'action' => 'edit',
                'label' => i18n_r('Lists/SETTINGS_NAVIGATIONDETAILS'),
            );
        }
        $section = '';
        if (array_key_exists('Lists_administration', $_REQUEST)) {
            $section = $_REQUEST['Lists_administration'];
        }
        if ($section == '') {
            $section = $navigation[0]['action'];
        }
        // GS_Debug('section', $section);
        // GS_Debug('message', $this->message->get());
        $template = GS_Template::factory()->
            set('plugin_id', Lists::get_plugin_id())->
            set('navigation', $navigation)->
            set('message', GS_Message::get_instance()->get_rendered())->
            set('navigation_current', $section);
        echo $template->fetch(LISTS_TEMPLATE_PATH.'administration_header.php');
        // GS_Debug('section', $section);
        switch ($section) {
            case 'all' :
                GS_Debug('warning', 'take care of the hard coded url... here and at other places');
                echo $template->clear()->
                    set('plugin_id', Lists::get_plugin_id())->
                    set('list', $this->settings->get_list())->
                    set('url_self', 'http://ww.getsimple.org/admin/load.php?id=Lists&Lists_administration=edit')->

                    fetch(LISTS_TEMPLATE_PATH.'administration_list.php');
            break;
            case 'new' :
            case 'edit' :
                // GS_Debug('page_list', self::$page_list);
                // GS_Debug('field_list', $this->content_fields->get());
                $field_list = array();
                foreach ($this->content_fields->get() as $key => $value) {
                    $field_list[$value->get_name()] = $value->get_label();
                }
                echo $template->clear()->
                    set('plugin_id', Lists::get_plugin_id())->
                    set('list_name', $this->list->get_title())->
                    set('id', $this->list->get_id())->
                    set('title', $this->list->get_title())->
                    set('page_list', self::$page_list)->
                    set('page', $this->list->get()->get_page())->
                    set('field_list', $field_list)->
                    set('field_as_label', $this->list->get()->get_label())->
                    set('field_for_order', $this->list->get()->get_order())->
                    set('field_for_multipagetag', $this->list->get()->get_multipagetag())->
                    set('frontend_create', $this->list->get()->get_frontend_create())->
                    set('frontend_append', true || $this->list->get()->get_frontend_append())->
                    set('field_editable', array())-> // checkboxes
                    set('content_fields', $this->content_fields->render_admin_list())->
                    fetch(LISTS_TEMPLATE_PATH.'administration_edit.php');
            break;
        }
        /*
		$ImClass = new ItemsManager;
		if(file_exists(ITEMDATAFILE))
		{
		  $category_file = getXML(ITEMDATAFILE);
			$file_url = $category_file->list->pageurl;
			$file_title = $category_file->list->title;
			$file_page = $category_file->list->pageurl;
			$file_page_details = $category_file->list->detailspage;
			$file_label_field = $category_file->list->labelfield;
			$file_results_page = $category_file->list->resultspage;
		}
        */
    } // Lists_administration::render()

    // TODO: move to a Validate class?
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
        // GS_Debug('$_REQUEST', $_REQUEST);
        $result = $this->validate_mandatory('lists_list_title', i18n_r('Lists/FORM_LABEL_TITLE')) && $result;
        // $result = $this->validate_mandatory('lists_list_id', i18n_r('Lists/FORM_LABEL_ID')) && $result;
        return $result;
    } // Lists_administration::validate()

} // Lists_administration
