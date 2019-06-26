<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcPortmap.php
 * Change history: 
 * 2018-12-20: created (Ninh)
 */

    /* Initialize Expected inputs */

    $act = "";
    if (isset($_POST['act']))
        $act = $_POST['act'];
        
    $port_id = "";
    if (isset($_POST['port_id']))
        $port_id = $_POST['port_id'];
        
    $node = "";
    if (isset($_POST['node']))
        $node = $_POST['node'];
        
	$slot = "";
    if (isset($_POST['slot']))
        $slot = $_POST['slot'];
        
	$pnum = "";
    if (isset($_POST['pnum']))
        $pnum = $_POST['pnum'];
        
    $ptyp = "";
    if (isset($_POST['ptyp']))
        $ptyp = strtoupper($_POST['ptyp']);
        
	$port = "";
	if (isset($_POST['port']))
        $port = strtoupper($_POST['port']);
        
	$psta = "";
    if (isset($_POST['psta']))
        $psta = strtoupper($_POST['psta']);
        
	$fac_id = "";
    if (isset($_POST['fac_id']))
        $fac_id = $_POST['fac_id'];
        
	$fac = "";
    if (isset($_POST['fac']))
        $fac = strtoupper($_POST['fac']);
        
	$ftyp = "";
    if (isset($_POST['ftyp']))
        $ftyp = strtoupper($_POST['ftyp']);
    
    $ort = "";
    if (isset($_POST['ort']))
        $ort = strtoupper($_POST['ort']);
    
    $spcfnc = "";
    if (isset($_POST['spcfnc']))
        $spcfnc = strtoupper($_POST['spcfnc']);
         
	$ckid = "";
    if (isset($_POST['ckid']))
		$ckid = strtoupper($_POST['ckid']);
    
    
    $evtLog = new EVENTLOG($user, "CONFIGURATION", "PORT MAPPING", $act, '');

    /* Dispatch to Func tions */
	
	if ($act == "query" || $act == "findPort") {
        $result = pmQueryPort($port, $psta, $ptyp);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}

	if ($act == "findFac") {
        $result = pmQueryFac($fac);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}
	if ($act == "findCkid") {
        if ($ckid == '') {
            $ckid = '%';
        }

        $portObj = new PORT();
        $portObj->findPortByCkid($ckid);
        $result['rslt'] = $portObj->rslt;
        $result['reason'] = $portObj->reason;
        $result['rows'] = ($portObj->rows);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}
    // Introduce CfgLog obj for MAP and UNMAP action
    $cfglogObj= new CFGLOG();
	if ($act == "MAP") {
        $result = mapPort($userObj,$port, $fac, $ftyp, $ort, $spcfnc);
        $cfglogObj->log( $user, $act, $port, $fac, $ftyp, $ort, $spcfnc, ($result["rslt"] . '-' . $result["reason"])  );
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result['reason']);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}
	if ($act == "UNMAP") {
        $result = unmapPort($userObj,$port, $fac);
        $cfglogObj->log( $user, $act, $port, $fac, $ftyp, $ort, $spcfnc, ($result["rslt"] . '-' . $result["reason"])  );
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	if ($act == "QUERYMIO") {
        $portObj = new PORT();
        $portObj->findPortBySlot($node, $slot, $ptyp);
        $result['rslt'] = $portObj->rslt;
        $result['reason'] = $portObj->reason;
        $result['rows'] = ($portObj->rows);
        echo json_encode($result);
		mysqli_close($db);
		return;
    }
    //Demo
    if ($act == "SET_RS") {
        $result = setRs($userObj,$port, $fac);
        $cfglogObj->log( $user, $act, $port, $fac, $ftyp, $ort, $spcfnc, ($result["rslt"] . '-' . $result["reason"])  );
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
    }
    if ($act == "SET_DF") {
        $result = setDf($userObj,$port, $fac);
        $cfglogObj->log( $user, $act, $port, $fac, $ftyp, $ort, $spcfnc, ($result["rslt"] . '-' . $result["reason"])  );
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
    }
    if ($act == "SET_SF") {
        $result = setSf($userObj,$port, $fac);
        $cfglogObj->log( $user, $act, $port, $fac, $ftyp, $ort, $spcfnc, ($result["rslt"] . '-' . $result["reason"])  );
        $evtLog->log($result["rslt"], $result['log'] . " | " . $result['reason']);
		echo json_encode($result);
		mysqli_close($db);
		return;
    }
	else {
 		$result["rslt"] = "fail";
        $result["reason"] = "INVALID ACTION";
        $evtLog->log($result["rslt"], $result["reason"]);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}


	/* Functions section */
    function mapPort($userObj, $port, $fac, $ftyp, $ort, $spcfnc) {

        if ($userObj->grpObj->portmap != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = 'Permission Denied';
			return $result;
		}

        // verify fac
        if ($fac == '') {
            $result['rslt'] = 'fail';
            $result['reason'] = 'MISSING FAC';
			return $result;
        }
        
        $facObj = new FAC($fac);
        if ($facObj->rslt == FAIL) {
            $facObj->add($fac, $ftyp, $ort, $spcfnc);
            if ($facObj->rslt == FAIL) {
                $result['rslt'] = $facObj->rslt;
                $result['reason'] = $facObj->reason;
                $result['rows'] = [];
                return $result;
            }
        }

        if ($facObj->port_id > 0) {
            $result["rslt"] = FAIL;
            $result["reason"] = "FAC_ALREADY_MAPPED";
            $result['rows'] = [];
			return $result;
        }

        // verify port
        $portObj = new PORT();
        if ($portObj->loadPort($port) === false) {
            $result['rslt'] = $portObj->rslt;
            $result['reason'] = $portObj->reason;
            $result['rows'] = [];
            return $result;
        }

        if ($portObj->fac_id > 0) {
            $result["rslt"] = FAIL;
            $result["reason"] = "PORT_ALREADY_MAPPED";
            $result['rows'] = [];
			return $result;
        }

        // verify state/evt
        $sms = new SMS($portObj->psta, $portObj->ssta, 'PT_MAP');
        if ($sms->rslt == FAIL) {
			$result["rslt"] = $sms->rslt;
            $result["reason"] = $sms->reason;
            $result['rows'] = [];
			return $result;
        }
		$portObj->linkFac($facObj->id);
        $portObj->updPsta($sms->npsta, $sms->nssta, $portObj->substa);
        $facObj->updPortLink($portObj->id, $port);
        $result = pmqueryFac("");
        $result['rslt'] = "success";
        $result['reason'] = $port . " MAPPED TO " . $fac;
        $result['log'] = "ACTION = MAP | PORT = $port | FAC = $fac | FTYP = $ftyp | ORT = $ort | SPCFNC = $spcfnc";
        return $result;
	}

	function unmapPort($userObj, $port, $fac) {

        if ($userObj->grpObj->portmap != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = 'Permission Denied';
			return $result;
		}
        
		// verify fac
        $facObj = new FAC($fac);
        if ($facObj->rslt == FAIL) {
            $result['rslt'] = $facObj->rslt;
            $result['reason'] = $facObj->reason;
            $result['rows'] = [];
            return $result;
        }

		// verify port
        $portObj = new PORT();
        if ($portObj->loadPort($port) === false) {
            $result['rslt'] = $portObj->rslt;
            $result['reason'] = $portObj->reason;
            $result['rows'] = [];
            return $result;
        }

        if ($facObj->port_id != $portObj->id) {
            $result["rslt"] = FAIL;
            $result["reason"] = "FAC IS NOT CURRENTLY MAPPED TO PORT";
            $result['rows'] = [];
			return $result;
        }

        // verify state/evt
        $sms = new SMS($portObj->psta, $portObj->ssta, 'PT_UNMAP');
        if ($sms->rslt == FAIL) {
			$result["rslt"] = $sms->rslt;
            $result["reason"] = $sms->reason;
            $result['rows'] = [];
			return $result;
        }

		$portObj->unlinkFac();
        $portObj->updPsta($sms->npsta, $sms->nssta, $portObj->substa);
        $facObj->updPortLink(0, '');
        $result = pmQueryPort('','','');
        $result['rslt'] = "success";
        $result['reason'] = $port . " UNMAPPED FROM " . $fac;
        $result['log'] = "ACTION = UNMAP | PORT = $port | FAC = $fac";
        return $result;
    }
    
    // Demo
    function setRs($userObj,$port, $fac ) {

		if ($userObj->grpObj->portmap != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = 'Permission Denied';
			return $result;
        }

        $portObj = new PORT();
        $portObj->loadPort($port);
        if ($portObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason']= $portObj->reason;
            return $result;
        }
  
        // check sms state
        $sms = new SMS($portObj->psta, $portObj->ssta, "MAN_RS");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason'] = $sms->reason;
            return $result;
        }
        $portObj->npsta = $sms->npsta;
        $portObj->nssta = $sms->nssta;

        // update port status
        $portObj->updPsta($portObj->npsta, $portObj->nssta, "-");
		if ($portObj->rslt != SUCCESS) {
            $result['rslt'] = $portObj->rslt;
            $result['reason'] = $portObj->reason;
			return $result;
        }
        $result['rslt'] = SUCCESS;
        $result['reason'] = "RESERVED SUCCESSFULLY";
        return $result;
        
    }

    function setDf($userObj,$port, $fac ) {

		if ($userObj->grpObj->portmap != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = 'Permission Denied';
			return $result;
        }


        $portObj = new PORT();

        $portObj->loadPort($port);
        if ($portObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason']= $portObj->reason;
            return $result;
        }

        // check sms state
        $sms = new SMS($portObj->psta, $portObj->ssta, "MAN_DEF");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason'] = $sms->reason;
            return $result;
        }
        $portObj->npsta = $sms->npsta;
        $portObj->nssta = $sms->nssta;

        // update port status
        $portObj->updPsta($portObj->npsta, $portObj->nssta, "-");
		if ($portObj->rslt != SUCCESS) {
            $result['rslt'] = $portObj->rslt;
            $result['reason'] = $portObj->reason;
			return $result;
        }
        $result['rslt'] = SUCCESS;
        $result['reason'] = "PORT DEFECTED";
        return $result;
        
    }

    function setSf($userObj,$port, $fac ) {

		if ($userObj->grpObj->portmap != "Y") {
			$result['rslt'] = 'fail';
            $result['reason'] = 'Permission Denied';
			return $result;
        }


        $portObj = new PORT();

        $portObj->loadPort($port);
        if ($portObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason']= $portObj->reason;
            return $result;
        }

        // check sms state
        $sms = new SMS($portObj->psta, $portObj->ssta, "MAN_SF");
        if ($sms->rslt == FAIL) {
            $result['rslt'] = FAIL;
            $result['reason'] = $sms->reason;
            return $result;
        }
        $portObj->npsta = $sms->npsta;
        $portObj->nssta = $sms->nssta;

        // update port status
        $portObj->updPsta($portObj->npsta, $portObj->nssta, "-");
		if ($portObj->rslt != SUCCESS) {
            $result['rslt'] = $portObj->rslt;
            $result['reason'] = $portObj->reason;
			return $result;
        }
        $result['rslt'] = SUCCESS;
        $result['reason'] = "PORT IS SF";
        return $result;
        
    }

	function pmQueryPort($port, $psta, $ptyp) {

        $port = str_replace('?','%',$port);
        
        $node = '';
        $slot = '';
        $pnum = '';
        if ($ptyp == '') {
            $p = explode("-", $port);
            if (isset($p[0]))
                $node = $p[0];

            if (isset($p[1]))
                $slot = $p[1];

            if (isset($p[2]))
                $ptyp = $p[2];

            if (isset($p[3]))
                $pnum = $p[3];
        }

        $portObj = new PORT();
        $portObj->queryPort($node, $slot, $pnum, $ptyp, $psta);
		$result['rslt'] = $portObj->rslt;
        $result['reason'] = $portObj->reason;
        $result['rows'] = ($portObj->rows);
        return $result;
	}

	function pmQueryFac($fac) {

        if ($fac == '') {
            $fac = '%';
        }
        
        $portObj = new PORT();
        $portObj->findPortByFac($fac);
        $result['rslt'] = $portObj->rslt;
        $result['reason'] = $portObj->reason;
        $result['rows'] = ($portObj->rows);
        return $result;
	}

?>
