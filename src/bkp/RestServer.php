<?php

class RestServer
{
    public $serviceClass;	
    
    public function __construct($serviceClass)
	{
        $this->serviceClass = $serviceClass;
    }

    public function handle()
    {
        if (array_key_exists("method", array_change_key_case($_REQUEST, CASE_LOWER))) {
            $rArray = array_change_key_case($_REQUEST, CASE_LOWER);
            $method = $rArray["method"];
            
            if (method_exists($this->serviceClass, $method)) {
                $ref = new ReflectionMethod($this->serviceClass, $method);
                $params = $ref->getParameters();
                $pCount = count($params);
                $pArray = array();
                $paramStr = "";	
                
                $i = 0;
                
                foreach ($params as $param) {
                    $pArray[strtolower($param->getName())] = null;
                    $paramStr .= $param->getName();
                    if ($i != $pCount-1) {
                        $paramStr .= ", ";
                    }
                    
                    $i++;
                }
                
                foreach ($pArray as $key => $val) {
                    $pArray[strtolower($key)] = $rArray[strtolower($key)];
                }
                
                if (count($pArray) == $pCount && !in_array(null, $pArray)) {
                    echo call_user_func_array(array($this->serviceClass, $method), $pArray);
                }
                else {
                    echo json_encode(array('error' => "Required parameter(s) for ". $method .": ". $paramStr));
                }
            }
            else {
                echo json_encode(array('error' => "The method " . $method . " does not exist."));
            }
        }
        else {
            echo json_encode(array('error' => 'No method was requested.'));
        }
    }
}

?>
