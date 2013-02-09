<?php
// in the CS css we only have notify updated and error as classes for notifications... should i adapt?
define('LISTSMESSAGE_MESSAGE', 'message');
define('LISTSMESSAGE_SUCCESS', 'success');
define('LISTSMESSAGE_WARNING', 'warning');
define('LISTSMESSAGE_ERROR', 'error');
class Lists_message {
    static protected $instance = null;

    protected $message = array();
    protected $success = array();
    protected $warning = array();
    protected $error = array();

    public static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new Lists_message();
        }
        return self::$instance;
    }

    public function Lists_message() {
    }

    public function add_message($message) {
        $this->message[] = $message;
    }

    public function add_success($success) {
        $this->success[] = $success;
    }

    public function add_warning($warning) {
        $this->warning[] = $warning;
    }

    public function add_error($error) {
        $this->error[] = $error;
    }

    public function has_error() {
        return empty($this->error);
    }

    public function get() {
        $result = array(
            LISTSMESSAGE_MESSAGE => $this->message,
            LISTSMESSAGE_SUCCESS => $this->success,
            LISTSMESSAGE_WARNING => $this->warning,
            LISTSMESSAGE_ERROR => $this->error,
        );
        return $result;
    }
}
