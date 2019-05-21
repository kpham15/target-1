<?php
//because this program needs to read /dev/ttyUSB file => www-data user need to be put in group: dialout
// cmd structure to run this program:
// php ipcCpsloopSerial_receiveRsp.php node sport

include 'ipcCpsClientSerialClass.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

//create url path for the post-request afterwards
$localUrl = buildUrl();
//assign arguments
$node = $argv[1];
$sport = $argv[2];
$baud = 115200;
$bits = 8;
$stop = 1;
$parity = 0;
$timeoutSec = 0;
$timeoutUsec = 500000;

$lostConn = 0;

//-------------------------Begin--------------------------------
// define ERROR CODE
const SERIAL_CPS_HW_FAIL = 2;

try {
    $clientExist = false;

    clientSock:
        // ------------create new connection to CPS HW 
        echo "\ncreating serial client....\n";
        if($clientExist == false) {
            $cpsClientObj = new CPSCLIENT($sport, $baud, $bits, $stop, $parity, $timeoutSec, $timeoutUsec);
            if($cpsClientObj->rslt == 'fail') {   
                throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,SERIAL_CPS_HW_FAIL);
            }
            $clientExist = true;
        }
    echo "start listening:\n";   

    //listening for cmd from api
    while(1) {
        //get the starting time 
        $startTime = microtime(true);
        while(microtime(true) - $startTime <5) {
            //receive reply from HW
            $rsp = receiveRsp($cpsClientObj);
            if($rsp !== '') {
                echo $rsp;
                // processRsp($rsp, $node, $localUrl);
                // if($lostConn > 0) 
                //     $lostConn = 0;
            }

        }
        if($rsp === '') {
            $lostConn ++;
            if($lostConn % 3 == 0) {
                echo "\nLost connection to HW!\n";
                //Process the lost-connection problem
            }
        }
        // when 5s expires, go back
    }
        
}
catch (Throwable $t)
{   
    echo "\n".$t->getMessage()."\n";

    if($t->getCode() == SERIAL_CPS_HW_FAIL) {
        // If errorCode = 2, that means socket to HW is broken, close the socket and create a new one
        $cpsClientObj->endConnection();
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

function receiveRsp($clientObj) {
    $rsp='';
    while(1) {
        $clientObj->receiveRsp();
        if($clientObj->rsp === "") {
            break;
        }
        $rsp .= $clientObj->rsp;
        // echo "\n***Response from HW: ".$clientObj->rsp."\n";
    }
    return $rsp;
}

?>