<?php

class ALMLOG {
    
    public $rslt;
    public $reason;
    public $rows;

	public function __construct() {
        $this->rslt = 'success';
        $this->reason = "ALMLOG_CONSTRUCTED";
        return;
    }


    public function query($uname, $action, $sev, $src, $fromDate, $toDate) {
        global $db;
        $fromDate= $fromDate." 00:00:00";
        $toDate = $toDate." 23:59:59";
        // $qry = "SELECT * FROM t_almlog";
        $qry = "SELECT * FROM t_almlog WHERE user LIKE '$uname' AND action LIKE '$action' AND sev LIKE '$sev' AND src LIKE '$src' AND date >= '$fromDate' AND date <= '$toDate' ORDER BY date DESC";
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
            $this->rows = $rows;
            $this->rslt = "success";
            $this->reason = "SUCCESSFUL - ALARM LOG QUERIE";
        }
         
    }

    public function log($almid, $ack, $sa, $src, $type, $cond, $sev, $psta, $ssta, $remark, $action, $user, $result) {
		global $db;

		$dd = !empty($dd) ? "'$dd'" : "NULL";
		$fdd = !empty($fdd) ? "'$fdd'" : "NULL";

		$qry = "INSERT INTO 
                t_almlog 
                (almid, ack, sa, src, type, 
                cond, sev, psta, ssta, remark, 
                action, user, result) 
                VALUES 
                ('$almid', '$ack', '$sa', '$src', '$type',
                '$cond', '$sev', '$psta', '$ssta', '$remark',
                '$action', '$user', '$result')";

		$res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
			$this->reason = mysqli_error($db);
			$this->rows = [];
		}
		else {
			$this->rslt = "success";
			$this->reason = "SUCCESSFUL - ALARM LOG INSERTED";
			$this->rows = [];
		}

    }  // end of function log
        
    //delete record based on ipcRef setting
	//expire_date format should be 'YYYY-MM-DD'
	public function deleteExpiredLog($expired_date) {
		global $db;
		
		$qry = "DELETE FROM t_almlog WHERE date < '$expired_date'";
		$res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
			$this->reason = mysqli_error($db);
		}
		else {
			$rows = [];
			$this->rslt = "success";
			$this->reason = "EXPIRED LOGS DELETED";
		}
			
	}

}


?>