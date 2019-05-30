<?php
include "../class/ipcPostRequestClass.php";

$postReqObj = new POST_REQUEST();

$hwRsp = "\$ackid=1-cps-csta,status,voltage1=46587mV,voltage2=47982mV,voltage3=48765mV,voltage4=49234mV,backplane=IAMAMIOXUUIDTHATYOUCANTDECODE*";

$url = 'ipcDispatch.php';
$params = array('api' => "ipcNodeOpe", 'user'=>'SYSTEM', 'act' => 'CPS_STATUS', 'hwRsp' => $hwRsp);

$postReqObj->syncPostRequest($url, $params);
echo "$postReqObj->reply";
?>