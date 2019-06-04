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

include '../os/ipcComPortClass.php';

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

$hwRsp = "";
if (isset($_POST['hwRsp'])) {
    $hwRsp = $_POST['hwRsp'];
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
    $result['rslt'] = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows'] = $nodeObj->rows;
    //$result = queryAll($nodeObj, $userObj);
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
    
    $nodeObj = new NODE($node);
    if ($nodeObj->rslt != SUCCESS) {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
    }
    else {
        $cmdExtract = explode('-',$cmd);
    
        if ($cmdExtract[1] === 'ONLINE') {
            
            $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'CPS_ON');
        }
        else {
            $sms = new SMS($nodeObj->psta, $nodeObj->ssta, 'CPS_OFF');
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
    $result = updateNodeDevicesStatus($node, $hwRsp);
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

function updateNodeDevicesStatus($node, $hwRsp) {
    // construct device obj using node
    $deviceObj = new DEV($node);
    if ($deviceObj->rslt == FAIL) {
        $result['rslt'] = $deviceObj->rslt;
        $result['reason'] = $deviceObj->reason;
        return $result;
    }

    // extract miox, mioy, mre, cps
    $parsedString = $deviceObj->parseDevString($hwRsp);
    if ($deviceObj->rslt == FAIL) {
        $result['rslt'] = $deviceObj->rslt;
        $result['reason'] = $deviceObj->reason;
        return $result;
    }

    $newMiox = $parsedString['miox'];
    $newMioy = $parsedString['mioy'];
    $newMre = $parsedString['mre'];
    $newCps = $parsedString['cps'];

    $currentMiox = $deviceObj->getMiox();
    $currentMioy = $deviceObj->getMioy();
    $currentMre = $deviceObj->getMre();
    $currentCps = $deviceObj->getCps();

    $diffMiox = false;
    $diffMioy = false;

    // compare strings and set flags for if different
    if (strcmp($currentMiox, $newMiox) !== 0) {
        if ($deviceObj->setMiox($newMiox)) {
            if ($deviceObj->rslt === FAIL) {
                $result['rslt'] = $deviceObj->rslt;
                $result['reason'] = $deviceObj->reason;
                return $result;
            }
            $diffMiox = true;
        }
    }

    if (strcmp($currentMioy, $newMioy) !== 0) {
        if ($deviceObj->setMioy($newMioy)) {
            if ($deviceObj->rslt === FAIL) {
                $result['rslt'] = $deviceObj->rslt;
                $result['reason'] = $deviceObj->reason;
                return $result;
            }
            $diffMioy = true;
        }
    }

    if (strcmp($currentMre, $newMre) !== 0) {
        if ($deviceObj->setMre($newMre)) {
            if ($deviceObj->rslt === FAIL) {
                $result['rslt'] = $deviceObj->rslt;
                $result['reason'] = $deviceObj->reason;
                return $result;
            }
        }
    }

    if (strcmp($currentCps, $newCps) !== 0) {
        if ($deviceObj->setCps($newCps)) {
            if ($deviceObj->rslt === FAIL) {
                $result['rslt'] = $deviceObj->rslt;
                $result['reason'] = $deviceObj->reason;
                return $result;
            }
        }
    }

    // insert or remove cards based on slot after compare pairs of numbers
    if ($diffMiox) {
        $newMioxArray = str_split($newMiox, 2);
        $currentMioxArray = str_split($currentMiox, 2);

        $postReqObj = new POST_REQUEST();

        for ($i = 0; $i < count($newMioxArray); $i++) {
            if ($newMioxArray[$i] !== $currentMioxArray[$i]) {
                $slot = $i + 1;
                $url = 'ipcDispatch.php';
                if (strpos($newMioxArray[$i], "0") !== false){
                    $params = array("act"=>"remove", "api"=>"ipcMxc", "user"=>"SYSTEM", "node"=>$node, "shelf"=>"1", "slot"=>$slot, "type"=>"MIOX");
                    $postReqObj->asyncPostRequest($url, $params);
                    //@TODO What happens if this fails?
                }
                else {
                    $params = array("act"=>"insert", "api"=>"ipcMxc", "user"=>"SYSTEM", "node"=>$node, "shelf"=>"1", "slot"=>$slot, "type"=>"MIOX");
                    $postReqObj->asyncPostRequest($url, $params);
                    //@TODO Wat happens if this fails?
                }
            }
        }
    }

    if ($diffMioy) {
        $newMioyArray = str_split($newMioy, 2);
        $currentMioyArray = str_split($currentMioy, 2);

        $postReqObj = new POST_REQUEST();

        for ($i = 0; $i < count($newMioyArray); $i++) {
            if ($newMioyArray[$i] !== $currentMioyArray[$i]) {
                $slot = $i + 1;
                $url = 'ipcDispatch.php';
                if (strpos($newMioyArray[$i], "0") !== false){
                    $params = array("act"=>"remove", "api"=>"ipcMxc", "user"=>"SYSTEM", "node"=>$node, "shelf"=>"2", "slot"=>$slot, "type"=>"MIOY");
                    $postReqObj->asyncPostRequest($url, $params);
                    //@TODO What happens if this fails?
                }
                else {
                    $params = array("act"=>"insert", "api"=>"ipcMxc", "user"=>"SYSTEM", "node"=>$node, "shelf"=>"2", "slot"=>$slot, "type"=>"MIOY");
                    $postReqObj->asyncPostRequest($url, $params);
                    //@TODO Wat happens if this fails?
                }
            }
        }
    }

    $result['rslt'] = SUCCESS;
    $result['reason'] = "NODE DEVICES UPDATED";
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
    $nodeId = $node;

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

    // $command = "php ipcCpsloop.php $node $ipadr $port > /dev/null 2>&1 & echo $!";
    $command = "php ipcCpsloop.php 1 9001 '/dev/ttyUSB0' > /dev/null 2>&1 & echo $!";

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
