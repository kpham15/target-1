
<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcBkup.php
 * Change history: 
 * 2018-11-09: created (Thanh)
 */

 	//include "coCommonFunctions.php";
  
    $act = "";
    if (isset($_POST['act']))
		$act = $_POST['act'];
		
    $user = "";
    if (isset($_POST['user']))
		$user = $_POST['user'];

	$id = "";
	if (isset($_POST['id']))
		$id = $_POST['id'];
	
	$dbfile = '';
	if (isset($_POST['dbfile']))
		$dbfile = $_POST['dbfile'];

		
	$fileName='';
	if (file_exists($_FILES['file']['tmp_name'])) {
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])) 
		{ 
			$fileName = $_FILES["file"]["name"];
			move_uploaded_file( $_FILES["file"]["tmp_name"], "../../DBBK/".$fileName);
		}
	}

	$evtLog = new EVENTLOG($user, "IPC ADMINISTRATION", "BACKUP DATABASE", $act, "");
	$ipAddress = $_SERVER['SERVER_ADDR'];
	if($ipAddress == '::1') {
		$ipAddress = '127.0.0.1';
	}


	$ipcCon = $db;
		
	$co5kDb = new DB();
	if ($co5kDb->rslt == "fail") {
		$result["rslt"] = "fail";
		$result["reason"] = $co5kDb->reason;
		echo json_encode($result);
		return;
	}

	if ($act == "query") {
		$result = queryBkup();
		echo json_encode($result);
		mysqli_close($ipcCon);
		mysqli_close($db);
		mysqli_close($co5kDb);
		return;
	}
	else if ($act == "BACKUP MANUALLY") {
		$result = manualBkup($userObj, $ipAddress, $co5kDb);
		$evtLog->log($result["rslt"],$result["reason"]);
		echo json_encode($result);
		mysqli_close($ipcCon);
		mysqli_close($db);
		mysqli_close($co5kDb);
		return;
	}
	// else if ($act == "UPLOAD") {
	// 	$result = uploadBkup($userObj, $ipAddress, $fileName);
	// 	echo json_encode($result);
	// 	mysqli_close($ipcCon);
	// 	mysqli_close($db);
	// 	mysqli_close($co5kDb);
	// 	return;
	// }
	else if ($act == "DELETE BACKUP FILE") {
		$result = deleteBkup($id, $dbfile, $userObj);
		$evtLog->log($result["rslt"],$result["reason"]);
		echo json_encode($result);
		mysqli_close($ipcCon);
		mysqli_close($db);
		mysqli_close($co5kDb);
		return;
	}
	// else if ($act == "RESTORE") {
	// 	$result = restoreDb($co5kDb, $dbfile, $userObj);
	// 	echo json_encode($result);
	// 	mysqli_close($ipcCon);
	// 	mysqli_close($db);
	// 	mysqli_close($co5kDb);
	// 	return;
	// }
	else {
 		$result["rslt"] = "fail";
		$result["reason"] = "ACTION " . $act . " is under development or not supported";
		$evtLog->log($result["rslt"],$result["reason"]);
		echo json_encode($result);
		mysqli_close($ipcCon);
		mysqli_close($db);
		mysqli_close($co5kDb);
		return;
	}
	
				
	function queryBkup() {
		global $ipcCon;
		
		$qry = "SELECT * FROM t_dbbk";
		
        $res = $ipcCon->query($qry);
        if (!$res) {
            $result["rslt"] = "fail";
            $result["reason"] = mysqli_error($ipcCon);
        }
        else {
            $rows = [];
            $result["rslt"] = "success";
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
			}
			$result["reason"] = "QUERY SUCCESSFULLY";
            $result["rows"] = $rows;
        }
		return $result;
	}

	function manualBkup($userObj, $ipAddress, $co5kDb) {
		// Note: the folder ../../DBBK must be open for the permission of execution
		global $ipcCon;

		if ($userObj->grpObj->bkupdb != "Y") {
			$result['rslt'] = 'fail';
        	$result['reason'] = 'Permission Denied';
			return $result;
		}

		$wc = new WC();
		$bkupName = $wc->wcc . '_' . $wc->getWCTime() . '.sql';
		$time = date("Y-m-d H:i:s");
		
		// $bkupName = date("Y-m-d-H-i-s").'.sql';
		//get directory of current php file
		$directory = dirname(__FILE__);
		
		//split it into array
		$dirArray = explode("/",$directory);
		$htmlIndex = array_search("html",$dirArray);
		$phpIndex = array_search("php",$dirArray);

		$fullpath = '../DBBK/' . $bkupName;
		$dir = "../../DBBK/" . $bkupName;

		$command = "mysqldump --user={$co5kDb->ui} --password={$co5kDb->pw} {$co5kDb->dbname} --result-file={$dir} 2>&1";

		exec($command,$output,$return);
		if(!$return) {
			$qry = "insert into t_dbbk values(0,'$userObj->uname','$time','$bkupName','$fullpath', 'M')";
			$res = $ipcCon->query($qry);
			if (!$res) {
				$result["rslt"] = "fail";
				$result["reason"] = mysqli_error($ipcCon);
			}
			else {
				$qry = "SELECT * FROM t_dbbk";
		
				$res = $ipcCon->query($qry);
				if (!$res) {
					$result["rslt"] = "fail";
					$result["reason"] = mysqli_error($ipcCon);
				}
				else {
					$rows = [];
					$result["rslt"] = "success";
					if ($res->num_rows > 0) {
						while ($row = $res->fetch_assoc()) {
							$rows[] = $row;
						}
					}
					$result["rows"] = $rows;
					$result["reason"] = "BACKUP SUCCESSFULLY";
				}	
			}
		}
		else {
			$result["rslt"] = "fail";
			$result["reason"] = "execution failed: " . $command . " : " . $return;
		}


		return $result;

	}
	

	function uploadBkup($userObj, $ipAddress, $fileName) {
		global $ipcCon;
		if ($userObj->grpObj->bkupdb != "Y") {
			$result['rslt'] = 'fail';
        	$result['reason'] = 'Permission Denied';
			return $result;
		}

		$time = date("Y-m-d H:i:s");

		//get directory of current php file
		$directory = dirname(__FILE__);
		
		//split it into array
		$dirArray = explode("/",$directory);
		$htmlIndex = array_search("html",$dirArray);
		$phpIndex = array_search("php",$dirArray);
		$fullpath = 'http://'.$ipAddress;
		
		for($i=($htmlIndex+1); $i<$phpIndex; $i++) {
			$fullpath .= '/'.$dirArray[$i];
			
		}
		$fullpath .= '../DBBK/' . $fileName;

		$qry = "insert into t_dbbk values(0,'$userObj->uname','$time','$fileName','$fullpath', 'U')";

		$res = $ipcCon->query($qry);
		if (!$res) {
			$result["rslt"] = "fail";
			$result["reason"] = mysqli_error($ipcCon);
		}
		else {
			$qry = "SELECT * FROM t_dbbk";
	
			$res = $ipcCon->query($qry);
			if (!$res) {
				$result["rslt"] = "fail";
				$result["reason"] = mysqli_error($ipcCon);
			}
			else {
				$rows = [];
				$result["rslt"] = "success";
				if ($res->num_rows > 0) {
					while ($row = $res->fetch_assoc()) {
						$rows[] = $row;
					}
				}
				$result["reason"] = "UPLOAD SUCCESSFULLY";
				$result["rows"] = $rows;
			}	
		}
		return $result;
	}

	function deleteBkup($id, $dbfile, $userObj) {
		global $ipcCon;
		if ($userObj->grpObj->bkupdb != "Y") {
			$result['rslt'] = 'fail';
        	$result['reason'] = 'Permission Denied';
			return $result;
		}

		if (unlink("../../DBBK/" . $dbfile)) {
			$qry = "delete from t_dbbk where id='$id' or dbfile='$dbfile'";
		
			$res = $ipcCon->query($qry);
			if (!$res) {
				$result["rslt"] = "fail";
				$result["reason"] = mysqli_error($ipcCon);
			}
			else {
				$qry = "SELECT * FROM t_dbbk";
			
				$res = $ipcCon->query($qry);
				if (!$res) {
					$result["rslt"] = "fail";
					$result["reason"] = mysqli_error($ipcCon);
				}
				else {
					$rows = [];
					$result["rslt"] = "success";
					if ($res->num_rows > 0) {
						while ($row = $res->fetch_assoc()) {
							$rows[] = $row;
						}
					}
					$result["reason"] = "DELETE SUCCESSFULLY";
					$result["rows"] = $rows;
				}	
			}
			
		}
		else {
			$result["rslt"] = "fail";
			$result["reason"] = "Something wrong with the file";
		}
		return $result;
	}

	function restoreDb($co5kDb, $dbfile, $userObj) {

		if ($userObj->grpObj->bkupdb != "Y") {
			$result['rslt'] = 'fail';
			$result['reason'] = 'Permission Denied';
			return $result;
		}

		$dir = "../../DBBK/" . $dbfile;

		$command = "mysql --user={$co5kDb->ui} --password={$co5kDb->pw} {$co5kDb->dbname} < $dir";
		exec($command,$output,$return);

		if(!$return) {
			$result["rslt"] = "success";
			$result["reason"] = "RESTORE SUCCESSFULLY";
		}
		else {
			$result["rslt"] = "fail";
			$result["reason"] = "execution failed";
		}


		return $result;
	} 
	
?>
