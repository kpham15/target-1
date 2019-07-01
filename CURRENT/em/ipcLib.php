<?php

function lib_getIpcTime() {
    
    $wcObj = new WC();

    // establish ipc_time
    date_default_timezone_set("UTC");
    $utc_tz = date_default_timezone_get();
    $utc_t = time();
    $ipc_tz_offset = $wcObj->tz * 3600;
    $ipc_t = $utc_t + ($wcObj->tz * 3600);

    $secondSundayInMarch = date("d-M-Y", strtotime("second sunday " . date('Y') . "-03"));
    $firstSundayInNovember = date("d-M-Y", strtotime("first sunday " . date('Y') . "-11"));
    $dst_begin_t = strtotime($secondSundayInMarch) + $ipc_tz_offset;
    $dst_end_t = strtotime($firstSundayInNovember) + $ipc_tz_offset;

    $ipc_tzone = $wcObj->tzone;
    if ($ipc_t > $dst_begin_t && $ipc_t < $dst_end_t) {
        if (date('I', $ipc_t) == 0) {
            $ipc_t = $ipc_t + 3600;
            $ipc_tzone = substr_replace($wcObj->tzone,"DT",-2);
        }
    }
    $ipc_time = date("Y-m-d H:i:s", $ipc_t);
    return $ipc_time;
}

function lib_IsTimedOut($date, $timeLimit) {
    //global $local_timezone;

    $local_timezone = date_default_timezone_get();
    $dt = new DateTime("now", new DateTimeZone($local_timezone));
    $currentTime = $dt->format('U');
    $lt = new DateTime($date, new DateTimeZone($local_timezone));
    $prevTime   = $lt->format('U');
    $timediff     = ($currentTime - $prevTime);
    if ($timediff > $timeLimit) {
        return TRUE;
    }
    return FALSE;
}

//logout user if exceeds idle_to
function lib_ValidateUser($userObj) {
    global $local_timezone;

    if ($userObj->uname != 'ADMIN' && $userObj->uname != 'SYSTEM') {
        $refObj = new REF();
        // if time since last action exceeds user_idle_to then logout user
        // if user_idle_to is 0/empty/null, set value to default, if default is 0/empty/null, set value = 45
        $userIdleInfo = $refObj->ref['user_idle_to'];
        if($userIdleInfo == 0){
            $userIdleInfo = $refObj->default['user_idle_to'];
            if($userIdleInfo == 0)
                $userIdleInfo = 45;
        }
    
        $idle_to = $userIdleInfo * 60;

        if ($userObj->stat == 'ACTIVE') {
            if (lib_IsTimedOut($userObj->login, $idle_to)) {
                $userObj->updateLogout();
                $result['rslt'] = 'fail';
                $result['reason'] = 'SESSION HAS TIMED OUT';
                $result['rows'] = [];
                return $result;
            }
            else {

                $userObj->updateLogin();
                $result['rslt'] = 'success';
                $result['reason'] = 'USER IS ACTIVE';
                $result['rows'] = [];
                return $result;
            }
        }
        else {
            if ($userObj->stat == 'INACTIVE') {
                $result['reason'] = 'SESSION HAS TIMED OUT';
            }
            else {
                $result['reason'] = 'USER IS ' . $userObj->stat;
            }
            $result['rslt'] = 'fail';
            $result['rows'] = [];
            return $result;
        }
    }
    else {
        $result['rslt'] = 'success';
        $result['reason'] = 'USER IS ACTIVE';
        $result['rows'] = [];
        return $result;
    }
}

function lib_scanUsersTimeOut() {
    $userList = new USERS();
    $userList->queryByName('','');
    $refObj = new REF();

    $len = count($userList->rows);
    for ($i=0; $i<$len; $i++) {
        if (lib_IsTimeout($userList->rows[$i]['login'], $refObj->rows[0]['disable_to'])) {
            if ($userList->rows[$i]['ugrp'] != 'ADMIN') {
                $userList->disableUserByUname($userList->rows[$i]['uname']);
            }
        }

    }
}

// function libValidat


function libFilterUsers($userLogginObj, $rows) {
    $filteredRows = [];
    
    for ($i = 0; $i < count($rows); $i++) {
        if($userLogginObj->uname == $rows[$i]['uname']) {
            $filteredRows[] = $rows[$i];
        }
        else {
            if (strtoupper($rows[$i]['uname']) != "ADMIN" && strtoupper($rows[$i]['uname']) != "SYSTEM") {
                // if userLoggin is higher than rows user
                if($userLogginObj->grp <= $rows[$i]['grp']) {
                    if($userLogginObj->ugrp == "ADMIN") {
                        if ($rows[$i]['ugrp'] == "ADMIN") {
                            $rows[$i]['ssn'] = "";
                        }
                        $filteredRows[] = $rows[$i];
                    }
                    else {
                        $rows[$i]['ssn'] = "";
                        $filteredRows[] = $rows[$i];
                    }
                }
            }
        }
    }
    return $filteredRows;
}

// function checkWcStatus() {
//     $wcObj = new WC();
//     if ($wcObj->rslt == "fail") {
//         $result["rslt"] = "fail";
//         $result["reason"] = "CHECK_WC_STATUS: ".$wcObj->reason;
//         return $result;
//     }

//     if($wcObj->getIpcStat() == "OOS") {
//         $result["rslt"] = "fail";
//         $result["reason"] = "CHECK_WC_STATUS: IPC SYSTEM IS OOS";
//         return $result;
//     }

//     $result["rslt"] = "success";
//     $result["reason"] = "CHECK_WC_STATUS: IPC SYSTEM IS INS";
//     return $result;
// }


///////////////////-----------SW UPDATE----------------/////////////////////

function removeFiles($directory){
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            removeFolder($file);
        } else {
            if(!unlink($file)) {
                throw new Exception("UNABLE_DELETE_FILE");
            }
        }
    }
}

function removeFolder($directory) {
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            removeFolder($file);
        } else {
           if(!unlink($file)) {
               throw new Exception("UNABLE_DELETE_FILE");
           }
        }
    }
    if(!rmdir($directory)) {
        throw new Exception("UNABLE_DELETE_FOLDER");
    }
}

function changePermissionFiles($directory, $permission){
    if(!chmod($directory, $permission)) {
        throw new Exception("UNABLE_CONFIGURE_PERMISSION_FOLDER");
    }
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            // chmod($file, 0777);
            changePermissionFiles($file);
        } else {
            if(!chmod($file, $permission)) {
                throw new Exception("UNABLE_CONFIGURE_PERMISSION_FILE");
            }
        }
    }
}

function moveFiles($fromDir,$toDir) { 

    ////Note: need to create folder before transfer files
    $dir = opendir($fromDir); 
    if (!file_exists($toDir)) {
        throw new Exception("FOLDER_NOT_EXIST");
    }
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if (is_dir($fromDir .'/'. $file) ) { 
                moveFiles($fromDir.'/'.$file, $toDir .'/'.$file); 
            } 
            else { 
                if(!copy($fromDir.'/'.$file, $toDir.'/'.$file)) {
                    throw new Exception("UNABLE_COPY_FILE");
                } 
            } 
        } 
    } 
    closedir($dir); 
} 


function updateFolderDir() {
    $running = "";
    // $new = "";
    // $update = "";
    // $previous = "";

    $file = fopen("../../bhd.cfg", "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            // process the line read.
            $lineExtract = explode(":", $line);
            if($lineExtract[0] == "RUNNING") 
                $running = str_replace(array("\r\n", "\r", "\n", "\t"),"",$lineExtract[1]);
            // else if($lineExtract[0] == "NEW") 
            //     $new = str_replace(array("\r\n", "\r", "\n", "\t"),"",$lineExtract[1]);
            // else if($lineExtract[0] == "UPDATE") 
            //     $update = str_replace(array("\r\n", "\r", "\n", "\t"),"",$lineExtract[1]);
            // else if($lineExtract[0] == "PREVIOUS") 
            //     $previous = str_replace(array("\r\n", "\r", "\n", "\t"),"",$lineExtract[1]);
        }

        fclose($file);
    }

    $result['RUNNING'] = $running;
    // $result['UPDATE'] = $update;
    // $result['NEW'] = $new;
    // $result['PREVIOUS'] = $previous;
    return $result;
}

function changeDir($dir) {
    $lines = file("../../bhd.cfg");
    if(!$lines) {
        return false;
    }
    $result = '';
    
    foreach($lines as $line) {
        if(explode(':',$line)[0] == 'RUNNING') {
            $result .= 'RUNNING:'.$dir."\n";
        } 
        else {
            $result .= $line;
        }
    }
    
    $write = file_put_contents("../../bhd.cfg", $result);
    if(!$write) {
        return false;
    }
    
    return true;
}


//-------------------create confirm key---------------------//
function createKey() {
    $key = "";
    $key .= ord('B');
    $key .= chr('66');
    $key .= ord('H');
    $key .= chr('88');
    $key .= ord('D');
    $key .= chr('99');

    return $key;
}

function encryptData($data) {
    $serverKey = createKey();
    return JWT::encode($data, $serverKey);
}

function decryptData($data) {
    try { 
        if($data == "")
            return "";
        $serverKey = createKey();
        return JWT::decode($data, $serverKey, array('HS256'));
    }
    catch (Exception $e) {
        throw new Exception("UNABLE TO DECODE INFORMATION!", 100);
    }
}

    
?>