<?php

class DEBUG{
    public $enable = false;
    public $debugFile = null;
    public $rslt;
    public $reason;

    public function __construct(){

        $str = file_get_contents("../../ipc-debug.cfg");
        if ($str == '1') {
            $this->enable = true;
        }
    
        if ($this->enable === true) {
            $this->debugFile =  fopen("../../LOG/debug.log", "a");
        }
        
        $this->rslt = 'success';
        $this->reason = "DEBUG CONSTRUCTED";

    }

    public function close() {
        if ($this->enable === false)
            return;
        fclose($this->debugFile);
    }

    public function log($string) {
        if ($this->enable === false) {
            return;
        }

        fwrite($this->debugFile, $string);

    }

    

}





?>