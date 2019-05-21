<?php
/*
    Filename: ipcProv.php
    Date: 2018-12-10
    By: Ninh
    Copyright: BHD SOLUTIONS, LLC @ 2018
*/  


include "ipcProvDisconnect.php";
include "ipcProvConnect.php";
// -- Initialize inputs -- //

	$act = "";
	if (isset($_POST['act'])) {
		$act = $_POST['act'];
	}

	$ckid = "";
	if (isset($_POST['ckid'])) {
		$ckid = $_POST['ckid'];
	}

	$cls = "";
	if (isset($_POST['cls'])) {
		$cls = $_POST['cls'];
	}

	$adsr = "";
	if (isset($_POST['adsr'])) {
		$adsr = $_POST['adsr'];
	}

	$prot = "";
	if (isset($_POST['prot'])) {
		$prot = $_POST['prot'];
	}

	$ordno = "";
	if (isset($_POST['ordno'])) {
		$ordno = $_POST['ordno'];
	}

	$mlo = "";
	if (isset($_POST['mlo'])) {
		$mlo = $_POST['mlo'];
	}

	$dd = "";
	if (isset($_POST['dd'])) {
		$dd = $_POST['dd'];
	}

	$fdd = "";
	if (isset($_POST['fdd'])) {
		$fdd = $_POST['fdd'];
	}

	$ctyp = "";
	if (isset($_POST['ctyp'])) {
		$ctyp = $_POST['ctyp'];
	}

	$cktcon = "";
	if (isset($_POST['cktcon'])) {
		$cktcon = $_POST['cktcon'];
	}

	$ffac = "";
	if (isset($_POST['ffac'])) {
		$ffac = $_POST['ffac'];
	}

	$tfac = "";
	if (isset($_POST['tfac'])) {
		$tfac = $_POST['tfac'];
	}

	$idx = "";
	if(isset($_POST['idx'])) {
		$idx = $_POST['idx'];
	}

	$newffac = "";
	if (isset($_POST['newffac'])) {
		$newffac = $_POST['newffac'];
	}

	$newtfac = "";
	if (isset($_POST['newtfac'])) {
		$newtfac = $_POST['newtfac'];
	}

	$fport= "";
	if (isset($_POST['fport'])) {
		$fport = $_POST['fport'];
	}
	
	$tport = "";
    if (isset($_POST['tport'])) {
		$tport = $_POST['tport'];
	}
	
	$newfport= "";
	if (isset($_POST['newfport'])) {
		$newfport = $_POST['newfport'];
	}
	
	$newtport = "";
    if (isset($_POST['newtport'])) {
		$newtport = $_POST['newtport'];
	}


	$tktno = "";
	if (isset($_POST['tktno'])) {
		$tktno = $_POST['tktno'];
    }

	// $input = "SVCCONN: USER=" . $user . ", ACT=" . $act . ", CKID=" . $ckid . ", CLS=" . $cls . ", ADSR=" . $adsr;
	// $input .= ", PROT=" . $prot . ", ORDNO=" . $ordno . ", MLO=" . $mlo . ", DD=" . $dd . ", FDD=" . $fdd;
	// $input .= ", CONTYP=" . $ctyp . ", FAC(X)=" . $ffac . ", FAC(Y)=" . $tfac;

	// $evtLog = new EVTLOG($user, "SVCCONN", $act, $input);

    $evtLog = new EVENTLOG($user, "PROVISIONING", "SETUP SERVICE CONNECTION", $act, '');
	$provLog = new PROVLOG();

	// --- Dispatch by ACTION ---
	if ($act == "query" || $act == "queryCkid") {
		$cktObj = new CKT();
		$cktObj->queryCkid($ckid, $cls, $adsr, $prot);
		$result['rslt'] = $cktObj->rslt;
		$result['reason'] = $cktObj->reason;
		$result['rows'] = $cktObj->rows;
        echo json_encode($result);
		mysqli_close($db);
		return;
	}

	// if ($act == "queryOrd") {
	// 	$result = provQueryOrd($ordno, $mlo);
	// 	echo json_encode($result);
	// 	mysqli_close($db);
	// 	return;
	// }
	
	if ($act == "queryCktcon") {
		$cktconObj = new CKTCON();
		$cktconObj->queryCktconWithFac($cktcon);
		$result['rslt'] = $cktconObj->rslt;
		$result['reason'] = $cktconObj->reason;
		$result['rows'] = $cktconObj->rows;
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	
	if ($act == "queryCktconByCkid") {
		$result = queryCktconByCkid($ckid);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}

	if ($act == "queryFacX" || $act == "queryFacY") {
		$facObj = new FAC();
		if ($act == "queryFacX")
			$facObj->queryFacByPtyp("X");
		else
			$facObj->queryFacByPtyp("Y");

		$result['rslt'] = $facObj->rslt;
		$result['reason'] = $facObj->reason;
		$result['rows'] = $facObj->rows;
		echo json_encode($result);
		mysqli_close($db);
		return;
	}

	if ($act == "UPDATE_CKT") {
		$result = provUpdateCkt($user, $ckid, $cls, $adsr, $prot, $ordno, $mlo, $userObj);
		$evtLog->log($result['rslt'], $result['log'] . " | " . $result['reason']);
		$provLog->log($user, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $tktno);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	if ($act == "CONNECT") {
    	$provLog = new PROVLOG();
        $result = provConnect($userObj, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac);
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        $provLog->log($user, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $tktno);
        echo json_encode($result);
        mysqli_close($db);
        return;
	}

	if ($act == "DISCONNECT") {
		$provLog = new PROVLOG();
        $result = provDisconnect($userObj, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac);
        $evtLog->log($result["rslt"], $result["reason"]);
		$provLog->log($user, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $tktno);
        echo json_encode($result);
        mysqli_close($db);
        return;
	}
	
	if ($act == "CHANGE") {
	    $provLog = new PROVLOG();
        $result = provChange($userObj, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac, $newffac, $newtfac, $fport, $tport,$newfport, $newtport, $dd, $fdd, $tktno);
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        $provLog->log($user, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $newffac, $newfport, $newtfac, $newtport, $result['reason'], $tktno);
        echo json_encode($result);
        mysqli_close($db);
        return;
    }

	else {
		$result["rslt"] = FAIL;
		$result["reason"] = "INVALID_ACTION";
		$evtLog->log($result['rslt'], $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}

	// -- Functions Section -- //
	function queryCktconByCkid($ckid) {

		$cktObj = new CKT($ckid);
		if ($cktObj->rslt == "fail") {
			$result['rslt'] = FAIL;
			$result['reason'] = "INVALID CKID for CKT";
			return $result;
		}

		$cktconObj = new CKTCON();
		$cktconObj->queryCktconByCkid($ckid);
		if ($cktconObj->rslt == "fail") {
			$result['rslt'] = FAIL;
			$result['reason'] = "INVALID CKID for CKTCON";
			return $result;
		}

		$result['rows'] = $cktconObj->rows;

		for ($i=0; $i<count($result['rows']); $i++) {
			$result['rows'][$i]['cls'] = $cktObj->cls;
			$result['rows'][$i]['adsr'] = $cktObj->adsr;
			$result['rows'][$i]['prot'] = $cktObj->prot;
		}
		
		$result['rslt'] = SUCCESS;
		$result['reason'] = "QUERY CKID SUCCESS";
		return $result;
	}

	function provUpdateCkt($user, $ckid, $cls, $adsr, $prot, $ordno, $mlo, $userObj) {

		$result['log'] = "ACTION = UPDATE_CKT";
		if ($ckid != $cktObj->ckid)
		$result['log'] .= " | CKID = " . $cktObj->ckid . " --> " . $ckid;

		if ($cls != $cktObj->cls)
		$result['log'] .= " | CLS = " . $cktObj->cls . " --> " . $cls;

		if ($adsr != $cktObj->adsr)
		$result['log'] .= " | ADSR = " . $cktObj->adsr . " --> " . $adsr;

		if ($prot != $cktObj->prot)
		$result['log'] .= " | PROT = " . $cktObj->prot . " --> " . $prot;

		if ($prot != $cktObj->ordno)
		$result['log'] .= " | ORDNO = " . $cktObj->ordno . " --> " . $ordno;

		if ($prot != $cktObj->mlo)
		$result['log'] .= " | MLO = " . $cktObj->mlo . " --> " . $mlo;


		if ($userObj->grpObj->prov != "Y") {
			$result['rslt'] = 'fail';
			$result['jeop'] = "SP5:PERMISSION DENIED";
            $result['reason'] = 'Permission Denied';
			return $result;
		}
		
		$cktObj = new CKT($ckid);
		if ($cktObj->rslt == FAIL) {
			$result['rslt'] = FAIL;
			$result['jeop'] = "SP5:$cktObj->reason";
			$result['reason'] = $cktObj->reason;
			return $result;
		}

		$cktObj->updateCkt($cls, $adsr, $prot, $ordno, $mlo);
		if ($cktObj->rslt == FAIL) {
			$result['rslt'] = FAIL;
			$result['jeop'] = "SP5:$cktObj->reason";
			$result['reason'] = $cktObj->reason;
			return $result;
		}

		$cktObj->queryCkid($ckid,"","","");
		if ($cktObj->rslt == FAIL) {
			$result["rslt"] = FAIL;
			$result['jeop'] = "SP5:$cktObj->reason";
			$result["reason"] = $cktObj->reason;
			return $result;
		}

		$result['rows'] = $cktObj->rows;
		$result["rslt"] = SUCCESS;
		$result["reason"] = "UPDATE_CKT_COMPLETED";
		return $result;
	}

	function provChange($userObj, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac, $newffac, $newtfac, $fport, $tport,$newfport, $newtport, $dd, $fdd, $tktno) {
		if(!(($ffac == $newffac && $tfac != $newtfac) || ($ffac != $newffac && $tfac == $newtfac))) {
			$result['rslt'] = 'fail';
			$result['jeop'] = "SP4:ONLY ONE NEW/IN FACILITY MUST BE THE SAME WITH THE OLD/OUT ONE";
			$result['reason'] = "PROV CHANGE: ONLY ONE NEW/IN FACILITY MUST BE THE SAME WITH THE OLD/OUT ONE";
			$result['log'] = "ACTION = PROV_CHANGE | ORDNO = $ordno | MLO = $mlo | CKID = $ckid | IDX = $idx | CLS = $cls | ADSR = $adsr | PROT = $prot | CONTYP = $ctyp | OLD-FAC(X) = $ffac | OLD-FAC(Y) = $tfac | NEW-FAC(X) = $newffac | NEW-FAC(Y) = $newtfac";
			return $result;
		}

		$newFfacObj = new FAC($newffac);
		if ($newFfacObj->rslt != SUCCESS || $newFfacObj->port_id == 0) {
			$result['rslt'] = "fail";
			$result['jeop'] = "SP2:FAC IS INVALID";
			$result['reason'] = "PROV CHANGE: INVALID_FAC - " . $newffac;
			$result['log'] = "ACTION = PROV_CHANGE | ORDNO = $ordno | MLO = $mlo | CKID = $ckid | IDX = $idx | CLS = $cls | ADSR = $adsr | PROT = $prot | CONTYP = $ctyp | OLD-FAC(X) = $ffac | OLD-FAC(Y) = $tfac | NEW-FAC(X) = $newffac | NEW-FAC(Y) = $newtfac";
			return $result;
		}
        
		// the tfac must exist in DB and must be currently mapped
		$newTfacObj = new FAC($newtfac);
		if ($newTfacObj->rslt != SUCCESS || $newTfacObj->port_id == 0) {
			$result['rslt'] = "fail";
			$result['jeop'] = "SP2:FAC IS INVALID";
            $result['reason'] = "PROV CHANGE: INVALID_FAC - " . $newtfac;
			$result['log'] = "ACTION = PROV_CHANGE | ORDNO = $ordno | MLO = $mlo | CKID = $ckid | IDX = $idx | CLS = $cls | ADSR = $adsr | PROT = $prot | CONTYP = $ctyp | OLD-FAC(X) = $ffac | OLD-FAC(Y) = $tfac | NEW-FAC(X) = $newffac | NEW-FAC(Y) = $newtfac";
			return $result;
		}
		
		if(!(($newFfacObj->portObj->ckt_id > 0 && $newTfacObj->portObj->ckt_id == 0) ||  
		($newFfacObj->portObj->ckt_id == 0 && $newTfacObj->portObj->ckt_id > 0))) {
			$result['rslt'] = "fail";
			$result['jeop'] = "SP4:ONE FAC MUST BE THE UNCONNECTED ONE";
			$result['reason'] = "PROV CHANGE: INVALID FACS - $newffac AND $newtfac";
			$result['log'] = "ACTION = PROV_CHANGE | ORDNO = $ordno | MLO = $mlo | CKID = $ckid | IDX = $idx | CLS = $cls | ADSR = $adsr | PROT = $prot | CONTYP = $ctyp | OLD-FAC(X) = $ffac | OLD-FAC(Y) = $tfac | NEW-FAC(X) = $newffac | NEW-FAC(Y) = $newtfac";
			return $result;
		}

        /////////////---Begin---/////////////
        $provLog = new PROVLOG();
        $result = provDisconnect($userObj, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac);
		$provLog->log($userObj->uname, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $ffac, $fport, $tfac, $tport, $result['reason'], $tktno);
		if($result['rslt'] == 'fail') {
			$result['log'] = "ACTION = PROV_CHANGE | ORDNO = $ordno | MLO = $mlo | CKID = $ckid | IDX = $idx | CLS = $cls | ADSR = $adsr | PROT = $prot | CONTYP = $ctyp | OLD-FAC(X) = $ffac | OLD-FAC(Y) = $tfac | NEW-FAC(X) = $newffac | NEW-FAC(Y) = $newtfac";
			return $result;
		}

        $result = provConnect($userObj, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $newffac, $newtfac);
		$provLog->log($userObj->uname, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $dd, $fdd, $act, $ctyp, $newffac, $newfport, $newtfac, $newtport, $result['reason'], $tktno);
		$result['log'] = "ACTION = PROV_CHANGE | ORDNO = $ordno | MLO = $mlo | CKID = $ckid | IDX = $idx | CLS = $cls | ADSR = $adsr | PROT = $prot | CONTYP = $ctyp | OLD-FAC(X) = $ffac | OLD-FAC(Y) = $tfac | NEW-FAC(X) = $newffac | NEW-FAC(Y) = $newtfac";
		return $result;
	}
	
	
?>