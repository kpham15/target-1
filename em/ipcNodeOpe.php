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
$hwRsp = "";
if (isset($_POST['hwRsp'])) {
    $hwRsp = $_POST['hwRsp'];
}
        

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

// not being used on the front end
if ($act == "START") {
    $result = start($node, $userObj);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "STOP") {
    $result = stop($node, $serial_no, $userObj);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "DISCOVERED") {
    $result = discovered($node, $hwRsp);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "CPS_STATUS") {
    $result = updateCpsStatus($hwRsp);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "CPS_ON") {
    $result = cps_on($node);
    echo json_encode($result);
	mysqli_close($db);
	return;
}
if ($act == "CPS_OFF") {
    $result = cps_off($node);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "EXEC_RESP") {
    $result = exec_resp($node, $hwRsp, $userObj);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "CONNECT_TBX_TAP1") {
    // $result = processHwResp($node, $hwRsp);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "EXEC_CMD") {
    $result = exec_cmd($node, $cmd, $userObj);
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

function cps_on($node) {
    // create cps to get data for psta/ssta
    $cpsObj = new CPS($node);
    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }
    
    // check if psta/ssta is in right status
    $smsObj = new SMS($cpsObj->psta, $cpsObj->ssta, 'CPS_ON');
    if ($smsObj->rslt == FAIL) {
        $result['rslt'] = $smsObj->rslt;
        $result['reason'] = $smsObj->reason;
        return $result;
    }
    
    // if correct stat
    // update sms psta/ssta
    $cpsObj->setPsta($smsObj->npsta, $smsObj->nssta);
    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }
    // post to nodeapi to update node cps stats
    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeAdmin",'act'=>'updateCpsCom',"node"=>$node, "cmd"=>"$node-ONLINE"];
    $postReqObj->syncPostRequest($url, $params);
    return json_decode($postReqObj->reply);

}

function cps_off($node) {
    // create cps to get data for psta/ssta
    $cpsObj = new CPS($node);
    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }
    
    // check if psta/ssta is in right status
    $smsObj = new SMS($cpsObj->psta, $cpsObj->ssta, 'CPS_OFF');
    if ($smsObj->rslt == FAIL) {
        $result['rslt'] = $smsObj->rslt;
        $result['reason'] = $smsObj->reason;
        return $result;
    }
    
    // if correct stat
    // update sms psta/ssta
    $cpsObj->setPsta($smsObj->npsta, $smsObj->nssta);
    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }

    // post to nodeapi to update node cps stats
    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeAdmin",'act'=>'updateCpsCom', "node"=>$node, "cmd"=>"$node-OFFLINE"];
    $postReqObj->syncPostRequest($url, $params);
    return json_decode($postReqObj->reply);

}

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

    $evt = "CPS_ON";
    // test sms
    $smsObj = new SMS($psta, $ssta, $evt);
    if ($smsObj->rslt == FAIL) {
        $result['rslt'] = $smsObj->rslt;
        $result['reason'] = $smsObj->reason;
        return $result;
    }

    // formulate msg #1
    // this cmd will be sent back to be parsed. the ackid must be the NEXT ACT and API
    $cmd = "inst=DISCV_CPS,node=$node,dev=$cpsObj->dev,sn=,cmd=\$status,source=uuid,device=backplane,ackid=$node-cps-dcvd*";

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
    $result['rows'] = [];
    return $result;
}

// not being used on the front end
function start($node, $userObj) {

    // permissions check here
    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }
    
    // construct cpsObj to get current psta/ssta of CPS
    $cpsObj = new CPS($node);
    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }

    $psta = $cpsObj->psta;
    $ssta = $cpsObj->ssta;

    $evt = "CPS_ON";

    // sms check psta/ssta if it is in correct state
    $smsObj = new SMS($psta, $ssta, $evt);
    if ($smsObj->rslt == FAIL) {
        $result['rslt'] = $smsObj->rslt;
        $result['reason'] = $smsObj->reason;
        return $result;
    }

    // update t_cps psta/ssta with npsta/nssta???

    $cmd = "inst=START_CPS,node=$node,dev=$cpsObj->dev,cmd=\$status,source=all,ackid=$node-CPS*\$status,source=devices,ackid=$node-dev*";

    $cmdObj = new CMD();
    $cmdObj->sendCmd($cmd, $node);    
    if ($cmdObj->rslt == "fail") {
        $result['rslt'] = $cmdObj->rslt;
        $result['reason'] = $cmdObj->reason;
        return;
    }
    $result['rslt'] = SUCCESS;
    $result['reason'] = "START COMMAND SENT SUCCESSFULLY";
    $result['rows'] = [];
    return $result;
}


function stop($node, $serial_no, $userObj) {

    // permissions check here
    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    $cpsObj = new CPS($node);

    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }

    $smsObj = new SMS($cpsObj->psta, $cpsObj->ssta, "CPS_STOP");
    if($smsObj->rslt == FAIL) {
        $result['rslt'] = $smsObj->rslt;
        $result['reason'] = $smsObj->reason;
        return $result;
    }
    
    $cpsObj->setPsta($smsObj->npsta, $smsObj->nssta);
    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }
    
    $cmd = "inst=STOP_CPS,sn=$serial_no";
    $cmdObj = new CMD();
    $cmdObj->sendCmd($cmd, $node);
    if ($cmdObj->rslt == "fail") {
        $result['rslt'] = $cmdObj->rslt;
        $result['reason'] = $cmdObj->reason;
        return;
    }
    $result['rslt'] = SUCCESS;
    $result['reason'] = "STOP COMMAND SENT SUCCESSFULLY";
    $result['rows'] = [];
    return $result;
}

function discovered($node, $hwRsp) {

    // parse hwString
    
    // $ackid=1-bkpln,status,device=miox(0),uuid=IAMAMIOXUUIDTHATYOUCANTDECODE*
    // UUID is serial number for now
    $newHwString = substr($hwRsp, 1, -1);
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
        $cmd = "inst=STOP_CPS,sn=$serialNum";
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

        $evt = "CPS_ON";
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
        $cpsObj->setPsta($newPsta, $newSsta);
        if ($cpsObj->rslt == FAIL) {
            $result['rslt'] = $cpsObj->rslt;
            $result['reason'] = $cpsObj->reason;
            return $result;
        }
        // if success
        $cpsObj->setSerialNo($serialNum);
        if ($cpsObj->rslt == FAIL) {
            $result['rslt'] = $cpsObj->rslt;
            $result['reason'] = $cpsObj->reason;
            return $result;
        }
        // call message 2
        // requires instruction and serial number
        $cmd = "inst=START_CPS,sn=$serialNum,cmd=\$status,source=all,ackid=$node-cps-csta*\$status,source=devices,ackid=$node-nadm-unds*";
        
        $cmdObj = new CMD();
        $cmdObj->sendCmd($cmd, $node);
        if ($cmdObj->rslt == FAIL) {
            $result['rslt'] = $cmdObj->rslt;
            $result['reason'] = $cmdObj->reason;
            return $result;
        }

        $result['rslt'] = SUCCESS;
        $result['reason'] = $cpsObj->reason;
        
        return $result;

    }


}

function updateCpsStatus($hwRsp) {
    
    // checks what type of $hwRsp is being sent
    if (strpos($hwRsp, "voltage") !== false){
        $result = updateCpsVolt($hwRsp);
        return $result;
    }
    else if (strpos($hwRsp, "temperature") !== false) {
        $result = updateCpsTemp($hwRsp);
        return $result;
    }
}

// function called by updateAlm in case string contains voltage only
// str looks like this "$ackid=1-cps,status,voltage1=46587mV,voltage2=47982mV,voltage3=48765mV,voltage4=49234mV*"
function updateCpsVolt($hwRsp) {
    // filters data brought from $cmd and extracts voltage values
    $newCmd = substr($hwRsp, 1, -1);
    $splitCmd = explode(',', $newCmd);
    $ackid = explode('=',$splitCmd[0]);
    $newAckid = $ackid[1];
    $volt1 = explode('=',$splitCmd[2]);
    $volt2 = explode('=',$splitCmd[3]);
    $volt3 = explode('=',$splitCmd[4]);
    $volt4 = explode('=',$splitCmd[5]);

    sscanf($volt1[1], "%d%s", $volt1Val, $volt1Unit);
    sscanf($volt2[1], "%d%s", $volt2Val, $volt2Unit);
    sscanf($volt3[1], "%d%s", $volt3Val, $volt3Unit);
    sscanf($volt4[1], "%d%s", $volt4Val, $volt4Unit);

    // get lowest and highest values from volt
    $volt_hi = max($volt1Val, $volt2Val, $volt3Val, $volt4Val);
    $volt_low = min($volt1Val, $volt2Val, $volt3Val, $volt4Val);
    
    // put units back onto volt values to prepare sending to t_nodes
    $newVolt_hi = round((int)($volt_hi/1000)) . 'V';
    $newVolt_low = round((int)($volt_low/1000)) . 'V';

    // extract node number from cmd
    $nodeArray = explode('-', $newAckid[0]);
    $nodeNumber = $nodeArray[0];
    $newNodeNumber = $nodeNumber;
    $nodeObj = new NODE($newNodeNumber);
    if($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }

    // write to t_nodes the volt_hi by default or the voltage that is out of range
    if ($volt_low < 42000) {
        $nodeObj->updateVolt($newVolt_low);
    }
    else{
        $nodeObj->updateVolt($newVolt_hi);
    }

    // makes new alm if voltage is out of range
    if (($volt_hi > 52000) || ($volt_low < 42000)) {
        $almid = $newAckid . '-V';
        $almObj = new ALMS($almid);
        if (count($almObj->rows) == 0) {
            $src = 'POWER';
            $almtype = 'VOLTAGE';
            $cond = 'VOLTAGE OUT-OF-RANGE';
            $sa = 'N';
            $sev = 'MIN';
            $remark = $almid . ' : ' . $cond;
            $almObj = new ALMS();
            $almObj->newAlm($almid, $src, $almtype, $cond, $sev, $sa, $remark);
            //logError if failed here
        }
    }

    // sys-clr alm if voltage is in range
    if (($volt_hi <= 46000) && ($volt_low >= 42000)) {
        $almid = $newAckid . '-V';
        $almObj = new ALMS($almid);
        if (count($almObj->rows) !== 0) {
            $remark = 'SYSTEM CLEAR ALARM: ' . $almid . ' : VOLTAGE IN-RANGE';
            $almObj->sysClr($almid, $remark);
            //logError if failed here
        }
    }
    $result['rslt'] = SUCCESS;
    $result['reason'] = "VOLTAGE ALARM UPDATE SUCCESS";
    return $result;
}

// function called by updateAlm in case string contains temp only
// str looks like this "$ackid=0-cps,status,temperature,zone1=67C,zone2=65C,zone3=66C,zone4=68C*"
function updateCpsTemp($hwRsp) {

    // filters data brought from $cmd and extracts temp values
    $newCmd = substr($hwRsp, 1, -1);
    $splitCmd = explode(',', $newCmd);
    $ackid = explode('=', $splitCmd[0]);
    $newAckid = $ackid[1];
    // $zeroBase = explode('-', $newAckid);
    // $oneBase = $zeroBase[0] + 1;
    // // puts back together 1-cps
    // $oneBaseAckid = $oneBase . '-' . $zeroBase[1];
    $temp1 = explode('=',$splitCmd[3]);
    $temp2 = explode('=',$splitCmd[4]);
    $temp3 = explode('=',$splitCmd[5]);
    $temp4 = explode('=',$splitCmd[6]);

    sscanf($temp1[1], "%d%s", $temp1Val, $temp1Unit);
    sscanf($temp2[1], "%d%s", $temp2Val, $temp2Unit);
    sscanf($temp3[1], "%d%s", $temp3Val, $temp3Unit);
    sscanf($temp4[1], "%d%s", $temp4Val, $temp4Unit);

    $temp_hi = max($temp1Val, $temp2Val, $temp3Val, $temp4Val);

    // combine temp value and unit to send to t_nodes
    $newTemp_hi = $temp_hi . $temp1Unit;
    
    // extract node number from cmd
    $nodeArray = explode('-', $newAckid[0]);
    $nodeNumber = $nodeArray[0];
    $newNodeNumber = $nodeNumber;
    $nodeObj = new NODE($newNodeNumber);
    if($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }
    
    // update t_nodes w/ highest temp
    $nodeObj->updateTemp($newTemp_hi);

    // makes new alm if temp is out of range
    if ($temp_hi > 66) {
        $almid = $newAckid . '-T';
        $almObj = new ALMS($almid);
        if (count($almObj->rows) == 0) {
            $src = 'POWER';
            $almtype = 'TEMPERATURE';
            $cond = 'TEMPERATURE OUT-OF-RANGE';
            $sa = 'N';
            $sev = 'MIN';
            $remark = $almid . ' : ' . $cond;
            $almObj = new ALMS();
            $almObj->newAlm($almid, $src, $almtype, $cond, $sev, $sa, $remark);
            if ($almObj->rslt == 'fail') {
                $result["rslt"] = "fail";
                $result["reason"] = "Fail to create alarm";
                return $result;
            }
        }
    }

    // sys-clr alm if temp is in range
    if ($temp_hi < 66) {
        $almid = $newAckid . '-T';
        $almObj = new ALMS($almid);
        if (count($almObj->rows) !== 0) {
            $remark = 'SYSTEM CLEAR ALARM: ' . $almid . ' : TEMPERATURE IN-RANGE';
            $almObj->sysClr($almid, $remark);
            if ($almObj->rslt == 'fail') {
                $result["rslt"] = "fail";
                $result["reason"] = "Fail to clear alarm";
                return $result;
            }
        }
    }
    $result['rslt'] = SUCCESS;
    $result['reason'] = "TEMP ALARM UPDATE SUCCESS";
    return $result;
}

function exec_resp($node, $hwRsp, $userObj) {

    // remove $ and * from string
    $rsp = substr($hwRsp, 1, -1);
    // divide string into sections
    $hwRspArray = explode(',', $rsp);
    // create ackid array to obtain ackid value
    $ackidArray = explode("=", $hwRspArray[0]);
    $ackid = $ackidArray[1];
    // parse ackid value to obtain node, api, apiAct
    $parsedAckid = explode('-', $ackid);
    $node = $parsedAckid[0];
    $api_key = $parsedAckid[1];
    $apiAct_key = $parsedAckid[2];

    // Obtain full api string from constant and api action from constant
    $api = apiAndActArray[$api_key]['api'];
    $apiAct = apiAndActArray[$api_key][$apiAct_key];

    // post to nodeapi to update node cps stats
    
    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch.php";
    $params = ["user"=>"SYSTEM", "api"=>$api, 'act'=>$apiAct, "node"=>$node, "hwRsp"=>$hwRsp];
    //@TODO Maybe need asyncPostRequest here? Sync for debugging
    $postReqObj->syncPostRequest($url, $params);
    return json_decode($postReqObj->reply);
    
}

function exec_cmd($node, $cmd, $userObj) {
    // permissions check here
    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    // nodeOpe->exec() will send UDP->msg[inst=EXEC,node,comport,serial_no,cmd] to cpsLoop
    // will receive string like this: ACKID=$node-api-act
    
    // get node from cmd
    $cmdArray = explode("=", $cmd);
    $ackIdArray = explode("-", $cmdArray[1]);
    $node = $ackIdArray[0];
    $api = $ackIdArray[1];
    $act = $ackIdArray[2];

    // create cpsObj to get comport and serialnum
    $cpsObj = new CPS($node);
    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }

    $newCmd = "inst=EXEC,$node,$cpsObj->dev,$cpsObj->serial_no";

    $cmdObj = new CMD();
    $cmdObj->sendCmd($newCmd, $node);
    if ($cmdObj->rslt == FAIL) {
        $result['rslt'] = $cmdObj->rslt;
        $result['reason'] = $cmdObj->reason;
        return $result;
    }
}

?>