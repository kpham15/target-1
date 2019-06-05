<?php
/*
* Copy Right @ 2018
* BHD Solutions, LLC.
* Project: CO-IPC
* Filename: os/ipcCps.php
* Change history: 
* 2019-06-03 (Ninh)
*/	

// This program will be started by a cron job
// It will:
// 1) read the ipc-cps.cfg file and use class COM to establish connection on the tty(s)
//    specified in this ipc-cps.cfg file. Stores the COM(s) in array cps[]
// 2) create an UDP server which start a while-loop listening for cmd from the APIs
// 3) start a 5 sec loop 
// 4) if cmd received from APIs, use COM class to send cmd over COM port to CPS-HW
// 5) if no cmd received, for each COM in the array read for at least 100ms from the CPS-HW 
// 6) if got a resp_string, then post it to api nodeOpe->exec_resp()
// 7) if 5-sec timer expires then send cmd-status=all to CPS-HW
// 8) back to beginning of the while-loop

chdir(__DIR__);

include "../class/ipcDebugClass.php";

class UDPSOCK {
    public $ip_addr = '127.0.0.1';
    public $ip_port = 9000;
    public $socket = false;
    public $bind = false;
    public $timeoutSec = 0;
    public $timeoutUsec = 500000;
    public $msg = '';
    public $rsp = '';

    public $rslt = '';
    public $reason = '';


    public function __construct() {
        // create socket to comunicate with IPC loop, UDP type
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, 0);
        if ($this->socket === false) {
            $this->rslt = 'fail';
            $this->reason = "Could not create socket";
            echo $this->reason . "\n";
            return;
        }

        $this->bind = socket_bind($this->socket, $this->ip_addr, $this->ip_port);
        if ($this->bind === false) {
            $this->rslt = 'fail';
            $this->reason = "Could not bind to socket $this->ip_addr:$this->ip_port";
            echo $this->reason . "\n";
            return;
        }
    
        socket_set_nonblock ($this->socket);

        $this->rslt = 'success';
        $this->reason = "(socket: $this->socket) Server $this->ip_addr is listening on port $this->ip_port";
        echo $this->reason . "\n";
    }

    public function setNonBlock() {
        socket_set_nonblock ($this->socket);
        
    }
    public function setBlock(){
        socket_set_block ($this->socket);
        //set timeout for the server
        if($this->timeoutSec != 0 || $this->timeoutUsec != 0) {
            socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $this->timeoutSec, 'usec' => $this->timeoutUsec));
            socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $this->timeoutSec, 'usec' => $this->timeoutUsec));
        }
    }


    public function recv() {
        $this->msg = '';
        if ($this->socket != false) {
            $buf ='';
            $input = socket_recvfrom($this->socket, $buf, 1024, 0, $remote_ip, $remote_port);
            $this->msg = trim($buf);
        }
        return $this->msg;
        echo "UDP-SOCK: recv: " . $this->msg ."\n";
    }


    public function endConnection() {
        socket_close($this->socket);
        $this->socket = null;
    }

}


// class COM:
class COM {
    public $tty = '';
    public $node = 0;
    public $target = '';
    public $psta = 'UNQ';
    public $status_req = '';
    public $conn = false;
    public $resp_str = '';
    public $timeout = 0;
    public $read_intv = 20000; //in msec

    public $rslt ='';
    public $reason = '';
    
    public function __construct($tty, $node) {
        if ($tty != '' && $node > 0) {
            //Connect to serial port
            $conn = dio_open("/dev/$tty", O_RDWR | O_NOCTTY | O_NONBLOCK);
            if ($conn !== false) {
                //if successfully connected
                $this->conn = $conn;
                $this->tty = $tty;

                //configure the connnection's parameters
                dio_tcsetattr($this->conn, array(
                    'baud'=> 115200,
                    'bits'=> 8,
                    'stop'=> 1,
                    'parity'=> 0
                ));

                //set timeout parameter for 500 msec
                $serial_timeoutSec = 0;
                $serial_timeoutUsec = 500000;
                $this->timeout = (float)$serial_timeoutSec + ((float)$serial_timeoutUsec/1000000);
                $this->node = $node;
                $this->tty = $tty;
                $this->status_req = "\$status,source=all,ackid=$this->node-CPS*";
                $this->rslt = 'success';
                $this->reason = 'TTY: ' . $tty . ' IS CONNECTED';        
            }
            else {
                $this->rslt = 'fail';
                $this->reason = 'CANNOT OPEN COM PORT';
            }
        }
        else {
            $this->rslt = 'fail';
            $this->reason = 'INVALID TTY' . $tty;
        }
        return;
    }

    
    public function sendStatusReq() {
        //send satus_req to CPS HW
        //this function returns # bytes written to descriptor
        $cnt = 0;
        if ($this->status_req != '') {
            $cnt = dio_write($this->conn, $this->status_req, strlen($this->status_req));
            if ($cnt === 0) {
                $this->rslt = "fail";
                $this->reason = "SEND STATUS FAILS";
            }
        }
        echo $this->tty . ": send-status-req: cnt=" . $cnt . " : " . $this->status_req . "\n";
        return $cnt;
    }

    public function sendCmd($cmd) {
        //send cmd to CPS HW
        //this function returns # bytes written to descriptor
        $cnt = dio_write($this->conn, $cmd, strlen($cmd));
        if ($cnt === 0) {
            $this->rslt = "fail";
            $this->reason = "SEND CMD FAILS";
        }
        return $cnt;
        echo $this->tty . ": send-cmd: cnt=" . $cnt . " : " . $cmd . "\n";
    }

    // if no data received for 100 msec then return
    // else read until no more data in buf
    public function receiveRsp() {
        $this->resp_str = '';
        $startTime = microtime(true);
        // loop for 1 sec until received some data
        while((microtime(true) - $startTime) < 1) {
            $data = dio_read($this->conn, 1024);
            if (trim($data) !== "") {
                $this->resp_str .= $data;
                //$startTime = microtime(true);
            }
        }
        
        $this->rslt = 'success';
        $this->reason = 'receive successfully';
        echo $this->tty . ": receive-resp: " . $this->resp_str . "\n";
        return $this->resp_str;
    }

    public function close() {
        dio_close($this->conn);
    }

}

function sendToCpsHw($cmd) {

}

function post_resp($resp_str) {
    echo "post-resp: " . "\n";
}

//
//program begins:
//

$cps = array();
$deb = new DEBUG();

// step 1:
// 1) read the ipc-cps.cfg file and use class COM to establish connection on the tty(s)
//    specified in this ipc-cps.cfg file. Stores the COM(s) in array cps[]

$file =  "../../ipc-cps.cfg";
$tty_str = file_get_contents($file);
$tty = explode(",", $tty_str);

$numofcps = count($tty);
for ($i=0; $i<$numofcps; $i++) {
    $node = $i+1;
    $com = trim($tty[$i]);

    $cps[$i] = new COM($com, $node);
    $deb->log($cps[$i]->reason);
    echo $cps[$i]->reason . "\n";
    $cnt = $cps[$i]->sendStatusReq();

    // if ($cnt > 0) {
    //     usleep(500000);
    //     $cps[$i]->receiveRsp();
    //     echo $cps[$i]->resp_str."\n";
    // }

}

// step 2:
// 2) create an UDP server and start a while-loop listening for cmd from the APIs
$udpsock = new UDPSOCK();

$startTime = microtime(true);
while(1) {
    // a) check for response from COM
    //    if there is response, post it to nodeOpe.api
    usleep(500000);
    echo microtime(true) . "\n";

    for ($i=0; $i<$numofcps; $i++) {
        if ($cps[$i]->receiveRsp() != '') {
            post_resp($cps[$i]->resp_str);
        }
    }

    // b) check for incoming cmd from APIs, 
    //    if there is a cmd, send cmd over appropriate COM
    if ($udpsock->recv() != '') {
        sendToCpsHw($udpsock->msg);
    }

    // c) check for 5 sec expires
    //    if expires, send status,source=all to COM, and reset 5 sec timer
    if (microtime(true) - $startTime > 5) {
        for ($i=0; $i<$numofcps; $i++) {
            $cps[$i]->sendStatusReq();
        }
        $startTime = microtime(true);
    }
    // d) loop back
}

?>