<?php

include 'ipcCpsClientClass_udp.php';
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

    //--------Get ip address of live CPS
    // $address = gethostbyname('8fde09f396e4.sn.mynetname.net');

    serverSock:  // to communicate with IPC loop (type UDP)
        $cpsServerObj = new CPSSERVER("127.0.0.1", 5000);
        if($cpsServerObj->rslt == 'fail') {   
            throw new Exception($cpsServerObj->rslt.":asfs ".$cpsServerObj->reason,12);
        }
        
    clientSock: // to communicate with CPS HW (type UDP)
        $cpsClientObj = new CPSCLIENT();
        if($cpsClientObj->rslt == 'fail') {   
            throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,12);
        }
        
        searchConn:
        while(true) {
            echo "\nCPS loop is listening for comming data from IPC loop!\n";

            //get CMD from IPC loop
            
            $input = socket_recvfrom($cpsServerObj->socket, $buf, 1024, 0, $remote_ip, $remote_port);
            if($input === false) {
                throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),15);
            }

            // $cmd = trim($input);
            $cmd = trim($buf);
            echo "CMD receive from IPC: ".$cmd."\n";

            //---------------send cmd to CPS HW--------------------

            $cpsClientObj->sendCommand($cmd, "192.168.0.101", 9000);
            if($cpsClientObj->rslt == 'fail') {
                throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
            }
            echo "Response from HW: ".$cpsClientObj->rps;

            //---------------send response back to IPC----------------
        
            echo "send back to :". $remote_ip."and ".$remote_port."\n";
            $sendRps = socket_sendto($cpsServerObj->socket,$cpsClientObj->rps, 1024,0, $remote_ip, $remote_port);
            if($sendRps === false) {
                throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),15);
            }
        }
    }
    catch (Throwable $t)
    {   
        echo $t->getMessage()."\n";
        if($t->getCode() == 12) {
            return;
        }
        else if($t->getCode() == 15) {
            goto searchConn;   
        }
        else if($t->getCode() ==16) {
            // $cpsClientObj->endConnection();
            // goto clientSock;
            goto searchConn;
        }
        // else if($t->getCode() == 16) {
        //     socket_close($processId);
        //     goto searchConn;   
        // }
        else {
            return;
        }
        
    }

?>