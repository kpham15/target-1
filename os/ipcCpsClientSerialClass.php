<?php
class CPSCLIENT {
    public $sport;
    public $connect;
    public $timeout;

    public $rslt;
    public $reason;
    public $rsp;

    public function __construct($sport, $timeoutSec, $timeoutUsec) {
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

        //set this connection to be asyncronous
        // dio_fcntl($fd, F_SETFL, O_ASYNC);
        dio_fcntl($fd, F_SETFL, O_NONBLOCK);

        //set timeout parameter
        $this->timeout = (float)$timeoutSec + ((float)$timeoutUsec/1000000);
        // pcntl_sigtimedwait ( [$fd], $siginfo , $delaySec, $delayUsec);
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
        $this->reason = 'send successfully';
        $this->rsp = $rsp;
        return;
    }

    public function getData() {
        $rsp = '';
        $startTime = microtime(true);
        while(1) {
            if((microtime(true) - $startTime) < $this->timeout) {
                $data = dio_read($this->timeout, 1024);
                if(trim($data) == "")
                    continue;
                else {
                    $rsp = $data;
                    break;
                }     
            }
            else {
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