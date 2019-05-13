<?php

include "../class/ipcDbClass.php";
include "../class/ipcHwClass.php";
include "./ipcNodeServerClass.php";
include "../class/ipcConstantClass.php";

$dbObj = new Db();
if ($dbObj->rslt == "fail") {
	$result["rslt"] = "fail";
	$result["reason"] = $dbObj->reason;
	echo json_encode($result);
	return;
}
$db = $dbObj->con;

$com1Obj = new NODESERVER("127.0.0.1", 9000,1);
if($com1Obj->rslt == 'fail') {
    echo $com1Obj->reason."\n";
    return;
}
$com1Obj->run();

?>