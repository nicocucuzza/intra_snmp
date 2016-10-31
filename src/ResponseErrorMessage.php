<?php

//Class used to response in case of API or request error
class ResponseErrorMessage {

    //Each error has: a code (an int number)
    private $code;

    // A message, use to know what happened
    private $message;

    // And a link to see the documentation
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

    //Function used to convert to json the ResponseERrorMessage object
    public function toJson() {
        return array(
                        "code" => $this->getCode(), 
                        "message" => $this->getMessage(), 
                        "link" => $this->getLink() );
    }
}

?>
