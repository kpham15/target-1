<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: coCommonFunctions.php
 * Change history: 
 * 2018-10-10: created (Ninh)
 * 2018-11-20: updated (Ninh)
 */
 
	class Db {
		public $host;
		public $dbname;
		public $ui;
		public $pw;
		public $con;
		public $rslt;
		public $reason;
		
		public function __construct() {
			$this->host = "localhost";
			// $this->dbname = "co5k"; $this->ui = "ninh"; $this->pw = "C0nsulta!!!";
			$this->dbname = "co5k"; $this->ui = "ninh"; $this->pw = "c0nsulta";
			// $this->dbname = "co5k"; $this->ui = "root"; $this->pw = "Qaz!2345";
			// $this->dbname = "wizzis5_co5k"; $this->ui = "wizzis5_co5kuser"; $this->pw = "co5kuser1";
			
			
			$this->con = mysqli_connect($this->host, $this->ui, $this->pw, $this->dbname);
			if (mysqli_connect_errno())
			{
				$this->rslt = "fail";
				$this->reason = mysqli_connect_error();
				mysqli_close($db);
			}
		}
	}

	class ipcDb {
		public $host;
		public $dbname;
		public $ui;
		public $pw;
		public $con;
		public $rslt;
		public $reason;
		
		public function __construct() {
			$this->host = "localhost";
			$this->dbname = "ipcdb"; $this->ui = "ninh"; $this->pw = "c0nsulta";
			// $this->dbname = "ipcdb"; $this->ui = "root"; $this->pw = "Qaz!2345";
			//$this->dbname = "wizzis5_co5k"; $this->ui = "wizzis5_co5kuser"; $this->pw = "co5kuser1";
			
			
			$this->con = mysqli_connect($this->host, $this->ui, $this->pw, $this->dbname);
			if (mysqli_connect_errno())
			{
				$this->rslt = "fail";
				$this->reason = mysqli_connect_error();
				mysqli_close($db);
			}
		}
	}


	class Fac {
		public $id;
		public $fac;
		public $ftyp;
		public $ort;
		public $spcfnc;
		public $port_id;
		public $rslt;
		public $reason;
		
		public function __construct($fac) {
			global $db;
			
			$qry = "SELECT * FROM t_facs WHERE fac='" . $fac . "' LIMIT 1";
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
					$this->rslt = "success";
					$this->fac = $rows[0]["fac"];
					$this->ftyp = $rows[0]["ftyp"];
					$this->ort = $rows[0]["ort"];
					$this->spcfnc = $rows[0]["spcfnc"];
					$this->port_id = $rows[0]["port_id"];
					if ($this->port_id == 0) {
						$this->rslt = "fail";
						$this->reason = "FAC not yet mapped";
					}
				}
				else {
					$this->rslt = "fail";
					$this->reason = "FAC not exist";
				}
			}
		}
			
	}
	
	class Port {
		public $id;
		public $node;
		public $slot;
		public $ptyp;
		public $pnum;
		public $psta;
		public $ssta;
		public $substa;
		public $fac_id;
		public $ckt_id;
		public $cktcon;
		public $mp_id;
		public $npsta;
		public $nssta;
		public $rslt;
		public $reason;
		
		
		public function __construct($port_id) {
			global $db;
			
			$qry = "SELECT * FROM t_ports WHERE id='" . $port_id . "' LIMIT 1";
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
					$this->rslt = "success";
					$this->id = $rows[0]["id"];
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
					$this->mp_id = $rows[0]["mp_id"];
					if ($this->fac_id == 0) {
						$this->rslt = "fail";
						$this->reason = "PORT not yet mapped";
					}
				}
				else {
					$this->rslt = "fail";
					$this->reason = "PORT not exist";
				}
			}
		}

	}
	
	class ckt {
		public $id;
		public $ckid;
		public $cls;
		public $adsr;
		public $prot;
		public $ordno;
		public $mlo;
		public $date;
		public $cktcon;
		public $user;
		public $rslt;
		public $reason;
		
		public function __construct($ckid) {
			global $db;
			
			$qry = "SELECT * FROM t_ckts WHERE ckid='" . $ckid . "' LIMIT 1";
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
					$this->rslt = "success";
					$this->id = $rows[0]["id"];
					$this->ckid = $rows[0]["ckid"];
					$this->cls = $rows[0]["cls"];
					$this->adsr = $rows[0]["adsr"];
					$this->ordno = $rows[0]["ordno"];
					$this->mlo = $rows[0]["mlo"];
					$this->date = $rows[0]["date"];
					$this->cktcon = $rows[0]["cktcon"];
				}
				else {
					$this->rslt = "fail";
					$this->reason = "CKID not exist";
				}
			}
		}

	}

	
	class cktcon {
		public $id;
		public $con = array();
		public $rslt;
		public $reason;
		
		public function __construct($cktcon) {
			global $db;
			
			$qry = "SELECT * FROM t_cktcon WHERE con=" . $cktcon;
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
					$this->rslt = "success";
					$this->con = $rows;
				}
				else {
					$this->rslt = "fail";
					$this->reason = "CKTCON not exist";
				}
			}
		}
	}


	class EvtLog {
		public $user;
		public $fnc;
		public $evt;
		
		public function __construct($user, $fnc, $evt) {
			$this->user = $user;
			$this->fnc = $fnc;
			$this->evt = $evt;
		}
		
		public function log($rslt, $reason) {
			global $db;
			
			$qry = "INSERT INTO t_evtlog VALUES(0,'" . $this->user . "','" . $this->fnc . "','" . $this->evt . "','" . $rslt;
			$qry .= "','" . $reason . "',now())";
			
			$res = $db->query($qry);
			if (!$res) {
				$this->rslt = "fail";
				$this->reason = mysqli_error($db);
			}
		}
	}


	class Mxc {
		public $id;
		public $node;
		public $slot;
		public $type;
		public $psta;
		public $ssta;
		public $rslt;
		public $reason;
		
		public function __construct($node, $slot) {
			global $db;
			
			$this->node = $node;
			$this->slot = $slot;
			
			$qry = "SELECT * FROM t_mxc WHERE node=" . $node . " AND slot=" . $slot;
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
					$this->rslt = "success";
					$this->type = $rows[0]["type"];
					$this->psta = $rows[0]["psta"];
					$this->ssta = $rows[0]["ssta"];
					$this->rslt = "success";
					$this->reason = "";
				}
				else {
					$this->rslt = "fail";
					$this->reason = "Invalid node: " . $this->node . ", slot: " . $this->slot;
				}
			}			
		}
	}
	
			

	class Sms {
		public $psta;
		public $ssta;
		public $evt;
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
				$this->rslt = "fail";
				$this->reason = mysqli_error($db);
			}
			else {
				$rows = [];
				if ($res->num_rows > 0) {
					while ($row = $res->fetch_assoc()) {
						$rows[] = $row;
					}
					$this->rslt = "success";
					$this->npsta = $rows[0]["npsta"];
					$this->nssta = $rows[0]["nssta"];
				}
				else {
					$this->rslt = "fail";
					$this->reason = "Invalid STAT: " . $this->psta . " - " . $this->ssta;
				}
			}			
		}
	}
	

	class Brdcst {
		public $id;
		public $user;
		public $owner;
		public $fname;
		public $lname;
		public $mi;
		public $date;
		public $wcc;
		public $frm_id;
		public $sa;
		public $msg;
		public $detail;
		public $rslt;
		public $reason;
		
		public function __construct($id) {
			global $db;
			
			$qry = "SELECT * FROM t_brdcst WHERE id='" . $id . "' LIMIT 1";
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
					$this->rslt = "success";
					$this->id = $rows[0]["id"];
					$this->user = $rows[0]["user"];
					$this->date = $rows[0]["date"];
					$this->wcc = $rows[0]["wcc"];
					$this->frm_id = $rows[0]["frm_id"];
					$this->sa = $rows[0]["sa"];
					$this->msg = $rows[0]["msg"];
					$this->detail = $rows[0]['detail'];
				}
				else {
					$this->rslt = "fail";
					$this->reason = "MSG not exist";
				}
			}
		}
	}
	

	class User {
		public $id;
		public $uname;
		public $fname;
		public $lname;
		public $mi;
		public $ssn;
		public $grp;
		public $ugrp;
		public $tel;
		public $email;
		public $title;
		public $rslt;
		public $reason;
		
		public function __construct($user) {
			global $db;
			
			$qry = "SELECT * FROM t_users WHERE uname='" . $user . "' LIMIT 1";
			$res = $db->query($qry);
			if (!$res) {
				$this->rslt = "fail";
				$this->reason = mysqli_error($db);
			}
			else {

				$this->rslt = "success";
				$rows = [];
				if ($res->num_rows > 0) {
					while ($row = $res->fetch_assoc()) {
						$rows[] = $row;
					}
					$this->rslt = "success";
					$this->id = $rows[0]["id"];
					$this->uname = $rows[0]["uname"];
					$this->fname = $rows[0]["fname"];
					$this->lname = $rows[0]["lname"];
					$this->mi = $rows[0]["mi"];
					$this->ssn = $rows[0]["ssn"];
					$this->grp = $rows[0]["grp"];
					$this->ugrp = $rows[0]["ugrp"];
				}
				else {
					$this->rslt = "fail";
					$this->reason = "USER (" . $user . ") not exist";
				}
			}
		}
	}
	
			
    // Support functions
	function userPermission($fnc, $user) {
		//temporary permit all users
		return true;
	}
 


?>
