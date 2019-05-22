<?php
// set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
//     // error was suppressed with the @-operator
//     if (0 === error_reporting()) {
//         return false;
//     }

//     throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
// });

// error_reporting(E_ALL);

class CMD {

    public $rslt;
    public $reason;
    public $rows;
    
    public function __construct() {

    }
    

    public function sendPathCmd($act, $pathId, $path) {
        global $db;

        $rcArray = []; //to store row,col string for each node;
        $rcObj = new RC(); //obj to query row,col 

        $relayArray = explode("-",$path);
        for($i=0; $i < count($relayArray); $i++) {
            $relayExtract = explode(".", trim($relayArray[$i]),2);
            $node = $relayExtract[0];
            $relay = $relayExtract[1];
            $rcObj->queryRC($relay);
            if($rcObj->rslt == 'fail') {
                $this->rslt = 'fail';
                $this->reason = 'RC QUERIED FOR '.$relay.' FAILED';
                return false;
            }
            
           //Put row, col into $rcArray
            $rowcol = $rcObj->rows[0];
            $row = $rowcol['row'];
            $col = $rowcol['col'];
            if(!isset($rcArray[$node])) {
                $rcArray[$node] = ""; //initialize key/value before append value to it
            }
            $rcArray[$node] .= ",row=$row,col=$col";

        }

        //create cmd and add cmd into t_cmdque
        foreach ($rcArray as $node => $rcs) {
            $ackid = "path-$node-$pathId";
            $cmd = "\$command,action=$act". $rcs.",ackid=$ackid*";
            $this->sendCmd($cmd, $node);
            if($this->rslt == 'fail') return;
        }

        $this->rslt = 'success';
        $this->reason = 'SEND PATH CMD SUCCESSFULLY';
        return true;


    }

    public function sendTestedPortCmd($act,$node,$col,$row) {  
        $cmd = "\$command,action=$act,col=$col,row=$row,ackid=$node-TBX*";
        $this->sendCmd($cmd, $node);
        if($this->rslt == 'fail') return;
        $this->rslt = 'success';
        $this->reason = 'SEND TEST CMD SUCCESSFULLY';
        return true;
    }

    
    public function sendZPortCmd($act, $portId, $node) {
        $cmd = "\$command,action=$act,bus=x,tap=$portId,ackid=$node-TAP*";
        $this->sendCmd($cmd, $node);
        if($this->rslt == 'fail') return;
        $this->rslt = 'success';
        $this->reason = 'SEND TEST CMD SUCCESSFULLY';
        return true;
    }

    public function sendDiscoverCmd($node) {
        $cmd = "\$status,source=uuid,device=backplane,ackid=$node-bkpln*";
        $this->sendCmd($cmd, $node);
        if($this->rslt == 'fail') return;
        $this->rslt = 'success';
        $this->reason = 'SEND TEST CMD SUCCESSFULLY';
        return true;

    }

    public function sendStartCmd($node) {
        $cmd = "start";
        $this->sendCmd($cmd, $node);
        if($this->rslt == 'fail') return;
        $this->rslt = 'success';
        $this->reason = 'SEND TEST CMD SUCCESSFULLY';
        return true;

    }

    public function sendStopCmd($node) {
        $cmd = "stop";
        $this->sendCmd($cmd, $node);
        if($this->rslt == 'fail') return;
        $this->rslt = 'success';
        $this->reason = 'SEND TEST CMD SUCCESSFULLY';
        return true;

    }


    public function sendCmd($cmd, $node) {
        //create socket UDP
        $socket = socket_create(AF_INET, SOCK_DGRAM, 0);
        if($socket === false) {
            $this->rslt = 'fail';
            $this->reason = 'CAN NOT CREATE UDP SOCKET';
            return;
        }
        socket_set_nonblock ($socket);
        $ip_port = 9000 + $node;
        $sendCmd = socket_sendto($socket,$cmd, 1024,0, '127.0.0.1', $ip_port);
        if($sendCmd === false) {
            $this->rslt = 'fail';
            $this->reason = 'CAN NOT SEND CMD';
            return;
        }
    } 

}

?>