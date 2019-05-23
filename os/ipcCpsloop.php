<?php
//because this program needs to read /dev/ttyUSB file => www-data user need to be put in the group: dialout
//cmd structure to run this program:
//php ipcCpsloop.php node ip_port com_port
include 'ipcComPortClass.php';
include 'ipcCpsServerClass.php';
include 'ipcRspClass.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});
error_reporting(E_ALL);

//assign arguments
$node = $argv[1];
$ip_port = $argv[2];
// $com_port = $argv[3];

$baud = 115200;
$bits = 8;
$stop = 1;
$parity = 0;

$udp_timeoutSec = 0;
$udp_timeoutUsec = 0;

$serial_timeoutSec = 0;
$serial_timeoutUsec = 500000;

$RDWR_interval = 200000;

$lostConn = 3;

$connectHw = false;
$start_mode = false;

//-------------------------Begin--------------------------------
// define ERROR CODE
const SOCKET_API_FAIL = 1;
const SERIAL_CPS_HW_FAIL = 2;

try {
    $serverExist = false;
    $clientExist = false;
    $rspObjExist = false;
 
    serverSock: 
        //-------------to communicate with API (UDP type)-----------------
        echo "\ncreating UPD server.....\n";
        if($serverExist == false) {
            $cpsServerObj = new CPSSERVER("127.0.0.1", $ip_port, $udp_timeoutSec, $udp_timeoutUsec);
            if($cpsServerObj->rslt == 'fail') {   
                throw new Exception($cpsServerObj->rslt.": ".$cpsServerObj->reason,SOCKET_API_FAIL);
            }
            $cpsServerObj->setNonBlock();
            $serverExist = true;
        }

    clientSock:
        // ------------create new connection to CPS HW  (serial type)
        echo "\ncreating serial client....\n";
        if($clientExist == false && $com_port != NULL) {
            $comPortObj = new COMPORT($com_port,$baud, $bits, $stop, $parity, $serial_timeoutSec, $serial_timeoutUsec);
            if($comPortObj->rslt == 'fail') {   
                throw new Exception($comPortObj->rslt.":".$comPortObj->reason,SERIAL_CPS_HW_FAIL);
            }
            $clientExist = true;
        }

    createRspObj:
        //------create response object (to process response afterwards)------
        if($rspObjExist == false) {
            $rspObj = new RSP();
            $rspObjExist = true;
        }
    
    startSendCmd:
    while(1) {
        if($connectHw) {
            //------Send the status cmd and device cmd to HW-------
            echo "\nCPS loop sends the status cmd:\n";
            $rsp = $comPortObj->sendCmd("\$status,source=all,ackid=$node-cps*");
            if($comPortObj->rslt == 'fail') {   
                throw new Exception($comPortObj->rslt.":".$comPortObj->reason,SERIAL_CPS_HW_FAIL);
            }

            echo "\nCPS loop sends the device status cmd:\n";
            $rsp = $comPortObj->sendCmd("\$status,source=devices,ackid=$node-dev**");
            if($comPortObj->rslt == 'fail') {   
                throw new Exception($comPortObj->rslt.":".$comPortObj->reason,SERIAL_CPS_HW_FAIL);
            }
        }
       
       
        //initialize the cps connection status to default
        $cpsAlive = false;
        //get the starting time before go into 5sec-window
        $startTime = microtime(true);
        while((microtime(true) - $startTime) < 5) {
            // echo "\n----CPS loop is listening for comming cmd from API!----\n";
            // Wait for cmd from API, if timeout (errorCode =11), go back to listen. For other errorCode, throw an Exception 
            $input = socket_recvfrom($cpsServerObj->socket, $buf, 1024, 0, $remote_ip, $remote_port);
            if($input === false) {
                $errorCode = socket_last_error($cpsServerObj->socket);
                if($errorCode != 11)
                    throw new Exception("fail: ".socket_strerror($errorCode),SOCKET_API_FAIL);
            }

            //if cmd exists, send cmd to API
            //after that clean the $buf. sleep for a while before listening for response
            $cmd = trim($buf);
            if($cmd !== '') {
                echo "\n===CMD receive from API: ".$cmd."\n";
                if($cmd == 'start') {
                    $start_mode = true;
                }
                else if($cmd == 'stop') {
                    return;
                }
                else if(strpos($cmd,'com_port=') !== false) {
                    $dataExtract = explode('=',$cmd);
                    $com_port = $dataExtract[1];
                    $connectHw = true;
                    goto clientSock;
                }
                else {
                    if($connectHw) {
                        $comPortObj->sendCmd($cmd);
                        if($comPortObj->rslt == 'fail') {   
                            throw new Exception($comPortObj->rslt.":".$comPortObj->reason,SERIAL_CPS_HW_FAIL);
                        }
                    }  
                }
                   
                $buf = '';
            }
            usleep($RDWR_interval);

            //receive response from HW. 
            //if response exists, process the response and update cps connection status
            $rsp = $comPortObj->receiveRsp();
            if($rsp !== '') {
                if($start_mode)
                    $rspObj->processRsp($rsp, $node);
                if($cpsAlive == false) $cpsAlive = true;
            }
        }



        if($connectHw) {
            //when 5sec expires, check the cps communication status. Send post-request to API to declare alarm if needed
            // If receive a response from HW, reset the lostConn = 0, and process the response
            // If not receive any response from HW, increase the lostConn. If lostConn = 3, consider that HW communication is broken 
            if($cpsAlive == true) {
                if($lostConn > 0 && $lostConn < 3)
                    $lostConn = 0; 
                else if($lostConn >= 3)
                    $rspObj->asyncPostRequest(['user'=>'SYSTEM','api'=>'ipcNodeAdmin','act'=>'updateCpsCom','node'=>$node,'cmd'=>"$node-ONLINE"]);         
            }
            else {
                $lostConn++;
                if(($lostConn % 3) == 0) 
                    $rspObj->asyncPostRequest(['user'=>'SYSTEM','api'=>'ipcNodeAdmin','act'=>'updateCpsCom','node'=>$node,'cmd'=>"$node-OFFLINE"]);
            
            }
            //go back and send status command again
        }
        
    }
        
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
    else if($t->getCode() == SERIAL_CPS_HW_FAIL) {
        // If errorCode = 2, that means socket to HW is broken, close the socket and create a new one
        $comPortObj->endConnection();
        $clientExist = false;
        // $rspObj->asyncPostRequest(['user'=>'SYSTEM','api'=>'ipcNodeAdmin','act'=>'updateCpsCom','node'=>$node,'cmd'=>"$node-errorSerialCom"]);
        sleep(5);
        goto clientSock;
    }
    else if(strpos($t->getMessage(),'cannot open file') !== false) {
        $clientExist = false;
        // $rspObj->asyncPostRequest(['user'=>'SYSTEM','api'=>'ipcNodeAdmin','act'=>'updateCpsCom','node'=>$node,'cmd'=>"$node-errorSerialCom"]);
        sleep(5);
        goto clientSock;
    }
    else {
        // for other errorCode, exit the program
        return;
    }
    
}

?>