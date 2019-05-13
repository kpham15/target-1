<?php

include "../class/ipcConstantClass.php";
include "ipcNodeClientClass.php";


set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});



//////////////-------Start the forever loop--------///////////////
$globalDir = "";

while (true) {

    $globalDir = buildUrl();

    $response = queryNodeById($argv);
    if ($response->rslt == 'fail'){
        echo $response->rslt.":".$response->reason."\n";
        return;
    }
    $nodeObj = $response->rows[0];

    ///////////------------------First command--------------------/////////////
    $clientObj = new NODECLIENT($nodeObj);
    if($clientObj->rslt == 'fail') {
        echo $clientObj->rslt.":".$clientObj->reason."\n";
    }
    else {
        $clientObj->checkNodeStatus(); 
        if($clientObj->rslt == 'fail') {
            echo $clientObj->rslt.":".$clientObj->reason."\n";
        }
        $clientObj->closeNodeClient();
    }
    //////////////-----------------Second command----------------------------////////////////
    $clientObj = new NODECLIENT($nodeObj);
    if($clientObj->rslt == 'fail') {
        echo $clientObj->rslt.":".$clientObj->reason."\n";
    }
    else {
        $clientObj->sendAlmStatus(); 
        if($clientObj->rslt == 'fail') {
            echo $clientObj->rslt.":".$clientObj->reason."\n";
        }
        $clientObj->closeNodeClient();
    }

    /////////////////-----------------Delay for 5sec-------------/////////////////
    sleep(5);
}

function queryNodeById($argv) {
    global $globalDir;
    // $url = 'http://localhost/v2.1/CURRENT/em/ipcNode.php';
    $url = $globalDir."/ipcDispatch.php";
   
    $data = array('api' => "ipcNode",'act' => "query", 'node' => $argv[1], 'user'=>'system');

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

function buildUrl() {
    $fullpath = 'http://localhost';
    //get the directory of current file
    $directory = dirname(__FILE__);
    //split it into array
    $dirArray = explode("/",$directory);
    $htmlIndex = array_search("html",$dirArray);
    $osIndex = array_search("os",$dirArray);
    
    // build up the fullpath url
    for($i=($htmlIndex+1); $i<$osIndex; $i++) {
        $fullpath .= '/'.$dirArray[$i];
        
    }
    $fullpath .= '/em';
    return $fullpath;
}


?>