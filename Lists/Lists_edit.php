<?php

class Lists_edit {
    static protected $plugin_id = '';
    public static function set_plugin_id($id) {self::$plugin_id = $id;}
    static protected $page_list = '';
    public static function set_page_list($list) {self::$page_list = $list;}

    // dependencies
    protected $storage = null; // Lists_storage
    protected $settings = null; // Lists_settings
    protected $item = null; // Lists_item
    protected $message = null;
    protected $content_fields = null;

    protected $action = "";

    public static function factory() {
        if (!class_exists('Lists_message'))
            include(LISTS_PLUGIN_PATH.'/Lists_message.php');
        $message = Lists_message::get_instance();
        if (!class_exists('Lists_settings'))
            include(LISTS_PLUGIN_PATH.'/Lists_settings.php');
        $settings = Lists_settings::get_instance();
        $settings->read();
        if (!class_exists('Lists_storage'))
            include(LISTS_PLUGIN_PATH.'/Lists_storage.php');
        $storage = new Lists_storage();
        if (!class_exists('Lists_item_entity'))
            include(LISTS_PLUGIN_PATH.'/Lists_item_entity.php');
        $item_entity = Lists_item_entity::factory();
        // include(CONTENTFIELDS_PLUGIN_PATH.'ContentFields.php');
        ContentFields::initialize();
        $content_fields = new ContentFields($message);
        if (!class_exists('Lists_item'))
            include(LISTS_PLUGIN_PATH.'/Lists_item.php');
        $item = new Lists_item($item_entity, $settings, $content_fields, $message);
        $admin = new Lists_edit($storage, $item, $settings, $content_fields, $message);
        return $admin;
    }

    public function Lists_edit($storage, $item, $settings, $content_fields, $message) {
        $this->storage = $storage;
        $this->item = $item;
        $this->settings = $settings;
        $this->content_fields = $content_fields;
        $this->message = $message;
    }

    public function process() {
        // debug('_REQUEST', $_REQUEST);
        $this->render();
    } // Lists_edit::process()

    public function render() {
        //  TODO: in list mode show the whole page (action), in item edit mode only show the item
        if (!class_exists('Template')) {
            include(LISTS_PLUGIN_PATH.'/Template.php');
        }
        echo Template::factory()->
            set('plugin_id', Lists::get_plugin_id())->
            fetch(LISTS_TEMPLATE_PATH.'edit_list.php');
    } // Lists_settings::render()

} // Lists_edit
