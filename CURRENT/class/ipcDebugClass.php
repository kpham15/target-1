<?php

class DEBUG{
    public $enable = false;
    public $debugFile = null;
    public $rslt;
    public $reason;

    public function __construct(){
        
        $file = __DIR__ . "/../../ipc-debug.cfg";
        $str = file_get_contents($file);
        $data = trim($str);
        if ($data == '1') {
            $this->enable = true;
        }
    
        if ($this->enable === true) {
            $logFile = __DIR__ . "/../../LOG/debug.log";
            $this->debugFile =  fopen($logFile, "a");
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