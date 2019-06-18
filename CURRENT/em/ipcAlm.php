<?php
/*
* Copy Right @ 2018
* BHD Solutions, LLC.
* Project: CO-IPC
* Filename: coQueryAlm.php
* Change history: 
* 09-27-2018: created (Tracy)
*/	
	
//Initialize expected inputs

$act = "";
if (isset($_POST['act']))
	$act = $_POST['act'];

$id = "";
if (isset($_POST['id']))
	$id = $_POST['id'];

$ack = "";
if (isset($_POST['ack']))
	$ack = $_POST['ack'];

$almid = "";
if (isset($_POST['almid']))
	$almid = $_POST['almid'];

$remark = "";
if (isset($_POST['remark']))
	$remark = $_POST['remark'];

$psta = "";
if (isset($_POST['psta']))
	$psta = $_POST['psta'];

$cond = "";
if (isset($_POST['cond']))
	$cond = $_POST['cond'];

$src = "";
if (isset($_POST['src']))
	$src = $_POST['src'];

$evt = "";
if (isset($_POST['evt']))
	$evt = $_POST['evt'];

$cmd = "";
if (isset($_POST['cmd']))
	$cmd = $_POST['cmd'];

$evtLog = new EVENTLOG($user, "MAINTENANCE", "ALARM ADMINISTRATION", $act, '');


/**
 * Create ALMS object from classes
 */


$almObj = new ALMS();
if ($almObj->rslt == "fail") {
	$result["rslt"]   = "fail";
	$result["reason"] = $almObj->reason;
	$evtLog->log($result["rslt"], $result["reason"]);
	echo json_encode($result);
	mysqli_close($db);
	return;
}

	
/*
	* Dispatch to functions
*/

if ($act == "query") {
	$almObj->queryAlm();
	$result["rslt"]   = $almObj->rslt;
	$result["reason"] = $almObj->reason;
	$result["rows"]   = $almObj->rows;
	echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == 'queryPsta') {
	$almObj->queryAlmByPsta($psta);
	$result['rslt'] = $almObj->rslt;
	$result['reason'] = $almObj->reason;
	$result['rows'] = $almObj->rows;
	echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "ACK") {
	$result = ackAlm($id, $almid, $ack, $remark, $user, $cond, $src, $userObj, $almObj);
	$evtLog->log($result["rslt"], $result["reason"]);
	//$almLog->log($id, $ack, $almObj->sa, $src, $almObj->type, $cond, $almObj->sev, $almObj->psta, $almObj->ssta, $remark, $act, $user, $almObj->rslt.'-'.$almObj->reason);
	echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "UN-ACK") {
	$result = unackAlm($id, $almid, $ack, $remark, $user, $cond, $src, $userObj, $almObj);
	$evtLog->log($result["rslt"], $result["reason"]);
	//$almLog->log($id, $ack, $almObj->sa, $src, $almObj->type, $cond, $almObj->sev, $almObj->psta, $almObj->ssta, $remark, $act, $user, $almObj->rslt.'-'.$almObj->reason);
	echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "CLR") {
	$result = clrAlm($id, $almid, $ack, $remark, $user, $cond, $src, $userObj, $almObj);
	$evtLog->log($result["rslt"], $result["reason"]);
	//$almLog->log($id, $ack, $almObj->sa, $src, $almObj->type, $cond, $almObj->sev, $almObj->psta, $almObj->ssta, $remark, $act, $user, $almObj->rslt.'-'.$almObj->reason);
	echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "REPORT") {
	$result = reportAlm($src, $evt, $userObj);
	$evtLog->log($result["rslt"], $result["reason"]);
	echo json_encode($result);
	mysqli_close($db);
	return;
}

if ($act == "CREATE") {
	$result = createAlm($src, $evt, $userObj);
	$evtLog->log($result["rslt"], $result["reason"]);
	echo json_encode($result);
	mysqli_close($db);
	return;
}

else {
	$result["rslt"] = FAIL;
	$result["reason"] = "This action is under development!";
	$evtLog->log($result["rslt"], $result["reason"]);
	echo json_encode($result);
	mysqli_close($db);
	return;
}


/**
	 * Functions
	 */
function ackAlm($id, $almid, $ack, $remark, $user, $cond, $src, $userObj, $almObj) {
			
	// validate $ack: check user permission for acknowledge alarm
	if ($userObj->grpObj->almadm != "Y") {
		$result["rslt"] = "fail";
		$result["reason"] = "Permission Denied";
		return $result;
	}


	$almObj = new ALMS($almid);
	if ($almObj->rslt == "fail") {
		$result["rslt"]   = "fail";
		$result["reason"] = $almObj->reason;
		return $result;
	}

	if ($almObj->ack != '') {
		$result["rslt"]   = "fail";
		$result["reason"] = "ACK DENIED - ALARM HAS ALREADY BEEN ACKNOWLEDGED";
		return $result;
	}


	$almObj->ackAlm($ack, $remark);
	$result["rslt"]   = $almObj->rslt;
	$result["reason"] = $almObj->reason;
	$result["rows"]   = $almObj->rows;
	return $result;
}
		
function unackAlm($id, $almid, $ack, $remark, $user, $cond, $src, $userObj, $almObj) {
			
	// validate $ack: check user permission for acknowledge alarm
	if ($userObj->grpObj->almadm != "Y") {
			$result["rslt"] = "fail";
			$result["reason"] = "PERMISSION DENIED";
			return $result;
	}

	$almObj = new ALMS($almid);
	if ($almObj->rslt == "fail") {
		$result["rslt"]   = "fail";
		$result["reason"] = $almObj->reason;
		return $result;
	}
	
	if ($almObj->ack == '') {
		$result["rslt"]   = "fail";
		$result["reason"] = "UN-ACK DENIED - ALARM HAS NOT BEEN ACKNOWLEDGED";
		return $result;
	}

	if ($user != $almObj->ack && $userObj->ugrp != 'ADMIN') {
		$result["rslt"]   = "fail";
		$result["reason"] = "UN-ACK DENIED - USER MUST BE (" . $almObj->ack . ") OR MEMBER OF THE GROUP ADMIN";
		return $result;
	}

	if ($remark == "") {
		$result["rslt"]   = "fail";
		$result["reason"] = "UN-ACK DENIED - MISSING COMMENTS";
		return $result;
	}

	$almObj->unackAlm($ack, $user, $remark);
	$result["rslt"]   = $almObj->rslt;
	$result["reason"] = $almObj->reason;
	$result["rows"]   = $almObj->rows;
	return $result;
}

function clrAlm($id, $almid, $ack, $remark, $user, $cond, $src, $userObj, $almObj) {

	//validate $ack: check user permission for acknowledge alarm
	if ($userObj->grpObj->almadm != "Y") {
			$result["rslt"] = "fail";
			$result["reason"] = "PERMISSION DENIED";
			return $result;
	}
	
	$almObj = new ALMS($almid);
	if ($almObj->rslt == "fail") {
		$result["rslt"]   = "fail";
		$result["reason"] = $almObj->reason;
		return $result;
	}

	if ($user != $almObj->ack && $userObj->ugrp != 'ADMIN') {
		$result["rslt"]   = "fail";
		$result["reason"] = "CLEAR ALARM DENIED - USER MUST BE (" . $almObj->ack . ") OR MEMBER OF THE GROUP ADMIN";
		return $result;
	}

	if ($remark == "") {
		$result["rslt"]   = "fail";
		$result["reason"] = "CLEAR ALARM DENIED - MISSING COMMENTS";
		return $result;
	}

	$almObj->clrAlm($ack, $user, $remark);
	$result["rslt"]   = $almObj->rslt;
	$result["reason"] = $almObj->reason;
	$result["rows"]   = $almObj->rows;
	return $result;
}

function reportAlm($src, $evt, $userObj) {
	
	$almObj = new ALMS($src);

	$s = explode('-',$src);

	// for matrix cards
	if ($s[1] == 'MX' || $s[1] == 'MY' || $s[1] == 'MR') {
		// for matrix card removed
		if ($evt == 'OUT') {
			if ($almObj->rslt == FAIL) {
				$type = 'HW';
				$sev = 'MAJ';
				$sa = 'Y';
				//$cond = 'NEW';
				$remark = $src . ": " . $evt;
				$almObj->newAlm($src, $type, $sev, $sa, $remark);
				if ($almObj->rslt == SUCCESS) {

					$result = setNodeAlm($s[0], $almObj);
					// if($result['rslt'] == 'fail') {
					// 	return $result;
					// }

				}
				$result['rslt'] = $almObj->rslt;
				$result['reason'] = $almObj->reason;
				$result['rows'] = $almObj->rows;
				return $result;
			}
			else {
				$result['rslt'] = "fail";
				$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
				return $result;
			}
		}

		// for matrix card inserted
		if ($evt == 'IN') {
			if ($almObj->rslt == SUCCESS) {
				$remark = $src . ": " . $evt;
				$almObj->sysClr($src, $remark);

				setNodeAlm($s[0], $almObj);
				// if($result['rslt'] == 'fail') {
				// 	return $result;
				// }

				$result['rslt'] = $almObj->rslt;
				$result['reason'] = $almObj->reason;
				$result['rows'] = $almObj->rows;
				return $result;
			}
			else {
				$result['rslt'] = "fail";
				$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
				return $result;
			}
		}
		else {
			$result['rslt'] = "fail";
			$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
			return $result;
		}
	}

	// for CPS card
	if ($s[1] == 'CPS') {
		$e = explode('-',$evt);
		$value = (float)$e[1];

		// for high temperature
		if ($e[0] == 'T') {
			if ($value > 100) {
				if ($almObj->rslt == FAIL) {
					$type = 'TEMPERATURE';
					$sev = 'MIN';
					$sa = 'N';
					//$almObj->cond = 'NEW';
					$remark = $src . ": " . $evt;
					$almObj->newAlm($src, $type, $sev, $sa, $remark);
					if ($almObj->rslt == SUCCESS) {

						setNodeAlm($s[0], $almObj);
						// if($result['rslt'] == 'fail') {
						// 	return $result;
						// }

						// $nodeObj = new NODE($s[0]);
						// if ($nodeObj->rslt == SUCCESS) {
						// 	$nodeObj->setAlarm($almObj->sev);
						// 	if($nodeObj->rslt = 'fail') {
						// 		$result['rslt'] = 'fail';
						// 		$result['reason'] = $nodeObj->reason;
						// 		return $result;
						// 	}
						// }
						// $result['rslt'] = $nodeObj->rslt;
						// $result['reason'] = $nodeObj->reason;
						// $result['rows'] = $nodeObj->rows;
						// return $result;
					}
					$result['rslt'] = $almObj->rslt;
					$result['reason'] = $almObj->reason;
					$result['rows'] = $almObj->rows;
					return $result;
				}
				else {
					$result['rslt'] = "fail";
					$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
					return $result;
				}
			}
			else {
				if ($almObj->rslt == SUCCESS) {
					$remark = $src . ": " . $evt;
					$almObj->sysClr($src, $remark);

					setNodeAlm($s[0], $almObj);
					// if($result['rslt'] == 'fail') {
					// 	return $result;
					// }

					$result['rslt'] = $almObj->rslt;
					$result['reason'] = $almObj->reason;
					$result['rows'] = $almObj->rows;
					return $result;
				}
				else {
					$result['rslt'] = FAIL;
					$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
					return $result;
				}
			}
		}
		
		// for low voltage
		if ($e[0] == 'V') {
			if ($value < 42) {
				if ($almObj->rslt == FAIL) {
					$type = 'POWER';
					$sev = 'MIN';
					$sa = 'N';
					//$almObj->cond = 'NEW';
					$remark = $src . ": " . $evt;
					$almObj->newAlm($src, $type, $sev, $sa, $remark);
					if ($almObj->rslt == SUCCESS) {

						setNodeAlm($s[0], $almObj);
						// if($result['rslt'] == 'fail') {
						// 	return $result;
						// }

						// $nodeObj = new NODE($s[0]);
						// if ($nodeObj->rslt == SUCCESS) {
						// 	$nodeObj->setAlarm($almObj->sev);
						// }
						// $result['rslt'] = $nodeObj->rslt;
						// $result['reason'] = $nodeObj->reason;
						// $result['rows'] = $nodeObj->rows;
						// return $result;
					}
					$result['rslt'] = $almObj->rslt;
					$result['reason'] = $almObj->reason;
					$result['rows'] = $almObj->rows;
					return $result;
				}
				else {
					$result['rslt'] = FAIL;
					$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
					return $result;
				}
			}
			else {
				if ($almObj->rslt == SUCCESS) {
					$remark = $src . ": " . $evt;
					$almObj->sysClr($src, $remark);
					
					setNodeAlm($s[0], $almObj);
					// if($result['rslt'] == 'fail') {
					// 	return $result;
					// }
					$result['rslt'] = $almObj->rslt;
					$result['reason'] = $almObj->reason;
					$result['rows'] = $almObj->rows;
					return $result;
				}
				else {
					$result['rslt'] = FAIL;
					$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
					return $result;
				}
			}
		}
		else {
			$result['rslt'] = FAIL;
			$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
			return $result;
		}
	}
	else {
		$result['rslt'] = FAIL;
		$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
		return $result;
	}
}


function createAlm($src, $evt, $userObj) {
	
	$almObj = new ALARM($almid);
	$almid = $src;

	$s = explode('-',$src);

	// for matrix cards
	if ($s[1] == 'MX' || $s[1] == 'MY' || $s[1] == 'MR') {
		// for matrix card removed
		if ($evt == 'OUT') {
			if ($almObj->rslt == FAIL) {
				$type = 'EQUIP';
				$sev = 'MAJ';
				$sa = 'N';
				$comment = "MATRIX CARD REMOVED";
				$almObj->newAlm($src, $type, $sev, $sa, $remark);
				if ($almObj->rslt == SUCCESS) {

					$result = setNodeAlm($s[0], $almObj);
					// if($result['rslt'] == 'fail') {
					// 	return $result;
					// }

				}
				$result['rslt'] = $almObj->rslt;
				$result['reason'] = $almObj->reason;
				$result['rows'] = $almObj->rows;
				return $result;
			}
			else {
				$result['rslt'] = "fail";
				$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
				return $result;
			}
		}

		// for matrix card inserted
		if ($evt == 'IN') {
			if ($almObj->rslt == SUCCESS) {
				$remark = $src . ": " . $evt;
				$almObj->sysClr($src, $remark);

				setNodeAlm($s[0], $almObj);
				// if($result['rslt'] == 'fail') {
				// 	return $result;
				// }

				$result['rslt'] = $almObj->rslt;
				$result['reason'] = $almObj->reason;
				$result['rows'] = $almObj->rows;
				return $result;
			}
			else {
				$result['rslt'] = "fail";
				$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
				return $result;
			}
		}
		else {
			$result['rslt'] = "fail";
			$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
			return $result;
		}
	}

	// for CPS card
	if ($s[1] == 'CPS') {
		$e = explode('-',$evt);
		$value = (float)$e[1];

		// for high temperature
		if ($e[0] == 'T') {
			if ($value > 100) {
				if ($almObj->rslt == FAIL) {
					$type = 'TEMPERATURE';
					$sev = 'MIN';
					$sa = 'N';
					//$almObj->cond = 'NEW';
					$remark = $src . ": " . $evt;
					$almObj->newAlm($src, $type, $sev, $sa, $remark);
					if ($almObj->rslt == SUCCESS) {

						setNodeAlm($s[0], $almObj);
						// if($result['rslt'] == 'fail') {
						// 	return $result;
						// }

						// $nodeObj = new NODE($s[0]);
						// if ($nodeObj->rslt == SUCCESS) {
						// 	$nodeObj->setAlarm($almObj->sev);
						// 	if($nodeObj->rslt = 'fail') {
						// 		$result['rslt'] = 'fail';
						// 		$result['reason'] = $nodeObj->reason;
						// 		return $result;
						// 	}
						// }
						// $result['rslt'] = $nodeObj->rslt;
						// $result['reason'] = $nodeObj->reason;
						// $result['rows'] = $nodeObj->rows;
						// return $result;
					}
					$result['rslt'] = $almObj->rslt;
					$result['reason'] = $almObj->reason;
					$result['rows'] = $almObj->rows;
					return $result;
				}
				else {
					$result['rslt'] = "fail";
					$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
					return $result;
				}
			}
			else {
				if ($almObj->rslt == SUCCESS) {
					$remark = $src . ": " . $evt;
					$almObj->sysClr($src, $remark);

					setNodeAlm($s[0], $almObj);
					// if($result['rslt'] == 'fail') {
					// 	return $result;
					// }

					$result['rslt'] = $almObj->rslt;
					$result['reason'] = $almObj->reason;
					$result['rows'] = $almObj->rows;
					return $result;
				}
				else {
					$result['rslt'] = FAIL;
					$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
					return $result;
				}
			}
		}
		
		// for low voltage
		if ($e[0] == 'V') {
			if ($value < 42) {
				if ($almObj->rslt == FAIL) {
					$type = 'POWER';
					$sev = 'MIN';
					$sa = 'N';
					//$almObj->cond = 'NEW';
					$remark = $src . ": " . $evt;
					$almObj->newAlm($src, $type, $sev, $sa, $remark);
					if ($almObj->rslt == SUCCESS) {

						setNodeAlm($s[0], $almObj);
						// if($result['rslt'] == 'fail') {
						// 	return $result;
						// }

						// $nodeObj = new NODE($s[0]);
						// if ($nodeObj->rslt == SUCCESS) {
						// 	$nodeObj->setAlarm($almObj->sev);
						// }
						// $result['rslt'] = $nodeObj->rslt;
						// $result['reason'] = $nodeObj->reason;
						// $result['rows'] = $nodeObj->rows;
						// return $result;
					}
					$result['rslt'] = $almObj->rslt;
					$result['reason'] = $almObj->reason;
					$result['rows'] = $almObj->rows;
					return $result;
				}
				else {
					$result['rslt'] = FAIL;
					$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
					return $result;
				}
			}
			else {
				if ($almObj->rslt == SUCCESS) {
					$remark = $src . ": " . $evt;
					$almObj->sysClr($src, $remark);
					
					setNodeAlm($s[0], $almObj);
					// if($result['rslt'] == 'fail') {
					// 	return $result;
					// }
					$result['rslt'] = $almObj->rslt;
					$result['reason'] = $almObj->reason;
					$result['rows'] = $almObj->rows;
					return $result;
				}
				else {
					$result['rslt'] = FAIL;
					$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
					return $result;
				}
			}
		}
		else {
			$result['rslt'] = FAIL;
			$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
			return $result;
		}
	}
	else {
		$result['rslt'] = FAIL;
		$result['reason'] = "NO NEW ALARM FOR: " . $src . "(" . $evt . ")";
		return $result;
	}
}


function setNodeAlm($node, $almObj) {
	$nodeObj = new NODE($node);
	if ($nodeObj->rslt == SUCCESS) {
		//Take highest sev for the node. If nothing returns, default = NONE
		$almObj->queryAlmByNode($node);
		// if($almObj->rslt == 'fail') {
		// 	$result['rslt'] = 'fail';
		// 	$result['reason'] = $almObj->reason;
		// 	return $result;
		// }
		if(count($almObj->rows) == 0) {
			$sev = 'NONE';
		}
		else {
			$sev = $almObj->rows[0]['sev'];
		} 

		$nodeObj->setAlarm($sev);
		// if($nodeObj->rslt == 'fail') {
		// 	$result['rslt'] = 'fail';
		// 	$result['reason'] = $nodeObj->reason;
		// 	return $result;
		// }
	}
	$result['rslt'] = $nodeObj->rslt;
	$result['reason'] = $nodeObj->reason;
	$result['rows'] = $nodeObj->rows;
	return $result;
}

?>