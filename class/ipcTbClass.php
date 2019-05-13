<?php

class TB {

    public $id = 0;
    public $tp = "";
    public $name = "";
    public $node;
    public $tb_x = 0;
    public $tbx_row;
    public $tbx_col;
    public $tb_y = 0;
    public $tby_row;
    public $tby_col;
    public $port = "";

    public $rows = [];
    public $rslt = "";
    public $reason = "";

    public function __construct($node=NULL, $tp=NULL) {
        global $db;

        if($node === NULL || $tp === NULL) {
            $this->rslt = 'success';
            $this->reason = "TB CONSTRUCTED";
            return;
        }

        $qry = "SELECT * FROM t_tb WHERE tp='$tp' AND node='$node'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->id = $rows[0]['id'];
                $this->tp = $rows[0]['tp'];
                $this->name = $rows[0]['name'];
                $this->node = $rows[0]['node'];
                $this->tb_x = $rows[0]['tb_x'];
                $this->tbx_row = $rows[0]['tbx_row'];
                $this->tbx_col = $rows[0]['tbx_col'];
                $this->tb_y = $rows[0]['tb_y'];
                $this->tby_row = $rows[0]['tby_row'];
                $this->tby_col = $rows[0]['tby_col'];
                $this->port = $rows[0]['port'];
            }
            else {
                $this->rslt = 'fail';
                $this->reason = "INVALID TB";
            }
            $this->rows = $rows;
        }
     }

    
    
     public function query($node) {
        global $db;

        $qry = "SELECT * FROM t_tb WHERE node='$node'";
        $res = $db->query($qry);
        if(!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            $this->rows = [];
        }
        else {
            $rows = [];
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rows = $rows;
            $this->rslt = "success";
            $this->reason = "TB_QUERIED";
            
        }
    }

    public function setTpName($name) {
        global $db;

        // set TP name values in table based on given information
        $qry = "UPDATE t_tb SET name = '$name' WHERE node = '$this->node' AND tp = '$this->tp'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->query($this->node);
            $this->rslt = "success";
            $this->reason = "TP_NAME_SET" ;
        }
    }

    public function getStatus($tb) {
        global $db;

        if($tb == 'x')
            $qry = "SELECT * FROM t_tb WHERE node='$this->node' AND tb_x <> 0";
        else if($tb == 'y')
            $qry = "SELECT * FROM t_tb WHERE node='$this->node' AND tb_y <> 0";
        else {
            $this->rslt = 'fail';
            $this->reason = "WRONG TEST BUS TYPE ";
            return false;
        }

        $res = $db->query($qry);
        if(!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $rows=[];
            if($res->num_rows >0) {
                while($row = $res->fetch_assoc()) 
                    $rows[] = $row;
            }
            if(count($rows) == 0) {
                $this->rslt = 'success';
                $this->reason = "TEST BUS IS AVAILABLE NOW";
                return true;
            }
            else {
                $this->rslt = 'fail';
                $this->reason = "TEST ACCESS (1-10) IS CURRENTLY CURRENTLY CONNECTED TO TEST BUS-(X/Y)";
                return false;
            }
        }
    }
    
    public function connectTp($tb) {
        global $db;
        if($tb == 'x')
            $qry = "UPDATE t_tb SET tb_x=1, tb_y=0 WHERE node='$this->node' AND tp = '$this->tp'";
        else if($tb == 'y')
            $qry = "UPDATE t_tb SET tb_y=1, tb_x=0 WHERE node='$this->node' AND tp = '$this->tp'";
        else {
            $this->rslt = 'fail';
            $this->reason = "WRONG TEST BUS TYPE";
            return false;
        }

        $res = $db->query($qry);
        
        if (!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            return false;
            // throw new Exception(mysqli_error($db), 50);
        }
        else {
            $this->rslt = 'success';
            $this->reason = 'TB TABLE UPDATED';
            return true;
        }
    }

    public function disconnectTp() {
        global $db;

        $qry = "UPDATE t_tb SET tb_x=0, tb_y=0 WHERE node='$this->node' AND tp = '$this->tp'";
        $res = $db->query($qry);
        
        if (!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            return false;
            // throw new Exception(mysqli_error($db), 50);
        }
        else {
            $this->rslt = 'success';
            $this->reason = 'TEST BUS TABLE UPDATED';
            return true;
        }
    }

    public function connectPort($port) {
        global $db;

        $qry = "UPDATE t_tb SET port='$port' WHERE node='$this->node' AND tp = '$this->tp'";
        $res = $db->query($qry);
        
        if (!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            return false;
            // throw new Exception(mysqli_error($db), 50);
        }
        else {
            $this->rslt = 'success';
            $this->reason = 'PORT UPDATED';
            return true;
        }
    }

    public function disconnectPort() {
        global $db;

        $qry = "UPDATE t_tb SET port='' WHERE node='$this->node' AND tp = '$this->tp'";
        $res = $db->query($qry);
        
        if (!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            return false;
            // throw new Exception(mysqli_error($db), 50);
        }
        else {
            $this->rslt = 'success';
            $this->reason = 'PORT UPDATED';
            return true;
        }
    }

  

}


?>