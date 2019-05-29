<?php
/*  Filename: ipcMaintRestore.php
    Date: 2019-01-20
    By: Ninh
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

    $mlo = "";
	if (isset($_POST['mlo'])) {
		$mlo = strtoupper($_POST['mlo']);
	}

	$ckid = "";
	if (isset($_POST['ckid'])) {
		$ckid = strtoupper($_POST['ckid']);
	}

    $cls = "";
	if (isset($_POST['cls'])) {
		$cls = strtoupper($_POST['cls']);
	}

    $adsr = "";
	if (isset($_POST['adsr'])) {
		$adsr = strtoupper($_POST['adsr']);
	}

    $prot = "";
	if (isset($_POST['prot'])) {
		$prot = strtoupper($_POST['prot']);
	}

    $ctyp = "";
	if (isset($_POST['ctyp'])) {
		$ctyp = strtoupper($_POST['ctyp']);
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
    
    // $evtLog = new EVTLOG($user, "MAINT", $act, $input);
    

    $maintLog = new MAINTLOG ();
    $evtLog = new EVENTLOG($user, "MAINTENANCE", "SETUP MAINTENANCE CONNECTION", $act, '');

    //DISPATCH AREA
		
	if ($act == "RESTORE_MTCD") {
        $result = maintRestoreMtcd($userObj, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac);
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        $maintLog->log($user, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $ordno);
        echo json_encode($result);
        mysqli_close($db);
        $debugObj->close();
        return;
    }
    else {
        $result['rslt'] = FAIL;
        $result['reason'] = INVALID_ACTION;
        $result['rows'] = [];
        $evtLog->log($result["rslt"], $result["reason"]);
        echo json_encode($result);
        mysqli_close($db);
        $debugObj->close();
        return;
    }
	
    function maintRestoreMtcd($userObj, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac) {

        $result['log'] = "ACTION = RESTORE_MTCD | CKID = $ckid | CLS = $cls | ADSR = $adsr | TKTNO = $tktno | MLO = $mlo | PROT = $prot | FFAC = $ffac | TFAC = $tfac";

        // verify user permission
		if ($userObj->grpObj->maint != "Y") {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:PERMISSION DENIED";            
            $result['reason'] = "MAINTENANCE RESTORE MTCD - " . "PERMISSION DENIED";
			return $result;
        }
        
        if($tktno === "") {
            $result["rslt"] = 'fail';
            $result['jeop'] = "SP5:TKTNO IS EMPTY";            
			$result["reason"] = "MAINTENANCE RESTORE MTCD - " . "TKTNO IS EMPTY";
			return $result;
		}
        
        // the CKT must exist
		$cktObj = new CKT($ckid);
		if ($cktObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP5:CKID ". $ckid . " DOES NOT EXIST";            
            $result['reason'] = "MAINTENANCE RESTORE MTCD - " . "CKID: " . $ckid . " DOES NOT EXIST";
            return $result;
        }
	
        // the ffac must exist
		$ffacObj = new FAC($ffac);
		if ($ffacObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:INVALID FAC(X): " . $ffac;          
            $result['reason'] = "MAINTENANCE RESTORE MTCD - " . "FAC(X): " . $ffac . " DOES NOT EXIST";
			return $result;
        }
        
        // the tfac must exist
        $tfacObj = new FAC($tfac);
        if ($tfacObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:INVALID FAC(Y): " . $tfac;          
            $result['reason'] = "MAINTENANCE RESTORE MTCD - " . "FAC(Y): " . $tfac . " DOES NOT EXIST";
            return $result;
        }

        // the ffac must belong to CKID
        if ($ffacObj->portObj->ckt_id != $cktObj->id) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP4:FAC " . $ffac . " IS ALREADY IN CKT";            
            $result['reason'] = "MAINTENANCE RESTORE MTCD - " . "FAC(X): " . $ffac . " DOES NOT BELONG TO CKID " . $ckid;
            return $result;
        }

        // the tfac must belong to CKID
        if ($tfacObj->portObj->ckt_id != $cktObj->id) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP4:FAC " . $tfac . " IS ALREADY IN CKT";            
            $result['reason'] = "MAINTENANCE RESTORE MTCD - " . "FAC(Y): " . $tfac . " DOES NOT BELONG TO CKID " . $ckid;
            return $result;
        }

        // the CKTCON must be exisit
        $cktconObj = new CKTCON($cktObj->cktcon);
        if ($cktconObj->rslt == FAIL) {
            $result["rslt"] = "fail";
            $result['jeop'] = "SP5:CKTCON " . $cktObj->cktcon . " DOES NOT EXIST";           
			$result["reason"] = "MAINTENANCE RESTORE MTCD - " . "CKTCON " . $cktObj->cktcon . " DOES NOT EXIST";
			return $result;
		}
        
        // both ffac and tfac must belong to same MAINT CKTCON
        if ($cktconObj->loadIdxByPortIds($ffacObj->portObj->id, $tfacObj->portObj->id) == 0) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP4:FAC " . $ffac . " or " . $tfac . " are not part of CKT";     
            $result['reason'] = "MAINTENANCE RESTORE MTCD - " . "FAC(X) AND FAC(Y) ARE NOT ON SAME CKTCON";
            return $result;
        }

        // verify both ffac and tfac are on the same MAINT cktcon
        if ($cktconObj->ctyp != 'MAINT') {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP4:FAC " . $ffac . " or " . $tfac . " are not on a MAINT CKTCON";     
            $result['reason'] = "MAINTENANCE RESTORE MTCD - " . "FAC(X) AND FAC(Y) ARE NOT ON A MAINT CKTCON";
            return $result;
        }
        $maint_idx = $cktconObj->idx;

        // locate MTCD CKTCON
        $mtcd_idx = $cktconObj->findIdxByCtyp('MTCD');
        if ($mtcd_idx == 0) {
            $result["rslt"] = "fail";
            $result['jeop'] = "SP5:UNABLE TO LOCATE MTCD CKTCON";           
			$result["reason"] = "MAINTENANCE RESTORE MTCD - " . "COULD NOT LOCATE MTCD CKTCON";
			return $result;
        }

        // validate state-event
        $sms = new SMS($ffacObj->portObj->psta, $ffacObj->portObj->ssta, "RST_MTCD");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = $sms->rslt;
            $result['jeop'] = "SP3:FAC STATUS (".$ffacObj->portObj->psta.")";            
			$result['reason'] = "MAINTENANCE RESTORE MTCD - " . $sms->reason;
			return $result;
		}
		$ffacObj->portObj->npsta = $sms->npsta;
		$ffacObj->portObj->nssta = $sms->nssta;
                
        // validate psta for tfac
        $sms = new SMS($tfacObj->portObj->psta, $tfacObj->portObj->ssta, "RST_MTCD");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = $sms->rslt;
            $result['jeop'] = "SP3:FAC STATUS (".$tfacObj->portObj->psta.")";            
			$result['reason'] = "MAINTENANCE RESTORE MTCD - " . $sms->reason;
			return $result;
		}
		$tfacObj->portObj->npsta = $sms->npsta;
		$tfacObj->portObj->nssta = $sms->nssta;

        if ($ffacObj->portObj->npsta == 'MTCD') {
            $mtcdObj = $ffacObj->portObj;
            $tstObj = $tfacObj->portObj;
        }
        else {
            $mtcdObj = $tfacObj->portObj;
            $tstObj = $ffacObj->portObj;
        }

        // now, process the MTC_RESTORE
        // check if TEST PATH using dedicated test port or not
		// if yes, then delete tbus path
		// if no, (meaning test path using both normal ports), delete testpath in t_path table
		if($tstObj->ssta == 'TST_SF' || $tstObj->ssta == 'TST_UAS') {
			//get tbus path id
            $tbpath_id = $cktconObj->tbus;
			$tbusObj = new TBUS();
			$tbusObj->deleteTBpath($tbpath_id);
			if($tbusObj->rslt == 'fail') {
				$result['rslt'] = 'fail';
				$result['jeop'] = "SP5:$tbusObj->reason";            
				$result['reason'] = "MAINTENANCE DISCONNECT - " . $tbusObj->reason;
				return $result; 
			}

        }
        else {
            // 1) locate the PATH and open all relays
            $path_id = 0;
            /* will enable after 100% non-blocking test is done */
            $pathObj = new PATH($ffacObj->port, $tfacObj->port);
            $pathObj->load();
            if($pathObj->rslt == 'fail') {
                $result['rslt'] = 'fail';
                $result['jeop'] = "SP5:UNABLE TO LOAD PATH";            
                $result['reason'] = "MAINTENANCE DISCONNECT - " . $pathObj->reason;
                return $result; 
            }
            $pathObj->resetPath();
            
            // 2) delete the PATH from t_path
            $pathObj->drop();
        }
        // 3) delete MAINT CKTCON IDX
        $cktconObj->deleteIdx($cktconObj->con,$maint_idx);
        if ($cktconObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:$cktconObj->reason";            
            $result["reason"] = "MAINTENANCE RESTORE MTCD - " . $cktconObj->reason;
            return $result;
        }

        // 4) update t_ports for the MTCD port
        $ffacObj->portObj->updPsta($ffacObj->portObj->npsta, $ffacObj->portObj->nssta, $ffacObj->portObj->substa);
        if ($ffacObj->portObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$ffacObj->portObj->reason;            
            $result["reason"] = "MAINTENANCE RESTORE MTCD - " . $ffacObj->portObj->reason;
            return $result;
        }

        $tfacObj->portObj->updPsta($tfacObj->portObj->npsta, $tfacObj->portObj->nssta, $tfacObj->portObj->substa);
        if ($tfacObj->portObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$tfacObj->portObj->reason;            
            $result["reason"] = "MAINTENANCE RESTORE MTCD - " . $tfacObj->portObj->reason;
            return $result;
        }

        // 5) locate the MTCD CKTCON and updateCktLink
        $cktconObj->loadIdx($mtcd_idx);
        if ($cktconObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$cktconObj->reason;            
            $result["reason"] = "MAINTENANCE RESTORE MTCD - " . $cktconObj->reason;
            return $result;
        }

        $mtcdObj->updCktLink($cktObj->id, $cktconObj->con, $mtcd_idx);
        if ($mtcdObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$mtcdObj->reason;            
            $result["reason"] = "MAINTENANCE RESTORE MTCD - " . $mtcdObj->reason;
            return $result;
        }

        $tstObj->updCktLink(0, 0, 0);
        if ($tstObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$tstObj->reason;            
            $result["reason"] = "MAINTENANCE RESTORE MTCD - " . $tstObj->reason;
            return $result;
        }

        // update ticketNo
        $cktObj->setTktno($tktno);
        if ($cktObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$cktObj->reason;            
            $result["reason"] = "MAINTENANCE RESTORE MTCD - " . $cktObj->reason;
            return $result;
        }

        // 6) queryCktConWithFac
        $cktconObj->queryCktConWithFac($cktObj->cktcon);
        if ($cktconObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$cktconObj->reason;            
            $result["reason"] = "MAINTENANCE RESTORE MTCD - " . $cktconObj->reason;
            return $result;
        }

        $result['rows'] = $cktconObj->rows;
        $result["rslt"] = SUCCESS;
        $result["reason"] = "MAINTENANCE RESTORE MTCD - SUCCESSFUL";
        return $result;
         

	}

?>