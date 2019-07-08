<?php

//initialize
$action = "";
if (isset($_POST['action'])) {
    $action = $_POST['action'];
}

$pw_expire = "";
if (isset($_POST['pw_expire'])) {
    $pw_expire = $_POST['pw_expire'];
}

$pw_alert = "";
if (isset($_POST['pw_alert'])) {
    $pw_alert = $_POST['pw_alert'];
}

$pw_reuse = "";
if (isset($_POST['pw_reuse'])) {
    $pw_reuse = $_POST['pw_reuse'];
}

$pw_repeat = "";
if (isset($_POST['pw_repeat'])) {
    $pw_repeat = $_POST['pw_repeat'];
}

$brdcst_del = "";
if (isset($_POST['brdcst_del'])) {
    $brdcst_del = $_POST['brdcst_del'];
}

$user_disable = "";
if (isset($_POST['user_disable'])) {
    $user_disable = $_POST['user_disable'];
}

$user_idle_to = "";
if (isset($_POST['user_idle_to'])) {
    $user_idle_to = $_POST['user_idle_to'];
}

$alm_archv = "";
if (isset($_POST['alm_archv'])) {
    $alm_archv = $_POST['alm_archv'];
}

$alm_del = "";
if (isset($_POST['alm_del'])) {
    $alm_del = $_POST['alm_del'];
}

$cfg_archv = "";
if (isset($_POST['cfg_archv'])) {
    $cfg_archv = $_POST['cfg_archv'];
}

$cfg_del = "";
if (isset($_POST['cfg_del'])) {
    $cfg_del = $_POST['cfg_del'];
}

$prov_archv = "";
if (isset($_POST['prov_archv'])) {
    $prov_archv = $_POST['prov_archv'];
}

$prov_del = "";
if (isset($_POST['prov_del'])) {
    $prov_del = $_POST['prov_del'];
}

$maint_archv = "";
if (isset($_POST['maint_archv'])) {
    $maint_archv = $_POST['maint_archv'];
}

$maint_del = "";
if (isset($_POST['maint_del'])) {
    $maint_del = $_POST['maint_del'];
}

$auto_ckid = "";
if (isset($_POST['auto_ckid'])) {
    $auto_ckid = $_POST['auto_ckid'];
}

$auto_ordno = "";
if (isset($_POST['auto_ordno'])) {
    $auto_ordno = $_POST['auto_ordno'];
}

$date_format = "";
if (isset($_POST['date_format'])) {
    $date_format = $_POST['date_format'];
}

$mtc_restore = "";
if (isset($_POST['mtc_restore'])) {
    $mtc_restore = $_POST['mtc_restore'];
}

$temp_max = "";
if (isset($_POST['temp_max'])) {
    $temp_max = $_POST['temp_max'];
}

$volt_range = "";
if (isset($_POST['volt_range'])) {
    $volt_range = $_POST['volt_range'];
}

$temp_format = "";
if (isset($_POST['temp_format'])) {
    $temp_format = $_POST['temp_format'];
}


$evtLog = new EVENTLOG($user, "IPC ADMINISTRATION", "IPC REFERENCE DATA", $action, '');

$refObj = new REF();
if ($refObj->rslt != SUCCESS) {
    $result['rslt']   = $refObj->rslt;
    $result['reason'] = $refObj->reason;
    $evtLog->log($result["rslt"], $result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}


// DISPATCH
if ($action == "query") {
    $result = queryRefs($refObj);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($action == "update") {
    $result = updateRefs($userObj,$refObj, $pw_expire, $pw_alert, $pw_reuse, $pw_repeat, $brdcst_del, $user_disable, $user_idle_to, $alm_archv, $alm_del, $cfg_archv, $cfg_del, $prov_archv, $prov_del, $maint_archv, $maint_del, $auto_ckid, $auto_ordno, $date_format, $mtc_restore, $temp_max, $volt_range, $temp_format);
    $evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($action == "reset") {
    $result = resetRefs($userObj, $refObj);
    $evtLog->log($result["rslt"], $result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

/**
 * Functions
 */

function queryRefs($refObj) {
    $refObj->queryRefs();
    $result['rslt']   = $refObj->rslt;
    $result['reason'] = $refObj->reason;
    $result['rows']   = $refObj->ref;
    return $result;

}

function updateRefs($userObj,$refObj, $pw_expire, $pw_alert, $pw_reuse, $pw_repeat, $brdcst_del, $user_disable, $user_idle_to, $alm_archv, $alm_del, $cfg_archv, $cfg_del, $prov_archv, $prov_del, $maint_archv, $maint_del, $auto_ckid, $auto_ordno, $date_format, $mtc_restore, $temp_max, $volt_range, $temp_format) {

    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    $result['log'] = "ACTION = UPDATE";
    
    if ($pw_expire != $refObj->ref['pw_expire'])
    $result['log'] .= " | PW_EXPIRE = " . $refObj->ref['pw_expire'] . " -> " . $pw_expire;

    if ($pw_alert != $refObj->ref['pw_alert'])
    $result['log'] .= " | PW_ALERT = " . $refObj->ref['pw_alert'] . " -> " . $pw_alert;

    if ($pw_reuse != $refObj->ref['pw_reuse'])
    $result['log'] .= " | PW_REUSE = " . $refObj->ref['pw_reuse'] . " -> " . $pw_reuse;

    if ($pw_repeat != $refObj->ref['pw_repeat'])
    $result['log'] .= " | PW_REPEAT = " . $refObj->ref['pw_repeat'] . " -> " . $pw_repeat;

    if ($brdcst_del != $refObj->ref['brdcst_del'])
    $result['log'] .= " | BRDCST_DEL = " . $refObj->ref['brdcst_del'] . " -> " . $brdcst_del;

    if ($user_disable != $refObj->ref['user_disable'])
    $result['log'] .= " | USER_DISABLE = " . $refObj->ref['user_disable'] . " -> " . $user_disable;

    if ($user_idle_to != $refObj->ref['user_idle_to'])
    $result['log'] .= " | USER_IDLE_TO = " . $refObj->ref['user_idle_to'] . " -> " . $user_idle_to;

    if ($alm_archv != $refObj->ref['alm_archv'])
    $result['log'] .= " | ALM_ARCHV = " . $refObj->ref['alm_archv'] . " -> " . $alm_archv;

    if ($alm_del != $refObj->ref['alm_del'])
    $result['log'] .= " | ALM_DEL = " . $refObj->ref['alm_del'] . " -> " . $alm_del;

    if ($cfg_archv != $refObj->ref['cfg_archv'])
    $result['log'] .= " | CFG_ARCHV = " . $refObj->ref['cfg_archv'] . " -> " . $cfg_archv;

    if ($cfg_del != $refObj->ref['cfg_del'])
    $result['log'] .= " | CFG_DEL = " . $refObj->ref['cfg_del'] . " -> " . $cfg_del;

    if ($prov_archv != $refObj->ref['prov_archv'])
    $result['log'] .= " | PROV_ARCHV = " . $refObj->ref['prov_archv'] . " -> " . $prov_archv;

    if ($prov_del != $refObj->ref['prov_del'])
    $result['log'] .= " | PROV_DEL = " . $refObj->ref['prov_del'] . " -> " . $prov_del;

    if ($maint_archv != $refObj->ref['maint_archv'])
    $result['log'] .= " | MAINT_ARCHV = " . $refObj->ref['maint_archv'] . " -> " . $maint_archv;

    if ($maint_del != $refObj->ref['maint_del'])
    $result['log'] .= " | MAINT_DEL = " . $refObj->ref['maint_del'] . " -> " . $maint_del;

    if ($auto_ckid != $refObj->ref['auto_ckid'])
    $result['log'] .= " | AUTO_CKID = " . $refObj->ref['auto_ckid'] . " -> " . $auto_ckid;

    if ($auto_ordno != $refObj->ref['auto_ordno'])
    $result['log'] .= " | AUTO_ORDNO = " . $refObj->ref['auto_ordno'] . " -> " . $auto_ordno;

    if ($date_format != $refObj->ref['date_format'])
    $result['log'] .= " | DATE_FORMAT = " . $refObj->ref['date_format'] . " -> " . $date_format;

    if ($mtc_restore != $refObj->ref['mtc_restore'])
    $result['log'] .= " | MTC_RESTORE = " . $refObj->ref['mtc_restore'] . " -> " . $mtc_restore;

    if ($temp_max != $refObj->ref['temp_max'])
    $result['log'] .= " | TEMP_MAX = " . $refObj->ref['temp_max'] . " -> " . $temp_max;

    if ($volt_range != $refObj->ref['volt_range'])
    $result['log'] .= " | VOLT_RANGE = " . $refObj->ref['volt_range'] . " -> " . $volt_range;

    if ($temp_format != $refObj->ref['temp_format'])
    $result['log'] .= " | TEMP_FORMAT = " . $refObj->ref['temp_format'] . " -> " . $temp_format;

    $refObj->updateRefs($pw_expire, $pw_alert, $pw_reuse, $pw_repeat, $brdcst_del, $user_disable, $user_idle_to, $alm_archv, $alm_del, $cfg_archv, $cfg_del, $prov_archv, $prov_del, $maint_archv, $maint_del, $auto_ckid, $auto_ordno, $date_format, $mtc_restore, $temp_max, $volt_range, $temp_format);
    $result['rslt']   = $refObj->rslt;
    $result['reason'] = $refObj->reason;
    $result['rows']   = $refObj->ref;
    return $result;

}

function resetRefs($userObj, $refObj) {
    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    $refObj->resetRefs();
    $result['rslt']   = $refObj->rslt;
    $result['reason'] = $refObj->reason;
    $result['rows']   = $refObj->ref;
    return $result;

}
?>
