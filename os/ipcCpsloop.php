<?php

//change working dir to the file location
chdir(__DIR__);

//because this program needs to read /dev/ttyUSB file => www-data user need to be put in the group: dialout
//cmd structure to run this program: php ipcCpsloop.php node ip_port
include 'ipcComPortClass.php';
include 'ipcCpsServerClass.php';
include 'ipcRspClass.php';
include '../class/ipcDebugClass.php';

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
$com_port = '';

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

$discover_mode = false;
$start_mode = false;

$statusCmd = '';
$cpsCmd='';
$sn='';

//-------------------------Begin--------------------------------
// define ERROR CODE
const SOCKET_API_FAIL = 1;
const SERIAL_CPS_HW_FAIL = 2;

try {
    $serverExist = false;
    $clientExist = false;
    $rspObjExist = false;

    $debugObj = new DEBUG();
 
    serverSock: 
        //-------------to communicate with API (UDP type)-----------------
        if($serverExist == false) {
            echo "\ncreating UPD server.....\n";
            $cpsServerObj = new CPSSERVER("127.0.0.1", $ip_port, $udp_timeoutSec, $udp_timeoutUsec);
            if($cpsServerObj->rslt == 'fail') {   
                throw new Exception($cpsServerObj->rslt.": ".$cpsServerObj->reason,SOCKET_API_FAIL);
            }
            $cpsServerObj->setNonBlock();
            $serverExist = true;
        }

    clientSock:
        // ------------create new connection to CPS HW  (serial type)
        if($clientExist == false && $com_port != null) {
            echo "\ncreating serial client....\n";
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
        if($start_mode && $statusCmd != '') {
            //------Send the status cmd and device cmd to HW-------
            echo "\nCPS loop sends the status cmd:\n";
            $rsp = $comPortObj->sendCmd($statusCmd);
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
            $udpMsg = trim($buf);
            $udpMsgArr=[];
            if($udpMsg !== '') {
                echo "\n===CMD receive from API: ".$udpMsg."\n";
                $udpMsgArr = processUDPmsg($udpMsg);
                echo "Convert updmsg to array:\n";print_r($udpMsgArr);echo "\n";
                if ($discover_mode && array_key_exists("cmd",$udpMsgArr))
                    $cpsCmd .= $udpMsgArr['cmd'];
                if($udpMsgArr['inst'] == 'DISCV_CPS') {
                    if($udpMsgArr['node'] == $node) {
                        $com_port = $udpMsgArr['dev'];
                        if($clientExist) {
                            $comPortObj->endConnection();
                            $clientExist = false;
                        }
                        $discover_mode = true;
                        $start_mode = false;
                        //just for now, chu Ninh want to replace backplane to miox, cause backplane is not ready yet
                        $udpMsgArr['cmd'] = str_replace('backplane','miox',$udpMsgArr['cmd']);
                        $cpsCmd .= $udpMsgArr['cmd'];
                        echo "Cmd changed to:".$udpMsgArr['cmd'];
                        $buf = '';
                        goto clientSock;
                    }
                }
                else if($udpMsgArr['inst'] == 'START_CPS') {
                    if($sn != '' && $udpMsgArr['sn'] == $sn) {
                        $start_mode = true;
                        $statusCmd = $udpMsgArr['cmd'];
                        $buf = '';
                        goto clientSock;
                    }
                }
                else if($udpMsgArr['inst'] == 'STOP_CPS') {
                    if($udpMsgArr['sn'] == $sn ) {
                        $com_port = '';
                        $sn = '';
                        if($clientExist) {
                            $comPortObj->endConnection();
                            $clientExist = false;
                        }
                        $discover_mode = false;
                        $start_mode = false;
                        $buf = '';
                        $cpsCmd = '';
                        goto clientSock;
                    }
                }   
            }

            if($discover_mode) {
                if($cpsCmd != '') {
                    $comPortObj->sendCmd($cpsCmd);
                    if($comPortObj->rslt == 'fail') {   
                        throw new Exception($comPortObj->rslt.":".$comPortObj->reason,SERIAL_CPS_HW_FAIL);
                    }
                    $cpsCmd = '';
                } 
            }  

            $buf = '';
            usleep($RDWR_interval);

            //receive response from HW. 
            //if response exists, process the response and update cps connection status
            if($discover_mode) {
                $rsp = $comPortObj->receiveRsp();
                if($rsp !== '') {        
                    $lostConn = 0;
                    if($cpsAlive == false) $cpsAlive = true;
                    //serial number is retrieved in discover_mode, not in start_mode
                    if($start_mode == false) {
                        $sn = $rspObj->getUuid($rsp);
                        if($sn != '') {
                            echo "\nSerial number: $sn\n";
                        }
                    }
                   
                    $rspObj->processRsp($rsp, $node);
                }
            
            }
           
        }



        if($start_mode) {
            //when 5sec expires, check the cps communication status. Send post-request to API to declare alarm if needed
            // If not receive any response from HW, increase the lostConn. If lostConn = 3, consider that HW communication is broken 
            echo "\nlostconn:".$lostConn."\n";
            if($cpsAlive !== true) {
                $lostConn++;
                if($lostConn >=3)
                    $rspObj->asyncPostRequest(['user'=>'SYSTEM','api'=>'ipcNodeOpe','act'=>'CPS_OFF','node'=>$node]);
            
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
        if($clientExist == true) {
            $comPortObj->endConnection();
            $clientExist = false;
        }
        $rspObj->asyncPostRequest(['user'=>'SYSTEM','api'=>'ipcNodeAdmin','act'=>'updateCpsCom','node'=>$node,'cmd'=>"$node-errorSerialCom"]);
        $lostConn = 3;
        sleep(5);
        goto clientSock;
    }
    else if(strpos($t->getMessage(),'cannot open file') !== false || strpos($t->getMessage(),'cannot write data to file descriptor') !== false) {
        if($clientExist == true) {
            $comPortObj->endConnection();
            $clientExist = false;
        }
        $rspObj->asyncPostRequest(['user'=>'SYSTEM','api'=>'ipcNodeOpe','act'=>'CPS_OFF','node'=>$node]);
        $lostConn = 3;
        sleep(5);
        goto clientSock;
    }
    else {
        // for other errorCode, exit the program
        return;
    }
    
}


//////////////////////////////////////////////////////////////
function processUDPmsg($buf) {
    $data = [];
    $index = stripos($buf,'cmd=');
    if($index !== false) {
        $inst = substr($buf, 0, $index);
        $cmdString = substr($buf, $index+4, strlen($buf) - $index -4);
    }
    else {
        $inst = $buf;
        $cmdString='';
    }
    $instArr = explode(',',$inst);
    foreach($instArr as $parameter) {
        if(trim($parameter) == '') continue;
        $paraExtract = explode('=',$parameter);
        if($paraExtract[0] == 'inst') 
            $data['inst'] = $paraExtract[1];
        else if($paraExtract[0] == 'node') 
            $data['node'] = $paraExtract[1];
        else if($paraExtract[0] == 'dev') 
            $data['dev'] = $paraExtract[1];
        else if($paraExtract[0] == 'sn') 
            $data['sn'] = $paraExtract[1];
    }
    $data['cmd']= $cmdString;
    return $data;
}

?>