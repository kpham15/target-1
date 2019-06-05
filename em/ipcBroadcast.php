<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcBroadcast.php
 * Change history: 
 * 2018-12-20: created (Tracy)
 */

	// Initialize Expected inputs
	
	
    $act = "";
	if (isset($_POST['act']))
		$act = $_POST['act'];
		
	$uname = "";
	if (isset($_POST['uname']))
		$uname = $_POST['uname'];

	$grp = "";
	if (isset($_POST['grp']))
		$grp = $_POST['grp'];

	$ugrp = "";
	if (isset($_POST['ugrp']))
		$ugrp = $_POST['ugrp'];

	$owner = "";
	if (isset($_POST['owner']))
		$owner = $_POST['owner'];

	$owner_id = "";
	if (isset($_POST['owner_id']))
		$owner_id = $_POST['owner_id'];
	
	$sa = "";
	if (isset($_POST['sa']))
		$sa = $_POST['sa'];

	$id = "";
	if (isset($_POST['id']))
		$id = $_POST['id'];

	$stamp = 0 ;
	if (isset($_POST['stamp']))
		$stamp = $_POST['stamp'];

	$wcc = "";
	if (isset($_POST['wcc']))
		$wcc = $_POST['wcc'];

	$frm_id = "";
	if (isset($_POST['frm_id']))
		$frm_id = $_POST['frm_id'];

	$msg = "";
	if (isset($_POST['msg']))
		$msg = $_POST['msg'];

	$detail = "";
	if (isset($_POST['detail']))
		$detail = $_POST['detail'];
	
	$evtLog = new EVENTLOG($user, "USER MANAGEMENT", "BROADCAST NOTIFICATION", $act, $_POST);

	// Dispatch to Functions

	$brdcstObj = new BRDCST();

	if ($brdcstObj->rslt == "fail") {
		$result["rslt"] = "fail";
		$result["reason"] = $brdcstObj->reason;
		$evtLog->log($result["rslt"], $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	
	if ($act == "query" || $act == "find") {
		deleteExpiredBrdcst($brdcstObj);
		$brdcstObj->findBcByUser($uname, $sa);
        $result['rslt'] = $brdcstObj->rslt;
        $result['reason'] = $brdcstObj->reason;
		$result['rows'] = $brdcstObj->rows;
        echo json_encode($result);
		mysqli_close($db);
		return;
    }
   
	if ($act == "ADD") {
		$result = addBroadcast($userObj, $user, $owner, $owner_id, $sa, $msg, $detail, $brdcstObj);
		$evtLog->log($result["rslt"], $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
		
    }
    
	if ($act == "DELETE") {
		$result = delBroadcast($userObj, $id, $owner_id, $brdcstObj, $uname);
		$evtLog->log($result["rslt"], $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
    }
    
	if ($act == "UPDATE") {
		$result = updBroadcast($brdcstObj, $userObj, $id, $user, $stamp, $date, $wcc, $frm_id, $sa, $msg, $detail, $uname, $owner, $owner_id, $grp, $ugrp);
		$evtLog->log($result["rslt"], $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	else {
 		$result["rslt"] = "fail";
		$result["reason"] = "Invalid ACTION";
		$evtLog->log($result["rslt"], $result['reason']);
		echo json_encode($result);
		return;
	}


	// Function Area

		//GETS SETTINGS FROM ipcRef & DELETES logs out of time range
		function deleteExpiredBrdcst($brdcstObj) {

			$refObj = new REF();
	
			//get cfg_del from refObj
			$brdcst_del = $refObj->ref[0]['brdcst_del'];
			
			//convert value into seconds
			$brdcst_del_in_sec = $brdcst_del * 86400;
			
			$current_timestamp = time();
			
			$expired_timestamp = $current_timestamp - $brdcst_del_in_sec;
			
			$expired_date = date('Y-m-d', $expired_timestamp);
	
			
			$brdcstObj->deleteExpiredLog($expired_date);
			return;
		}
	
	function addBroadcast($userObj, $user, $owner, $owner_id, $sa, $msg, $detail, $brdcstObj){

		if ($userObj->grpObj->brdcst != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = 'Permission Denied';
			return $result;
		}
		
		$brdcstObj->addBroadcast($user, $owner, $owner_id, $sa, $msg, $detail);
		$result['rslt'] = $brdcstObj->rslt;
        $result['reason'] = $brdcstObj->reason;
		$result['rows'] = $brdcstObj->rows;

		return $result;

	}

	function delBroadcast($userObj, $id, $owner_id, $brdcstObj, $uname){

		if ($userObj->grpObj->brdcst != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = 'DELETE BROADCAST: USER_PERMISSION_DENIED';
			return $result;
		}

		// deny if not creator and not owner of msg
		if ($userObj->uname != $uname && $userObj->uname != $owner_id) {
			// deny if not admin and not supervisor
			if ($userObj->ugrp != 'ADMIN' && $userObj->ugrp != 'SUPERVISOR') {
				$result['rslt'] = 'fail';
				$result['reason'] = 'DELETE BROADCAST: USER_PERMISSION_DENIED';
				return $result;
			}
			else {
				$ownerObj = new USERS($owner_id);
				if ($ownerObj->rslt != 'success') {
					$result['rslt'] = $ownerObj->rslt;
					$result['reason'] = $ownerObj->reason;
					return $result;
				}
				// deny if user is less priviledge than owner's
				if ($userObj->grp >= $ownerObj->grp) {
					$result['rslt'] = 'fail';
					$result['reason'] = "DELETE BROADCAST: USER_PERMISSION_DENIED";
					return $result;
				}
			}
		}

		$brdcstObj->delBroadcast($id);
		$result['rslt'] = $brdcstObj->rslt;
        $result['reason'] = $brdcstObj->reason;
		$result['rows'] = $brdcstObj->rows;
		return $result;
	}

	function updBroadcast($brdcstObj, $userObj, $id, $user, $stamp, $date, $wcc, $frm_id, $sa, $msg, $detail, $uname, $owner, $owner_id, $grp, $ugrp){

		if ($userObj->grpObj->brdcst != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = 'Permission Denied';
			return $result;
		}

		// deny if not creator and not owner of msg
		if ($userObj->uname != $uname && $userObj->uname != $owner_id) {
			// deny if not admin and not supervisor
			if ($userObj->ugrp != 'ADMIN' && $userObj->ugrp != 'SUPERVISOR') {
				$result['rslt'] = 'fail';
				$result['reason'] = 'DELETE BROADCAST: USER_PERMISSION_DENIED';
				return $result;
			}
			else {
				$ownerObj = new USERS($owner_id);
				if ($ownerObj->rslt != 'success') {
					$result['rslt'] = $ownerObj->rslt;
					$result['reason'] = $ownerObj->reason;
					return $result;
				}
				// deny if user is less priviledge than owner's
				if ($userObj->grp >= $ownerObj->grp) {
					$result['rslt'] = 'fail';
					$result['reason'] = "DELETE BROADCAST: USER_PERMISSION_DENIED";
					return $result;
				}
			}
		}

		$brdcstObj->updBroadcast($id,$stamp, $date, $wcc, $frm_id, $sa, $msg, $detail );
		$result['rslt'] = $brdcstObj->rslt;
        $result['reason'] = $brdcstObj->reason;
		$result['rows'] = $brdcstObj->rows;

		return $result;
	}
	
?>
