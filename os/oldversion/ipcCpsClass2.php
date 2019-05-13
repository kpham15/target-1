<?php

class CPS {
    public $socket;
    public $connect;
    public $rslt;
    public $reason;

    public $read;

    
    public function __construct($ip_addr, $ip_port) {
        //create socket
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            $this->rslt = 'fail';
            $this->reason = "Could not create socket";
            return;
        }
        //configure timeout
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 0, 'usec' => 10000));
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 10000));
        
        //connect to host
        $this->connect = socket_connect($this->socket, $ip_addr, $ip_port);
        if ($this->connect === false) {
            $this->rslt = FAIL;
            $this->reason = "Could not create connection";
            return;
        }
    }

    public function sendCommand($ackid, $cmd) {

        $write = socket_write($this->socket, $cmd, strlen($cmd));
        $read = socket_read ($this->socket, 1024);
        if($read === false) {
            $this->rslt = "fail";
            $this->reason = "unable to read";
            return;
        }

        if($read != $ackid) {
            $this->rslt = "fail";
            $this->reason = "ACKID is not matched";
            return;
            // throw Exception("ACKID is not matched", 12);
        }
  
        // $qry = "log";
        // $write = socket_write($this->socket, $qry, strlen($qry));
        $read = socket_read ($this->socket, 1024);
        if($read === false) {
            $this->rslt = "fail";
            $this->reason = "unable to read";
            return;
            // throw Exception("read fail", 12);
        }

        $this->read = $read;
        
    }


    public function endConnection() {
        socket_close($this->socket);
    }




}


?>