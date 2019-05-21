<?php

class DEV {
    
    private $id = 0;
    private $node = 0;
    private $miox = "";
    private $mioy = "";
    private $mre = "";
    private $cps = "";

    public $rslt;
    public $reason;
    public $rows;

    // constucts and query based on node to fill member variables
	public function __construct($node=NULL) {
        global $db;

        if ($node === NULL) {
            $this->rslt = FAIL;
            $this->reason = "MISSING NODE";
            return;
        }

        $qry = "SELECT * FROM t_devices WHERE node = '$node'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt    = FAIL;
            $this->reason  = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->id      = $rows[0]["id"];
                $this->node    = $rows[0]["node"];
                $this->miox    = $rows[0]["miox"];
                $this->mioy    = $rows[0]["mioy"];
                $this->mre     = $rows[0]["mre"];
                $this->cps     = $rows[0]["cps"];
            
                $this->rslt    = SUCCESS;
                $this->reason  = "DEVICE CONSTRUCTED";
                
            }
            else {
                $this->rslt    = FAIL;
                $this->reason  = "INVALID NODE";
            }
            $this->rows = $rows;
        }
    }

    public function parseDevString($device_status) {
        //$ackid=1-dev,status,devices,miox=11111111111111111111,mioy=11111111111111111111,mre=11111111111111111111,cps=11*

        $newCmd = substr($device_status, 1, -1);
        $splitCmd = explode(',', $newCmd);

        ["ackid=1-dev","status","devices","miox=", "mioy=","mre=","cps="];

        for ($i = 0; $i < count($splitCmd); $i++) {
            if (strpos($splitCmd[$i], "ackid") !== false) {
                if (strpos($splitCmd[$i], "dev") == false) {
                    $this->rslt = FAIL;
                    $this->reason = "INVALID ACKID";
                    return;
                }
                $ackidArray = explode('=', $splitCmd[$i]);
                $nodeArray = explode('-', $ackidArray[1]);
                $ackid =  $nodeArray[0];
            }
            else if (strpos($splitCmd[$i], "miox") !== false
                  || strpos($splitCmd[$i], "mioy") !== false
                  || strpos($splitCmd[$i], "mre") !== false) {
                $pcbArray = explode('=', $splitCmd[$i]);
                if (strlen($pcbArray[1]) !== 20) {
                    $this->rslt = FAIL;
                    $this->reason = "PCB IS NOT 20 CHARACTERS";
                    return;
                }

                if ($pcbArray[0] == 'miox') {
                    $miox = $pcbArray[1];
                } else if ($pcbArray[0] == 'mioy') {
                    $mioy = $pcbArray[1];
                } else if ($pcbArray[0] == 'mre') {
                    $mre = $pcbArray[1];
                }
            }
            else if (strpos($splitCmd[$i], "cps") !== false) {
                $cpsArray = explode('=', $splitCmd[$i]);
                $cps = $cpsArray[1];
            }
        }

        $parsedData = [
            "node" => $ackid,
            "miox" => $miox,
            "mioy" => $mioy,
            "mre" => $mre,
            "cps" => $cps
        ];
        return $parsedData;
    }

    // get functions to return stored values

    public function getMiox() {
        return $this->miox;
    }

    public function getMioy() {
        return $this->mioy;
    }

    public function getMre() {
        return $this->mre;
    }
    public function getCps() {
        return $this->cps;
    }

    // set functions to update t_devices

    public function setMiox($newMiox) {
        global $db;
          
        $qry = "UPDATE t_devices SET miox='$newMiox' WHERE node='$this->node'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }

        $this->rslt = SUCCESS;
        $this->reason = "MIOX UPDATED";
        return;
    
    }

    public function setMioy($newMioy) {
        global $db;
    
        $qry = "UPDATE t_devices SET mioy='$newMioy' WHERE node='$this->node'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }

        $this->rslt = SUCCESS;
        $this->reason = "MIOY UPDATED";
        return;
        
    }

    public function setMre($newMre) {
        global $db;
        
        $qry = "UPDATE t_devices SET mre='$newMre' WHERE node='$this->node'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }

        $this->rslt = SUCCESS;
        $this->reason = "MRE UPDATED";
        return;
        
    }

    public function setCps($newCps) {
        global $db;
        
        $qry = "UPDATE t_devices SET cps='$newCps' WHERE node='$this->node'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }

        $this->rslt = SUCCESS;
        $this->reason = "CPS UPDATED";
        return;

    }


    public function getDevicePcb($device) {
        global $db; 

        $qry = "SELECT pcb FROM t_devices where node = '$this->node' AND device = '$device'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            $this->rows = [];
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }    
            }
            else {
                $this->rslt = FAIL;
                $this->reason = "NO PCB FOUND";
                return;
            }
            $this->rows = $rows;
            $this->rslt = SUCCESS;
            $this->reason = "DEVICE PCB RETRIEVED";
            return;
        }
    }

    private function compareDevicePcb($device, $pcb) {
        if ($device == 'miox') {
            if (strcmp($this->miox, $pcb) !== 0) {
                return false;
            }
        }
        else if ($device == 'mioy') {
            if (strcmp($this->mioy, $pcb) !== 0) {
                return false;
            }
        }
        else if ($device == 'mre') {
            if (strcmp($this->mre, $pcb) !== 0) {
                return false;
            }
        }
        else if ($device == 'cps') {
            if (strcmp($this->cps, $pcb) !== 0) {
                return false;
            }
        }
        return true;
    }

    public function updateDevicePcb($device, $pcb) {
        global $db;
        
        if ($this->compareDevicePcb($device, $pcb)) {
            $this->rslt = FAIL;
            $this->reason = $device . " - PCB STRINGS ARE THE SAME";
            return;
        }
        else {
            if ($device == "miox") {
                $newPcb = $this->miox;
            }
            else if ($device == "mioy") {
                $newPcb = $this->mioy;
            }
            else if ($device == "mre") {
                $newPcb = $this->mre;
            }

            $qry = "UPDATE t_devices SET pcb='$newPcb' WHERE node='$this->node' AND device='$device'";

            $res = $db->query($qry);
            if (!$res) {
                $this->rslt   = FAIL;
                $this->reason = mysqli_error($db);
                return;
            }

            $this->rslt = SUCCESS;
            $this->reason = "PCB UPDATED";
            return;
        }

    }




}


?>