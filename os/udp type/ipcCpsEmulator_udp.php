<?php
    $ip_addr = '192.168.0.101';
    $ip_port = 9000;

    $socket = socket_create(AF_INET, SOCK_DGRAM, 0);
    if($socket === false) {
        throw new Exception("fail: Unable to create socket",12);
    }
    
    $bind = socket_bind($socket, $ip_addr, $ip_port);
    if($bind === false) {
        throw new Exception("fail: ".socket_strerror(socket_last_error($socket)),12);
    }

    /////////////////////////////////////////////////////
    try {

        searchConn:
        while(true) {
            echo "\nHW is listening for comming data!\n";

            $input = socket_recvfrom($socket, $buf, 1024, 0, $remote_ip, $remote_port);
            if($input === false) {
                throw new Exception("fail: ".socket_strerror(socket_last_error($socket)),15);
            }

            $cmd = trim($buf);
            echo $cmd."\n";

            $rps = "ACK-".$cmd;
            usleep(500000); /// time CPS takes for getting HW information
            //---------------send response back to IPC----------------
            $sendRps = socket_sendto($socket, $rps, 1024, 0, $remote_ip, $remote_port);
            if($sendRps === false) {
                throw new Exception("fail: ".socket_strerror(socket_last_error($socket)),15);
            }
        }
    }
    catch (Throwable $t)
    {   
        echo $t->getMessage()."\n";
        if($t->getCode() == 12) {
            return; //also mean that we close the socket
        }
        else if($t->getCode() == 15) {
            goto searchConn;   
        }
        
        else {
            return; //also mean that we close the socket
        }
        
    }

?>