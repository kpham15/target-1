<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcProvReprot.php
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
		deleteExpiredCfgLog();
		$result = queryCfglog($action, $uname, $fromDate, $toDate, $userObj);
		echo json_encode($result);
		mysqli_close($db);
		return;
    }
    else {
        $result["rslt"]   = "fail";
		$result["reason"] = "Invalid action!";
        echo json_encode($result);
        mysqli_close($db);
        return; 
    }

		//GETS SETTINGS FROM ipcRef & DELETES logs out of time range
		function deleteExpiredCfgLog() {

			$refObj = new REF();
	
			//get cfg_del from refObj
			$cfg_del = $refObj->ref['cfg_del'];
			if($cfg_del == 0){
				$cfg_del = $refObj->default['cfg_del'];
				if($cfg_del == 0)
					$cfg_del = 180;
			}

			//convert value into seconds
			$cfg_del_in_sec = $cfg_del * 86400;
			
			$current_timestamp = time();
			
			$expired_timestamp = $current_timestamp - $cfg_del_in_sec;
			
			$expired_date = date('Y-m-d', $expired_timestamp);
	
			// print_r("CFG DEL = $cfg_del");
			// print_r("CFG DEL IN SEC = $cfg_del_in_sec");
			// print_r("CURRENT TIME STAMP = $current_timestamp");
			// print_r("EXPIRED TIMESTAMP = $expired_timestamp");
			// print_r("EXPIRED DATE = $expired_date");
	
			$cfglogObj = new CFGLOG();
			$cfglogObj->deleteExpiredLog($expired_date);
			return;
		}
	
	
	
	function queryCfglog($action, $uname, $fromDate, $toDate, $userObj) {

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
		if ($action == "") {
			$action = "%";
		}
		if ($uname == "") {
			$uname = "%";
		}

		// 4) query config log
        $cfglogObj = new CFGLOG();
		$cfglogObj->query($action, $uname, $fromDate, $toDate);
        $result["rslt"]   = $cfglogObj->rslt;
        $result["reason"] = $cfglogObj->reason;
        $result["rows"]   = $cfglogObj->rows;
        return $result;
	}

?>
