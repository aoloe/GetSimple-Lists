<?php

class Lists_settings {
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
                    set('name', '')->
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
} // Lists_settings
