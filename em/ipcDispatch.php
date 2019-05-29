<?php
include "./ipcClasses.php";

$root_dir = '/var/www/html/target-1/UPDATE';

/* Initialize expected inputs */
$api = '';
if (isset($_POST['api'])) {
    $api = $_POST['api'];
}
//get the key
if($api =='ipcConfirm') {
    include "ipcConfirm.php";
    return;
}


$user = '';
if (isset($_POST['user'])) {
    $user = $_POST['user'];
}

$dbObj = new Db();
if ($dbObj->rslt == "fail") {
    $result["rslt"] = "fail";
    $result["reason"] = $dbObj->reason;
    echo json_encode($result);
    return;
}
$db = $dbObj->con;

$debugObj = new DEBUG();



// validate login user
    $userObj = new USERS($user);
    if ($userObj->rslt != SUCCESS) {
        $evtLog = new EVENTLOG($user, "USER MANAGEMENT", "USER ACCESS", '-');
        $result['rslt'] = $userObj->rslt;
        $result['reason'] = $userObj->reason;
        $evtLog->log($result["rslt"], $result["reason"]);
        $vioObj = new VIO();
        $vioObj->setUnameViolation();
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);
        return;
    }
// The following apis skip user validation
    if ($api =='ipcLogout') {
        include "ipcLogout.php";
        return;
    }
 
    if ($api =='ipcLogin') {
        include "ipcLogin.php";
        return;
    }

// validate login user
if ($userObj->uname != 'SYSTEM') {
    $result = lib_ValidateUser($userObj);
    if ($result['rslt'] == 'fail') {
        echo json_encode($result);
        mysqli_close($db);
        $debugObj->close();
        return;
    }
}

// Dispatch to API
if($api =='ipcAlm') {
    include "ipcAlm.php";
}
else if($api =='ipcAlmReport') {
    include "ipcAlmReport.php";
}
else if($api =='coQueryBkup') {
    include "coQueryBkup.php";
}
else if($api =='ipcBatchExc') {
    include "ipcBatchExc.php";
}
else if($api =='ipcBroadcast') {
    include "ipcBroadcast.php";
}
else if($api =='ipcCfgReport') {
    include "ipcCfgReport.php";
}
else if($api =='ipcEvtlog') {
    include "ipcEvtlog.php";
}
else if($api =='ipcFacilities') {
    include "ipcFacilities.php";
}
else if($api =='ipcFindOrder') {
    include "ipcFindOrder.php";
}
else if($api =='ipcFtModTable') {
    include "ipcFtModTable.php";
}
else if($api =='ipcFtOrd') {
    include "ipcFtOrd.php";
}
else if($api =='ipcFtRelease') {
    include "ipcFtRelease.php";
}
else if($api =='ipcLib') {
    include "ipcLib.php";
}
else if($api =='ipcMaintConnect') {
    include "ipcMaintConnect.php";
}
else if($api =='ipcMaintDiscon') {
    include "ipcMaintDiscon.php";
}
else if($api =='ipcMaintReport') {
    include "ipcMaintReport.php";
}
else if($api =='ipcMaintRestoreMtcd') {
    include "ipcMaintRestoreMtcd.php";
}
else if($api =='ipcMxc') {
    include "ipcMxc.php";
}
else if($api =='ipcNodeAdmin') {
    include "ipcNodeAdmin.php";
}
else if ($api == 'ipcNodeOpe') {
    include "ipcNodeOpe.php";
}
else if($api =='ipcOpt') {
    include "ipcOpt.php";
}
else if($api =='ipcPath') {
    include "ipcPath.php";
}
else if($api =='ipcPortmap') {
    include "ipcPortmap.php";
}
else if($api =='ipcProv') {
    include "ipcProv.php";
}
// else if($api =='ipcProvConnect') {
//     include "ipcProvConnect.php";
// }
// else if($api =='ipcProvDisconnect') {
//     include "ipcProvDisconnect.php";
// }
else if($api =='ipcProvReport') {
    include "ipcProvReport.php";
}
else if($api =='ipcRef') {
    include "ipcRef.php";
}
else if($api =='ipcSearch') {
    include "ipcSearch.php";
}
else if($api =='ipcSwUpdate') {
    include "ipcSwUpdate.php";
}
// else if($api =='ipcTrouble') {
//     include "ipcTrouble.php";
// }
else if($api =='ipcUser') {
    include "ipcUser.php";
}
else if($api =='ipcWc') {
    include "ipcWc.php";
}
else if($api == 'ipcEventlog') {
    include "ipcEventlog.php";
}
else if($api == 'ipcOrd') {
    include "ipcOrd.php";
}
else if($api == 'ipcTb') {
    include "ipcTb.php";
}
else if($api == 'ipcTbus') {
    include "ipcTbus.php";
}
// else if($api == 'ipcAction') {
//     include "ipcAction.php";
// }

else {
    $result["rslt"] = FAIL;
    $result["reason"] = "INVALID_API";
    echo json_encode($result);
    return;
}


?>
