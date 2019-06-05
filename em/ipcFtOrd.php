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

$ctid = "";
if (isset($_POST['ctid'])) {
    $ctid = $_POST['ctid'];
}

$act = "";
if (isset($_POST['act'])) {
    $act = $_POST['act'];
}

$cls = "";
if (isset($_POST['cls'])) {
    $cls = $_POST['cls'];
}

$ctyp = "";
if (isset($_POST['ctyp'])) {
    $ctyp = $_POST['ctyp'];
}

$ffacid = "";
if (isset($_POST['ffacid'])) {
    $ffacid = $_POST['ffacid'];
}

$tfacid = "";
if (isset($_POST['tfacid'])) {
    $tfacid = $_POST['tfacid'];
}

$op = "";
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

// dispatch to functions

if ($act == "") {
    $result = queryOrd($ordno);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "queryOrd") {
    $result = queryOrd($ordno);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "queryCkt") {
    $result = queryCkt($ordno);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "queryFac") {
    $result = queryFac($ctid, $ordno);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "PROCESS_CONNECTION") {
    $result = processConnection($ordno, $mlo, $ctid, $cls, $adsr, $prot, $ctyp, $ffacid, $tfacid, $op);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "PROCESS_ORD") {
    $result = processOrd($ordno);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "PROCESS_CKT") {
    $result = processCkt($ordno, $ctid);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "FETCH_FT_ORDERS") {
    try {
        $result = fetchFtOrders();
    } catch (Throwable $e) {
        $res['rslt'] = 'fetch ft orders fail';
        $res['reason'] = $e->getMessage();
        echo json_encode($res);
        mysqli_close($db);
        return;
    }
    echo json_encode($result);
    mysqli_close($db);
    return;
}

// POST functions for operation
function postOP($ordno, $newCls, $ctyp, $action, $ckid, $ffac, $tfac) {
    $url = "http://localhost/target-1/UPDATE/em/ipcDispatch.php";
    $data = array('api' => 'ipcProv', 'user' => 'ninh','act' => $action, 'ordno' => $ordno, 'mlo'=> $mlo , 'ckid' => $ckid, 'cls' => $newCls, 'adsr'=> $adsr, 'prot'=> $prot, 'ctyp' => $ctyp, 'ffac' => $ffac, 'tfac' => $tfac);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $response = json_decode(file_get_contents($url, false, $context));
    return $response;
}

// takes inputs from POST, translates to send POST to ipcProv with required data to
// disconnect or connect based on $op

// Process all Orders
function processOrd($ordno) {
    $ftordersObj = new FTORDERS($ordno);

    if ($ftordersObj->ord['STAT'] !== 'RELEASED') {
        $result['rslt'] = FAIL;
        $result['reason'] = 'ORDER IS NOT RELEASED';
        return $result;
    }
    
    $newCls = "";

    foreach ($ftordersObj->ckts as $ckt) {
        if ($ckt['cls'] == "R") {
            $newCls = "RES";
        }
        else if ($ckt['cls'] == "B") {
            $newCls = "BUS";
        }

        // hardcoded ctyp for now
        $ctyp = "GEN";
        $ckid = $ckt['ctid'];
        
        $ckcons = array_filter($ftordersObj->ckcons, function($v) use ($ckid) {
            return $v['ctid'] == $ckid;
        });
        
        foreach($ckcons as $ckcon) {
            $ffac = $ckcon['ffacid'];
            $tfac = $ckcon['tfacid'];
            $op = $ckcon['op'];

            if ($op == "IN") {
                $action = 'CONNECT';
                $response = postOP($ordno, $newCls, $ctyp, $action, $ckid, $ffac, $tfac);

                if ($response->rslt == 'fail') {
                    return $response;
                }
            }
            else {
                $action = 'DISCONNECT';
                $response = postOP($ordno, $newCls, $ctyp, $action, $ckid, $ffac, $tfac);

                if ($response->rslt == 'fail') {
                    return $response;
                }
            }
        }
    }

    $result['rslt'] = SUCCESS;
    $result['reason'] = 'ORDERS PROCESSED';
    return $result;
}

function processCkt($ordno, $ctid) {
    $ftordersObj = new FTORDERS($ordno);
    $newCls = "";

    $ckts = array_filter($ftordersObj->ckts, function($v) use ($ctid) {
        return $v['ctid'] == $ctid;
    });

    foreach ($ckts as $ckt) {
        if ($ckt['cls'] == "R") {
            $newCls = "RES";
        } else if ($ckt['cls'] == "B") {
            $newCls = "BUS";
        }
    
        // hardcoded ctyp for now
        $ctyp = "GEN";
        $ckid = $ckt['ctid'];
    
        $ckcons = array_filter($ftordersObj->ckcons, function($v) use ($ckid) {
            return $v['ctid'] == $ckid;
        });
    
        foreach($ckcons as $ckcon) {
            $ffac = $ckcon['ffacid'];
            $tfac = $ckcon['tfacid'];
            $op = $ckcon['op'];
    
            if ($op == "IN") {
                $action = 'CONNECT';
                $response = postOP($ordno, $newCls, $ctyp, $action, $ckid, $ffac, $tfac);
    
                if ($response->rslt == 'fail') {
                    return $response;
                }
            } else {
                $action = 'DISCONNECT';
                $response = postOP($ordno, $newCls, $ctyp, $action, $ckid, $ffac, $tfac);
    
                if ($response->rslt == 'fail') {
                    return $response;
                }
            }
        }
    }

    $result['rslt'] = SUCCESS;
    $result['reason'] = 'CKTS PROCESSED';
    return $result;
}

function processConnection($ordno, $mlo, $ctid, $cls, $adsr, $prot, $ctyp, $ffacid, $tfacid, $op) {

    $ftordersObj = new FTORDERS($ordno);
    $newCls = "";

    if ($cls == "R") {
        $newCls = "RES";
    }
    else if ($cls == "B") {
        $newCls = "BUS";
    }

    // hardcoded ctyp for now
    $ctyp = "GEN";
    $ckid = $ctid;

    $ffac = $ffacid;
    $tfac = $tfacid;

    if ($op == "IN") {
        $action = 'CONNECT';
        $response = postOP($ordno, $newCls, $ctyp, $action, $ckid, $ffac, $tfac);

        return $response;
    }
    else {
        $action = 'DISCONNECT';
        $response = postOP($ordno, $newCls, $ctyp, $action, $ckid, $ffac, $tfac);

        return $response;
    }
}


function queryOrd($ordno) {

    $ordObj = new ORD($ordno);
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

function queryCkt($ordno) {

    $ftcktObj = new FTCKT();
    $ftcktObj->queryCkt($ordno);
    if ($ftcktObj->rslt == FAIL) {
        $result["rslt"] = FAIL;
        $result["reason"] = $ftcktObj->reason;
        return $result;
    }
    $result["rslt"]   = SUCCESS;
    $result["reason"] = "queryCkt_success";
    $result['rows']   = $ftcktObj->rows;
    return $result;

}

function queryFac($ctid, $ordno) {

    $ftckconObj = new FTCKCON($ctid, $ordno);
    if ($ftckconObj->rslt == FAIL) {
        $result["rslt"]    = FAIL;
        $result["reason"]  = $ftckconObj->reason;
        return $result;
    }
    $result["rslt"]   = SUCCESS;
    $result["reason"] = "queryFac_success";
    $result['rows']   = $ftckconObj->rows;
    return $result;

}

function fetchFtOrders() {

    $url = __DIR__ . "/../../FTORDERS/";
    try {
        $iterator = new DirectoryIterator($url);
    } catch (Throwable $e) {
        $result['rslt'] = 'iterator fail';
        $result['reason'] = $e->getMessage();
        return $result;
    }

    $files = array();
    try {
        while ($iterator->valid()) {
            if ($iterator->isFile() && substr($iterator->getPathName(), -4) == '.txt') {
                $files[] = $iterator->getPathName();
            }
            $iterator->next();
        }
    } catch (Throwable $e) {
        $result['rslt'] = 'while iterator fail';
        $result['reason'] = $e->getMessage();
        return $result;
    }

    foreach ($files as $file) {

        $ctString = file_get_contents($file);
        $ctString = substr($ctString, 0, -1);

        $fomsObj = new FOMS($ctString);
        if ($fomsObj->rslt != SUCCESS) {
            $result['rslt'] = $fomsObj->rslt;
            $result['reason'] = $fomsObj->reason;
            return $result;
        }

        // $result['foms'] = $fomsObj->ckts;
        // $result['string'] = $fomsObj->string;
        // $result['ctstring'] = $fomsObj->ctString;
        // $result['rslt'] = $fomsObj->rslt;
        // $result['reason'] = $fomsObj->reason;

        $fomsObj->createOrd();
        if ($fomsObj->rslt == 'fail') {
            $result['rslt'] = $fomsObj->rslt;
            $result['reason'] = $fomsObj->reason;
            return $result;
        }

        $fomsObj->createCkt();
        if ($fomsObj->rslt == 'fail') {
            $result['rslt'] = $fomsObj->rslt;
            $result['reason'] = $fomsObj->reason;
            return $result;
        }

        $fomsObj->createOperations();
        if ($fomsObj->rslt == 'fail') {
            $result['rslt'] = $fomsObj->rslt;
            $result['reason'] = $fomsObj->reason;
            return $result;
        }

    }

    $result['rslt'] = SUCCESS;
    $result['reason'] = "FT ORDER ADDED INTO DATABASE";
    $result['rows'] = 1;
    return $result;
}


?>
