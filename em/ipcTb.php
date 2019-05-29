<?php
/*
* Copy Right @ 2018
* BHD Solutions, LLC.
* Project: CO-IPC
* Filename: ipcTb.php
* Change history: 
* 03-26-2019: created (Thanh)
*/	
	
//Initialize expected inputs

$act = "";
if (isset($_POST['act'])) {
	$act = $_POST['act'];
}

$node = "";
if (isset($_POST['node'])) {
	$node = strtoupper($_POST['node']);
}	
$node = $node -1;

$tp = "";
if (isset($_POST['tp'])) {
	$tp = strtoupper($_POST['tp']);
}
$tp = $tp -1;

$name = "";
if (isset($_POST['name'])) {
	$name = strtoupper($_POST['name']);
}

$tbx = "";
if (isset($_POST['tbx'])) {
	$tbx = $_POST['tbx'];
}	

$tby = "";
if (isset($_POST['tby'])) {
	$tby = $_POST['tby'];
}	


$port = "";
if (isset($_POST['port'])) {
	$port = strtoupper($_POST['port']);
}	



$evtLog = new EVENTLOG($user, "CONFIGURATION", "SETUP TEST ACCESS", $act, '');

$tbObj = new TB();
    
//Dispatch to functions
if ($act == "query") {
    $tbObj->query($node);
	$result["rslt"]   = $tbObj->rslt;
	$result["reason"] = $tbObj->reason;
	$result["rows"]   = $tbObj->rows;
	echo json_encode($result);
	mysqli_close($db);
	$debugObj->close();
	return;
}

if ($act == "UPDATE") {
	$result = setTpName($tp, $node, $name, $userObj);
	echo json_encode($result);
	mysqli_close($db);
	$debugObj->close();
	return;
}

if ($act == "CONNECT") {
	$result = connectTb($tp, $node,$port, $tbx, $tby, $userObj);
	echo json_encode($result);
	mysqli_close($db);
	$debugObj->close();
	return;
}

if ($act == "DISCONNECT") {
	$result = disconnectTb($tp, $node,$port, $tbx, $tby, $userObj);
	echo json_encode($result);
	mysqli_close($db);
	$debugObj->close();
	return;
}

if ($act == "DSP-50ms single tone") {
	$result = dsp50msST($tp, $node,$port, $tbx, $tby, $userObj);
	echo json_encode($result);
	mysqli_close($db);
	$debugObj->close();
	return;
}

if ($act == "DSP-50ms dual tone") {
	$result = dsp50msDT($tp, $node,$port, $tbx, $tby, $userObj);
	echo json_encode($result);
	mysqli_close($db);
	$debugObj->close();
	return;
}

// if ($act == "DSP-forever single tone") {
// 	$result = dspFST($tp, $node,$port, $tbx, $tby, $userObj);
// 	echo json_encode($result);
// 	mysqli_close($db);
	// $debugObj->close();
// 	return;
// }

// if ($act == "DSP-forever dual tone") {
// 	$result = dspFDT($tp, $node,$port, $tbx, $tby, $userObj);
// 	echo json_encode($result);
// 	mysqli_close($db);
	// $debugObj->close();
// 	return;
// }

if ($act == "DSP-stop single/dual tone") {
	$result = dspStopTone($tp, $node,$port, $tbx, $tby, $userObj);
	echo json_encode($result);
	mysqli_close($db);
	$debugObj->close();
	return;
}

else {
	$result["rslt"] = 'fail';
	$result["reason"] = "This action is under development!";
	$evtLog->log($result["rslt"], $result["reason"]);
	echo json_encode($result);
	mysqli_close($db);
	$debugObj->close();
	return;
}

// FUNCTIONS

function setTpName($tp, $node, $name, $userObj) {

	// CHECK USER PERMISSIONS
	if ($userObj->grpObj->maint != "Y") {
		$result['rslt'] = 'fail';
		$result['reason'] = 'Permission Denied';
		return $result;
	}
	// deny update if NAME is left blank
	if ($name == "") {
		$result['rslt'] = 'fail';
		$result['reason'] = 'TEST EQUIPMENT cannot be left blank!';
		return $result;
	}

	if($tp === "" || $node === "") {
		$result['rslt'] = 'fail';
		$result['reason'] = 'MISSING TP AND NODE INFO';
		return $result;
	}

	$tbObj = new TB($node, $tp);
	if($tbObj->rslt == 'fail') {
		$result['rslt'] = 'fail';
		$result['reason'] = 'INVALID TP AND NODE';
		return $result;
	}
	
	$tbObj->setTpName($name);
	
	$result['rslt'] = $tbObj->rslt;
	$result['reason'] = $tbObj->reason;
	$result['rows'] = $tbObj->rows;

	return $result;
}


function connectTb($tp, $node, $port, $tbx, $tby, $userObj) {	
	try{
		
		// ------------CHECK USER PERMISSIONS---------------//
		if ($userObj->grpObj->maint != "Y") {
			throw new Exception('TEST BUS - Permission Denied', 50);
		}

		//-----------------CHECK THE INPUT DATA------------------//
		if($tp === "" || $node === "") {
			throw new Exception('TEST BUS - MISSING TP OR NODE INFO', 50);
		}

		if($port === "") {
			throw new Exception('TEST BUS - MISSING PORT INFO', 50);
		}

		$tbObj = new TB($node, $tp);
		if($tbObj->rslt == 'fail') {
			throw new Exception('TEST BUS - INVALID TP AND NODE', 50);
		}

		//-------------------CHECK T_TB-------------------------///
		if(($tbx == 'y' && $tby == 'y') || ($tbx == 'n' && $tby == 'n')) {
			throw new Exception('TEST BUS - WRONG TB_X AND TB_Y CONFIGURATION', 50);
		}

		if($tbx == 'y')
			$tb = 'x';
		if($tby == 'y')
			$tb = 'y';

		if($tbObj->getStatus($tb) !== true) {
			throw new Exception("$tbObj->reason", 50);
		}

		//---------------- CHECK THE PORT -----------------//
		$portObj = new PORT();
		$portObj->loadPort($port);
		if($portObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $portObj->reason", 50);
        }

		//--------------CHECK PORT NDDE == $NODE-----------------------//
		// have to decrease $portObj->node by 1, because node in t_tb uses base 0
		if(($portObj->node -1) != $node) {
			throw new Exception("TEST BUS - PORT $port IS NOT IN NODE ".($node+1), 50);
		}
		
        //--------------CHECK PORT TYPE VS TEST BUS -------------------//
        if($portObj->ptyp != strtoupper($tb)) {
			throw new Exception("TEST BUS - PORT TYPE AND TEST BUS TYPE MUST BE THE SAME", 50);
        }
		
		
		//--------------------CHECK t_X/Y TABLE--------------//
        $portExtract = explode('-', $port);
        $portConvert = ($portExtract[0]-1).".".$portExtract[2].".".($portExtract[1]-1).".".($portExtract[3]-1);
        if($portObj->ptyp == 'X')
            $portTypeObj = new X($portConvert);
        else
            $portTypeObj = new Y($portConvert);

        if($portTypeObj->rslt == 'fail') {
			throw new Exception("TEST BUS - ".$portTypeObj->reason, 50);
        }
        
        if($portTypeObj->checkTestConnStatus($tbObj->node) === false) {
			throw new Exception("TEST BUS - $portTypeObj->reason IS ALREADY CONNECTED TO TEST BUS", 50);
		}
		
		//--------------------UP TO THIS POITN: ALL CHECKINGS ARE GOOD
		//------------------ CREATE CMD-------------------//
		//-------------GET ROW/COL INFO------------------//
		if($tb == 'x') {
            $row = $tbObj->tbx_row;
            $col = $tbObj->tbx_col;
        }
        else {
            $row = $tbObj->tby_row;
            $col = $tbObj->tby_col;
        }

        //----------------create new cmd and insert into t_cmdque------------//
		$cmdObj = new CMD();
		//-------------CMD FOR TEST PORT--------------//
        $cmdObj->createTestPathCmd('close', $tbObj->name, $tbObj->node, $row, $col);
        if($cmdObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $cmdObj->reason", 50);
		}
		//------------CMD FOR PORT----------------//
        $cmdObj->createTestPathCmd('close', $portObj->id, $tbObj->node, $portTypeObj->tb_row, $portTypeObj->tb_col);
        if($cmdObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $cmdObj->reason", 50);
		}
		
		///////-----------------UPDATE DATABASE-------------///////

		if($portTypeObj->updateTestConn(1) == false) {
            throw new Exception("TEST BUS - $portTypeObj->reason", 50);
		}
		
		if($tbObj->connectTp($tb) == false) {
			throw new Exception("TEST BUS - ".$tbObj->reason." - UNABLE TO CONNECT TEST PORT TO TEST BUS", 50);
		}

		
		if($tbObj->connectPort($port) == false) {
			throw new Exception("TEST BUS - ".$tbObj->reason."- UNABLE TO CONNECT PORT TO TEST BUS", 50);
		}
	
		$tbObj->query($node);
		$result['rows'] = $tbObj->rows;
		$result['rslt'] = 'success';
		$result['reason'] = "TEST CONNECTION IS CREATED";
		return $result;

	}
	catch (Exception $e) {
		if($e->getCode() == 50) {
			$result['rslt'] = 'fail';
			$result['reason'] = $e->getMessage();
			return $result;
		}
	}
}


function disconnectTb($tp, $node,$port, $tbx, $tby, $userObj) {
	try{
		// ------------CHECK USER PERMISSIONS---------------//
		if ($userObj->grpObj->maint != "Y") {
			throw new Exception('TEST BUS - Permission Denied', 50);
		}

		//--------------CHECK INPUT DATA-----------------//
		if($tp === "" || $node === "") {
			throw new Exception('TEST BUS - MISSING TP AND NODE INFO', 50);
		}

		if($port === "") {
			throw new Exception('TEST BUS - MISSING PORT INFO', 50);
		}

		$tbObj = new TB($node, $tp);
		if($tbObj->rslt == 'fail') {
			throw new Exception('TEST BUS - INVALID TP AND NODE', 50);
		}

		if(($tbx == 'y' && $tby == 'y') || ($tbx == 'n' && $tby == 'n')) {
			throw new Exception('TEST BUS - WRONG TB_X AND TB_Y CONFIGURATION', 50);
		}

		if($tbx == 'y')
			$tb = 'x';
		if($tby == 'y')
			$tb = 'y';

		///--------------------CHECK T_TB----------------------///
		if($tb == 'x') {
			if($tbObj->tb_x != 1) {
				throw new Exception("TEST BUS - THERE IS NO CONNECTION FROM THIS TEST PORT TO TEST BUS X", 50);
				
			}			
		}
		else {
			if($tbObj->tb_y != 1) {
				throw new Exception("TEST BUS - THERE IS NO CONNECTION FROM THIS TEST PORT TO TEST BUS Y", 50);
			}
		}

		///////------------------CHECK THE PORT INFO IN T_TB---------------------//
		if($tbObj->port != $port) {
			throw new Exception("TEST BUS - $port IS NOT CONNECTED TO THIS TEST BUS", 50);
            
        }
        //---------------- CHECK THE PORT -----------------//
		$portObj = new PORT();
		$portObj->loadPort($port);
		if($portObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $portObj->reason", 50);
            
        }

		//--------------CHECK PORT NDDE == $NODE-----------------------//
		// have to decrease $portObj->node by 1, because node in t_tb uses base 0
		if(($portObj->node -1) != $node) {
			throw new Exception("TEST BUS - PORT $port IS NOT IN NODE ".($node+1), 50);
		}

        //check the port type is the same with test bus
        if($portObj->ptyp != strtoupper($tb)) {
			throw new Exception("TEST BUS - PORT TYPE AND TEST BUS TYPE MUST BE THE SAME", 50);
        }
		
		//-------------------CHECK T_X/Y--------------------//
        $portExtract = explode('-', $port);
        $portConvert = ($portExtract[0]-1).".".$portExtract[2].".".($portExtract[1]-1).".".($portExtract[3]-1);
        if($portObj->ptyp == 'X')
            $portTypeObj = new X($portConvert);
        else 
            $portTypeObj = new Y($portConvert);

        if($portTypeObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $portTypeObj->reason", 50);
            
        }
        
        //check in t_X or t_Y, there is a test connection exist
        if($portTypeObj->tb_conn != 1) {
			throw new Exception("TEST BUS - THERE IS NO TEST CONNECTION AT THIS PORT", 50);
            
		}
		

		//--------------------UP TO THIS POINT: ALL CHECKINGS ARE GOOD---------------------//
		//------------CREATE CMD---------------------//
		//-------Insert cmd to t_cmdque----------//
		//---------GET ROW/COL INFO----------//
		if($tb == 'x') {
            $row = $tbObj->tbx_row;
            $col = $tbObj->tbx_col;
        }
        else {
            $row = $tbObj->tby_row;
            $col = $tbObj->tby_col;
		}
		
		$cmdObj = new CMD();
		$cmdObj->createTestPathCmd('open', $tbObj->name, $tbObj->node, $row, $col);
		if($cmdObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $cmdObj->reason", 50);
		}

		$cmdObj->createTestPathCmd('open', $portObj->id, $tbObj->node, $portTypeObj->tb_row, $portTypeObj->tb_col);
        if($cmdObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $cmdObj->reason", 50);
		}
		
		//////-----------------BEGIN UPDATE TABLE IN DATABASE----------------------//
		
		if($portTypeObj->updateTestConn(0) == false) {
			throw new Exception("TEST BUS - $portTypeObj->reason", 50);
		}
		
		if($tbObj->disconnectTp() === false) {
			throw new Exception("TEST BUS - ".$tbObj->reason." - UNABLE TO CONNECT TEST PORT TO TEST BUS", 50);
		}

		if($tbObj->disconnectPort() === false) {
			throw new Exception("TEST BUS - ".$tbObj->reason."- UNABLE TO CONNECT PORT TO TEST BUS", 50);
		}

		$tbObj->query($node);
		$result['rows'] = $tbObj->rows;
		$result['rslt'] = 'success';
		$result['reason'] = "TEST CONNECTION IS DELETED";
		return $result;
	}
	catch (Exception $e) {
		if($e->getCode() == 50) {
			$result['rslt'] = 'fail';
			$result['reason'] = $e->getMessage();
			return $result;
		}
	}
}

function dsp50msST($tp, $node,$port, $tbx, $tby, $userObj) {
	try{

		// ------------CHECK USER PERMISSIONS---------------//
		if ($userObj->grpObj->maint != "Y") {
			throw new Exception('TEST BUS - Permission Denied', 50);
		}

		//--------------CHECK INPUT DATA-----------------//
		if($tp === "" || $node === "") {
			throw new Exception('TEST BUS - MISSING TP AND NODE INFO', 50);
		}

		if($port === "") {
			throw new Exception('TEST BUS - MISSING PORT INFO', 50);
		}

		$tbObj = new TB($node, $tp);
		if($tbObj->rslt == 'fail') {
			throw new Exception('TEST BUS - INVALID TP AND NODE', 50);
		}

		///--------------CHECK THE TEST PORT == DSP OR NOT----------------------///
		if(strpos($tbObj->name, 'DSP') === false) {
			throw new Exception('TEST BUS - INVALID TP AND NODE', 50);
		}

		if(($tbx == 'y' && $tby == 'y') || ($tbx == 'n' && $tby == 'n')) {
			throw new Exception('TEST BUS - WRONG TB_X AND TB_Y CONFIGURATION', 50);
		}

		if($tbx == 'y')
			$tb = 'x';
		if($tby == 'y')
			$tb = 'y';

		///--------------------CHECK T_TB----------------------///
		if($tb == 'x') {
			if($tbObj->tb_x != 1) {
				throw new Exception("TEST BUS - THERE IS NO CONNECTION FROM THIS TEST PORT TO TEST BUS X", 50);
				
			}
		}
		else {
			if($tbObj->tb_y != 1) {
				throw new Exception("TEST BUS - THERE IS NO CONNECTION FROM THIS TEST PORT TO TEST BUS Y", 50);
			}
		}
		///////------------------CHECK THE PORT INFO IN T_TB---------------------//
		if($tbObj->port != $port) {
			throw new Exception("TEST BUS - $port IS NOT CONNECTED TO THIS TEST BUS", 50);
            
        }
        //---------------- CHECK THE PORT -----------------//
		$portObj = new PORT();
		$portObj->loadPort($port);
		if($portObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $portObj->reason", 50);
            
        }

		//--------------CHECK PORT NDDE == $NODE-----------------------//
		// have to decrease $portObj->node by 1, because node in t_tb uses base 0
		if(($portObj->node -1) != $node) {
			throw new Exception("TEST BUS - PORT $port IS NOT IN NODE ".($node+1), 50);
		}

        //check the port type is the same with test bus
        if($portObj->ptyp != strtoupper($tb)) {
			throw new Exception("TEST BUS - PORT TYPE AND TEST BUS TYPE MUST BE THE SAME", 50);
        }
		
		//-------------------CHECK T_X/Y--------------------//
        $portExtract = explode('-', $port);
        $portConvert = ($portExtract[0]-1).".".$portExtract[2].".".($portExtract[1]-1).".".($portExtract[3]-1);
        if($portObj->ptyp == 'X')
            $portTypeObj = new X($portConvert);
        else 
            $portTypeObj = new Y($portConvert);

        if($portTypeObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $portTypeObj->reason", 50);
            
        }
        
        //check in t_X or t_Y, there is a test connection exist
        if($portTypeObj->tb_conn != 1) {
			throw new Exception("TEST BUS - THERE IS NO TEST CONNECTION AT THIS PORT", 50);
            
		}
		//--------------------UP TO THIS POINT: ALL CHECKINGS ARE GOOD---------------------//
		//------------CREATE CMD---------------------//
		//-------Insert cmd to t_cmdque----------//

		$dspObj = new DSP();
		if($dspObj->generate_50msSingleTone($node, $tbObj->name) ===false) {
			throw new Exception("TEST BUS - ".$dspObj->reason, 50);
		}
		
		//////-----------------BEGIN UPDATE TABLE IN DATABASE----------------------////
		$tbObj->query($node);
		$result['rows'] = $tbObj->rows;
		$result['rslt'] = 'success';
		$result['reason'] = "50ms SINGLE TONE IS GENERATED";
		return $result;
	}
	catch (Exception $e) {
		if($e->getCode() == 50) {
			$result['rslt'] = 'fail';
			$result['reason'] = $e->getMessage();
			return $result;
		}
	}
}

function dsp50msDT($tp, $node,$port, $tbx, $tby, $userObj) {
	try{
		// ------------CHECK USER PERMISSIONS---------------//
		if ($userObj->grpObj->maint != "Y") {
			throw new Exception('TEST BUS - Permission Denied', 50);
		}

		//--------------CHECK INPUT DATA-----------------//
		if($tp === "" || $node === "") {
			throw new Exception('TEST BUS - MISSING TP AND NODE INFO', 50);
		}

		if($port === "") {
			throw new Exception('TEST BUS - MISSING PORT INFO', 50);
		}

		$tbObj = new TB($node, $tp);
		if($tbObj->rslt == 'fail') {
			throw new Exception('TEST BUS - INVALID TP AND NODE', 50);
		}

		///--------------CHECK THE TEST PORT == DSP OR NOT----------------------///
		if(strpos($tbObj->name, 'DSP') === false) {
			throw new Exception('TEST BUS - INVALID TP AND NODE', 50);
		}

		if(($tbx == 'y' && $tby == 'y') || ($tbx == 'n' && $tby == 'n')) {
			throw new Exception('TEST BUS - WRONG TB_X AND TB_Y CONFIGURATION', 50);
		}

		if($tbx == 'y')
			$tb = 'x';
		if($tby == 'y')
			$tb = 'y';

		///--------------------CHECK T_TB----------------------///
		if($tb == 'x') {
			if($tbObj->tb_x != 1) {
				throw new Exception("TEST BUS - THERE IS NO CONNECTION FROM THIS TEST PORT TO TEST BUS X", 50);
				
			}
		}
		else {
			if($tbObj->tb_y != 1) {
				throw new Exception("TEST BUS - THERE IS NO CONNECTION FROM THIS TEST PORT TO TEST BUS Y", 50);
			}
		}

		///////------------------CHECK THE PORT INFO IN T_TB---------------------//
		if($tbObj->port != $port) {
			throw new Exception("TEST BUS - $port IS NOT CONNECTED TO THIS TEST BUS", 50);
            
        }
        //---------------- CHECK THE PORT -----------------//
		$portObj = new PORT();
		$portObj->loadPort($port);
		if($portObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $portObj->reason", 50);
            
        }

		//--------------CHECK PORT NDDE == $NODE-----------------------//
		// have to decrease $portObj->node by 1, because node in t_tb uses base 0
		if(($portObj->node -1) != $node) {
			throw new Exception("TEST BUS - PORT $port IS NOT IN NODE ".($node+1), 50);
		}

        //check the port type is the same with test bus
        if($portObj->ptyp != strtoupper($tb)) {
			throw new Exception("TEST BUS - PORT TYPE AND TEST BUS TYPE MUST BE THE SAME", 50);
        }
		
		//-------------------CHECK T_X/Y--------------------//
        $portExtract = explode('-', $port);
        $portConvert = ($portExtract[0]-1).".".$portExtract[2].".".($portExtract[1]-1).".".($portExtract[3]-1);
        if($portObj->ptyp == 'X')
            $portTypeObj = new X($portConvert);
        else 
            $portTypeObj = new Y($portConvert);

        if($portTypeObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $portTypeObj->reason", 50);
            
        }
        
        //check in t_X or t_Y, there is a test connection exist
        if($portTypeObj->tb_conn != 1) {
			throw new Exception("TEST BUS - THERE IS NO TEST CONNECTION AT THIS PORT", 50);           
		}
		
		//--------------------UP TO THIS POINT: ALL CHECKINGS ARE GOOD---------------------//
		//------------CREATE CMD---------------------//
		//-------Insert cmd to t_cmdque----------//

		$dspObj = new DSP();
		if($dspObj->generate_50msDualTone($node, $tbObj->name) ===false) {
			throw new Exception("TEST BUS - ".$dspObj->reason, 50);
		}
		
		//////-----------------BEGIN UPDATE TABLE IN DATABASE----------------------//

		$tbObj->query($node);
		$result['rows'] = $tbObj->rows;
		$result['rslt'] = 'success';
		$result['reason'] = "50ms DUAL TONE IS GENERATED";
		return $result;
	}
	catch (Exception $e) {
		if($e->getCode() == 50) {
			$result['rslt'] = 'fail';
			$result['reason'] = $e->getMessage();
			return $result;
		}
	}
}

function dspStopTone($tp, $node,$port, $tbx, $tby, $userObj) {
	try{
		// ------------CHECK USER PERMISSIONS---------------//
		if ($userObj->grpObj->maint != "Y") {
			throw new Exception('TEST BUS - Permission Denied', 50);
		}

		//--------------CHECK INPUT DATA-----------------//
		if($tp === "" || $node === "") {
			throw new Exception('TEST BUS - MISSING TP AND NODE INFO', 50);
		}

		if($port === "") {
			throw new Exception('TEST BUS - MISSING PORT INFO', 50);
		}

		$tbObj = new TB($node, $tp);
		if($tbObj->rslt == 'fail') {
			throw new Exception('TEST BUS - INVALID TP AND NODE', 50);
		}

		///--------------CHECK THE TEST PORT == DSP OR NOT----------------------///
		if(strpos($tbObj->name, 'DSP') === false) {
			throw new Exception('TEST BUS - INVALID TP AND NODE', 50);
		}

		if(($tbx == 'y' && $tby == 'y') || ($tbx == 'n' && $tby == 'n')) {
			throw new Exception('TEST BUS - WRONG TB_X AND TB_Y CONFIGURATION', 50);
		}

		if($tbx == 'y')
			$tb = 'x';
		if($tby == 'y')
			$tb = 'y';

		///--------------------CHECK T_TB----------------------///
		if($tb == 'x') {
			if($tbObj->tb_x != 1) {
				throw new Exception("TEST BUS - THERE IS NO CONNECTION FROM THIS TEST PORT TO TEST BUS X", 50);
				
			}
		}
		else {
			if($tbObj->tb_y != 1) {
				throw new Exception("TEST BUS - THERE IS NO CONNECTION FROM THIS TEST PORT TO TEST BUS Y", 50);
			}
		}

		///////------------------CHECK THE PORT INFO IN T_TB---------------------//
		if($tbObj->port != $port) {
			throw new Exception("TEST BUS - $port IS NOT CONNECTED TO THIS TEST BUS", 50);
            
        }
        //---------------- CHECK THE PORT -----------------//
		$portObj = new PORT();
		$portObj->loadPort($port);
		if($portObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $portObj->reason", 50);
            
        }

		//--------------CHECK PORT NDDE == $NODE-----------------------//
		// have to decrease $portObj->node by 1, because node in t_tb uses base 0
		if(($portObj->node -1) != $node) {
			throw new Exception("TEST BUS - PORT $port IS NOT IN NODE ".($node+1), 50);
		}

        //check the port type is the same with test bus
        if($portObj->ptyp != strtoupper($tb)) {
			throw new Exception("TEST BUS - PORT TYPE AND TEST BUS TYPE MUST BE THE SAME", 50);
        }
		
		//-------------------CHECK T_X/Y--------------------//
        $portExtract = explode('-', $port);
        $portConvert = ($portExtract[0]-1).".".$portExtract[2].".".($portExtract[1]-1).".".($portExtract[3]-1);
        if($portObj->ptyp == 'X')
            $portTypeObj = new X($portConvert);
        else 
            $portTypeObj = new Y($portConvert);

        if($portTypeObj->rslt == 'fail') {
			throw new Exception("TEST BUS - $portTypeObj->reason", 50);
            
        }
        
        //check in t_X or t_Y, there is a test connection exist
        if($portTypeObj->tb_conn != 1) {
			throw new Exception("TEST BUS - THERE IS NO TEST CONNECTION AT THIS PORT", 50);           
		}
		
		//--------------------UP TO THIS POINT: ALL CHECKINGS ARE GOOD---------------------//
		//------------CREATE CMD---------------------//
		//-------Insert cmd to t_cmdque----------//

		$dspObj = new DSP();
		if($dspObj->stopTone($node, $tbObj->name) ===false) {
			throw new Exception("TEST BUS - ".$dspObj->reason, 50);
		}
		
		//////-----------------BEGIN UPDATE TABLE IN DATABASE----------------------//

		$tbObj->query($node);
		$result['rows'] = $tbObj->rows;
		$result['rslt'] = 'success';
		$result['reason'] = "TONE IS TERMINATED";
		return $result;
	}
	catch (Exception $e) {
		if($e->getCode() == 50) {
			$result['rslt'] = 'fail';
			$result['reason'] = $e->getMessage();
			return $result;
		}
	}
}

?>