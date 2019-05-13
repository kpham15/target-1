<?php
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false)
{
    echo "Can not create socket-1\n" . socket_strerror(socket_last_error()) . "\n";
    return null;
}
//socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 0, 'usec' => 0));
socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 0));        
$ip_addr = '192.168.1.148';
$ip_port = 9000;

$conn = socket_connect($socket, $ip_addr, $ip_port);
if ($conn === false)
{
    echo "Can not connect-1\n" . socket_strerror(socket_last_error()) . "\n";
    return;
}


echo "connect successful: " . $conn . "\n";
$cont = true;

    
    for ($i=0; $i<4; $i++) {
        $cmd = "\$STATUS,SOURCE=ALL,ACKID=1-CPS" . $i . "*";
        echo "sending: " . $cmd . "\n";
        $result = socket_write($socket, $cmd, strlen($cmd));
        if ($result === false) {
            echo "FAILED\n" . socket_strerror(socket_last_error()) . "\n";
            socket_close($socket);
            $socket = null;
            //$i = 0;
            return;
        }
        echo "SUCCESS\n" . $result . "\n";

        usleep(100000);

        echo "receiving: ";
        //$resp = socket_read($socket, 1024);
        $recv = null;
        $recv = @socket_read($socket, 4096, PHP_BINARY_READ);
        //$recv = socket_recv($socket, $resp,1024,0);
        if ($recv === false) {
            echo $recv . "\nFAIL\n" . socket_strerror(socket_last_error()) . "\n";
            return;
        }
        else {
            $resp = trim($recv);
            echo $resp . "\n\n";
            echo "wait 3 sec\n\n";
            sleep(3);
        }
    }

return;




?>