<?php

$helpText = "Usage: php CurlTest.php -c COMMAND -h HOSTNAME -v VERSION -o OID -m COMMUNITY\nOptional parameters: -t TIMEOUT (in microseconds) -r RETRIES -y TYPE (in case of SET command) -d DESCRIPTION (in caso of SET command) -a ASYNC_MODE (true/false) -u CALLBACK_URL\n";

// Script to test SNMP 

//List of mandatory parameters
$shortMandatory = "c:v:h:o:m:";
//List of optional parameters
$shortOptional = "a::u::t::r::y::d::";

// Read parameters from cmd
$options = getopt($shortMandatory.$shortOptional);

$mandatoryArray = array("c","v","h","o","m");

//Verify if all mandatory parameters are given
foreach ($mandatoryArray as $mandatoryParameter) {
    if(!isset($options[$mandatoryParameter])) {
        echo "Parameter $mandatoryParameter is missing\n";
        echo $helpText;
        exit(-1);
    }
}

//URL where HTTP request is going to be dispatched (localhost)
$service_url = "http://127.0.0.1/snmp";
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//POST HTTP as method to test
curl_setopt($curl, CURLOPT_POST, true);

//Array with mandatory parameters
$curl_post_data = array("command"=> $options['c'],
    "version"=> $options['v'],
    "hostname"=> $options['h'],
    "oid"=> $options['o'],
    "community"=> $options['c']
);

//Set parameters and send the HTTP POST request
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);

// Print the response via standard output
print_r($curl_response);
curl_close($curl);   

?>
