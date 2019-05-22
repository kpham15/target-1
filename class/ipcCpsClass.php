<?php

class CPSS {
    public $node        = [];
    public $serial_no   = [];
    public $psta        = [];
    public $ssta        = [];
    public $device      = [];

    public $rslt        = "";
    public $reason      = "";
    public $rows        = [];

    public function __construct() {
        global $db;

        $qry = "SELECT * FROM t_cps";
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
                $this->reason   = QUERY_MATCHED;
                $this->rows     = $rows;
                
                for ($i = 0; $i < count($rows); $i++) {

                    // populate member arrays
                    array_push($this->node,      $rows[$i]['node']);
                    array_push($this->serial_no, $rows[$i]['serial_no']);
                    array_push($this->psta,      $rows[$i]['psta']);
                    array_push($this->ssta,      $rows[$i]['ssta']);
                    array_push($this->device,    $rows[$i]['dev']);
                }
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = "CPS QUERY ALL SUCCESS";
                $this->rows   = $rows;
            }
        }
    }

}

Class CPS {
    public $node        = "";
    public $serial_no   = "";
    public $psta        = "";
    public $ssta        = "";
    public $device      = "";

    public $rslt        = "";
    public $reason      = "";
    public $rows        = [];

    public function __construct($node) {
        global $db;

        $qry = "SELECT * FROM t_cps WHERE node = '$node'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt         = SUCCESS;
                $this->reason       = QUERY_MATCHED;
                $this->rows         = $rows;
                $this->node         = $rows[0]['node'];
                $this->serial_no    = $rows[0]['serial_no'];
                $this->psta         = $rows[0]['psta'];
                $this->device       = $rows[0]['device'];

            }
            else {
                $this->rslt   = FAIL;
                $this->reason = "CPS QUERY BY NODE " . $node . " SUCCESS";
                $this->rows   = $rows;
            }
        }
    }


}


?>