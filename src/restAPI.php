<?php
// Autoload
// TODO: autoload using PSR3
require __DIR__ . '/vendor/autoload.php';
spl_autoload_register(function ($className) {
        include $className . '.php';
});


//If the request is application/json, decode the data
if(count($_REQUEST) == 0) {
    $json = file_get_contents('php://input');
    $parameters = json_decode($json,true);
} else {
    $parameters = $_REQUEST;
}

// Set ini file path
$iniFile = RestController::INI_FILE;

//If an ini file is given, the API use it
if (isset($argv[1]))
    $iniFile = $argv[1];

try {
    //Create a Rest Controller
    $restController = new RestController($iniFile, $parameters);

    //Execute the command
    $restController->executeRestCommand();
} catch (SNMPAPIException $e) {
    //Build the response in case of error
    $response = new ResponseErrorMessage($e->getCode(),$e->getMessage());
    echo json_encode( $response->toJson() );
    exit($e->getCode());
}


?>
