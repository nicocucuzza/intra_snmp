<?php

class ResponseErrorMessage {

    private $code;
    private $message;
    private $link;

    public function ResponseErrorMessage($code, $message = "", $link = "") {
        $this->code = $code;        
        $this->message = $message;
        $this->link = $link;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function setMessage($msg) {
        $this->message = $msg;
    }

    public function setLink($link) {
        $this->link = $link;
    }

    public function getCode() {
        return $this->code;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getLink() {
        return $this->link;
    }

    public function toJson() {
        return array(
                        "code" => $this->getCode(), 
                        "message" => $this->getMessage(), 
                        "link" => $this->getLink() );
    }
}

?>
