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
        if($serverExist == false) {
            $cpsServerObj = new CPSSERVER("127.0.0.1", 5000);
            if($cpsServerObj->rslt == 'fail') {   
                throw new Exception($cpsServerObj->rslt.": ".$cpsServerObj->reason,11);
            }
            $serverExist = true;
        }
       

        clientSock:
        // ------------create new connection to CPS HW  (TCP type)
        // if($clientExist == false) {
        //     $cpsClientObj = new CPSCLIENT($address, 8000, 0, 500000);
        //     if($cpsClientObj->rslt == 'fail') {   
        //         throw new Exception($cpsClientObj->rslt.":".$cpsClientObj->reason,12);
        //     }
        //     $clientExist = true;
        // }
      

        searchConn:
         // start timer
        $remote_ip = "";
        $remote_port = 0;
        $startTime = microtime(true);
        
            for($i=0; $i < 5; $i++) {
                echo "\n\nCPS loop is listening for comming data from IPC loop!\n";
                
                $input = socket_recvfrom($cpsServerObj->socket, $buf, 1024, 0, $remote_ip, $remote_port);
                if($input === false) {
                    throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),11);
                }

                $cmd = trim($buf);
                echo "CMD receive from IPC: ".$cmd."\n";

                //---------------send cmd to CPS HW--------------------
                // $cpsClientObj->sendCommand($cmd);
                // if($cpsClientObj->rslt == 'fail') {
                //     throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),12);
                // }
            }
            readRspFromHW:
            // while((microtime(true) - $startTime) < 5) {
            //     $cpsClientObj->receiveRsp();
            //     // if($cpsClientObj->rslt == 'fail') {
            //     //     throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),14);
            //     // }
            //     if($cpsClientObj->rps == '') {
            //         $sendRps = socket_sendto($cpsServerObj->socket,'', 1024,0, $remote_ip, $remote_port);
            //         if($sendRps === false) {
            //             throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),11);
            //         }
            //         goto searchConn;
            //     }

            //     echo $cpsClientObj->rps."\n";
            //     echo "Send data back to $remote_ip port:$remote_port\n";
            //     //---------------send response back to IPC----------------
            //     $sendRps = socket_sendto($cpsServerObj->socket,$cpsClientObj->rps, 1024,0, $remote_ip, $remote_port);
            //     if($sendRps === false) {
            //         throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),11);
            //     }
            // }

            // $sendRps = socket_sendto($cpsServerObj->socket,'', 1024,0, $remote_ip, $remote_port);
            // if($sendRps === false) {
            //     throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),11);
            // }
            goto searchConn;
            

    }
    catch (Throwable $t)
    {   
        echo $t->getMessage()."\n";
        if($t->getCode() == 11) {
            //--------------End connection to ipcloop----------------------
            $serverExist = false;
            goto serverSock;
        }

        if($t->getCode() == 12) {
            //--------------End connection to CPS HW----------------------
            $cpsClientObj->endConnection();
            $clientExist = false;
            goto clientSock;
        }

        if($t->getCode() == 14) {
            goto readRspFromHW;
        }
        // else if(strpos($t->getMessage(), "unable to write") !== false) {
        //     goto searchConn;
        // }
        else {
            return;
        }
        
    }

?>