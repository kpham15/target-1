<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcMaintReprot.php
 * Change history: 
 * 02-06-2019: created (Thanh)
 */

    // Initialize expected inputs
    $act = "";
    if (isset($_POST['act'])) {
		$act = $_POST['act'];
	}

	$uname = "";
	if (isset($_POST['uname'])) {
		$uname = $_POST['uname'];
	}

    $tktno = "";
    if (isset($_POST['tktno'])) {
		$tktno = strtoupper($_POST['tktno']);
	}

	$action = "";
    if (isset($_POST['action'])) {
		$action = strtoupper($_POST['action']);
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
		deleteExpiredMaintLog();
		$result = queryMaintlog($uname, $tktno, $action, $fromDate, $toDate, $userObj);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	if ($act == "queryTkt") {
		$result = queryTkt($tktno);
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
	function deleteExpiredMaintLog() {

		$refObj = new REF();

		//get prov_del from refObj
		$maint_del = $refObj->ref['maint_del'];
		if($maint_del == 0){
			$maint_del = $refObj->default['maint_del'];
			if($maint_del == 0)
				$maint_del = 180;
		}

		//convert value into seconds
		$maint_del_in_sec = $maint_del * 86400;
		
		$current_timestamp = time();
		$expired_timestamp = $current_timestamp - $maint_del_in_sec;
		$expired_date = date('Y-m-d', $expired_timestamp);

		$maintLogObj = new MAINTLOG();
		$maintLogObj->deleteExpiredLog($expired_date);
		return;
	}


	
	function queryMaintlog($uname, $tktno, $action, $fromDate, $toDate, $userObj) {
		
		// 1) check user priviledges
		if ($userObj->grpObj->report == 'N') {
			$result['rslt']   = 'fail';
			$result['reason'] = 'PERMISSION DENIED';
			$result['rows']   = [];
			return $result;
		}

		// 2) default from/to date to current time if not specified
		if ($fromDate == "" || $toDate == "") {
			$fromDate	= date("Y-m-d",time());
			$toDate		= date("Y-m-d",time());
		}

		// 3) If user input is empty, replace empty string with %
		if ($uname == "") {
			$uname = "%";
		}
		if ($tktno == "") {
			$tktno = "%";
		}
		if ($action == "") {
			$action = "%";
		}
		
		// 4) query maint log
        $maintlogObj= new MAINTLOG();
		$maintlogObj->query($uname, $tktno, $action, $fromDate, $toDate);
        $result["rslt"]   = $maintlogObj->rslt;
        $result["reason"] = $maintlogObj->reason;
        $result["rows"]   = $maintlogObj->rows;
        return $result;
	}

	function queryTkt($tktno) {

		if($tktno === "") {
			$tktno = '%';
		}
		$maintLogObj = new MAINTLOG();
		$maintLogObj->queryTkt($tktno);
		$result['rslt'] = $maintLogObj->rslt;
		$result['reason'] = $maintLogObj->reason;
		$result['rows'] = $maintLogObj->rows;
		return $result;
	}


?>
