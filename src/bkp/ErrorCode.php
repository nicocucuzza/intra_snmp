<?php

class SNMPAPIException extends Exception {
    const OPERATION_OK = 0;
    const MANDATORY_PARAMETER_FAILED = 1;
    const VERSION_MISMATCHED = 2;

    public function SNMPAPIException($code, $message) {
        $this->code = $code;   
        $this->message = $message;     
    }

}


?>
