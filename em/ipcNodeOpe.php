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
    $result = start($node, $device);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "STOP") {
    $result = stop($node, $device);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "UPDATE RACK") {
    $result = updateRack($node, $device)
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


?>