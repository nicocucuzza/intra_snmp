<?php
require __DIR__ . '/vendor/autoload.php';
spl_autoload_register(function ($className) {
        include $className . '.php';
});


class SnmpRestHandler extends SimpleRest {
    const GET = "get";
    const GETNEXT = "getnext";
    const WALK = "walk";
    const SET = "set";

    private function checkVersion($version) {
        switch($version) {
            case "1":
                return SNMP::VERSION_1;
            case "2c":
            case "2C":
                return SNMP::VERSION_2C;
            default:
                throw new SNMPAPIException(SNMPAPIException::VERSION_MISMATCHED,"Version $version is not a valid SNMP version supported by this API");
                break;
        }
    }

    // Receive a request, execute (using PHP's SNMP library) a valid command and return the response
    // TODO async mode
	function command($command, $version, $hostname, $oid, $community,$timeout=1000000500,$retries=5,$type ="=", $description = "", $async = false, $callback_url = "") {	
        try {
            // Validates SNMP version (supported: 1 and 2C)
            $snmpVersion = $this->checkVersion($version);
            $snmp = new SNMP($snmpVersion, $hostname, $community, $timeout, $retries);
           
            $command = strtolower($command);

            // execute the command (supported:: get, getnext, walk, set)
            switch($command) {
                case self::GET:
                    $rawData = $snmp->get($oid);
                    break;
                case self::GETNEXT:
                    $rawData = $snmp->getNext($oid);
                    break;
                case self::WALK:
                    $rawData = $snmp->walk($oid);
                    break;
                case self::SET:
                    //TODO implement SET command
//                  $rawData = $snmp->set($oid, $type, $description);
                    break;
                default:
                    // Throws an exception if the command is not one of the valid ones
                    throw new SNMPAPIException(SNMPAPIException::COMMAND_INVALID,"Command $command not recognized");
                    break;
            }

            //Throws an exception if the command has returned an error
            if ($snmp->getErrno() != SNMP::ERRNO_NOERROR)
                throw new SNMPAPIException($snmp->getErrno(), $snmp->getError());

	        $response = $this->encodeJson($rawData);

            // Build the HTTP response
            if (isset($_SERVER['HTTP_ACCEPT']) ) {
    		    $requestContentType = $_SERVER['HTTP_ACCEPT'];
	    	    $this ->setHttpHeaders($requestContentType, 200);            

    		    if(strpos($requestContentType,'application/json') !== false){
    		    	$response = $this->encodeJson($rawData);
    		    	echo $response;
    		    } else if(strpos($requestContentType,'text/html') !== false){
    		    	$response = $this->encodeHtml($rawData);
    		    	echo $response;
    		    } else if(strpos($requestContentType,'application/xml') !== false){
    		    	$response = $this->encodeXml($rawData);
    		    	echo $response;
    		    }
            }
            
		    return $response;

        } catch (Exception $e) {
            throw $e;
        }
	}
	
    // Encode in HTML format the response
	public function encodeHtml($responseData) {
	
		$htmlResponse = "<table border='1'>";
		foreach($responseData as $key=>$value) {
    			$htmlResponse .= "<tr><td>". $key. "</td><td>". $value. "</td></tr>";
		}
		$htmlResponse .= "</table>";
		return $htmlResponse;		
	}
	
    // Encode in JSON format the response
	public function encodeJson($responseData) {
		$jsonResponse = json_encode($responseData);
		return $jsonResponse;		
	}
	
    // Encode in XML the response
	public function encodeXml($responseData) {
		// creating object of SimpleXMLElement
		$xml = new SimpleXMLElement('<?xml version="1.0"?><mobile></mobile>');
		foreach($responseData as $key=>$value) {
			$xml->addChild($key, $value);
		}
		return $xml->asXML();
	}
	
}
?>
