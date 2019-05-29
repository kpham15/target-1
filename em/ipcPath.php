<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: coQueryPath.php
 * Change history: 
 * 11-2-2018: created (Alex)
 */

    //Initialize expected inputs
    $act = "";
    if (isset($_POST['act'])) {
		$act = $_POST['act'];
	}

    $id = "";
    if (isset($_POST['id'])) {
		$id = $_POST['id'];
	}
    
	$node = "";
    if (isset($_POST['node'])) {
		$node = $_POST['node'];
	}
    
    $slot = "";
    if (isset($_POST['slot'])) {
		$slot = $_POST['slot'];
	}
    
	$pnum = "";
	if (isset($_POST['pnum'])) {
		$pnum = $_POST['pnum'];
	}
	
	$ptyp = "";
	if (isset($_POST['ptyp'])) {
		$ptyp = $_POST['ptyp'];        
	}

	$ckid = "";
    if (isset($_POST['ckid'])) {
        $ckid = $_POST['ckid'];
	}

	$port = "";
    if (isset($_POST['port'])) {
        $port = $_POST['port'];
	}

	$fac = "";
    if (isset($_POST['fac'])) {
        $fac = $_POST['fac'];
	}

	// $evtLog 	= new EVTLOG($user, "PATH", $act);

	$evtLog = new EVENTLOG($user, "MAINTENANCE", "PATH ADMINISTRATION", $act, $_POST);

	
    $pathObj = new PATHS();
	if ($pathObj->rslt == "fail") {
		$result["rslt"] = "fail";
		$result["reason"] = $pathObj->reason;
		$evtLog->log($result["rslt"], $result["reason"]);
		echo json_encode($result);
        mysqli_close($db);
        $debugObj->close();
		return;
	}



	// Dispatch to Functions
	
	if ($act == "queryByNode") {
		$result = queryPathByNode($pathObj, $node, $slot);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
    }
    
	if ($act == "queryByFac") {
		$result = queryPathByFac($pathObj, $fac);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
    }
    
	if ($act == "queryByPort") {
		$result = queryPathByPort($pathObj, $port);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
    }
    
	if ($act == "query") {
		$result = queryPathsByCkid($pathObj, $ckid);
		echo json_encode($result);
		mysqli_close($db);
        $debugObj->close();
		return;
    }
    else {
        $result["rslt"] = "fail";
		$result["reason"] = "Invalid action!";
		$evtLog->log($result["rslt"], $result["reason"]);
		echo json_encode($result);
        mysqli_close($db);
        $debugObj->close();
        return; 
    }

	function queryPathByPort($pathObj, $port) {
		
		$portObj = new PORT();
		$portObj->loadPort($port);
		if (count($portObj->rows) == 0) {
			$result["rslt"]   = "fail";
			$result["reason"] = "PORT $port DOES NOT EXIST";
			$result["rows"]   = [];
			return $result;

		}

		if ($portObj->cktcon == 0) {
			$result["rslt"]   = "fail";
			$result["reason"] = "NO PATH FOUND";
			$result["rows"]   = [];
			return $result;

		}

		$cktconObj = new CKTCON($portObj->cktcon);
		$cktconObj->loadIdx($portObj->con_idx);

		$pathObj->queryPathById($cktconObj->path);
        $result["rslt"]   = $pathObj->rslt;
        $result["reason"] = $pathObj->reason;
        $result["rows"]   = $pathObj->rows;
        return $result;
	}


	function queryPathByFac($pathObj, $fac) {
		
		$facObj = new FAC($fac);
		if (count($facObj->rows) == 0) {
			$result["rslt"]   = "fail";
			$result["reason"] = "FACILITY $fac DOES NOT EXIST";
			$result["rows"]   = [];
			return $result;
		}

		if ($facObj->port_id == 0) {
			$result["rslt"]   = "fail";
			$result["reason"] = "NO PATH FOUND";
			$result["rows"]   = [];
			return $result;
		}
		
		if ($facObj->portObj->cktcon == 0) {
			$result["rslt"]   = "fail";
			$result["reason"] = "NO PATH FOUND";
			$result["rows"]   = [];
			return $result;

		}

		$cktconObj = new CKTCON($facObj->portObj->cktcon);
		$cktconObj->loadIdx($facObj->portObj->con_idx);

		$pathObj->queryPathById($cktconObj->path);
        $result["rslt"]   = $pathObj->rslt;
        $result["reason"] = $pathObj->reason;
        $result["rows"]   = $pathObj->rows;
        return $result;
	}
	
	function queryPathsByCkid($pathObj, $ckid) {
		
		if ($ckid !== '') {
			$cktObj = new CKT($ckid);
			if (count($cktObj->rows) == 0) {
				$result["rslt"]   = 'fail';
				$result["reason"] = "CKID ($ckid) DOES NOT EXIST";
				$result["rows"]   = [];
				return $result;
			}
		}
		
		$pathObj->queryPathsByCkid($ckid);
        $result["rslt"]   = $pathObj->rslt;
        $result["reason"] = $pathObj->reason;
        $result["rows"]   = $pathObj->rows;
        return $result;
	}
	
	function queryPathByNode($pathObj, $node, $slot) {
		
		if ($node == "") {
			$result["rslt"]   = 'fail';
			$result["reason"] = "MISSING NODE";
			$result["rows"]   = [];
			return $result;
		}
		$node = $node -1;
		$pathObj->queryPathByNode($node, $slot);
        $result["rslt"]   = $pathObj->rslt;
        $result["reason"] = $pathObj->reason;
        $result["rows"]   = $pathObj->rows;
        return $result;
	}



?>
