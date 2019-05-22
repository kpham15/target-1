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
	public function __construct($node) {
        global $db;

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
                    $this->reason = "MATRIX STRING IS NOT 20 CHARACTERS";
                    return;
                }

                if (preg_match('/[^0-1]+/', $pcbArray[1]) == 1) {
                    $this->rslt = FAIL;
                    $this->reason = "CHARACTERS OTHER THAN 0 OR 1 IN MATRIX STRING";
                    return;                    
                }
                else {
                    if ($pcbArray[0] == 'miox') {
                        $miox = $pcbArray[1];
                    } else if ($pcbArray[0] == 'mioy') {
                        $mioy = $pcbArray[1];
                    } else if ($pcbArray[0] == 'mre') {
                        $mre = $pcbArray[1];
                    }
                }

            }
            else if (strpos($splitCmd[$i], "cps") !== false) {
                $cpsArray = explode('=', $splitCmd[$i]);
                if (strlen($cpsArray[1]) !== 2) {
                    $this->rslt = FAIL;
                    $this->reason = "CPS STRING IS NOT 2 CHARACTERS";
                    return;
                }

                if (preg_match('/[^0-1]+/', $cpsArray[1]) == 1) {
                    $this->rslt = FAIL;
                    $this->reason = "CHARACTERS OTHER THAN 0 OR 1 IN CPS STRING";
                    return;
                }
                else {
                    $cps = $cpsArray[1];
                }
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

        if (strlen($newMiox) !== 20) {
            $this->rslt = FAIL;
            $this->reason = "MIOX STRING IS NOT 20 CHARACTERS";
            return false;
        }
        
        if (preg_match('/[^0-1]+/', $newMiox) == 1) {
            $this->rslt = FAIL;
            $this->reason = "MIOX HAS INVALID CHARACTERS";
            return false;
        }
          
        $qry = "UPDATE t_devices SET miox='$newMiox' WHERE node='$this->node'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return true;
        }

        $this->rslt = SUCCESS;
        $this->reason = "MIOX UPDATED";
        return true;
    
    }

    public function setMioy($newMioy) {
        global $db;

        if (strlen($newMioy) !== 20) {
            $this->rslt = FAIL;
            $this->reason = "MIOY STRING IS NOT 20 CHARACTERS";
            return false;
        }

        if (preg_match('/[^0-1]+/', $newMioy) == 1) {
            $this->rslt = FAIL;
            $this->reason = "MIOY HAS INVALID CHARACTERS";
            return false;
        }
    
        $qry = "UPDATE t_devices SET mioy='$newMioy' WHERE node='$this->node'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return true;
        }

        $this->rslt = SUCCESS;
        $this->reason = "MIOY UPDATED";
        return true;
        
    }

    public function setMre($newMre) {
        global $db;

        if (strlen($newMre) !== 20) {
            $this->rslt = FAIL;
            $this->reason = "MRE STRING IS NOT 20 CHARACTERS";
            return false;
        }

        if (preg_match('/[^0-1]+/', $newMre) == 1) {
            $this->rslt = FAIL;
            $this->reason = "MRE HAS INVALID CHARACTERS";
            return false;
        }
        
        $qry = "UPDATE t_devices SET mre='$newMre' WHERE node='$this->node'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return true;
        }

        $this->rslt = SUCCESS;
        $this->reason = "MRE UPDATED";
        return true;
        
    }

    public function setCps($newCps) {
        global $db;

        if (strlen($newCps) !== 2) {
            $this->rslt = FAIL;
            $this->reason = "CPS STRING IS NOT 2 CHARACTERS";
            return false;
        }

        if (preg_match('/[^0-1]+/', $newCps) == 1) {
            $this->rslt = FAIL;
            $this->reason = "CPS HAS INVALID CHARACTERS";
            return false;
        }

        $qry = "UPDATE t_devices SET cps='$newCps' WHERE node='$this->node'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return true;
        }

        $this->rslt = SUCCESS;
        $this->reason = "CPS UPDATED";
        return true;

    }
}


?>