<?php
class RSP {
    public $url;

    public function __construct() {
        $fullpath = 'http://localhost';
        //get the directory of current file
        $directory = dirname(__FILE__);
        //split it into array
        $dirArray = explode("/",$directory);
        $htmlIndex = array_search("html",$dirArray);
        $osIndex = array_search("os",$dirArray);
        
        // build up the fullpath url
        for($i=($htmlIndex+1); $i<$osIndex; $i++) {
            $fullpath .= '/'.$dirArray[$i];
            
        }
        $fullpath .= '/em';
        $this->url =  $fullpath;
    }

    public function getAckid($rsp) {
        $rspExtract = substr($rsp,1,-1);
        $rspExtract = explode(',',$rspExtract);
        $ackid = explode('=', $rspExtract[0])[1];
        echo "\nackid received:$ackid\n";
        return $ackid;
    }
    
    // this function is to build and send a post request in required format 
    public function asyncPostRequest($params){
        $targetUrl = $this->url."/ipcDispatch.php";
        echo "\nurl API:".$targetUrl."\n";
        $content = http_build_query($params);
        $parts = parse_url($targetUrl);
        $fp = fsockopen($parts['host'],
            isset($parts['port'])?$parts['port']:80,
            $errno, $errstr, 30);
    
        $out = "POST ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($content)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
    
        if (isset($content)) 
            $out.= $content;
        echo $out."\n";
        fwrite($fp, $out);
        ///this part should be commented out when checking process is finished
        $result="";
        while (!feof($fp)) {
            print_r(fgets($fp, 1024));
        }
        //---------------------
        fclose($fp);
    }
    
    // this function is to process the response from HW. 
    // it extracts the ackid, to know where to send the post request
    // only process the response in the format: $.....*
    public function processRsp($rsp, $node) {

        $rsp = preg_replace("/(\r\n|\n|\r)/",'',$rsp);
        preg_match_all("/\\$.*?\*/", $rsp, $searchArray);
        $rspArray = $searchArray[0];
        print_r($rspArray);
        for($i=0; $i<count($rspArray); $i++) {
            $len = strlen($rspArray[$i]);
            if($rspArray[$i] !== '' && $rspArray[$i][0] == '$' && $rspArray[$i][$len-1] == '*') {
                if(stripos($rspArray[$i],'$ackid') !== false) {
                    echo "\nProcessing:".$rspArray[$i]."\n";
                    $ackid = $this->getAckid($rspArray[$i]);
                    if(trim($ackid) != '')
                        $this->asyncPostRequest(['user'=>'SYSTEM','api'=>'ipcNodeOpe','act'=>'EXEC_RESP','node'=>$node,'hwRsp'=>"$rspArray[$i]"]);

                }
            }
           
        }
    }

    public function getUuid($rsp) {
        $sn = '';
        $data = preg_replace("/(\r\n|\n|\r)/",'',$rsp);
        if(strpos($data,'uuid=') !== false) {
            preg_match_all("/\\$.*?\*/", $data, $searchArray);
            $rspArray = $searchArray[0];
            print_r($rspArray);
            for($i=0; $i<count($rspArray); $i++) {
                $len = strlen($rspArray[$i]);
                if($rspArray[$i] !== '' && $rspArray[$i][0] == '$' && $rspArray[$i][$len-1] == '*') {
                    if(stripos($rspArray[$i],'uuid=') !== false) {
                        echo "\nProcess this response to get sn:".$rspArray[$i]."\n";
                        $newHwString = substr($rspArray[$i], 1, -1);
                        $newHwStringArray = explode(",", $newHwString);
                        $serialNumArray = explode("=", $newHwStringArray[3]);
                        $sn = $serialNumArray[1];
                    }
                }
               
            }
        }

        return $sn; 
    }
    
}




?>




