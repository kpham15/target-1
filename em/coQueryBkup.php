
<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: coQueryBkup.php
 * Change history: 
 * 2018-11-09: created (Thanh)
 */

 	include "coCommonFunctions.php";
  
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
			move_uploaded_file( $_FILES["file"]["tmp_name"], "../resource/dbbk/".$fileName);
		}
	}

	
	$ipAddress = $_SERVER['SERVER_ADDR'];
	if($ipAddress == '::1') {
		$ipAddress = '127.0.0.1';
	}

	// Dispatch to Functions
	$ipcDb = new ipcDb();
	if ($ipcDb->rslt == "fail") {
		$result["rslt"] = "fail";
		$result["reason"] = $ipcDb->reason;
		echo json_encode($result);
		return;
	}
	$ipcCon = $ipcDb->con;
		
	$co5kDb = new Db();
	if ($co5kDb->rslt == "fail") {
		$result["rslt"] = "fail";
		$result["reason"] = $co5kDb->reason;
		echo json_encode($result);
		return;
	}

	if ($act == "query") {
		$result = queryBkup();
		echo json_encode($result);
		return;
	}
	else if ($act == "MANUAL") {
		$result = manualBkup();
		echo json_encode($result);
		return;
	}
	else if ($act == "UPLOAD") {
		$result = uploadBkup();
		echo json_encode($result);
		return;
	}
	else if ($act == "DELETE") {
		$result = deleteBkup();
		echo json_encode($result);
		return;
	}
	else if ($act == "RESTORE") {
		$result = restoreDb();
		echo json_encode($result);
		return;
	}
	else {
 		$result["rslt"] = "fail";
		$result["reason"] = "ACTION " . $act . " is under development or not supported";
		echo json_encode($result);
		mysqli_close($ipcCon);
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
            $result["rows"] = $rows;
        }
		mysqli_close($ipcCon);
		return $result;
	}

	function manualBkup() {
		// Note: the folder resource/dbbk must be open for the permission of execution
		global $ipcCon, $ipcDb, $user, $ipAddress, $co5kDb;

		$time = date("Y-m-d H:i:s");
		
		$bkupName = date("Y-m-d-H-i-s").'.sql';
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
		$fullpath .= '/resource/dbbk/'.$bkupName;
		$dir = "../resource/dbbk/".$bkupName;

		$command = "mysqldump --user={$co5kDb->ui} --password={$co5kDb->pw} {$co5kDb->dbname} --result-file={$dir} 2>&1";

		exec($command,$output,$return);
		if(!$return) {
			$qry = "insert t_dbbk values(0,'$user','$time','$bkupName','$fullpath', 'M')";
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
				}	
			}
			mysqli_close($ipcCon);
		}
		else {
			$result["rslt"] = "fail";
			$result["reason"] = "execution failed";
		}


		return $result;

	}
	

	function uploadBkup() {
		global $ipcCon, $user, $ipAddress, $fileName;

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
		$fullpath .= '/resource/dbbk/'.$fileName;

		$qry = "insert t_dbbk values(0,'$user','$time','$fileName','$fullpath', 'U')";

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
			}	
		}
		mysqli_close($ipcCon);
		return $result;
	}

	function deleteBkup() {
		global $ipcCon, $id, $dbfile;

		if(unlink("../resource/dbbk/".$dbfile)) {
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
					$result["rows"] = $rows;
				}	
			}
			mysqli_close($ipcCon);
			
		}
		else {
			$result["rslt"] = "fail";
			$result["reason"] = "Something wrong with the file";
		}
		return $result;
	}

	function restoreDb() {
		global $co5kDb, $dbfile;


		$dir = "../resource/dbbk/".$dbfile;

		$command = "mysql --user={$co5kDb->ui} --password={$co5kDb->pw} {$co5kDb->dbname} < $dir";
		exec($command,$output,$return);

		if(!$return) {
			$result["rslt"] = "success";
		}
		else {
			$result["rslt"] = "fail";
			$result["reason"] = "execution failed";
		}


		return $result;
	} 
	
?>
