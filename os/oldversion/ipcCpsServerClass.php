<?php
class CPSSERVER {
    public $socket;
    public $connect;
    public $rslt;
    public $code;
    public $reason;

    public $rps;

    public function __construct($ip_addr, $ip_port) {
        // $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
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
        
        // start listening for connections
        // $listen = socket_listen($this->socket);
        // if($listen === false) {
        //     $this->rslt = 'fail';
        //     $this->reason = "Could not set up socket listener";
        //     return;
        // }

        echo "Server $ip_addr is listening on port $ip_port!";
    }

}




?>