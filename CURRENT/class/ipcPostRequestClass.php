<?php

class POST_REQUEST {
    public $url = '';
    public $reply = '';

    public function __construct() {
        $this->url = $this->buildUrl();
    }

    private function buildUrl() {
        // $fullpath = "/var/www/html/target-1/UPDATE/em";
        $fullpath = 'http://localhost';
        //get the directory of current file
        $directory = dirname(__FILE__);
        //split it into array
        $dirArray = explode("/",$directory);
        $htmlIndex = array_search("html",$dirArray);
        $osIndex = array_search("class",$dirArray);
        
        // build up the fullpath url
        for($i=($htmlIndex+1); $i<$osIndex; $i++) {
            $fullpath .= '/'.$dirArray[$i];
            
        }
        $fullpath .= '/em';
        return $fullpath;
    }
    
    // this function is to build and send a post request in required format 
    public function asyncPostRequest($url, $params){
        $url = $this->url."/$url";
        $content = http_build_query($params);
        $parts=parse_url($url);
        
        // echo $parts."\n";
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
        // echo $out."\n";
        fwrite($fp, $out);
        // while (!feof($fp)) {
        //     $this->reply = fgets($fp, 1024);
        // }
        fclose($fp);
    }
    
    public function syncPostRequest($url, $params) {
        $this->reply = "";
        $url = $this->url."/$url";    
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($params)
            )
        );
        $context  = stream_context_create($options);
        $this->reply = json_decode(file_get_contents($url, false, $context));
        return $this->reply;
    }
}

?>