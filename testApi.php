<?php

function dev($cmd) {

    // the URL to post to which API (Its almost always going to be Dispatch)
    // Array that would POST to the API as if it were from front end GUI
    // 'act' is the dispatch function being called
    $url = "http://localhost/workspaces/alex/em/ipcDispatch.php";
    $data = array('api' => "ipcNodeOpe", 'user'=>'system', 'act' => 'CPS_STATUS', 'cmd' => $cmd);

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


// echo $resp->rslt . ": " . $resp->reason . "\n";
$cmd = "\$ackid=1-cps,status,voltage1=46587mV,voltage2=47982mV,voltage3=48765mV,voltage4=49234mV*";
$resp = dev($cmd);
// print_r($resp->rslt);
print_r($resp->reason);
// $resp = almApi('\$ackid=0-cps,status,temperature,zone1=67C,zone2=65C,zone3=66C,zone4=68C*');
// echo $resp->rslt . ": " . $resp->reason . "\n";


?>