<?php
/*  Filename: ipcMaintDiscon.php
    Date: 2018-11-20
    By: Thanh
    Copyright: BHD SOLUTIONS, LLC @ 2018
*/  
	    
	// -- Initialize inputs --
	$act = "";
	if (isset($_POST['act'])) {
		$act = $_POST['act'];
	}

	$ordno = "";
	if (isset($_POST['ordno'])) {
		$ordno = strtoupper($_POST['ordno']);
	}

	$ckid = "";
	if (isset($_POST['ckid'])) {
		$ckid = strtoupper($_POST['ckid']);
	}

	$ffac = "";
	if (isset($_POST['ffac'])) {
		$ffac = $_POST['ffac'];
	}

	$tfac = "";
	if (isset($_POST['tfac'])) {
		$tfac = $_POST['tfac'];
	}

    $tktno = "";
	if (isset($_POST['tktno'])) {
		$tktno = strtoupper($_POST['tktno']);
	}
    
    $dd = "";
	if (isset($_POST['dd'])) {
		$dd = $_POST['dd'];
	}
    
    $fdd = "";
	if (isset($_POST['fdd'])) {
		$fdd = $_POST['fdd'];
	}
    
    $fport = "";
	if (isset($_POST['fport'])) {
		$fport = $_POST['fport'];
	}
    
    $tport = "";
	if (isset($_POST['tport'])) {
		$tport = $_POST['tport'];
	}	
	// $evtLog 	= new EVTLOG($user, "MAINT", $act, $input);
	
	$maintLog 	= new MAINTLOG();
	$evtLog = new EVENTLOG($user, "MAINTENANCE", "SETUP MAINTENANCE CONNECTION", $act, '');

	// --- Dispatch to Funcitons by ACTION ---
		
	if ($act == "MTC_DISCON") {
		
		$result = maintDiscon($ckid, $ffac, $tfac, $tktno, $userObj);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        $maintLog->log($user, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $ordno);
		echo json_encode($result);
        mysqli_close($db);
        $debugObj->close();
        return;
	}
	
    function maintDiscon($ckid, $ffac, $tfac, $tktno, $userObj) {

		$result['log'] = "ACTION = $act | CKID = $ckid | FFAC = $ffac | TFAC = $tfac | TKTNO = $tktno";
		
		if ($userObj->grpObj->maint != "Y") {
			$result['rslt'] = 'fail';
            $result['jeop'] = "SP5:PERMISSION DENIED";            
			$result['reason'] = "MAINTENANCE DISCONNECT - " . 'PERMISSION DENIED';
			return $result;
		}

		if($tktno === "") {
			$result["rslt"] = 'fail';
            $result['jeop'] = "SP5:TKTNO IS EMPTY";            
			$result["reason"] = "MAINTENANCE DISCONNECT - " . "TKTNO IS EMPTY";
			return $result;
		}

		// Check REF table for auto_ordno & auto_ckid
		$refObj = new REF();
		if ($tktno == '' || $tktno == null) {
			if ($refObj->ref[0]['auto_ordno'] == 'Y') {
				$time = new DateTime('now');
				$timestr = $time->format('H:i:s');
				$ordno = strtoupper(substr($userObj->uname, 0, 4)) . $timestr;
			} else {
				$result['rslt'] = 'fail';
				$result['reason'] = "MAINTENANCE DISCONNECT - " . "TKTNO MISSING";
				return $result;
			}
		}
    

		// the ckid must exist in DB and must attach a cktcon
		$cktObj = new CKT($ckid);
		if ($cktObj->rslt == FAIL || $cktObj->cktcon == 0) {
			$result["rslt"] = FAIL;
            $result['jeop'] = "SP5:CKID ". $ckid . " DOES NOT EXIST";            
			$result["reason"] = "MAINTENANCE DISCONNECT - " . "CKID ". $ckid . " DOES NOT EXIST";
			return $result;
		}
		
		// the cktObj->cktcon must exist in DB
		$cktconObj = new CKTCON($cktObj->cktcon);
		if ($cktconObj->rslt == FAIL) {
			$result["rslt"] = FAIL;
            $result['jeop'] = "SP5:CKTCON " . $cktObj->cktcon . " DOES NOT EXIST";           
			$result["reason"] = "MAINTENANCE DISCONNECT - " . "CKTCON " . $cktObj->cktcon . " DOES NOT EXIST";
			return $result;
		}
		
		// the ffac must exist in DB and must be currently mapped
		$ffacObj = new FAC($ffac);
		if ($ffacObj->rslt != SUCCESS || $ffacObj->port_id == 0) {
			$result['rslt'] = "fail";
            $result['jeop'] = "SP2:INVALID FAC(X): " . $ffac;          
			$result['reason'] = "MAINTENANCE DISCONNECT - " . "INVALID FAC(X): " . $ffac;
			return $result;
		}
		
		// the tfac must exist in DB and must be currently mapped
		$tfacObj = new FAC($tfac);
		if ($tfacObj->rslt != SUCCESS || $tfacObj->port_id == 0) {
			$result['rslt'] = "fail";
            $result['jeop'] = "SP2:INVALID FAC(Y): " . $tfac;          
			$result['reason'] = "MAINTENANCE DISCONNECT - " . "INVALID FAC(Y): " . $tfac;
			return $result;
		}

		// first, there must not be any MTCD in the CKT currently (only one MTCD can be at given time)
		if ($cktconObj->findIdxByCtyp('MTCD') > 0) {
			$result['rslt'] = "fail";
            $result['jeop'] = "SP5:MAX ALLOWED NUMBER OF MTCD IS 1. | THERE IS ALREADY ONE MTCD ON THIS CKT";         
			$result['reason'] = "MAINTENANCE DISCONNECT - " . "MAX ALLOWED NUMBER OF MTCD IS 1. | THERE IS ALREADY ONE MTCD ON THIS CKT";
			return $result;
		}

		// second, the pair fac(x) and fac(Y) must exist in one of cktObj->cktcon[idx]
		if ($cktconObj->loadIdxByPortIds($ffacObj->port_id, $tfacObj->port_id) == 0) {
			$result["rslt"] = FAIL;
            $result['jeop'] = "SP4:FAC " . $ffac . " or " . $tfac . " are not part of CKT";     
			$result["reason"] = "MAINTENANCE DISCONNECT - " . "FAC: " . $ffac . " or " . $tfac . " are not part of CKT";
			return $result;
		}

						
		// validate state-event
		$fpObj = new PORT($ffacObj->port_id);
		$tpObj = new PORT($tfacObj->port_id);

		$sms = new SMS($fpObj->psta, $fpObj->ssta, "MT_DISCON");
		if ($sms->rslt != SUCCESS) {
			$result['rslt'] = $sms->rslt;
            $result['jeop'] = "SP3:FAC STATUS (".$fpObj->psta.")";            
			$result['reason'] = "MAINTENANCE DISCONNECT - " . $sms->reason;
			return $result;
		}
		$fpObj->npsta = $sms->npsta;
		$fpObj->nssta = $sms->nssta;

		$sms = new SMS($tpObj->psta, $tpObj->ssta, "MT_DISCON");
		if ($sms->rslt != SUCCESS) {
			$result['rslt'] = $sms->rslt;
            $result['jeop'] = "SP3:FAC STATUS (".$tpObj->psta.")";            
			$result['reason'] = "MAINTENANCE DISCONNECT - " . $sms->reason;
			return $result;
		}
		$tpObj->npsta = $sms->npsta;
		$tpObj->nssta = $sms->nssta;

		// now, process the MTC_DISCONNECT
		// 1) locate the PATH and open all relays
		/* will enable after 100% non-blocking test is done */
		$pathObj = new PATH($fpObj->port, $tpObj->port);
		$pathObj->load();
        if($pathObj->rslt == 'fail') {
			$result['rslt'] = 'fail';
            $result['jeop'] = "SP5:UNALBE TO LOAD PATH";            
            $result['reason'] = "MAINTENANCE DISCONNECT - " . $pathObj->reason;
            return $result; 
        }
        $pathObj->resetPath();
		
		// 2) delete the PATH from t_path
		$pathObj->drop();
		


		// 2.5) update CKTCON path=0
		$cktconObj->updPath($cktconObj->con, $cktconObj->idx, 0);

		// 3) update t_ports ///check ckt_id
		$fpObj->updPsta($fpObj->npsta, $fpObj->nssta, $fpObj->substa);
		if ($fpObj->rslt != SUCCESS) {
			$result['rslt'] = $fpObj->rslt;
            $result['jeop'] = "SP5:$fpObj->reason";            
			$result['reason'] = "MAINTENANCE DISCONNECT - " . $fpObj->reason;
			return;
		}

		$tpObj->updPsta($tpObj->npsta, $tpObj->nssta, $tpObj->substa);
		if ($tpObj->rslt != SUCCESS) {
			$result['rslt'] = $tpObj->rslt;
            $result['jeop'] = "SP5:$tpObj->reason";            
			$result['reason'] = "MAINTENANCE DISCONNECT - " . $tpObj->reason;
			return;
		}

		// 4) update ctyp with MTCD
		$cktconObj->updCtyp($cktObj->cktcon, $cktconObj->idx, 'MTCD', $cktconObj->ctyp);
		if ($cktconObj->rslt == FAIL) {
			$result["rslt"] = FAIL;
            $result['jeop'] = "SP5:$cktconObj->reason";            
			$result["reason"] = "MAINTENANCE DISCONNECT - " . $cktconObj->reason;
			return $result;
		}

		// update ticketNo
		$cktObj->setTktno($tktno);
		if ($cktObj->rslt == FAIL) {
			$result["rslt"] = FAIL;
            $result['jeop'] = "SP5:$cktObj->reason";            
			$result["reason"] = "MAINTENANCE DISCONNECT - " . $cktObj->reason;
			return $result;
		}

		// 5) query the updated cktcon
		$cktconObj->queryCktConWithFac($cktObj->cktcon);
		if ($cktconObj->rslt == FAIL) {
			$result["rslt"] = FAIL;
            $result['jeop'] = "SP5:$cktconObj->reason";            
			$result["reason"] = "MAINTENANCE DISCONNECT - " . $cktconObj->reason;
			return $result;
		}
		
		$result['rows'] = $cktconObj->rows;
		$result["rslt"] = SUCCESS;
		$result["reason"] = "MAINTENANCE DISCONNECT - " . "SUCCESSFUL";
		return $result;
	}

	
?>