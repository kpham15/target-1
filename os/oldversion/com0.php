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

$com0Obj = new NODESERVER("127.0.0.1", 9000,0);
if($com0Obj->rslt == 'fail') {
    echo $com0Obj->reason."\n";
    return;
}
$com0Obj->run();

?>