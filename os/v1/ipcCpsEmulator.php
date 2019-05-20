<?php
    $ip_addr = '192.168.0.101';
    $ip_port = 9000;

    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if($socket === false) {
        throw new Exception("fail: Unable to create socket",12);
    }
    
    $bind = socket_bind($socket, $ip_addr, $ip_port);
    if($bind === false) {
        throw new Exception("fail: ".socket_strerror(socket_last_error($socket)),12);
    }

    // start listening for connections
    $listen = socket_listen($socket);
    if($listen === false) {
        throw new Exception("fail: ".socket_strerror(socket_last_error($socket)),12);
    }

    echo "Server $ip_addr is listening on port $ip_port!";

    /////////////////////////////////////////////////////
    try {

        searchConn:
        while(true) {
            //connect to IPC
            $processId = socket_accept($socket);
            if($processId === false) {
                throw new Exception("fail: ".socket_strerror(socket_last_error($socket)),15);
            }
            socket_set_option($processId, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 0, 'usec' => 500000));
            socket_set_option($processId, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 500000));
            
            //get CMD from IPC
            $input = socket_read($processId, 1024);
            if($input === false) {
                throw new Exception("fail: ".socket_strerror(socket_last_error($processId)),16);
            }

            $cmd = trim($input);
            echo $cmd."\n";

            $rsp = "ACK-".$cmd;

            //---------------send response back to IPC----------------
            $sendrsp = socket_write($processId, $rsp, strlen($rsp));
            if($sendrsp === false) {
                throw new Exception("fail: ".socket_strerror(socket_last_error($processId)),16);
            }
            socket_close($processId);
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
        else if($t->getCode() == 16) {
            socket_close($processId);
            goto searchConn;   
        }
        else {
            return;
        }
        
    }

?>