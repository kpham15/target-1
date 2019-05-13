<?php
class CPSCLIENT {
    public $socket;
    public $connect;
    public $rslt;
    public $code;
    public $reason;

    public $rps;

    public function __construct() {
        //create socket UDP type
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, 0);
        if ($this->socket === false) {
            $this->rslt = 'fail';
            $this->reason = "Could not create socket";
            return;
        }
        //configure timeout
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 0, 'usec' => 500000));
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 500000));
        
    }

    public function sendCommand($cmd, $ipHw, $portHw) {
        $this->rps = null;
        echo "\nsending to HW: ".$cmd."\n";
        $sendCmd = socket_sendto($this->socket,$cmd, 1024,0, $ipHw, $portHw);
        if($sendCmd === false) {
            $this->rslt = "fail";
            $this->code = socket_last_error($this->socket);
            $this->reason = socket_strerror($this->code);
            return;
        }
        
        // $rps = socket_recvfrom($this->socket,$buf, 1024,0, $ipHw, $portHw);
        $rps = socket_recv($this->socket,$buf, 2048,MSG_WAITALL);
        if($rps === false) {
            $this->rslt = "fail";
            $this->code = socket_last_error($this->socket);
            $this->reason = socket_strerror($this->code);
            return;
        }

        $this->rps = $buf;
        
    }


    public function endConnection() {
        socket_close($this->socket);
    }




}


?>