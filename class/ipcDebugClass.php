<?php

class DEBUG{
    public $enable = 0;
    public $rslt;
    public $reason;

    public function __construct($mode=NULL){

        if (!file_exists('../../ipc-debug.cfg ')) {
            $this->rslt = 'fail';
            $this->reason = "FILE IPC-DEBUG.CFG DOES NOT EXIST";
            return;
        }
        $enable = 0;
        $debugFile = fopen("../../ipc-debug.cfg", "r");
        if (!$debugFile) {
            $this->rslt = 'fail';
            $this->reason = "OPEN_LOGFILE_FAIL";
            return;
        }
        
        if ($debugFile) {
            while (($line = fgets($file)) !== false) {
                // process the line read.
                $enable = trim($line);
                if($enable != '') break;
            }
        
            fclose($file);
        }
        else {

        }
        $this->enable = $enable;


        $this->rslt = 'success';
        $this->reason = "OPEN_LOGFILE_SUCCESS";

    }

    // public function getSizeOfLogFile(){
        
    //     return filesize("../report/debugLog.txt");

    // }

    public function logPostRequestInfo($filename,$variableArray) {
        $this->logString = "-----------------------\n".date("Y-m-d H:i:s")." ".$filename.": ";
        $lastkey = end(array_keys($variableArray));
        foreach($variableArray as $key => $value) {
            if ($key ===  $lastkey) {
                $this->logString .= $key.":".$value."\n";
            }
            else {
                $this->logString .= $key.":".$value.", ";
            }
                
        }
    }

    public function logFunction($funcName) {
        $this->logString .= $funcName."()\n";
    }
    
    public function logEvent($result, $reason) {
        $this->logString .= $result.": ".$reason."\n";

        // $response = fwrite($this->debugFile,$this->logString);
        // if(!$response) {
        //     $this->rslt = 'fail';
        //     $this->reason = "WRITE_LOGFILE_FAIL";
        //     return;
        // }
        // will insert log to DB instead
        $this->rslt = 'success';
        $this->reason = "WRITE_LOGFILE_SUCCESS";
    }

    public function closeLogfile(){
        // fclose($this->debugFile);
    }
}





?>