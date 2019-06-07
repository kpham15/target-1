<?php
    class PROVLOG {

		public $rslt;
		public $reason;
		public $rows;
		
		public function __construct() {
			$this->rslt = 'success';
            $this->reason = "PROVLOG_CONSTRUCTED";
            return;
		}
		

		public function query($uname, $action, $ordno, $ckid, $fromDate, $toDate) {
            global $db;
            
			$fromDate= $fromDate." 00:00:00";
            $toDate = $toDate." 23:59:59";
            
			$qry = "SELECT * FROM t_provlog WHERE user LIKE '$uname' AND action LIKE '$action' AND ordno LIKE '$ordno' AND ckid LIKE '$ckid' AND date >= '$fromDate' AND date <= '$toDate' ORDER BY date DESC";
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
				$this->reason = "SUCCESSFUL - PROVISIONING LOG QUERIED";
				$this->rows = $rows;
			}
		}

		public function queryOrd($ordno) {
			global $db;
            
			$qry = "SELECT * FROM t_provlog WHERE ordno LIKE '$ordno' ORDER BY date DESC";
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
				$this->reason = "SUCCESSFUL - PROVISIONING LOG ORDNO QUERIED";
				$this->rows = $rows;
			}
		}


		public function log($user, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $reason, $tktno) {
			global $db;

			if ($dd == null) $dd ='';
			if ($fdd == null) $fdd='';

            $qry = "INSERT INTO 
					t_provlog 
					(user, ckid, cls, adsr, prot, date, 
					ordno, tktno, dd, fdd, mlo, 
					action, result, ctyp, ffac, fport, 
					tfac, tport) 
					VALUES 
					('$user', '$ckid', '$cls', '$adsr', '$prot', now(), 
					'$ordno', '$tktno', '$dd', '$fdd', '$mlo', 
					'$act', '$reason', '$ctyp', '$ffac', '$fport', 
					'$tfac','$tport')";

			$res = $db->query($qry);
			if (!$res) {
				$this->rslt = "fail";
                $this->reason = mysqli_error($db) . " - " . $qry;
                $this->rows = [];
			}
			else {
                $this->rslt = "success";
                $this->reason = "SUCCESSFUL - PROVISIONING LOG" . ": " . $qry;
                $this->rows = [];
			}

        }  // end of function log

		//delete record based on ipcRef setting
		//expire_date format should be 'YYYY-MM-DD'
		public function deleteExpiredLog($expired_date) {
			global $db;
			
			$qry = "DELETE FROM t_provlog WHERE date < '$expired_date'";
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