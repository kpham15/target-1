<?php
include 'ipcCmdClass.php';
include 'ipcDbClass.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

error_reporting(E_ALL);

$localUrl = buildUrl();
////////////////////////////////////////////
try {
    $dbObj = new Db();
    if ($dbObj->rslt == "fail") {
        throw new Exception($dbObj->rslt.": ".$dbObj->reason, 10);
    }
    $db = $dbObj->con;

    //Address of CPS loop
    $addressServer = "127.0.0.1";
    $portServer = 5000;   

    createClientSocket:
    //create socket UDP
    $clientSocket = socket_create(AF_INET, SOCK_DGRAM, 0);
    if($clientSocket === false) {
        throw new Exception("fail: Unable to create socket", 10);
    }

    //set timeout
    socket_set_option($clientSocket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 0, 'usec' => 500000));
    socket_set_option($clientSocket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 500000));
            
    getNode:
    $nodeList = [
        0=>[
            'ip'=>'127.0.0.1',
            'port'=>5000
        ]
    ];


    $cmdObj = new CMD();
    getCmd:
        //--------------Go to database to get cmdList
        $cmdList = [];
        $cmdObj->getCmdList(5);  //get a block of 5 cmds from t_cmdque
        if($cmdObj->rslt == 'fail') {
            throw new Exception($cmdObj->rslt.": ".$cmdObj->reason, 10);
        }
        $cmdList = $cmdObj->rows;

        //get the starting time 
        $startTime = microtime(true);
        /////////////////////////////////////////////

    sendCmd:  //send cmds blocks to cpsloop
        for($k=0; $k<count($nodeList); $k++) {
            if((microtime(true) - $startTime) < 5) { 
                echo "\nSending: STATUS CMD FOR NODE $k\n";

                //send cmd to CPS loop
                $cmd = "\$STATUS,SOURCE=ALL,ACKID=$k-CPS"; 
                $sendCmd = socket_sendto($clientSocket,$cmd, 1024,0, $nodeList[$k]['ip'], $nodeList[$k]['port']);
                if($sendCmd === false) {
                    throw new Exception("fail: ".socket_strerror(socket_last_error($clientSocket)), 15);
                }
            }
        }

        for($i = 0; $i<count($cmdList); $i++) {
            //if time counter doesn't over 5 seconds
            if((microtime(true) - $startTime) < 5) { 
                echo "\nSending: ".$cmdList[$i]['cmd']."\n";

                //send cmd to CPS loop
                $nodeId = $cmdList[$i]['node'];
                $sendCmd = socket_sendto($clientSocket,$cmdList[$i]['cmd'], 1024,0, $nodeList[$nodeId]['ip'], $nodeList[$nodeId]['port']);
                if($sendCmd === false) {
                    throw new Exception("fail: ".socket_strerror(socket_last_error($clientSocket)), 15);
                }
            }
        }

        usleep(100000);

    receiveRsp:  //keep listening to cpsloop

        while((microtime(true) - $startTime) < 5) {
            //receive response from CPS loop
            echo "Listening:\n";
            $rps = socket_recv($clientSocket, $buf, 2048, MSG_WAITALL);
            if($rps === false) {
                throw new Exception("fail: ".socket_strerror(socket_last_error($clientSocket)),15);
            }
            if($buf == '') {
                throw new Exception("fail: empty string return",15);
            }

            //display the response ($buf)
            echo "Receiving from cps:".$buf."\n";
            
            $ackid = ackidExtract($buf);
        
            //update response to cmdque table
            $cmdObj->updCmd($ackid, 'COMPL',$buf);
            if($cmdObj->rslt == 'fail') {
                throw new Exception($cmdObj->rslt.": ".$cmdObj->reason, 10);
            }

        }

        //makeup some fake data, because live CPS is down in the afternoon
        //receive response from CPS loop
        echo "Listening STATE:\n";
        $buf = "\$ackid=0-CPS,status,voltage1=43000mV,voltage2=43982mV,voltage3=43000mV,voltage4=43234mV,zone1=85C,zone2=89C,zone3=95C,zone4=100C,current=1600mA*";
        echo "Receiving from cps:".$buf."\n";
        
        $ackid = ackidExtract($buf);
        if($ackid == '0-CPS') {
            asyncPostRequest($localUrl."/ipcDispatch.php", ['user'=>'SYSTEM','api'=>'ipcAlm','act'=>'UPDATE_ALARM','cmd'=>"$buf"]);
        }

        
        goto getCmd;
}
catch (Throwable $t)
{   
    echo $t->getMessage()."\n";
    if($t->getCode() == 10) {
        return; //also mean that we close the socket
    }
    else if($t->getCode() == 15) {
        if(strpos($t->getMessage(),'Resource temporarily unavailable') !== false
            || strpos($t->getMessage(),'empty string return') !== false) {
                goto receiveRsp;
        }   
        else {
            socket_close($clientSocket);
            goto createClientSocket;
        }
            
    }
    else {
        return; //also mean that we close the socket
    }
    
}
//---------------------------End of socket area----------------------//////////
////////////////--------------supporting function---------------------//////////////
function ackidExtract($buf) {
    $bufExtract = substr($buf,1,strlen($buf) -2);
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

function asyncPostRequest($url, $params){
    $content = http_build_query($params);
    $parts=parse_url($url);
    echo $content."\n";
    // echo $parts."\n";
    $fp = fsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($content)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    echo "$out\n";
    if (isset($content)) 
        $out.= $content;
    fwrite($fp, $out);
    // while (!feof($fp)) {
    //     print_r(fgets($fp, 1024));
    // }
	fclose($fp);
}



?>