<?php
/*
 * Copy Right @ 2019
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcAlmClass.php
 * Change history: 
 * 2018-10-10: created (Ninh)
 * 2019-1-8: updated (Alex)
 * 2019-1-10: update (Kris)
 */

 /*
 * Creates a class called ALMS
 * all member variables are initialized to a 0 or an empty string
 */
class ALMS {
    public $id          = 0;
    public $almid       = '';
    public $ack         = "";
    public $sa          = "";
    public $src         = "";
    public $type        = "";
    public $cond        = "";
    public $sev         = "";
    public $psta        = "";
    public $ssta        = "";
    public $remark      = "";
    public $datetime    = "";

    public $rslt        = "";
    public $reason      = "";
    public $rows        = [];

    public function __construct($almid=NULL) {
        global $db;

        if ($almid == NULL) {
            $this->rslt = "success";
            $this->reason = "";
            $this->rows = [];
            return;
        }

        $qry = "SELECT * FROM t_alms WHERE almid = '$almid' LIMIT 1";
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
                $this->rslt     = SUCCESS;
                $this->reason   = QUERY_MATCHED;
                $this->rows     = $rows;
                $this->id       = $rows[0]['id'];
                $this->almid    = $rows[0]['almid'];
                $this->ack      = $rows[0]['ack'];
                $this->sa       = $rows[0]['sa'];
                $this->src      = $rows[0]['src'];
                $this->type     = $rows[0]['type'];
                $this->cond     = $rows[0]['cond'];
                $this->sev      = $rows[0]['sev'];
                $this->psta     = $rows[0]['psta'];
                $this->ssta     = $rows[0]['ssta'];
                $this->remark   = $rows[0]['remark'];
                $this->datetime = $rows[0]['datetime'];
                $this->rows = $rows;
                $this->rslt = SUCCESS;
                $this->reason = "ALARM FOUND";
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = "ALARM NOT FOUND";
                $this->rows   = $rows;
            }
        }
    }

    public function queryalm() {
        global $db;

        $qry = "SELECT * FROM t_alms ORDER BY sev ASC";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rslt = "success";
            $this->reason = "queryAlm Success";
            $this->rows = $rows;
        }
    }

    public function queryAlmByNode($node) {
        global $db;

        $qry = "SELECT * FROM t_alms WHERE almid LIKE '$node-%' ORDER BY sev ASC";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rslt = "success";
            $this->reason = "queryAlm Success";
            $this->rows = $rows;
        }
    }

    public function newAlm($almid, $src, $type, $cond, $sev, $sa, $remark) {
        global $db;

        $psta = 'NEW';
        $ssta = 'NEW';
        $now = date('Y-m-d H:i:s', time());
        $remark = $now . ": NEW-ALARM " . $remark;

        $qry = "INSERT INTO t_alms VALUES(0, '$almid', '', '$sa', '$src', '$type', '$cond', '$sev', '$psta', '$ssta', '$remark', '$now')";
        $res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
            $this->reason = mysqli_error($db);
		}
		else {
            $this->id = $db->insert_id;
            $this->almid = $almid;
            $this->sev = $sev;
			$this->rslt = "success";
            $this->reason = "NEW ALARM CREATED";
        }
        
        $almLog = new ALMLOG();
        $action = 'NEW';
        $ack = '';
        $user = 'SYStem';
        $result = $this->rslt . ' : ' . $this->reason;
        $almLog->log($almid, $ack, $sa, $src, $type, $cond, $sev, $psta, $ssta, $remark, $action, $user, $result);
        
        return;
    }

    public function sysClr($almid, $remark) {
        global $db;

        $now = date('Y-m-d H:i:s', time());
        $remark = $now . ": " . "SYSTEM: SYS-CLR: " . $remark;
        $this->remark .= "| " . $remark;
        //remove alarm only if it has not been acknowledged yet, otherwise update the cond to 'SYS-CLR'
        if ($this->psta != 'NEW' && $this->psta != 'ACK') {
            $this->rslt = "fail";
            $this->reason = "INVALID ALARM STAT: " . $this->psta;
        }
        else {
            if ($this->psta == 'NEW') {
                $qry = "DELETE from t_alms WHERE almid='$this->almid'";
            }
            else {
                $this->ssta = $this->psta;
                $this->psta = 'SYS-CLR';
                $qry = "UPDATE t_alms SET psta='$this->psta', ssta='$this->ssta', remark='$this->remark', datetime='$now' WHERE almid='$this->almid'";
            }
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = "fail";
                $this->reason = mysqli_error($db);
            }
            else {
                $this->rslt = "success";
                $this->reason = "ALARM SYSTEM CLEARED";
            }
        }
        
        $almLog = new ALMLOG();
        $action = 'SYS-CLR';
        $user = 'SYSTEM';
        $result = $this->rslt . ' : ' . $this->reason;
        $almLog->log($this->almid, $this->ack, $this->sa, $this->src, $this->type, $this->cond, $this->sev, $this->psta, $this->ssta, $remark, $action, $user, $result);
        
        return;
    }
    
    public function ackAlm($user, $remark) {
        global $db;

        if ($this->psta != "NEW") {
            $this->rslt = "fail";
            $this->reason = "INVALID ALARM STAT: $this->psta";
            return;
        }

        $this->ack = $user;
        $this->psta = 'ACK';
        $this->ssta = 'NEW';
        $now = date('Y-m-d H:i:s', time());
        $remark = $now . ": " . $user . ": ACK-ALARM: " . $remark;
        $this->remark .= "| " . $remark;

        $qry = "UPDATE t_alms SET 
                ack       = '$this->ack', 
                psta      = '$this->psta', 
                remark    = '$this->remark', 
                datetime  = '$now' WHERE almid = '$this->almid'";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $qry = "SELECT * FROM t_alms";
            $res = $db->query($qry);
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }

            $this->rslt = "success";
            $this->reason = "ALARM ACKNOWLEDGED";
            $this->rows = $rows;
        }

        $almLog = new ALMLOG();
        $action = 'ACK';
        $result = $this->rslt . ' : ' . $this->reason;
        $almLog->log($this->almid, $this->ack, $this->sa, $this->src, $this->type, $this->cond, $this->sev, $this->psta, $this->ssta, $remark, $action, $user, $result);
        
    }

	public function unackAlm($ack, $user, $remark) {
		global $db;
				
        if ($this->psta != "ACK") {
            $this->rslt = "fail";
            $this->reason = "INVALID ALARM STAT: $this->psta";
            return;
        }

        $this->psta = 'NEW';
        $this->ssta = 'ACK';
        $this->ack = ''; 
        $now = date('Y-m-d H:i:s', time());
        $remark = $now . ": " . $user . ": UNACK-ALARM: " . $remark;
        $this->remark .= "| " . $remark;

		$qry = "UPDATE t_alms SET 
                ack       = '$this->ack', 
                psta      = '$this->psta',
                ssta      = '$this->ssta',
                remark    = '$this->remark', 
                datetime  = '$now' WHERE almid = '$this->almid'";

		$res = $db->query($qry);
		if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
		}
		else {
            $qry = "SELECT * FROM t_alms";
            $res = $db->query($qry);
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }

            $this->rslt = "success";
            $this->reason = "ALARM UN-ACKOWLEDGED";
            $this->rows = $rows;
        }
        
        $almLog = new ALMLOG();
        $action = 'UN-ACK';
        $result = $this->rslt . ' : ' . $this->reason;
        $almLog->log($this->almid, $this->ack, $this->sa, $this->src, $this->type, $this->cond, $this->sev, $this->psta, $this->ssta, $remark, $action, $user, $result);

    }
    
    public function clrAlm($ack, $user, $remark) {
		global $db;
        
        if ($this->almid == 'INV-USER') {
            if ($this->psta != 'ACK') {
                $this->rslt = "fail";
                $this->reason = "CLEAR ALARM DENIED - ALARM HAS NOT BEEN ACKNOWLEDED";
                return;
            }
        }
        else {
            if ($this->psta != "SYS-CLR") {
                $this->rslt = "fail";
                $this->reason = "CLEAR ALARM DENIED - ALARM HAS NOT BEEN CLEARED BY SYSTEM";
                return;
            }
        }
        
		$now = date('Y-m-d H:i:s', time());
        $remark = $now . ": " . $user . ": CLEAR-ALARM: " . $remark;
        $this->remark .= "| " . $remark;

		$qry = "DELETE FROM t_alms WHERE almid = '$this->almid'";
		$res = $db->query($qry);
		if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
		}
		else {
            $qry = "SELECT * FROM t_alms";
            $res = $db->query($qry);
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rslt = "success";
            $this->reason = "ALARM CLEARED";
            $this->rows = $rows;
        }
        
        $almLog = new ALMLOG();
        $action = 'CLR';
        $result = $this->rslt . ' : ' . $this->reason;
        $almLog->log($this->almid, $ack, $this->sa, $this->src, $this->type, $this->cond, $this->sev, $this->psta, $this->ssta, $remark, $action, $user, $result);

	}

    public function ack($user, $remark) {
        global $db;
			
		if ($remark == "") {
			$this->rslt = FAIL;
			$this->reason = "MISSING REMARK";
			return false;
		}
			
		if ($this->cond != "NEW" && $this->cond != "UN-ACK") {
			$this->rslt = "fail";
			$this->reason = "ALARM CONDITION must be NEW or UNACK";
			return false;
		}
	
		$qry = "UPDATE t_alms SET ack='$user', cond='ACK', remark='$remark' WHERE src='$this->src'";
		$res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
		}
		else {
            $this->ack = $user;
            $this->remark = $remark;
			$this->rslt = "success";
            $this->reason = "ALM_ACKNOWLEDGED";
            return true;
		}
    }

    public function unack($user, $remark) {
        global $db;
			
		if ($ack == "") {
			$this->rslt = "fail";
            $this->reason = "ALARM has not been ACKNOWLEDGED";
            $this->rows = [];
			return false;
		}
			
		if ($remark == "") {
			$this->rslt = "fail";
			$this->reason = "Missing REMARK";
			$this->rows = [];
			return false;
		}
			
		if ($this->cond != "ACK") {
			$this->rslt = "fail";
			$this->reason = "ALARM CONDITION must be ACK";
            $this->rows = [];
            return false;
		}

		$qry = "UPDATE t_alms SET ack='', cond='UN-ACK', remark=concat('$remark',remark) WHERE src='$this->src'";
		$res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
            $this->reason = mysqli_error($db);
            $this->rows = [];
            return false;
		}
		else {
			$qry = "select *  FROM t_alms"; 
			$res = $db->query($qry);
			$rows = [];
			if ($res->num_rows > 0) {
				while ($row = $res->fetch_assoc()) {
					$rows[] = $row;
				}
			}
            $this->rslt = "success";
            $this->reason = "ALM_UNACKNOWLEDGED";
            $this->rows = $rows;
            return true;
		}
    }

    public function update() {
        global $db;
        
        if ($this->ack  != "" && !in_array($this->ack, ACK_LST)) {
            $this->rslt     =   FAIL;
            $this->reason   =   INVALID_ACK;
            return;
        }
        if ($this->sa   != "" && !in_array($this->sa, SA_LST)) {
            $this->rslt     =   FAIL;
            $this->reason   =   INVALID_SA;
            return;
        }
        if ($this->src  != "" && !in_array($this->src, SRC_LST)) {
            $this->rslt     =   FAIL;
            $this->reason   =   INVALID_SRC;
            return;
        }
        if ($this->type != "" && !in_array($this->type, TYPE_LST)) {
            $this->rslt     =   FAIL;
            $this->reason   =   INVALID_TYPE;
            return;
        }
        if ($this->cond != "" && !in_array($this->cond, COND_LST)) {
            $this->rslt     =   FAIL;
            $this->reason   =   INVALID_COND;
            return;
        }
        if ($this->sev  != "" && !in_array($this->sev, SEV_LST)) {
            $this->rslt     =   FAIL;
            $this->reason   =   INVALID_SEV;
            return;
        }

        $qry  = "UPDATE t_alms SET ";
        $qry .= "ack          =   '$this->ack'       ";
        $qry .= ",sa          =   '$this->sa'        ";
        $qry .= ",src         =   '$this->src'       ";
        $qry .= ",type        =   '$this->type'      ";
        $qry .= ",cond        =   '$this->cond'      ";
        $qry .= ",sev         =   '$this->sev'       ";
        $qry .= ",remark      =   '$this->remark'    ";
        $qry .= " WHERE almid =   '$this->almid'     ";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason = "ALARM_UPDATE_SUCCESS";
        }
    }

    public function getAlmByAck($ack=NULL) {
        global $db;
        if ($ack === NULL || $ack === "") {
            $qry = "SELECT * FROM t_alms";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
        else {
            $qry = "SELECT * FROM t_alms 
                    WHERE ack = '$ack'
                    ";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }

    public function getAlmBySa($sa=NULL) {
        global $db;
        if ($sa === NULL || $sa === "") {
            $qry = "SELECT * FROM t_alms";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
        else {
            $qry = "SELECT * FROM t_alms 
                    WHERE sa = '$sa'
                    ";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }

    public function getAlmBySrc($src=NULL) {
        global $db;
        if ($src === NULL || $src === "") {
            $qry = "SELECT * FROM t_alms";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
        else {
            $qry = "SELECT * FROM t_alms 
                    WHERE src = '$src'
                    ";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }

    public function getAlmByType($type=NULL) {
        global $db;
        if ($type === NULL || $type === "") {
            $qry = "SELECT * FROM t_alms";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
        else {
            $qry = "SELECT * FROM t_alms 
                    WHERE type = '$type'
                    ";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }
    
    public function getAlmByCond($cond=NULL) {
        global $db;
        if ($cond === NULL || $cond === "") {
            $qry = "SELECT * FROM t_alms";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
        else {
            $qry = "SELECT * FROM t_alms 
                    WHERE cond = '$cond'
                    ";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }

    public function getAlmBySev($sev=NULL) {
        global $db;
        if ($sev === NULL || $sev === "") {
            $qry = "SELECT * FROM t_alms";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
        else {
            $qry = "SELECT * FROM t_alms 
                    WHERE sev = '$sev'
                    ";
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
                    $this->rslt     = SUCCESS;
                    $this->reason   = QUERY_MATCHED;
                    $this->rows     = $rows;
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }

    public function query() {
        global $db;

        $qry = "SELECT * FROM t_alms";
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
            $this->reason = "ALM_QUERY";
            $this->rows   = $rows;
        }
    }

    public function reportBySrc($user, $src, $evt) {
        global $db;

        $s = explode('-',$src);
        if ($s[1] == 'MX' || $s[1] == 'MY' || $s[1] == 'MR') {
            $this->src = $src;
            $this->type = 'HW';
            $this->sev = 'MAJ';
            $this->sa = 'Y';
            $this->cond = 'NEW';
            $this->remark = $src . ": " . $evt;
        }
        else if ($s[1] == 'CPS') {
            $e = explode('-',$evt);
            if ($e[0] == 'T') {
                if ($e[1] > 100) {
                    $this->src = $src;
                    $this->type = 'TEMPERATURE';
                    $this->sev = 'MIN';
                    $this->sa = 'N';
                    $this->cond = 'NEW';
                    $this->remark = $src . ": " . $evt;
                }
                else {
                    $this->rslt = FAIL;
                    $this->reason = 'CPS_TEMPERATURE_OK';
                    return false;
                }
            }
            else if ($e[0] == 'V') {
                if ($e[1] < 42) {
                    $this->src = $src;
                    $this->type = 'POWER';
                    $this->sev = 'MIN';
                    $this->sa = 'N';
                    $this->cond = 'NEW';
                    $this->remark = $src . ": " . $evt;
                }
                else {
                    $this->rslt = FAIL;
                    $this->reason = "CPS_VOLTAGE_OK";
                    return false;
                }
            }
            else {
                $this->rslt = FAIL;
                $this->reason = "INVALID_ALM_REQUEST";
                return false;
            }
        }
        else {
            $this->rslt = FAIL;
            $this->reason = "INVALID_ALM_REQUEST";
            return false;
        }

        $qry = "INSERT INTO t_alms VALUES(0, id, '', '$this->sa', '$this->src', '$this->type', '$this->cond', '$this->sev', '', '', '$this->remark',now())";
        $res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
            $this->reason = $qry . "\n" . mysqli_error($db);
            return false;
		}
		else {
            $this->id = $db->insert_id;
            $this->almid = $this->id;
			$this->rslt = "success";
            $this->reason = "ALM_CREATED";
            return true;
		}
    }

}


class ALARM {
    public $id          = 0;
    public $almid       = "";
    public $ack         = "";
    public $sa          = "";
    public $src         = "";
    public $type        = "";
    public $cond        = "";
    public $sev         = "";
    public $psta        = "";
    public $ssta        = "";
    public $comment     = "";
    public $datetime    = "";

    public $rslt        = "";
    public $reason      = "";
    public $rows        = [];

    public function __construct($almid) {
        global $db;

        if ($almid == 'ALL')
            $qry = "SELECT * FROM t_alarms";
        else
            $qry = "SELECT * FROM t_alms WHERE almid = '$almid' LIMIT 1";
        
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
                $this->rslt     = SUCCESS;
                $this->reason   = QUERY_MATCHED;
                $this->rows     = $rows;
                $this->id       = $rows[0]['id'];
                $this->almid    = $rows[0]['almid'];
                $this->ack      = $rows[0]['ack'];
                $this->sa       = $rows[0]['sa'];
                $this->src      = $rows[0]['src'];
                $this->type     = $rows[0]['type'];
                $this->cond     = $rows[0]['cond'];
                $this->sev      = $rows[0]['sev'];
                $this->psta     = $rows[0]['psta'];
                $this->ssta     = $rows[0]['ssta'];
                $this->comment  = $rows[0]['comment'];
                $this->datetime = $rows[0]['datetime'];
                $this->rows = $rows;
                $this->rslt = SUCCESS;
                $this->reason = "ALARM FOUND";
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = "ALARM NOT FOUND";
                $this->rows   = $rows;
            }
        }
    }

    public function addAlarm($almid, $evt, $comment) {

    }
}



?>