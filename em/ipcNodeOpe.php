<?php

// initialize expected inputs from front end
$act = "";
if (isset($_POST['act']))
    $act = $_POST['act'];
    
$node = "";
if (isset($_POST['node']))
    $node = $_POST['node'];
    
$rack = "";
if (isset($_POST['rack']))
    $rack = $_POST['rack'];
    
$serial_no = "";
if (isset($_POST['serial_no']))
    $serial_no = $_POST['serial_no'];

$device = "";
if (isset($_POST['device']))
    $device = $_POST['device'];
        

// dispatch to functions


if ($act == "queryAll") {
	$result = queryAll();	
	echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "DISCOVER") {
    $result = discover($node, $device);
    echo json_encode($result);
	mysqli_close($db);
	return;
}


if ($act == "START") {
    $result = start($node);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "STOP") {
    $result = stop($node);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "DISCOVERED") {
    $result = discovered($node, $serial_no, $device);
    echo json_encode($result);
	mysqli_close($db);
	return;
}


if ($act == "UPDATE RACK") {
    $result = updateRack($node, $device);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

// functions area

function queryAll() {
    $cpssObj = new CPSS();
    if ($cpssObj->rslt == FAIL) {
        $result['rslt'] = $cpssObj->rslt;
        $result['reason'] = $cpssObj->reason;
        return $result;
    }

    $result['rslt'] = SUCCESS;
    $result['reason'] = "QUERY SUCCESS";
    $result['rows'] = $cpssObj->rows;
    return $result;
}

function discover($node, $device) {

    // add user permission for ipcAdmin


    $cmdObj = new CMD();

    $cmdObj->sendDiscoverCmd($node, $device);
    if ($cmdObj->rslt == "fail") {
        $result['rslt'] = $cmdObj->rslt;
        $result['reason'] = $cmdObj->reason;
        return;
    }
    // $cmdObj->sendQueryBackplaneId($node);
    $result['rslt'] = $cmdObj->rslt;
    $result['reason'] = $cmdObj->reason;
    return $result;
}

function start($node) {

    // permissions check here
    // smsObj check psta/ssta if it is in correct state

    $cmdObj = new CMD();
    $cmdObj->sendStartCmd($node);
    if ($cmdObj->rslt == "fail") {
        $result['rslt'] = $cmdObj->rslt;
        $result['reason'] = $cmdObj->reason;
        return;
    }
    $result['rslt'] = $cmdObj->rslt;
    $result['reason'] = $cmdObj->reason;
    return $result;
}

function stop($node) {
    $cmdObj = new CMD();
    $cmdObj->sendStopCmd($node);
    if ($cmdObj->rslt == "fail") {
        $result['rslt'] = $cmdObj->rslt;
        $result['reason'] = $cmdObj->reason;
        return;
    }
    $result['rslt'] = $cmdObj->rslt;
    $result['reason'] = $cmdObj->reason;
    return $result;
}

function discovered($node, $serial_no, $device) {
    // serial number must not exist in system
    $cpssObj = new CPSS();
    if (in_array($serial_no, $cpssObj->serial_no)) {
        // b) if already exists then send UDP->msg($node,$device,STOP)
        $result = stop($node);
        return $result;
    }
    else {
        // a) if $serial_no not exist in t_cps then update CPS->psta/ssta with npsta/nssta obtained from SMS
        
    }
    
}

?>