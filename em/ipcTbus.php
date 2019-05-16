<?php
/*
* Copy Right @ 2018
* BHD Solutions, LLC.
* Project: CO-IPC
* Filename: ipcTb.php
* Change history: 
* 03-26-2019: created (Thanh)
*/	
	
//Initialize expected inputs

$act = "";
if (isset($_POST['act'])) {
	$act = $_POST['act'];
}

$node = "";
if (isset($_POST['node'])) {
	$node = strtoupper($_POST['node']);
}	

$tb = "";
if (isset($_POST['tb'])) {
	$tb = $_POST['tb'];
}

$fac = "";
if (isset($_POST['fac'])) {
	$fac = $_POST['fac'];
}	

$zport = "";
if (isset($_POST['zport'])) {
	$zport = strtoupper($_POST['zport']);
}

$port = "";
if (isset($_POST['port'])) {
	$port = strtoupper($_POST['port']);
}	

$evtLog = new EVENTLOG($user, "CONFIGURATION", "SETUP TEST BUS", $act, '');
    
//Dispatch to functions
if ($act == "queryTBpath") {
    $result = queryTBpath($fac);
	echo json_encode($result);
	mysqli_close($db);
	return;
}

else {
	$result["rslt"] = 'fail';
	$result["reason"] = "This action is under development!";
	$evtLog->log($result["rslt"], $result["reason"]);
	echo json_encode($result);
	mysqli_close($db);
	return;
}

function queryTBpath($fac) {
    // the fac must exist in DB 
    $facObj = new FAC($fac);
    if ($facObj->rslt == FAIL) {
        $result['rslt'] = "fail";
        $result['reason'] = "FAC: " . $fac . " DOES NOT EXIST";
        return $result;
    }
    
    // the fac must have port mapped
    if ($facObj->port_id == 0) {
        $result['rslt'] = "fail";
        $result['reason'] = "FAC: " . $fac . " IS NOT MAPPED TO A PORT";
        return $result;
    }
    $node = $facObj->portObj->node;
    $tb = $facObj->portObj->ptyp;
    $tbusObj = new TBUS();
    $tbusObj->queryTBpath($node, $tb);

    $result['rslt'] = $tbusObj->rslt;
    $result['reason'] = $tbusObj->reason;
    $result['rows'] = $tbusObj->rows;
    return $result;
}


?>