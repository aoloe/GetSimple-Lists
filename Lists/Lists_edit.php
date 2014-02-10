<?php
/**
 * edit the item in the list
 */

class Lists_edit {
    static protected $plugin_id = '';
    public static function set_plugin_id($id) {self::$plugin_id = $id;}
    static protected $page_list = '';
    public static function set_page_list($list) {self::$page_list = $list;}

    // dependencies
    protected $settings = null; // Lists_settings
    protected $list = null; // Lists_list
    protected $content_fields = null;

    protected $action = "";

    public static function factory() {
        if (!class_exists('Lists_settings'))
            include(LISTS_PLUGIN_PATH.'/Lists_settings.php');
        $settings = Lists_settings::get_instance();
        $settings->read();
        if (!class_exists('Lists_list_entity'))
            include(LISTS_PLUGIN_PATH.'/Lists_list_entity.php');
        $list_entity = Lists_list_entity::factory();
        // include(CONTENTFIELDS_PLUGIN_PATH.'ContentFields.php');
        ContentFields::initialize();
        $content_fields = new ContentFields();
        if (!class_exists('Lists_list'))
            include(LISTS_PLUGIN_PATH.'/Lists_list.php');
        $list = new Lists_list($list_entity, $settings, $content_fields);
        $edit = new Lists_edit($list, $settings, $content_fields);
        return $edit;
    }

    public function Lists_edit($list, $settings, $content_fields) {
        $this->list = $list;
        $this->settings = $settings;
        $this->content_fields = $content_fields;
    }

    public function process() {
        GS_debug('_REQUEST', $_REQUEST);
        $page_id = Lists::get_page_id();
        // debug('page_id', $page_id);
        $list_cache = Lists::get_cache();
        // debug('list_cache', $list_cache);
        if (
            array_key_exists('Lists_edit', $_REQUEST) &&
            array_key_exists('Lists_id', $_REQUEST) &&
            array_key_exists($_REQUEST['Lists_id'], $list_cache['list'])
        ) {
            $this->list->read($_REQUEST['Lists_id']);
            // debug('list', $this->list);
            $list_list_id = null;
            if (array_key_exists('Lists_list_id', $_REQUEST)) {
                $list_list_id = $_REQUEST['Lists_list_id'];
            }

            $this->content_fields->read($_REQUEST['Lists_id']);
            if (array_key_exists('save', $_REQUEST)) {
                // TODO: for the _REQUEST put the fields value into Lists_Contentfields_ (fix the case?)
                // TODO: read the list_list_id, too
                // TODO: save the values to the xml file!
                if (isset($list_list_id)) {
                } else {
                }
                $content = $this->content_fields->get_content(Lists::get_plugin_id().'_'.$this->list->get_id());
                GS_debug('content', $content);
                if ($item = $this->content_fields->get_content($_REQUEST, LISTS_REQUEST_PREFIX.CONTENTFIELDS_REQUEST_FIELD_PREFIX)) {
                    $this->content_fields->set_item($content, $item);
                    // $this->content->add(); TODO: should we also have a ContentFields_item?
                    $this->content_fields->write_content(Lists::get_plugin_id().'_'.$this->list->get_id(), $content);
                }
            }
            // debug('content_fields', $this->content_fields);

            $this->render_item($list_list_id);
        } elseif (array_key_exists($page_id, $list_cache['page'])) {
            $this->list->read($list_cache['page'][$page_id]);
            $this->render_list();
        }
    } // Lists_edit::process()

    public function render_list() {
        //  TODO: in list mode show the whole page (action), in list edit mode only show the list
        // debug('list', $this->list);
        GS_debug('content_fields', $this->content_fields);
        // debug('list_id', $this->list->get_id());

        $lists_rows = array();
        foreach ($this->list->get() as $item) {
            $lists_rows[] = GS_Template::factory()->
                set('plugin_id', Lists::get_plugin_id())->
                set('list_id', $this->list->get_id())->
                set('sortable', $item->is_sortable(''))->
                set('list_list_id', '1' /* $item->get_id( */)->
                set('label', 'test label' /* $item->get_label( */)->
                set('date_change', '' /* $item->get_label( */)->
                set('visible', true /* $item->get_label( */)->
                set('promotion', true /* $item->get_label( */)->
                set('lists_title', '' /* $item->get_label( */)->
                fetch(LISTS_TEMPLATE_PATH.'edit_list.php');
        }


        echo GS_Template::factory()->
            set('page_id', Lists::get_page_id())->
            set('plugin_id', Lists::get_plugin_id())->
            set('list_id', $this->list->get_id())->
            set('title', 'Projects')->
            set('rows', empty($lists_rows) ? '<tr><td colspan="5">'.i18n_r('Lists/EDIT_NOITEMFOUND').'</td></tr>' : implode("\n", $lists_rows))->
            set('rows_count', count($lists_rows))->
            fetch(LISTS_TEMPLATE_PATH.'edit_list.php');
    } // Lists_settings::render_list()

    public function render_item() {
        // TODO: in list mode show the whole page (action), in list edit mode only show the list
        // TODO: would it be a good idea to implement this as ContentFields::render_edit()?
        // debug('list', $this->list);
        // debug('content_fields', $this->content_fields);
        // debug('content_fields', $this->content_fields->get());
        // debug('list_id', $this->list->get_id());
        $list_hidden = array();
        // TODO: add the hidden List_list_id in order to be able to know what list is being edited
        $hidden_fields = array (
            ContentFields_entity::factory()->set_type('hidden')->set_name('id')->set_value(Lists::get_plugin_id()),
            ContentFields_entity::factory()->set_type('hidden')->set_name(Lists::get_plugin_id().'_edit')->set_value('edit'),
            ContentFields_entity::factory()->set_type('hidden')->set_name(Lists::get_plugin_id().'_id')->set_value($this->list->get_id()),
            ContentFields_entity::factory()->set_type('hidden')->set_name(Lists::get_plugin_id().'_edit')->set_value('edit'),
        );
        if (false /* TODO: isset($list_id)) */) {
            $hidden_fields[] = ContentFields_entity::factory()->set_type('hidden')->set_name(Lists::get_plugin_id().'list_id')->set_value($list_id);
        }
        foreach ($hidden_fields as $item) {
            $list_hidden[] = $item->render();
        }
        // debug('hidden_fields', $hidden_fields);
        $list_field = array();
        foreach ($this->content_fields->get() as $item) {
            // debug('item', $item);
            if ($item->is_type('hidden')) {
                $list_hidden[] = $item->render(LISTS_REQUEST_PREFIX.CONTENTFIELDS_REQUEST_FIELD_PREFIX);
            } else {
                $list_field[] = $item->render(LISTS_REQUEST_PREFIX.CONTENTFIELDS_REQUEST_FIELD_PREFIX);
            }
        }
        // debug('list_field', $list_field);
        // debug('list_hidden', $list_hidden);

        echo GS_Template::factory()->
            set('plugin_id', Lists::get_plugin_id())->
            set('plugin_list_id', $this->list->get_id())->
            set('list_hidden', implode("\n", $list_hidden))->
            set('list_field', empty($list_field) ? array('<td colspan="5">'.i18n_r('Lists/EDIT_NOITEMFOUND').'</td>') : $list_field)->
            fetch(CONTENTFIELDS_TEMPLATE_PATH.'edit_list.php');
    } // Lists_edit::render_item()

} // Lists_edit
