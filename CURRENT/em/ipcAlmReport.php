<?php
/*
* Copy Right @ 2019
* BHD Solutions, LLC.
* Project: CO-IPC
* Filename: ipcAlmLog.php
* Change history: 
* 2019-01-04: created (Kris)
*/

// INITIALIZE

$uname = "";
if (isset($_POST['uname'])) {
    $uname = strtoupper($_POST['uname']);
}

$act = "";
if (isset($_POST['act'])) {
    $act = $_POST['act'];
}

$action = "";
if (isset($_POST['action'])) {
    $action = $_POST['action'];
}

$sev = "";
if (isset($_POST['sev'])) {
    $sev = $_POST['sev'];
}

$toDate = "";
if (isset($_POST['toDate'])) {
    $toDate = $_POST['toDate'];
}

$fromDate = "";
if (isset($_POST['fromDate'])) {
    $fromDate = $_POST['fromDate'];
}

// DISPATCH TO FUNCTIONS

if ($act == "query" || $act == "VIEW REPORT") {
    deleteExpiredAlmLog();
    $result = queryAlmlog($uname, $action, $sev, $source, $fromDate, $toDate, $userObj);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

else {
    $result["rslt"] = "fail";
    $result["reason"] = "Invalid ACTION";
    echo json_encode($result);
    mysqli_close($db);
    return;
}

// API FUNCTIONS
//GETS SETTINGS FROM ipcRef & DELETES logs out of time range
function deleteExpiredAlmLog() {

    $refObj = new REF();

    //get prov_del from refObj
    $alm_del = $refObj->ref[0]['alm_del'];
    
    //convert value into seconds
    $alm_del_in_sec = $alm_del * 86400;
    
    $current_timestamp = time();
    $expired_timestamp = $current_timestamp - $alm_del_in_sec;
    $expired_date = date('Y-m-d', $expired_timestamp);

    $almlogObj= new ALMLOG();
    $almlogObj->deleteExpiredLog($expired_date);
    return;
}


function queryAlmlog($uname, $action, $sev, $source, $fromDate, $toDate, $userObj) {

    // 1) check user priviledges
    if ($userObj->grpObj->report == 'N') {
        $result['rslt'] = 'fail';
        $result['reason'] = 'PERMISSION DENIED';
        $result['rows'] = [];
        return $result;
    }

	// 2) default from/to date to current time if not specified
    if($fromDate =="" || $toDate =="") {
        $fromDate = date("Y-m-d",time());
        $toDate = date("Y-m-d",time());
    }
    if($uname == "") {
        $uname = "%";
    }
    if($action == "") {
        $action = "%";
    }
    if($sev == "") {
        $sev = "%";
    }
    if($source == "") {
        $source = "%";
    }

    // 3) query alm log
    $almlogObj= new ALMLOG();
    $almlogObj->query($uname, $action, $sev, $source, $fromDate, $toDate);
    $result["rslt"]   = $almlogObj->rslt;
    $result["reason"] = $almlogObj->reason;
    $result["rows"]   = $almlogObj->rows;
    return $result;
}

?>