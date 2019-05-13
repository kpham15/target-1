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
    // $address = gethostbyname('8fde09f396e4.sn.mynetname.net');  
 
    serverSock: 
    //-------------to communicate with IPC loop (UDP type)-----------------
    echo "\ncreating UPD server.....\n";
    if($serverExist == false) {
        $cpsServerObj = new CPSSERVER("127.0.0.1", $argv[2]);
        if($cpsServerObj->rslt == 'fail') {   
            throw new Exception($cpsServerObj->rslt.": ".$cpsServerObj->reason,11);
        }
        $serverExist = true;
    }
   

    clientSock:
    // ------------create new connection to CPS HW  (TCP type)
    echo "\ncreating TCP client....\n";
    if($clientExist == false) {
        $cpsClientObj = new CPSCLIENT($argv[1], $argv[2], 0, 500000);
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

        while(1) {
            $rsp='';
            $cpsClientObj->receiveRsp();
            if($cpsClientObj->rslt == 'fail') {
                throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
            }
         
            if($cpsClientObj->rsp === "") {
                break;
            }

            $rsp .= $cpsClientObj->rsp;
            echo "Response from HW: ".$cpsClientObj->rsp."\n";
            // // if(stripos($cpsClientObj->rsp, '$ackid') == false) {
            //     //2nd receive message
            //     $cpsClientObj->receiveRsp();
            //     if($cpsClientObj->rslt == 'fail') {
            //         throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
            //     }
            //     $rsp .= $cpsClientObj->rsp;
            //     echo "Response 2 from HW: ".$cpsClientObj->rsp."\n";
            // // }
    
            //  //3rd receive message
            //  $cpsClientObj->receiveRsp();
            //  if($cpsClientObj->rslt == 'fail') {
            //      throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
            //  }
            //  $rsp .= $cpsClientObj->rsp;
            //  echo "Response 3 from HW: ".$cpsClientObj->rsp."\n";
    
            // //4th receive message
            // $cpsClientObj->receiveRsp();
            // if($cpsClientObj->rslt == 'fail') {
            //     throw new Exception("fail: ".socket_strerror(socket_last_error($cpsClientObj->socket)),16);
            // }
            // $rsp .= $cpsClientObj->rsp;
            // echo "Response 4 from HW: ".$cpsClientObj->rsp."\n";
           
    
            //---------------send response back to IPC----------------
            //--------------process the received string
            $result = preg_split("/(\r\n|\n|\r)/",$rsp);
            // $result = [];
            // $index = 0;
            // while(($pos1 = stripos($rsp,'$',$index)) !== false) {
            //     echo "index:$index\n";
            //     echo "pos1:$pos1\n";
            //     $pos2 = stripos($rsp,'*',$pos1+1);
            //     $result[] = substr($rsp, $pos1, $pos2 - $pos1 +1);
            //     echo "pos2:$pos2\n";
            //     $index = $pos2;
            // }
            print_r($result);
    
            for($i=0; $i<count($result); $i++) {
                if(stripos($result[$i],'$ackid') !== false) {
                    $sendrsp = socket_sendto($cpsServerObj->socket,$result[$i], 1024,0, $remote_ip, $remote_port);
                    // $sendrsp = socket_sendto($cpsServerObj->socket,$ackidRsp, 1024,0, $remote_ip, $remote_port);
            
                    if($sendrsp === false) {
                        throw new Exception("fail: ".socket_strerror(socket_last_error($cpsServerObj->socket)),15);
                    }
                }
            }
        }
       
        // usleep(500000);
        // echo "received response from HW\n";
        // $dataRsp = "DatafromHW";

        // $sendrsp = socket_sendto($cpsServerObj->socket,$dataRsp, 1024,0, $remote_ip, $remote_port);

        // if($sendrsp === false) {
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
        $clientExist = false;
        sleep(5);
        goto clientSock;
    }

    else if($t->getCode() == 15) {
        goto searchConn;   
    }
    else if($t->getCode() ==16 || strpos($t->getMessage(), "unable to write") !== false) {
        $cpsClientObj->endConnection();
        $clientExist = false;
        sleep(5);
        goto clientSock;
        // goto searchConn;
    }
    else if(strpos($t->getMessage(), "unable to connect") !== false) {
        $clientExist = false;
        sleep(5);
        goto clientSock;
        // goto searchConn;
    }
    else {
        return;
    }
    
}

?>