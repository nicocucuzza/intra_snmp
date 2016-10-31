<?php

// Autoload
// TODO use PSR3
require __DIR__ . '/vendor/autoload.php';
spl_autoload_register(function ($className) {
        include $className . '.php';
});
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

class RestController {

    // Logger 
    private $logger = null;

    //Config values
    private $config = array();

    //Parameters received in request, without parsing
    private $rawParameters = array();

    //Parameters received in request, parsed
    private $parameters = array();

    //Path to ini file (by default)
    const INI_FILE = __DIR__."/config/snmp-api.ini";

    public function RestController($iniFile = self::INI_FILE, $rawParameters) {
        //Verify if configuration file exists
        if(!file_exists($iniFile) ) {
            throw new SNMPAPIException(SNMPAPIException::INI_FILE_NOT_FOUND, "Error: configuration file not found. ".$iniFile." doesn't exist.");
        }

        $this->rawParameters = $rawParameters;

        //Load configuration file values
        $this->config = parse_ini_file($iniFile,true);
        $logFile = $this->config['logger']['log-dir'];

        // Create the logger
        $this->logger = new Logger('snmp_rest_api_logger');

        // Check if log file exists (or if it can be created)
        $this->verifyLogFile($logFile); 

        //TODO encapsulate this methods
        $this->logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());

    }

    //Verify if log file has permission
    public function verifyLogFile($logFile) {
        
        // Check file existence and permissions
        if(!file_exists($logFile) ) {
            if(!touch($logFile)) {                
                throw new SNMPAPIException(SNMPAPIException::LOG_FILE_ERROR, "Error: Log can't start. Details: ". $logFile." can't be created. Please verify if the file exists, or if you have permission to create it");                
            }
        }
        //Check if log file is writable
        if(!is_writable($logFile)) {
            throw new SNMPAPIException(SNMPAPIException::LOG_FILE_ERROR, "Error: Log can't start. Details: ". $logFile." is not writable. Please verify if the file exists, or if you have permission to create it");
        }

        return true;
    }

    //Read parameters (mandatory and optionals)
    public function readParameters() {

        //Read mandatory parameters 
        $iniMandatory = $this->config['api-parameters']['mandatory-parameters'];
        $this->mandatoryParameters = explode(",",$iniMandatory);
        
        //Read optional parameters
        $iniOptional = $this->config['api-parameters']['optional-parameters'];
        $this->optionalParameters = explode(",",$iniOptional);

        // Verifies if each mandatory parameter is present in the request received
        foreach($this->mandatoryParameters as $param) {
            if(!isset($this->rawParameters[$param])) {
                $message = "Parameter $param is not present in the request";
                $this->logger->addError($message);
                throw new SNMPAPIException(SNMPAPIException::MANDATORY_PARAMETER_FAILED, "Parameter $param is not present in the request");
            } else {
                $this->logger->addDebug("Adding parameter $param with value ".$this->rawParameters[$param]);
                $parameter[] = new RestParameter($param, $this->rawParameters[$param]);
            }
        } 
    
        //Verify optional parameters. If it's missing, use the default value
        foreach($this->optionalParameters as $param) {
            if(isset($_REQUEST[$param]) ) {
                $this->logger->addDebug("Adding parameter $param with value ".$_REQUEST[$param]);
                $parameter[] = new RestParameter($param,$_REQUEST[$param]);
            } else {
                $value = $this->config['parameters-default-values'][$param."-default"];
                $this->logger->addDebug("Adding parameter $param with DEFAULT value ".$value);
                $parameter[] = new RestParameter($param,$value);
            }
        }

        //Set all the parameters and its values in an internal array
        $this->parameters = $parameter;
    }

    //Verify if async mode is active or not
    public function isAsync() {
        return (isset($this->parameters['async']) );
    }

    //Execute the command and return the result
    public function executeRestCommand () {

        //Read parameters (mandatories and optionals)
        $this->readParameters();

        //If it's an async call, response HTTP ACCEPT, and then execute the command. The result will be in the callback_url given
        if($this->isAsync() ) {
            $requestContentType = $_SERVER['HTTP_ACCEPT'];
            $restResponse = new SimpleRest();
            $restResponse->setHttpHeaders($requestContentType, 202);
            $this->logger->addDebug($restResponse);
            echo $restResponse;
        }

        $snmpRestHandler = new SnmpRestHandler();

        //Execute the command using all the parameters given (mandatories and optionals)
        $response = $snmpRestHandler->command(
            $this->parameters[0]->getValue(),
            $this->parameters[1]->getValue(),
            $this->parameters[2]->getValue(),
            $this->parameters[3]->getValue(),
            $this->parameters[4]->getValue(),
            $this->parameters[5]->getValue(),
            $this->parameters[6]->getValue(),
            $this->parameters[7]->getValue(),
            $this->parameters[8]->getValue(),
            $this->parameters[9]->getValue(),
            $this->parameters[10]->getValue()
        );

      $this->logger->addDebug($response);

      //Show response via standard output
      echo $response;
    }

}

?>
