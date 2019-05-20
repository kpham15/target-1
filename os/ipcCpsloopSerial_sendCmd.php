<?php

include 'ipcCpsClientSerialClass.php';
include 'ipcCpsServerClass.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

error_reporting(E_ALL);

$node = $argv[1];
$nodeStat = [
    'fail_count'=>1,
];

//-------------------------Begin--------------------------------
// define ERROR CODE
const SOCKET_API_FAIL = 1;
const SERIAL_CPS_HW_FAIL = 2;

try {
    $serverExist = false;
    $clientExist = false;
 
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
        // ------------create new connection to CPS HW  (Serial type)
        echo "\ncreating TCP client....\n";
        if($clientExist == false) {
            $cpsClientObj = new CPSCLIENT('dev/ttyS10', 0, 500000);
            if($cpsClientObj->rslt == 'fail') {   
                throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,SERIAL_CPS_HW_FAIL);
            }
            $clientExist = true;
        }
        

    while(1) {
        //------Send the status cmd-------
        $cmd = "\$STATUS,SOURCE=ALL,ACKID=$node-CPS*"; 
        $clientObj->sendCmd($cmd);
        if($clientObj->rslt == 'fail') {
            throw new Exception("fail: ".$clientObj->reason,SERIAL_CPS_HW_FAIL);
        }

        //get the starting time 
        $startTime = microtime(true);
        
        //listening for cmd from api
        while(microtime(true) - $startTime <5) {
            echo "\n----------CPS loop is listening for comming data from API!\n";
            // Wait for cmd from API, if timeout (errorCode =11), go back to listen. For other errorCode, throw an Exception 
            $input = socket_recvfrom($cpsServerObj->socket, $buf, 1024, 0, $remote_ip, $remote_port);
            if($input === false) {
                $errorCode = socket_last_error($cpsServerObj->socket);
                if($errorCode != 11)
                    throw new Exception("fail: ".socket_strerror($errorCode),SOCKET_API_FAIL);
                else    
                    continue;
            }

            $cmd = trim($buf);
            echo "\n==========CMD receive from API: ".$cmd."\n";

            // send cmd to HW and receive reply from HW
            $clientObj->sendCmd($cmd);
            if($clientObj->rslt == 'fail') {
                throw new Exception("fail: ".$clientObj->reason,SERIAL_CPS_HW_FAIL);
            }

            usleep(100000);
        }
        // when 5s expires, go back and send status command again

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
        // If errorCode = 2, that means serial connection to HW is broken, close the connection and create a new one
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

?>