<?php

    /* Initialize expected inputs */

    $act = "";
    if (isset($_POST['act'])) {
        $act = $_POST['act'];
    }


    $sot = "";
    if (isset($_POST['sot'])) {
        $sot = $_POST['sot'];
    }

    $ot = "";
    if (isset($_POST['ot'])) {
        $ot = $_POST['ot'];
    }

    $rot = "";
    if (isset($_POST['rot'])) {
        $rot = $_POST['rot'];
    }

    $cls = "";
    if (isset($_POST['cls'])) {
        $cls = $_POST['cls'];
    }

    $oc = "";
    if (isset($_POST['oc'])) {
        $oc = $_POST['oc'];
    }

    $adsr = "";
    if (isset($_POST['adsr'])) {
        $adsr = $_POST['adsr'];
    }

    $rtAct = "";
    if (isset($_POST['rtAct'])) {
        $rtAct = $_POST['rtAct'];
    }

    $facType = "";
    if (isset($_POST['facType'])) {
        $facType = $_POST['facType'];
    }

    $facId = "";
    if (isset($_POST['facId'])) {
        $facId = $_POST['facId'];
    }

    $fddInt = "";
    if (isset($_POST['fddInt'])) {
        $fddInt = $_POST['fddInt'];
    }


    $ddInt = "";
    if (isset($_POST['ddInt'])) {
        $ddInt = $_POST['ddInt'];
    }


    $rtyp = "";
    if (isset($_POST['rtyp'])) {
        $rtyp = $_POST['rtyp'];
    }

    $rt = "";
    if (isset($_POST['rt'])) {
        $rt = $_POST['rt'];
    }

    $jCond = "";
    if (isset($_POST['jCond'])) {
        $jCond = $_POST['jCond'];
    }

    $jeop = "";
    if (isset($_POST['jeop'])) {
        $jeop = $_POST['jeop'];
    }

    $id = "";
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }


    $evtLog = new EVENTLOG($user, "PROVISIONING", "FLOW-THROUGH RELEASE TABLE", $act, $_POST);

	// Dispatch to Functions

    $ftreleaseObj = new FTRELEASE();
	if ($ftreleaseObj->rslt == "fail") {
		$result["rslt"] = "fail";
		$result["reason"] = $ftreleaseObj->reason;
		$evtLog->log($result["rslt"], $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}

    if ($act == "query") {
        $result = queryFtRelease($sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop, $ftreleaseObj);
        mysqli_close($db);
        echo json_encode($result);
        return;
    }
    else if ($act == "ADD") {
        $result = addFtRelease($sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop, $ftreleaseObj);
        $evtLog->log($result["rslt"], $result['reason']);
		mysqli_close($db);
        echo json_encode($result);
        return;
    }
    else if ($act == "DELETE") {
        $result = deleteFtRelease($id, $sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop, $ftreleaseObj);
        $evtLog->log($result["rslt"], $result['reason']);
		mysqli_close($db);
        echo json_encode($result);
        return;
    }
    else if ($act == "UPDATE") {
        $result = updateFtRelease($id, $sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop, $ftreleaseObj);
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
   

    function queryFtRelease ($sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop, $ftreleaseObj) {

        $ftreleaseObj->queryFtRelease($sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop);
        $result['rslt']     = $ftreleaseObj->rslt;
        $result['reason']   = $ftreleaseObj->reason;
		$result['rows']     = $ftreleaseObj->rows;

		return $result;

    }

    function addFtRelease($sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop, $ftreleaseObj) {

		$ftreleaseObj->add($sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop);
		$result['rslt']     = $ftreleaseObj->rslt;
        $result['reason']   = $ftreleaseObj->reason;
        
        $ftreleaseObj->queryFtRelease($sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop);
		$result['rows']     = $ftreleaseObj->rows;

		return $result;

    }

    function deleteFtRelease($id, $sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop, $ftreleaseObj) {

		$ftreleaseObj->delete($id);
		$result['rslt']     = $ftreleaseObj->rslt;
        $result['reason']   = $ftreleaseObj->reason;
        
        $ftreleaseObj->queryFtRelease($sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop);
		$result['rows']     = $ftreleaseObj->rows;

		return $result;
    }

    function updateFtRelease($id, $sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop, $ftreleaseObj) {
        
        $ftreleaseObj->update($id, $sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop);
		$result['rslt']     = $ftreleaseObj->rslt;
        $result['reason']   = $ftreleaseObj->reason;
        
        $ftreleaseObj->queryFtRelease($sot, $ot, $rot, $cls, $oc, $adsr, $rtAct, $facType, $facId, $fddInt, $ddInt, $rtyp, $rt, $jCond, $jeop);
		$result['rows']     = $ftreleaseObj->rows;

		return $result;

    }

?>