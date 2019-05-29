<?php
/*  Filename: ipcProvChange.php
    Date: 2018-12-14
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

    $mlo = "";
	if (isset($_POST['mlo'])) {
		$mlo = strtoupper($_POST['mlo']);
	}
    if ($mlo == "") {
        $mlo = "N";
    }

	$ckid = "";
	if (isset($_POST['ckid'])) {
		$ckid = strtoupper($_POST['ckid']);
    }
    
    $idx = "";
    if (isset($_POST['idx'])) {
		$idx = strtoupper($_POST['idx']);
    }

    $cls = "";
	if (isset($_POST['cls'])) {
		$cls = strtoupper($_POST['cls']);
	}

    $adsr = "";
	if (isset($_POST['adsr'])) {
        $adsr = strtoupper($_POST['adsr']);
	}
    if ($adsr == "" ) {
        $adsr = "N";
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
    
    $tktno = "";
	if (isset($_POST['tktno'])) {
		$tktno = $_POST['tktno'];
    }

    $dd = "";
	if (isset($_POST['dd'])) {
		$dd = $_POST['dd'];
    }

    $fdd = "";
	if (isset($_POST['fdd'])) {
		$fdd = $_POST['fdd'];
    }

    $fport= "";
	if (isset($_POST['fport'])) {
		$fport = $_POST['fport'];
    }

    $tport = "";
    if (isset($_POST['tport'])) {
		$tport = $_POST['tport'];
    }
    
    // $input = date("Y-m-d H:i:s") . "\n";
    // $input .= str_pad("ORDNO=" . $ordno,30) . str_pad("ACTION=" . $act,20) . str_pad("MLO=" . $mlo,10) . str_pad("USER=" . $user,30) . "\n";
	// $input .= str_pad("DD=" . $dd,20) . str_pad("FDD=" . $fdd,20) . "\n";
    // $input .= str_pad("CKID=" . $ckid,30) . str_pad("CLS=" . $cls,15) . str_pad("ADSR=" . $adsr,10) . str_pad("PROT=" . $prot,10) . "\n";
    // $input .= str_pad("CONTYP=" . $ctyp,15) . str_pad("FAC(X)=" . $ffac,30) . str_pad("FAC(Y)=" . $tfac,30);

    // $evtLog = new EVTLOG($user, "SVCCONN", $act, $input);




	// --- Dispatch by ACTION ---
    $evtLog = new EVENTLOG($user, "PROVISIONING", "SETUP SERVICE CONNECTION", $act, '');
		
	if ($act == "CHANGE") {
	    $provLog = new PROVLOG();
        $result = provChange($userObj, $ordno, $mlo, $ckid, $idx, $cls, $adsr, $prot, $ctyp, $ffac, $tfac);
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        $provLog->log($user, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $tktno);
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
    
    function provChange($userObj, $ordno, $mlo, $ckid, $idx, $cls, $adsr, $prot, $ctyp, $ffac, $tfac) {
        
        $result['log'] = "ACTION = $act | ORDNO = $ordno | MLO = $mlo | CKID = $ckid | IDX = $idx | CLS = $cls | ADSR = $adsr | PROT = $prot | CONTYP = $ctyp | FAC(X) = $ffac | FAC(Y) = $tfac";

		if ($userObj->grpObj->prov != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = 'Permission Denied';
			return $result;
		}
        
        /////////////---Begin---/////////////
        
        // if ckid is not in DB, then this is an invalid CKT
		$cktObj = new CKT($ckid);
		if ($cktObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason'] = $cktObj->reason;
            return $result;
        }

        // verify ffac and tfac are currently connected under this CKT
        $cktconObj = new CKTCON($cktObj->cktcon);
        if ($cktconObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason'] = $cktconObj->reason;
            return $result;
        }

        // verify IDX exists in CKTCON
        $cktconObj->loadIdx($idx);
        if ($cktconObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['reason'] = $cktconObj->reason;
            return $result;
        }

        // the ffac must exist in DB and currently part of same CKTCON/IDX
		$ffacObj = new FAC($ffac);
		if ($ffacObj->rslt != SUCCESS || $ffacObj->port_id == 0) {
			$result['rslt'] = "fail";
            $result['reason'] = INVALID_FAC . ": " . $ffac;
			return $result;
        }
        
        if ($ffacObj->portObj->ptyp != "X") {
			$result['rslt'] = "fail";
            $result['reason'] = "PROVISIONING CHANGE - " . "FAC(X) " . $ffac . " NOT MAPPED TO X-PORT";
			return $result;
		}
        
        // the tfac must exist in DB and must be currently mapped
		$tfacObj = new FAC($tfac);
		if ($tfacObj->rslt != SUCCESS || $tfacObj->port_id == 0) {
			$result['rslt'] = "fail";
            $result['reason'] = INVALID_FAC . ": " . $tfac;
			return $result;
        }

        if ($tfacObj->portObj->ptyp != "Y") {
			$result['rslt'] = "fail";
            $result['reason'] = "PROVISIONING CHANGE - " . "FAC(Y) " . $tfac . " NOT MAPPED TO Y-PORT";
			return $result;
		}

        if(($ffacObj->port!= $cktconObj->fport && $tfacObj->port != $cktconObj->tport) ||
            ($ffacObj->port == $cktconObj->fport && $tfacObj->port == $cktconObj->tport) ){
            $result['rslt'] = "fail";
            $result['reason'] = INVALID_FACS . ": " . $tfac." ".$ffac;
			return $result;
        }

        $oldFportObj = new PORT();
        $oldFportObj->loadPort($cktconObj->fport);
        if ($oldFportObj->rslt == 'fail') {
			$result['rslt'] = "fail";
            $result['reason'] = "PROV_CHANGE: INVALID OLD FPORT - " . $cktconObj->fport;
			return $result;
        }

        $oldTportObj = new PORT();
        $oldTportObj->loadPort($cktconObj->tport);
        if ($oldTportObj->rslt == 'fail') {
			$result['rslt'] = "fail";
            $result['reason'] = "PROV_CHANGE: INVALID OLD TPORT - " . $cktconObj->tport;
			return $result;
        }

        // validate state-event
        $sms = new SMS($oldFportObj->psta, $oldFportObj->ssta, "SV_DISCON");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason'] = $sms->reason." ".$oldFportObj->port;
            return $result;
        }
        $oldFportObj->npsta = $sms->npsta;
        $oldFportObj->nssta = $sms->nssta;

        $sms = new SMS($oldTportObj->psta, $oldTportObj->ssta, "SV_DISCON");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason'] = $sms->reason." ".$oldTportObj->port;
            return $result;
        }
        $oldTportObj->npsta = $sms->npsta;
        $oldTportObj->nssta = $sms->nssta;

        /* will enable this after testing of 100% non-blocking */
         
        // $pathObj = new PATH($ffacObj->portObj->port, $tfacObj->portObj->port);
        // $pathObj->load();
        // if($pathObj->rslt == 'fail') {
        //     $result['rslt'] = 'fail';
        //     $result['reason'] = "PROVISIONING CONNECT - " . $pathObj->reason;
        //     return $result; 
        // }
        // $pathObj->resetPath();
        // $pathObj->drop();
        /* */

        // Ready for DB updates
        // 1) remove IDX
        $cktconObj->deleteIdx($cktconObj->con, $cktconObj->idx);
        if ($cktconObj->rslt != SUCCESS) {
            $result['rslt'] = $cktconObj->rslt;
            $result['reason'] = $cktconObj->reason;
            return $result;
        }
        
        // 2) if last IDX removed, then remove CKT as well
        // $newCktconObj = new CKTCON($cktconObj->con);
        // if ($newCktconObj->rslt == FAIL) {
        //     $cktObj->deleteCkt($ckid);
        //     if ($cktObj->rslt == FAIL) {
        //         $ressult['rslt'] = FAIL;
        //         $result['reason'] = $cktObj->reason;
        //         return $result;
        //     }
        // }
                
        // 3) update PORT's PSTA and link with CKT, CKTCON
        $oldFportObj->updPsta($oldFportObj->npsta, $oldFportObj->nssta, "-");
		if ($oldFportObj->rslt != SUCCESS) {
			$result['rslt'] = $oldFportObj->rslt;
            $result['reason'] = $oldFportObj->reason;
			return $result;
		}

        $oldFportObj->updCktLink(0, 0, 0);
		if ($oldFportObj->rslt != SUCCESS) {
			$result['rslt'] = $oldFportObj->rslt;
            $result['reason'] = $oldFportObj->reason;
			return $result;
        }
        
        $oldTportObj->updPsta($oldTportObj->npsta, $oldTportObj->nssta, "-");
		if ($oldTportObj->rslt != SUCCESS) {
			$result['rslt'] = $oldTportObj->rslt;
            $result['reason'] = $oldTportObj->reason;
			return $result;
		}

        $oldTportObj->updCktLink(0, 0, 0);
		if ($oldTportObj->rslt != SUCCESS) {
			$result['rslt'] = $oldTportObj->rslt;
            $result['reason'] = $oldTportObj->reason;
			return $result;
        }

        //Just finished delete cktcon
        //now we create the new one

        // allow only CTYP=GEN for now (i.e. X-port and Y-port)
        // if ($ctyp != 'GEN' && $ctyp != 'MLPT') {
        if ($ctyp != 'GEN') {
            $result["rslt"] = FAIL;
            $result["reason"] = "PROVISIONING CHANGE - " . "CONTYP: " . $ctyp . " is not supported yet";
            return $result;
        }

        //get updated port data
        $ffacObj->setPortObj();
        if ($ffacObj->portObj->ckt_id > 0) {
            $result["rslt"] = FAIL;
            $result["reason"] = "PROVISIONING CHANGE - " . "FAC(X) " . $ffac . " is already part of a CKT CONNECTION";
            return $result;
        }
        
        //get updated port data
        $tfacObj->setPortObj();
        if ($tfacObj->portObj->ckt_id > 0) {
            $result["rslt"] = FAIL;
            $result["reason"] = "PROVISIONING CONNECT - " . "FAC(Y) " . $tfac . " is already part of a CKT CONNECTION";
            return $result;
        }

        // validate state-event
        $sms = new SMS($ffacObj->portObj->psta, $ffacObj->portObj->ssta, "SV_CONN");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason'] = "PROVISIONING CONNECT - " . $sms->reason;
            return $result;
        }
        $ffacObj->portObj->npsta = $sms->npsta;
        $ffacObj->portObj->nssta = $sms->nssta;

        $sms = new SMS($tfacObj->portObj->psta, $tfacObj->portObj->ssta, "SV_CONN");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason'] = "PROVISIONING CONNECT - " . $sms->reason;
            return $result;
        }
        $tfacObj->portObj->npsta = $sms->npsta;
        $tfacObj->portObj->nssta = $sms->nssta;

        $path_id = 0;
        
        /* this section will be enabled when testing of 100% non-blocking is done */
        // create PATH. The t_pathXY requires all stages to be unique, so createPath() can return fail if 
        // any of the stage are duplicated.
        // $pathObj = new PATH($ffacObj->portObj->port, $tfacObj->portObj->port);
        // $pathObj->createPath();
        // if($pathObj->rslt == 'fail') {
        //     $result['rslt'] = 'fail';
        //     $result['reason'] = "PROVISIONING CONNECT - " . $pathObj->reason;
        //     return $result; 
        // }
        // apply the PATH into the t_stg 
        // $pathObj->setPath();
        /* end of create path section */
        // $path_id = $pathObj->id;

        // Ready for DB updates
        // locate current CKTCON
        // add new CON_IDX
        $cktconObj->addIdx($cktObj->cktcon, $cktObj->id, $cktObj->ckid, $ctyp, $ctyp, $ffacObj->port_id, $ffacObj->port, 1, $tfacObj->port_id, $tfacObj->port, 1, $path_id);
        if ($cktconObj->rslt != SUCCESS) {
            $result['rslt'] = $cktconObj->rslt;
            $result['reason'] = "PROVISIONING CONNECT - " . $cktconObj->reason;
            return $result;
        }
            
        // 4) update PORT's PSTA and link with CKT, CKTCON
        $ffacObj->portObj->updPsta($ffacObj->portObj->npsta, $ffacObj->portObj->nssta, "-");
        if ($ffacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $ffacObj->portObj->rslt;
            $result['reason'] = "PROVISIONING CONNECT - " . $ffacObj->portObj->reason;
            return $result;
        }

        $ffacObj->portObj->updCktLink($cktObj->id, $cktconObj->con, $cktconObj->idx);
        if ($ffacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $ffacObj->portObj->rslt;
            $result['reason'] = "PROVISIONING CONNECT - " . $ffacObj->portObj->reason;
            return $result;
        }
        
        $tfacObj->portObj->updPsta($tfacObj->portObj->npsta, $tfacObj->portObj->nssta, "-");
        if ($tfacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $tfacObj->portObj->rslt;
            $result['reason'] = "PROVISIONING CONNECT - " . $tfacObj->portObj->reason;
            return $result;
        }

        $tfacObj->portObj->updCktLink($cktObj->id, $cktconObj->con, $cktconObj->idx);
        if ($tfacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $tfacObj->portObj->rslt;
            $result['reason'] = "PROVISIONING CONNECT - " . $tfacObj->portObj->reason;
            return $result;
        }

        // update ORDERS table
        $ordObj = new ORDER();
        $ordObj->updateOrderStat($ordno,$mlo,'CHANGE',$ckid,$cls,$adsr,$prot,$ctyp,$ffac,$tfac);

        $result['rows'] = [];
        $result["rslt"] = SUCCESS;
        $result["reason"] = "PROVISIONING CHANGE - " . "SUCCESSFULL";
        return $result;

	}

	
?>