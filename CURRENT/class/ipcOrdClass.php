<?php

class ORD {
    public $id      = 0;
    public $ordno   = "";
    public $ot      = "";
    public $cdd     = "";
    public $dd      = "";
    public $fdd     = "";
    public $fdt     = "";
    public $wc      = "";
    public $pri     = "";

    public $rslt    = "";
    public $reason  = "";
    public $rows    = [];
    
    public function __construct($ordno=NULL) {
        global $db;

        if ($ordno == NULL) {
            $qry = "SELECT * FROM t_Ord ORDER BY ordno ASC";
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
        else {
            $qry = "SELECT * FROM t_Ord 
                    WHERE ordno LIKE '%$ordno%'
                    LIMIT 1
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
                    $this->rslt   = SUCCESS;
                    $this->reason = QUERY_MATCHED;
                    $this->rows   = $rows;
                    $this->id     = $rows[0]['id'];
                    $this->ordno  = $rows[0]['ordno'];
                    $this->ot     = $rows[0]['ot'];
                    $this->cdd    = $rows[0]['cdd'];
                    $this->dd     = $rows[0]['dd'];
                    $this->fdd    = $rows[0]['fdd'];
                    $this->fdt    = $rows[0]['fdt'];
                    $this->wc     = $rows[0]['wc'];
                    $this->pri    = $rows[0]['pri'];
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }

    public function query($ordno, $ot, $wc, $pri, $stat, $cdd, $dd, $fdd, $fdt) {
        global $db;

        $qry = "SELECT * FROM t_Ord WHERE ordno LIKE '%$ordno%' 
                AND ot   LIKE '%$ot%' 
                AND wc   LIKE '%$wc%' 
                AND pri  LIKE '%$pri%' 
                AND stat LIKE '%$stat%' 
                AND cdd  LIKE '%$cdd%' 
                AND dd   LIKE '%$dd%' 
                AND fdd  LIKE '%$fdd%' 
                AND fdt  LIKE '%$fdt%' 
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

    public function findOrder($ordno, $ot, $wc, $pri, $stat) {
        global $db;

        $qry = "SELECT * FROM t_Ord WHERE ordno LIKE '%$ordno%' 
                AND ot   LIKE '%$ot%' 
                AND wc   LIKE '%$wc%' 
                AND pri  LIKE '%$pri%' 
                AND stat LIKE '%$stat%' 
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

    public function findOrderByDD($cdd, $dd, $fdd, $fdt) {
        global $db;

        $qry = "SELECT * FROM t_Ord WHERE cdd  LIKE '%$cdd%' 
                AND dd   LIKE '%$dd%' 
                AND fdd  LIKE '%$fdd%' 
                AND fdt  LIKE '%$fdt%' 
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