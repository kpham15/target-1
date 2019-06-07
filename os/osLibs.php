<?php

function rspExtract($rsp) {
    //get rid of line break character
    $rsp = preg_replace("/(\r\n|\n|\r)/",'',$rsp);
    //find position of 1st $ackid
    $begin = stripos($rsp,'$ackid');
    if($begin !== false) {
        //find position of * sign after the $ackid
        $stop = stripos($rsp,'*', $begin +1);
        if($stop !== false) {
            // $str[0] = $ackid.....*
            $str[] = substr($rsp,$begin, $stop - $begin + 1);
            // $str[1] =  the rest of the string
            $str[] = substr($rsp, $stop + 1);
            return $str;
        }
        else
            return false;
    } 
    else
        return false; 
}

?>