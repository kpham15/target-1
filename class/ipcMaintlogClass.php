<?php
	class MAINTLOG {

	public $rslt;
	public $reason;
	public $rows;
	
	public function __construct() {
		$this->rslt = 'success';
					$this->reason = "MAINTLOG_CONSTRUCTED";
					return;
	}
	

	public function query($uname, $tktno, $action, $fromDate, $toDate) {
		global $db;
		
		$fromDate= $fromDate." 00:00:00";
		$toDate = $toDate." 23:59:59";
		
		$qry = "SELECT * FROM t_maintlog WHERE user LIKE '$uname' AND tktno LIKE '$tktno' AND action LIKE '$action' AND date >= '$fromDate' AND date <= '$toDate' ORDER BY date DESC";
		$res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
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
			$this->rslt = "success";
			$this->reason = "SUCCESSFUL - MAINTENANCE LOG";
			$this->rows = $rows;
		}
	}

	public function queryTkt($tktno) {
		global $db;

		$qry = "SELECT * FROM t_maintlog WHERE tktno LIKE '$tktno' ORDER BY date DESC";
		$res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
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
			$this->rslt = "success";
			$this->reason = "SUCCESSFUL - MAINTENANCE LOG TKT QUERIED";
			$this->rows = $rows;
		}
	}

	public function log($user, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result, $ordno) {
		global $db;

		$dd = !empty($dd) ? "'$dd'" : "NULL";
		$fdd = !empty($fdd) ? "'$fdd'" : "NULL";

		$qry = "INSERT INTO 
				t_maintlog 
				(user, tktno, mlo, ckid, cls, 
				adsr, prot, dd, fdd, action, 
				ctyp, ffac, fport, tfac, tport, 
				result) 
				VALUES 
				('$user', '$tktno', '$mlo', '$ckid', '$cls',
				'$adsr', '$prot', '$dd', '$fdd', '$act',
				'$ctyp', '$ffac', '$fport', '$tfac', '$tport', 
				'$result')";
				
		$res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
			$this->reason = mysqli_error($db);
			$this->rows = [];
		}
		else {
			$this->rslt = "success";
			$this->reason = "SUCCESSFUL - MAINTENANCE LOG INSERTED";
			$this->rows = [];
		}

	}  // end of function log
	
	//delete record based on ipcRef setting
	//expire_date format should be 'YYYY-MM-DD'
	public function deleteExpiredLog($expired_date) {
		global $db;
		
		$qry = "DELETE FROM t_maintlog WHERE date < '$expired_date'";
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