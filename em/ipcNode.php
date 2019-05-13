<?php

//initialize
$act = "";
if (isset($_POST['act'])) {
    $act = $_POST['act'];
}

$node = "";
if (isset($_POST['node'])) {
    $node = $_POST['node'];
}

$volt = "";
if (isset($_POST['volt'])) {
    $volt = $_POST['volt'];
}

$temp = "";
if (isset($_POST['temp'])) {
    $temp = $_POST['temp'];
}

$com = "";
if (isset($_POST['com'])) {
    $com = $_POST['com'];
}

$mx0 = "";
if (isset($_POST['mx0'])) {
    $mx0 = $_POST['mx0'];
}

$mx1 = "";
if (isset($_POST['mx1'])) {
    $mx1 = $_POST['mx1'];
}

$mx2 = "";
if (isset($_POST['mx2'])) {
    $mx2 = $_POST['mx2'];
}

$mx3 = "";
if (isset($_POST['mx3'])) {
    $mx3 = $_POST['mx3'];
}

$mx4 = "";
if (isset($_POST['mx4'])) {
    $mx4 = $_POST['mx4'];
}

$mx5 = "";
if (isset($_POST['mx5'])) {
    $mx5 = $_POST['mx5'];
}

$mx6 = "";
if (isset($_POST['mx6'])) {
    $mx6 = $_POST['mx6'];
}

$mx7 = "";
if (isset($_POST['mx7'])) {
    $mx7 = $_POST['mx7'];
}

$mx8 = "";
if (isset($_POST['mx8'])) {
    $mx8 = $_POST['mx8'];
}

$mx9 = "";
if (isset($_POST['mx9'])) {
    $mx9 = $_POST['mx9'];
}

$my0 = "";
if (isset($_POST['my0'])) {
    $my0 = $_POST['my0'];
}

$my1 = "";
if (isset($_POST['my1'])) {
    $my1 = $_POST['my1'];
}

$my2 = "";
if (isset($_POST['my2'])) {
    $my2 = $_POST['my2'];
}

$my3 = "";
if (isset($_POST['my3'])) {
    $my3 = $_POST['my3'];
}

$my4 = "";
if (isset($_POST['my4'])) {
    $my4 = $_POST['my4'];
}

$my5 = "";
if (isset($_POST['my5'])) {
    $my5 = $_POST['my5'];
}

$my6 = "";
if (isset($_POST['my6'])) {
    $my6 = $_POST['my6'];
}

$my7 = "";
if (isset($_POST['my7'])) {
    $my7 = $_POST['my7'];
}

$my8 = "";
if (isset($_POST['my8'])) {
    $my8 = $_POST['my8'];
}

$my9 = "";
if (isset($_POST['my9'])) {
    $my9 = $_POST['my9'];
}

$mr0 = "";
if (isset($_POST['mr0'])) {
    $mr0 = $_POST['mr0'];
}

$mr1 = "";
if (isset($_POST['mr1'])) {
    $mr1 = $_POST['mr1'];
}

$mr2 = "";
if (isset($_POST['mr2'])) {
    $mr2 = $_POST['mr2'];
}

$mr3 = "";
if (isset($_POST['mr3'])) {
    $mr3 = $_POST['mr3'];
}

$mr4 = "";
if (isset($_POST['mr4'])) {
    $mr4 = $_POST['mr4'];
}

$mr5 = "";
if (isset($_POST['mr5'])) {
    $mr5 = $_POST['mr5'];
}

$mr6 = "";
if (isset($_POST['mr6'])) {
    $mr6 = $_POST['mr6'];
}

$mr7 = "";
if (isset($_POST['mr7'])) {
    $mr7 = $_POST['mr7'];
}

$mr8 = "";
if (isset($_POST['mr8'])) {
    $mr8 = $_POST['mr8'];
}

$mr9 = "";
if (isset($_POST['mr9'])) {
    $mr9 = $_POST['mr9'];
}

$rack1 = "";
if (isset($_POST['rack1'])) {
    $rack1 = $_POST['rack1'];
}

$rack2 = "";
if (isset($_POST['rack2'])) {
    $rack2 = $_POST['rack2'];
}

$rack3 = "";
if (isset($_POST['rack3'])) {
    $rack3 = $_POST['rack3'];
}

$rack4 = "";
if (isset($_POST['rack4'])) {
    $rack4 = $_POST['rack4'];
}

$rack5 = "";
if (isset($_POST['rack5'])) {
    $rack5 = $_POST['rack5'];
}

$rack6 = "";
if (isset($_POST['rack6'])) {
    $rack6 = $_POST['rack6'];
}

$rack7 = "";
if (isset($_POST['rack7'])) {
    $rack7 = $_POST['rack7'];
}

$rack8 = "";
if (isset($_POST['rack8'])) {
    $rack8 = $_POST['rack8'];
}

$rack9 = "";
if (isset($_POST['rack9'])) {
    $rack9 = $_POST['rack9'];
}

$rack10 = "";
if (isset($_POST['rack10'])) {
    $rack10 = $_POST['rack10'];
}

// $evtLog = new EVTLOG($user, "NODE", $act);

$evtLog = new EVENTLOG($user, "IPC ADMINISTRATION", "NODE ADMINISTRATION", $act, $_POST);

//creating obj from class

$nodeObj = new NODE($node);
if ($nodeObj->rslt != SUCCESS) {
    $result['rslt'] = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $evtLog->log($result["rslt"], $result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "query") {
    $nodeObj->query($node);
    $result['rslt']   = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows']   = $nodeObj->rows;
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "queryAll") {
    $nodeObj->queryAll();
    $result['rslt']   = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows']   = $nodeObj->rows;
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "queryRack") {
    $nodeObj->queryRack();
    $result['rslt']   = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows']   = $nodeObj->rows;
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "updateRack") {
    $result = updRacks($rack1, $rack2, $rack3, $rack4, $rack5, $rack6, $rack7, $rack8, $rack9, $rack10, $nodeObj);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "update") {
    $result = updateNode($nodeObj, $userObj, $volt, $temp, $com, $mx0, $mx1, $mx2, $mx3, $mx4, $mx5, $mx6, $mx7, $mx8, $mx9, $my0, $my1, $my2, $my3, $my4, $my5, $my6, $my7, $my8, $my9, $mr0, $mr1, $mr2, $mr3, $mr4, $mr5, $mr6, $mr7, $mr8, $mr9);
    $evtLog->log($result["rslt"], $result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if($act == "START_SCAN_NODE") {
    $result = startScanNode($node, $nodeObj, $userObj);
    $evtLog->log($result["rslt"], $result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if($act == "STOP_SCAN_NODE") {
    $result = stopScanNode($node, $nodeObj, $userObj);
    $evtLog->log($result["rslt"], $result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if($act == "UPDATE_COM") {
    $result = updateCOM($nodeObj, $userObj, $com);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if($act == "CHECK_NODES") {
    $result = checkNodes($nodeObj);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

function updateNode($nodeObj, $userObj, $volt, $temp, $com, $mx0, $mx1, $mx2, $mx3, $mx4, $mx5, $mx6, $mx7, $mx8, $mx9,
$my0, $my1, $my2, $my3, $my4, $my5, $my6, $my7, $my8, $my9, $mr0, $mr1, $mr2, $mr3, $mr4, $mr5, $mr6, $mr7, $mr8, $mr9) {

    // validate $ack: check user permission for acknowledge alarm
    if ($userObj->grpObj->ipcadm != "Y") {
        $result["rslt"] = "fail";
        $result["reason"] = "Permission Denied";
        return $result;
    }
        
    $nodeObj->updateState($volt,$temp, $com);
    $nodeObj->updateMx($mx0, $mx1, $mx2, $mx3, $mx4, $mx5, $mx6, $mx7, $mx8, $mx9);
    $nodeObj->updateMy($my0, $my1, $my2, $my3, $my4, $my5, $my6, $my7, $my8, $my9);
    $nodeObj->updateMr($mr0, $mr1, $mr2, $mr3, $mr4, $mr5, $mr6, $mr7, $mr8, $mr9);   

    $result['rslt']   = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    return $result;
}

function updateCOM($nodeObj, $userObj, $com) {

    // validate $ack: check user permission for acknowledge alarm
    if ($userObj->grpObj->ipcadm != "Y") {
        $result["rslt"] = "fail";
        $result["reason"] = "Permission Denied";
        return $result;
    }
        
    $nodeObj->updateCOM($com);

    $result['rslt']   = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    return $result;
}

function updRacks($rack1, $rack2, $rack3, $rack4, $rack5, $rack6, $rack7, $rack8, $rack9, $rack10, $nodeObj) {

    if ($userObj->grpObj->ipcadm != "Y") {
        $result["rslt"] = "fail";
        $result["reason"] = "Permission Denied";
        return $result;
    }
    
    
    $nodeObj->updRacks($rack1, $rack2, $rack3, $rack4, $rack5, $rack6, $rack7, $rack8, $rack9, $rack10);
    $result['rslt']   = $nodeObj->rslt;
    $result['reason'] = $nodeObj->reason;
    $result['rows']   = $nodeObj->rows;
    return $result;
}
function startScanNode($node, $nodeObj, $userObj){

    // validate $ack: check user permission for acknowledge alarm
    if ($userObj->grpObj->ipcadm != "Y") {
        $result["rslt"] = "fail";
        $result["reason"] = "Permission Denied";
        return $result;
    }
    // // check the process created by www-data
    // if($nodeObj->pid != "") {
    //     if(file_exists("/proc/$nodeObj->pid")){
    //         $result["rslt"] = "fail";
    //         $result["reason"] = "A process is already running";
    //         return $result;
    //     }
    // }

    // all process already running on this node
    exec("ps -ef | grep '\<php hwscan.php $node\>'", $output, $return);
    if($return !==0) {
        $result["rslt"] = "fail";
        $result["reason"] = "Unable to check running processes of server";
        return $result;
    }
    if($output != []) {
        if(checkPid($output, $node)) {
            $result["rslt"] = "fail";
            $result["reason"] = "There is a process already running on this node $node";
            return $result;
        }
    }
   
    // chdir('/var/www/html/v2.1/os/');
    // chdir('/var/www/html/v2.1/workspace/thanh/os/');
    chdir('../os/');
    $command = "php hwscan.php $node > /dev/null 2>&1 & echo $!;";

    $pid = exec($command, $output);
    sleep(1); //delay 1s waiting for exec taking into effect
    if (!file_exists( "/proc/$pid" )){
        $result['rslt']   = 'fail';
        $result['reason'] = "Node '$node' is not being scanned";
        return $result;
    }

    $nodeObj->updateScanPid($pid);
    if($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }
    $nodeObj->queryAll();
    if($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }

    $result['rslt']   = 'success';
    $result['reason'] = "Node '$node' is being scanned";
    $result['rows'] = $nodeObj->rows;
    return $result;
}

function stopScanNode($node, $nodeObj, $userObj){

    // validate $ack: check user permission for acknowledge alarm
    if ($userObj->grpObj->ipcadm != "Y") {
        $result["rslt"] = "fail";
        $result["reason"] = "Permission Denied";
        return $result;
    }

    $pid = $nodeObj->pid;
    $command = "kill -9 $pid";
    exec($command, $output);
    sleep(1);   
    if (file_exists( "/proc/$pid" )){
        $result['rslt']   = 'fail';
        $result['reason'] = "Scan process on $node is not stopped yet";
        return $result;
    }

    $nodeObj->updateScanPid();
    if($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }
    $nodeObj->queryAll();
    if($nodeObj->rslt == 'fail') {
        $result['rslt'] = $nodeObj->rslt;
        $result['reason'] = $nodeObj->reason;
        return $result;
    }

    $result['rslt']   = 'success';
    $result['reason'] = "Scan process on node '$node' is stopped";
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

function checkPid($processList, $node) {
    for($i=0; $i<count($processList); $i++) {
        $processArray = preg_split("/[\s]+/", $processList[$i], 8);
        $cmd = $processArray[7];
        $pid = $processArray[1];
        if($cmd === "php hwscan.php $node"){
            if(file_exists("/proc/$pid")){
                return true;
            }
        }
    }
    return false;
}


?>