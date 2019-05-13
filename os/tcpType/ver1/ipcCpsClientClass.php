<?php
class CPSCLIENT {
    public $socket;
    public $connect;
    public $rslt;
    public $code;
    public $reason;

    public $rps;

    public function __construct($ip_addr, $ip_port, $delaySec, $delayUsec) {
        //create socket to communicate with CPS HW
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            $this->rslt = 'fail';
            $this->reason = "Could not create socket";
            return;
        }
        //configure timeout
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $delaySec, 'usec' => $delayUsec));
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $delaySec, 'usec' => $delayUsec));
        
        //connect to CPS HW
        echo "connect to $ip_addr and $ip_port";
        $this->connect = socket_connect($this->socket, $ip_addr, $ip_port);
        if ($this->connect === false) {
            $this->rslt = 'fail';
            $this->code = socket_last_error($this->socket);
            $this->reason = socket_strerror($this->code);
            return;
        }
    }

    public function sendCommand($cmd) {

        //send cmd to CPS HW
        echo "Send cmd to hw\n";
        $write = socket_write($this->socket, $cmd, strlen($cmd));
        if($write === false) {
            $this->rslt = "fail";
            $this->code = socket_last_error($this->socket);
            $this->reason = socket_strerror($this->code);
            return;
        }
    }

    public function receiveRsp(){
        $this->rps = null;
        echo "\nReading from HW\n";
        //get response back from CPS HW
        for($i=0; $i<3; $i++) {
            $rps = socket_read($this->socket, 1024);
            if($rps === false) {
                if(socket_last_error($this->socket) == 11) {
                    continue;
                }
                else {
                    $this->rslt = 'fail';
                    $this->code = socket_last_error($this->socket);
                    $this->reason = socket_strerror($this->code);
                    return;
                }
            }
            if($rps != '') {
                break;
            }

        }
        if($rps != '') {
            $this->rslt = 'success';
            $this->reason = 'send successfully';
            $this->rps = $rps;
        }
        else {
            $this->rslt = 'fail';
            $this->reason = 'fail to read';
        }
        
        
    }


    public function endConnection() {
        socket_close($this->socket);
    }




}


?>