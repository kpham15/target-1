<?php

    $pwr_status1 = ",current=1540mA,voltage=46000mV*";
    $pwr_status2 = ",current=1450mA,voltage=45000mV*";
    $volt_status1 = ",voltage1=45000mV,voltage2=45500mV,voltage3=46000mV,voltage4=465000mV*";
    $volt_status2 = ",voltage1=44000mV,voltage2=44500mV,voltage3=44000mV,voltage4=445000mV*";
    $temp_status1 = ",temperature,zone1=65C,zone2=66C,zone3=67C,zone4=68C*";
    $temp_status2 = ",temperature,zone1=60C,zone2=62C,zone3=63C,zone4=64C*";

    $rq_cnt = 0;

    // TCP Server
    error_reporting(E_ALL);
    
    /* Allow the script to hang around waiting for connections. */
    set_time_limit(0);
    
    /* Turn on implicit output flushing so we see what we're getting as it comes in. */
    ob_implicit_flush();
    $address = '192.168.0.4';
    $port = 9001;

    // create a streaming socket, of type TCP/IP
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
     
    socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
     
    socket_bind($sock, $address, $port);
     
    socket_listen($sock);
     
    // create a list of all connections from CO-server
    // add the listening socket to this list
    $clients = array($sock);
     
    while (true)
    {
        // create a copy, so $clients doesn't get modified by socket_select()
        $read = $clients;
        $write = null;
        $except = null;
     
        // get a list of all the connections that have data to be read from
        // if there are no clients with data, go to next iteration
        if (socket_select($read, $write, $except, 0) < 1)
            continue;
     
        // check if there is a client trying to connect
        if (in_array($sock, $read))
        {
            $clients[] = $newsock = socket_accept($sock);
     
            //socket_write($newsock, "There are ".(count($clients) - 1)." client(s) connected to the server\n");
     
            socket_getpeername($newsock, $ip, $port);
            echo "New connection: {$ip}\n";
     
            $key = array_search($sock, $read);
            unset($read[$key]);
        }
     
        // loop through all the connections that have data to read from
        foreach ($read as $read_sock)
        {
            // read until newline or 1024 bytes
            // socket_read while show errors when the client is disconnected, so silence the error messages
            $data = @socket_read($read_sock, 4096, PHP_BINARY_READ);
     
            // check if the client is disconnected
            if ($data === false)
            {
                // remove client for $clients array
                $key = array_search($read_sock, $clients);
                unset($clients[$key]);
                echo "client disconnected.\n";
                continue;
            }

            $data = trim($data);
            
            if (!empty($data))
            {
                echo " recv: {$data}\n";

                // send back ACK
                $ack_data = $data . "\n";
                socket_write($read_sock, $ack_data);
                echo " send ack: {$ack_data}\n";
                
                usleep(50000); // sleep 30 ms
                // send resonse message to listening socket
                $e = explode(',', $ack_data);
                $len = count($e);
                $ackid = $e[$len-1];
                $ackid = substr($ackid,0,strlen($ackid)-2);
                
                $rq_cnt++;
                if ($rq_cnt > 40)
                    $rq_cnt = 0;

                if ($rq_cnt < 20) {
                    $pwr_data = "\$" . $ackid . $pwr_status1 . "\n";
                    $volt_data = "\$" . $ackid . $volt_status1 . "\n";
                    $temp_data = "\$" . $ackid . $temp_status1 . "\n";
                }
                else {
                    $pwr_data = "\$" . $ackid . $pwr_status2 . "\n";
                    $volt_data = "\$" . $ackid . $volt_status2 . "\n";
                    $temp_data = "\$" . $ackid . $temp_status2 . "\n";
                }

                if ($e[0] == "\$STATUS" && $e[1] == "SOURCE=ALL") {
                    socket_write($read_sock, $pwr_data);
                    echo " send pwr: {$pwr_data}\n";
                    usleep(50000);

                    socket_write($read_sock, $volt_data);
                    echo " send volt: {$volt_data}\n";
                    usleep(50000);

                    socket_write($read_sock, $temp_data);
                    echo " send temp: {$temp_data}\n";
                    usleep(20000);
                    
                }


                // send this to all the clients in the $clients array (except the first one, which is a listening socket)
                // foreach ($clients as $send_sock)
                // {
                //     if ($send_sock == $sock)
                //         continue;
     
                //     socket_write($send_sock, $data);

                // } // end of broadcast foreach
            }

        } // end of reading foreach
    }

    // close the listening socket
    socket_close($sock);
    


?>
