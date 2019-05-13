<?php
/*  Filename: ipcProvDisconnect.php
    Date: 2018-12-17
    By: Ninh
    Copyright: BHD SOLUTIONS, LLC @ 2018
*/  
    // // -- Initialize inputs --

	// $act = "";
	// if (isset($_POST['act'])) {
	// 	$act = $_POST['act'];
	// }

	// $ordno = "";
	// if (isset($_POST['ordno'])) {
	// 	$ordno = $_POST['ordno'];
	// }

    // $mlo = "";
	// if (isset($_POST['mlo'])) {
	// 	$mlo = $_POST['mlo'];
	// }

	// $ckid = "";
	// if (isset($_POST['ckid'])) {
	// 	$ckid = $_POST['ckid'];
	// }

    // $cls = "";
	// if (isset($_POST['cls'])) {
	// 	$cls = $_POST['cls'];
	// }

    // $adsr = "";
	// if (isset($_POST['adsr'])) {
	// 	$adsr = $_POST['adsr'];
	// }

    // $prot = "";
	// if (isset($_POST['prot'])) {
	// 	$prot = $_POST['prot'];
	// }

    // $ctyp = "";
	// if (isset($_POST['ctyp'])) {
	// 	$ctyp = $_POST['ctyp'];
    // }
    
	// $ffac = "";
	// if (isset($_POST['ffac'])) {
	// 	$ffac = $_POST['ffac'];
	// }

	// $tfac = "";
	// if (isset($_POST['tfac'])) {
	// 	$tfac = $_POST['tfac'];
    // }
        
    // $dd = "";
    // if (isset($_POST['dd']))
    //     $dd = $_POST['dd'];

    // $fdd = "";
    // if (isset($_POST['fdd']))
    //     $fdd = $_POST['fdd'];

    // $fport = "";
    // if (isset($_POST['fport']))
    //     $fport = $_POST['fport'];

    // $tport = "";
    // if (isset($_POST['tport']))
    //     $tport = $_POST['tport'];

    // $tktno = "";
    // if (isset($_POST['tktno']))
    //     $tktno = $_POST['tktno'];


    
    // // $input = "SVCCONN: USER=" . $user . ", ACT=" . $act . ", CKID=" . $ckid . ", CLS=" . $cls . ", ADSR=" . $adsr;
	// // $input .= ", PROT=" . $prot . ", ORDNO=" . $ordno . ", MLO=" . $mlo . ", DD=" . $dd . ", FDD=" . $fdd;
	// // $input .= ", CONTYP=" . $ctyp . ", FAC(X)=" . $ffac . ", FAC(Y)=" . $tfac;

    // // $evtLog = new EVTLOG($user, "SVCCONN", $act, $input);
    
    // $evtLog = new EVENTLOG($user, "PROVISIONING", "SETUP SERVICE CONNECTION", $act, $_POST);
    $evtLog = new EVENTLOG($user, "PROVISIONING", "SETUP SERVICE CONNECTION", $act, '');

	// $provLog = new PROVLOG();

	// // --- Dispatch by ACTION ---
		
	// if ($act == "DISCONNECT") {
    //     $result = provDisconnect($userObj, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac);
    //     $evtLog->log($result["rslt"], $result["reason"]);
	// 	$provLog->log($user, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $tktno);
    //     echo json_encode($result);
    //     mysqli_close($db);
    //     return;
    // }
    // else {
    //     $result['rslt'] = FAIL;
    //     $result['reason'] = INVALID_ACTION;
    //     $result['rows'] = [];
    //     $evtLog->log($result["rslt"], $result["reason"]);
    //     echo json_encode($result);
    //     mysqli_close($db);
    //     return;
    // }
	
    function provDisconnect($userObj, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac) {
        
        $result['log'] = "ACTION = DISCONNECT | ORDNO = $ordno | MLO = $mlo | CKID = $ckid | CLS = $cls | ADSR = $adsr | PROT = $prot | CTYP = $ctyp | FFAC = $ffac | TFAC = $tfac";

        if ($userObj->grpObj->prov != "Y") {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:PERMISSION DENIED"; 
            $result['reason'] = 'Permission Denied';
			return $result;
		}
        
        // if ckid is not in DB, then this is an invalid CKT
		$cktObj = new CKT($ckid);
		if ($cktObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP5:$cktObj->reason"; 
            $result['reason'] = $cktObj->reason;
            return $result;
        }

        // verify ffac and tfac are currently connected under this CKT
        $cktconObj = new CKTCON($cktObj->cktcon);
        if ($cktconObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP5:$cktconObj->reason"; 
            $result['reason'] = $cktconObj->reason;
            return $result;
        }
        
        // the ffac must exist in DB and currently part of same CKTCON/IDX
		$ffacObj = new FAC($ffac);
		if ($ffacObj->rslt != SUCCESS || $ffacObj->port_id == 0) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:FAC IS INVALID"; 
            $result['reason'] = INVALID_FAC . ": " . $ffac;
			return $result;
		}
        $ffacObj->setPortObj();
        if ($ffacObj->portObj->cktcon != $cktconObj->con) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP4:FAC IS MAPPED TO DIFFERENT PORT";
            $result['reason'] = "FAC: " . $ffac . " cktcon: " . $ffacObj->portObj->cktcon . " is not part of CKTCON: " . $cktconObj->con;
			return $result;
        }
        
        // the tfac must exist in DB and must be currently mapped
		$tfacObj = new FAC($tfac);
		if ($tfacObj->rslt != SUCCESS || $tfacObj->port_id == 0) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP2:FAC IS INVALID"; 
            $result['reason'] = INVALID_FAC . ": " . $tfac;
			return $result;
        }
        $tfacObj->setPortObj();
        if ($tfacObj->portObj->cktcon != $cktconObj->con) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP4:FAC IS MAPPED TO DIFFERENT PORT";
            $result['reason'] = "FAC: " . $tfac . " is not part of CKTCON: " . $cktconObj->con;
			return $result;
        }

        // verify both ffac and tfac are connected on same IDX
        if ($ffacObj->portObj->con_idx != $tfacObj->portObj->con_idx) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:FFAC and TFAC are not connected on same CKTCON/IDX";
            $result['reason'] = "FFAC and TFAC are not connected on same CKTCON/IDX";
			return $result;
        }

        // verify IDX exists in CKTCON
        $cktconObj->loadIdx($ffacObj->portObj->con_idx);
        if ($cktconObj->rslt == FAIL) {
            $result['rslt'] = "fail";
            $result['jeop'] = "SP5:$cktconObj->reason";
            $result['reason'] = $cktconObj->reason;
			return $result;
        }
        
                // extract node number from portObj
                $fNode = $ffacObj->portObj->node;
                $tNode = $tfacObj->portObj->node;
        
                // get fromNode stat and toNode stat
                $fNodeObj = new NODE($fNode);
                $tNodeObj = new NODE($tNode);
        
                // deny action if fnode is not in service
                if ($fNodeObj->stat !== 'INS') {
                    $result['rslt'] = 'fail';
                    $result['jeop'] = "SP5:ACCESS DENIED; NODE ($fNode) HAS BEEN LOCKED BY SYSTEM ADMINISTRATOR $fNodeObj->user";
                    $result['reason'] = "ACCESS DENIED; NODE ($fNode) HAS BEEN LOCKED BY SYSTEM ADMINISTRATOR $fNodeObj->user";
                    return $result;
                }
        
                // deny action if tnode is not in service
                if ($tNodeObj->stat !== 'INS') {
                    $result['rslt'] = 'fail';
                    $result['jeop'] = "SP5:ACCESS DENIED; NODE ($tNode) HAS BEEN LOCKED BY SYSTEM ADMINISTRATOR $tNode->user";
                    $result['reason'] = "ACCESS DENIED; NODE ($tNode) HAS BEEN LOCKED BY SYSTEM ADMINISTRATOR $tNodeObj->user";
                    return $result;
                }
        
        
        // validate state-event
        $sms = new SMS($ffacObj->portObj->psta, $ffacObj->portObj->ssta, "SV_DISCON");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP3:FAC STATUS (".$ffacObj->portObj->psta.")";            
            $result['reason'] = $sms->reason;
            return $result;
        }
        $ffacObj->portObj->npsta = $sms->npsta;
        $ffacObj->portObj->nssta = $sms->nssta;

        $sms = new SMS($tfacObj->portObj->psta, $tfacObj->portObj->ssta, "SV_DISCON");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['jeop'] = "SP3:FAC STATUS (".$tfacObj->portObj->psta.")";            
            $result['reason'] = $sms->reason;
            return $result;
        }
        $tfacObj->portObj->npsta = $sms->npsta;
        $tfacObj->portObj->nssta = $sms->nssta;

        /* will enable this after testing of 100% non-blocking */
         
        $pathObj = new PATH($ffacObj->portObj->port, $tfacObj->portObj->port);
        $pathObj->load();
        if($pathObj->rslt == 'fail') {
            $result['rslt'] = 'fail';
            $result['jeop'] = "SP5:UNABLE TO LOAD PATH";            
            $result['reason'] = "PROVISIONING CONNECT - " . $pathObj->reason;
            return $result; 
        }
        $pathObj->resetPath();
        $pathObj->drop();
        /* */

        // Ready for DB updates
        // 1) remove IDX
        $cktconObj->deleteIdx($cktconObj->con, $cktconObj->idx);
        if ($cktconObj->rslt != SUCCESS) {
            $result['rslt'] = $cktconObj->rslt;
            $result['jeop'] = "SP5:$cktconObj->reason";            
            $result['reason'] = $cktconObj->reason;
            return $result;
        }
        
        // 2) if last IDX removed, then remove CKT as well
        $newCktconObj = new CKTCON($cktconObj->con);
        if ($newCktconObj->rslt == FAIL) {
            $cktObj->deleteCkt($ckid);
            if ($cktObj->rslt == FAIL) {
                $ressult['rslt'] = FAIL;
                $result['jeop'] = "SP5:$cktObj->reason";            
                $result['reason'] = $cktObj->reason;
                return $result;
            }
        }
                
        // 3) update PORT's PSTA and link with CKT, CKTCON
        $ffacObj->portObj->updPsta($ffacObj->portObj->npsta, $ffacObj->portObj->nssta, "-");
		if ($ffacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $ffacObj->portObj->rslt;
            $result['jeop'] = "SP5:".$ffacObj->portObj->reason;
            $result['reason'] = $ffacObj->portObj->reason;
			return $result;
		}

        $ffacObj->portObj->updCktLink(0, 0, 0);
		if ($ffacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $ffacObj->portObj->rslt;
            $result['jeop'] = "SP5:".$ffacObj->portObj->reason;
            $result['reason'] = $ffacObj->portObj->reason;
			return $result;
        }
        
        $tfacObj->portObj->updPsta($tfacObj->portObj->npsta, $tfacObj->portObj->nssta, "-");
		if ($tfacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $tfacObj->portObj->rslt;
            $result['jeop'] = "SP5:".$tfacObj->portObj->reason;
            $result['reason'] = $tfacObj->portObj->reason;
			return $result;
		}

        $tfacObj->portObj->updCktLink(0, 0, 0);
		if ($tfacObj->portObj->rslt != SUCCESS) {
            $result['rslt'] = $tfacObj->portObj->rslt;
            $result['jeop'] = "SP5:".$tfacObj->portObj->reason;
            $result['reason'] = $tfacObj->portObj->reason;
			return $result;
        }
        
        // update ORDERS table
        // $ordObj = new ORDER();
        // $ordObj->updateOrderStat($ordno,$mlo,'DISCONNECT',$ckid,$cls,$adsr,$prot,$ctyp,$ffac,$tfac);

        $result['rows'] = [];
        $result["rslt"] = SUCCESS;
        $result["reason"] = "PROVISIONING DISCONNECT - " . "SUCCESSFUL";
        return $result;

	}

?>