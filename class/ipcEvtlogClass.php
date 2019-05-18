<?php
	class EVTLOG {
		public $user;
		public $fnc;
		public $evt;
		public $input;

		public $rslt;
		public $reason;
		public $rows;
		
		public function __construct($user, $fnc, $evt, $input=NULL) {
			$this->user = $user;
			$this->fnc = $fnc;
			$this->evt = $evt;
			if ($input == NULL)
				$this->input = '';
			else
				$this->input = $input;

		}
		
		public function log($rslt, $msg) {
			global $db;
			
			$this->reason = $this->input . "\n" . $rslt . ": " . $msg;
			// $qry = "INSERT INTO t_evtlog VALUES(0,'$this->user','$this->fnc','$this->evt','$rslt','$this->reason',now())";

			$qry - "INSERT INTO
					t_evtlog 
					(user, fnc, evt, rslt, detail, 
					time) 
					VALUES 
					('$this->user', '$this->fnc', '$this->evt', '$rslt', '$this->reason', 
					now())";

			$res = $db->query($qry);
			if (!$res) {
				$this->rslt = "fail";
				$this->reason = mysqli_error($db);
			}
		}

		public function query($uname, $fnc, $evt, $days) {
			global $db;
	
			if ($days == "")
				$days = "-3";
			else if ($days == "Last 1 day")
				$days = "-1";
			else if ($days == "Last 5 days")
				$days = "-5";
			else if ($days == "Last 10 days")
				$days = "-10";
			else if ($days == "Last 30 days")
				$days = "-30";
			else
				$days = "-30";
			
			$time = date("Y-m-d H:i:s", strtotime("now " . $days . " days"));
				
			$qry = "SELECT * FROM t_evtlog WHERE user LIKE '%$uname%' AND fnc LIKE '%$fnc%' AND evt LIKE '%$evt%' AND time > '$time' ORDER BY time DESC";
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

	}
		

	class EVENTLOG {
		public $user;
		public $fnc;
		public $evt;
		public $log;

		public $rslt;
		public $reason;
		public $rows;
		
		public function __construct($user, $evt, $fnc, $task, $log=NULL) {
			$this->user = $user;
			$this->fnc = $fnc;
			$this->evt = $evt;
			$this->task = $task;
			if ($log == NULL)
				$this->log = '';
			else
				$this->log = $log;

		}
		
		public function log($rslt, $log) {
			global $db;
			
			$this->log .= $log;
			$qry = "INSERT INTO 
					t_evtlog 
					(user, evt, fnc, task, rslt, 
					detail, time) 
					VALUES 
					('$this->user', '$this->evt', '$this->fnc', '$this->task', '$rslt', 
					'$this->log', now())";

			$res = $db->query($qry);
			if (!$res) {
				$this->rslt = "fail";
				$this->reason = mysqli_error($db);
			}
		}

		public function query($uname, $evt, $fnc, $task, $fromDate, $toDate) {
			global $db;
			
			$fromDate= $fromDate." 00:00:00";
			$toDate = $toDate." 23:59:59";
				
			$qry = "SELECT * FROM t_evtlog WHERE user LIKE '$uname' AND evt LIKE '$evt' AND fnc LIKE '$fnc' AND task LIKE '$task' AND time >= '$fromDate' AND time <= '$toDate' ORDER BY time DESC";
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


		//delete record based on ipcRef setting
		//expire_date format should be 'YYYY-MM-DD'
		public function deleteExpiredLog($expired_date) {
			global $db;
			
			$qry = "DELETE FROM t_evtlog WHERE time < '$expired_date'";
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