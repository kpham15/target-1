<?php
/*  Filename: ipcMaintConnect.php
    Date: 2018-12-20
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
		$ffac = strtoupper($_POST['ffac']);
	}

	$tfac = "";
	if (isset($_POST['tfac'])) {
		$tfac = strtoupper($_POST['tfac']);
    }
    
    $tstfac = "";
	if (isset($_POST['tstfac'])) {
		$tstfac = strtoupper($_POST['tstfac']);
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
    
    $evtLog = new EVENTLOG($user, "MAINTENANCE", "SETUP MAINTENANCE CONNECTION", $act, '');
    

    $maintLog = new MAINTLOG();

	// --- Dispatch by ACTION ---
		
	if ($act == "MTC_CONN") {
        $result = maintConnect($userObj, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac);
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        $maintLog->log($user, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $ordno);
        echo json_encode($result);
        mysqli_close($db);
        return;
    }
    
    if ($act == "MTC_RESTORE") {
        $result = maintRestore($userObj, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac);
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        $maintLog->log($user, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $ordno);
        echo json_encode($result);
        mysqli_close($db);
        return;
    }

    if ($act == "MTC_TEST") {
        $result = maintTest($userObj, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tstfac);
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        $maintLog->log($user, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $ordno);
        echo json_encode($result);
        mysqli_close($db);
        return;
    }

    else {
        $result['rslt'] = FAIL;
        $result['reason'] = INVALID_ACTION;
        $result['rows'] = [];
        $evtLog->log($result["rslt"], $result["reason"]);
        echo json_encode($result);
        mysqli_close($db);
        return;
    }
	
    function maintConnect($userObj, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac) {
        
        $result['log'] = "ACTION = MTC_CONN | CKID = $ckid | CLS = $cls | ADSR = $adsr | TKTNO = $tktno | MLO = $mlo | PROT = $prot | CTYP = $ctyp | FFAC = $ffac | TFAC = $tfac";

		if ($userObj->grpObj->maint != "Y") {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:PERMISSION DENIED";
			$result['reason'] = "MAINTENANCE CONNECT - " . 'PERMISSION DENIED';
			return $result;
        }
        
        if($tktno === "") {
            $result["rslt"] = 'fail';
            $result['jeop'] = "SP5:MISSING TKTNO";
			$result["reason"] = "MAINTENANCE CONNECT - " . "MISSING TKTNO";
			return $result;
		}
        
        // the ckid must exist in DB
		$cktObj = new CKT($ckid);
		if ($cktObj->rslt == FAIL) {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:".$cktObj->reason;
			$result['reason'] = "MAINTENANCE CONNECT - " . $cktObj->reason;
			return $result;
        }

		// the ffac must exist in DB 
		$ffacObj = new FAC($ffac);
		if ($ffacObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:FAC IS INVALID";
			$result['reason'] = "MAINTENANCE CONNECT - " . "FAC: " . $ffac . " DOES NOT EXIST";
			return $result;
        }
        
        // the ffac must have port mapped
        if ($ffacObj->port_id == 0) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP3:FAC IS UNQ";
			$result['reason'] = "MAINTENANCE CONNECT - " . "FAC: " . $ffac . " IS NOT MAPPED TO A PORT";
			return $result;
        }

        
        // the tfac must exist in DB
        $tfacObj = new FAC($tfac);
		if ($tfacObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:FAC IS INVALID";
			$result['reason'] = "MAINTENANCE CONNECT - " . "FAC: " . $tfac . " DOES NOT EXIST";
			return $result;
        }
        
        // the ffac must have port mapped
        if ($tfacObj->port_id == 0) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP3:FAC IS UNQ";
			$result['reason'] = "MAINTENANCE CONNECT - " . "FAC: " . $tfac . " IS NOT MAPPED TO A PORT";
			return $result;
        }

        // locate the MTCD facility and the TST facility
        if ($ffacObj->portObj->psta == 'MTCD') {
            if ($tfacObj->portObj->psta == 'MTCD') {
                $result['rslt'] = "fail";
                $result['jeop'] = "SP3:FAC STATUS (".$ffacObj->portObj->psta.")";            
                $result['reason'] = "MAINTENANCE CONNECT - " . "ONLY ONE FACILITY CAN BE MTCD";
                return $result;
            }
            $mtcdObj = $ffacObj;
            $tstObj = $tfacObj;
        }
        else if ($tfacObj->portObj->psta == 'MTCD') {
            if ($ffacObj->portObj->psta == 'MTCD') {
                $result['rslt'] = "fail";
                $result['jeop'] = "SP3:FAC STATUS (".$tfacObj->portObj->psta.")";            
                $result['reason'] = "MAINTENANCE CONNECT - " . "ONLY ONE FACILITY CAN BE MTCD";
                return $result;
            }
            $mtcdObj = $tfacObj;
            $tstObj = $ffacObj;
        }
        else {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:MISSING AN MTCD FACILITY";            
            $result['reason'] = "MAINTENANCE CONNECT - " . "MISSING AN MTCD FACILITY";
            return $result;
        }

        // MTCD fac and TST fac must be on different ptyp
        if ($mtcdObj->portObj->ptyp == $tstObj->portObj->ptyp) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP4:TCD FAC AND TEST FAC MUST BE ON OPPOSITE PORT_TYP";            
            $result['reason'] = "MAINTENANCE CONNECT - " . "MTCD FAC AND TEST FAC MUST BE ON OPPOSITE PORT_TYP";
            return $result;
        }

        // verify if MTCD fac is on the right CKTCON
        $cktconObj = new CKTCON($mtcdObj->portObj->cktcon);
        if ($cktconObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:UNABLE TO LOCATE CKTCON(" . $mtcdObj->portObj->cktcon . ") OF MTCD FAC(" . $mtcdObj->fac . ")";            
            $result['reason'] = "MAINTENANCE CONNECT - " . "COULD NOT LOCATE CKTCON(" . $mtcdObj->portObj->cktcon . ") OF MTCD FAC(" . $mtcdObj->fac . ")";
            return $result;
        }

        
        if ($cktconObj->loadIdx($mtcdObj->portObj->con_idx) === FALSE) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:UNABLE TO LOCATE CKTCON(" . $cktconObj->con . ") IDX(" . $mtcdObj->portObj->con_idx . ") OF MTCD FAC";            
            $result['reason'] = "MAINTENANCE CONNECT - " . "COULD NOT LOCATE CKTCON(" . $cktconObj->con . ") IDX(" . $mtcdObj->portObj->con_idx . ") OF MTCD FAC";
            return $result;
        }

        if ($cktObj->ckid != $cktconObj->ckid) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:MTCD FAC " . $mtcdObj->fac . " IS NOT ON CKID " . $cktObj->ckid;            
            $result['reason'] = "MAINTENANCE CONNECT - " . "MTCD FAC " . $mtcdObj->fac . " IS NOT ON CKID " . $cktObj->ckid;
            return $result;
        }

        // locate MTCD FAC position on the connection
        if ($cktconObj->fport == $mtcdObj->portObj->port) {
            $fportObj = $mtcdObj->portObj;
            $tportObj = $tstObj->portObj;
        }
        else if ($cktconObj->tport == $mtcdObj->portObj->port) {
            $tportObj = $mtcdObj->portObj;
            $fportObj = $tstObj->portObj;
        }
        else {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:COULD NOT LOCATE CKTCON OF MTCD FAC";           
            $result['reason'] = "MAINTENANCE CONNECT - " . "COULD NOT LOCATE CKTCON OF MTCD FAC";
            return $result;
        }


        // validate state-event
        $sms = new SMS($fportObj->psta, $fportObj->ssta, "MT_CONN");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP3:FAC STATUS (".$fportObj->psta.")";            
            $result['reason'] = "MAINTENANCE CONNECT - " . $sms->reason;
            return $result;
        }
        $fportObj->npsta = $sms->npsta;
        $fportObj->nssta = $sms->nssta;

        $sms = new SMS($tportObj->psta, $tportObj->ssta, "MT_CONN");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP3:FAC STATUS (".$tportObj->psta.")";            
            $result['reason'] = "MAINTENANCE CONNECT - " . $sms->reason;
            return $result;
        }
        $tportObj->npsta = $sms->npsta;
        $tportObj->nssta = $sms->nssta;

        $path_id = 0;
        /* will enable when testing of 100% non-blocking is done */
        $pathObj = new PATH($ffacObj->portObj->port, $tfacObj->portObj->port);
        $pathObj->createPath();
        if($pathObj->rslt == 'fail') {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:UNABLE TO ESTABLISH PATH";            
            $result['reason'] = "MAINTENANCE CONNECT - " . $pathObj->reason;
            return $result; 
        }
        // apply PATH
        $path_id = $pathObj->id;
        $pathObj->setPath();
        

        // add new CON_IDX
        $cktconObj->addIdx($cktObj->cktcon, $cktObj->id, $cktObj->ckid, "MAINT", "MAINT", $fportObj->id, $fportObj->port, 1, $tportObj->id, $tportObj->port, 1, $path_id);
        if ($cktconObj->rslt != SUCCESS) {
            $result['rslt'] = $cktconObj->rslt;
            $result['jeop'] = "SP5:".$cktconObj->reason;            
            $result['reason'] = "MAINTENANCE CONNECT - " . $cktconObj->reason;
            return $result;
        }
            
        
        // 4) update PORT's PSTA and link with CKT, CKTCON
        $fportObj->updPsta($fportObj->npsta, $fportObj->nssta, "-");
		if ($fportObj->rslt != SUCCESS) {
            $result['rslt'] = $fportObj->rslt;
            $result['jeop'] = "SP5:".$fportObj->reason;            
			$result['reason'] = "MAINTENANCE CONNECT - " . $fportObj->reason;
			return $result;
		}

        $fportObj->updCktLink($cktObj->id, $cktconObj->con, $cktconObj->idx);
		if ($fportObj->rslt != SUCCESS) {
            $result['rslt'] = $fportObj->rslt;
            $result['jeop'] = "SP5:".$fportObj->reason;            
			$result['reason'] = "MAINTENANCE CONNECT - " . $fportObj->reason;
			return $result;
        }
       
        $tportObj->updPsta($tportObj->npsta, $tportObj->nssta, "-");
		if ($tportObj->rslt != SUCCESS) {
            $result['rslt'] = $tportObj->rslt;
            $result['jeop'] = "SP5:".$tportObj->reason;            
			$result['reason'] = "MAINTENANCE CONNECT - " . $tportObj->reason;
			return $result;
		}

        $tportObj->updCktLink($cktObj->id, $cktconObj->con, $cktconObj->idx);
		if ($tportObj->rslt != SUCCESS) {
            $result['rslt'] = $tportObj->rslt;
            $result['jeop'] = "SP5:".$tportObj->reason;            
			$result['reason'] = "MAINTENANCE CONNECT - " . $tportObj->reason;
			return $result;
        }
        
        // update ticketNo
        $cktObj->setTktno($tktno);
        if ($cktObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$cktObj->reason;            
            $result["reason"] = "MAINTENANCE CONNECT - " . $cktObj->reason;
            return $result;
        }

        $cktconObj->queryCktConWithFac($cktconObj->con);
        if ($cktconObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$cktconObj->reason;            
            $result["reason"] = "MAINTENANCE CONNECT - " . $cktconObj->reason;
            return $result;
        }

        $result['rows'] = $cktconObj->rows;
        $result["rslt"] = SUCCESS;
        $result["reason"] = "MAINTENANCE CONNECT - " . "SUCCESSFUL";
        return $result;
	}

	
    function maintRestore($userObj, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac) {

        $result['log'] = "ACTION = MTC_RESTORE | CKID = $ckid | CLS = $cls | ADSR = $adsr | TKTNO = $tktno | MLO = $mlo | PROT = $prot | CTYP = $ctyp | FFAC = $ffac | TFAC = $tfac";

		if ($userObj->grpObj->maint != "Y") {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:PERMISSION DENIED";            
			$result['reason'] = "MAINTENANCE RESTORE - " . 'PERMISSION DENIED';
			return $result;
        }
        
        if($tktno === "") {
            $result["rslt"] = 'fail';
            $result['jeop'] = "SP5:MISSING TKTNO";            
			$result["reason"] = "MAINTENANCE RESTORE - " . "MISSING TKTNO";
			return $result;
		}
        
        // the ckid must exist in DB
		$cktObj = new CKT($ckid);
		if ($cktObj->rslt == FAIL) {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:".$cktObj->reason;            
			$result['reason'] = "MAINTENANCE RESTORE - " . $cktObj->reason;
			return $result;
        }

		// the ffac must exist in DB 
		$ffacObj = new FAC($ffac);
		if ($ffacObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:FAC IS INVALID";            
			$result['reason'] = "MAINTENANCE RESTORE - " . "FAC: " . $ffac . " DOES NOT EXIST";
			return $result;
        }
        
        // the ffac must have port mapped
        if ($ffacObj->port_id == 0) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP3:FAC IS UNQ";            
			$result['reason'] = "MAINTENANCE RESTORE - " . "FAC: " . $ffac . " IS NOT MAPPED TO A PORT";
			return $result;
        }

        
        // the tfac must exist in DB
        $tfacObj = new FAC($tfac);
		if ($tfacObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:FAC IS INVALID";            
			$result['reason'] = "MAINTENANCE RESTORE - " . "FAC: " . $tfac . " DOES NOT EXIST";
			return $result;
        }
        
        // the ffac must have port mapped
        if ($tfacObj->port_id == 0) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP3:FAC IS UNQ";            
			$result['reason'] = "MAINTENANCE RESTORE - " . "FAC: " . $tfac . " IS NOT MAPPED TO A PORT";
			return $result;
        }

        // locate the two MTCD facilities
        $cktconObj = new CKTCON($cktObj->cktcon);
        if ($cktconObj->rslt == FAIL) {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:".$cktconObj->reason;            
			$result['reason'] = "MAINTENANCE RESTORE - " . $cktconObj->reason;
			return $result;
        }

        if ($cktconObj->loadIdxByPortIds($ffacObj->port_id, $tfacObj->port_id) == 0) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP4:FAC " . $ffac . " OR " . $tfac . " IS NOT ON CKT " . $ckid;          
			$result["reason"] = "MAINTENANCE RESTORE - " . "FAC: " . $ffac . " OR " . $tfac . " IS NOT ON CKT " . $ckid;
			return $result;
		}


        // validate state-event for ffac
        $sms = new SMS($ffacObj->portObj->psta, $ffacObj->portObj->ssta, "MT_RST");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP3:FAC STATUS (".$ffacObj->portObj->psta.")";            
            $result['reason'] = "MAINTENANCE RESTORE - " . $sms->reason;
            return $result;
        }
        $ffacObj->portObj->npsta = $sms->npsta;
        $ffacObj->portObj->nssta = $sms->nssta;

        // validate state-event for tfac
        $sms = new SMS($tfacObj->portObj->psta, $tfacObj->portObj->ssta, "MT_RST");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP3:FAC STATUS (".$tfacObj->portObj->psta.")";            
            $result['reason'] = "MAINTENANCE RESTORE - " . $sms->reason;
            return $result;
        }
        $tfacObj->portObj->npsta = $sms->npsta;
        $tfacObj->portObj->nssta = $sms->nssta;

        $path_id = 0;
        /* will enable when 100% non-blocking test is done
        */
        $pathObj = new PATH($ffacObj->portObj->port, $tfacObj->portObj->port);
        $pathObj->createPath();
        if($pathObj->rslt == 'fail') {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:UNABLE TO ESTABLISH PATH";            
            $result['reason'] = "MAINTENANCE RESTORE - " . $pathObj->reason;
            return $result; 
        }
        // apply PATH
        $path_id = $pathObj->id;
        $pathObj->setPath();

        // update CKTCON IDX with ctyp = ctyp_o and path = $pathObj->id
        $cktconObj->updateIdx($cktconObj->con, $cktconObj->idx, $cktconObj->ctyp_o, $cktconObj->ctyp_o, $path_id);
        if ($cktconObj->rslt != SUCCESS) {
            $result['rslt'] = $cktconObj->rslt;
            $result['jeop'] = "SP5:$cktconObj->reason";            
            $result['reason'] = "MAINTENANCE RESTORE - " . $cktconObj->reason;
            return $result;
        }           
        
        // update PORT's PSTA and link with CKT, CKTCON
        $ffacObj->portObj->updPsta($ffacObj->portObj->npsta, $ffacObj->portObj->nssta, "-");
		if ($ffacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $ffacObj->portObj->rslt;
            $result['jeop'] = "SP5:".$ffacObj->portObj->reason;            
			$result['reason'] = "MAINTENANCE RESTORE - " . $ffacObj->portObj->reason;
			return $result;
		}
        
        $tfacObj->portObj->updPsta($tfacObj->portObj->npsta, $tfacObj->portObj->nssta, "-");
		if ($tfacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $tfacObj->portObj->rslt;
            $result['jeop'] = "SP5:".$tfacObj->portObj->reason;            
			$result['reason'] = "MAINTENANCE RESTORE - " . $tfacObj->portObj->reason;
			return $result;
        }
        
        // update ticketNo
        $cktObj->setTktno($tktno);
        if ($cktObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:$cktObj->reason";        
            $result["reason"] = "MAINTENANCE RESTORE - " . $cktObj->reason;
            return $result;
        }
        
        $cktconObj->queryCktConWithFac($cktconObj->con);
        if ($cktconObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:$cktconObj->reason";        
            $result["reason"] = "MAINTENANCE RESTORE - " . $cktconObj->reason;
            return $result;
        }

        $result['rows'] = $cktconObj->rows;
        $result["rslt"] = SUCCESS;
        $result["reason"] = "MAINTENANCE RESTORE - " . "SUCCESSFUL";
        return $result;
    }
    
    function maintTest($userObj, $tktno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tstfac) {
        
        $result['log'] = "ACTION = MTC_TEST | CKID = $ckid | CLS = $cls | ADSR = $adsr | TKTNO = $tktno | MLO = $mlo | PROT = $prot | CTYP = $ctyp | FAC = $ffac | TESTFAC = $tstfac";

		if ($userObj->grpObj->maint != "Y") {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:PERMISSION DENIED";
			$result['reason'] = "MAINTENANCE TEST - " . 'PERMISSION DENIED';
			return $result;
        }
        
        if($tktno === "") {
            $result["rslt"] = 'fail';
            $result['jeop'] = "SP5:MISSING TKTNO";
			$result["reason"] = "MAINTENANCE TEST - " . "MISSING TKTNO";
			return $result;
        }

        //-----------------------check ckt info-----------------------------
        //the ckid must exist in DB
		$cktObj = new CKT($ckid);
		if ($cktObj->rslt == FAIL) {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:".$cktObj->reason;
			$result['reason'] = "MAINTENANCE TEST - " . $cktObj->reason;
			return $result;
        }

        //--------------------check the facs info----------------------
		//the ffac must exist in DB 
		$ffacObj = new FAC($ffac);
		if ($ffacObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:FAC IS INVALID";
			$result['reason'] = "MAINTENANCE TEST - " . "FAC: " . $ffac . " DOES NOT EXIST";
			return $result;
        }
        
        // the ffac must have port mapped
        if ($ffacObj->port_id == 0) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP3:FAC IS UNQ";
			$result['reason'] = "MAINTENANCE TEST - " . "FAC: " . $ffac . " IS NOT MAPPED TO A PORT";
			return $result;
        }

        // the tstfac must exist in DB
        $tstfacObj = new FAC($tstfac);
		if ($tstfacObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:FAC IS INVALID";
			$result['reason'] = "MAINTENANCE TEST - " . "FAC: " . $tstfac . " DOES NOT EXIST";
			return $result;
        }
        
        // the tstfac must have port mapped
        if ($tstfacObj->port_id == 0) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP3:FAC IS UNQ";
			$result['reason'] = "MAINTENANCE TEST - " . "FAC: " . $tstfac . " IS NOT MAPPED TO A PORT";
			return $result;
        }

        //---------------------check tbus table---------------------
        //from ffac, we have node info, and ptyp info. Now checking the tbus table
        $node = $ffacObj->portObj->node;
        $tb = $ffacObj->portObj->ptyp;
        $tbusObj = new TBUS();
        $tbusObj->queryTBpath($node, $tb);
        if($tbusObj->rslt == 'fail') {
            $result['rslt'] = $tbusObj->rslt;
            $result['jeop'] = "SP5:$tbusObj->reason";
            $result['reason'] = $tbusObj->reason;
            return $result;
        }
        
        if(count($tbusObj->rows) > 0){
            $result['rslt'] = 'fail';
            $rows = $tbusObj->rows;
            $zport = $rows[0]['zport'];
            $port = $rows[0]['port'];
            $result['jeop'] = "SP5:TEST BUS IS BUSY AT $zport and $port";
            $result['reason'] = "MAIN TEST - TEST BUS IS BUSY AT $zport and $port";
            return $result;
        }
        
        //-----------------------check cktcon info-----------------------------
        //verify if MTCD fac is on the right CKTCON
        $cktconObj = new CKTCON($ffacObj->portObj->cktcon);
        if ($cktconObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:UNABLE TO LOCATE CKTCON(" . $ffacObj->portObj->cktcon . ") OF MTCD FAC(" . $ffacObj->fac . ")";            
            $result['reason'] = "MAINTENANCE TEST - " . "COULD NOT LOCATE CKTCON(" . $ffacObj->portObj->cktcon . ") OF MTCD FAC(" . $ffacObj->fac . ")";
            return $result;
        }

        if ($cktconObj->loadIdx($ffacObj->portObj->con_idx) === FALSE) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:UNABLE TO LOCATE CKTCON(" . $cktconObj->con . ") IDX(" . $ffacObj->portObj->con_idx . ") OF MTCD FAC";            
            $result['reason'] = "MAINTENANCE TEST - " . "COULD NOT LOCATE CKTCON(" . $cktconObj->con . ") IDX(" . $ffacObj->portObj->con_idx . ") OF MTCD FAC";
            return $result;
        }

        if ($cktObj->ckid != $cktconObj->ckid) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:MTCD FAC " . $ffacObj->fac . " IS NOT ON CKID " . $cktObj->ckid;            
            $result['reason'] = "MAINTENANCE TEST - " . "MTCD FAC " . $ffacObj->fac . " IS NOT ON CKID " . $cktObj->ckid;
            return $result;
        }

        //-------------------------check SMS infor---------------------------
        $fportObj = $ffacObj->portObj;
        $tstportObj = $tstfacObj->portObj;
        // validate state-event
        $sms = new SMS($fportObj->psta, $fportObj->ssta, "MT_CONN");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP3:FAC STATUS (".$fportObj->psta.")";            
            $result['reason'] = "MAINTENANCE TEST - " . $sms->reason;
            return $result;
        }
        $fportObj->npsta = $sms->npsta;
        $fportObj->nssta = $sms->nssta;

        $sms = new SMS($tstportObj->psta, $tstportObj->ssta, "MT_CONN");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP3:FAC STATUS (".$tstportObj->psta.")";            
            $result['reason'] = "MAINTENANCE TEST - " . $sms->reason;
            return $result;
        }
        $tstportObj->npsta = $sms->npsta;
        $tstportObj->nssta = $sms->nssta;
        

        //-----------------------get relay info for tstport and fport----------------------------
        $portExtract = explode('-', $fportObj->port);
        //re-order port info to match the format in t_X, t_Y table
        $portConvert = ($portExtract[0]-1).".".$portExtract[2].".".($portExtract[1]-1).".".($portExtract[3]-1);
        if($fportObj->ptyp == 'X')
            $portTypeObj = new X($portConvert);
        else
            $portTypeObj = new Y($portConvert);

        if($portTypeObj->rslt == 'fail') {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP5:$portTypeObj->reason";            
            $result['reason'] = "MAINTENANCE TEST - " . $sms->reason;
            return $result;
        }
        //create relay format for fport
        //--check if the port is X type, then relay to connect fport to testbus is
        //   stage.n:input.10   for examle: 1A.0:1.10
        // if the port is Y type, the the relay in following format:
        //   stage.n:10.output  for example: 7A.0:10.1
        $fd = $portTypeObj->d;
        $fd_extract = explode('.', $fd);
        if($fportObj->ptyp == 'X')
            $frelay = "$fd_extract[1].$fd_extract[2]:$fd_extract[3].10";
        else if($fportObj->ptyp == 'Y') {
            $frelay = "$fd_extract[1].$fd_extract[2]:10.$fd_extract[3]";
        }
        // //create relay format for tstport
        // $tstPortNum = $tstportObj->pnum -1;
        // if($fportObj->ptyp == 'X') {
        //     $tstrelay = "TB.X:0.$tstPortNum";
        // }
        // else if($fportObj->ptyp == 'Y') {
        //     $tstrelay = "TB.Y:0.$tstPortNum";
        // }

        //get row/col for relays 
        $rcObj = new RC();
        $rcObj->queryRC($frelay);
        if($rcObj->rslt == 'fail') {
            $result['rslt'] = $rcObj->rslt;
            $result['jeop'] = "SP5:".$rcObj->reason;            
            $result['reason'] = "MAINTENANCE TEST - " . $rcObj->reason;
            return $result;
        }
        $frow = $rcObj->rows[0]['row'];
        $fcol = $rcObj->rows[0]['col'];

        // $rcObj->queryRC($tstrelay);
        // if($rcObj->rslt == 'fail') {
        //     $result['rslt'] = $rcObj->rslt;
        //     $result['jeop'] = "SP5:".$rcObj->reason;            
        //     $result['reason'] = "MAINTENANCE TEST - " . $rcObj->reason;
        //     return $result;
        // }
        // $tstrow = $rcObj->rows[0]['row'];
        // $tstcol = $rcObj->rows[0]['col'];

        //--------------------------Update database table-----------------------------
        //Up to this poitn, all checking processes are good , needed data is prepared
        //Ready to update table

        //add new testpath
        $tbusObj->addTBpath($node,$tb, $tstportObj->port, $fportObj->port);
        if($tbusObj->rslt == 'fail') {
            $result['rslt'] = $tbusObj->rslt;
            $result['jeop'] = "SP5:".$tbusObj->reason;            
            $result['reason'] = "MAINTENANCE TEST - " . $tbusObj->reason;
            return $result;
        }
        //add new CON_IDX
        $cktconObj->addIdx($cktObj->cktcon, $cktObj->id, $cktObj->ckid, "MAINT", "MAINT", $fportObj->id, $fportObj->port, 1, $tstportObj->id, $tstportObj->port, 1, 0);
        if ($cktconObj->rslt != SUCCESS) {
            $result['rslt'] = $cktconObj->rslt;
            $result['jeop'] = "SP5:".$cktconObj->reason;            
            $result['reason'] = "MAINTENANCE TEST - " . $cktconObj->reason;
            return $result;
        }
        // update tbus id in cktconn
        $cktconObj->updateTbus($tbusObj->id);
        if ($cktconObj->rslt != SUCCESS) {
            $result['rslt'] = $cktconObj->rslt;
            $result['jeop'] = "SP5:".$cktconObj->reason;            
            $result['reason'] = "MAINTENANCE TEST - " . $cktconObj->reason;
            return $result;
        }

        //------------update PORT's PSTA----------------------
        $fportObj->updPsta($fportObj->npsta, $fportObj->nssta, "-");
		if ($fportObj->rslt != SUCCESS) {
            $result['rslt'] = $fportObj->rslt;
            $result['jeop'] = "SP5:".$fportObj->reason;            
			$result['reason'] = "MAINTENANCE TEST - " . $fportObj->reason;
			return $result;
		}
        //------------update PORT's CKT, CKTCON
        $fportObj->updCktLink($cktObj->id, $cktconObj->con, $cktconObj->idx);
		if ($fportObj->rslt != SUCCESS) {
            $result['rslt'] = $fportObj->rslt;
            $result['jeop'] = "SP5:".$fportObj->reason;            
			$result['reason'] = "MAINTENANCE TEST - " . $fportObj->reason;
			return $result;
        }   
        //------------update PORT's PSTA
        $tstportObj->updPsta($tstportObj->npsta, $tstportObj->nssta, "-");
		if ($tstportObj->rslt != SUCCESS) {
            $result['rslt'] = $tstportObj->rslt;
            $result['jeop'] = "SP5:".$tstportObj->reason;            
			$result['reason'] = "MAINTENANCE TEST - " . $tstportObj->reason;
			return $result;
		}
        //------------update PORT's CKT
        $tstportObj->updCktLink($cktObj->id, $cktconObj->con, $cktconObj->idx);
		if ($tstportObj->rslt != SUCCESS) {
            $result['rslt'] = $tstportObj->rslt;
            $result['jeop'] = "SP5:".$tstportObj->reason;            
			$result['reason'] = "MAINTENANCE TEST - " . $tstportObj->reason;
			return $result;
        }
        //------------update ticketNo
        $cktObj->setTktno($tktno);
        if ($cktObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$cktObj->reason;            
            $result["reason"] = "MAINTENANCE TEST - " . $cktObj->reason;
            return $result;
        }
        //------------------------create cmds-------------------------------
		$cmdObj = new CMD();
        //create cmd for fport......
        $cmdObj->sendTestedPortCmd('close',$node,$fcol,$frow);
        //create cmd for zport.....
        $cmdObj->sendZPortCmd('connect',$tstportObj->pnum, $node);

        // ------------------------------------------------------------------
        $cktconObj->queryCktConWithFac($cktconObj->con);
        if ($cktconObj->rslt == FAIL) {
            $result["rslt"] = FAIL;
            $result['jeop'] = "SP5:".$cktconObj->reason;            
            $result["reason"] = "MAINTENANCE TEST - " . $cktconObj->reason;
            return $result;
        }

        $result['rows'] = $cktconObj->rows;
        $result["rslt"] = SUCCESS;
        $result["reason"] = "MAINTENANCE TEST - " . "SUCCESSFUL";
        return $result;
	}


?>