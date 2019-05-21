<?php
class COMPORT {
    public $sport;
    public $connect;
    public $timeout;

    public $rslt;
    public $reason;

    public function __construct($sport,$baud, $bits, $stop, $parity, $timeoutSec, $timeoutUsec) {
        //Connect to serial port
        $connect = dio_open($sport, O_RDWR | O_NOCTTY | O_NONBLOCK);
        if($connect === false) {
            $this->rslt = 'fail';
            $this->reason = "UNABLE TO CREATE CONNECTION TO PORT ($sport)";
            return;
        }
        //if successfully connected
        $this->connect = $connect;
        $this->sport = $sport;

        //configure the connnection's parameters
        dio_tcsetattr($this->connect, array(
            'baud'=> $baud,
            'bits'=>$bits,
            'stop'=>$stop,
            'parity'=>$parity
        ));

        //set timeout parameter
        $this->timeout = (float)$timeoutSec + ((float)$timeoutUsec/1000000);
    }

    public function sendCmd($cmd) {
        //send cmd to CPS HW
        $write = dio_write($this->connect, $cmd, strlen($cmd));
        //this function returns # bytes written to descriptor
        if ($write == 0) {
            $this->rslt = "fail";
            $this->reason = "SEND CMD FAILS";
            return;
        }
    }

    
    //waiting for data from HW with timeout
    public function receiveRsp() {
        $rsp = '';
        $startTime = microtime(true);
        while((microtime(true) - $startTime) < $this->timeout) {
            $data = dio_read($this->connect, 1024);
            if(trim($data) == "")
                continue;
            else {
                while(1) {
                    $rsp .= $data;
                    usleep(100000);
                    $data = dio_read($this->connect, 1024);
                    if(trim($data) != ""){
                        continue;
                    }
                    else break;
                }
                break;
            }     
        }

        $this->rslt = 'success';
        $this->reason = 'receive successfully';
        return $rsp;
    }


    public function endConnection() {
        dio_close($this->connect);
    }




}


?>