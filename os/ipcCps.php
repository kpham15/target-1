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
    public $online = false;
    public $status_req = '';
    public $fd = 0;         // file_discriptor
    public $conn = false;   // tty connect status
    public $resp_str = '';
    public $timeout = 0;
    public $read_intv = 100000; //in msec

    public $rslt ='';
    public $reason = '';
    
    public function __construct($tty, $node) {
        if ($tty != '' && $node > 0) {
            $this->node = $node;
            $this->tty = $tty;
                
            //Connect to serial port
            $this->open();

        }
        else {
            $this->rslt = 'fail';
            $this->reason = 'INVALID TTY' . $tty;
        }
        return;
    }

    public function open() {

        $fd = dio_open("/dev/$this->tty", O_RDWR | O_NOCTTY | O_NONBLOCK);
        if ($fd !== false) {
            $this->fd = $fd;
            $this->conn = true;
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
            $this->status_req = "\$status,source=uuid,device=miox1,ackid=1-sn*";
            $this->rslt = 'success';
            $this->reason = 'TTY: ' . $this->tty . ' IS CONNECTED';
            return true;   
        }
        else {
            $this->rslt = 'fail';
            $this->reason = 'CANNOT OPEN COM PORT';
            return false;
        }
    }


    public function sendStatusReq() {
        //send satus_req to CPS HW
        //this function returns # bytes written to descriptor
        
        $cnt = 0;
        if ($this->status_req != '') {
            $cnt = dio_write($this->fd, $this->status_req, strlen($this->status_req));
            if ($cnt < 0) {
                $this->online = false;
                $this->close();
                $this->rslt = "fail";
                $this->reason = "SEND STATUS FAILS";
                echo $this->tty . ": >>> : " . $this->reason . "\n";
                return false;
            }
            else {
                $this->online = true;
                echo $this->tty . ": >>> : " . $this->status_req . "\n" . $this->reason . "\n";
                return true;
            }
        }
        usleep(50000);
        return true;
    }

    public function sendCmd($cmd) {
        //send cmd to CPS HW
        //this function returns # bytes written to descriptor
        if ($this->conn === false)
            return false;

        $cnt = 0;
        $cnt = dio_write($this->fd, $cmd, strlen($cmd));
        if ($cnt < 0) {
            $this->close();
            $this->rslt = "fail";
            $this->reason = "SEND CMD FAILS";
            return false;
        }
        else {
            echo $this->tty . ": >>> : " . $cmd . "\n" . $this->reason . "\n";
            return true;
        }
        usleep(50000);
    }

    // if no data received for 100 msec then return
    // else read until no more data in buf
    public function receiveRsp() {
        if ($this->conn === false)
            return false;

        $startTime = microtime(true);
        $str = '';
        // loop for 0.5 sec until received some data
        while((microtime(true) - $startTime) < 0.5) {
            if($this->fd !== false) {
                $data = dio_read($this->fd, 1024);
                if (trim($data) !== "") {
                    $this->online = true;
                    $str .= $data;
                    $startTime = microtime(true);
                }
            }
        }

        if ($str != '') {
            $this->resp_str .= $str;
            echo $this->tty . ": <<< : " . $str . "\n";
            return true;
        }
        else {
            return false;
        }
    }

    public function close() {
        dio_close($this->fd);
        $this->online = false;
        $this->conn = false;
        $this->fd = 0;
    }

}

function procUdpMsg($msgObj, $cps) {
    
    if ($msgObj->inst == 'discover') {
        // 
        if ($msgObj->node > 0) {
            $i = $msgObj->node -1;
            if ($cps[$i]->psta == 'UNQ') {
                $cps[$i]->status_req = $msgObj->cmd;
            }
        }
    }
    else if ($msgObj->inst == 'send') {
        if ($msgObj->node > 0) {
            $i = $msgObj->node -1;
            $cps[$i]->sendCmd($msgObj->cmd);
            echo $cps[$i]->tty . ": >>> : " . $msgObj->cmd . "\n";
        }
    }
}

function extractAckidMsg($resp) {
    //get rid of line break character
    $resp = preg_replace("/(\r\n|\n|\r)/",'',$resp);
    //find position of 1st $ackid
    $ackpos = stripos($resp,'$ackid');
    if ($ackpos !== false) {
        $remain = substr($resp, $ackpos);
        //find position of * sign after the $ackid
        $pos = stripos($remain,'*');
        if ($pos !== false) {
            // $str[0] = $ackid.....*
            $str[] = substr($remain,0,$pos+1);
            // $str[1] =  the rest of the string
            $str[] = substr($remain, $pos + 1);
            return $str;
        }
        else
            return false;
    }
    else
        return false;
}
    
  


function post_resp($cpsObj) {
    $str = extractAckidMsg($cpsObj->resp_str);
    if ($str !== false) {
        echo $cpsObj->tty.": ackid_str: " . $str[0] . "\n";
        $cpsObj->resp_str = $str[1];
        echo $cpsObj->tty.": remaining resp_str: " . $cpsObj->resp_str . "\n";
    }
    //report_cps_online($cpsObj);
}

function report_cps_connected($cpsObj) {

    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeOpe",'act'=>'cps_connected',"node"=>$cpsObj->node];
    $result = $postReqObj->asyncPostRequest($url, $params);

    echo "report_cps_connected: " . $cpsObj->tty . "\n";
}


function report_cps_disconnected($cpsObj) {

    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeOpe",'act'=>'cps_disconnected',"node"=>$cpsObj->node];
    $result = $postReqObj->asyncPostRequest($url, $params);

    echo "report_cps_disconnected: ". $cpsObj->tty . "\n";
}

function report_cps_online($cpsObj) {

    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeOpe",'act'=>'cps_online',"node"=>$cpsObj->node, 'msg'=>$cpsObj->resp_str];
    $postReqObj->asyncPostRequest($url, $params);
    echo "report_cps_online: ". $cpsObj->tty . "\n";

}

function report_cps_offline($cpsObj) {

    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeOpe",'act'=>'cps_offline',"node"=>$cpsObj->node];
    $postReqObj->asyncPostRequest($url, $params);
    echo "report_cps_offline: ". $cpsObj->tty . "\n";

}

function pollCps($cpsObj) {
    if ($cpsObj->conn === false) {
        if ($cpsObj->open()) {
            report_cps_connected($cpsObj);
            $cpsObj->sendStatusReq();
        }
        else {
            report_cps_disconnected($cpsObj);
        }
    }
    else if (!$cpsObj->sendStatusReq()) {
        report_cps_disconnected($cpsObj);
    }        
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
$str = file_get_contents($file);
$tty = explode(",", $str);

$numofcps = count($tty);
for ($i=0; $i<$numofcps; $i++) {
    $node = $i+1;
    $com = trim($tty[$i]);

    $cps[$i] = new COM($com, $node);
    $deb->log($cps[$i]->reason);
    pollCps($cps[$i]);

}

// step 2:
// 2) create an UDP server and intial poll-cps
$udpsock = new UDPSOCK();



// 3) start a loop 
$startTime = microtime(true);
while(1) {
    // a) check for 10 sec expires
    //    if expires, send status,source=all to COM, and reset 5 sec timer
    if (microtime(true) - $startTime > 10) {

        $numofcps = count($cps);
        for ($i=0; $i<$numofcps; $i++) {
            pollCps($cps[$i]);
            $startTime = microtime(true);
        }
    }
    
    for ($i=0; $i<$numofcps; $i++) {
        if ($cps[$i]->receiveRsp()) {
            post_resp($cps[$i]);
        }
    }

    // b) check for incoming cmd from APIs, 
    //    if there is a cmd, send cmd over appropriate COM
    $msg = $udpsock->recv();
    if ($msg != '') {
        $msgObj = new UDPMSG($msg);
        procUdpMsg($msgObj, $cps);
    }

    
    // d) loop back
}

?>