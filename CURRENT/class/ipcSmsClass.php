<?php
/*
    Filename: ipcSmsClass.php

*/

    class SMS {
        public $id;
        public $evt;
        public $psta;
        public $ssta;
        public $npsta;
        public $nssta;
        public $rslt;
        public $reason;
        
		public function __construct($psta, $ssta, $evt) {
			global $db;
			
			$this->psta = $psta;
			$this->ssta = $ssta;
			$this->evt = $evt;     
    
			$qry = "SELECT * FROM t_sms WHERE evt='" . $evt . "' AND psta='" . $psta . "' AND ssta='" . $ssta . "'";
			$res = $db->query($qry);
			if (!$res) {
				$this->rslt = FAIL;
				$this->reason = mysqli_error($db);
			}
			else {
				$rows = [];
				if ($res->num_rows > 0) {
					while ($row = $res->fetch_assoc()) {
						$rows[] = $row;
					}
					$this->rslt = SUCCESS;
					$this->reason = "SMS_CONSTRUCTED";
					$this->npsta = $rows[0]["npsta"];
					$this->nssta = $rows[0]["nssta"];
				}
				else {
					$this->rslt = FAIL;
				}
				$this->reason = "SMS: $this->evt($this->psta->$this->npsta - $this->ssta->$this->nssta";
			}			
		}
	}
?>