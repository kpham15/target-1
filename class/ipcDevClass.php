<?php

class DEV {
    
    private $id = 0;
    private $node = 0;
    private $miox = "";
    private $mioy = "";
    private $mre = "";
    private $cps = "";

    public $nodes = 0;

    public $rslt;
    public $reason;
    public $rows;

	public function __construct($devString = NULL) {
        global $db;

        if ($devString === NULL) {
            $this->rslt = FAIL;
            $this->reason = "MISSING DEVICE STRING";
            return;
        }

        if ($this->parseDevString($devString) !== FAIL) {
            $this->rslt = SUCCESS;
            $this->reason = "SUCCESSFULLY PARSED DEVICE STRING";
        }
    

    
    }

    private function parseDevString($devString) {
        //$ackid=1-dev,status,devices,miox=11111111111111111111,mioy=11111111111111111111,mre=11111111111111111111,cps=11*

        $newCmd = substr($devString, 1, -1);
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
                $this->node =  $nodeArray[0];
            }
            else if (strpos($splitCmd[$i], "miox" || strpos($splitCmd[$i], "mioy") || strpos($splitCmd[$i], "mre")) !== false) {
                $pcbArray = explode('=', $splitCmd[$i]);
                if (strlen($pcbArray[1]) !== 20) {
                    $this->rslt = FAIL;
                    $this->reason = "PCB IS NOT 20 CHARACTERS";
                    return;
                }

                if ($pcbArray[0] == 'miox') {
                    $this->miox = $pcbArray[1];
                } else if ($pcbArray[0] == 'mioy') {
                    $this->mioy = $pcbArray[1];
                } else if ($pcbArray[0] == 'mre') {
                    $this->mre = $pcbArray[1];
                }
            }
            else if (strpos($splitCmd[$i], "cps") !== false) {
                $cpsArray = explode('=', $splitCmd[$i]);
                $this->cps = $cpsArray[1];
            }
        }




    }

    public function getDevicePcb($device) {
        global $db; 
        
        $qry = "SELECT pcb FROM t_devices where node = '$this->node' AND device = '$device'";

        $res = $db->query($qry);
        if (!res) {
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

            $qry = "UPDATE t_devices SET pcb='$pcb' WHERE node='$this->node' AND device='$device'";

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