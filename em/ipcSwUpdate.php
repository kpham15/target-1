<?php

/* Initialize expected inputs */
  
$act = "";
if (isset($_POST['act']))
    $act = $_POST['act'];

$fileName = '';
if (isset($_FILES['file']['tmp_name'])) {
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])) 
    { 
        $fileName = $_FILES["file"]["name"];
    }
}

// $evtLog = new Evtlog($user, "SWUPD", $act);

$evtLog = new EVENTLOG($user, "IPC ADMINISTRATION", "UPDATE SOFTWARE RELEASES", $act, $_POST);



$updateDir = '../../UPDATE';
$defaultDir = '../../DEFAULT';
$currentDir = '../../CURRENT';
$uploadDir = '../../UPLOAD';
$runningDir = '';

///////////////--------------Dispatch------------------//////////////
if ($act == "UPLOAD SW") {
    $result = uploadSw($userObj, $fileName, $uploadDir);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}

if ($act == "INSTALL UPDATE SW") {
    $result = installUpdateSw($userObj, $updateDir, $uploadDir);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
if ($act == "RUN CURRENT SW") {
    $result = runCurrentSw($userObj, $currentDir, $updateDir, $defaultDir, $dir);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
if ($act == "RUN UPDATE SW") {
    $result = runUpdateSw($userObj, $currentDir, $updateDir, $defaultDir, $dir);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
if ($act == "RUN DEFAULT SW") {
    $result = runDefaultSw($userObj, $currentDir, $updateDir, $defaultDir, $dir);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
if ($act == "APPLY UPDATE SW") {
    $result = applyUpdateSw($userObj, $updateDir, $currentDir, $defaultDir);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
if ($act == "APPLY DEFAULT SW") {
    $result = applyDefaultSw($userObj, $updateDir, $currentDir, $defaultDir);
    $evtLog->log($result["rslt"],$result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    return;
}
if ($act == "SET DEFAULT SW") {
    $result = setDefaultSw($userObj, $updateDir, $currentDir, $defaultDir);
    $evtLog->log($result["rslt"],$result["reason"]);
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



function uploadSw($userObj, $fileName, $uploadDir) {

    try {
        if ($userObj->grpObj->swupd != "Y") {
            throw new Exception('Permission Denied');
        }
    
        if ($_FILES["file"]["error"] > 0 || $fileName === "") {
            throw new Exception("Error: " . $_FILES["file"]["error"]); 
        } 
    
        if ($uploadDir ==="") {
            throw new Exception("NO_UPDATE_FOLDER_INFORMATION"); 
        }
    
        if (!file_exists($uploadDir)) {
            // // if(!mkdir($updateDir, 0755, true)){
            if(!mkdir($uploadDir, 0777, true)){
                throw new Exception("UNABLE_CREATE_FOLDER_UPLOAD"); 
            }
            
        }
        exec("rm -rf ".$uploadDir.'/*', $output, $return);
        if($return !== 0) {
            throw new Exception("UNABLE_REMOVE_FILES_IN_FOLDER_UPLOAD");
        }

        if(!move_uploaded_file($_FILES["file"]["tmp_name"],$uploadDir.'/'.$fileName)) {
            throw new Exception("UNABLE_TO_MOVE_SW_FILE"); 
        }

        // exec("chmod 755  ".$uploadDir, $output, $return);
        exec("chmod -R 777  ".$uploadDir, $output, $return);
        if($return !== 0) {
            throw new Exception("UNABLE_CONFIGURE_PERMISSION_FOLDER_UPLOAD");
        }

        $result["rslt"] = 'success';
        $result["reason"] = "SW_UPLOADED";
        
        return $result;
    }
    catch(Exception $e) {
        $result["rslt"] = 'fail';
        $result["reason"] = $e->getMessage();
        return $result;
    }
    
}

function installUpdateSw($userObj, $updateDir, $uploadDir) {

    try {
        if ($userObj->grpObj->swupd != "Y") {
            throw new Exception('Permission Denied');
        }
        // Step: 
        //     Check folder exist or not
        //     Delete everything inside folder 
        //     Move new files into folder 
        //     Change permission of new files inside folder

        ////////////////------------check folder UPLOAD------------/////////////
        if ($uploadDir ==="") {
            throw new Exception("NO_UPLOAD_FOLDER_INFORMATION");
        }
    
        if (!file_exists($uploadDir)) {
            throw new Exception('UPLOAD_FOLDER_NOT_EXIST');
        }
        //check if there are more than 1 zip file in there
        $fileList = glob("{$uploadDir}/*");
        if(count($fileList) !== 1) {
            throw new Exception('NEW_VERSION_NOT_FOUND');
        }

        ////////////////------------check folder UPDATE------------/////////////
        if ($updateDir ==="") {
            throw new Exception("NO_UPDATE_FOLDER_INFORMATION");
        }
    
        if (!file_exists($updateDir)) {
            // if(!mkdir($updateDir, 0755, true)){
            if(!mkdir($updateDir, 0777, true)){
                throw new Exception("FOLDER_FOR_UPDATESW_NOT_CREATED");
            }
        }

        ///clear content inside the folder UPDATE
        exec("rm -rf ".$updateDir."/*", $output, $return);
        if($return !== 0) {
            throw new Exception("UNABLE_REMOVE_FILES_IN_FOLDER_UPDATE");
        }
        
        ///////////////----------Process the zip file----------///////////////
        $zipFile = $fileList[0];

        exec("unzip ".$zipFile." -d ".$updateDir,$output, $return);
        if($return !== 0) {
            throw new Exception('UNABLE_UNZIP_NEW_SW');
        }

        // exec("chmod 755 ".$updateDir, $output, $return);
        exec("chmod -R 777 ".$updateDir, $output, $return);
        if($return !== 0) {
            throw new Exception('UNABLE_CHANGE_PERMISSION_UPDATE_FOLDER');
        }
    
        $result['rslt'] = 'success';
        $result['reason'] = 'NEW_VERSION_INSTALLED';
        return $result;
    }
    catch(Exception $e) {
        $result["rslt"] = 'fail';
        $result["reason"] = $e->getMessage();
        return $result;
    }
   

}

function runCurrentSw($userObj, $currentDir, $updateDir, $defaultDir, $dir) {

    try {
        if ($userObj->grpObj->swupd != "Y") {
            throw new Exception('Permission Denied');
        }
        // Step: 
        //     Check folder exist or not
        //     Delete everything inside folder 
        //     Move new files into folder 
        //     Change permission of new files inside folder
        ////////////////------------check folder CURRENT------------/////////////
        if ($currentDir ==="") {
            throw new Exception("NO_CURRENT_FOLDER_INFORMATION");
        }
    
        if (!file_exists($currentDir)) {
            throw new Exception("FOLDER_CURRENTSW_NOT_EXISTED");
        }
       
        $fileList = glob("{$currentDir}/*");
        if(count($fileList) == 0) {
            throw new Exception('CURRENT_FOLDER_IS_EMPTY');
        }

        if(!changeDir('CURRENT')) {
            throw new Exception('CFG_FILE_UNWRITABLE');
        }

        $result['rslt'] = 'success';
        $result['reason'] = 'CURRENT_FOLDER_ACTIVE';
        return $result;
    }
    catch(Exception $e) {
        $result["rslt"] = 'fail';
        $result["reason"] = $e->getMessage();
        return $result;
    }
   

}

function runUpdateSw($userObj, $currentDir, $updateDir, $defaultDir, $dir) {

    try {
        if ($userObj->grpObj->swupd != "Y") {
            throw new Exception('Permission Denied');
        }

        // Step: 
        //     Check folder exist or not
        //     Delete everything inside folder 
        //     Move new files into folder 
        //     Change permission of new files inside folder
        ////////////////------------check folder UPDATE------------/////////////
        if ($updateDir ==="") {
            throw new Exception("NO_UPDATE_FOLDER_INFORMATION");
        }

        if (!file_exists($updateDir)) {
            throw new Exception("FOLDER_UPDATE_NOT_EXISTED");
        }
       
        $fileList = glob("{$updateDir}/*");
        if(count($fileList) == 0) {
            throw new Exception('UPDATE_FOLDER_IS_EMPTY');
        }

        if(!changeDir('UPDATE')) {
            throw new Exception('CFG_FILE_UNWRITABLE');
        }

        $result['rslt'] = 'success';
        $result['reason'] = 'UPDATE_FOLDER_ACTIVE';
        return $result;
    }
    catch(Exception $e) {
        $result["rslt"] = 'fail';
        $result["reason"] = $e->getMessage();
        return $result;
    }
}

function runDefaultSw($userObj, $currentDir, $updateDir, $defaultDir, $dir) {
    try {
        if ($userObj->grpObj->swupd != "Y") {
            throw new Exception('Permission Denied');
        }
        // Step: 
        //     Check folder exist or not
        //     Delete everything inside folder 
        //     Move new files into folder 
        //     Change permission of new files inside folder
        ////////////////------------check folder DEFAULT------------/////////////
        if ($defaultDir ==="") {
            throw new Exception("NO_DEFAULT_FOLDER_INFORMATION");
        }
    
        if (!file_exists($defaultDir)) {
            throw new Exception("FOLDER_DEFAULT_NOT_EXISTED");
        }
    
        $fileList = glob("{$defaultDir}/*");
        if(count($fileList) == 0) {
            throw new Exception('DEFAULT_FOLDER_IS_EMPTY');
        }

        if(!changeDir('DEFAULT')) {
            throw new Exception('CFG_FILE_UNWRITABLE');
        }

        $result['rslt'] = 'success';
        $result['reason'] = 'DEFAULT_FOLDER_ACTIVE';
        return $result;
    }
    catch(Exception $e) {
        $result["rslt"] = 'fail';
        $result["reason"] = $e->getMessage();
        return $result;
    }
}

function applyUpdateSw($userObj, $updateDir, $currentDir, $defaultDir) {
 
    try {
        if ($userObj->grpObj->swupd != "Y") {
            throw new Exception('Permission Denied');
        }
        // Step: 
        //     Check folder exist or not
        //     Delete everything inside folder 
        //     Move new files into folder 
        //     Change permission of new files inside folder
        ////////////////------------check folder UPDATE------------/////////////
        if ($updateDir ==="") {
            throw new Exception("NO_UPDATE_FOLDER_INFORMATION");
        }
    
        if (!file_exists($updateDir)) {
            throw new Exception("FOLDER_FOR_UPDATESW_NOT_CREATED");
        }
    
        $fileList = glob("{$updateDir}/*");
        if(count($fileList) == 0) {
            throw new Exception('UPDATE_FOLDER_EMPTY');
        }
        ////////////////------------check folder CURRENT------------/////////////
        if ($currentDir ==="") {
            throw new Exception("NO_CURRENT_FOLDER_INFORMATION");
        }
    
        if (!file_exists($currentDir)) {
            throw new Exception('CURRENT_FOLDER_NOT_EXIST');
        }

        ///clear content inside the folder CURRENT
        exec("rm -rf ".$currentDir."/*", $output, $return);
        if($return !== 0) {
            throw new Exception("UNABLE_REMOVE_FILES_IN_FOLDER_CURRENT");
        }
        ///////////////----------COPY FILES PROCESS----------///////////////

        exec("cp -R ".$updateDir."/* ".$currentDir."/",$output, $return);
        if($return !== 0) {
            throw new Exception('UNABLE_COPY_FILES_FROM_UPDATE_TO_CURRENT');
        }

        // exec("chmod 755 ".$currentDir, $output, $return);
        exec("chmod -R 777 ".$currentDir, $output, $return);
        if($return !== 0) {
            throw new Exception('UNABLE_CHANGE_PERMISSION_CURRENT_FOLDER');
        }
      

        if(!changeDir('CURRENT')) {
            throw new Exception('CFG_FILE_UNWRITABLE');
        }

        $result['rslt'] = 'success';
        $result['reason'] = 'UPDATE_APPLIED';
        return $result;
    }
    catch(Exception $e) {
        $result["rslt"] = 'fail';
        $result["reason"] = $e->getMessage();
        return $result;
    }

}


function applyDefaultSw($userObj, $updateDir, $currentDir, $defaultDir) {

    try {
        if ($userObj->grpObj->swupd != "Y") {
            throw new Exception('Permission Denied');
        }
        // Step: 
        //     Check folder exist or not
        //     Delete everything inside folder 
        //     Move new files into folder 
        //     Change permission of new files inside folder
        ////////////////------------check folder DEFAULT------------/////////////
        if ($defaultDir ==="") {
            throw new Exception("NO_DEFAULT_FOLDER_INFORMATION");
        }
    
        if (!file_exists($defaultDir)) {
            throw new Exception("FOLDER_FOR_DEFAULTSW_NOT_CREATED");
        }
    
        //check if there are more than 1 zip file in there
        $fileList = glob("{$defaultDir}/*");
        if(count($fileList) == 0) {
            throw new Exception('DEFAULT_FOLDER_EMPTY');
        }
        ////////////////------------check folder CURRENT------------/////////////
        if ($currentDir ==="") {
            throw new Exception("NO_CURRENT_FOLDER_INFORMATION");
        }
    
        if (!file_exists($currentDir)) {
            throw new Exception('CURRENT_FOLDER_NOT_EXIST');
        }
        
        ///clear content inside the folder CURRENT
        exec("rm -rf ".$currentDir."/*", $output, $return);
        if($return !== 0) {
            throw new Exception("UNABLE_REMOVE_FILES_IN_FOLDER_CURRENT");
        }
     
        ///////////////----------COPY FILES PROCESS----------///////////////

        exec("cp -R ".$defaultDir."/* ".$currentDir."/",$output, $return);
        if($return !== 0) {
            throw new Exception('UNABLE_COPY_FILES_FROM_UPDATE_TO_CURRENT');
        }

        // exec("chmod 755 ".$currentDir, $output, $return);
        exec("chmod -R 777 ".$currentDir, $output, $return);
        if($return !== 0) {
            throw new Exception('UNABLE_CHANGE_PERMISSION_CURRENT_FOLDER');
        }
        
        if(!changeDir('CURRENT')) {
            throw new Exception('CFG_FILE_UNWRITABLE');
        }

        $result['rslt'] = 'success';
        $result['reason'] = 'DEFAULT_APPLIED';
        return $result;
    }
    catch(Exception $e) {
        $result["rslt"] = 'fail';
        $result["reason"] = $e->getMessage();
        return $result;
    }

}


function setDefaultSw($userObj, $updateDir, $currentDir, $defaultDir) {

    try {
        if ($userObj->grpObj->swupd != "Y") {
            throw new Exception('Permission Denied');
        }
        // Step: 
        //     Check folder exist or not
        //     Delete everything inside folder 
        //     Move new files into folder 
        //     Change permission of new files inside folder
        ////////////////------------check folder CURRENT------------/////////////
        if ($currentDir ==="") {
            throw new Exception("NO_CURRENT_FOLDER_INFORMATION");
        }
    
        if (!file_exists($currentDir)) {
            throw new Exception('CURRENT_FOLDER_NOT_EXIST');
        }

        $fileList = glob("{$currentDir}/*");
        if(count($fileList) == 0) {
            throw new Exception('CURRENT_FOLDER_EMPTY');
        }

        ////////////////------------check folder DEFAULT------------/////////////

        if ($defaultDir ==="") {
            throw new Exception("NO_DEFAULT_FOLDER_INFORMATION");
        }
    
        if (!file_exists($defaultDir)) {
            // if(!mkdir($updateDir, 0755, true)){
            if(!mkdir($defaultDir, 0777, true)){
                throw new Exception("FOLDER_FOR_DEFAULTSW_NOT_CREATED");
            }
        }
    
        exec("rm -rf ".$defaultDir."/*", $output, $return);
        if($return !== 0) {
            throw new Exception("UNABLE_REMOVE_FILES_IN_FOLDER_DEFAULT");
        }

        
    
        ///////////////----------COPY FILES PROCESS----------///////////////

        exec("cp -R ".$currentDir."/* ".$defaultDir."/",$output, $return);
        if($return !== 0) {
            throw new Exception('UNABLE_COPY_FILES_FROM_CURRENT_TO_DEFAULT');
        }

        // exec("chmod 755 ".$defaultDir, $output, $return);
        exec("chmod -R 777 ".$defaultDir, $output, $return);
        if($return !== 0) {
            throw new Exception('UNABLE_CHANGE_PERMISSION_DEFAULT_FOLDER');
        }

        $result['rslt'] = 'success';
        $result['reason'] = 'DEFAULT_SET';
        return $result;
    }
    catch(Exception $e) {
        $result["rslt"] = 'fail';
        $result["reason"] = $e->getMessage();
        return $result;
    }

}


?>