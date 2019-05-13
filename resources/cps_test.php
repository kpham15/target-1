<?php


    function createSocket() {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false)
        {
            echo "Can not create socket-1\n" . socket_strerror(socket_last_error()) . "\n";
            return null;
        }
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 2, 'usec' => 0));
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 2, 'usec' => 0));        
        $ip_addr = gethostbyname('8fde09f396e4.sn.mynetname.net');
        $ip_port = 8000;
        
        $conn = socket_connect($socket, $ip_addr, $ip_port);
        if ($conn === false)
        {
            echo "Can not connect-1\n";
            return null;
        }
        else
            return $socket;
    }
        

    // program begin:
    ob_implicit_flush(1);

    // 1) create and connect socket-1
    $socket = createSocket();
    if ($socket === null) {
        echo "can not connect socket\n";
        return;
    }

    //$cmd1 = "\$STATUS,SOURCE=VOLTAGE,ACKID=T-1*";
    $cmd1 = "\$STATUS,SOURCE=ALL,ACKID=T-1*";
    $cmd2 = "\$command,action=close,row=55,col=66,row=56,col=67,ackid=abc123*";
    $cmd1 = "\$command,action=close,begin_row=55,final_row=56,begin_col=66,final_col=66,ackid=peer*";
    $cmd2 = "\$command,action=close,row=55,col=66,row=56,col=67,ackid=abc123*";
    $cmd1 = "\$COMMAND,ACTION=TONEGEN,BUS=X,FREQUENCY=1000,AMPLITUDE=-10.0,DURATION=50[,ACKID=ABC123*";
    
    for ($i=0; $i<4; $i++) {
        if ($socket === null) {
            $socket = createSocket();
        }
       
        if ($i==0)
            $cmd = $cmd1;
        else {
            if ($cmd == $cmd1) {
                $cmd = $cmd2;
            }
            else
                $cmd = $cmd1;
        }

        //$cmd = $cmd1 . $i . '*';
        echo "sending $i: " . $cmd . "\n";
        $result = socket_write($socket, $cmd, strlen($cmd));
        if ($result === false) {
            echo "FAILED\n" . socket_strerror(socket_last_error()) . "\n";
            socket_close($socket);
            $socket = null;
            //$i = 0;
            continue;
        }
        echo "SUCCESS: " . $result . "\n";
        usleep(10000);

        echo "receiving $i: ";
        //$resp = socket_read($socket, 1024);
        $recv = null;
        $recv = @socket_read($socket, 4096, PHP_BINARY_READ);
        //$recv = socket_recv($socket, $resp,1024,0);
        if ($recv === false) {
            echo $recv . "\nFAIL\n" . socket_strerror(socket_last_error()) . "\n";
            return;
        }
        else {
            $ack = trim($recv);
            echo $ack . "\n\n";
            usleep(20000);
            //$cmd3 = "\$LOG";
            //echo "sending: " . $cmd3 . "\n";
            //$result = socket_write($socket, $cmd3, strlen($cmd3));
            //usleep(10000);
            $recv = null;
            $recv = @socket_read($socket, 4096, PHP_BINARY_READ);
            $resp = trim($recv);
            echo $resp . "\n\n";

        }
        echo "wait 3 sec\n\n";
        sleep(3);


    }
    echo "close socket\n";
    socket_close($socket);

?>