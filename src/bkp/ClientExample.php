<?php

$service_url = 'http://127.0.0.1/snmp/';
$curl = curl_init($service_url);

$curl_post_data = array(
    "command"=> "GetNext",
//    "version"=> "1",
    "hostname"=> "localhost",
    "oid"=> "system.sysUpTime.0",
    "community"=> "default"
);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);

print_r($curl_response);

//if(!$curl_response)
//    trigger_error(curl_error($curl));

curl_close($curl);

?>
