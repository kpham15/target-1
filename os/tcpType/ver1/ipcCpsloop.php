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

try {
    $serverExist = false;
    $clientExist = false;
    //--------------Get ip address of live CPS------------------
    $address = gethostbyname('8fde09f396e4.sn.mynetname.net');  

    serverSock: 
    //-------------to communicate with IPC loop (UDP type)-----------------
    echo "creating UPD server.....\n";
    if($serverExist == false) {
        $cpsServerObj = new CPSSERVER("127.0.0.1", 5000);
        if($cpsServerObj->rslt == 'fail') {   
            throw new Exception($cpsServerObj->rslt.": ".$cpsServerObj->reason,11);
        }
        $serverExist = true;
    }
   

    clientSock:
    // ------------create new connection to CPS HW  (TCP type)
    echo "creating TCP client....\n";
    if($clientExist == false) {
        $cpsClientObj = new CPSCLIENT($address, 8000, 0, 500000);
        if($cpsClientObj->rslt == 'fail') {   
            throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,12);
        }
        $clientExist = true;
    }
        
    searchConn:
    while(true) {
        echo "\nCPS loop is listening for comming data from IPC loop!\n";
        
        $input = socket_recvfrom($cpsServerObj->socket, $buf, 1024, 0, $remote_ip, $remote_port);
        if($input === false) {
            throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),15);
        }

        $cmd = trim($buf);
        echo "CMD receive from IPC: ".$cmd."\n";

        //---------------send cmd to CPS HW--------------------

        // usleep(500000);

        // $ackidRsp = "123456";
        $cpsClientObj->sendCommand($cmd);
        if($cpsClientObj->rslt == 'fail') {
            throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
        }

        usleep(100000);

        //1st receive message
        $cpsClientObj->receiveRsp();
        if($cpsClientObj->rslt == 'fail') {
            throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
        }
        echo "Response 1 from HW: ".$cpsClientObj->rps."\n";
        if(strpos($cpsClientObj->rps, '$ackid') == false) {
            //2nd receive message
            $cpsClientObj->receiveRsp();
            if($cpsClientObj->rslt == 'fail') {
                throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
            }
            echo "Response 2 from HW: ".$cpsClientObj->rps."\n";
        }
       

        //---------------send response back to IPC----------------
        //--------------process the received string
        $result = preg_split("/(\r\n|\n|\r)/",$cpsClientObj->rps);

        for($i=0; $i<count($result); $i++) {
            if(strpos($result[$i],'$ackid') !== false) {
                $sendRps = socket_sendto($cpsServerObj->socket,$result[$i], 1024,0, $remote_ip, $remote_port);
                // $sendRps = socket_sendto($cpsServerObj->socket,$ackidRsp, 1024,0, $remote_ip, $remote_port);
        
                if($sendRps === false) {
                    throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),15);
                }
            }
        }
        // usleep(500000);
        // echo "received response from HW\n";
        // $dataRsp = "DatafromHW";

        // $sendRps = socket_sendto($cpsServerObj->socket,$dataRsp, 1024,0, $remote_ip, $remote_port);

        // if($sendRps === false) {
        //     throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),15);
        // }
    }
}
catch (Throwable $t)
{   
    echo "\n".$t->getMessage()."\n";

    if($t->getCode() == 11) {
        //--------------End connection to ipcloop----------------------
        sleep(5);
        $serverExist = false;
        goto serverSock;
    }

    if($t->getCode() == 12) {
        //--------------End connection to CPS HW----------------------
        $cpsClientObj->endConnection();
        sleep(5);
        $clientExist = false;
        goto clientSock;
    }

    else if($t->getCode() == 15) {
        goto searchConn;   
    }
    else if($t->getCode() ==16 || strpos($t->getMessage(), "unable to write") !== false) {
        $cpsClientObj->endConnection();
        goto clientSock;
        // goto searchConn;
    }
    else {
        return;
    }
    
}

?>