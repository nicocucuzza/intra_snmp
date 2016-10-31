<?php

//Exceptions in all SNMP API
class SNMPAPIException extends Exception {
    const OPERATION_OK = 1000;
    const MANDATORY_PARAMETER_FAILED = 1001;
    const VERSION_MISMATCHED = 1002;
    const COMMAND_INVALID = 1003;
    const NO_CALLBACK = 1004;
    const INI_FILE_NOT_FOUND = 1005;
    const LOG_FILE_ERROR = 1006;

    public function SNMPAPIException($code, $message) {
        $this->code = $code;   
        $this->message = $message;     
    }

}


?>
