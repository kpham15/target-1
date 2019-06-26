<?php

class FTCKT {
    public $id          = 0;
    public $ordno       = "";
    public $cttype      = "";
    public $ctid        = "";
    public $octtype     = "";
    public $octid       = "";
    public $adsr        = "";
    public $ssm         = "";
    public $ssp         = "";
    public $oc          = "";
    public $act         = "";
    public $lst         = "";
    public $cls         = "";
    public $noscm       = "";
    public $relordno    = "";
    public $relcttype   = "";
    public $relctid     = "";
    public $relot       = "";
    public $relact      = "";

    public $rslt        = "";
    public $reason      = "";
    public $rows        = [];

    public function __construct($ctid=NULL) {
        global $db;

        if ($ctid == NULL) {
            $qry = "SELECT * FROM t_Ckt ORDER BY ctid ASC";
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
            $qry = "SELECT * FROM t_Ckt
                    WHERE ctid = '$ctid'
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
                    $this->rslt      = SUCCESS;
                    $this->reason    = QUERY_MATCHED;
                    $this->rows      = $rows;
                    $this->id        = $rows[0]['id       '];
                    $this->ordno     = $rows[0]['ordno    '];
                    $this->cttype    = $rows[0]['cttype   '];
                    $this->ctid      = $rows[0]['ctid     '];
                    $this->octtype   = $rows[0]['octtype  '];
                    $this->octid     = $rows[0]['octid    '];
                    $this->adsr      = $rows[0]['adsr     '];
                    $this->ssm       = $rows[0]['ssm      '];
                    $this->ssp       = $rows[0]['ssp      '];
                    $this->oc        = $rows[0]['oc       '];
                    $this->act       = $rows[0]['act      '];
                    $this->lst       = $rows[0]['lst      '];
                    $this->cls       = $rows[0]['cls      '];
                    $this->noscm     = $rows[0]['noscm    '];
                    $this->relordno  = $rows[0]['relordno '];
                    $this->relcttype = $rows[0]['relcttype'];
                    $this->relctid   = $rows[0]['relctid  '];
                    $this->relot     = $rows[0]['relot    '];
                    $this->relact    = $rows[0]['relact   '];
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

        $qry = "SELECT * FROM t_Ckt WHERE ctid LIKE '$ctid'";
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

    public function queryCkt($ordno) {
        global $db;

        $qry = "SELECT * FROM t_Ckt WHERE ordno = '$ordno'";
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