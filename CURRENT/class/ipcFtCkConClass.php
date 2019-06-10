<?php

class FTCKCON {
    public $id      = 0;
    public $ordno   = "";
    public $ctid    = "";
    public $act     = "";
    public $op      = "";
    public $ffactyp = "";
    public $ffacid  = "";
    public $ffrloc  = "";
    public $tfactyp = "";
    public $tfacid  = "";
    public $tfrloc  = "";
    public $stat    = "";

    public $rslt    = "";
    public $reason  = "";
    public $rows    = [];

    public function __construct($ctid=NULL, $ordno=NULL) {
        global $db;

        if ($ctid == NULL) {
            $qry = "SELECT * FROM t_Ckcon ORDER BY ctid ASC";
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
                    $this->rslt   = SUCCESS;
                    $this->reason = QUERY_MATCHED;
                    $this->rows   = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
        else if ($ordno !== NULL) {
            $qry = "SELECT * FROM t_Ckcon
                    WHERE ctid = '$ctid'
                    AND ordno = '$ordno'
                    ";
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
                    $this->rslt         = SUCCESS;
                    $this->reason       = QUERY_MATCHED;
                    $this->rows         = $rows;
                    $this->id           = $rows[0]['id'];
                    $this->ordno        = $rows[0]['ordno'];
                    $this->ctid         = $rows[0]['ctid'];
                    $this->act          = $rows[0]['act'];
                    $this->op           = $rows[0]['op'];
                    $this->ffactyp      = $rows[0]['ffactyp'];
                    $this->ffacid       = $rows[0]['ffacid'];
                    $this->ffrloc       = $rows[0]['ffrloc'];
                    $this->tfactyp      = $rows[0]['tfactyp'];
                    $this->tfacid       = $rows[0]['tfacid'];
                    $this->tfrloc       = $rows[0]['tfrloc'];
                    $this->stat         = $rows[0]['stat'];                
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }

    public function query($ctid) {
        global $db;

        $qry = "SELECT * FROM t_Ckt WHERE ctid = '$ctid'";
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
                $this->rslt   = SUCCESS;
                $this->reason = QUERY_MATCHED;
                $this->rows   = $rows;
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
                $this->rows   = $rows;
            }
        }
    }

}

?>