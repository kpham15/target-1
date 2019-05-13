<?php

class CMD {

    public $rslt;
    public $reason;
    public $rows;
    
    public function __construct() {

    }
    

    public function getCmdList() {
        global $db;
        
        $this->rows = [];
        $qry = "SELECT * FROM t_cmdque WHERE stat <> 'COMPL'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            $this->rslt = "success";
            $this->reason = "QUERY_SUCCESS";
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rows = $rows;
        }
        
    }

    public function deleteCmd($ackid) {
        global $db;
         
        $qry = "DELETE FROM t_cmdque WHERE ackid = $ackid";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }
        if (mysqli_affected_rows($db) > 0) {
            $this->rslt = "success";
            $this->reason = 'CMD DELETED';
        }
        else {
            $this->rslt = 'fail';
            $this->reason = 'INVALID ACKID';
        }
        
    }

    public function addCmd($node, $ackid, $cmd) {
        global $db;
         
        $qry = "INSERT INTO t_cmdque (time,node,ackid,stat,cmd) VALUES(now(),$node, '$ackid', 'NEW','$cmd')";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }

        $this->rslt = "success";
        $this->reason = 'CMD INSERTED';
    }

    public function updCmd($ackid, $stat, $rsp) {
        global $db;
         
        $qry = "UPDATE t_cmdque SET stat = '$stat', rsp = '$rsp' WHERE ackid = '$ackid'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }

        $this->rslt = "success";
        $this->reason = 'CMD UPDATED';
    }

    public function createCmd($act, $pathId, $path) {
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
            $rcArray[$node] .= ",ROW=$row,COL=$col";

        }

        //create cmd and add cmd into t_cmdque
        foreach ($rcArray as $node => $rcs) {
            $act_upper = strtoupper($act);
            $ackid = "PATH-$node-$pathId";
            $cmd = "\$COMMAND,ACTION=$act_upper". $rcs.",ACKID=$ackid*";

            $this->addCmd($node,$ackid,$cmd); //Do we need to check error of this query
        }

        $this->rslt = 'success';
        $this->reason = 'CMD CREATED SUCCESSFULLY';
        return true;


    }

    public function createTestPathCmd($act, $portId, $node, $row, $col) {
                
        $act_upper = strtoupper($act);
        $ackid = "TP-$node-$portId";
        $cmd = "\$COMMAND,ACTION=$act_upper,ROW=$row,COL=$col,ACKID=$ackid*";

        $this->addCmd($node,$ackid,$cmd); //Do we need to check error of this query
        $this->rslt = 'success';
        $this->reason = 'TEST CONN CMD CREATED SUCCESSFULLY';
        return true;
    }

}

?>