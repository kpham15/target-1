<?php
class REF {
    public $id          = 0;
    public $ref         = array();
    public $default     = array();
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
                $this->ref[$rows[$i]["ref"]] = $rows[$i]["val"];
                $this->default[$rows[$i]["ref"]] = $rows[$i]["def"];

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
    
    public function updateRefs($pw_expire, $pw_alert, $pw_reuse, $pw_repeat, $brdcst_del, $user_disable, $user_idle_to, $alm_archv, $alm_del, $cfg_archv, $cfg_del, $prov_archv, $prov_del, $maint_archv, $maint_del, $auto_ckid, $auto_ordno, $date_format, $mtc_restore, $temp_max, $volt_range, $temp_format) {
        $this->updPwExpire      ($pw_expire);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updPwAlert       ($pw_alert);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updPwReuseAndRepeat($pw_reuse, $pw_repeat);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        // $this->updPwReuse       ($pw_reuse);
        // if ($this->rslt != SUCCESS) {
        //     return $this->rslt . $this->reason;
        // }
        // $this->updPwRepeat      ($pw_repeat);
        // if ($this->rslt != SUCCESS) {
        //     return $this->rslt . $this->reason;
        // }
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
        $this->updTempMax       ($temp_max);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updVoltRange     ($volt_range);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updTempFormat     ($temp_format);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        
        
        $this->queryRefs();
        $this->rslt     =   SUCCESS;
        $this->reason   =   "UPDATE SUCCESSFUL";
        return;
    }

    public function updPwExpire($pw_expire) {
        global $db;
        //pw_expire = 0 - 90
        if($pw_expire === "" || !($pw_expire >= 0 && $pw_expire <= 90)) {
            $this->rslt     = FAIL;
            $this->reason   = "pw_expire:Invalid Value ($pw_expire)";
            return;
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
        if($pw_alert === "" || !($pw_alert >= 0 && $pw_alert <= 7)) {
            $this->rslt     = FAIL;
            $this->reason   = "pw_alert:Invalid Value ($pw_alert)";
            return;
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

    public function updPwReuseAndRepeat($pw_reuse, $pw_repeat) {
        global $db;

        // if pw_reuse == 0, pw_repeat == 0
        // if pw_reuse == other, pw_repeat != 0
        if ($pw_reuse == 0 && $pw_repeat != 0) {
            $this->rslt = FAIL;
            $this->reason = "PW REUSE and PW REPEAT must both be N/A";
            return;
        }

        if ($pw_reuse != 0 && $pw_repeat == 0) {
            $this->rslt = FAIL;
            $this->reason = "PW REPEAT cannot be N/A if PW REUSE IS NOT N/A";
            return;
        }

        $this->updPwReuse($pw_reuse);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
        $this->updPwRepeat($pw_repeat);
        if ($this->rslt != SUCCESS) {
            return $this->rslt . $this->reason;
        }
    }

    public function updPwReuse($pw_reuse) {
        global $db;
        //pw_reuse = 0 - 4
        if($pw_reuse === "" || !($pw_reuse >= 0 && $pw_reuse <= 4)) {
            $this->rslt     = FAIL;
            $this->reason   = "pw_reuse:Invalid Value ($pw_reuse)";
            return;
        }

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
        //pw_repeat = 0 - 365
        if($pw_repeat === "" || !($pw_repeat >= 0 && $pw_repeat <= 365)) {
            $this->rslt     = FAIL;
            $this->reason   = "pw_repeat:Invalid Value ($pw_repeat)";
            return;
        }

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
        //brdcst_del = 0 - 14
        if($brdcst_del === "" || !($brdcst_del >= 0 && $brdcst_del <= 14)) {
            $this->rslt     = FAIL;
            $this->reason   = "brdcst_del:Invalid Value ($brdcst_del)";
            return;
        }
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
        //user_disable = 0 - 240
        if($user_disable === "" || !($user_disable >= 0 && $user_disable <= 240)) {
            $this->rslt     = FAIL;
            $this->reason   = "user_disable:Invalid Value ($user_disable)";
            return;
        }

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
        //user_idle_to = 0 - 60
        if($user_idle_to === "" || !($user_idle_to >= 0 && $user_idle_to <= 60)) {
            $this->rslt     = FAIL;
            $this->reason   = "user_idle_to:Invalid Value ($user_idle_to)";
            return;
        }

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
        //alm_archv = 60 - 120
        if($alm_archv === "" || !($alm_archv >= 60 && $alm_archv <= 120)) {
            $this->rslt     = FAIL;
            $this->reason   = "alm_archv:Invalid Value ($alm_archv)";
            return;
        }

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
        //alm_del = 340 - 440
        if($alm_del === "" || !($alm_del >= 340 && $alm_del <= 440)) {
            $this->rslt     = FAIL;
            $this->reason   = "alm_del:Invalid Value ($alm_del)";
            return;
        }

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
        //cfg_archv = 30 - 60
        if($cfg_archv === "" || !($cfg_archv >= 30 && $cfg_archv <= 60)) {
            $this->rslt     = FAIL;
            $this->reason   = "cfg_archv:Invalid Value ($cfg_archv)";
            return;
        }

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
        //cfg_del = 130 - 230
        if($cfg_del === "" || !($cfg_del >= 130 && $cfg_del <= 230)) {
            $this->rslt     = FAIL;
            $this->reason   = "cfg_del:Invalid Value ($cfg_del)";
            return;
        }

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
        //prov_archv = 60 - 120
        if($prov_archv === "" || !($prov_archv >= 60 && $prov_archv <= 120)) {
            $this->rslt     = FAIL;
            $this->reason   = "prov_archv:Invalid Value ($prov_archv)";
            return;
        }

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
        //prov_del = 340 - 440
        if($prov_del === "" || !($prov_del >= 340 && $prov_del <= 440)) {
            $this->rslt     = FAIL;
            $this->reason   = "prov_del:Invalid Value ($prov_del)";
            return;
        }

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
        //maint_archv = 30 - 60
        if($maint_archv === "" || !($maint_archv >= 30 && $maint_archv <= 60)) {
            $this->rslt     = FAIL;
            $this->reason   = "prov_del:Invalid Value ($maint_archv)";
            return;
        }

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
        //maint_del = 130 - 230
        if($maint_del === "" || !($maint_del >= 130 && $maint_del <= 230)) {
            $this->rslt     = FAIL;
            $this->reason   = "prov_del:Invalid Value ($maint_del)";
            return;
        }

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
        //auto_ckid = N/Y
        if($auto_ckid === "" || !($auto_ckid == 'Y' || $auto_ckid == 'N')) {
            $this->rslt     = FAIL;
            $this->reason   = "auto_ckid:Invalid Value ($auto_ckid)";
            return;
        }


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
        //auto_ordno = N/Y
        if($auto_ordno === "" || !($auto_ordno == 'Y' || $auto_ordno == 'N')) {
            $this->rslt     = FAIL;
            $this->reason   = "auto_ordno:Invalid Value ($auto_ordno)";
            return;
        }

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
        //date_format = MM-DD-YYYY, MM-DD-YY, YYYY-MM-DD
        if($date_format === "" || !($date_format == 'MM-DD-YYYY' || $date_format == 'MM-DD-YY' || $date_format == 'YYYY-MM-DD')) {
            $this->rslt     = FAIL;
            $this->reason   = "date_format:Invalid Value ($date_format)";
            return;
        }

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
        //mtc_restore = 15 - 45
        if($mtc_restore === "" || !($mtc_restore >= 15 && $mtc_restore <= 45)) {
            $this->rslt     = FAIL;
            $this->reason   = "mtc_restore:Invalid Value ($mtc_restore)";
            return;
        }

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

    public function updEvtDel($evt_del) {
        global $db;
        //evt_del = 5 - 30
        if($evt_del === "" || !($evt_del >= 5 && $evt_del <= 30)) {
            $this->rslt     = FAIL;
            $this->reason   = "evt_del:Invalid Value ($evt_del)";
            return;
        }

        $qry = "UPDATE t_ref SET val='" . $evt_del . "' WHERE ref = 'evt_del'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "EVT_DEL_UPDATED";
        }
    }

    public function updTempMax($temp_max) {
        global $db;
        //temp_max = 70 - 80
        if($temp_max === "" || !($temp_max >= 70 && $temp_max <= 80)) {
            $this->rslt     = FAIL;
            $this->reason   = "temp_max:Invalid Value ($temp_max)";
            return;
        }

        $qry = "UPDATE t_ref SET val='" . $temp_max . "' WHERE ref = 'temp_max'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "TEMP_MAX_UPDATED";
        }
    }

    public function updVoltRange($volt_range) {
        global $db;
        //volt_range = 35-50, 40-50, 45-55
        if($volt_range === "" || !($volt_range == "35-50" || $volt_range == "40-50" || $volt_range == "45-55")) {
            $this->rslt     = FAIL;
            $this->reason   = "volt_range:Invalid Value ($volt_range)";
            return;
        }

        $qry = "UPDATE t_ref SET val='" . $volt_range . "' WHERE ref = 'volt_range'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "VOLT_RANGE_UPDATED";
        }
    }

    public function updTempFormat($temp_format) {
        global $db;
        //temp_format = F, C
        if ($temp_format === "" || !($temp_format == "F" || $temp_format == "C")) {
            $this->rslt     = FAIL;
            $this->reason   = "temp_format:Invalid Value ($temp_format)";
            return;
        }

        $qry = "UPDATE t_ref SET val='" . $temp_format . "' WHERE ref = 'temp_format'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "TEMP_FORMAT_UPDATED";
        }
    }
}   //end of REF CLASS

?>