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
    $result = discovered($node, $hwRsp);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "CPS_STATUS") {
    $result = updateCpsStatus($cmd);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "CPS_ON") {
    $result = cps_on($node, $cmd);
    echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "updateCpsCom") {
    $nodeObj = new NODE($node);
    if ($nodeObj->rslt != SUCCESS) {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
    }
    else {
        $cmdExtract = explode('-',$cmd);
    
        if ($cmdExtract[1] === 'ONLINE') {
            $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'COMM_ON');
        }
        else {
            $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'COMM_OFF');
        }
    
        if ($sms->rslt === SUCCESS) {
            $nodeObj->updatePsta($sms->npsta, $sms->nssta);
        }
        
        $result = updateCpsCom($cmd, $userObj);
    }
    

    echo json_encode($result);
    mysqli_close($db);
    return;
}

// functions area

function cps_on($node, $cmd) {
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
    $cpsObj->updatePsta($smsObj->npsta, $smsObj->ssta);
    if ($cpsObj->rslt == FAIL) {
        $result['rslt'] = $cpsObj->rslt;
        $result['reason'] = $cpsObj->reason;
        return $result;
    }

    // post to nodeapi to update node cps stats
    $postReqObj = new POST_REQUEST();
    $url = "ipcDispatch";
    $params = ["user"=>"SYSTEM", "api"=>"ipcNodeAdmin", "node"=>$node, "cmd"=>"$node-ONLINE"];
    $postReqObj->asyncPostRequest($url, $params);

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

    $evt = "DISCV_CPS";
    // test sms
    $smsObj = new SMS($psta, $ssta, $evt);
    if ($smsObj->rslt == FAIL) {
        $result['rslt'] = $smsObj->rslt;
        $result['reason'] = $smsObj->reason;
        return $result;
    }

    // formulate msg #1
    $cmd = "inst=DISCV_CPS,node=$node,dev=$cpsObj->dev,cmd=\$status,source=uuid,device=backplane,ackid=$node-bkpln*";

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

    $evt = "DISCV_CPS";

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


function stop($node, $userObj) {

    // permissions check here
    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }
    $cpsObj = new CPS($node);
    $cmd = "inst=STOP_CPS,node=$node,dev=$cpsObj->dev";

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
        $cmd = "inst=STOP_CPS,node=$node,dev=$cpsObj->dev";
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

        $cmd = "inst=START_CPS,node=$node,dev=$cpsObj->dev,cmd=\$status,source=all,ackid=$node-CPS*\$status,source=devices,ackid=$node-dev*";
        
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



function updateCpsStatus($cmd) {
    
    // checks what type of $cmd is being sent
    if (strpos($cmd, "voltage") !== false){
        $result = updateCpsVolt($cmd);
        return $result;
    }
    else if (strpos($cmd, "temperature") !== false) {
        $result = updateCpsTemp($cmd);
        return $result;
    }
}

// function called by updateAlm in case string contains voltage only
// str looks like this "$ackid=1-cps,status,voltage1=46587mV,voltage2=47982mV,voltage3=48765mV,voltage4=49234mV*"
function updateCpsVolt($cmd) {
    // filters data brought from $cmd and extracts voltage values
    $newCmd = substr($cmd, 1, -1);
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
    $newNodeNumber = $nodeNumber + 1;
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
function updateCpsTemp($cmd) {

    // filters data brought from $cmd and extracts temp values
    $newCmd = substr($cmd, 1, -1);
    $splitCmd = explode(',', $newCmd);
    $ackid = explode('=', $splitCmd[0]);
    $newAckid = $ackid[1];
    $zeroBase = explode('-', $newAckid);
    $oneBase = $zeroBase[0] + 1;
    // puts back together 1-cps
    $oneBaseAckid = $oneBase . '-' . $zeroBase[1];
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
    $newNodeNumber = $nodeNumber + 1;
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

function updateCpsCom($cmd,$userObj) {
    //-------check user permission--------------
        // if ($userObj->grpObj->ipcadm != "Y") {
        //     $result['rslt'] = 'fail';
        //     $result['reason'] = 'Permission Denied';
        //     return $result;
        // }
    ///////////////////////////////////////////////
    

    /**
     * 1) $nodeObj = new NODE($node);
     * 2) If $node exists then $nodeObj->updateCOM($com)
     */
    $cmdExtract = explode('-',$cmd);
    $node = $cmdExtract[0];
    $com = $cmdExtract[1];
    $nodeId = $node+1;

    $nodeObj = new NODE($nodeId);
    if ($nodeObj->rslt != FAIL) {
        $nodeObj->updateCOM($com);
        if ($nodeObj->rslt == FAIL) {
            $result['rslt'] = $nodeObj->rslt;
            $result['reason'] = $nodeObj->reason;
            return $result;
        }
    }
    /**
     * 3) If not exist then do nothing (return)
     */
    else {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }
    /**
     * 4) If $com == "OFFLINE" then create new alarm where:
     *      almid='$node-CPS-C', 
     *      sev=MAJ, 
     *      sa=N, 
     *      src=EQUIP, 
     *      type=COMMUNICATION, 
     *      cond= COMMUNICATION, 
     *      remark=CPS: OFFLINE
     */
    if ($com == "OFFLINE") {
        $almid = $node . "-CPS-C";
        $almObj = new ALMS($almid);
        if (count($almObj->rows) == 0) {
            $src    = "EQUIP";
            $type   = "COMMUNICATION";
            $cond   = "COMMUNICATION";
            $sev    = "MAJ";
            $sa     = "N";
            $remark = "CPS: OFFLINE";
			$almObj->newAlm($almid, $src, $type, $cond, $sev, $sa, $remark);
            if ($almObj->rslt == "fail") {
				$result["rslt"]   = $almObj->rslt;
				$result["reason"] = $almObj->reason;
				return $result;
			}
        }
    }
    /**
     * 6) If $com == "ONLINE" then send SYS-CLR alarm
     */
    if ($com == "ONLINE") {
        $almid = $node."-CPS-C";
        $almObj = new ALMS($almid);
        if (count($almObj->rows) > 0) {
            $remark = $almid . " : SYSTEM CLEAR ALARM";
            $almObj->sysClr($almid, $remark);
            if ($almObj->rslt == FAIL) {
				$result['rslt']   = $almObj->rslt;
				$result['reason'] = $almObj->reason;
				return $result;
			}
        }
    }
    $result['rslt'] = $almObj->rslt;
    $result['reason'] = $almObj->reason;
    return $result;
}

?>