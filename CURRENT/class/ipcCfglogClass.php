<?php
    class CFGLOG {

		public $rslt;
		public $reason;
		public $rows;
		
		public function __construct() {
			$this->rslt = 'success';
            $this->reason = "CFGLOG_CONSTRUCTED";
            return;
		}


		public function query($event, $uname, $fromDate, $toDate) {
			global $db;
			
			$fromDate = $fromDate." 00:00:00";
			$toDate = $toDate." 23:59:59";

			$qry = "SELECT * FROM t_cfglog WHERE action LIKE '$event%' AND user LIKE '$uname' AND date >= '$fromDate' AND date <= '$toDate' ORDER BY date DESC";
			// $qry = "SELECT * FROM t_cfglog WHERE action LIKE '$event%' AND user LIKE '%$uname%' AND date >= '$fromdate' AND date <= '$toDate' ORDER BY date DESC";
           
			$res = $db->query($qry);
			if (!$res) {
				$this->rslt = "fail";
				$this->reason = mysqli_error($db);
			}
			else {
				$rows = [];
				$this->rslt = "success";
				$this->reason = "QUERY_SUCCESS";
				if ($res->num_rows > 0) {
					while ($row = $res->fetch_assoc()) {
						$rows[] = $row;
					}
				}
				$this->rows = $rows;
			}
			 
		}


		public function log($user, $action, $port, $fac, $ftyp, $ort, $spcfnc, $result) {
			global $db;

            $qry = "INSERT INTO 
					t_cfglog 
					(user, action, port, fac, ftyp, ort, spcfnc, result) 
					VALUES 
					('$user', '$action', '$port', '$fac', '$ftyp', '$ort', '$spcfnc', '$result')" ;

			$res = $db->query($qry);
			if (!$res) {
				$this->rslt = "fail";
				$this->reason = mysqli_error($db);
			}
			else {
				$rows = [];
				$this->rslt = "success";
				$this->reason = "QUERY_SUCCESS" . ' - ' . $qry ;
				if ($res->num_rows > 0) {
					while ($row = $res->fetch_assoc()) {
						$rows[] = $row;
					}
				}
				$this->rows = $rows;
			}

		}  // end of function log
		
		//delete record based on ipcRef setting
		//expire_date format should be 'YYYY-MM-DD'
		public function deleteExpiredLog($expired_date) {
			global $db;
			
			$qry = "DELETE FROM t_cfglog WHERE date < '$expired_date'";
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
