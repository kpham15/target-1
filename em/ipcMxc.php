<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Filename: ipcFac.php
 * Change history: 
 * 2018-12-28: created (Thanh)
 */
    /* Initialize expected inputs */

$act = "";
if (isset($_POST['act'])) {
    $act = $_POST['act'];
}

$id = "";
if (isset($_POST['id'])) {    
    $id = $_POST['id'];
}

$node = "";
if (isset($_POST['node'])) {
    $node = $_POST['node'];
}

$shelf = "";
if (isset($_POST['shelf'])) {
    $shelf = $_POST['shelf'];
}

$slot = "";
if (isset($_POST['slot'])) {
    $slot = $_POST['slot'];
}

$type = "";
if (isset($_POST['type'])) {
    $type = $_POST['type'];
}

$stat = "";
if (isset($_POST['stat'])) {
    $stat = $_POST['stat'];
}

$slotBitmask = "";
if (isset($_POST['slotBitmask'])) {
    $slotBitmask = $_POST['slotBitmask'];
}

// $evtLog = new EVTLOG($user, "MXC", $act);

$evtLog = new EVENTLOG($user, "MAINTENANCE", "LOCK/UNLOCK MATRIX CARD", $act, '');


/* Dispatch to functions */

if ($act  ==  "query") {
    $result = query($node, $shelf, $slot, $type, $stat);
    // $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "queryMatrix") {
    $mxcObj = new MXC();
    $mxcObj->queryByNode($node);
    $result['rslt']=$mxcObj->rslt;
    $result['reason']=$mxcObj->reason;
    $result['rows']=$mxcObj->rows;
    // $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act  ==  "lck") {
    $result = lckMatrix($node, $shelf, $slot, $type, $userObj);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "lck_node") {
    $result = lckNode($node, $userObj);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;

}

if ($act  ==  "unlck") {
    $result = unlckMatrix($node, $shelf, $slot, $type, $userObj);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act  ==  "unlck_node") {
    $result = unlckNode($node, $userObj);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "insert") {
    $result = insertMxc($node, $shelf, $slot, $type);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "remove") {
    $result = removeMxc($node, $shelf, $slot, $type);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "updateNodeMxc") {
    $result = updateNodeMxc($slotBitmask, $node);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "VIEW_PORT_STATUS") {
    $result = queryPortStat($node, $shelf, $slot, $type, $stat);
    // $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

    
else {
    $result["rslt"] = "fail";
    $result["reason"] = "ACTION " . $act . " is under development or not supported";
    // $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

           
/* Function section */
function query($node, $shelf, $slot, $type, $stat) {
   
    $mxcObj = new MXC();
    $mxcObj->queryByNodeSlotTypeStat($node, $shelf, $slot, $type, $stat);
    if ($mxcObj->rslt == FAIL) {
        $result["rslt"] = FAIL;
        $result["reason"] = "INVALID_MXC";
        return $result;
    }

    $result['rows'] = $mxcObj->rows;
    $result["rslt"] = SUCCESS;
    $result["reason"] = "MXC_QUERY_SUCCESS";
    return $result;
}

function queryMatrix($node) {
    $mxcObj = new MXC();
    $mxcObj->queryByNode($node);
    if ($mxcObj->rslt == FAIL) {
        $result["rslt"] = FAIL;
        $result["reason"] = "INVALID_MXC";
        return $result;
    }

    $result['rows'] = $mxcObj->rows;
    $result["rslt"] = SUCCESS;
    $result["reason"] = "MXC_QUERY_SUCCESS";
    return $result;
}

function queryPortStat($node, $shelf, $slot, $type, $stat) {

    $pnum = "";
    $ptyp = "";
    if ($type === "MRE") {
        $result["rslt"] = "fail";
        $result["reason"] = "MRE CARD DOES NOT HAVE PORT STATUS";
        return $result;    
    }
    if ($type === "") {
        if ($shelf === "1") {
            $ptyp = "X";
        }
        else if ($shelf == "2") {
            $ptyp = "Y";
        }
    }
    else if ($type === "MIOX") {
        if ($shelf == "1") {
            $ptyp = "X";
        }
        else {
            $result["rslt"] = "fail";
            $result["reason"] = "MIOX CARD DOES NOT EXIST ON SHELF 2";
            return $result;    
        }
    }
    else if ($type === "MIOY") {
        if ($shelf == "2") {
            $ptyp = "Y";
        }
        else {
            $result["rslt"] = "fail";
            $result["reason"] = "MIOY CARD DOES NOT EXIST ON  SHELF 1";
            return $result;    
        }
    }
    $portObj = new PORT();
    $portObj->queryPort($node, $slot, "", $ptyp, $stat);
    $result['rslt']   = $portObj->rslt;
    $result['reason'] = $portObj->reason;
    $result['rows']   = $portObj->rows;
    return $result;
}

function lckNode($node, $userObj) {
    $reason = "";
    $rslt = SUCCESS;

    // if node is locked, dont take action
    $nodeObj = new NODE($node);
    if ($nodeObj->rslt == 'fail') {
        $result['rslt'] = 'fail';
        $result['reason'] = $nodeObj->reason;
    }

    if ($nodeObj->stat == 'LCK') {
        $result['rslt'] = 'fail';
        $result['reason'] = 'NODE ' . $node . ' IS ALREADY LOCKED';
        return $result;
    }

    // ----------------------- //

    for ($i = 1; $i <= 10; $i++) {
        $result = lckMatrix($node, 1, $i, 'MIOX', $userObj);
        $reason .= "$node-MIOX-$i: " . $result['reason'] . "<br>";

        $result = lckMatrix($node, 2, $i, 'MIOY', $userObj);
        $reason .= "$node-MIOY-$i: " . $result['reason'] . "<br>";
    }

    // get fname and lname to pass into lcknode
    $user = $userObj->fname . " " . $userObj->lname;
    $nodeObj->lockNode($user);
    
    $result["rslt"]   = $rslt;
    $result["reason"] = $reason;
    return $result;
    
}

function unlckNode($node, $userObj) {
    $reason = "";
    $rslt = SUCCESS;

    $nodeObj = new NODE($node);
    if ($nodeObj->rslt == 'fail') {
        $result['rslt'] = 'fail';
        $result['reason'] = $nodeObj->reason;
    }

    // if node status is not locked, do not unlock
    if ($nodeObj->stat !== 'LCK') {
        $result['rslt'] = 'fail';
        $result['reason'] = 'NODE ' . $node . ' IS NOT LOCKED';
        return $result;
    }

    // get fname and lname to pass into lcknode
    $user = $userObj->fname . " " . $userObj->lname;
    // update status in t_nodes
    $nodeObj->unlockNode($user);

    // loop through all nodes and unlock matrix cards in that node
    for ($i = 1; $i <= 10; $i++) {
        $result = unlckMatrix($node, 1, $i, 'MIOX', $userObj);
        $reason .= "$node-MIOX-$i: " . $result['reason'] . "<br>";

        $result = unlckMatrix($node, 2, $i, 'MIOY', $userObj);
        $reason .= "$node-MIOY-$i: " . $result['reason'] . "<br>";
    }

    $result["rslt"]   = $rslt;
    $result["reason"] = $reason;
    return $result;
    
}

function lckMatrix($node, $shelf, $slot, $type, $userObj) {

    if ($userObj->grpObj->mtxcard != "Y") {
        $result["rslt"] = "fail";
        $result["reason"] = "Permission Denied";
        return $result;
    }

    $evt = "MC_LOCK";

    // deny action of card on locked node
    $nodeObj = new NODE($node);
    if ($nodeObj->stat == 'INS') {

        $mxcObj = new MXC($node, $shelf, $slot, $type);
        if ($mxcObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result["reason"] = $mxcObj->reason;
            return $result;
        }

        $mxcObj->lockMxc();
        if ($mxcObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result["reason"] = $mxcObj->reason;
            return $result;
        }

        // creates array that includes node, slot, type, psta
        $rows['node'] = $mxcObj->node;
        $rows['slot'] = $mxcObj->slot;
        $rows['type'] = $mxcObj->type;
        $rows['psta'] = $mxcObj->psta;

        // return in rows[node, slot, type, psta] instead of empty rows
        $result['rows'] = $rows;
        $result["rslt"] = SUCCESS;
        $result["reason"] = "LOCK_MXC_SUCCESS";
        return $result;
    }
    else {
        $result['rslt'] = 'fail';
        $result['reason'] = 'DENIED - NODE (' . $node . ') IS ' . $nodeObj->stat;
        return $result;
    }
}


function unlckMatrix($node, $shelf, $slot, $type, $userObj) {

    $evt = "MC_UNLOCK";
   
    // check user's permission
    if ($userObj->grpObj->mtxcard != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    // deny action of card on locked node
    $nodeObj = new NODE($node);
    if ($nodeObj->stat == 'INS') {
        

        $mxcObj = new MXC($node, $shelf, $slot, $type);
        if ($mxcObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result["reason"] = "INVALID_MXC";
            return $result;
        }

        $mxcObj->unlockMxc();
        if ($mxcObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result["reason"] = $mxcObj->reason;
            return $result;
        }

        // creates array that includes node, slot, type, psta
        $rows['node'] = $mxcObj->node;
        $rows['slot'] = $mxcObj->slot;
        $rows['type'] = $mxcObj->type;
        $rows['psta'] = $mxcObj->psta;

        // return in rows[node, slot, type, psta] instead of empty rows
        $result['rows'] = $rows;
        $result["rslt"] = SUCCESS;
        $result["reason"] = "UNLOCK_MXC_SUCCESS";
        return $result;
    }
    else {
        $result['rslt'] = 'fail';
        $result['reason'] = 'DENIED - NODE (' . $node . ') IS ' . $nodeObj->stat;
        return $result;
    }
}

function insertMxc($node, $shelf, $slot, $type) {

    $evt = "MC_IN";
   
    $mxcObj = new MXC($node, $shelf, $slot, $type);
    if ($mxcObj->rslt == FAIL) {
        $result["rslt"] = FAIL;
        $result["reason"] = "INVALID_MXC";
        return $result;
    }

    $mxcObj->inserted();
    if ($mxcObj->rslt == FAIL) {
        $result["rslt"] = FAIL;
        $result["reason"] = $mxcObj->reason;
        return $result;
    }

    $result["rslt"] = SUCCESS;
    $result["reason"] = "INSERT_MXC_SUCCESS";

    // report alarm SYS-CLR
    $almid = $node . '-' . $type . '-' . $slot;
    $almObj = new ALMS($almid);
    $almtype = 'EQUIP';
    $remark = ': MATRIX CARD INSERTED';    
    $almObj->sysClr($almid, $remark);

    return $result;
}

function removeMxc($node, $shelf, $slot, $type) {
    $evt = "MC_OUT";
   
    $mxcObj = new MXC($node, $shelf, $slot, $type);
    if ($mxcObj->rslt == FAIL) {
        $result["rslt"] = FAIL;
        $result["reason"] = "INVALID_MXC";
        return $result;
    }

    
    $mxcObj->removed();
    if ($mxcObj->rslt == FAIL) {
        $result["rslt"] = FAIL;
        $result["reason"] = $mxcObj->reason;
        return $result;
    }

    $result["rslt"] = SUCCESS;
    $result["reason"] = "REMOVE_MXC_SUCCESS";
    
    // set alarm source and type
    $src = 'MATRIX CARD';
    $almtype = 'EQUIP';

    // checks for existing path in t_stg
    $card = ($node - 1) . '.' . $type . '.' . ($slot - 1);


    $stgObj = new Stgs();
    $stgObj->queryForExistPath($card);

    // card is removed while there is an existing path
    if (count($stgObj->rows) > 0) {
        $cond = 'REMOVED, CROSS CONNECT';
        $sev = 'CRI';
        $sa = 'Y';
    }
    else {
        // if card is locked and card is pulled
        if ($mxcObj->ssta == 'LCK') {
            $cond = 'REMOVED, LOCKED, NO CROSS-CONNECT';
            $sev = 'MAJ';
            $sa = 'N';
        }
        // if card is unlocked and card is pulled
        else {
            $cond = 'REMOVED, UNLOCKED, NO CROSS-CONNECT';
            $sev = 'CRI';
            $sa = 'N';
        }
    }


    $almid = $node . '-' . $type . '-' . $slot;
    $remark = ': MATRIX CARD REMOVED';
    
    $almObj = new ALMS();
    $almObj->newAlm($almid, $src, $almtype, $cond, $sev, $sa, $remark);
        
    return $result;
}

function updateNodeMxc($slotBitmask, $node) {

    // int to insert all cards = 4294967295
    // converts int to binary
    // adds 0 to left until length is 32
    // separates binary into array
    $int = intval($slotBitmask);
    $decbin = decbin($int);
    $hwStatus = str_pad($decbin, 32, "0", STR_PAD_LEFT);
    $cardStatus = str_split($hwStatus);
    
    // set values according to their position in array
    
    for ($i = 1; $i < (count($cardStatus)+1); $i++) {
        if ($i < 11) {
            // $node = 1;
            $shelf = 1;
            $slot = $i;
            $type = 'MIOX';
        }
        else if (($i == 11) || ($i == 27)) {
            continue;
        }
        else if (($i > 11) && ($i < 17)) {
            // $node = 1;
            $shelf = 1;
            $slot = $i;
            $type = 'MRE';
        }
        else if (($i > 16) && ($i < 27)) {
            // $node = 1;
            $shelf = 2;
            $slot = $i-16;
            $type = 'MIOY';
        }
        else if ($i > 27) {
            // $node = 1;
            $shelf = 2;
            $slot = $i-16;
            $type = 'MRE';
        }
        
        // call insertMxc() or removeMxc() based on if cardStatus is 0 or 1
        if ($cardStatus[$i-1] == 0) {                   
            $result = removeMxc($node, $shelf, $slot, $type);
            $row = "$node-$type-$slot: " .$result['reason'];
            $rows[] = $row;
        }
        else {
            $result = insertMxc($node, $shelf, $slot, $type);
            $row = "$node-$type-$slot: " .$result['reason'];
            $rows[] = $row;
        }

    }

    $result["rslt"]   = SUCCESS;
    $result["reason"] = "GET_MXC_HW UPDATE SUCCESS";
    $result["rows"] = $rows;
    return $result;
}

?>