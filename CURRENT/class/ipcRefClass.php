<?php
class REF {
    public $id          = 0;
    public $ref         = array();
    public $val         = 0;

    public $rslt        = "";
    public $reason      = "";
    public $rows        = [];
    
    public function __construct() {

        $this->queryRefs();
    }

    public function queryRefs() {
        global $db;
    
        $qry = "SELECT * FROM t_ref";
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
            for ($i=0; $i<count($rows); $i++) {
                $this->ref[0][$rows[$i]["ref"]] = $rows[$i]["val"];
            }
        }
    }

    public function resetRefs() {
        global $db;

        $qry = "UPDATE t_ref SET val = def";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        $this->rslt = "success";
        $this->reason = "REF has been reset";
    }
    
    public function updateRefs($pw_expire, $pw_alert, $pw_reuse, $pw_repeat, $brdcst_del, $user_disable, $user_idle_to, $alm_archv, $alm_del, $cfg_archv, $cfg_del, $prov_archv, $prov_del, $maint_archv, $maint_del, $auto_ckid, $auto_ordno, $date_format, $mtc_restore) {
        $this->updPwExpire      ($pw_expire);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updPwAlert       ($pw_alert);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updPwReuse       ($pw_reuse);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updPwRepeat      ($pw_repeat);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updBrdcstDel     ($brdcst_del);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updUserDisable   ($user_disable);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updUserIdleTo    ($user_idle_to);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updAlmArchv      ($alm_archv);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updAlmDel        ($alm_del);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updCfgArchv      ($cfg_archv);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updCfgDel        ($cfg_del);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updProvArchv     ($prov_archv);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updProvDel       ($prov_del);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updMaintArchv    ($maint_archv);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updMaintDel      ($maint_del);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updAutoCkid      ($auto_ckid);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updAutoOrdno     ($auto_ordno);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updDateFormat    ($date_format);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updMtcRestore    ($mtc_restore);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        
        $this->queryRefs();
        $this->rslt     =   SUCCESS;
        $this->reason   =   "REFS HAVE BEEN UPDATED";
        return;
    }

    public function updPwExpire($pw_expire) {
        global $db;
        //pw_expire = 0 - 90
        if(!($pw_expire >= 0 && $pw_expire <= 90)) {
            $this->rslt     = FAIL;
            $this->reason   = "pw_expire is out of range (0-90)";
        }

        $qry = "UPDATE t_ref SET val='$pw_expire' WHERE ref = 'pw_expire'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason   = "PW_EXPIRE_UPDATED";
        }
    }
    
    public function updPwAlert($pw_alert) {
        global $db;
        //pw_alert = 0 - 7
        if(!($pw_alert >= 0 && $pw_alert <= 7)) {
            $this->rslt     = FAIL;
            $this->reason   = "pw_alert is out of range (0-90)";
        }

        $qry = "UPDATE t_ref SET val='" . $pw_alert . "' WHERE ref = 'pw_alert'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason   = "PW_ALERT_UPDATED";
        }
    }

    public function updPwReuse($pw_reuse) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $pw_reuse . "' WHERE ref = 'pw_reuse'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "PW_REUSE_UPDATED";
        }
    }

    public function updPwRepeat($pw_repeat) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $pw_repeat . "' WHERE ref = 'pw_repeat'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "PW_REPEAT_UPDATED";
        }
    }

    public function updBrdcstDel($brdcst_del) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $brdcst_del . "' WHERE ref = 'brdcst_del'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "BROADCAST_DEL_UPDATED";
        }
    }

    public function updUserDisable($user_disable) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $user_disable . "' WHERE ref = 'user_disable'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "USER_DISABLE_UPDATED";
        }
    }

    public function updUserIdleTo($user_idle_to) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $user_idle_to . "' WHERE ref = 'user_idle_to'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "USER_IDLE_UPDATED";
        }
    }

    public function updAlmArchv($alm_archv) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $alm_archv . "' WHERE ref = 'alm_archv'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason     = "ALM_ARCHV_UPDATED";
        }
    }

    public function updAlmDel($alm_del) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $alm_del . "' WHERE ref = 'alm_del'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "ALM_DEL_UPDATED";
        }
    }

    public function updCfgArchv($cfg_archv) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $cfg_archv . "' WHERE ref = 'cfg_archv'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason   = "CFG_ARCHV_UPDATED";
        }
    }

    public function updCfgDel($cfg_del) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $cfg_del . "' WHERE ref = 'cfg_del'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason   = "CFG_DEL_UPDATED";
        }
    }

    public function updProvArchv($prov_archv) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $prov_archv . "' WHERE ref = 'prov_archv'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "PROV_ARCHV_UPDATED";
        }
    }

    public function updProvDel($prov_del) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $prov_del . "' WHERE ref = 'prov_del'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason   = "PROV_DEL_UPDATED";
        }
    }

    public function updMaintArchv($maint_archv) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $maint_archv . "' WHERE ref = 'maint_archv'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "MAINT_ARCHV_UPDATED";
        }
    }

    public function updMaintDel($maint_del) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $maint_del . "' WHERE ref = 'maint_del'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "MAINT_DEL_UPDATED";
        }
    }

    public function updAutoCkid($auto_ckid) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $auto_ckid . "' WHERE ref = 'auto_ckid'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "AUTO_CKID_UPDATED";
        }
    }

    public function updAutoOrdno($auto_ordno) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $auto_ordno . "' WHERE ref = 'auto_ordno'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason   = "AUTO_ORDNO_UPDATED";
        }
    }

    public function updDateFormat($date_format) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $date_format . "' WHERE ref = 'date_format'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "DATE_FORMAT_UPDATED";
        }
    }

    public function updMtcRestore($mtc_restore) {
        global $db;

        $qry = "UPDATE t_ref SET val='" . $mtc_restore . "' WHERE ref = 'mtc_restore'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "MTC_RESTORE_UPDATED";
        }
    }

}   //end of REF CLASS

?>