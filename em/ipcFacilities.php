<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcFacilities.php
 * Change history: 
 * 2018-12-20: created (Ninh)
 */

 /* Initialize expected inputs */

    $act = "";
    if (isset($_POST['act'])) {
		$act = $_POST['act'];
	}
		
	$fac_id = 0;
	if (isset($_POST['fac_id'])) {
		$fac_id = $_POST['fac_id'];
	}
	    
    $fac = "";
    if (isset($_POST['fac'])) {
		$fac = strtoupper($_POST['fac']);
	}
		
    
    $ftyp = "";
    if (isset($_POST['ftyp'])) {
		$ftyp = strtoupper($_POST['ftyp']);
	}
		
    $ort = "";
    if (isset($_POST['ort'])) {
		$ort = strtoupper($_POST['ort']);
	}

	$spcfnc = "";
	if (isset($_POST['spcfnc'])) {
		$spcfnc = strtoupper($_POST['spcfnc']);
	}

	$range ="";
	if (isset($_POST['range'])) {
		$range = strtoupper($_POST['range']);
	}

	$psta ="";
	if (isset($_POST['psta'])) {
		$psta = strtoupper($_POST['psta']);
	}

	// $evtLog = new EVTLOG($user, "FACILITY", $act, $input);
	$evtLog = new EVENTLOG($user, "CONFIGURATION", "SETUP FACILITY", $act, '');

	// Dispatch to Functions	
	
	if ($act == "findAvailFac") {
        $facObj = new FAC();
        $facObj->findAvailFac();
        $result['rslt'] = $facObj->rslt;
        $result['reason'] = $facObj->reason;
		$result['rows'] = $facObj->rows;
		mysqli_close($db);
        $debugObj->close();
		echo json_encode($result);
		return;
	}

	if ($act == "findFac"  || $act == "findFOS") {
		$facObj = new FAC();
        $facObj->findFacLike($fac, $ftyp, $ort, $spcfnc, $psta);
        $result['rslt'] = $facObj->rslt;
        $result['reason'] = $facObj->reason;
		$result['rows'] = $facObj->rows;
		mysqli_close($db);
        $debugObj->close();
		echo json_encode($result);
		return;
	}

	if ($act == "queryTestFac") {
		$result = queryTestFac($fac);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
	}

	if ($act == "ADD" ) {
		if ($range == ""){
			$result = addFac($fac, $ftyp, $ort, $spcfnc, $userObj);
		} 
		else {
			$result = addFacs($fac, $ftyp, $ort, $spcfnc, $range, $userObj);
		}
		// In this case, $db is reused multiple times before close
		$evtLog->log($result['rslt'], $result['log'] . " | " . $result['reason']);
        mysqli_close($db);
        $debugObj->close();
		echo json_encode($result);
		return;
	}
	
	if ($act == "UPDATE") {
		$result = updateFac($fac, $ftyp, $ort, $spcfnc,$userObj);
		$evtLog->log($result['rslt'], $result['log'] . " | " . $result['reason']);
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);
		return;
	}
	
	if ($act == "DELETE") {
		$result = deleteFac($fac, $userObj);
		$evtLog->log($result['rslt'], $result['log'] . " | " . $result['reason']);
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);
		return;
	}
	
	if ($act == "queryFtyp") {
		$result = queryFtyp();
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);
		return;
	}
	
	if ($act == "queryOrt") {
		$result = queryOrt();
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);
		return;
	}
	
	if ($act == "querySpcfnc") {
		$result = querySpcfnc();
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);
		return;
	}

	else {
 		$result["rslt"] = "fail";
		$result["reason"] = "ACTION " . $act . " is under development or not supported";
		$evtLog->log($result["rslt"], $result["reason"]);
        echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
    }
    	
   
	function addFac($fac, $ftyp, $ort, $spcfnc, $userObj) {
		global $debugObj;

		$result['log'] = "ACTION = ADD | FAC = $fac | FTYP = $ftyp | ORT = $ort | SPCFNC = $spcfnc";
		if ($userObj->grpObj->portmap != "Y") {
			$result['rslt'] = 'fail';
			 $result['reason'] = 'Permission Denied';
			 $debugObj->log($result['reason']);
			return $result;
		}

        if ($fac == "") {
            $result['rslt'] = FAIL;
			$result['reason'] = "INVALID_FAC";
			$debugObj->log($result['reason']);
            return $result;
        }
        
        $facObj = new FAC($fac);
        if ($facObj->rslt == SUCCESS) {
            $result['rslt'] = FAIL;
			$result['reason'] = "FAC_ALREADY_EXIST";
			$debugObj->log($result['reason']);
            return $result;
        }

        $facObj->add($fac, $ftyp, $ort, $spcfnc);
        if ($facObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
			$result['reason'] = $facObj->reason;
			$debugObj->log($result['reason']);
            return $result;
        }

        $facObj->findFacLike($fac, $ftyp, $ort, $spcfnc,"");
        if ($facObj->rslt == FAIL) {
            $result["rslt"] = "fail";
			$result["reason"] = $facObj->reason;
			$debugObj->log($result['reason']);
            return $result;
        }

        $result['rslt'] = "success";
        $result['reason'] = "FAC_ADDED";
		$result['rows'] = $facObj->rows;
        return $result;
	}


	function addFacs($fac, $ftyp, $ort, $spcfnc, $range, $userObj){
		global $debugObj;

		if ($userObj->grpObj->portmap != "Y") {
			$result['rslt'] = 'fail';
         	$result['reason'] = 'Permission Denied';
			 $debugObj->log($result['reason']);
			return $result;
		}

		//last dash position
		$dashPos = strrpos($fac,'-');  
		//In case there is no dash in Fac
		if($dashPos == "") 
    		$dashPos = -1;
		// string length of fac
		$facLength= strlen($fac) -1; 	 

		// Initialize postion of digits
		$digitFirstPos=-1;
		$digitLastPos=-1;

		for($i = $facLength; $i > $dashPos; $i--) {
			if ( is_numeric($fac[$i])) {
				if ($digitLastPos == -1) {
					$digitLastPos = $i;
					$digitFirstPos = $i;
				}          
				else if ($i == ($digitFirstPos -1))
					$digitFirstPos = $i;
			}
		}
		
		if ($digitLastPos == -1)
		{
			$result["rslt"] = "fail";
			$result["reason"] = "There is no digit in the last block of fac string!";
		}
		else {
			
			$digitNum = $digitLastPos - $digitFirstPos + 1;

			$startValue = (int)(substr($fac, $digitFirstPos, $digitNum)); 
			$maxValue = pow(10, $digitNum)-1; 
			
			$maxRange = $maxValue - $startValue +1; 

			//convert $range from string to int number
			$rangeFac = (int)$range;
			if ($rangeFac > $maxRange) {
				$result["rslt"] = "fail";
				$result["reason"] = "The range is too big. The maximum range is ".$maxRange."!";
				$debugObj->log($result['reason']);
			}
			else {
				for ($i=0; $i < $rangeFac ; $i++) {
					//update the digit number
					$currentValue = $startValue + $i; 
					//convert to string format
					$digitString = str_pad($currentValue,$digitNum,"0",STR_PAD_LEFT); 
					//replace the digitString into fac
					$updatedFac = substr_replace($fac,$digitString, $digitFirstPos, $digitNum); 
					$result = addFac($updatedFac, $ftyp, $ort, $spcfnc, $userObj); 
					if ($result['rslt'] == "fail") {
						$result['stopIndex'] = $i;
						break;
                    }
				}
			}

		}
		return $result;
		
	}


	function updateFac($fac, $ftyp, $ort, $spcfnc,$userObj) {
		global $debugObj;

		if ($userObj->grpObj->portmap != "Y") {
			$result['rslt'] = 'fail';
        	$result['reason'] = 'Permission Denied';
			$debugObj->log($result['reason']);
			return $result;
		}

        $facObj = new FAC($fac);
        if ($facObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
			$result['reason'] = $facObj->reason;
			$debugObj->log($result['reason']);
            return $result;
        }

        $facObj->update($ftyp, $ort, $spcfnc);
        if ($facObj->rslt == FAIL) {
            $result['rslt'] = FAIL;
			$result['reason'] = $facObj->reason;
			$debugObj->log($result['reason']);
            return $result;
        }

        $facObj->findFacLike($fac,'','','','');
        if ($facObj->rslt == FAIL) {
            $result["rslt"] = "fail";
			$result["reason"] = $facObj->reason;
			$debugObj->log($result['reason']);
            return $result;
        }

		$result['log'] = "ACTION = UPDATE";
		if ($fac != $facObj->fac)
		$result['log'] .= " | FAC = " . $facObj->fac . " --> " . $fac;

		if ($ftyp != $facObj->ftyp)
		$result['log'] .= " | FTYP = " . $facObj->ftyp . " --> " . $ftyp;

		if ($ort != $facObj->ort)
		$result['log'] .= " | ORT = " . $facObj->ort . " --> " . $ort;

		if ($spcfnc != $facObj->spcfnc)
		$result['log'] .= " | SPCFNC = " . $facObj->spcfnc . " --> " . $spcfnc;

        $result['rslt'] = "success";
        $result['reason'] = "FAC_UPDATED";
		$result['rows'] = $facObj->rows;
        return $result;
	}

	
	function deleteFac($fac, $userObj) {
		global $debugObj;

		$result['log'] = "ACTION = DELETE | FAC = $fac";
		if ($userObj->grpObj->portmap != "Y") {
			$result['rslt'] = 'fail';
         	$result['reason'] = 'Permission Denied';
			 $debugObj->log($result['reason']);
			return $result;
		}

		if ($fac == "") {
			$result["rslt"] = "fail";
			$result["reason"] = "Missing FAC";
			$debugObj->log($result['reason']);
			return $result;
		}
        
        $facObj = new FAC($fac);
        if ($facObj->rslt == FAIL) {
            $result["rslt"] = "fail";
			$result["reason"] = $facObj->reason;
			$debugObj->log($result['reason']);
            return $result;
        }
        
        
        $facObj->delete($fac);
        if ($facObj->rslt == FAIL) {
            $result["rslt"] = "fail";
			$result["reason"] = $facObj->reason;
			$debugObj->log($result['reason']);
            return $result;
        }

        // $facObj->findFacLike('%','','','','');
        // if ($facObj->rslt == FAIL) {
        //     $result["rslt"] = "fail";
		// 	$result["reason"] = $facObj->reason;
        //     return $result;
        // }

        $result['rslt'] = "success";
        $result['reason'] = "FAC_DELETED";
		$result['rows'] = [];
        return $result;
	}
	
	function queryFtyp() {
		$ftypObj = new FTYP();
		
		$result['rslt'] = $ftypObj->rslt;
		$result['reason'] = $ftypObj->reason;
		$result['rows'] = $ftypObj->rows;
		return $result;
	}

	function queryOrt() {
		$ortObj = new ORT();
		
		$result['rslt'] = $ortObj->rslt;
		$result['reason'] = $ortObj->reason;
		$result['rows'] = $ortObj->rows;
		return $result;
	}

	function querySpcfnc() {
		$spcfncObj = new SPCFNC();
		
		$result['rslt'] = $spcfncObj->rslt;
		$result['reason'] = $spcfncObj->reason;
		$result['rows'] = $spcfncObj->rows;
		return $result;
	}

	
	function queryTestFac($fac) {
		global $debugObj;
		// the fac must exist in DB 
		$facObj = new FAC($fac);
		if ($fac == '' || $facObj->rslt == FAIL) {
            $result['rslt'] = "fail";
			$result['reason'] = "FFAC: " . $fac . " DOES NOT EXIST";
			$debugObj->log($result['reason']);
			return $result;
        }
        
        // the fac must have port mapped
        if ($facObj->port_id == 0) {
            $result['rslt'] = "fail";
			$result['reason'] = "FFAC: " . $fac . " IS NOT MAPPED TO A PORT";
			$debugObj->log($result['reason']);
			return $result;
		}
		$node = $facObj->portObj->node;

		$facObj->queryTestFacByNode($node);
		if ($facObj->rslt == FAIL) {
            $result['rslt'] = "fail";
			$result['reason'] = $facObj->reason;
			$debugObj->log($result['reason']);
			return $result;
		}

		$result['rslt'] = "success";
		$result['reason'] = "TEST-FAC QUERIED";
		$result['rows'] = $facObj->rows;
		return $result;

	}
   	
	

?>
