<?php
/*
* Copy Right @ 2018
* BHD Solutions, LLC.
* Project: CO-IPC
* Filename: ipcTb.php
* Change history: 
* 04-11-2019: created (Kris)
*/	
	
//Initialize expected inputs

include '../os/ipcCpsClientClass.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

error_reporting(E_ALL);

//$defaultIp = '192.168.5.100';
$defaultIp = '192.168.1.99';   // test IpAddr
$defaultIpPort = 9000;

$act = "";
if (isset($_POST['act']))
    $act = $_POST['act'];
    
$node = "";
if (isset($_POST['node'])) {
    $node = $_POST['node'];
}

$ipadr = "";
if (isset($_POST['ipadr'])) {
    $ipadr = $_POST['ipadr'];
}

$gw = "";
if (isset($_POST['gw'])) {
    $gw = $_POST['gw'];
}

$netmask = "";
if (isset($_POST['netmask'])) {
    $netmask = $_POST['netmask'];
}

$port = "";
if (isset($_POST['port'])) {
    $port = $_POST['port'];
}

$rack = "";
if (isset($_POST['rack'])) {
    $rack = $_POST['rack'];
}

$com = "";
if (isset($_POST['com'])) {
    $com = $_POST['com'];
}

$cmd = "";
if (isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
}

$device_status = "";
if (isset($_POST['device_status'])) {
    $device_status = $_POST['device_status'];
}



// $evtLog = new EVENTLOG($user, "IPC ADMINISTRATION", "NODE ADMINISTRATION", $act, $_POST);

// $nodeObj = new NODE($node);
// if ($nodeObj->rslt != SUCCESS) {
//     $result['rslt'] = $nodeObj->rslt;
//     $result['reason'] = $nodeObj->reason;
//     echo json_encode($result);
//     mysqli_close($db);
//     return;
// }
    
//Dispatch to functions

if ($act == "queryAll") {
    $nodeObj = new NODES();
    $result = queryAll($nodeObj, $userObj);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if($act == "CHECK_NODES") {
    // $result = checkNodes($nodeObj);
    // echo json_encode($result);
    // mysqli_close($db);
    return;
}

if ($act == "UPDATE_NETWORK") {
    $result = updateNetwork($node, $ipadr, $gw, $netmask, $port, $nodeObj, $userObj);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "UPDATE_RACK") {
    $nodeObj = new NODE($node);
    if ($nodeObj->rslt != SUCCESS) {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
    }
    else {
        $result = updateRack($rack, $nodeObj, $userObj);
    }

    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "updateCpsStatus") {
    $result = updateCpsStatus($cmd, $userObj);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "START_NODE") {
    $nodeObj = new NODE($node);
    if ($nodeObj->rslt != SUCCESS) {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
    }
    else {
        $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'START_NODE');

        if ($sms->rslt === FAIL) {
            $result['rslt'] = $sms->rslt;
            $result['reason'] = $sms->reason;
        } else {
            $result = startNode($node, $nodeObj, $userObj);
            if ($result['rslt'] == 'success') {
                $nodeObj->updatePsta($sms->npsta, $sms->nssta);
            }
        }
    }
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "STOP_NODE") {
    $nodeObj = new NODE($node);
    if ($nodeObj->rslt != SUCCESS) {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
    }
    else {
        $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'STOP_NODE');

        if ($sms->rslt === FAIL) {
            $result['rslt'] = $sms->rslt;
            $result['reason'] = $sms->reason;
        } else {
            $result = stopNode($node, $nodeObj, $userObj);
            if ($result['rslt'] == 'success') {
                $nodeObj->updatePsta($sms->npsta, $sms->nssta);
            }
        }
    }

    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "updateCpsCom") {
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

    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "DISCOVER_NODE") {
    $nodeObj = new NODE($node);
    if ($nodeObj->rslt != SUCCESS) {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
    }
    else {
        $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'DISCOVER_NODE');

        if ($sms->rslt === FAIL) {
            $result['rslt'] = $sms->rslt;
            $result['reason'] = $sms->reason;
        } else {
            $result = discoverIP($defaultIp,$defaultIpPort);

            if ($result['rslt'] == 'success') {
                $nodeObj->updatePsta($sms->npsta, $sms->nssta);
            }
        }
    }

    echo json_encode($result);
    mysqli_close($db);
	return;
}

if ($act == "ASSIGN_NODE_IP") {
    
    $nodeObj = new NODE($node);
    if ($nodeObj->rslt != SUCCESS) {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
    }
    else {
        $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'ASSIGN_NODE');

        if ($sms->rslt === FAIL) {
            $result['rslt'] = $sms->rslt;
            $result['reason'] = $sms->reason;
        } else {
            $result = assignIP($defaultIp, $defaultIpPort, $ipadr, $port, $nodeObj);
        }
    }

    echo json_encode($result);
    mysqli_close($db);
	return;
}
if ($act == "UNASSIGN_NODE") { // @TODO may change act name
    $nodeObj = new NODE($node);
    if ($nodeObj->rslt != SUCCESS) {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
    }
    else {
        $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'UNASSIGN_NODE');

        if ($sms->rslt === FAIL) {
            $result['rslt'] = $sms->rslt;
            $result['reason'] = $sms->reason;
        } else {
            $result = unassignNode($ipadr, $port, $sms->npsta, $sms->nssta, $nodeObj);
        }
    }

    echo json_encode($result);
    mysqli_close($db);
    return;
}
if ($act == "updateNodeDevicesStatus") {
    try{
        $result = updateNodeDevicesStatus($device_status);

    }
    catch (Throwable $e){
        $result['reason'] = $e->getMessage();
    }
    echo json_encode($result);
    mysqli_close($db);
    return;
}
else {
	$result["rslt"] = 'fail';
	$result["reason"] = "This action is under development!";
	echo json_encode($result);
	mysqli_close($db);
	return;
}

// LOCAL FUNCTIONS

function updateNodeDevicesStatus($device_status) {
    $deviceObj = new DEV($device_status);
    $deviceObj->getDevicePcb('miox');
    $miox = $deviceObj->rows[0]['pcb'];
    // $result['rslt'] = FAIL;
    // $result['reason'] = $miox;
    // return $result;

    $deviceObj->getDevicePcb('mioy');
    $mioy = $deviceObj->rows[0]['pcb'];

    $deviceObj->getDevicePcb('mre');
    $mre = $deviceObj->rows[0]['pcb'];

    $deviceObj->updateDevicePcb('miox', $miox);
    if ($deviceObj->rslt == FAIL) {
        $result['rslt'] = $deviceObj->rslt;
        $result['reason'] = $deviceObj->reason;
        return $result;
    }
    $deviceObj->updateDevicePcb('mioy', $mioy);
    if ($deviceObj->rslt == FAIL) {
        $result['rslt'] = $deviceObj->rslt;
        $result['reason'] = $deviceObj->reason;
        return $result;
    }
    $deviceObj->updateDevicePcb('mre', $mre);
    if ($deviceObj->rslt == FAIL) {
        $result['rslt'] = $deviceObj->rslt;
        $result['reason'] = $deviceObj->reason;
        return $result;
    }
    //@TODO add CPS check and update

    $result['rslt'] = SUCCESS;
    $result['reason'] = "UPDATE DEVICE SUCCESS";
    return $result;
}
// This function extract individual status from a combined status received from the CPS
function filterNodeStatus($cmd) {
    $dataArray = [];
    for ($i=0; $i<count($cmd); $i++) {
        if ((strpos($cmd[$i], "voltage") !== false) && (strpos($cmd[$i], "current") !== false)) {
            $value = filterNodeCurrent($cmd[$i]);
            $dataArray['current'] = $value;
        }
        else if (strpos($cmd[$i], "voltage") !== false){
            $value = filterNodeVolt($cmd[$i]);
            $dataArray['volt'] = $value;

        }
        else if (strpos($cmd[$i], "temperature") !== false) {
            $value = filterNodeTemp($cmd[$i]);
            $dataArray['temp'] = $value;
        }
    }
   
	return $dataArray;
}

function filterNodeCurrent($cmd) {
    // str looks like "$ackid=1-cps,status,current=1239mA,voltage=45678mV*"
    $newCmd = substr($cmd, 1, -1);

	/**
	 * Split $newCmd at each ',' into an array containing each slice
	 * Leaving: [["ackid=1-CPS"]["status"]["current=1239mA"][voltage=45678mV]]
	 */
	$splitCmd = explode(',', $newCmd);

	/**
	 * Extract ackid
	 */
	$ackidArray = explode('=', $splitCmd[0]);
    $ackid = $ackidArray[1];
 
	/**
	 * Extract status
	 */
	$status = $splitCmd[1];

	/**
	 * Extract current
	 */
	$currentArray = explode('=', $splitCmd[2]);
	sscanf($currentArray[1], "%d%s", $currentVal, $currentUnit);

	/**
	 * Extract voltage
	 */
	$voltArray = explode('=', $splitCmd[3]);
    sscanf($voltArray[1], "%d%s", $voltVal, $voltUnit);

    $nodeCurrent = $currentVal . $currentUnit;
    return $nodeCurrent;
}

function filterNodeVolt($cmd) {
    // str looks like this "$ackid=1-cps,status,voltage1=46587mV,voltage2=47982mV,voltage3=48765mV,voltage4=49234mV*"
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

    if ($volt_hi >= 52000) {
        return $newVolt_hi;
    }
    else if ($volt_low < 42000) {
        return $newVolt_low;
    }

}

function filterNodeTemp($cmd) {
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

    return $newTemp_hi;
}

function discoverIP($ip, $port) {
    try {
        // 1. Attempt to connect CPS of the CO-500 node.
        for($i=0; $i<10; $i++) {
            $cpsClientObj = new CPSCLIENT($ip, $port, 0, 500000);
            if($cpsClientObj->rslt == 'fail') {
                if($i == 9)   
                    // If failed up to 10 time then return fail reason
                    throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,0);
                else
                    continue;    
            }
            else {
                break;
            }
        }

        // 2. Request all status from CPS
        $cmd = "\$STATUS,SOURCE=ALL,ACKID=NEW-CPS*"; 
        $cpsClientObj->sendCommand($cmd);
        if($cpsClientObj->rslt == 'fail') {
            throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,0);
        }

        // 3. Wait 100 ms before reading responses from CPS
        usleep(100000);
        $reply = '';
        while(1) {     
            $cpsClientObj->receiveRsp();
            if($cpsClientObj->rslt == 'fail') {
                throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,0);
            }
            else {
                if($cpsClientObj->rsp === "") {
                    break;
                }
                $reply .= $cpsClientObj->rsp;
            }
        }
        $cpsClientObj->endConnection();

        $rsp = preg_split("/(\r\n|\n|\r)/",$reply);
        if(count($rsp) === 0) {
            throw new Exception("The new device does not receive the command",16);
        }
        else {
            $parameters = filterNodeStatus($rsp);
            $result['rslt'] = 'success';
            $result['rows'] = $parameters;
            $result['reason'] = "Node is discovered successfully";
            return $result;
        }
    }
    catch (Throwable $t) {
        $result['rslt'] = 'fail';
        $result['reason'] = $t->getMessage();
        return $result;
    }
}

function unassignNode($ip, $port, $npsta, $nssta, $nodeObj) {

    try {
        // @TODO detach node from ipcloop
        
        $nodeObj->updatePsta($npsta, $nssta);

        $result['rslt'] = SUCCESS;
        $result['reason'] = "Node unassigned successfully";
        return $result;

    } catch (Throwable $t) {
        $result['rslt'] = 'fail';
        $result['reason'] = $t->getMessage();
        return $result;
    }
}

function assignIP($oldIp, $oldport, $newIp, $newPort, $nodeObj){

    try {
        // attempt upto 10 times to establish socket connection with CPS-HW
        for($i=0; $i<10; $i++) {
            $cpsClientObj = new CPSCLIENT($oldIp, $oldport, 0, 500000);
            if($cpsClientObj->rslt == 'fail') {
                if($i == 9)   
                    throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,12);
                else
                    continue;    
            }
            else {
                break;
            }
        }

        // assign new IpAddr to CPS-HW 
        $cmd = "\$COMMAND,ACTION=UPDATE,IPADDR=".$newIp.",PORT=".$newPort.",ACKID=IP-CPS*"; 

        $cpsClientObj->sendCommand($cmd);
        if($cpsClientObj->rslt == 'fail') {
            throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
        }

        // wait to receive ack from CPS-HW
        usleep(100000); 
        $rsp=[];
        while(1) {
            $cpsClientObj->receiveRsp();
            if($cpsClientObj->rslt == 'fail') {
                throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
            }
         
            if($cpsClientObj->rsp === "") {
                break;
            }

            $rsp[] = $cpsClientObj->rsp;
           
        }
        $cpsClientObj->endConnection();

        if(count($rsp) === 0) {
            throw new Exception("The new device does not receive the command",16);
        }


    }
    catch (Throwable $t) {
        $result['rslt'] = 'fail';
        $result['reason'] = $t->getMessage();
        return $result;
    }

    // give CPS-HW 10 sec to reboot
    sleep(10);

    // now re-discover CPS-HW with new assigned IpAddr
    $result = discoverIP($newIp,$newPort);
    if($result['rslt'] == 'fail') {
        return $result;
    }

    // move Node to new psta/ssta
    $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'ASSIGN_NODE');
    if ($sms->rslt === 'fail') {
        $result['rslt'] = $sms->rslt;
        $result['reason'] = $sms->reason;
        return $result;
    }

    $nodeObj->updatePsta($sms->npsta, $sms->nssta);
    if ($nodeObj->rslt === 'fail') {
        $result['rslt'] = 'fail';
        $result['reason'] = $nodeObj->reason;
        return $result;
    }

    $nodeObj->queryAll();
    $result['rslt'] = 'success';
    $result['rows']= $nodeObj->rows;
    $result['reason'] = "New IP is assigned successfully";        
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

function updateCpsStatus($cmd, $userObj) {
    
	//-------check user permission--------------
	// if ($userObj->grpObj->ipcadm != "Y") {
    //     $result['rslt'] = 'fail';
    //     $result['reason'] = 'Permission Denied';
    //     return $result;
    // }
	///////////////////////////////////////////////

	// checks what type of $cmd is being sent
	if ((strpos($cmd, "voltage") !== false) && (strpos($cmd, "current") !== false)) {
		$result = updateCpsCurrent($cmd);
		return $result;
	}
	else if (strpos($cmd, "voltage") !== false){
		$result = updateCpsVolt($cmd);
		return $result;
	}
	else if (strpos($cmd, "temperature") !== false) {
		$result = updateCpsTemp($cmd);
		return $result;
	}
}


function updateCpsCurrent($cmd) {
    
	/**
	 * Parse $cmd to retrieve: almid, status, current, voltage
	 * 
	 * For example: $ackid=1-CPS,status,current=1239mA,voltage=45678mV*
	 */
	
	/** 
	 * Remove $ and * from beginning and end of $cmd
	 * Leaving: ackid=1-CPS,status,current=1239mA,voltage=45678mV
	 */
	$newCmd = substr($cmd, 1, -1);

	/**
	 * Split $newCmd at each ',' into an array containing each slice
	 * Leaving: [["ackid=1-CPS"]["status"]["current=1239mA"][voltage=45678mV]]
	 */
	$splitCmd = explode(',', $newCmd);

	/**
	 * Extract ackid
	 */
	$ackidArray = explode('=', $splitCmd[0]);
    $ackid = $ackidArray[1];
 
	/**
	 * Extract status
	 */
	$status = $splitCmd[1];

	/**
	 * Extract current
	 */
	$currentArray = explode('=', $splitCmd[2]);
	sscanf($currentArray[1], "%d%s", $currentVal, $currentUnit);

	/**
	 * Extract voltage
	 */
	$voltArray = explode('=', $splitCmd[3]);
    sscanf($voltArray[1], "%d%s", $voltVal, $voltUnit);
    
    //  extract node number
    //  $nodeArray = explode('-', $ackid);
    //  $nodeNumber = $nodeArray[0];
    //  $newNodeNumber = $nodeNumber + 1;
    //  $nodeObj = new NODE($newNodeNumber);
    //  if($nodeObj->rslt == 'fail') {
    //     $result['rslt'] = $nodeObj->rslt;
    //     $result['reason'] = $nodeObj->reason;
    //     return $result;
    //  }
    //  $newCurrent = $currentVal . $currentUnit;
    //  $nodeObj->updateCurrent($newCurrent);

	/**
	 * If current is greater than 1600, then create new alarm with:
	 * almid=1-CPS-P, sev=MIN, sa=N, src=POWER, type=CURRENT, cond=CURRENT OUT-OF-RANGE
	 */
	if ($currentVal > 1500) {
		$almid 	= $ackid . '-P';
		$almObj = new ALMS($almid);
		
		if (count($almObj->rows) == 0) {

			$src 	= "POWER";
			$type 	= "CURRENT";
			$cond 	= "CURRENT OUT-OF-RANGE";
			$sev 	= "MIN";
			$sa 	= "N";
			$remark = $almid . ' : ' . $cond;
			
			$almObj->newAlm($almid, $src, $type, $cond, $sev, $sa, $remark);
			if ($almObj->rslt == "fail") {
				$result["rslt"]   = $almObj->rslt;
				$result["reason"] = $almObj->reason;
				return $result;
			}
		}
	}
	/**
	 * If current is less than or equal to 1600 then send System Clear alarm
	 */
	else if ($currentVal <= 1500) {
		$almid = $ackid . '-P';

		$almObj = new ALMS($almid);
		
		if (count($almObj->rows) !== 0) {
			$remark = 'SYSTEM CLEAR ALARM: ' . $almid . ' : CURRENT IN-RANGE';
			$almObj->sysClr($almid, $remark);
			if ($almObj->rslt == FAIL) {
				$result['rslt']   = $almObj->rslt;
				$result['reason'] = $almObj->reason;
				return $result;
			}
		}
	}
	$result['rslt'] = SUCCESS;
	$result['reason'] = "POWER ALARM UPDATE SUCCESS";
	return $result;
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


// updates name of rack
function updateRack($rack, $nodeObj, $userObj) {
    // CHECK USER PERMISSIONS
    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    $nodeObj->updateRack($rack);
    
    $result['rslt'] = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows'] = $nodeObj->rows;
    
    return $result;
}


// starts the cpsLoop. gets pid and updates table with pid
function startNode($node, $nodeObj, $userObj) {
    
    // CHECK USER PERMISSIONS

    if ($userObj->grpObj->sysadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }
    
    // start cpsloop & get it's process ID
    chdir("../os");

    $ipadr = $nodeObj->ipadr;
    $port = $nodeObj->ip_port;

    $command = "php ipcCpsloop.php $node $ipadr $port > /dev/null 2>&1 & echo $!";

    $pid = exec($command, $output);

    // write pid to table
    $nodeObj->updateScanPid($pid);
    if($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }

    // gather updated info from table
    $nodeObj->queryAll();
    if($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }

    $result['rslt'] = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows'] = $nodeObj->rows;
    return $result;
}

// kills process based on pid
function stopNode($node, $nodeObj, $userObj) {

    // CHECK USER PERMISSIONS

    if ($userObj->grpObj->ipcadm != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    // kill process based on pid
    $pid = $nodeObj->pid;
    $command = "kill -9 $pid";
    exec ($command, $output);
    sleep(1);
    if (file_exists( "/proc/$pid")) {
        $result['rslt'] = 'fail';
        $result['reason'] = "CPS Loop is still running on node: '$node'!";
        return $result;
    }

    // update pid in table
    $nodeObj->updateScanPid();
    if ($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }

    // gather updated info from table
    $nodeObj->queryAll();
    if ($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }

    $result['rslt']   = 'success';
    $result['reason'] = "CPS Loop has been stopped on node: '$node'";
    $result['rows'] = $nodeObj->rows;
    return $result;


}

function updateNetwork($node, $ipadr, $gw, $netmask, $port, $nodeObj, $userObj) {

    // CHECK USER PERMISSIONS

    if ($userObj->grpObj->ipcadm != "Y") {
		$result['rslt'] = 'fail';
		$result['reason'] = 'Permission Denied';
		return $result;
    }
    
    // verify valid format for ip,gw, netmask / checks port range
    if (filter_var($ipadr, FILTER_VALIDATE_IP) === false) {
        $result['rslt'] = FAIL;
        $result['reason'] = "Invalid IP Address '$ipadr' on Node '$node'";
        return $result; 
    }

    if (($port < 9000) || ($port > 10000)) {
        $result['rslt'] = FAIL;
        $result['reason'] = "Invalid IP Port '$port' on Node '$node'";
        return $result;
    }

    if (filter_var($gw, FILTER_VALIDATE_IP) === false) {
        $result['rslt'] = FAIL;
        $result['reason'] = "Invalid Gateway '$gw' on Node '$node'";
        return $result;
    }

    if (filter_var($netmask, FILTER_VALIDATE_IP) === false) {
        $result['rslt'] = FAIL;
        $result['reason'] = "Invalid Netmask '$netmask' on Node '$node'";
        return $result;
    }

    $nodeObj->updateNetwork($node, $ipadr, $gw, $netmask, $port);

    $result['rslt'] = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows'] = $nodeObj->rows;
    
    return $result;
}

function checkNodes($nodeObj) {

    $nodeObj->queryAll();
    if($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }

    $nodeRows = $nodeObj->rows;
    for($i=0; $i<count($nodeRows); $i++) {
        if($nodeRows[$i]['pid'] === "" || $nodeRows[$i]['pid'] === NULL) {
            continue;
        }
        else {
            $pid = $nodeRows[$i]['pid'];
            if (!file_exists( "/proc/$pid" )){
                $nodeObj->updatePid($nodeRows[$i]['node']);
                if($nodeObj->rslt == 'fail') {
                    $result['rslt'] = $nodeObj->rslt;
                    $result['reason'] = $nodeObj->reason;
                    return $result;
                }
            }
        }
    }

    $nodeObj->queryAll();
  
    $result['rslt'] = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows'] = $nodeObj->rows;
    return $result;
    

}

function queryAll($nodeObj, $userObj) {
    $nodeObj->queryAll();
    $result['rslt'] = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows'] = $nodeObj->rows;
    return $result;
}




?>
