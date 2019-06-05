<?php

/* Initialize expected inputs */

  
$act = "";
if (isset($_POST['act']))
    $act = $_POST['act'];

$id   ="";
if(isset($_POST['id']))
    $id = $_POST['id'];

$paraArray = "";
if(isset($_POST['paraArray'])){
    $paraArray = $_POST['paraArray'];
}

$paraValueArray = "";
if(isset($_POST['paraValueArray'])){
    $paraValueArray = $_POST['paraValueArray'];
}

$fileName = '';
$fileContent = '';
if (isset($_FILES['file']['tmp_name'])) {
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])) 
    { 
        $fileName = $_FILES["file"]["name"];
        $fileContent = file_get_contents($_FILES['file']['tmp_name']); 
    }
}

$evtLog = new EVENTLOG($user, "PROVISIONING", "BATCH EXECUTION", $act, $_POST);


///////////////--------------Dispatch------------------//////////////
if ($act == 'QUERY')
{
    $result = queryBatch($filename);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
else if ($act == "ADD") {
    $result = addBatch($userObj, $fileName, $fileContent);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
else if ($act == "DELETE") {
    $result = delBatch($id, $userObj);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
else if ($act == "QUERYBATS"){
    $result = queryBats($id);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
else {
    $result["rslt"] = FAIL;
    $result["reason"] = "This action is under development!";
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

function addBatch($userObj, $fileName, $fileContent) {

    if ($userObj->grpObj->portmap != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    $batchObj = new BATCH();
    $batchObj->addBatch($userObj->uname, $fileName, $fileContent);
    if($batchObj->rslt == FAIL) {
        $result['rslt'] = $batchObj->rslt;
        $result['reason'] = $batchObj->reason;
        return $result;
    }

    $result['rows'] = $batchObj->rows;
    $result["rslt"] = SUCCESS;
    $result["reason"] = "BATCH_ADD_SUCCESS";
    
    return $result;
}

function delBatch($id, $userObj) {

    if ($userObj->grpObj->portmap != "Y") {
        $result['rslt'] = 'fail';
        $result['reason'] = 'Permission Denied';
        return $result;
    }

    $batchObj = new BATCH();
    $batchObj->deleteBatch($id);
    if($batchObj->rslt == FAIL) {
        $result['rslt'] = $batchObj->rslt;
        $result['reason'] = $batchObj->reason;
        return $result;
    }

    $result['rows'] = $batchObj->rows;
    $result["rslt"] = SUCCESS;
    $result["reason"] = "BATCH_DELETE_SUCCESS";
    
    return $result;
}

function queryBatch($filename) {
    $batchObj = new BATCH();
    $batchObj->queryBatch($filename);
    if($batchObj->rslt == FAIL) {
        $result['rslt'] = $batchObj->rslt;
        $result['reason'] = $batchObj->reason;
        return $result;
    }

    $result['rows'] = $batchObj->rows;
    $result["rslt"] = SUCCESS;
    $result["reason"] = "BATCH_QUERY_SUCCESS";
    return $result;
}

function queryBats($id) {

    $batchObj = new BATCH();
    $batchObj->queryBats($id);
    if($batchObj->rslt == FAIL) {
        $result['rslt'] = $batchObj->rslt;
        $result['reason'] = $batchObj->reason;
        return $result;
    }

    $result['rows'] = $batchObj->rows;
    $result["rslt"] = SUCCESS;
    $result["reason"] = "BATS_QUERY_SUCCESS";
    return $result;
}


?>