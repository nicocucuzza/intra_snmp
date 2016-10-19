<?php
//  example request: http://path/to/resource/Example?method=sayHello&name=World
require_once "RestServer.php";
$rest = new RestServer(SnmpApi);

print_r($_REQUEST);
$json = json_decode($_REQUEST);
print_r($json);
$rest->handle();

class Operations {
    const GET = "Get";
    const GETNEXT = "GetNext";
    const WALK = "Walk";
    const SET = "Set";
}

class Regex {
    const REGEX_GET = "/get/i";
    const REGEX_GETNEXT = "/getnext/i";
    const REGEX_WALK = "/walk/i";
    const REGEX_SET = "/set/i";

}

class SNMPVersion {
    const V1 = "1";
    const V2C = "2C";
}

class ErrorCode {
    const NO_OPERATION_ERROR = 1001;

}

class SnmpApi {

    private static function getCommand($command) {

        //Applies regex match to decide if command provided in request is a valid one
        if ( preg_match(Regex::REGEX_GET,$command) == 1)
            return Operations::GET;
        if (preg_match(Regex::REGEX_GETNEXT,$command) == 1)
            return Operations::GETNEXT;
        if (preg_match(Regex::REGEX_WALK,$command) == 1)
            return Operations::WALK;
        if (preg_match(Regex::REGEX_SET,$command) == 1)
            return Operations::SET;

        // In case of no match in any command, return the error code associated
        return NO_OPERATION_ERROR;
    }

    private static function v1executeCommand($command,$hostname,$oid,$community) {
        $response = "";

        switch ($command) {
        case Operations::GET:
            $response = snmpget($hostname,$community,$oid);
            break;
        case Operations::GETNEXT:
            $response = snmpgetnext($hostname,$community,$oid);
            break;    
        case Operations::WALK:
            $response = snmpwalk($hostname,$community,$oid);
            break;
        case Operations::SET:
            break;
        default:
            break;
        }
        return $response;

    }
    public static function cmd($command, $version, $hostname, $oid, $community,$timeout=1000000,$retries=5,$type ="=", $description = "", $async = false, $callback_url = "") {
        // Applies a regex to obtain the command executed
        $command = SnmpApi::getCommand($command);

        // Execute SNMP command using the right version (provided in the request)
        if($version == SNMPVersion::V1)
            $response = v1executeCommand($command,$hostname,$oid,$community);
        elseif ($version = SNMPVersion::V2C)
            //TODO
            ;

        // Build the response object
        $objReply = array();
        $objReply['oid']=$oid;
        $objReply['type']= TBD;
        $objReply['value']=$response;

        // Return the response object in JSON code
        return json_encode($objReply);
    }

}
?>
