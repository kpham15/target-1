<?php
class NODESERVER {
    public $rslt;
    public $reason;

    public $id;
    public $socket;

    public function __construct($host, $port, $id) {
        $this->id = $id+1;
        $nodePort = $port + $id;
        // set_time_limit(0);
        // create socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(!$socket) {
            $this->rslt = 'fail';
            $this->reason = "Could not create socket";
            return;
        }
        $this->socket = $socket;
        // bind socket to port
        $result = socket_bind($socket, $host, $nodePort);
        if(!$result) {
            $this->rslt = 'fail';
            // $this->reason = "Could not bind to socket";
            $this->reason = "Could not bind to socket ".$host.":".$nodePort;
            return;
        }

        // start listening for connections
        $result = socket_listen($socket);
        if(!$result) {
            $this->rslt = 'fail';
            $this->reason = "Could not set up socket listener";
            return;
        }

        echo "Server $host is listening on port $nodePort!";
    }

    public function run(){
        global $db;
        echo "Server is listening!";
        while(true) {
            $processId = socket_accept($this->socket);
            if(!$processId) {
                $this->rslt = "'fail'";
                $this->reason = "Could not accept incoming connection";
                $this->echoError();
                goto close_socket;
            }

           
            $input = json_decode(socket_read($processId, 1024));
            if(!$input) {
                $this->rslt = "'fail'";
                $this->reason = "Could not read input1";
                $this->echoError();
                goto close_socket;
            }

            $cmd = trim($input);
            $this->processInput($processId,$cmd);
            
            
            close_socket:
                socket_close($processId);
        }
    }

    public function processInput($connect,$input) {

        if($input == "COM") {
            $hwObj = new HW($this->id);
            if($hwObj->rslt == 'fail') {
                $this->rslt = 'fail';
                $this->reason = "INVALID_NODE_ID";
                $this->echoError();
                return;
            }
            
            if($hwObj->com == "ON") {
                $msg = json_encode($hwObj->rows[0]);
                $write = socket_write($connect, $msg, strlen ($msg)); 
                if(!$write) {
                    $this->rslt = 'fail';
                    $this->reason = "Could not write output";
                    $this->echoError();
                    return;
                }
            }
            
        }
        else if (strpos($input, 'alm=') !== false) {

            $alm = explode("=", $input)[1];

            $hwObj = new HW($this->id);
            if($hwObj->rslt == 'fail') {
                $this->rslt = 'fail';
                $this->reason = "INVALID_NODE_ID";
                $this->echoError();
                return;
            }

            if($hwObj->com == "ON") {
                $hwObj->updateAlm($alm);
                if($hwObj->rslt == 'fail') {
                    $this->rslt = 'fail';
                    $this->reason = "UPDATE_ALM_FAIL";
                    $this->echoError();
                    return;
                }
            }

        }
    }

    public function echoError() {
        echo $this->rslt.":".$this->reason."\n";
    }
}




?>