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
    public $id;
    public $time;
    public $node;
    public $ackid;
    public $stat;
    public $cmd;
    public $rsp;

    public $rslt;
    public $reason;
    public $rows;
    
    public function __construct($ackid=NULL) {
        if($ackid == NULL) {
            return;
        }
        $qry = "SELECT * FROM t_cmdque WHERE ackid = '$ackid' LIMIT 1";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt     = SUCCESS;
                $this->reason   = "ACKID FOUND";
              
                $this->id   = $rows[0]['id'];
                $this->time   = $rows[0]['time'];
                $this->node   = $rows[0]['node'];
                $this->ackid   = $rows[0]['ackid'];
                $this->stat   = $rows[0]['stat'];
                $this->cmd   = $rows[0]['cmd'];
                $this->rsp   = $rows[0]['rsp'];

            }
            else {
                $this->rslt   = SUCCESS;
                $this->reason = "ACKID NOT FOUND";
                $this->rows   = $rows;
            }
        }

    }

    public function updCmd($stat, $rsp) {
        global $db;

        $qry = "UPDATE t_cmdque SET stat='$stat', rsp='$rsp' WHERE ackid='$this->ackid'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->stat = $stat;
            $this->rsp = $rsp;
            $this->rslt = SUCCESS;
            $this->reason = 'CMD UPDATED';
            return true;
        }
    }
    
    public function updateStat($stat) {
        global $db;

        $qry = "UPDATE t_cmdque SET stat='$stat' WHERE ackid='$this->ackid'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->stat = $stat;
            $this->rslt = SUCCESS;
            $this->reason = 'STAT UPDATED';
            return true;
        }
    }

    public function addCmd($node, $ackid, $cmd) {
        global $db;

        if ($node == '') {
            $this->rslt = FAIL;
            $this->reason = "INVALID NODE";
            return false;
        }
        if ($ackid == '') {
            $this->rslt = FAIL;
            $this->reason = "MISSING ACKID";
            return false;
        }

        if ($cmd == '') {
            $this->rslt = FAIL;
            $this->reason = "MISSING CMD";
            return false;
        }
    
        $qry = "INSERT INTO t_cmdque (time, node, ackid, stat, cmd) VALUES (now(), '$node', '$ackid', 'PENDING', $cmd)";
                
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = SUCCESS;
            $this->reason = 'CMD ADDED';
            return true;
        }
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

    public function sendComPort($node, $com_port) {
        $cmd = "com_port=$com_port";
        $this->sendCmd($cmd, $node);
        if($this->rslt == 'fail') 
            return false;
        $this->rslt = 'success';
        $this->reason = 'SEND COMPORT SUCCESSFULLY';
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