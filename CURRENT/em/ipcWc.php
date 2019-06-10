<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: coQueryMatrix.php
 * Change history: 
 * 2018-11-01: created (Thanh)
 */

	/* Initialize expected inputs */
	$act= "";
	if (isset($_POST['act']))
        $act = $_POST['act'];

	$id= "";
	if (isset($_POST['id']))
		$id = strtoupper($_POST['id']);

	$wcname= "";
	if (isset($_POST['wcname']))
		$wcname = strtoupper($_POST['wcname']);

	$wcc= "";
	if (isset($_POST['wcc']))
	    $wcc = strtoupper($_POST['wcc']);

	$clli= "";
	if (isset($_POST['clli']))
		$clli = strtoupper($_POST['clli']);

	$npanxx= "";
	if (isset($_POST['npanxx']))
		$npanxx = strtoupper($_POST['npanxx']);

	$frloc= "";
	if (isset($_POST['frloc']))
		$frloc = strtoupper($_POST['frloc']);

    $tzone= "";
	if (isset($_POST['tzone']))
	    $tzone = strtoupper($_POST['tzone']);

	$stat= "";
	if (isset($_POST['stat']))
		$stat = strtoupper($_POST['stat']);

	$addr= "";
	if (isset($_POST['addr']))
		$addr = strtoupper($_POST['addr']);

	$city= "";
	if (isset($_POST['city']))
		$city = strtoupper($_POST['city']);
		
	$state= "";
	if (isset($_POST['state']))
		$state = strtoupper($_POST['state']);
		
	$zip= "";
	if (isset($_POST['zip']))
		$zip = strtoupper($_POST['zip']);

	$gps= "";
	if (isset($_POST['gps']))
		$gps = strtoupper($_POST['gps']);

	$company= "";
	if (isset($_POST['company']))
		$company = strtoupper($_POST['company']);
		
	$region= "";
	if (isset($_POST['region']))
		$region = strtoupper($_POST['region']);

	$area= "";
	if (isset($_POST['area']))
		$area = strtoupper($_POST['area']);

	$district= "";
	if (isset($_POST['district']))
		$district = strtoupper($_POST['district']);

	$manager= "";
	if (isset($_POST['manager']))
		$manager = strtoupper($_POST['manager']);

	$ipadr = "";
	if (isset($_POST['ipadr']))
		$ipadr = strtoupper($_POST['ipadr']);	

	$gateway = "";
	if (isset($_POST['gateway']))
		$gateway = strtoupper($_POST['gateway']);

	$netmask = "";
	if (isset($_POST['netmask']))
		$netmask = strtoupper($_POST['netmask']);

	$iport = "";
	if (isset($_POST['iport']))
		$iport = strtoupper($_POST['iport']);
		
	$uname = "";
	if (isset($_POST['uname']))
		$uname = $_POST['uname'];


    $evtLog = new EVENTLOG($user, "CONFIGURATION", "SETUP WIRE CENTER", $act, '');


    /**
     * Create WC object from class
     */
    $wcObj = new WC();
    if ($wcObj->rslt == "fail") {
        $result["rslt"] = "fail";
        $result["reason"] = $wcObj->reason;
		$evtLog->log($result["rslt"], $result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
        return;
    }

    /* Dispatch to functions */
	if ($act  == "query") {
		$wcObj->queryWc();
		$result["rslt"] = $wcObj->rslt;
        $result["reason"] = $wcObj->reason;
        $result["rows"] = $wcObj->rows;
        echo json_encode($result);
		mysqli_close($db);
		return;
	}
	
	if ($act  ==  "update" || $act == "UPDATE") {
		$result = updateWc($wcname, $wcc, $clli, $npanxx, $frloc, $tzone, $stat, $addr, $city, $state, $zip, $gps, $company, $region, $area, $district, $manager, $id, $userObj, $wcObj);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}

	// UPDATE NETWORK
	if ($act  ==  "update_network" || $act == "UPDATE_NETWORK") {
		$result = updateNetwork($ipadr, $gateway, $netmask, $iport, $id, $userObj, $wcObj);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}
    
    if ($act  ==  "turn_up" || $act == "TURN_UP" ) {
		$result = turnup($id, $userObj, $wcObj, $wcname, $wcc, $clli, $npanxx, $frloc);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}

    if ($act  ==  "hold" || $act == "HOLD" ) {
		$result = holdWc($userObj, $wcObj, $wcname, $wcc, $clli, $npanxx, $frloc);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}

	if ($act  ==  "reset" || $act == "RESET" ) {
		$result = resetWc($id, $userObj, $wcObj);
		$evtLog->log($result["rslt"], $result['log'] . " | " . $result["reason"]);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}

	if ($act == "getWCHeader") {
		$result = getWCHeader($wcObj);
        echo json_encode($result);
		mysqli_close($db);
		return;
	}

	if ($act == "getWCTimeZone") {
		$result = getWCTimeZone($wcObj);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}

	if ($act == "getAlmStat") {
		$result = getAlmStat();
		echo json_encode($result);
		mysqli_close($db);
		return;
	}	
	
	if ($act == "getHeader") {
		$result = getHeader($wcObj, $uname);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	else {
 		$result["rslt"] = "fail";
		$result["reason"] = "ACTION " . $act . " is under development or not supported";
		$evtLog->log($result["rslt"],$result["reason"]);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}
	
	// API Functions

	function getHeader($wcObj, $uname) {

		// establish ipc_time
		date_default_timezone_set("UTC");
		$utc_tz = date_default_timezone_get();
		$utc_t = time();
		$ipc_tz_offset = $wcObj->tz * 3600;
		$ipc_t = $utc_t + ($wcObj->tz * 3600);

		$secondSundayInMarch = date("d-M-Y", strtotime("second sunday " . date('Y') . "-03"));
		$firstSundayInNovember = date("d-M-Y", strtotime("first sunday " . date('Y') . "-11"));
		$dst_begin_t = strtotime($secondSundayInMarch) + $ipc_tz_offset;
		$dst_end_t = strtotime($firstSundayInNovember) + $ipc_tz_offset;

		$ipc_tzone = $wcObj->tzone;
		if ($ipc_t > $dst_begin_t && $ipc_t < $dst_end_t) {
			if (date('I', $ipc_t) == 0) {
				$ipc_t = $ipc_t + 3600;
				$ipc_tzone = substr_replace($wcObj->tzone,"DT",-2);
			}
		}
		$ipc_time = date("Y-m-d H:i:s", $ipc_t);

		// get date_format
		$refObj = new REF();
		$date_format = $refObj->ref[0]['date_format'];

		$rows = [];
		$row['wcname'] = $wcObj->wcname;
		$row['wcc'] = $wcObj->wcc;
		$row['npanxx'] = $wcObj->npanxx;
		$row['frmid'] = $wcObj->frloc;
		$row['ipcstat'] = $wcObj->stat;
		$row['tzone'] = $ipc_tzone;
		$row['time']= $ipc_time;
		$row['date_format'] = $date_format;
		$row['nodes'] = $wcObj->nodes;
		$almObj = new ALMS();
		$almObj->queryalm();
		if (count($almObj->rows) == 0) {
			$row['sev'] = "NONE";
		}
		else {
			$row['sev'] = $almObj->rows[0]['sev'];
		}
		
		$unameObj = new USERS($uname);
		if ($unameObj->rslt == 'success') {
			$row['loginTime'] = $unameObj->login;
		}
		else {
			$row['loginTime'] = '';
		}
		
		$row['node_info'] = array();

		for ($k = 0; $k < $wcObj->nodes; $k++) {
			
			$node = $k + 1;
			$nodeObj = new NODE($node);

			$mxcObj = new MXC();
			$mxcObj->queryByNode($node);
			
			$noderows = $mxcObj->rows;

			$nodeinfo = [
				"node" => "$node",
				"node_alm" => '',
				"node_stat" => $nodeObj->psta,
				"node_volt" => $nodeObj->volt,
				"node_temp" => $nodeObj->temp,
				"node_rack" => $nodeObj->rack,
				"MIOX" => [],
				"MIOY" => []
			];

			$almObj->queryAlmByNode($node);
			if (count($almObj->rows) == 0) {
				$nodeinfo['node_alm'] = "NONE";
			}
			else {
				$nodeinfo['node_alm'] = $almObj->rows[0]['sev'];
			}

			$miox_rows = array_filter($noderows, function($v) {
				if ($v['type'] === 'MIOX') {
					return true;
				} else {
					return false;
				}
			});
			$mioy_rows = array_filter($noderows, function($v) {
				if ($v['type'] === 'MIOY') {
					return true;
				} else {
					return false;
				}
			});

			usort($miox_rows, function($a, $b) {
				if ($a['slot'] === $b['slot']) {
					return 0;
				} else if ($a['slot'] > $b['slot']) {
					return 1;
				} else {
					return -1;
				}
			});

			usort($mioy_rows, function($a, $b) {
				if ($a['slot'] === $b['slot']) {
					return 0;
				} else if ($a['slot'] > $b['slot']) {
					return 1;
				} else {
					return -1;
				}
			});

			$miox = array_column($miox_rows, 'psta');
			$mioy = array_column($mioy_rows, 'psta');

			$nodeinfo['MIOX'] = $miox;
			$nodeinfo['MIOY'] = $mioy;

			array_push($row['node_info'], $nodeinfo);
		}

		$rows[] = $row;

		$result["rows"] = $rows;
		$result["rslt"] = "success";
		$result["reason"] = 'QUERY_HEADER';
		return $result;
	}

	function getAlmStat() {
		$almObj = new ALMS();
		$almObj->queryalm();

		$result["rslt"] = $almObj->rslt;
        $result["reason"] = $almObj->reason;
		$result["rows"] = $almObj->rows;
		return $result;
	}

	function getWCTimeZone($wcObj) {
		$time = new DateTime("now", new DateTimeZone($wcObj->tzone));
		$time = $time->format('Y-m-d\ H:i');

		$rows = [];
		$row['ipcstat'] = $wcObj->stat;
		$row['tzone'] = $wcObj->tzone;
		$row['time']= $time;
		$rows[] = $row;

		$result["rows"] = $rows;
		$result["rslt"] = "Success";
		$result["reason"] = 'QUERY_TIME_ZONE';
		return $result;
	}

	function getWCHeader($wcObj) {
		$result["rslt"] = $wcObj->rslt;
        $result["reason"] = $wcObj->reason;
		$result["rows"] = $wcObj->rows[1];
		return $result;
	}
 
	function updateWc($wcname, $wcc, $clli, $npanxx, $frloc, $tzone, $stat, $addr, $city, $state, $zip, $gps, $company, $region, $area, $district, $manager, $id, $userObj, $wcObj){

		if ($userObj->grpObj->setwc != "Y") {
			$result['rslt'] = 'fail';
			$result['reason'] = 'PERMISSION DENIED';
			return $result;
		}

		// validate mandatory data, and log only data need update
		$result['log'] = "ACTION = UPDATE";

		if ($wcname == '') {
			$result['reason'] .= " | MISSING WCNAME";
			$result['rslt'] = 'fail';
			$result['rows'] = [];
			return $result;
		}
		else if ($wcname != $wcObj->wcname) {
			$result['log'] .= " | WCNAME =" . $wcObj->wcname . " -> " . $wcname;
		}

		if ($wcc == '') {
			$result['reason'] .= " | MISSING WCC";
			$result['rslt'] = 'fail';
			$result['rows'] = [];
			return $result;
		}
		else if ($wcc != $wcObj->wcc) {
			$result['log'] .= " | WCC=" . $wcObj->wcc . " -> " . $wcc;
		}

		if ($clli == '') {
			$result['reason'] .= " | MISSING CLLI";
			$result['rslt'] = 'fail';
			$result['rows'] = [];
			return $result;
		}
		else if ($clli != $wcObj->clli) {
			$result['log'] .= " | CLLI =" . $wcObj->clli . " -> " . $clli;
		}

		if ($npanxx == '') {
			$result['reason'] .= " | MISSING NPANXX";
			$result['rslt'] = 'fail';
			$result['rows'] = [];
			return $result;
		}
		else if ($npanxx != $wcObj->npanxx) {
			$result['log'] .= " | NPANXX =" . $wcObj->npanxx . " -> " . $npanxx;
		}

		if ($frloc == '') {
			$result['reason'] .= " | MISSING FRM_ID";
			$result['rslt'] = 'fail';
			$result['rows'] = [];
			return $result;
		}
		else if ($frloc != $wcObj->frloc) {
			$result['log'] .= " | FRM_ID =" . $wcObj->frloc . " -> " . $frloc;
		}

		if ($tzone == '') {
			$result['reason'] .= " | MISSING TZONE";
			$result['rslt'] = 'fail';
			$result['rows'] = [];
			return $result;
		}
		else if ($tzone != $wcObj->tzone) {
			$result['log'] .= " | TZONE =" . $wcObj->tzone . " -> " . $tzone;
		}

		if ($addr != $wcObj->addr) {
			$result['log'] .= " | ADDR =" . $wcObj->addr . " -> " . $addr;
		}

		if ($city != $wcObj->city) {
			$result['log'] .= " | CITY =" . $wcObj->city . " -> " . $city;
		}

		if ($state != $wcObj->state) {
			$result['log'] .= " | STATE =" . $wcObj->state . " -> " . $state;
		}

		if ($zip != $wcObj->zip) {
			$result['log'] .= " | ZIP =" . $wcObj->zip . " -> " . $zip;
		}

		if ($gps != $wcObj->gps) {
			$result['log'] .= " | GPS =" . $wcObj->gps . " -> " . $gps;
		}

		if ($company != $wcObj->company) {
			$result['log'] .= " | COMPANY =" . $wcObj->company . " -> " . $company;
		}

		if ($region != $wcObj->region) {
			$result['log'] .= " | REGION =" . $wcObj->region . " -> " . $region;
		}

		if ($area != $wcObj->area) {
			$result['log'] .= " | AREA =" . $wcObj->area . " -> " . $area;
		}

		if ($district != $wcObj->district) {
			$result['log'] .= " | DISTRICT =" . $wcObj->district . " -> " . $district;
		}

		else if ($manager != $wcObj->manager) {
			$result['log'] .= " | MANAGER =" . $wcObj->manager . " -> " . $manager;
		}
		
		$wcObj->updateWc($wcname, $wcc, $clli, $npanxx, $frloc, $tzone, $stat, $addr, $city, $state, $zip, $gps, $company, $region, $area, $district, $manager, $id);
		if ($wcObj->rslt == 'fail') {
			$result["rslt"] = $wcObj->rslt;
        	$result["reason"] .= $wcObj->reason;
			$result["rows"] = $wcObj->rows;
			return $result;
		}
		$result["rslt"] = $wcObj->rslt;
        $result["reason"] .= "WC_UPDATE_SUCCESS";
		$result["rows"] = $wcObj->rows;
		return $result;
	}

	function updateNetwork($ipadr, $gateway, $netmask, $iport, $id, $userObj, $wcObj){

		$result['log'] = "ACTION=UPDATE_NETWORK";
		if ($ipadr != $wcObj->ipadr)
			$result['log'] .= " | IP_ADDR=" . $wcObj->ipadr . " --> " . $ipadr;
		
		if ($gateway != $wcObj->gateway)
			$result['log'] .= " | GATEWAY=" . $wcObj->gateway . " --> " . $gateway;

		if ($netmask != $wcObj->netmask)
			$result['log'] .= " | NETMASK=" . $wcObj->netmask . " --> " . $netmask;

		if ($iport != $wcObj->iport)
			$result['log'] .= " | IP_PORT=" . $wcObj->iport . " --> " . $iport;

		if ($userObj->grpObj->setwc != "Y") {
			$result['rslt'] = 'fail';
			$result['reason'] = 'PERMISSION DENIED';
			return $result;
		}

		$wcObj->updateNetwork( $ipadr, $gateway, $netmask, $iport, $id);
		if($wcObj->rslt == FAIL) {
			$result["rslt"] = $wcObj->rslt;
        	$result["reason"] = $wcObj->reason;
			$result["rows"] = $wcObj->rows;
			return $result;
		}
		$result["rslt"] = $wcObj->rslt;
        $result["reason"] = "WC_UPDATE_SUCCESS";
		$result["rows"] = $wcObj->rows;
		return $result;
	}

	function resetWc($id, $userObj, $wcObj) {
		
		$result['log'] = "ACTION = RESET";

		if ($userObj->ugrp != 'ADMIN') {
			$result['rslt'] = 'fail';
			$result['reason'] = 'PERMISSION DENIED';
			$result['rows'] = [];
			
		}
		$wcObj->resetWc($id);
		if($wcObj->rslt == FAIL) {
			$result["rslt"] = $wcObj->rslt;
        	$result["reason"] = $wcObj->reason;
			$result["rows"] = $wcObj->rows;
			return $result;
		}
		$result["rslt"] = $wcObj->rslt;
        $result["reason"] = "WC_RESET_SUCCESS";
		$result["rows"] = $wcObj->rows;
		return $result;

	}
	
	function turnup($id, $userObj, $wcObj, $wcname, $wcc, $clli, $npanxx, $frloc) {

		$result['log'] = "ACTION = TURN_UP | WCNAME = $wcname | WCC = $wcc | CLLI = $clli | NPANXX = $npanxx | FRLOC = $frloc";

		if ($userObj->ugrp != 'ADMIN') {
			$result['rslt'] = 'fail';
			$result['reason'] = 'PERMISSION DENIED';
			$result['rows'] = [];
			return $result;
		}

		$wcObj->turnup($wcname, $wcc, $clli, $npanxx, $frloc);
		if($wcObj->rslt == FAIL) {
			$result["rslt"] = $wcObj->rslt;
        	$result["reason"] = $wcObj->reason;
			$result["rows"] = $wcObj->rows;
			return $result;
		}

		$wcObj->queryWc();
		$result["rslt"] = $wcObj->rslt;
        $result["reason"] = "WC TURN UP";
		$result["rows"] = $wcObj->rows;
		return $result;

	}

	function holdWc($userObj, $wcObj, $wcname, $wcc, $clli, $npanxx, $frloc) {

		$result['log'] = "ACTION = HOLD | WCNAME = $wcname | WCC = $wcc | CLLI = $clli | NPANXX = $npanxx | FRLOC = $frloc";

		if ($userObj->ugrp != 'ADMIN') {
			$result['rslt'] = 'fail';
			$result['reason'] = 'PERMISSION DENIED';
			$result['rows'] = [];
			return $result;
		}

		$wcObj->holdWc($wcname, $wcc, $clli, $npanxx, $frloc);
		if($wcObj->rslt == FAIL) {
			$result["rslt"] = $wcObj->rslt;
        	$result["reason"] = $wcObj->reason;
			$result["rows"] = $wcObj->rows;
			return $result;
		}
		$result["rslt"] = $wcObj->rslt;
        $result["reason"] = "HOLD WC";
		$result["rows"] = $wcObj->rows;
		return $result;

	}
				

?>
