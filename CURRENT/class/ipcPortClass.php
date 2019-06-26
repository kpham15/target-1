<?php
/*  Filename: ipcCktconClass.php
    Date: 2018-11-20
    By: Thanh
    Copyright: BHD SOLUTIONS, LLC @ 2018
*/

class PORT {

    public $id = 0;
    public $port = '';
    public $node = 0;
    public $slot = 0;
    public $ptyp = "";
    public $pnum = 0;

    public $psta = "";
    public $ssta = "";
    public $npsta = "";
    public $nssta = "";
    public $substa = "";
    public $fac_id = 0;
    public $ckt_id = 0;
    public $cktcon = 0;
    public $con_idx= 0;
    public $mp_id = 0;

    public $rows = [];
    public $rslt = "";
    public $reason = "";
    
    public $nodes = 0;
    
    public function __construct($id=NULL) {
        global $db;

        $nodesObj = new NODES();
        $nodesObj->queryAll();
        $this->nodes = count($nodesObj->rows);
        

        if($id == NULL) {
            $this->rslt = SUCCESS;
            $this->reason = PORT_CONSTRUCTED;
            return;
        } 
       
        $qry = "SELECT * FROM t_ports WHERE id='$id' LIMIT 1";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt = SUCCESS;
                $this->reason = PORT_CONSTRUCTED;
                $this->id = $rows[0]["id"];
                $this->port = $rows[0]["port"];
                $this->node = $rows[0]["node"];
                $this->slot = $rows[0]["slot"];
                $this->ptyp = $rows[0]["ptyp"];
                $this->pnum = $rows[0]["pnum"];
                $this->psta = $rows[0]["psta"];
                $this->ssta = $rows[0]["ssta"];
                $this->substa = $rows[0]["substa"];
                $this->fac_id = $rows[0]["fac_id"];
                $this->ckt_id = $rows[0]["ckt_id"];
                $this->cktcon = $rows[0]["cktcon"];
                $this->con_idx = $rows[0]["con_idx"];
                $this->mp_id = $rows[0]["mp_id"];   
            }
            else {
                $this->rslt = FAIL;
                $this->reason = INVALID_PORT;
                return;
            }
            $this->rows = $rows;
        }
    }

    public function loadPort($port) {
        global $db;

        $p = explode('-', $port);
        if (!isset($p[0]) || !isset($p[1]) || !isset($p[2]) || !isset($p[3])) {
            $this->rslt = FAIL;
            $this->reason = "INVALID_PORT";
            $this->rows = [];
            return false;
        }

        $qry = "SELECT * FROM t_ports WHERE node <= '$this->nodes' AND node='$p[0]' AND slot='$p[1]' AND ptyp='$p[2]' AND pnum='$p[3]'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            $this->rows = [];
            return false;
        }
        else {
            $rows = [];
			if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $this->id = $row['id'];
                    $this->port = $row["port"];
                    $this->node = $row['node'];
                    $this->slot = $row['slot'];
                    $this->ptyp = $row['ptyp'];
                    $this->pnum = $row['pnum'];
                    $this->psta = $row['psta'];
                    $this->ssta = $row['ssta'];
                    $this->substa = $row['substa'];
                    $this->ckt_id = $row['ckt_id'];
                    $this->cktcon = $row['cktcon'];
                    $this->con_idx = $row['con_idx'];
                    $this->mp_id = $row['mp_id'];

					if ($row["fac_id"] == null)
						$row["fac_id"] = "0";
					$this->fac_id = $row['fac_id'];
                    $rows[] = $row;
                }
            }
            $this->rslt = SUCCESS;
            $this->reason = "LOAD_PORT";
            $this->rows = $rows;
            return true;
        }
    }

    public function queryPort($node, $slot, $pnum, $ptyp, $psta) {
        global $db;

        $qry = "SELECT t_ports.id as id, t_ports.port, t_ports.node, t_ports.slot, t_ports.pnum, t_ports.ptyp, t_ports.psta, ";
		$qry .= "t_facs.id as fac_id, t_facs.fac, t_facs.ftyp, t_facs.ort, t_facs.spcfnc, t_ckts.ckid ";
		$qry .= "FROM t_ports LEFT JOIN t_facs ON t_ports.fac_id = t_facs.id LEFT JOIN t_ckts ON t_ports.ckt_id = t_ckts.id";
		$qry .= " WHERE t_ports.psta LIKE '%$psta%' AND t_ports.node <= '$this->nodes'";
		if ($ptyp != "") {
			$qry .= " AND t_ports.ptyp LIKE '$ptyp'";
		}    
        if ($node != "") {
            $qry .= " AND t_ports.node LIKE '$node'"; 
        }
        if ($slot != "") {
            $qry .= " AND t_ports.slot LIKE '$slot'"; 
        }
        if ($pnum != "") {
            $qry .= " AND t_ports.pnum LIKE '$pnum'"; 
        }
        
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
        }
        else {
            $this->rows = [];
			if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    if($row["ckid"] == null)
						$row["ckid"] = "";
					if ($row["fac"] == null)
						$row["fac"] = "";
					if ($row["fac_id"] == null)
                        $row["fac_id"] = "0";
                    if ($row['ftyp'] == null)
                        $row['ftyp'] = '';
                    if ($row['ort'] == null)
                        $row['ort'] = '';
                    if ($row['spcfnc'] == null)
                        $row['spcfnc'] = '';
                    
					$this->rows[] = $row;
                }
            }
            $this->rslt = SUCCESS;
            $this->reason = "QUERY_PORT";
        }
    }

    public function findPortBySlot($node, $slot, $ptyp) {
        global $db;

        $qry = "SELECT t_ports.id as id, t_ports.port, t_ports.node, t_ports.slot, t_ports.pnum, t_ports.ptyp, t_ports.psta, ";
		$qry .= "t_ports.ssta, t_ports.substa, t_ports.con_idx, t_facs.id as fac_id, t_facs.fac, t_facs.ftyp, t_ckts.ckid ";
		$qry .= "FROM t_ports LEFT JOIN t_facs ON t_ports.fac_id = t_facs.id LEFT JOIN t_ckts ON t_ports.ckt_id = t_ckts.id";
		$qry .= " WHERE t_ports.ptyp = '$ptyp' AND t_ports.node = '$node' AND t_ports.node <= '$this->nodes'";
		$qry .= " AND t_ports.slot = '$slot'";
        
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            $this->rows = [];
            return false;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    if($row["ckid"] == null)
						$row["ckid"] = "";
					if ($row["fac"] == null)
						$row["fac"] = "";
					if ($row["fac_id"] == null)
                        $row["fac_id"] = "0";
                    if ($row['ftyp'] == null)
                        $row['ftyp'] = "";
					$rows[] = $row;
                }
            }
            $this->rslt = SUCCESS;
            $this->reason = "FIND_PORT_BY_SLOT";
            $this->rows = $rows;
            return true;
		}
    }

    public function findPortByCkid($ckid) {
        global $db;

        $ckid = str_replace('?','%',$ckid);

        $qry = "SELECT t_ports.id as id, t_ports.port, t_ports.node, t_ports.slot, t_ports.pnum, t_ports.ptyp, t_ports.psta, ";
		$qry .= "t_ports.con_idx, t_facs.id as fac_id, t_facs.fac, t_facs.ftyp, t_facs.ort, t_facs.spcfnc, t_ckts.ckid ";
		$qry .= "FROM t_ports LEFT JOIN t_facs ON t_ports.fac_id = t_facs.id LEFT JOIN t_ckts ON t_ports.ckt_id = t_ckts.id";
		$qry .= " WHERE t_ckts.ckid LIKE '$ckid' AND t_ports.node <= '$this->nodes' ORDER BY t_ports.con_idx";
        
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            $this->rows = [];
            return false;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    if($row["ckid"] == null)
						$row["ckid"] = "";
					if ($row["fac"] == null)
						$row["fac"] = "";
					if ($row["fac_id"] == null)
						$row["fac_id"] = "0";
                    if ($row["ftyp"] == null)
						$row["ftyp"] = "";
                    if ($row["ort"] == null)
						$row["ort"] = "";
                    if ($row["spcfnc"] == null)
						$row["spcfnc"] = "";
					$rows[] = $row;
                }
            }
            $this->rows = $rows;
            $this->rslt = SUCCESS;
            $this->reason = "FIND_PORT_BY_CKID";
            return true;
		}
    }

    public function findPortByFac($fac) {
        global $db;

        $fac = str_replace('?','%',$fac);

        $qry = "SELECT t_ports.id as id, t_ports.port, t_ports.node, t_ports.slot, t_ports.pnum, t_ports.ptyp, t_ports.psta, ";
		$qry .= "t_facs.id as fac_id, t_facs.fac, t_facs.ftyp, t_facs.ort, t_facs.spcfnc, t_ckts.ckid ";
		$qry .= "FROM t_ports LEFT JOIN t_facs ON t_ports.fac_id = t_facs.id LEFT JOIN t_ckts ON t_ports.ckt_id = t_ckts.id";
		$qry .= " WHERE t_facs.fac LIKE '$fac' AND t_ports.node <= '$this->nodes'";
        
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            $this->rows = [];
            return false;
        }
        else {
            $rows = [];
			if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    if($row["ckid"] == null)
						$row["ckid"] = "";
					if ($row["fac"] == null)
						$row["fac"] = "";
					if ($row["fac_id"] == null)
						$row["fac_id"] = "0";
                    if ($row["ftyp"] == null)
						$row["ftyp"] = "";
                    if ($row["ort"] == null)
						$row["ort"] = "";
                    if ($row["spcfnc"] == null)
						$row["spcfnc"] = "";
					$rows[] = $row;
                }
            }
            $this->rows = $rows;
            $this->rslt = SUCCESS;
            $this->reason = "FIND_PORT_BY_FAC";
            return true;
        }
    }


    public function getPortByFac($fac_id=NULL) {
        global $db;

        if($fac_id == NULL) {
            $qry = "SELECT * FROM t_ports WHERE fac_id > 0 AND t_ports.node <= '$this->nodes'";
        }
        else {
            $qry = "SELECT * FROM t_ports WHERE fac_id = '$fac_id' AND t_ports.node <= '$this->nodes'";
        }
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt = SUCCESS;
                $this->reason = QUERY_MATCHED;
            }
            else {
                $this->rslt = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
            }
            $this->rows = $rows;
        }
    }

    public function getPortByCkt($ckt_id=NULL) {
        global $db;

        if ($ckt_id == NULL) {
            $qry = "SELECT * FROM t_ports WHERE ckt_id > 0 AND t_ports.node <= '$this->nodes'";
        }
        else {
            $qry = "SELECT * FROM t_ports WHERE ckt_id = '$ckt_id' AND t_ports.node <= '$this->nodes'";
        }
        
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt = SUCCESS;
                $this->reason = QUERY_MATCHED;
                
            }
            else {
                $this->rslt = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
            }
            $this->rows = $rows;
        }
    }

    public function getPortByCktcon($cktcon=NULL) {
        global $db;

        if($cktcon == NULL) {
            $qry = "SELECT * FROM t_ports WHERE cktcon > 0 AND t_ports.node <= '$this->nodes'";
        }
        else {
            $qry = "SELECT * FROM t_ports WHERE cktcon = '$cktcon' AND t_ports.node <= '$this->nodes'";
        }
        
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rslt = SUCCESS;
                $this->reason = QUERY_MATCHED;
                
            }
            else {
                $this->rslt = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
            }
            $this->rows = $rows;
        }
    }

    public function updatePortStat($node, $slot, $ptyp, $pnum, $psta, $ssta, $substa) {
        global $db;
        
        if ($psta == "" || $ssta == "" || $substa == "") {
            $this->rslt = FAIL;
            $this->reason = "INVALID PORT STAT";
            $this->rows = [];
            return false;
        }

        $qry = "UPDATE t_ports SET psta='$psta', ssta='$ssta', substa='$substa' ";
        $qry .= "WHERE node <= '$this->nodes' AND node='$node' AND slot='$slot' AND ptyp='$ptyp' AND pnum='$pnum'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return false;
        }
        
        $this->rslt = SUCCESS;
        $this->reason = "PORT_PSTA_UPDATED";
        $this->rows = [];
        return true;
    }

    public function updPsta($npsta, $nssta, $substa) {
        
        global $db;
        if ($this->id <= 0) {
            $this->rslt = FAIL;
            $this->reason = INVALID_PORT;
            return;
        }

        if ($npsta == "") {
            $this->rslt = FAIL;
            $this->reason = INVALID_PSTA;
            return;
        }
       
        if ($nssta == ""){
            $this->rslt = FAIL;
            $this->reason = INVALID_SSTA;
            return;
        }
        
        if ($substa == ""){
            $this->rslt = FAIL;
            $this->reason = INVALID_SUBSTA;
            return;
        }


        $qry = "UPDATE t_ports SET psta='$npsta', ssta='$nssta', substa='$substa' WHERE id='$this->id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
       
        $this->psta = $npsta;
        $this->ssta = $nssta;
        $this->substa = $substa;
        
        $this->rslt = SUCCESS;
        $this->reason = "PORT_PSTA_UPDATED";
    }

    public function updCktLink($ckt_id, $cktcon, $con_idx) {
        global $db;

        if ($this->id <= 0) {
            $this->rslt = FAIL;
            $this->reason = "INVALID_PORT";
            return;
        }

        if (!($ckt_id !== "" && is_numeric($ckt_id) && (int)$ckt_id >= 0)) {
            $this->rslt = FAIL;
            $this->reason = "INVALID_CKT_ID";
            return;
        }

        if (!($cktcon !== "" && is_numeric($cktcon) && (int)$cktcon >=0)) {
            $this->rslt = FAIL;
            $this->reason = INVALID_CKTCON;
            return;
        }

        if (!($con_idx !== "" && is_numeric($con_idx) && (int)$con_idx >=0)) {
            $this->rslt = FAIL;
            $this->reason = INVALID_CONIDX;
            return;
        }

        $qry = "UPDATE t_ports SET ckt_id='$ckt_id', cktcon='$cktcon', con_idx='$con_idx' WHERE id='$this->id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
       
        $this->ckt_id = $ckt_id;
        $this->cktcon = $cktcon;
        $this->con_idx = $con_idx;
     
        $this->rslt = SUCCESS;
        $this->reason = "CKT_LINKED";
    }

    public function linkFac($fac_id) {
        global $db;

        if ($this->id <= 0) {
            $this->rslt = FAIL;
            $this->reason = INVALID_PORT;
            return;
        }

        if (!($fac_id != "" && is_numeric($fac_id) && (int)$fac_id >=0)) {
            $this->rslt = FAIL;
            $this->reason = INVALID_FACID;
            return;
        }

        $qry = "UPDATE t_ports SET fac_id = '$fac_id' WHERE id='$this->id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
       
        $this->fac_id = $fac_id;
    
        $this->rslt = SUCCESS;
        $this->reason = FAC_LINKED;
    }

    public function unlinkFac() {
        global $db;

        $qry = "UPDATE t_ports SET fac_id = 0 WHERE id='$this->id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
       
        $this->fac_id = 0;
    
        $this->rslt = SUCCESS;
        $this->reason = "FAC_UNLINKED";
    }


    public function updatePortStatByNodeSlot($node, $slot, $ptyp, $evt) {
        global $db;

        for ($pnum = 1; $pnum <= 50; $pnum++) {
            $port = $node . '-' . $slot . '-' . $ptyp . '-' . $pnum;
            $this->loadPort($port);
            $smsObj = new SMS($this->psta, $this->ssta, $evt);
            if ($smsObj->rslt == SUCCESS) {
                $qry = "UPDATE t_ports SET psta='$smsObj->npsta', ssta='$smsObj->nssta', substa='$this->substa' WHERE id='$this->id'";
                $res = $db->query($qry);
                if (!$res) {
                    $this->rslt = FAIL;
                    $this->reason = mysqli_error($db);
                    return;
                }
            }
        }
        $this->rslt = 'success';
        $this->reason = 'UPDATE PORT STATUS BY NODE SLOT';
        $this->rows = [];
    }


}



?>