<?php
$globalDir = buildUrl();

asyncPostRequest($globalDir."/ipcAlmReport.php", ['act'=>'clean','user'=>'system']);
// asyncPostRequest($globalDir."/ipcProvReport.php", ['act'=>'clean','user'=>'system']);
// asyncPostRequest($globalDir."/ipcCfgReport.php", ['act'=>'clean','user'=>'system']);
// asyncPostRequest($globalDir."/ipcMaintReport.php", ['act'=>'clean','user'=>'system']);

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

function asyncPostRequest($url, $params){
    $content = http_build_query($params);
    $parts=parse_url($url);

    $fp = fsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($content)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($content)) 
        $out.= $content;
    fwrite($fp, $out);
    // while (!feof($fp)) {
    //     echo fgets($fp, 1024);
    // }
	fclose($fp);
}



?>
