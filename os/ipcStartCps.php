<?php  

//initialize parameter
$nodeNum = 0;

$file = fopen('nodeInfo.cfg','r');
if ($file) {
    while (($line = fgets($file)) !== false) {
        // process the line read.
        $lineExtract = explode(":", $line);
        if($lineExtract[0] == "nodeNum") {
            $nodeNum = str_replace(array("\r\n", "\r", "\n", "\t"),"",$lineExtract[1]);
            break;
        }
    }
    fclose($file);
}
$nodeNum = (int)$nodeNum;
chdir("../os");
echo $nodeNum;
return;
if($nodeNum > 0 ) {
    for($i=0; $i<2; $i++) {
        $node = $i+1;
        $ip_port = 9000 + $i;
        $pid = exec("php ipcCpsloop.php $node $ip_port > /dev/null 2>&1 & echo $!", $output, $return);
        echo $pid;
        if(!$return) {
            //log the error somewhere
        }

    }
}



?>