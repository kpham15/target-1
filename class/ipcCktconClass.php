<?php
/*  Filename: ipcCktconClass.php
    Date: 2018-11-20
    By: Thanh
    Copyright: BHD SOLUTIONS, LLC @ 2018
*/

class CKTCON {

    //  members here are exactly colomns in t_cktcon
    public $id = 0;
    public $con = 0;
    public $ckt_id = 0;
    public $ckid = '';
    public $idx = 0;
    public $ctyp = "";
    public $ctyp_o = "";
    public $fp_id = 0;
    public $fport = '';
    public $fp_n = 0;
    public $tp_id = 0;
    public $tport = '';
    public $tp_n = 0;
    public $path = 0;
    public $tbus = 0;
    
    public $rslt = "";
    public $reason = "";
    public $rows = [];
    
    public function __construct($con=NULL) {

        global $db;

        if ($con === NULL) {
            $this->rslt = SUCCESS;
            $this->reason = 'CONSTRUCT LIST OF CKTCON';
            return;
        }
            
        $qry = "SELECT * FROM t_cktcon WHERE con='$con'";
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
                $this->con = $con;
                $this->rslt = SUCCESS;
                $this->reason = 'CONSTRUCT SINGLE CKTCON';
            }
            else {
                $this->rslt = FAIL;
                $this->reason = 'CKTCON DOES NOT EXIST';
            }
            $this->rows = $rows;
        }
    }

    public function loadIdx($idx) {
        $len = count($this->rows);
        for ($i=0; $i<$len; $i++) {
            if ($this->rows[$i]["idx"] == $idx) {
                $this->idx = $this->rows[$i]["idx"];
                $this->ctyp = $this->rows[$i]["ctyp"];
                $this->ctyp_o = $this->rows[$i]["ctyp_o"];
                $this->fp_id = $this->rows[$i]["fp_id"];
                $this->fp_n = $this->rows[$i]["fp_n"];
                $this->tp_id = $this->rows[$i]["tp_id"];
                $this->tp_n = $this->rows[$i]["tp_n"];
                $this->ckt_id = $this->rows[$i]['ckt_id'];
                $this->ckid = $this->rows[$i]['ckid'];
                $this->fport = $this->rows[$i]['fport'];
                $this->tport = $this->rows[$i]['tport'];
                $this->path = $this->rows[$i]['path'];
                $this->tbus = $this->rows[$i]['tbus'];
                $this->rslt = SUCCESS;
                $this->reason = "LOAD IDX";
                return TRUE;
            }
        }
        $this->rslt = FAIL;
        $this->reason = "IDX DOES NOT EXIST";
        return FALSE;
    }

    public function queryCktConByCkid($ckid) {
        global $db;

        $qry = "SELECT t_cktcon.idx, t_cktcon.ctyp, t_facs.fac, t_facs.id, t_facs.port, t_ports.psta, t_ports.ptyp from t_facs, t_cktcon left join t_ports on";
		$qry .= " (t_cktcon.fp_id=t_ports.id or t_cktcon.tp_id=t_ports.id) where t_cktcon.ckid ='$ckid'";
        $qry .= " AND t_facs.id=t_ports.fac_id ORDER BY t_cktcon.idx";
  
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = $qry . "\n" . mysqli_error($db);
        }
        else {
            $rows = [];
			if ($res->num_rows > 0) {
                $con = ['idx' => ''];
                while ($row = $res->fetch_assoc()) {
                    if ($con['idx'] != $row['idx']) {
                        $con['idx'] = $row['idx'];
                        $con['ctyp'] = $row['ctyp'];
                        if ($row['ptyp'] == 'X') {
                            $con['ffac'] = $row['fac'];
                            $con['fport'] = $row['port'];
                            $con['fpsta'] = $row['psta'];
                        }
                        else {
                            $con['tfac'] = $row['fac'];
                            $con['tport'] = $row['port'];
                            $con['tpsta'] = $row['psta'];
                        }       
                    }
                    else {
                        if ($row['ptyp'] == 'X') {
                            $con['ffac'] = $row['fac'];
                            $con['fport'] = $row['port'];
                            $con['fpsta'] = $row['psta'];
                        }
                        else {
                            $con['tfac'] = $row['fac'];
                            $con['tport'] = $row['port'];
                            $con['tpsta'] = $row['psta'];
                        }
                        $rows[] = $con;
                    }
                }
            }
            $this->rows = $rows;
            $this->rslt = SUCCESS;
            $this->reason = "QUERY CKTCON BY CKID";
		}
		return;
    }

    public function queryCktConWithFac($cktcon) {
        global $db;

        $qry = "SELECT t_cktcon.idx, t_cktcon.ctyp, t_facs.fac, t_facs.id, t_facs.port, t_ports.psta, t_ports.ptyp FROM t_facs, t_cktcon left join t_ports on";
		$qry .= " (t_cktcon.fp_id=t_ports.id or t_cktcon.tp_id=t_ports.id) where t_cktcon.con =" . $cktcon;
        $qry .= " AND t_facs.id=t_ports.fac_id ORDER BY t_cktcon.idx";
  
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = $qry . "\n" . mysqli_error($db);
        }
        else {
            $rows = ['idx' => ''];
			if ($res->num_rows > 0) {
                $con =[];
                while ($row = $res->fetch_assoc()) {
                    if ($con['idx'] != $row['idx']) {
                        $con['idx'] = $row['idx'];
                        $con['ctyp'] = $row['ctyp'];
                        if ($row['ptyp'] == 'X') {
                            $con['ffac'] = $row['fac'];
                            $con['fport'] = $row['port'];
                            $con['fpsta'] = $row['psta'];
                        }
                        else {
                            $con['tfac'] = $row['fac'];
                            $con['tport'] = $row['port'];
                            $con['tpsta'] = $row['psta'];
                        }       
                    }
                    else {
                        if ($row['ptyp'] == 'X') {
                            $con['ffac'] = $row['fac'];
                            $con['fport'] = $row['port'];
                            $con['fpsta'] = $row['psta'];
                        }
                        else {
                            $con['tfac'] = $row['fac'];
                            $con['tport'] = $row['port'];
                            $con['tpsta'] = $row['psta'];
                        }
                        $rows[] = $con;
                    }
                }
            }
            $this->rows = $rows;
            $this->rslt = SUCCESS;
            $this->reason = "QUERY CKTCON WITH FAC";
		}
		return;
    }

    public function addCon($ckt_id, $ckid, $ctyp, $fp_id, $fport, $fp_n, $tp_id, $tport, $tp_n, $path) {
        global $db;

        $result = $this->getAvailCktcon();
        if ($result["rslt"] != SUCCESS) {
            $this->rslt = FAIL;
            $this->reason = $result["reason"];
            return;
        }
        $con = $result["cktcon"];
        
        $qry = "INSERT INTO 
                t_cktcon 
                (con, ckt_id, ckid, idx, ctyp, 
                ctyp_o, fp_id, fport, fp_n, tp_id, 
                tport, tp_n, path) 
                VALUES 
                ('$con', '$ckt_id', '$ckid', 1, '$ctyp', 
                '$ctyp', '$fp_id', '$fport', $fp_n, '$tp_id', 
                '$tport', $tp_n, $path)";

		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db) . ": " . $qry;
			return $result;
        }
        if ($this->con == 0) {
            $this->id = $db->insert_id;
            $this->con = $con;
            $this->idx = 1;
    
            $this->ckt_id = $ckt_id;
            $this->ctyp = $ctyp;
            $this->fp_id = $fp_id;
            $this->fport = $fport;
            $this->fp_n = $fp_n;
            $this->tp_id = $tp_id;
            $this->tport = $tport;
            $this->tp_n = $tp_n;
        }

        $this->rslt = SUCCESS;
        $this->reason = CKTCON_ADDED;
    }

    public function addIdx($con, $ckt_id, $ckid, $ctyp, $ctyp_o, $fp_id, $fport, $fp_n, $tp_id, $tport, $tp_n, $path) {

        global $db;

        $result = $this->getAvailCktconIdx($con);
        if ($result["rslt"] != SUCCESS) {
            $this->rslt = FAIL;
            $this->reason = $result["reason"];
            return;
        }
        $idx = $result['idx'];

        $qry = "INSERT INTO 
                t_cktcon 
                (con, ckt_id, ckid, idx, ctyp, 
                ctyp_o, fp_id, fport, fp_n, tp_id, 
                tport, tp_n, path) 
                VALUES 
                ('$con', '$ckt_id', '$ckid', $idx, '$ctyp', 
                '$ctyp_o', '$fp_id', '$fport', $fp_n, '$tp_id', 
                '$tport', $tp_n, $path)";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt =  FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        if($this->con == 0 || $this->con == $con) {
            $this->id = $db->insert_id;
            $this->con = $con;
            $this->idx = $idx;
    
            $this->ckt_id = $ckt_id;
            $this->ctyp = $ctyp;
            $this->fp_id = $fp_id;
            $this->fport = $fport;
            $this->fp_n = $fp_n;
            $this->tp_id = $tp_id;
            $this->tport = $tport;
            $this->tp_n = $tp_n;
        }
  
        $this->rslt = SUCCESS;
        $this->reason = CKTCON_IDX_ADDED;
        
    }

    public function deleteIdx($con, $idx) {
        global $db;

        $qry = "DELETE FROM t_cktcon WHERE con='$con' AND idx='$idx'";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt =  FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        
        $this->rslt = SUCCESS;
        $this->reason = CKTCON_IDX_DELETED;
    }

    public function updateIdx($con, $idx, $ctyp, $ctyp_o, $path) {
        global $db;

        $qry = "UPDATE t_cktcon SET ctyp='$ctyp', ctyp_o='$ctyp_o', path='$path' WHERE con='$con' AND idx='$idx'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt =  FAIL;
            $this->reason = mysqli_error($db);
            return;
        }

        $this->rslt = SUCCESS;
        $this->reason = 'UPDATE_CKTCON_IDX';
    }
    
    public function updCtyp($con, $idx, $ctyp, $ctyp_o) {
        global $db;

        $qry = "UPDATE t_cktcon SET ctyp = '$ctyp', ctyp_o = '$ctyp_o' WHERE con = '$con' and idx = '$idx'";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt =  FAIL;
            $this->reason = mysqli_error($db);
            return;
        }

        $this->rslt = SUCCESS;
        $this->reason = 'UPDATE_CKTCON_CTYP';
    }

    public function updPath($con, $idx, $path) {
        global $db;

        $qry = "UPDATE t_cktcon SET path = '$path' WHERE con = '$con' and idx = '$idx'";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt =  FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        $this->path = $path;
        $this->rslt = SUCCESS;
        $this->reason = "UPDATE_CKTCON_PATH";
    }

    function getAvailCktcon() {
		global $db;
		
		$qry = "SELECT MAX(con) from t_cktcon";
		$res = $db->query($qry);
        if (!$res) {
            $result["rslt"] = FAIL;
            $result["reason"] = mysqli_error($db);
        }
        else {
			$result["rslt"] = SUCCESS;
			$result["cktcon"] = 1;
			if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    if ($row["MAX(con)"] != NULL) {
						$result["cktcon"] = $row["MAX(con)"] +1;
					}
                }
			}
		}
        return $result;
	}

    function getAvailCktconIdx($con) {

        global $db;

        $qry = "SELECT MAX(idx) FROM t_cktcon WHERE con='$con'";
		$res = $db->query($qry);
        if (!$res) {
            $result['rslt'] = FAIL;
            $result['reason'] = mysqli_error($db);
        }
        else {
			$result['rslt'] = SUCCESS;
			$result['idx'] = 1;
			if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    if ($row["MAX(idx)"] != NULL) {
						$result['idx'] = $row["MAX(idx)"] +1;
					}
                }
			}
        }
        return $result;
	}
    
    public function findIdxByCtyp($ctyp) {
        for ($i=0; $i<count($this->rows); $i++) {
            if ($this->rows[$i]['ctyp'] == $ctyp) {
                return $this->rows[$i]['idx'];
            }
        }
        return 0;
    }

    public function findIdxByFportId($port_id) {
        for ($i=0; $i<count($this->rows); $i++) {
            if ($this->rows[$i]['fp_id'] == $port_id) {
                return $this->rows[$i]['idx'];
            }
        }
        return 0;
    }

    public function findIdxByFport($port) {
        for ($i=0; $i<count($this->rows); $i++) {
            if ($this->rows[$i]['fport'] == $port) {
                return $this->rows[$i]['idx'];
            }
        }
        return 0;
    }

    public function findIdxByTportId($port_id) {
        for ($i=0; $i<count($this->rows); $i++) {
            if ($this->rows[$i]['tp_id'] == $port_id) {
                return $this->rows[$i]['idx'];
            }
        }
        return 0;
    }

    public function findIdxByTport($port) {
        for ($i=0; $i<count($this->rows); $i++) {
            if ($this->rows[$i]['tport'] == $port) {
                return $this->rows[$i]['idx'];
            }
        }
        return 0;
    }

    public function loadIdxByPortIds($fp_id, $tp_id) {
        for ($i=0; $i<count($this->rows); $i++) {
            if ($this->rows[$i]['fp_id'] == $fp_id && $this->rows[$i]['tp_id'] == $tp_id) {
                $this->idx = $this->rows[$i]['idx'];
                $this->ctyp = $this->rows[$i]['ctyp'];
                $this->ctyp_o = $this->rows[$i]['ctyp_o'];
                $this->fp_id = $this->rows[$i]['fp_id'];
                $this->tp_id = $this->rows[$i]['tp_id'];
                $this->fp_n = $this->rows[$i]['fp_n'];
                $this->tp_n = $this->rows[$i]['tp_n'];
                $this->ckid = $this->rows[$i]['ckid'];
                $this->fport = $this->rows[$i]['fport'];
                $this->tport = $this->rows[$i]['tport'];
                $this->path = $this->rows[$i]['path'];
                $this->tbus = $this->rows[$i]['tbus'];
                return $this->idx;
            }
        }
        return 0;
    }

    public function updateTbus($tbusId){
        global $db;

        $qry = "UPDATE t_cktcon SET tbus='$tbusId' WHERE con='$this->con' AND idx='$this->idx'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt =  FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        $this->tbus = $tbusId;
        $this->rslt = SUCCESS;
        $this->reason = 'TBUS UPDATED';
    }
}   

?>