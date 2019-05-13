<?php
class CPSCLIENT {
    public $socket;
    public $connect;
    public $rslt;
    public $code;
    public $reason;
    public $rsp;

    public function __construct($ip_addr, $ip_port, $delaySec, $delayUsec) {
        //create socket to communicate with CPS HW
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            $this->rslt = 'fail';
            $this->reason = "UNABLE TO CREATE SOCKET ($ip_addr:$ip_port)";
            return;
        }
        //configure timeout
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $delaySec, 'usec' => $delayUsec));
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $delaySec, 'usec' => $delayUsec));
        
        //connect to CPS HW
        // echo "connect to $ip_addr and $ip_port";
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
        // echo "Send cmd to hw\n";
        $write = socket_write($this->socket, $cmd, strlen($cmd));
        if ($write === false) {
            $this->rslt = "fail";
            $this->code = socket_last_error($this->socket);
            $this->reason = socket_strerror($this->code);
            return;
        }
    }

    public function receiveRsp(){
        $this->rsp = '';
        // echo "\nReading from HW\n";
        //get response back from CPS HW
        for ($i=0; $i<3; $i++) {
            $rsp = socket_read($this->socket, 1024);
            if ($rsp === false) {
                // continue reading upto 3 timeouts
                if (socket_last_error($this->socket) == 11) { 
                    continue;
                }
                else {
                    $this->rslt = 'fail';
                    $this->code = socket_last_error($this->socket);
                    $this->reason = socket_strerror($this->code);
                    return;
                }
            }
            else {
                break;
            }

        }
        $this->rslt = 'success';
        $this->reason = 'send successfully';
        if($rsp !== false)
            $this->rsp = $rsp;
        return;
    }


    public function endConnection() {
        socket_close($this->socket);
    }




}


?>