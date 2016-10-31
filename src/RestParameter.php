<?php

//Class used to read the parameters given in a request
class RestParameter {
    private $parameter;
    private $value;

    public function RestParameter($param, $value) {
        $this->parameter = $param;
        $this->value = $value;
    }

    public function getParameter() {
        return $this->parameter;
    }

    public function getValue() {
        return $this->value;
    }
}

?>
