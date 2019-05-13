<?php
class NODE {
    public $id      = 0;
    public $node    = 0;
    public $stat    = "";
    public $rack    = "";
    public $alm     = "";
    public $volt    = "";
    public $temp    = "";
    public $com     = "";
    public $ipadr   = "";
    public $gateway = "";
    public $netmask = "";
    public $ip_port = 0;
    public $psta    = "";
	public $ssta    = "";
	public $user	= "";
    
    public $nodes   = 0;

    public $scan    = "";
    public $pid     = "";

    public $rslt    = "";
    public $reason  = "";
    public $rows    = [];

    public function __construct($node) {
        global $db;

        $this->queryAll();
        $this->nodes = count($this->rows);
        
        $qry = "SELECT * FROM t_nodes WHERE node = '$node' LIMIT 1";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt     = SUCCESS;
                $this->reason   = QUERY_MATCHED;
                $this->rows     = $rows;
                $this->id       = $rows[0]['id'];
                $this->node     = $rows[0]['node'];
                $this->rack     = $rows[0]['rack'];
                $this->stat     = $rows[0]['stat'];
                $this->alm      = $rows[0]['alm'];
                $this->volt     = $rows[0]['volt'];
                $this->temp     = $rows[0]['temp'];
                $this->com      = $rows[0]['com'];
                $this->ipadr    = $rows[0]['ipadr'];
                $this->gateway  = $rows[0]['gateway'];
                $this->netmask  = $rows[0]['netmask'];
				$this->ip_port  = $rows[0]['ip_port'];
				$this->psta		= $rows[0]['psta'];
				$this->ssta		= $rows[0]['ssta'];
				$this->user		= $rows[0]['user'];

                $this->pid      = $rows[0]['pid'];
                $this->scan     = $rows[0]['scan'];
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = "NODE " . $node . " DOES NOT EXIST";
                $this->rows   = $rows;
            }
        }
    }

    public function query($node) {
        global $db;
    
        $qry = "SELECT * FROM t_nodes WHERE node = '$node'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt   = SUCCESS;
                $this->reason = QUERY_MATCHED;
                $this->rows   = $rows;
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
                $this->rows   = $rows;
            }
        }
    }

    public function queryAll() {
        global $db;
    
        $qry = "SELECT * FROM t_nodes";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rslt   = SUCCESS;
            $this->reason = "QUERY_SUCCESSFUL";
            $this->rows   = $rows;
        }
    }

    public function queryRack() {
        global $db;
    
        $qry = "SELECT rack FROM t_nodes";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt   = SUCCESS;
                $this->reason = QUERY_MATCHED;
                $this->rows   = $rows;
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
                $this->rows   = $rows;
            }
        }
    }

    public function updRacks($rack1, $rack2, $rack3, $rack4, $rack5, $rack6, $rack7, $rack8, $rack9, $rack10) {
        global $db;

        $racks = [$rack1, $rack2, $rack3, $rack4, $rack5, $rack6, $rack7, $rack8, $rack9, $rack10];

        for ($i = 0; $i <= 9; $i++) {
            $nodeId = $i +1;
            $qry = "UPDATE t_nodes SET rack = '$racks[$i]' WHERE node = '$nodeId'";
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt     = FAIL;
                $this->reason   = mysqli_error($db);
                return;
            }
            else {
                $this->rslt     = SUCCESS;
                $this->reason   = "Racks updated";
            }
        }
        $this->queryRack();
        $this->rslt = SUCCESS;
        $this->reason = "Racks Updated";
    }

    public function setAlarm($sev) {
        global $db;

        $qry = "UPDATE t_nodes SET alm='$sev' WHERE node='$this->node'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
                $this->rslt   = SUCCESS;
                $this->reason = "NODE: setAlarm(" . $sev . ")";
        }
    }
    
    public function update($node, $volt, $temp, $com, $mx0, $mx1, $mx2, $mx3, $mx4, $mx5, $mx6, $mx7, $mx8, $mx9, $my0, $my1, $my2, $my3, $my4, $my5, $my6, $my7, $my8, $my9, $mr0, $mr1, $mr2, $mr3, $mr4, $mr5, $mr6, $mr7, $mr8, $mr9) {
        global $db;

        $qry  = "UPDATE t_nodes SET ";
        $qry .= "volt   =   '$volt'   ";
        $qry .= ",temp   =   '$temp'   ";
        $qry .= ",com    =   '$com'    ";
        $qry .= ",mx0    =   '$mx0'    ";
        $qry .= ",mx1    =   '$mx1'    ";
        $qry .= ",mx2    =   '$mx2'    ";
        $qry .= ",mx3    =   '$mx3'    ";
        $qry .= ",mx4    =   '$mx4'    ";
        $qry .= ",mx5    =   '$mx5'    ";
        $qry .= ",mx6    =   '$mx6'    ";
        $qry .= ",mx7    =   '$mx7'    ";
        $qry .= ",mx8    =   '$mx8'    ";
        $qry .= ",mx9    =   '$mx9'    ";
        $qry .= ",my0    =   '$my0'    ";
        $qry .= ",my1    =   '$my1'    ";
        $qry .= ",my2    =   '$my2'    ";
        $qry .= ",my3    =   '$my3'    ";
        $qry .= ",my4    =   '$my4'    ";
        $qry .= ",my5    =   '$my5'    ";
        $qry .= ",my6    =   '$my6'    ";
        $qry .= ",my7    =   '$my7'    ";
        $qry .= ",my8    =   '$my8'    ";
        $qry .= ",my9    =   '$my9'    ";
        $qry .= ",mr0    =   '$mr0'    ";
        $qry .= ",mr1    =   '$mr1'    ";
        $qry .= ",mr2    =   '$mr2'    ";
        $qry .= ",mr3    =   '$mr3'    ";
        $qry .= ",mr4    =   '$mr4'    ";
        $qry .= ",mr5    =   '$mr5'    ";
        $qry .= ",mr6    =   '$mr6'    ";
        $qry .= ",mr7    =   '$mr7'    ";
        $qry .= ",mr8    =   '$mr8'    ";
        $qry .= ",mr9    =   '$mr9'    ";
        $qry .= " WHERE node = '$node'";
        $res  = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            if ($db->affected_rows < 1) {
                $this->rslt = FAIL;
                $this->reason = "No change was submitted";
                return;
            }
            $this->rslt = "success";
            $this->reason     = "Node ".$node." has been updated";
        }
    }

    public function updateState($volt, $temp, $com) {
        global $db;
       
        $qryArray = [];

        if($volt != "") {
            $qryArray[] = "volt ='$volt'";
        }
       
        if($temp != ""){
            $qryArray[] = "temp='$temp'";
        }
        
        if($com != ""){
            $qryArray[] = "com='$com'";
        }

        if(count($qryArray) == 0) {
            $this->rslt = FAIL;
            $this->reason = "DATA_NOT_ENOUGH";
            return;
        }

        $qry = "UPDATE t_nodes SET ";
        for($i=0; $i<count($qryArray); $i++) {
            $qry .= $qryArray[$i];
            if($i < (count($qryArray) -1))
                $qry .=",";
        }
        $qry .= " WHERE id='$this->id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }

        $this->volt = $volt;
        $this->temp = $temp;
        $this->com = $com;

        $this->rslt = SUCCESS;
        $this->reason = "NODE_UPDATED";
    }

    // update psta function
    public function updatePsta($psta, $ssta) {
		global $db;
		
		$qry = "UPDATE t_nodes SET psta='$psta', ssta='$ssta' WHERE node='$this->node'";

		$res = $db->query($qry);
		if (!$res) {
			$this->rslt = FAIL;
			$this->reason = mysqli_error($db);
			return;
		} else {
			$this->psta = $psta;
			$this->ssta = $ssta;
			
			$this->rslt = SUCCESS;
			$this->reason = "NODE_PSTA_UPDATED";
			return;
		}

	}

    public function updateAlm($alm) {
        global $db;

        $qry = "UPDATE t_nodes SET alm='$alm' WHERE node='$this->node'";
       
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }

        $this->alm = $alm;

        $this->rslt = SUCCESS;
        $this->reason = "NODE_ALM_UPDATED";
    }

    public function updateCOM($com) {
        global $db;
       
        $qry = "UPDATE t_nodes SET com='$com' WHERE id='$this->node' AND node <= '$this->nodes'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        $this->com = $com;

        $this->rslt = SUCCESS;
        $this->reason = "NODE_COM_UPDATED";
    }

    public function updateScanPid($pid=NULL) {
        global $db;
        if($pid === NULL) {
            $qry = "UPDATE t_nodes SET pid=NULL, scan='OFFLINE', com='OFF' WHERE node='$this->node' AND node <= '$this->nodes'";
        }  
        else 
            $qry = "UPDATE t_nodes SET pid=$pid, scan='ONLINE',  com='OFF' WHERE node='$this->node' AND node <= '$this->nodes'";
       
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            // $this->reason = mysqli_error($db);
            $this->reason = $qry;
            return;
        }
        $this->pid = $pid;
        //$this->scan = $scan;
        $this->rslt = SUCCESS;
        $this->reason = "NODE_SCAN_PID_UPDATED";
    }

    public function updatePid($node,$pid=NULL) {
        global $db;
        if($pid === NULL) {
            $qry = "UPDATE t_nodes SET pid=NULL WHERE node='$node' AND node <= '$this->nodes'";
        }  
        else 
            $qry = "UPDATE t_nodes SET pid=$pid WHERE node='$node' AND node <= '$this->nodes'";
       
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            // $this->reason = mysqli_error($db);
            $this->reason = $qry;
            return;
        }
        if($node == $this->node)
            $this->pid = $pid;
        $this->rslt = SUCCESS;
        $this->reason = "NODE_PID_UPDATED";
    }

    // updates network with values given in NodeAdmin
    public function updateNetwork ($node, $ipadr, $gw, $netmask, $ip_port) {
        global $db;

        $qry = "UPDATE t_nodes SET ipadr = '$ipadr', gateway = '$gw', netmask = '$netmask', ip_port = '$ip_port' WHERE node = '$node'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
			$this->queryAll();
			
			$this->ipadr = $ipadr;
			$this->gateway = $gw;
			$this->netmask = $netmask;
			$this->ip_port = $ip_port;
            $this->rslt = SUCCESS;
            $this->reason = "NETWORK UPDATE SUCCESSFUL";
            return;
        }
    }

    // function to set stat of node to LCK
    public function lockNode($user) {
        global $db;

        $qry = "UPDATE t_nodes SET stat='LCK', user='$user' WHERE node='$this->node' AND node <= '$this->nodes'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
			$this->queryAll();
			
			$this->stat = 'LCK';
			$this->user = $user;
            $this->rslt = SUCCESS;
            $this->reason = "NODE LOCKED";
        }
    }

    // function to set stat of node to INS
    public function unlockNode($user) {
        global $db;

        $qry = "UPDATE t_nodes SET stat='INS', user='$user' WHERE node='$this->node' AND node <= '$this->nodes'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
			$this->queryAll();
			
			$this->stat = 'INS';
			$this->user = $user;
            $this->rslt = SUCCESS;
            $this->reason = "NODE UNLOCKED";
        }
    }

    public function updateRack($rack) {
        global $db;

        $qry = "UPDATE t_nodes SET rack = '$rack' WHERE node = '$this->node' AND node <= '$this->nodes'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
			$this->queryAll();
			
			$this->rack = $rack;
            $this->rslt = SUCCESS;
            $this->reason = "RACK NAME UPDATED";
        }
    }

    public function updateVolt($volt) {
        global $db;

        $qry = "UPDATE t_nodes SET volt = '$volt' WHERE node = '$this->node' AND node <= '$this->nodes'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
			$this->queryAll();
			
			$this->volt = $volt;
            $this->rslt = SUCCESS;
            $this->reason = "VOLT UPDATED";
        }
    }

    public function updateTemp($temp) {
        global $db;
        
        $qry = "UPDATE t_nodes SET temp = '$temp' WHERE node = '$this->node' AND node <= '$this->nodes'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
			$this->queryAll();
			
			$this->temp = $temp;
            $this->rslt = SUCCESS;
            $this->reason = "TEMP UPDATED";
        }
    }

    public function updateCurrent($current) {
        global $db;

        $qry = "UPDATE t_nodes SET current = '$current' WHERE node = '$this->node' AND node <= '$this->nodes'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
			$this->queryAll();
			
			$this->current = $current;
            $this->rslt = SUCCESS;
            $this->reason = "CURRENT UPDATED";
        }
    }
}


class NODES {
    public $rslt    = "";
    public $reason  = "";
    public $rows    = [];

    public function __construct() {
        global $db;

        $this->queryAll();
        $this->nodes = count($this->rows);
        $this->reason = 'NODES IS CONSTRUCTED';
        return;
    }

    public function query($node) {
        global $db;
    
        $qry = "SELECT * FROM t_nodes WHERE node = '$node'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt   = SUCCESS;
                $this->reason = QUERY_MATCHED;
                $this->rows   = $rows;
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
                $this->rows   = $rows;
            }
        }
    }

    public function queryAll() {
        global $db;
    
        $qry = "SELECT * FROM t_nodes";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rslt   = SUCCESS;
            $this->reason = "QUERY_SUCCESSFUL";
            $this->rows   = $rows;
        }
    }

    public function queryRack() {
        global $db;
    
        $qry = "SELECT rack FROM t_nodes";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt   = SUCCESS;
                $this->reason = QUERY_MATCHED;
                $this->rows   = $rows;
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
                $this->rows   = $rows;
            }
        }
    }

}

?>