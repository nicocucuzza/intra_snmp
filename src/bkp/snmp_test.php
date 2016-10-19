<?php

$response = snmpget("localhost","public", ".1.3.6.1.2.1.1.2.0");
print_r($response);

?>
