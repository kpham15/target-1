<?php

// Initialize Expected Inputs

$ordno = "";
if (isset($_POST['ordno'])) {
    $ordno = $_POST['ordno'];
}

$ot = "";
if (isset($_POST['ot'])) {
    $ot = $_POST['ot'];
}

$wc = "";
if (isset($_POST['wc'])) {
    $wc = $_POST['wc'];
}

$pri = "";
if (isset($_POST['pri'])) {
    $pri = $_POST['pri'];
}

$stat = "";
if (isset($_POST['stat'])) {
    $stat = $_POST['stat'];
}

$cdd = "";
if (isset($_POST['cdd'])) {
    $cdd = $_POST['cdd'];
}

$dd = "";
if (isset($_POST['dd'])) {
    $dd = $_POST['dd'];
}

$fdd = "";
if (isset($_POST['fdd'])) {
    $fdd = $_POST['fdd'];
}

$fdt = "";
if (isset($_POST['fdt'])) {
    $fdt = $_POST['fdt'];
}

$act = "";
if (isset($_POST['action'])) {
    $act = $_POST['action'];
}

// Check what type of action is being performed

if ($act == "findOrder") {
    $result = findOrder($ordno, $ot, $wc, $pri, $stat);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "findOrderByDD") {
    $result = findOrderByDD($cdd, $dd, $fdd, $fdt);
    echo json_encode($result);
    mysqli_close($db);
    return;
}


// FUNCTIONS FOR ACTION

function findOrder($ordno, $ot, $wc, $pri, $stat) {

    $ordObj = new ORD();

    $ordObj->findOrder($ordno, $ot, $wc, $pri, $stat);
    if ($ordObj->rslt == FAIL) {
        $result["rslt"] = FAIL;
        $result["reason"] = $ordObj->reason;
        return $result;
    }
    $result["rslt"]   = SUCCESS;
    $result["reason"] = "queryOrd_success";
    $result['rows']   = $ordObj->rows;
    return $result;
}

function findOrderByDD($cdd, $dd, $fdd, $fdt) {

    $ordObj = new ORD();

    $ordObj->findOrderByDD($cdd, $dd, $fdd, $fdt);
    if ($ordObj->rslt == FAIL) {
        $result["rslt"] = FAIL;
        $result["reason"] = $ordObj->reason;
        return $result;
    }
    $result["rslt"]   = SUCCESS;
    $result["reason"] = "queryOrd_success";
    $result['rows']   = $ordObj->rows;
    return $result;
}

?>