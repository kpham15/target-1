<?php

function discovered($node, $hwRsp) {

    // the URL to post to which API (Its almost always going to be Dispatch)
    // Array that would POST to the API as if it were from front end GUI
    // 'act' is the dispatch function being called
    $url = "http://localhost/workspaces/kris/em/ipcDispatch.php";
    $data = array('api' => "ipcNodeOpe", 'user'=>'system', 'act' => 'DISCOVERED', 'node' => $node, 'hwRsp' => $hwRsp);

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


// $resp = almApi('\$ackid=1-CPS,status,current=1239mA,voltage=45678mV*');
// echo $resp->rslt . ": " . $resp->reason . "\n";
$node = "1";
$hwRsp = "\$ackid=1-bkpln,status,device=miox(0),uuid=IAMAMIOXUUIDTHATYOUCANTDECODE*";
$resp = discovered($node, $hwRsp);
// print_r($resp->rslt);
print_r($resp->reason);
// $resp = almApi('\$ackid=0-cps,status,temperature,zone1=67C,zone2=65C,zone3=66C,zone4=68C*');
// echo $resp->rslt . ": " . $resp->reason . "\n";


?>