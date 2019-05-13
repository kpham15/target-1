<?php

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

function reportAlm($src, $evt) {

    $url = "http://localhost/target-1/UPDATE/em/ipcDispatch.php";
    $data = array('api' => "ipcAlm", 'user'=>'system','act' => 'REPORT', 'src' => $src, 'evt'=> $evt);

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


echo "start\n";

$src = '1-MX-1';
$evt = 'IN';
$resp = reportAlm($src, $evt);
echo $resp->rslt . ": " . $resp->reason . "\n";


?>
