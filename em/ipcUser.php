<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: coQueryUser.php
 * Change history: 
 * 2018-10-05: created (Thanh)
 */
	
 	/* Initialize expected inputs */

    $id = "";
	if (isset($_POST['id']))
		$id = $_POST['id'];

    $act = "";
	if (isset($_POST['act']))
		$act = $_POST['act'];

    $stat = "";
	if (isset($_POST['stat']))
		$stat = strtoupper($_POST['stat']);

    $uname = "";
	if (isset($_POST['uname']))
		$uname = strtoupper($_POST['uname']);

    $lname = "";
	if (isset($_POST['lname']))
		$lname = strtoupper($_POST['lname']);

    $fname = "";
	if (isset($_POST['fname']))
		$fname = strtoupper($_POST['fname']);

    $mi = "";
	if (isset($_POST['mi']))
		$mi = strtoupper($_POST['mi']);

    $ssn = "";
	if (isset($_POST['ssn']))
		$ssn = $_POST['ssn'];

    $tel = "";
	if (isset($_POST['tel']))
		$tel = $_POST['tel'];

    $email = "";
	if (isset($_POST['email']))
		$email = strtoupper($_POST['email']);

    $title = "";
	if (isset($_POST['title']))
		$title = strtoupper($_POST['title']);

    $ugrp = "";
	if (isset($_POST['ugrp']))
		$ugrp = $_POST['ugrp'];

	// $evtLog = new EVTLOG($user, "USER", $act);
	// $input = "UNAME=$uname | LNAME=$lname | FNAME=$fname | MI=$mi | SSN=$ssn | TEL=$tel | EMAIL=$email | TITLE=$title | UGRP=$ugrp";

    //$evtLog = new EVENTLOG($user, "USER MANAGEMENT", "SETUP USER", $act, $_POST);


	// Dispatch to Functions

	$userObj = new USERS($user);
	if ($userObj->rslt != SUCCESS) {
		$result['rslt'] = $userObj->rslt;
		$result['reason'] = $userObj->reason;
		$evtLog->log($result["rslt"], $result["reason"]);
        mysqli_close($db);
		echo json_encode($result);
		return;
	}

	$refObj = new REF();
	if ($refObj->rslt == 'fail') {
        $result["rslt"] = 'fail';
        $result["reason"] = $refObj->reason;
        $evtLog->log($result["rslt"], $result["reason"]);
        echo json_encode($result);
        mysqli_close($db);
        return;
	}

	// validate login user
	$result = lib_ValidateUser($userObj);
	if ($result['rslt'] == 'fail') {
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	
	// $userValidate = libValidateUser($userObj, $refObj);
	// if ($userValidate['rslt'] == 'fail') {
    //     $evtLog->log($userValidate["rslt"], $userValidate["reason"]);
    //     $debugObj->logEvent($userValidate["rslt"], $userValidate["reason"]);
	// 	$debugObj->closeLogfile();
    //     echo json_encode($userValidate);
    //     mysqli_close($db);
    //     return;
	// }

	// $scanUserTimeout = libScanUserTimeout($userObj, $refObj);
	// if($scanUserTimeout['rslt'] == 'fail') {
	// 	$evtLog->log($scanUserTimeout["rslt"], $scanUserTimeout["reason"]);
    //     $debugObj->logEvent($scanUserTimeout["rslt"], $scanUserTimeout["reason"]);
	// }

	$targetUserObj = new USERS();
	$evtLog = new EVENTLOG($user, "USER MANAGEMENT", "SETUP USER", $act, '');

	if ($act == 'queryBrdcstOwner') {
		$targetUserObj->query();
		$result['rslt'] = $targetUserObj->rslt;
		$result['reason'] = $targetUserObj->reason;
		$result['rows'] = getBrdcstOwners($targetUserObj->rows);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}

	if ($act == "query") {
		$targetUserObj->query();
        $result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = $targetUserObj->reason;
        $result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);
        echo json_encode($result);
		mysqli_close($db);
        return;
	}

	if ($act == "findUser") {
		$targetUserObj->queryByStatus($uname, $stat);
		$result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = $targetUserObj->reason;
		$result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);
        echo json_encode($result);
		mysqli_close($db);
        return;
	}
	
	if ($act == "findNameSsn") {
		$targetUserObj->queryByUName($lname, $fname);
        $result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = $targetUserObj->reason;
        $result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	
	if ($act == "findTelEmail") {
		$targetUserObj->queryByTel($tel, $email);
        $result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = $targetUserObj->reason;
        $result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}
	
	if ($act == "findTitleGrp") {
		$targetUserObj->queryByUserGrp($title, $ugrp);
        $result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = $targetUserObj->reason;
        $result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}
	
	if ($act == "add" || $act == "ADD") {
		$result = addUser($userObj, $uname, $lname, $fname, $mi, $ssn, $tel, $email, $title, $ugrp, $targetUserObj);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}

	if ($act == "upd" || $act == "UPDATE") {
		$result = updUser($userObj, $uname, $lname, $fname, $mi, $ssn, $tel, $email, $title, $ugrp);

		/* sample code for eventlog */
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
		/* end of sample code */

		echo json_encode($result);
		mysqli_close($db);
        return;
	}

	if ($act == "LOCK") {
		$result = lockUser($userObj, $uname);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}

	if ($act == "UNLOCK") {
		$result = unlockUser($userObj, $uname);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}

	if ($act == "DISABLE") {
		$result = disableUser($userObj, $uname);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}

	if ($act == "ENABLE") {
		$result = enableUser($userObj, $uname);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}

	if ($act == "DELETE") {
		$result = deleteUser($userObj, $uname);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}

	if ($act == "RESET_PW") {
		$result = resetPw($userObj, $uname);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}

	else {
 		$result["rslt"] = "fail";
		$result["reason"] = $act . " is under development or not supported";
		$evtLog->log($result["rslt"], $result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
        return;
	}
	
	
	// Functions section

	function addUser($userObj, $uname, $lname, $fname, $mi, $ssn, $tel, $email, $title, $ugrp, $targetUserObj){

		try{
				$result['log'] = "ACTION = ADD | UNAME = $uname | FNAME = $fname | MI = $mi | LNAME = $lname | SSN = $ssn | TEL = $tel | EMAIL = $email | TITLE = $title | UGRP = $ugrp";

			if ($userObj->grpObj->setuser != "Y") {
				$result['rslt'] = 'fail';
				$result['reason'] = "ADD_USER: Permission Denied";
				return $result;
			}

			///ONLY ADMIN GRP CAN ADD USER
			if ($userObj->ugrp != 'ADMIN') {
				$result['rslt'] = 'fail';
				$result['reason'] = "ADD_USER: PERMISSION_DENIED";
				return $result;
			}
			//ONLY USER ADMIN CAN ADD USER ADMIN GRP
			if($ugrp == "ADMIN" && $userObj->uname != 'ADMIN') {
				$result['rslt'] = 'fail';
				$result['reason'] = "ADD_USER: PERMISSION_DENIED_FOR_CREATE_USER_ADMIN";
				return $result;
			}

			$targetUserObj->addUser($uname, $lname, $fname, $mi, $ssn, $tel, $email, $title, $ugrp);
			if($targetUserObj->rslt == "fail") {
				$result['rslt'] = $targetUserObj->rslt;
				$result['reason'] = "ADD_USER: ".$targetUserObj->reason;
				return $result;
			}
			$targetUserObj->queryByUName("","");
			
			$result['rslt'] = $targetUserObj->rslt;
			$result['reason'] = "USER_ADDED";
			$result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);
			return $result;
		
		}
		catch (Exception $e) {
			if($e->getCode() == 100) {
				$result["rslt"]     = 'fail';
				$result["reason"]   = $e->getMessage();
				return $result;
			}
		}
	}

	function updUser($userObj, $uname, $lname, $fname, $mi, $ssn, $tel, $email, $title, $ugrp){

		if ($userObj->grpObj->setuser != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = "UPDATE_USER: PERMISSION DENIED";
			return $result;
		}

		$targetUserObj = new USERS($uname);

		// sample code for preparing log data
		// compare each input with targetUser->member and log those that are diff

		$result['log'] = 'ACTION=UPDATE';
		if ($lname != $targetUserObj->lname)
			$result['log'] .= " | LNAME=" . $targetUserObj->lname . " --> " . $lname;

		if ($fname != $targetUserObj->fname)
			$result['log'] .= " | FNAME=" . $targetUserObj->fname . " --> " . $fname;
			
		if ($mi != $targetUserObj->mi)
			$result['log'] .= " | MI=" . $targetUserObj->mi . " --> " . $mi;
		
		if ($ssn != $targetUserObj->ssn)
			$result['log'] .= " | SSN=" . $targetUserObj->ssn . " --> " . $ssn;

		if ($tel != $targetUserObj->tel)
			$result['log'] .= " | TEL=" . $targetUserObj->tel . " --> " . $tel;

		if ($email != $targetUserObj->email)
			$result['log'] .= " | EMAIL=" . $targetUserObj->email . " --> " . $email;

		if ($title != $targetUserObj->title)
			$result['log'] .= " | TITLE=" . $targetUserObj->title . " --> " . $title;

		// end of sample code

		if ($targetUserObj->rslt != SUCCESS) {
			$result['rslt'] = $targetUserObj->rslt;
			$result['reason'] = "UPDATE_USER: ".$targetUserObj->reason;
			return $result;
		}
		
		// user can update his/her own tel,email
		if($userObj->uname == $targetUserObj->uname) {
			// only admins can change their own names
			if ($userObj->grp < 2)
				$targetUserObj->updUser($lname, $fname, $mi, $ssn, $tel, $email, $title, $targetUserObj->ugrp);
				// other users cannot change their names
			else {
				$targetUserObj->updUser("", "", "", "", $tel, $email, "", $targetUserObj->ugrp);
			}
		}
		else {
			// ADMIN and SUPERVISOR user can update other users in lower ugrp
			if ($userObj->grp < 3 && $userObj->grp < $targetUserObj->grp) {
				$targetUserObj->updUser($lname, $fname, $mi, $ssn, $tel, $email, $title, $ugrp);
			}
			else {
				$result['rslt'] = 'fail';
				$result['reason'] = "UPDATE_USER: PERMISSION_DENIED";
				return $result;
			}
		}

		if ($targetUserObj->rslt == "fail") {
			$result['rslt'] = $targetUserObj->rslt;
			$result['reason'] = "UPDATE_USER: " . $targetUserObj->reason;
			return $result;
		}

		$targetUserObj->queryByUName("","");
		$result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = "USER_UPDATED";
		$result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);	
		return $result;
	}

	function lockUser($userObj, $uname) {

		$result['log'] = "ACTION = LOCK | UNAME = $uname";

		if ($userObj->grpObj->setuser != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = "LOCK_USER: Permission Denied";
			return $result;
		}

		$targetUserObj = new USERS($uname);
		
		if ($targetUserObj->rslt != SUCCESS) {
			$result['rslt'] = $targetUserObj->rslt;
			$result['reason'] = "LOCK_USER: ".$targetUserObj->reason;
			return $result;
		}

		
		// only ADMIN and SUPERVISOR users can LOCK a user in lower grp
		if ($userObj->grp < 3 && $userObj->grp < $targetUserObj->grp) {
			$targetUserObj->lckUser();
			if ($targetUserObj->rslt == "fail") {
				$result['rslt'] = $targetUserObj->rslt;
				$result['reason'] = "LOCK_USER: ".$targetUserObj->reason;
				return $result;
			}
		}
		else {
			$result['rslt'] = 'fail';
			$result['reason'] = "LOCK_USER: PERMISSION_DENIED";
			return $result;
		}

		$targetUserObj->queryByUName("","");
		$result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = "USER_LOCKED";
		$result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);	
		return $result;
	}
	
	function unlockUser($userObj, $uname) {

		$result['log'] = "ACTION = UNLOCK | UNAME = $uname";

		if ($userObj->grpObj->setuser != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = "UNLOCK_USER: Permission Denied";
			return $result;
		}

		$targetUserObj = new USERS($uname);

		if ($targetUserObj->rslt != SUCCESS) {
			$result['rslt'] = $targetUserObj->rslt;
			$result['reason'] = "UNLOCK_USER: ".$targetUserObj->reason;
			return $result;
		}

		// only ADMIN and SUPERVISOR users can UNLOCK a user in lower grp
		if ($userObj->grp < 3 && $userObj->grp < $targetUserObj->grp) {
			$targetUserObj->unlckUser();
			if ($targetUserObj->rslt == "fail") {
				$result['rslt'] = $targetUserObj->rslt;
				$result['reason'] = "UNLOCK_USER: ".$targetUserObj->reason;
				return $result;
			}
		}
		else {
			$result['rslt'] = 'fail';
			$result['reason'] = "UNLOCK_USER: PERMISSION_DENIED";
			return $result;
		}

		$targetUserObj->queryByUName("","");
		$result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = "USER_UNLOCKED";
		$result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);	
		return $result;
	}	

	function disableUser($userObj, $uname) {

		$result['log'] = "ACTION = DISABLE | UNAME = $uname";

		if ($userObj->grpObj->setuser != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = "DISABLE_USER: Permission Denied";
			return $result;
		}

		$targetUserObj = new USERS($uname);

		if ($targetUserObj->rslt != SUCCESS) {
			$result['rslt'] = $targetUserObj->rslt;
			$result['reason'] = "DISABLE_USER: ".$targetUserObj->reason;
			return $result;
		}

		// only ADMIN and SUPERVISOR users can disable a user in lower grp
		if ($userObj->grp < 3 && $userObj->grp < $targetUserObj->grp) {
			$targetUserObj->disableUser();
			if ($targetUserObj->rslt == "fail") {
				$result['rslt'] = $targetUserObj->rslt;
				$result['reason'] = "DISABLE_USER: ".$targetUserObj->reason;
				return $result;
			}
		}
		else {
			$result['rslt'] = 'fail';
			$result['reason'] = "DISABLE_USER: PERMISSION_DENIED";
			return $result;
		}

		$targetUserObj->queryByUName("","");
		$result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = "USER_DISABLED";
		$result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);	
		return $result;
	}

	function enableUser($userObj, $uname) {

		$result['log'] = "ACTION = ENABLE | UNAME = $uname";

		if ($userObj->grpObj->setuser != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = "ENABLE_USER: Permission Denied";
			return $result;
		}

		$targetUserObj = new USERS($uname);
		
		if ($targetUserObj->rslt != SUCCESS) {
			$result['rslt'] = $targetUserObj->rslt;
			$result['reason'] = "ENABLE_USER: ".$targetUserObj->reason;
			return $result;
		}

		// only ADMIN and SUPERVISOR users can enable a user in lower grp
		if ($userObj->grp < 3 && $userObj->grp < $targetUserObj->grp) {
			$targetUserObj->enableUser();
			if ($targetUserObj->rslt == "fail") {
				$result['rslt'] = $targetUserObj->rslt;
				$result['reason'] = "ENABLE_USER: ".$targetUserObj->reason;
				return $result;
			}
		}
		else {
			$result['rslt'] = 'fail';
			$result['reason'] = "ENABLE_USER: PERMISSION_DENIED";
			return $result;
		}

		$targetUserObj->queryByUName("","");
		$result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = "USER_ENABLED";
		$result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);	
		return $result;
	}

	function deleteUser($userObj, $uname) {

		$result['log'] = "ACTION = DELETE | UNAME = $uname";

		if ($userObj->grpObj->setuser != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = "DELETE_USER: Permission Denied";
			return $result;
		}

		$targetUserObj = new USERS($uname);

		if ($targetUserObj->rslt != SUCCESS) {
			$result['rslt'] = $targetUserObj->rslt;
			$result['reason'] = "DELETE_USER: ".$targetUserObj->reason;
			return $result;
		}
		
		// only ADMIN and SUPERVISOR users can delete a user in lower grp
		if ($userObj->grp < 3 && $userObj->grp < $targetUserObj->grp) {
			$targetUserObj->deleteUser();
			if ($targetUserObj->rslt == "fail") {
				$result['rslt'] = $targetUserObj->rslt;
				$result['reason'] = "DELETE_USER: ".$targetUserObj->reason;
				return $result;
			}
		}
		else {
			$result['rslt'] = 'fail';
			$result['reason'] = "DELETE_USER: PERMISSION_DENIED";
			return $result;
		}

		$targetUserObj->queryByUName("","");
		$result['rslt'] = $targetUserObj->rslt;
        $result['reason'] = "USER_DELETED";
		$result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);	
		return $result;
	}

	function resetPw($userObj, $uname) {
		try {

			$result['log'] = "ACTION = RESET_PW | UNAME = $uname";

			if ($userObj->grpObj->setuser != "Y") {
				$result['rslt'] = 'fail';
				$result['reason'] = "RESET_PW: Permission Denied";
				return $result;
			}

			$targetUserObj = new USERS($uname);

			if ($targetUserObj->rslt != SUCCESS) {
				$result['rslt'] = $targetUserObj->rslt;
				$result['reason'] = "RESET_USER_PW: ".$targetUserObj->reason;
				return $result;
			}
			
			// only ADMIN and SUPERVISOR users can resetpw a user in lower grp
			if ($userObj->grp < 3 && $userObj->grp < $targetUserObj->grp) {
				$targetUserObj->resetPw();
				if ($targetUserObj->rslt == "fail") {
					$result['rslt'] = $targetUserObj->rslt;
					$result['reason'] = "RESET_PW: ".$targetUserObj->reason;
					return $result;
				}
			}
			else {
				$result['rslt'] = 'fail';
				$result['reason'] = "RESET_PW: USER_PERMISSION_DENIED";
				return $result;
			}

			$targetUserObj->queryByUName("","");
			$result['rslt'] = $targetUserObj->rslt;
			$result['reason'] = "USER_PW_RESET";
			$result['rows'] = libFilterUsers($userObj, $targetUserObj->rows);	
			return $result;
		}
		catch (Exception $e) {
			if($e->getCode() == 100) {
				$result["rslt"]     = 'fail';
				$result["reason"]   = $e->getMessage();
				return $result;
			}
		}
	}

	function getBrdcstOwners($ownerRows) {
		$rows = [];
		$len = count($ownerRows);
		for ($i=0; $i<$len; $i++) {
			if (strtoupper($ownerRows[$i]['uname']) == 'ADMIN' || strtoupper($ownerRows[$i]['uname']) == 'SYSTEM')
				continue;
			$row['uname'] = $ownerRows[$i]['uname'];
			$row['lname'] = $ownerRows[$i]['lname'];
			$row['fname'] = $ownerRows[$i]['fname'];
			$row['mi'] = $ownerRows[$i]['mi'];
			$rows[] = $row;
		}
		return $rows;
	}
?>
