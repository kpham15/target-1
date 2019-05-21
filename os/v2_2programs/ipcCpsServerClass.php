<?php
class CPSSERVER {
    public $socket;
    public $connect;
    public $rslt;
    public $code;
    public $reason;

    public $rsp;

    public function __construct($ip_addr, $ip_port) {

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

        echo "\nServer $ip_addr is listening on port $ip_port!\n";
    }

    public function endConnection() {
        socket_close($this->socket);
    }

}




?>