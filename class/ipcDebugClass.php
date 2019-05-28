<?php

class DEBUG{
    public $enable = 0;
    public $debugFile = null;
    public $rslt;
    public $reason;

    public function __construct($mode=NULL){

        if (!file_exists('../../ipc-debug.cfg ')) {
            $this->rslt = 'fail';
            $this->reason = "FILE IPC-DEBUG.CFG DOES NOT EXIST";
            return;
        }
        $enable = 0;
        $fd = fopen("../../ipc-debug.cfg", "r");
        if (!$debugFile) {
            $this->rslt = 'fail';
            $this->reason = "OPEN_LOGFILE_FAIL";
            return;
        }
        else {
            while (($line = fgets($fd)) !== false) {
                // process the line read.
                $enable = trim($line);
                if ($enable != '')
                    break;
            }
        
            fclose($fd);
        }

        $this->enable = $enable;

        if ($this->enable == 1) {
            $this->debugFile =  fopen("../report/debug.log", "a");
        }
        
        $this->rslt = 'success';
        $this->reason = "DEBUG CONSTRUCTED";

    }

    public function close() {
        if ($this->enable == 0)
            return;
        fclose($this->debugFile);
    }

    public function log($string) {
        if ($this->enable == 0) {
            return;
        }

        fwrite($this->debugFile, $string);

    }

    

}





?>