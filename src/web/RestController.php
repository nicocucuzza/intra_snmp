<?php
//require_once("SnmpRestHandler.php");
require __DIR__ . '/vendor/autoload.php';
spl_autoload_register(function ($className) {
        include $className . '.php';
});
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

// Create the logger
$logger = new Logger('snmp_rest_api_logger');
// // Now add some handlers
$logger->pushHandler(new StreamHandler('/home/nico/snmp_intraway/src/snmp_api.log', Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());

// List of mandatory parameters in all requests
$mandatoryParameters = array (
    "command",
    "version",
    "hostname",
    "oid",
    "community"
);

$optionalParameters = array (
    "timeout",
    "retries",
    "type",
    "description",
    "async",
    "callback_url"
);

try {
    // Verifies if each mandatory parameter is present in the request received
    foreach($mandatoryParameters as $param) {
        if(!isset($_REQUEST[$param])) {
            $message = "Parameter $param is not present in the request";
            $logger->addError($message);
            throw new SNMPAPIException(SNMPAPIException::MANDATORY_PARAMETER_FAILED, "Parameter $param is not present in the request");
        } else
            $parameter[] = $_REQUEST[$param];
    } 

    //TODO finish the async mode
    if( isset($_REQUEST['async']) ) {
        $requestContentType = $_SERVER['HTTP_ACCEPT'];     
        $restResponse = new SimpleRest();
        $restResponse->setHttpHeaders($requestContentType, 202);
    }

    foreach($optionalParameters as $param) {
        if(isset($_REQUEST[$param]) )
            $parameter[] = $_REQUEST[$param];
    }

    $snmpRestHandler = new SnmpRestHandler();

    //Execute the right command using the parameters given
    switch(count($parameter)) {
        case 5:
            $response = $snmpRestHandler->command($parameter[0],$parameter[1],$parameter[2],$parameter[3],$parameter[4]);
            break;
        case 6:
            $response = $snmpRestHandler->command($parameter[0],$parameter[1],$parameter[2],$parameter[3],$parameter[4],$parameter[5]);
            break;
        case 7:
            $response = $snmpRestHandler->command($parameter[0],$parameter[1],$parameter[2],$parameter[3],$parameter[4],$parameter[5],$parameter[6]);
            break;
        case 8:
            $response = $snmpRestHandler->command($parameter[0],$parameter[1],$parameter[2],$parameter[3],$parameter[4],$parameter[5],$parameter[6],$parameter[7]);
            break;
        case 9:
            $response = $snmpRestHandler->command($parameter[0],$parameter[1],$parameter[2],$parameter[3],$parameter[4],$parameter[5],$parameter[6],$parameter[7],$parameter[8]);
            break;
        case 10:
            throw new SNMPAPIException(SNMPAPIException::NO_CALLBACK, "Async mode requires an URL callback");
            break;
        case 11:
            $response = $snmpRestHandler->command($parameter[0],$parameter[1],$parameter[2],$parameter[3],$parameter[4],$parameter[5],$parameter[6],$parameter[7],$parameter[8],$parameter[9],$parameter[10]);
            break;
    }

    $logger->addDebug($response);
    echo $response;

} catch (SNMPAPIException $e) {
    //Build the response in case of error
    $response = new ResponseErrorMessage($e->getCode(),$e->getMessage());
    echo json_encode( $response->toJson() );
    exit($e->getCode());
}

?>
