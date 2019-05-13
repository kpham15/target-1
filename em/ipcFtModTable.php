<?php
    
    /* Initialize expected inputs */

    $act = "";
    if (isset($_POST['act'])) {
        $act = $_POST['act'];
    }

    $ot = "";
    if (isset($_POST['ot'])) {
        $ot = $_POST['ot'];
    }

    $pri = "";
    if (isset($_POST['pri'])) {
        $pri = $_POST['pri'];
    }

    $cdd = "";
    if (isset($_POST['cdd'])) {
        $cdd = $_POST['cdd'];
    }

    $noscm = "";
    if (isset($_POST['noscm'])) {
        $noscm = $_POST['noscm'];
    }

    $rtype = "";
    if (isset($_POST['rtype'])) {
        $rtype = $_POST['rtype'];
    }

    $processingfile = "";
    if (isset($_POST['processingfile'])) {
        $processingfile = $_POST['processingfile'];
    }

    $id = "";
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }

    // $evtLog = new EVTLOG($user, "FTMODTABLE", $act, "");
    $evtLog = new EVENTLOG($user, "PROVISIONING", "FLOW THROUGH", "", $_POST);

	// Dispatch to Functions

    $ftmodtableObj = new FTMODTABLE();
	if ($ftmodtableObj->rslt == "fail") {
		$result["rslt"] = "fail";
		$result["reason"] = $ftmodtableObj->reason;
		$evtLog->log($result["rslt"], $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}

    if ($act == "query") {
        $result = queryFtModTable($ot, $pri, $cdd, $noscm, $rtype, $processingfile, $ftmodtableObj);
        mysqli_close($db);
        echo json_encode($result);
        return;
    }
    else if ($act == "ADD") {
        $result = addFtModTable($ot, $pri, $cdd, $noscm, $rtype, $processingfile, $ftmodtableObj);
        $evtLog->log($result["rslt"], $result['reason']);
		mysqli_close($db);
        echo json_encode($result);
        return;
    }
    else if ($act == "DELETE") {
        $result = deleteFtModTable($id, $ot, $pri, $cdd, $noscm, $rtype, $processingfile, $ftmodtableObj);
        $evtLog->log($result["rslt"], $result['reason']);
		mysqli_close($db);
        echo json_encode($result);
        return;
    }
    else if ($act == "UPDATE") {
        $result = updateFtModTable($id, $ot, $pri, $cdd, $noscm, $rtype, $processingfile, $ftmodtableObj);
        $evtLog->log($result["rslt"], $result['reason']);
		mysqli_close($db);
        echo json_encode($result);
        return;
    }
    else {
        $result["rslt"] = "fail";
        $result["reason"] = "ACTION " . $act . " is under development or not supported";
        echo json_encode($result);
        mysqli_close($db);
        return;
    }

    function queryFtModTable ($ot, $pri, $cdd, $noscm, $rtype, $processingfile, $ftmodtableObj) {
        
        $ftmodtableObj->queryFtModTable($ot, $pri, $cdd, $noscm, $rtype, $processingfile);
        $result['rslt']     = $ftmodtableObj->rslt;
        $result['reason']   = $ftmodtableObj->reason;
		$result['rows']     = $ftmodtableObj->rows;

		return $result;

    }

    function addFtModTable($ot, $pri, $cdd, $noscm, $rtype, $processingfile, $ftmodtableObj) {

		$ftmodtableObj->add($ot, $pri, $cdd, $noscm, $rtype, $processingfile);
		$result['rslt']     = $ftmodtableObj->rslt;
        $result['reason']   = $ftmodtableObj->reason;
        
        $ftmodtableObj->queryFtModTable($ot, $pri, $cdd, $noscm, $rtype, $processingfile);
		$result['rows']     = $ftmodtableObj->rows;

		return $result;

    }

    function deleteFtModTable($id, $ot, $pri, $cdd, $noscm, $rtype, $processingfile, $ftmodtableObj) {

		$ftmodtableObj->delete($id);
		$result['rslt']     = $ftmodtableObj->rslt;
        $result['reason']   = $ftmodtableObj->reason;
        
        $ftmodtableObj->queryFtModTable($ot, $pri, $cdd, $noscm, $rtype, $processingfile);
		$result['rows']     = $ftmodtableObj->rows;

		return $result;
    }

    function updateFtModTable($id, $ot, $pri, $cdd, $noscm, $rtype, $processingfile, $ftmodtableObj) {

		$ftmodtableObj->update($id, $ot, $pri, $cdd, $noscm, $rtype, $processingfile);
		$result['rslt']     = $ftmodtableObj->rslt;
        $result['reason']   = $ftmodtableObj->reason;
        
        $ftmodtableObj->queryFtModTable($ot, $pri, $cdd, $noscm, $rtype, $processingfile);
		$result['rows']     = $ftmodtableObj->rows;

		return $result;

    }





?>