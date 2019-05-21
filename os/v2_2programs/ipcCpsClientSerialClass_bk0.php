<?php
class CPSCLIENT {
    public $sport;
    public $connect;
    public $timeout;

    public $rslt;
    public $reason;
    public $rsp;

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

    public function sendCommand($cmd) {
        //send cmd to CPS HW
        $write = dio_write($this->connect, $cmd, strlen($cmd));
        //this function returns # bytes written to descriptor
        if ($write == 0) {
            $this->rslt = "fail";
            $this->reason = "SEND CMD FAILS";
            return;
        }
    }

    //waiting for data from HW with timeout. Do it 3 times to make sure that it 
    // won't lose data
    public function receiveRsp(){
        $this->rsp = '';
        //get response back from CPS HW
        for ($i=0; $i<3; $i++) {
            $rsp = $this->getData();
            if ($rsp === "") {
                // continue reading upto 3 timeouts
                continue;
            }
            else {
                break;
            }
        }
        $this->rslt = 'success';
        $this->reason = 'receive successfully';
        $this->rsp = $rsp;
        return;
    }

    //waiting for data from HW with timeout
    public function getData() {
        $rsp = '';
        $startTime = microtime(true);
        while((microtime(true) - $startTime) < $this->timeout) {
            $data = dio_read($this->connect, 1024);
            if(trim($data) == "")
                continue;
            else {
                $rsp = $data;
                break;
            }     
        }
        return $rsp;
    }


    public function endConnection() {
        dio_close($this->connect);
    }




}


?>