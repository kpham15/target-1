<?php

include 'ipcCpsClientClass.php';
include 'ipcCpsServerClass.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

error_reporting(E_ALL);

$localUrl = buildUrl();
$node = $argv[1];
$nodeStat = [
    'fail_count'=>1,
];

//-------------------------Begin--------------------------------
// define ERROR CODE
const SOCKET_API_FAIL = 1;
const SOCKET_CPS_HW_FAIL = 2;

try {
    $serverExist = false;
    $clientExist = false;
    //--------------Get testing ip address of live CPS------------------
    // $address = gethostbyname('8fde09f396e4.sn.mynetname.net');  
 
    serverSock: 
        //-------------to communicate with API (UDP type)-----------------
        echo "\ncreating UPD server.....\n";
        if($serverExist == false) {
            $cpsServerObj = new CPSSERVER("127.0.0.1", $argv[3]);
            if($cpsServerObj->rslt == 'fail') {   
                throw new Exception($cpsServerObj->rslt.": ".$cpsServerObj->reason,SOCKET_API_FAIL);
            }
            //set timeout for the server
            socket_set_option($cpsServerObj->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 0, 'usec' => 500000));
            socket_set_option($cpsServerObj->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 500000));
            $serverExist = true;
        }

       

    clientSock:
        // ------------create new connection to CPS HW  (TCP type)
        echo "\ncreating TCP client....\n";
        if($clientExist == false) {
            $cpsClientObj = new CPSCLIENT($argv[2], $argv[3], 0, 500000);
            if($cpsClientObj->rslt == 'fail') {   
                throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,SOCKET_CPS_HW_FAIL);
            }
            $clientExist = true;
        }
        

    sendStatusCmd:
        //------Send the status cmd-------
        $cmd = "\$STATUS,SOURCE=ALL,ACKID=$node-CPS*"; 
        $rsp = sendReceiveCmd($cpsClientObj,$cmd);
        // If receive a response from HW, reset the fail_count = 0, and process the response
        // If not receive any response from HW, increase the fail_count. If fail_count = 3, consider that HW communication is broken 
        if($rsp !== '') {
            processRsp($rsp, $node, $localUrl);
            if($nodeStat['fail_count'] !== 0) {
                $nodeStat['fail_count'] = 0;
                asyncPostRequest($localUrl."/ipcDispatch.php", ['user'=>'SYSTEM','api'=>'ipcNodeAdmin','act'=>'updateCpsCom','node'=>$node,'cmd'=>"$node-ONLINE"]);
            }
            
        }
        else {
            $nodeStat['fail_count']++;
            if($nodeStat['fail_count'] == 3) {
                asyncPostRequest($localUrl."/ipcDispatch.php", ['user'=>'SYSTEM','api'=>'ipcNodeAdmin','act'=>'updateCpsCom','node'=>$node,'cmd'=>"$node-OFFLINE"]);
            }
        }
       
    //get the starting time 
    $startTime = microtime(true);
    
    //listening for cmd from api
    listenCmd:
        while(microtime(true) - $startTime <5) {
            echo "\n----------CPS loop is listening for comming data from API!\n";
            // Wait for cmd from API, if timeout (errorCode =11), go back to listen. For other errorCode, throw an Exception 
            $input = socket_recvfrom($cpsServerObj->socket, $buf, 1024, 0, $remote_ip, $remote_port);
            if($input === false) {
                $errorCode = socket_last_error($cpsServerObj->socket);
                if($errorCode != 11)
                    throw new Exception("fail: ".socket_strerror($errorCode),SOCKET_API_FAIL);
                else    
                    goto listenCmd;
            }

            $cmd = trim($buf);
            echo "\n==========CMD receive from API: ".$cmd."\n";

            // send cmd to HW and receive reply from HW
            $rsp = sendReceiveCmd($cpsClientObj,$cmd);
            if($rsp !== '') {
                processRsp($rsp, $node, $localUrl);
            }

        }
        // when 5s expires, go back and send status command again
        goto sendStatusCmd;
        
}
catch (Throwable $t)
{   
    echo "\n".$t->getMessage()."\n";

    if($t->getCode() == SOCKET_API_FAIL) {
        // If errorCode = 1, that means socket to API is broken. Close the socket and create a new one
        $cpsServerObj->endConnection();
        $serverExist = false;
        sleep(5);
        goto serverSock;
    }

    else if($t->getCode() == SOCKET_CPS_HW_FAIL || strpos($t->getMessage(), "unable to write") !== false) {
        // If errorCode = 2, that means socket to HW is broken, close the socket and create a new one
        $cpsClientObj->endConnection();
        $clientExist = false;
        sleep(5);
        goto clientSock;
    }
    else if(strpos($t->getMessage(), "unable to connect") !== false) {
        $clientExist = false;
        sleep(5);
        goto clientSock;
    }
    else {
        // for other errorCode, exit the program
        return;
    }
    
}

////////////////////////////////////////////////////////////////////////////////////

///////---------------------------End of socket area----------------------//////////
//////////-------------------supporting function---------------------//////////////


function ackidExtract($buf) {
    $bufExtract = substr($buf,1,-1);
    $bufExtract = explode(',',$bufExtract);
    $ackid = explode('=', $bufExtract[0])[1];
    echo "\nackid received:$ackid\n";
    return $ackid;
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

// this function is to build and send a post request in required format 
function asyncPostRequest($url, $params){
    $content = http_build_query($params);
    $parts=parse_url($url);
    
    // echo $parts."\n";
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
    echo $out."\n";
    fwrite($fp, $out);
    while (!feof($fp)) {
        print_r(fgets($fp, 1024));
    }
	fclose($fp);
}

// this function is to process the response from HW. 
// it extracts the ackid, to know where to send the post request
function processRsp($rsp, $node, $localUrl) {
    $result = preg_split("/(\r\n|\n|\r)/",$rsp);
    print_r($result);

    for($i=0; $i<count($result); $i++) {
        $len = strlen($result[$i]);
        if($result[$i] !== '' && $result[$i][0] == '$' && $result[$i][$len-1] == '*') {
            if(stripos($result[$i],'$ackid') !== false) {
                //check and create post request
                echo "\nProcessing:".$result[$i]."\n";
                $ackid = ackidExtract($result[$i]);
                if(strpos($ackid,'CPS') !== false) {
                    asyncPostRequest($localUrl."/ipcDispatch.php", ['user'=>'SYSTEM','api'=>'ipcNodeAdmin','act'=>'updateCpsStatus','node'=>$node,'cmd'=>"$result[$i]"]);
                }
            }
        }
       
    }
}

function sendReceiveCmd($clientObj, $cmd) {
    $clientObj->sendCommand($cmd);
    if($clientObj->rslt == 'fail') {
        throw new Exception("fail: ".socket_strerror(socket_last_error($clientObj->socket)),SOCKET_CPS_HW_FAIL);
    }

    usleep(100000); 

    $rsp='';
    while(1) {
        $clientObj->receiveRsp();
        if($clientObj->rslt == 'fail') {
            throw new Exception("fail: ".socket_strerror(socket_last_error($clientObj->socket)),SOCKET_CPS_HW_FAIL);
        }
    
        if($clientObj->rsp === "") {
            break;
        }

        $rsp .= $clientObj->rsp;
        echo "\n***Response from HW: ".$clientObj->rsp."\n";
    
    }
    return $rsp;
}

?>