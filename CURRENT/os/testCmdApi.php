<?php

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

error_reporting(E_ALL);

try{
    //create socket UDP
    $clientSocket = socket_create(AF_INET, SOCK_DGRAM, 0);
    if($clientSocket === false) {
        throw new Exception("fail: Unable to create socket", 10);
    }

    // //set timeout
    // socket_set_option($clientSocket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 0, 'usec' => 500000));
    // socket_set_option($clientSocket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 500000));
    $msg = "inst:send;node:1;sn:;cmd:\$status,source=temperature,ackid=1-cps*";
    //$msg = "inst:discover;node:1;sn:;cmd:\$status,source=uuid,device=miox1,ackid=1-sn*";
    //$msg = "\$command,action=disconnect,bus=x,tap=1,ackid=1-tbus*";
    //$msg = "inst=DISCV_CPS,node=1,dev=ttyUSB0,sn=,cmd=\$status,source=device,ackid=1-dev*";
    // $msg = "inst=START_CPS,sn=IAMAMIOXUUIDTHATYOUCANTDECODE,cmd=\$status,source=all,ackid=1-CPS*\$status,source=devices,ackid=1-nadm-unds*";
    // $msg = "inst=STOP_CPS,sn=IAMAMIOXUUIDTHATYOUCANTDECODE";


    echo "\nSending....$msg\n";
    $sendCmd = socket_sendto($clientSocket,$msg, 1024,0, '127.0.0.1', 9000);
    if($sendCmd === false) {
        throw new Exception("fail: ".socket_strerror(socket_last_error($clientSocket)), 15);
    }
}
catch(Throwable $t) {

}




?>