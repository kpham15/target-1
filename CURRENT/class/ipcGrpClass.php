<?php 

class GRP {
    public $id          = 0;
    public $ugrp        = "";
    public $brdcst      = "";
    public $setuser     = "";
    public $pwreset     = "";
    public $pwchg       = "";
    public $usersrch    = "";
    public $bkupdb      = "";
    public $resetsys    = "";
    public $setwc       = "";
    public $portmap     = "";
    public $swupd       = "";
    public $prov        = "";
    public $maint       = "";
    public $mtxcard     = "";
    public $almadm      = "";
    public $pathadm     = "";
    public $sysadm      = "";
    public $ipcadm      = "";
    public $report      = "";
    public $grp         = 0;

    public $rslt        = "";
    public $reason      = "";
    public $rows        = [];


    public function __construct($id=NULL) {
        global $db;
        if ($id == NULL) {
            $qry = "SELECT * FROM t_grp 
                    ORDER BY id ASC
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
        else {
            $qry = "SELECT * FROM t_grp 
                    WHERE id  =   '$id'
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                    $this->id       = $rows[0]['id'];
                    $this->ugrp     = $rows[0]['ugrp'];
                    $this->brdcst   = $rows[0]['brdcst'];
                    $this->setuser  = $rows[0]['setuser'];
                    $this->pwreset  = $rows[0]['pwreset'];
                    $this->pwchg    = $rows[0]['pwchg'];
                    $this->usersrch = $rows[0]['usersrch'];
                    $this->bkupdb   = $rows[0]['bkupdb'];
                    $this->resetsys = $rows[0]['resetsys'];
                    $this->setwc    = $rows[0]['setwc'];
                    $this->portmap  = $rows[0]['portmap'];
                    $this->swupd    = $rows[0]['swupd'];
                    $this->prov     = $rows[0]['prov'];
                    $this->maint    = $rows[0]['maint'];
                    $this->mtxcard  = $rows[0]['mtxcard'];
                    $this->almadm   = $rows[0]['almadm'];
                    $this->pathadm  = $rows[0]['pathadm'];
                    $this->sysadm   = $rows[0]['sysadm'];
                    $this->ipcadm   = $rows[0]['ipcadm'];
                    $this->report   = $rows[0]['report'];
                    $this->grp      = $rows[0]['grp'];
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }

    
}


