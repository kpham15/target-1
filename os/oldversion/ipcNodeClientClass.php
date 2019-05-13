<?php
class NODECLIENT {
    public $rslt;
    public $reason;

    public $socket;
    public $nodeObj;

    public function __construct($nodeObj) {
        $this->nodeObj= $nodeObj;

        $socket = socket_create(AF_INET, SOCK_STREAM,SOL_TCP);
        if ($socket === false) {
            $this->rslt = FAIL;
            $this->reason = "Could not create socket";
            return;
        }
       
        // ini_set('default_socket_timeout',10); => not working
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 5, 'usec' => 0));
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 5, 'usec' => 0));

        echo "Socket of nodeClient ".$this->nodeObj->node." is created \n";

        $this->socket = $socket;
      
    }
     
    public function checkNodeStatus(){

        try{
            /////////////////---------Connect to server----------------//////////////////
            echo "Connecting to ".$this->nodeObj->ipadr." on port ".$this->nodeObj->ip_port."...\n";
            $result = socket_connect($this->socket, $this->nodeObj->ipadr, $this->nodeObj->ip_port);  
            if(!$result) {
                throw new Exception("Can not connect to node ".$this->nodeObj->node);
            }
            echo "Connect Successfully\n";

            ////////////////////----------------Send messages-------------------//////////////
            $message= "COM";
            $message = json_encode($message);
            
            $write = socket_write($this->socket, $message, strlen($message));
            
            /////////////////////-----------Read the reply and process------------////////////////
            $hwObj = json_decode(socket_read ($this->socket, 1024));

            //if go to this point, then COM between NODE vs server is ok
            $response = $this->updateCOM("ON");
            if ($response->rslt == 'fail'){
                throw new Exception($response->reason);
            }
 
            if($hwObj) {
                $this->updateStatus($hwObj, $this->nodeObj);
            }
            else {
                throw new Exception("There is no reply from COM".($this->nodeObj->node));
            }
        }
        catch (Exception $e) {
            if(strpos($e->getMessage(), 'unable to connect') !== FALSE ||
                strpos($e->getMessage(),"unable to read") !== FALSE || 
                strpos( $e->getMessage(), "unable to write") !== FALSE ||
                strpos( $e->getMessage(), "timed out") !== FALSE ||
                strpos( $e->getMessage(), "no reply") !== FALSE) {
                $response = $this->updateCOM("OFF");
                if ($response->rslt == 'fail'){
                    $this->rslt = 'fail';
                    $this->reason = $response->reason;
                    $this->echoError();
                }
            }
            $this->rslt = 'fail';
            $this->reason = $e->getMessage();
            return;
        }
        catch (ErrorException $e) {
            $this->rslt = 'fail';
            $this->reason = $e->getMessage();
            return;
        }
            
    }

    public function sendAlmStatus(){

        try{
            /////////////////---------Connect to server----------------//////////////////
            echo "Connecting to ".$this->nodeObj->ipadr." on port ".$this->nodeObj->ip_port."...\n";
            $result = socket_connect($this->socket, $this->nodeObj->ipadr, $this->nodeObj->ip_port);  
            if(!$result) {
                throw new Exception("Can not connect to node ".$this->nodeObj->node);
            }
            echo "Connect Successfully\n";

            ////////////////////----------------Send messages-------------------//////////////
            $message= "alm=".$this->nodeObj->alm;
            $message = json_encode($message);
            
            $write = socket_write($this->socket, $message, strlen($message));
            
            
        }
        catch (Exception $e) {
            if(strpos($e->getMessage(), 'unable to connect') !== FALSE ||
                strpos($e->getMessage(),"unable to read") !== FALSE || 
                strpos( $e->getMessage(), "unable to write") !== FALSE ||
                strpos( $e->getMessage(), "timed out") !== FALSE ||
                strpos( $e->getMessage(), "no reply") !== FALSE) {
                $response = $this->updateCOM("OFF");
                if ($response->rslt == 'fail'){
                    $this->rslt = 'fail';
                    $this->reason = $response->reason;
                    $this->echoError();
                }
            }
            $this->rslt = 'fail';
            $this->reason = $e->getMessage();
            return;
        }
        catch (ErrorException $e) {
            $this->rslt = 'fail';
            $this->reason = $e->getMessage();
            return;
        }
       
            
    }

    private function updateStatus($hw, $node) {
       
       $nodeUpdate = false;
        //check for differences
        $parameter = ['stat','alm', 'temp', 'volt','com', 'mx0', 'mx1', 'mx2','mx3','mx4','mx5','mx6','mx7','mx8','mx9',
        'my0', 'my1', 'my2','my3','my4','my5','my6','my7','my8','my9', 'mr0', 'mr1', 'mr2','mr3','mr4','mr5','mr6','mr7','mr8','mr9'];
    
        for($i=0; $i<count($parameter); $i++) {
            $attribute = $parameter[$i];

            if($hw->$attribute != $node->$attribute) {
                if(!$nodeUpdate) 
                    $nodeUpdate = true;

                if (strpos($attribute, 'mx') !== false || strpos($attribute, 'my') !== false) {
                    /// Ex: mx0 => matrix type MX, slot 1
                    $nodeId = $this->nodeObj->node;
                    $type= strtoupper(substr($attribute, 0, 2));
                    $slot= (int)substr($attribute, 2) + 1;
                    
                    $evt= $hw->$attribute;
                    //Update status for Mxc and Ports
                    if($evt == 'OUT') {
                        $act="remove";
                    }
                    else if ($evt == 'IN') {
                        $act="insert";
                    }
                    if(strpos($attribute, 'mx') !== false)
                        $shelf = 1;
                    if(strpos($attribute, 'my') !== false)
                        $shelf = 2;
                    $response = $this->updateMxc($act, $nodeId, $shelf, $slot, $type);
                    if ($response->rslt == 'fail'){
                        $this->rslt = $response->rslt;
                        $this->reason = $response->reason;
                        // return;
                        $this->echoError();
                    }

                    /// Report alarm
                    $src= $nodeId."-".$type."-".$slot;
                    $response = $this->reportAlm($src, $evt);
                    if ($response->rslt == 'fail'){
                        $this->rslt = $response->rslt;
                        $this->reason = $response->reason;
                        // return;
                        $this->echoError();
                    }
                   

                }

                else if ($attribute =="temp" || $attribute == "volt"){
                    $nodeId = $this->nodeObj->id;
                    $type= "CPS";
                    $slot = 1; 
                    $feature= strtoupper($attribute[0]);
                    $value = substr($hw->$attribute, 0, strlen($hw->$attribute)-1);

                    if ($attribute == "temp")
                        $src = $nodeId . "-" . $type . "-T";
                    else
                        $src = $nodeId . "-" . $type . "-V";

                    $evt = $feature."-".$value;
 
                    $response = $this->reportAlm($src, $evt);
                    if ($response->rslt == 'fail'){
                        $this->rslt = $response->rslt;
                        $this->reason = $response->reason;
                        // return;
                        $this->echoError();
                    }
                }

            }
        }

        ///Update t_nodes only there is differences
        if($nodeUpdate) {
            //update for t_nodes
            $response = $this->updateNode($hw);
            if($response->rslt == FAIL) {
                $this->rslt = $response->rslt;
                $this->reason = $response->reason;
                return;
            }
        }

    }

    private function queryNodeById() {
        global $globalDir;
        $url = $globalDir.'/ipcDispatch.php';
        $data = array('api' => "ipcNode", 'act' => "query", 'node' => $this->nodeObj->id, 'user'=>'system');

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $response = json_decode(file_get_contents($url, false, $context));
        return $response;
       
    }

    private function updateNode($hw) {
        global $globalDir;
        $url = $globalDir.'/ipcDispatch.php';

        $data = array('api' => "ipcNode", 'user'=>'system', 'act' => "update", 'node' => $this->nodeObj->node,
                    'volt'=>$hw->volt,'temp'=>$hw->temp,
                    'mx0'=>$hw->mx0, 'mx1'=>$hw->mx1, 'mx2'=>$hw->mx2, 'mx3'=>$hw->mx3, 'mx4'=>$hw->mx4, 'mx5'=>$hw->mx5, 'mx6'=>$hw->mx6, 'mx7'=>$hw->mx7, 'mx8'=>$hw->mx8, 'mx9'=>$hw->mx9,
                    'my0'=>$hw->my0, 'my1'=>$hw->my1, 'my2'=>$hw->my2, 'my3'=>$hw->my3, 'my4'=>$hw->my4, 'my5'=>$hw->my5, 'my6'=>$hw->my6, 'my7'=>$hw->my7, 'my8'=>$hw->my8, 'my9'=>$hw->my9,
                    'mr0'=>$hw->mr0, 'mr1'=>$hw->mr1, 'mr2'=>$hw->mr2, 'mr3'=>$hw->mr3, 'mr4'=>$hw->mr4, 'mr5'=>$hw->mr5, 'mr6'=>$hw->mr6, 'mr7'=>$hw->mr7, 'mr8'=>$hw->mr8, 'mr9'=>$hw->mr9
                );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $response = json_decode(file_get_contents($url, false, $context));
        return $response;
    }

    private function updateCOM($com) {
        global $globalDir;
        $url = $globalDir.'/ipcDispatch.php';

        $data = array('api' => "ipcNode", 'user'=>'system', 'act' => "UPDATE_COM", 'node' => $this->nodeObj->node,'com'=>$com);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $response = json_decode(file_get_contents($url, false, $context));
        return $response;
    }

    private function updateMxc($act, $nodeId,$shelf, $slot, $type) {
        global $globalDir;
        $url = $globalDir.'/ipcDispatch.php';
        $data = array('api' => "ipcMxc", 'user'=> 'system','act' => $act, 'node' => $nodeId, 'shelf'=>$shelf, 'slot'=> $slot, 'type'=>$type);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $response = json_decode(file_get_contents($url, false, $context));
        return $response;
    }

    private function reportAlm($src, $evt) {
        global $globalDir;
        $url = $globalDir.'/ipcDispatch.php';
        $data = array('api' => "ipcAlm", 'user'=>'system','act' => 'REPORT', 'src' => $src, 'evt'=> $evt);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $response = json_decode(file_get_contents($url, false, $context));
        return $response;
    }

    public function closeNodeClient() {
        socket_close($this->socket);
    }

    public function echoError() {
        echo $this->rslt.":".$this->reason."\n";
    }
}




?>