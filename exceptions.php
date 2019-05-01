<?php 

class EntryNotFoundException extends Exception {
    public function __construct(string $msg = null) {
        parent::__construct($msg);
    }
}

class DublicateEntryException extends Exception {
    public function __construct(string $msg = null) {
        parent::__construct($msg);
    }
}

?>