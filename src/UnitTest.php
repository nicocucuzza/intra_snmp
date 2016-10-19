<?php
require __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($className) {
        include $className . '.php';
});

use PHPUnit\Framework\TestCase;

class SnmpTest extends TestCase {

    public $curl = null;
    public $service_url = 'http://127.0.0.1/snmp/';
    public $curl_post_data = array();

    public function initCurl() {
        $this->curl = curl_init($this->service_url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->curl_post_data);
    }

    // Test #1: SNMP get in v1
    public function testGetV1() {
        // Arrange
        $snmp = new SnmpRestHandler();

        // Act
        // 
        $command = "get";
        $host = "127.0.0.1";
        $community = "public";
        $oid = "system.sysUpTime.0";
        $version = "1";

        $response = $snmp->command($command,$version,$host,$oid,$community);

        // Assert
        $this->assertContains("Timeticks",$response);

    }

    // Test #2: SNMP get in v2C
    public function testGetV2C() {
        // Arrange
        $snmp = new SnmpRestHandler();

        // Act
        // 
        $command = "get";
        $host = "127.0.0.1";
        $community = "public";
        $oid = "system.sysUpTime.0";
        $version = "2C";

        $response = $snmp->command($command,$version,$host,$oid,$community);

        // Assert
        $this->assertContains("Timeticks",$response);

    }

    // Test #3: SNMP get in an unexpected version
    // @expectedException SNMPAPIException
    // @expectedExceptionCode 2
    public function testGetUnexpectedVersion() {
        try {
            // Arrange
            $snmp = new SnmpRestHandler();

            // Act
            // 
            $command = "get";
            $host = "127.0.0.1";
            $community = "public";
            $oid = "system.sysUpTime.0";
            $version = "10";

            $response = $snmp->command($command,$version,$host,$oid,$community);
        } catch (SNMPAPIException $e) {
            $this->assertEquals($e->getCode(), SNMPAPIException::VERSION_MISMATCHED);
        }

    }

    // Test #4: SNMP get in a invalid community
    // @expectedException SNMPAPIException
    // @expectedExceptionCode 
    public function testGetInvalidCommunity() {
        // Arrange
        $snmp = new SnmpRestHandler();

        // Act
        // 
        $command = "get";
        $host = "127.0.0.1";
        $community = "invalid";
        $oid = "system.sysUpTime.0";
        $version = "1";
        try {
            $response = $snmp->command($command,$version,$host,$oid,$community);
        } catch (Exception $exception) {
        ; }
    }

    // Test #5: SNMP get using an invalid OID
    // @expectedException     SNMPException
    public function testGetInvalidOid() {
        // Arrange
        $snmp = new SnmpRestHandler();

        // Act
        // 
        $command = "get";
        $host = "127.0.0.1";
        $community = "public";
        $oid = "invalid";
        $version = "1";
        try {
            $response = $snmp->command($command,$version,$host,$oid,$community);
        } catch (Exception $exception) {
        ; }
    }

    // Test #6: SNMP getNext in v1
    public function testGetNextV1() {
        // Arrange
        $snmp = new SnmpRestHandler();

        // Act
        // 
        $command = "getNext";
        $host = "127.0.0.1";
        $community = "public";
        $oid = "system.sysUpTime.0";
        $version = "1";

        $response = $snmp->command($command,$version,$host,$oid,$community);

        // Assert
        $this->assertContains("STRING: ",$response);

    }

    // Test #7: SNMP getNext in v2C
    public function testGetNextV2C() {
        // Arrange
        $snmp = new SnmpRestHandler();

        // Act
        $command = "getNext";
        $host = "127.0.0.1";
        $community = "public";
        $oid = "system.sysUpTime.0";
        $version = "2C";

        $response = $snmp->command($command,$version,$host,$oid,$community);

        // Assert
        $this->assertContains("STRING: ",$response);

    }


    // Test #11: SNMP walk in v1
    public function testWalkNextV1() {
        // Arrange
        $snmp = new SnmpRestHandler();

        // Act
        // 
        $command = "walk";
        $host = "127.0.0.1";
        $community = "public";
        $oid = "system.sysUpTime.0";
        $version = "1";

        $response = $snmp->command($command,$version,$host,$oid,$community);

        // Assert

        $this->assertContains("Timeticks: ",$response);

    }

    // Test #12: SNMP walk in v2C
    public function testWalkV2C() {
        // Arrange
        $snmp = new SnmpRestHandler();

        // Act
        // 
        $command = "walk";
        $host = "127.0.0.1";
        $community = "public";
        $oid = "system.sysUpTime.0";
        $version = "2C";

        $response = $snmp->command($command,$version,$host,$oid,$community);

        // Assert
        $this->assertContains("Timeticks: ",$response);

    }

    // Test #12: SNMP set in v2C
    public function testSetV2C() {
        // Arrange
        $snmp = new SnmpRestHandler();

        // Act
        // 
        $command = "set";
        $host = "127.0.0.1";
        $community = "private";
        $oid = "SNMPv2-MIB::sysContact.0";
        $version = "2C";
        $type = "s";
        $description = "Nobody";

        $response = $snmp->command($command,$version,$host,$oid,$community);

        // Assert
        $this->assertContains("Timeticks: ",$response);

    }

    // Test #16: send a request without oid 
    public function testSendRequestMissingArgument() {
        // Act
        $this->curl_post_data = array();
        $this->curl_post_data = array(    "command"=> "GetNext",
            "version"=> "1",
            "hostname"=> "localhost",
//            "oid"=> "system.sysUpTime.0",
            "community"=> "default"
        );

        $this->initCurl();
        $curl_response = curl_exec($this->curl);
        curl_close($this->curl);        
        $this->curl_post_data = null    ;

        $resp = json_decode($curl_response);
        $this->assertEquals($resp->code , SNMPAPIException::MANDATORY_PARAMETER_FAILED);
    }

    // Test #17: send a request to command get in v1
    public function testCurlGetV1() {
        // Arrange
        $this->curl_post_data = array();
        $this->curl_post_data = array(    "command"=> "Get",
            "version"=> "1",
            "hostname"=> "localhost",
            "oid"=> "system.sysUpTime.0",
            "community"=> "default"
        );

        $this->initCurl();
        $curl_response = curl_exec($this->curl);
        curl_close($this->curl);        
        $this->curl_post_data = null    ;

        $resp = json_decode($curl_response);

        $this->assertContains("Timeticks",$resp);
    }
 
    // Test #17: send a request to command get in v2C
    public function testCurlGetV2C() {
        // Arrange
        $this->curl_post_data = array();
        $this->curl_post_data = array(    "command"=> "Get",
            "version"=> "2C",
            "hostname"=> "localhost",
            "oid"=> "system.sysUpTime.0",
            "community"=> "default"
        );

        $this->initCurl();
        $curl_response = curl_exec($this->curl);
        curl_close($this->curl);        
        $this->curl_post_data = null    ;

        $resp = json_decode($curl_response);

        $this->assertContains("Timeticks",$resp);
    }
}
?>
