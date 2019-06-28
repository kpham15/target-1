<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcProvReport.php
 * Change history: 
 * 02-06-2019: created (Thanh)
 */

    //Initialize expected inputs
    $act = "";
    if (isset($_POST['act'])) {
		$act = $_POST['act'];
	}

	$uname = "";
	if (isset($_POST['uname'])) {
		$uname = strtoupper($_POST['uname']);
	}

    $action = "";
    if (isset($_POST['action'])) {
		$action = $_POST['action'];
	}
	
    $ordno = "";
    if (isset($_POST['ordno'])) {
		$ordno = $_POST['ordno'];
	}
    
	$ckid = "";
    if (isset($_POST['ckid'])) {
		$ckid = $_POST['ckid'];
	}
    
    $fromDate = "";
    if (isset($_POST['fromDate'])) {
		$fromDate = $_POST['fromDate'];
	}
    
	$toDate = "";
	if (isset($_POST['toDate'])) {
		$toDate = $_POST['toDate'];
	}

	// Dispatch to Functions
	
	if ($act == "VIEW REPORT") {
		deleteExpiredProvLog();
		$result = queryProvlog($uname, $action, $ordno, $ckid, $fromDate, $toDate, $userObj);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	if ($act == "queryOrd") {
		$result = queryOrd($ordno);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
    else {
        $result["rslt"] = "fail";
		$result["reason"] = "Invalid action!";
        echo json_encode($result);
        mysqli_close($db);
        return; 
    }

	//GETS SETTINGS FROM ipcRef & DELETES logs out of time range
	function deleteExpiredProvLog() {

		$refObj = new REF();

		//get prov_del from refObj
		$prov_del = $refObj->ref['prov_del'];
		if($prov_del == 0){
			$prov_del = $refObj->default['prov_del'];
			if($prov_del == 0)
				$prov_del = 180;
		}
		//convert value into seconds
		$prov_del_in_sec = $prov_del * 86400;
		
		$current_timestamp = time();
		$expired_timestamp = $current_timestamp - $prov_del_in_sec;
		$expired_date = date('Y-m-d', $expired_timestamp);

		$provlogObj= new PROVLOG();
		$provlogObj->deleteExpiredLog($expired_date);
		return;
	}
	

	function queryProvlog($uname, $action, $ordno, $ckid, $fromDate, $toDate, $userObj) {
		
		// 1) Check User Priviledges
		if ($userObj->grpObj->report == 'N') {
			$result['rslt'] = 'fail';
			$result['reason'] = 'PERMISSION DENIED';
			$result['rows'] = [];
			return $result;
		}

		// 2) Default from/to date to current time if not specificed

        if(($fromDate == "") || ($toDate == "")) {
			$fromDate = date("Y-m-d",time());
			$toDate = date("Y-m-d",time());
		}

		// 3) Default input to % if left blank
		if ($uname == "") {
			$uname = '%';
		}
		if ($action == "") {
			$action = '%';
		}
		if ($ordno == "") {
			$ordno = '%';
		}
		if ($ckid == "") {
			$ckid = '%';
		}

		// 4) Query ProvLog
        $provlogObj= new PROVLOG();
		$provlogObj->query($uname, $action, $ordno, $ckid, $fromDate, $toDate);
        $result["rslt"]   = $provlogObj->rslt;
        $result["reason"] = $provlogObj->reason;
        $result["rows"]   = $provlogObj->rows;
        return $result;
	}

	function queryOrd($ordno) {
		
		// $cktObj = new CKT();
		// $cktObj->queryOrd($ordno, $mlo);
		if($ordno === "") {
			$ordno = '%';
		}
		$provLogObj = new PROVLOG();
		$provLogObj->queryOrd($ordno);
		$result['rslt'] = $provLogObj->rslt;
		$result['reason'] = $provLogObj->reason;
		$result['rows'] = $provLogObj->rows;
		return $result;
	}


?>
