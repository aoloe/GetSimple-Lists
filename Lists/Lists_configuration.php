<?php

class Lists_configuration {
    // dependencies
    protected $storage = null;

    protected $configuration = null;

    public function List_configuration($storage) {
        $this->storage = $storage;
    }

    public function read() {
		if(is_readable(LISTSDATACONFIGURATION)) {
            $this->configuration = getXML(LISTSDATACONFIGURATION);
        }
        $this->configuration = array (
            'list' => array (
                'a' => array (
                    'id' => 'a',
                    'title' => 'Liste A',
                    'show' => array (
                        'page' => 'index',
                        'rule' => 'prepend', // prepend, append, replace
                    ),
                ),
                'b' => array (
                    'id' => 'b',
                    'title' => 'Liste B',
                    'show' => array (
                        'page' => 'index',
                        'rule' => 'append', // prepend, append, replace
                    ),
                ),
            ),
        );
        
    }

    public function get_list() {
        return $this->configuration['list'];
    }

    protected function write() {
        if (is_writable(dirname(LISTSDATACONFIGURATION))) {
            self::$configuration = getXML(LISTSDATACONFIGURATION);
            return XMLsave(self::$configuration, LISTSDATACONFIGURATION);
        }
    }
}
