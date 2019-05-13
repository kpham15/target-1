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

    //create socket UDP
    $clientSocket = socket_create(AF_INET, SOCK_DGRAM, 0);
    if($clientSocket === false) {
        throw new Exception("fail: Unable to create socket", 10);
    }

    //set timeout
    socket_set_option($clientSocket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 0, 'usec' => 500000));
    socket_set_option($clientSocket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 500000));
            


    $cmdObj = new CMD();
    getCmd:
        //--------------Go to database to get cmdList
        $cmdList = [];
        $cmdObj->getCmdList();
        if($cmdObj->rslt == 'fail') {
            throw new Exception($cmdObj->rslt.": ".$cmdObj->reason, 10);
        }
        $cmdList = $cmdObj->rows;

        // start timer
        $startTime = microtime(true);
        /////////////////////////////////////////////
        sendCmd:
        for($i = 0; $i<count($cmdList); $i++) {
            //if time counter doesn't over 5 seconds
            if((microtime(true) - $startTime) < 5) { 
                echo "\nsending: ".$cmdList[$i]['cmd']."\n";

                //send cmd to CPS loop
                $sendCmd = socket_sendto($clientSocket,$cmdList[$i]['cmd'], 1024,0, $addressServer, $portServer);
                if($sendCmd === false) {
                    throw new Exception("fail: ".socket_strerror(socket_last_error($clientSocket)), 15);
                }
                //receive response from CPS loop
                $rps = socket_recv($clientSocket, $buf, 2048, MSG_WAITALL);
                if($rps === false) {
                    throw new Exception("fail: ".socket_strerror(socket_last_error($clientSocket)),15);
                }

                //display the response ($buf)
                echo $buf;
                
                //update response to cmdque table
                $cmdObj->updCmd($cmdList[$i]['ackid'], 'UNCOMPL',$buf);
                if($cmdObj->rslt == 'fail') {
                    throw new Exception($cmdObj->rslt.": ".$cmdObj->reason, 10);
                }

            }
        }
        // sleep until 5 secs
        if((5000000 - (microtime(true) - $startTime)*1000000) > 0)
            usleep(5000000 - (microtime(true) - $startTime)*1000000);
        goto getCmd;
}
catch (Throwable $t)
{   
    echo $t->getMessage()."\n";
    if($t->getCode() == 10) {
        return; //also mean that we close the socket
    }
    else if($t->getCode() == 15) {
        if((5000000 - (microtime(true) - $startTime)*1000000) > 0)
            usleep(5000000 - (microtime(true) - $startTime)*1000000);
        goto sendCmd;
    }
    else {
        // socket_close($clientSocket);
        return; //also mean that we close the socket
    }
    
}
    




?>