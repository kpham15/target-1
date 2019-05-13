<?php
include "./ipcClasses.php";

$root_dir = '/var/www/html/target-1/UPDATE';

$ipcSwUpdate		 = "../../../em/ipcSwUpdate.php";
$ipcAlm				 = "../../../em/ipcAlm.php";
$ipcAlmReport		 = "../../../em/ipcAlmReport.php";
$ipcBatchExc		 = "../../../em/ipcBatchExc.php";
$ipcBroadcast		 = "../../../em/ipcBroadcast.php";
$ipcCfgReport		 = "../../../em/ipcCfgReport.php";
$ipcEventlog         = "../../../em/ipcEventlog.php";
$ipcEvtlog			 = "../../../em/ipcEvtlog.php";
$ipcFacilities		 = "../../../em/ipcFacilities.php";
$ipcFindOrder		 = "../../../em/ipcFindOrder.php";
// $ipcFoms             = "../../../em/ipcFoms.php";
$ipcFtOrd			 = "../../../em/ipcFtOrd.php";
$ipcFtRelease		 = "../../../em/ipcFtRelease.php";
$ipcFtModTable		 = "../../../em/ipcFtModTable.php";
$ipcLogin			 = "../../../em/ipcLogin.php";
$ipcLogout			 = "../../../em/ipcLogout.php";
$ipcMaintConnect	 = "../../../em/ipcMaintConnect.php";
$ipcMaintDiscon		 = "../../../em/ipcMaintDiscon.php";
$ipcMaintReport		 = "../../../em/ipcMaintReport.php";
$ipcMaintRestoreMtcd = "../../../em/ipcMaintRestoreMtcd.php";
$ipcMxc				 = "../../../em/ipcMxc.php";
$ipcNode			 = "../../../em/ipcNode.php";
$ipcNodeAdmin        = "../../../em/ipcNodeAdmin.php";
$ipcOrd  			 = "../../../em/ipcOrd.php";
$ipcOpt				 = "../../../em/ipcOpt.php";
$ipcPath			 = "../../../em/ipcPath.php";
$ipcPortmap			 = "../../../em/ipcPortmap.php";
$ipcProv			 = "../../../em/ipcProv.php";
// $ipcProvConnect		 = "../../../em/ipcProvConnect.php";
$ipcProvDisconnect	 = "../../../em/ipcProvDisconnect.php";
$ipcProvReport		 = "../../../em/ipcProvReport.php";
$ipcRef				 = "../../../em/ipcRef.php";
$ipcSearch 			 = "../../../em/ipcSearch.php";
$ipcTrouble			 = "../../../em/ipcTrouble.php";
$ipcUser			 = "../../../em/ipcUser.php";
$ipcWc				 = "../../../em/ipcWc.php";
$ipcTb               = "../../../em/ipcTb.php";
$ipcConfirm          = "../../../em/ipcConfirm.php";


// $ipcSwUpdate		 = "../em/ipcSwUpdate.php";
// $ipcAlm				 = "../em/ipcAlm.php";
// $ipcAlmReport		 = "../em/ipcAlmReport.php";
// $ipcBatchExc		 = "../em/ipcBatchExc.php";
// $ipcBroadcast		 = "../em/ipcBroadcast.php";
// $ipcCfgReport		 = "../em/ipcCfgReport.php";
// $ipcEventReport      = "../em/ipcEventlog.php";
// $ipcEvtlog			 = "../em/ipcEvtlog.php";
// $ipcFacilities		 = "../em/ipcFacilities.php";
// $ipcFindOrder		 = "../em/ipcFindOrder.php";
$ipcFoms             = "../em/ipcFoms.php";
$ipcFtOrd			 = "../em/ipcFtOrd.php";
// $ipcFtRelease		 = "../em/ipcFtRelease.php";
// $ipcFtModTable		 = "../em/ipcFtModTable.php";
// $ipcLogin			 = "../em/ipcLogin.php";
// $ipcLogout			 = "../em/ipcLogout.php";
// $ipcMaintConnect	 = "../em/ipcMaintConnect.php";
// $ipcMaintDiscon		 = "../em/ipcMaintDiscon.php";
// $ipcMaintReport		 = "../em/ipcMaintReport.php";
// $ipcMaintRestoreMtcd = "../em/ipcMaintRestoreMtcd.php";
// $ipcMxc				 = "../em/ipcMxc.php";
// $ipcNode			 = "../em/ipcNode.php";
$ipcNodeAdmin	    = "../em/ipcNodeAdmin.php";
// $ipcOrd  			 = "../em/ipcOrd.php";
// $ipcOpt				 = "../em/ipcOpt.php";
// $ipcPath			 = "../em/ipcPath.php";
// $ipcPortmap			 = "../em/ipcPortmap.php";
// $ipcProv			 = "../em/ipcProv.php";
$ipcProvConnect		 = "../em/ipcProvConnect.php";
// $ipcProvDisconnect	 = "../em/ipcProvDisconnect.php";
// $ipcProvReport		 = "../em/ipcProvReport.php";
// $ipcRef				 = "../em/ipcRef.php";
// $ipcSearch 			 = "../em/ipcSearch.php";
// $ipcTrouble			 = "../em/ipcTrouble.php";
// $ipcUser			 = "../em/ipcUser.php";
// $ipcWc				 = "../em/ipcWc.php";
// $ipcTb               = "../em/ipcTb.php";
// $ipcConfirm          = "../em/ipcConfirm.php";



/* Initialize expected inputs */
$api = '';
if(isset($_POST['api'])) {
    $api = $_POST['api'];
}
//get the key
    if($api =='ipcConfirm') {
        include $ipcConfirm;
        return;
    }

$user = '';
if(isset($_POST['user'])) {
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

$userObj = new USERS($user);
if ($userObj->rslt != SUCCESS) {
    $evtLog = new EVENTLOG($user, "USER MANAGEMENT", "USER ACCESS", '-');
    $result['rslt'] = $userObj->rslt;
    $result['reason'] = $userObj->reason;
    $evtLog->log($result["rslt"], $result["reason"]);    
    $vioObj = new VIO();
    $vioObj->setUnameViolation();
    mysqli_close($db);
    echo json_encode($result);
    return;
}

// The following apis skip user validation
if($api =='ipcLogout') {
    include $ipcLogout;
    return;
}

if($api =='ipcLogin') {
    include $ipcLogin;
    return;
}



// validate login user
if ($userObj->uname != 'SYSTEM') {
    $result = lib_ValidateUser($userObj);
    if ($result['rslt'] == 'fail') {
        echo json_encode($result);
        mysqli_close($db);
        return;
    }
}

// Dispatch to API
if($api =='ipcAlm') {
    include $ipcAlm;
}
else if($api =='ipcAlmReport') {
    include $ipcAlmReport;
}
else if($api =='ipcBatchExc') {
    include $ipcBatchExc;
}
else if($api =='ipcBroadcast') {
    include $ipcBroadcast;
}
else if($api =='ipcCfgReport') {
    include $ipcCfgReport;
}
else if($api =='ipcEvtlog') {
    include $ipcEvtlog;
}
else if($api =='ipcFacilities') {
    include $ipcFacilities;
}
else if($api =='ipcFindOrder') {
    include $ipcFindOrder;
}
else if($api == 'ipcFoms') {
    include $ipcFoms;
}
else if($api =='ipcFtModTable') {
    include $ipcFtModTable;
}
else if($api =='ipcFtOrd') {
    include $ipcFtOrd;
}
else if($api =='ipcFtRelease') {
    include $ipcFtRelease;
}
else if($api =='ipcLib') {
    include $ipcLib;
}
else if($api =='ipcMaintConnect') {
    include $ipcMaintConnect;
}
else if($api =='ipcMaintDiscon') {
    include $ipcMaintDiscon;
}
else if($api =='ipcMaintReport') {
    include $ipcMaintReport;
}
else if($api =='ipcMaintRestoreMtcd') {
    include $ipcMaintRestoreMtcd;
}
else if($api =='ipcMxc') {
    include $ipcMxc;
}
else if($api =='ipcNode') {
    include $ipcNode;
}
else if($api =='ipcNodeAdmin') {
    include $ipcNodeAdmin;
}
else if($api =='ipcOpt') {
    include $ipcOpt;
}
else if($api =='ipcPath') {
    include $ipcPath;
}
else if($api =='ipcPortmap') {
    include $ipcPortmap;
}
else if($api =='ipcProv') {
    include $ipcProv;
}
else if($api =='ipcProvConnect') {
    include $ipcProvConnect;
}
else if($api =='ipcProvDisconnect') {
    include $ipcProvDisconnect;
}
else if($api =='ipcProvReport') {
    include $ipcProvReport;
}
else if($api =='ipcRef') {
    include $ipcRef;
}
else if($api =='ipcSearch') {
    include $ipcSearch;
}
else if($api =='ipcSwUpdate') {
    include $ipcSwUpdate;
}
else if($api =='ipcTrouble') {
    include $ipcTrouble;
}
else if($api =='ipcUser') {
    include $ipcUser;
}
else if($api =='ipcWc') {
    include $ipcWc;
}
else if($api == "ipcEventlog") {
    include $ipcEventlog;
}
else if ($api == "ipcOrd") {
    include $ipcOrd;
}
else if($api == "ipcTb") {
    include $ipcTb;
}
else {
    $result["rslt"] = FAIL;
    $result["reason"] = "INVALID_API";
    echo json_encode($result);
    return;
}


?>