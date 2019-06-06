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
    
    // this function is to build and send a post request in required format 
    public function asyncPostRequest($params){
        $targetUrl = $this->url."/ipcDispatch.php";
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
        fwrite($fp, $out);
        ///this part should be commented out when checking process is finished
        $result="";
        while (!feof($fp)) {
            $result = fgets($fp, 1024);
        }
        echo "result:".$result;
        //---------------------
        fclose($fp);
    }
    
    // this function is to process the response from HW. 
    // it extracts the ackid, to know where to send the post request
    // only process the response in the format: $.....*
    public function processRsp($rsp, $node) {

        $this->asyncPostRequest(['user'=>'SYSTEM','api'=>'ipcNodeOpe','act'=>'EXEC_RESP','node'=>$node,'hwRsp'=>"$rsp"]);
                
    }

    
}

$rspObj = new RSP();
$rspObj->processRsp("\$ackid=1-cps-csta,status,current=1239mA,temperature=45678mV,backplane=IAMAMIOXUUIDTHATYOUCANTDECODE*",1);




?>




