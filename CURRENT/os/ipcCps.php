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
include "../class/ipcPostRequestClass.php";

class UDPMSG {
    public $msg = '';
    public $inst = '';
    public $node = '';
    public $target = '';
    public $cmd = '';

    public function __construct($msg) {
        $this->msg = $msg;
        $a = explode(';',$msg);
        for ($i=0; $i<count($a); $i++) {
            $b = explode(':',$a[$i]);
            if ($b[0] == 'inst')
                $this->inst = $b[1];
            else if ($b[0] == 'node')
                $this->node = $b[1];
            else if ($b[0] == 'target')
                $this->target = $b[1];
            else if ($b[0] == 'cmd')
                $this->cmd = $b[1];
            
        }
    }
}

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
        if ($this->socket !== false) {
            $buf ='';
            $input = socket_recvfrom($this->socket, $buf, 1024, 0, $remote_ip, $remote_port);
            $this->msg = trim($buf);
        }
        
        if ($this->msg != '') {

            echo "\nUDP-SOCK: <<< : " . $this->msg ."\n";
        }
        return $this->msg;
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
    public $offline = true;
    public $offline_cnt = 0;
    public $status_req = '';
    public $fd = 0;         // file_discriptor
    public $conn = false;   // tty connect status
    public $resp_str = '';
    public $ackid_str = '';
    public $timeout = 0;
    public $read_intv = 100000; //in msec

    public $rslt ='';
    public $reason = '';
    
    public function __construct($tty, $node) {
        if ($tty != '' && $node > 0) {
            $this->node = $node;
            $this->tty = $tty;
            $this->status_req = "\$status,source=uuid,device=miox1,ackid=$node-sn*";
                
            //Connect to serial port
            if ($this->open()) {
                $this->sendCmdToHw($this->status_req);
            }
            $this->rslt = 'success';
            $this->reason = "TTY:$this->tty FOR NODE:$this->node";
        }
        else {
            $this->rslt = 'fail';
            $this->reason = "INVALID TTY:$tty OR NODE:$node";
        }
    }

    public function open() {

        $fd = dio_open("/dev/$this->tty", O_RDWR | O_NOCTTY | O_NONBLOCK);
        if ($fd > 0) {
            $this->fd = $fd;
            //configure the connnection's parameters
            dio_tcsetattr($this->fd, array(
                'baud'=> 115200,
                'bits'=> 8,
                'stop'=> 1,
                'parity'=> 0
            ));

            //set timeout parameter for 500 msec
            $serial_timeoutSec = 0;
            $serial_timeoutUsec = 500000;
            $this->timeout = (float)$serial_timeoutSec + ((float)$serial_timeoutUsec/1000000);
            $this->rslt = 'success';
            $this->reason = 'TTY: ' . $this->tty . ' IS CONNECTED';
            return true;   
        }
        else {
            $this->fd = 0;
            $this->rslt = 'fail';
            $this->reason = 'CANNOT OPEN COM PORT';
            return false;
        }
    }


    //send satus_req to CPS HW
    public function sendCmdToHw($cmd) {
        
        $this->cmd = $cmd;
        $cnt = 0;
        $cnt = dio_write($this->fd, $this->status_req, strlen($this->status_req));
        if ($cnt < 0) {
            $this->close();
            $this->rslt = "fail";
        }
        else {
            $this->conn = true;
            $this->rslt = 'success';
        }
        $this->reason = "sendStatusReq";
        usleep(50000);
        return $cnt;
    }
        


    // if no data received for 100 msec then return
    // else read until no more data in buf
    public function recvRespFromHw() {

        $startTime = microtime(true);
        $str = '';
        // loop for 0.5 sec until received some data
        while((microtime(true) - $startTime) < 0.5) {
            if ($this->fd !== false) {
                $data = dio_read($this->fd, 1024);
                if (trim($data) !== "") {
                    $str .= $data;
                    $startTime = microtime(true);
                }
            }
            else {
                return false;
            }
        }

        if ($str != '') {
            $this->resp_str .= $str;
            $this->offline = false;
            $this->offline_cnt = 0;
            echo $this->tty . ": <<< : " . $str . "\n";
            return true;
        }
        else {
            return false;
        }
    }

    public function close() {
        dio_close($this->fd);
        $this->offline = true;
        $this->fd = 0;
    }


    public function extractAckidMsg() {
        $this->resp_str = preg_replace("/(\r\n|\n|\r)/",'',$this->resp_str);
        $ackpos = stripos($this->resp_str,'$ackid');
        if ($ackpos !== false) {
            $remain = substr($this->resp_str, $ackpos);
            $pos = stripos($remain,'*');
            if ($pos !== false) {
                $this->ackid_str = substr($remain,0,$pos+1);
                $this->resp_str = substr($remain, $pos + 1);
                return $this->ackid_str;
            }
            else
                return false;
        }
        else
            return false;
    }
        
}

// support functions
function procUdpMsg($msgObj, $cpsLst) {
    
    if ($msgObj->inst == 'discover') {
        // 
        if ($msgObj->node > 0) {
            $i = $msgObj->node -1;
            $cps = $cpsLst[$i];
            if ($cps->psta == 'UNQ') {
                $cps->status_req = $msgObj->cmd;
            }
        }
    }
    else if ($msgObj->inst == 'send') {
        if ($msgObj->node > 0) {
            $i = $msgObj->node -1;
            $cps = $cpsLst[$i];
            $cps->sendCmdToHw($msgObj->cmd);
            echo $cps->tty . ": >>> : " . $msgObj->cmd . "\n";
        }
    }
}


function post_resp($cps) {
    $cps->extractAckidMsg();
    if ($cps->ackid_str != '') {
        report_cps_online($cps);
    }
}

function report_cps_connected($cps) {

    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeOpe",'act'=>'cps_connected',"node"=>$cps->node];
    //$result = $postReqObj->asyncPostRequest($url, $params);
    $result = $postReqObj->syncPostRequest($url, $params);

    echo "report_cps_connected: " . $cps->tty . "\n";
    print_r($result);
}


function report_cps_disconnected($cps) {

    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeOpe",'act'=>'cps_disconnected',"node"=>$cps->node];
    //$result = $postReqObj->asyncPostRequest($url, $params);
    $result = $postReqObj->syncPostRequest($url, $params);

    echo "report_cps_disconnected: ". $cps->tty . "\n";
    print_r($result);
}

function report_cps_online($cps) {

    if ($cps->ackid_str != '') {
        $postReqObj = new POST_REQUEST();
        $url = "ipcDispatch.php";
        $params = ["user"=>"SYSTEM", "api"=>"ipcNodeOpe",'act'=>'cps_online',"node"=>$cps->node, 'msg'=>$cps->ackid_str];
        //$postReqObj->asyncPostRequest($url, $params);
        $result = $postReqObj->syncPostRequest($url, $params);
        echo "report_cps_online: ". $cps->tty . "\n";
        print_r($result);
    }
}

function report_cps_offline($cps) {

    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeOpe",'act'=>'cps_offline',"node"=>$cps->node];
    $postReqObj->asyncPostRequest($url, $params);
    echo "report_cps_offline: ". $cps->tty . "\n";

}

function polling_Cps($cps) {
    if ($cps->fd === 0) {
        if ($cps->open()) {
            report_cps_connected($cps);
            $cps->sendStatusReq();
        }
        else {
            report_cps_disconnected($cps);
        }
    }
    else if (!$cps->sendStatusReq()) {
        report_cps_disconnected($cps);
    }        
}


//
//program begins:
//

$cpsLst = array();
$deb = new DEBUG();


// step 1:
// 1) read the ipc-cps.cfg file and use class COM to establish connection on the tty(s)
//    specified in this ipc-cps.cfg file. Stores the COM(s) in array cpsLst[]

$file =  "../../ipc-cps.cfg";
$str = file_get_contents($file);
$tty = explode(",", $str);

$numofcps = count($tty);
for ($i=0; $i<$numofcps; $i++) {
    $node = $i+1;
    $com = trim($tty[$i]);
    $cpsLst[$i] = new COM($com, $node);
    //polling_Cps($cpsLst[$i]);

}

// report_cps_connected($cpsLst[0]);
// return;

// step 2:
// 2) create an UDP server and intial poll-cps
$udpsock = new UDPSOCK();



// 3) start a loop 
$startTime = microtime(true);
while(1) {
    // a) check for 10 sec expires
    //    if expires, send status,source=all to COM, and reset 5 sec timer
    if (microtime(true) - $startTime > 5) {

        $numofcps = count($cpsLst);
        for ($i=0; $i<$numofcps; $i++) {
            $cps = $cpsLst[$i];
            if ($cps->fd > 0) {
                if ($cps->offline == false) {
                    $cps->offline_cnt++;
                    if ($cps->offline_cnt > 2) {
                        report_cps_offline($cps);
                    }
                }
                $cps->sendCmdToHw($cps->status_req);
            }
        }
        $startTime = microtime(true);
    }
    
    // b) check for any resonse from cps-hw
    for ($i=0; $i<$numofcps; $i++) {
        $cps = $cpsLst[$i];
        if ($cps->recvRespFromHw()) {
            post_resp($cps);
        }
    }

    // c) check for incoming cmd from APIs, 
    //    if there is a cmd, send cmd over appropriate COM
    $msg = $udpsock->recv();
    if ($msg != '') {
        $msgObj = new UDPMSG($msg);
        procUdpMsg($msgObj, $cpsLst);
    }

    
    // d) loop back
}

?>