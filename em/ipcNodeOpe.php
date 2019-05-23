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
if (isset($_POST['device'])) {
    $device = $_POST['device'];
}
$device = "ttyUSB0";
        

// dispatch to functions


if ($act == "queryAll") {
	$result = queryAll();	
	echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "DISCOVER") {
    $result = discover($node, $device, $userObj);
    echo json_encode($result);
	mysqli_close($db);
	return;
}


if ($act == "START") {
    $result = start($node, $userObj);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "STOP") {
    $result = stop($node, $userObj);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "DISCOVERED") {
    $result = discovered($node, $hwString);
    echo json_encode($result);
	mysqli_close($db);
	return;
}


// if ($act == "UPDATE RACK") {
//     $result = updateRack($node, $device);
//     echo json_encode($result);
// 	mysqli_close($db);
// 	return;
// }

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

function discover($node, $device, $userObj) {

    // add user permission for ipcAdmin
    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    $cpsObj = new CPS($node);
    $psta = $cpsObj->psta;
    $ssta = $cpsObj->ssta;

    $evt = "DISCV_CPS";
    // test sms
    $smsObj = new SMS($psta, $ssta, $evt);
    if ($smsObj->rslt == FAIL) {
        $result['rslt'] = $smsObj->rslt;
        $result['reason'] = $smsObj->reason;
        return $result;
    }

    // formulate msg #1
    $cmd = "inst=$evt,node=$node,dev=$device,cmd=\$status,source=uuid,device=backplane,ackid=$node-bkpln*";

    // call function to send UDP message
    $cmdObj = new CMD();
    $cmdObj->sendCmd($cmd, $node);
    if ($cmdObj->rslt == FAIL) {
        $result['rslt'] = $cmdObj->rslt;
        $result['reason'] = $cmdObj->reason;
        return $result;
    }

    $result['rslt'] = SUCCESS;
    $result['reason'] = "DISCOVER CPS SUCCESS";
    return $result;
}

function start($node, $userObj) {

    // permissions check here
    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }
    // smsObj check psta/ssta if it is in correct state
    $cpsObj = new CPS($node);
    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }

    $psta = $cpsObj->psta;
    $ssta = $cpsObj->ssta;

    $evt = "START_NODE";

    $smsObj = new SMS($psta, $ssta, $evt);
    if ($smsObj->rslt == FAIL) {
        $result['rslt'] = $smsObj->rslt;
        $result['reason'] = $smsObj->reason;
        return $result;
    }


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

function stop($node, $userObj) {

    // permissions check here
    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

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

function discovered($node, $hwString) {

    // parse hwString
    
    // $ackid=1-bkpln,status,device=miox(0),uuid=IAMAMIOXUUIDTHATYOUCANTDECODE*
    // UUID is serial number for now
    $newHwString = substr($hwString, 1, -1);
    $newHwStringArray = explode(",", $newHwString);
    // ["ackid=1-bkpln","status","device=miox(0)","uuid=IAMAMIOXUUIDTHATYOUCANTDECODE"];
    $serialNumArray = explode("=", $newHwStringArray[3]);
    // ["uuid","IAMAMIOXUUIDTHATYOUCANTDECODE"]
    $serialNum = $serialNumArray[1];
     
    // construct to see if serial number already exists in DB
    $cpssObj = new CPSS();
    if ($cpssObj->rslt == FAIL) {
        $result['rslt'] = $cpssObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }

    if (in_array($serialNum, $cpssObj->serial_no)) {
        // b) if already exists then send UDP->msg($node,$device,STOP)
        $cpsObj = new CPS($node);
        // send message 3 to udp
        $cmd = "inst=STOP_CPS,node=$node,dev=$cpsObj->device";
        $cmdObj = new CMD();
        $cmdObj->sendCmd($cmd, $node);
        if ($cmdObj->rslt == FAIL)         {
            $result['rslt'] = $cmdObj->rslt;
            $result['reason'] = $cmdObj->reason;
            return $result;
        }

        $result['rslt'] = FAIL;
        $result['reason'] = "SERIAL NUMBER ALREADY EXISTS IN SYSTEM";
        return $result;
    }
    else {
        // a) if $serial_no not exist in t_cps then update CPS->psta/ssta with npsta/nssta obtained from SMS

        // @TODO AM I USING THE CORRECT EVT HERE FOR SMS??
        $evt = "DISCOVER_NODE";
        // gets psta and ssta to create smsObj
        $cpsObj = new CPS($node);
        
        // get nspta and nssta from sms obj
        $smsObj = new SMS($cpsObj->psta, $cpsObj->ssta, $evt);
        if ($smsObj->rslt == FAIL) {
            $result['rslt'] = $smsObj->rslt;
            $result['reason'] = $smsObj->reason;
            return $result;
        }

        $newPsta = $smsObj->npsta;
        $newSsta = $smsObj->nssta;

        // update psta and ssta, write serial number to t_cps, start the cps
        $cpsObj->setCpsStatus($newPsta, $newSsta);
        // if success
        $cpsObj->setSerialNo($serial_no);
        // call message 2

        $cmd = "inst=START_CPS,node=$node,dev=$cpsObj->device,cmd=\$status,source=all,ackid=$node-CPS*\$status,source=devices,ackid=$node-dev*";
        $cmdObj = new CMD();
        $cmdObj->sendCmd($cmd, $node);
        if ($cmdObj->rslt == FAIL) {
            $result['rslt'] = $cmdObj->rslt;
            $result['reason'] = $cmdObj->reason;
            return $result;
        }

        
        return $result;

    }
    
}

?>