<?php
class CPSSERVER {
    public $socket;
    public $connect;
    public $rslt;
    public $code;
    public $reason;

    public $rsp;

    public function __construct($ip_addr, $ip_port, $timeoutSec, $timeoutUsec) {
        // create socket to comunicate with IPC loop, UDP type
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, 0);
        if($this->socket === false) {
            $this->rslt = 'fail';
            $this->reason = "Could not create socket";
            return;
        }
        $bind = socket_bind($this->socket, $ip_addr, $ip_port);
        if($bind === false) {
            $this->rslt = 'fail';
            $this->reason = "Could not bind to socket ".$ip_addr.":".$ip_port;
            return;
        }
        socket_set_nonblock ($this->socket);
        //set timeout for the server
        if($timeoutSec != 0 || $timeoutUsec != 0) {
            socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeoutSec, 'usec' => $timeoutUsec));
            socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $timeoutSec, 'usec' => $timeoutUsec));
        }
        echo "\nServer $ip_addr is listening on port $ip_port!\n";
    }

    public function endConnection() {
        socket_close($this->socket);
    }

}




?>