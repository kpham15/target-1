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

    //set timeout
    socket_set_option($clientSocket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 0, 'usec' => 500000));
    socket_set_option($clientSocket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 500000));
    
    $cmd = "\$command,action=disconnect,bus=x,tap=1,ackid=1-tbus*";
    echo "\nSending....$cmd\n";
    $sendCmd = socket_sendto($clientSocket,$cmd, 1024,0, '127.0.0.1', $argv[2]);
    if($sendCmd === false) {
        throw new Exception("fail: ".socket_strerror(socket_last_error($clientSocket)), 15);
    }
}
catch(Throwable $t) {

}




?>