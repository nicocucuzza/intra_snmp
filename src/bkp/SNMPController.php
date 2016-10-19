<?php

class SNMPController {
    public function get ($hostname, $community,$oid,$version) {
        if($version == "1")
            return snmpget($hostname,$community,$oid,500);
        elseif($version == "2C")
            return snmp2_get($hostname,$community,$oid,500);
        return false;
    }

    public function getNext ($hostname, $community,$oid,$version) {
        if($version == "1")
            return snmpgetnext($hostname,$community,$oid,500);
        elseif($version == "2C")
            return snmp2_getNext($hostname,$community,$oid,500);
        return false;
    }


    public function set ($hostname, $community,$oid,$version) {
        if($version == "1")
            return snmpset($hostname,$community,$oid,500);
        elseif($version == "2C")
            return snmp2_set($hostname,$community,$oid,500);
    }

    public function walk ($hostname, $community,$oid,$version) {
        if($version == "1")
            return snmpwalk($hostname,$community,$oid,500);
        elseif($version == "2C")
            return snmp2_walk($hostname,$community,$oid,500);
        return false;
    }


}

?>
