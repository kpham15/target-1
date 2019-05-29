<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcSearch.php
 * Change history: 
 * 02-11-2019: created (Thanh)
 */

 /* Initialize expected inputs */
    $act = "";
    if (isset($_POST['act'])) {
		$act = $_POST['act'];
	}

	$item = "";
	if (isset($_POST['item'])) {
		$item = $_POST['item'];
	}

	$descr = "";
	if (isset($_POST['descr'])) {
		$descr = $_POST['descr'];
	}

	// $input = "SEARCH: USER=" . $user . ", ACT=" . $act . ", item=" . $item;
	// $evtLog = new EVTLOG($user, "SEARCH", $act, $input);
  
    /*
     * Dispatch to functions
     */

	if ($act == "search") {
		$result = searchItem($item, $userObj);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
	}
	if ($act == "ADD") {
		$result = addSearchItem($userObj,$item,$descr);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
	}
	if ($act == "UPDATE") {
		$result = updateSearchItem($userObj, $item,$descr);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
	}
	if ($act == "DELETE") {
		$result = deleteSearchItem($userObj, $item);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
	}
	
	else {
		$result["rslt"] = FAIL;
		$result["reason"] = "This action is under development!";
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
	}


	/**
     * Functions
     */
	function searchItem($item, $userObj) {
		$searchObj = new SEARCH();
		
		$searchObj->searchItem($item);
	
        $result["rslt"]   = $searchObj->rslt;
        $result["reason"] = $searchObj->reason;
		$result["rows"]   = $searchObj->rows;
		
        return $result;
	}

	function addSearchItem($userObj,$item,$descr) {
		$searchObj = new SEARCH();

		if ($userObj->ugrp != 'ADMIN') {
			$result["rslt"] = 'fail';
			$result["reason"] = 'Permission Denied';
			$result["rows"] = [];
			return $result; 
		}

		$searchObj->addSearchItem($item,$descr);
		$result["rslt"]   = $searchObj->rslt;
        $result["reason"] = $searchObj->reason;
        $result["rows"]   = $searchObj->rows;
        return $result;
	}

	function updateSearchItem($userObj,$item,$descr) {
		$searchObj = new SEARCH();

		if ($userObj->ugrp != 'ADMIN') {
			$result["rslt"] = 'fail';
			$result["reason"] = 'Permission Denied';
			$result["rows"] = [];
			return $result; 
		}

		$searchObj->updateSearchItem($item,$descr);
		$result["rslt"]   = $searchObj->rslt;
        $result["reason"] = $searchObj->reason;
        $result["rows"]   = $searchObj->rows;
        return $result;
	}

	function deleteSearchItem($userObj,$item) {
		$searchObj = new SEARCH();

		if ($userObj->ugrp != 'ADMIN') {
			$result["rslt"] = 'fail';
			$result["reason"] = 'Permission Denied';
			$result["rows"] = [];
			return $result; 
		}

		$searchObj->deleteSearchItem($item);
		$result["rslt"]   = $searchObj->rslt;
        $result["reason"] = $searchObj->reason;
        $result["rows"]   = $searchObj->rows;
        return $result;
	}

?>
