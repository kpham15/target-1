<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcEventlog.php
 * Change history: 
 * 2019-03-14: created (Thanh)
 */
	
    // Initialize Expected inputs
    $act = "";
	if (isset($_POST['act']))
		$act = $_POST['act'];
		
	$uname = "";
	if (isset($_POST['uname']))
		$uname = $_POST['uname'];

	$fnc = "";
	if (isset($_POST['fnc']))
		$fnc = $_POST['fnc'];

	$evt = "";
	if (isset($_POST['evt']))
        $evt = $_POST['evt'];
        
    $task = "";
	if (isset($_POST['task']))
		$evt = $_POST['task'];

	$fromDate = "";
	if (isset($_POST['fromDate']))
        $fromDate = $_POST['fromDate'];
    
    $toDate = "";
	if (isset($_POST['toDate']))
		$toDate = $_POST['toDate'];

	// Dispatch to Functions
	
	if ($act == "VIEW REPORT") {
		deleteExpiredLog();
		$result = queryEventlog($uname, $evt, $fnc, $task, $fromDate, $toDate);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
	}

	else {
 		$result["rslt"] = "fail";
		$result["reason"] = "Invalid ACTION";
		$evtLog->log($result["rslt"], $result["reason"]);
		echo json_encode($result);
        mysqli_close($db);
        $debugObj->close();
		return;
    }
    
	//GETS SETTINGS FROM ipcRef & DELETES logs out of time range
	function deleteExpiredLog() {

		$refObj = new REF();
		//get evt_del from refObj
		$event_del = $refObj->ref[0]['evt_del'];

		//convert value into seconds
		$event_del_in_sec = $event_del * 86400;
		
		$current_timestamp = time();
		
		$expired_timestamp = $current_timestamp - $event_del_in_sec;
		
		$expired_date = date('Y-m-d', $expired_timestamp);

		$eventObj = new EVENTLOG("","","","");
		$eventObj->deleteExpiredLog($expired_date);

		return;
	}


    function queryEventlog($uname, $evt, $fnc, $task, $fromDate, $toDate) {
        if($uname === "") {
            $uname = "%";
        }
        if($evt === "") {
            $evt = "%";
        }
        if($fnc === "") {
            $fnc = "%";
        }
        if($task === "") {
            $task = "%";
        }
        $eventObj = new EVENTLOG("","","","");
		$eventObj->query($uname, $evt, $fnc, $task, $fromDate, $toDate);
		$result["rslt"] = $eventObj->rslt;
		$result["reason"] = $eventObj->reason;
        $result["rows"] = $eventObj->rows;
        return $result;
    }
	
	
?>