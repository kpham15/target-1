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
    

// dispatch to functions


if ($act == "queryAll") {
	
	
	echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "DISCOVER") {

    echo json_encode($result);
	mysqli_close($db);
	return;
}


if ($act == "START") {

    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "STOP") {

    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "UPDATE RACK") {

    echo json_encode($result);
	mysqli_close($db);
	return;
}

// functions area


?>