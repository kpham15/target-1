<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: coQueryEventLog.php
 * Change history: 
 * 2018-10-10: created (Tracy)
 */
	
    // Initialize Expected inputs
    $act = "";
	if (isset($_POST['act']))
		$act = $_POST['act'];
		
	$uname = "";
	if (isset($_POST['uname']))
		$uname = $_POST['uname'];

	$fnc = "";
	if (isset($_POST['fnc']))
		$fnc = $_POST['fnc'];

	$evt = "";
	if (isset($_POST['evt']))
		$evt = $_POST['evt'];

	$days = "";
	if (isset($_POST['days']))
		$days = $_POST['days'];

	// Dispatch to Functions
	
	if ($act == "query") {
		$evtObj = new EVTLOG("","","");
		$evtObj->query($uname, $fnc, $evt, $days);
		$result["rslt"] = $evtObj->rslt;
		$result["reason"] = $evtObj->reason;
		$result["rows"] = $evtObj->rows;
		echo json_encode($result);
		mysqli_close($db);
		return;
	}

	if ($act == "report") {
		$result = reportEvents($uname, $fnc, $evt, $days);
		echo json_encode($result);
		mysqli_close($db);
		return;
	}

	if ($act == "queryEvents") {
		$result = queryEvents();
		echo json_encode($result);
		mysqli_close($db);
		return;
	}

	else {
 		$result["rslt"] = "fail";
		$result["reason"] = "Invalid ACTION";
		$evtLog->log($result["rslt"], $result["reason"]);
		echo json_encode($result);
        mysqli_close($db);
		return;
	}
	
	// Functions

	function reportEvents($uname, $fnc, $evt, $days) {

		$evtObj = new EVTLOG("","","");
		$evtObj->query($uname, $fnc, $evt, $days);
		if($evtObj->rslt == 'fail') {
			$result['rslt'] = "fail";
			$result['reason'] = $evtObj->reason;
			return $result;
		}

		if(($uname === "" && $fnc === "" && $evt === "" && $days === "") || $fnc === "") {
			$fileList = filesQuery();
			$result['rslt'] = "success";
			$result['reason'] = "FILES_QUERY";
			$result['reportName'] = "";
			$result["rows"] = [];
			$result['reportFileList'] = $fileList;
			return $result;
		}

		if (count($evtObj->rows) == 0){
			$fileList = filesQuery();
			$result['rslt'] = "success";
			$result['rslt'] = "FILES_FOUND";
			$result["rows"] = $evtObj->rows;
			$result['reportName'] = "";
			$result['reportFileList'] = $fileList;
			return $result;
		}
		
		$reportFile = strtolower($fnc)."_report_".date("Y-m-d-H-i-s").".txt";
		createReport($reportFile, $evtObj->rows);

		$fileList = filesQuery();

		$result['rslt'] = "success";
		$result['reason'] = "REPORT_CREATED";
		$result['reportName'] = $reportFile;
		$result["rows"] = $evtObj->rows;
		$result['reportFileList'] = $fileList;
		
		return $result;
	}
	
	function createReport($reportFile, $rows) {

		if (!file_exists('../REPORTS')) {
            mkdir('../REPORTS', 0777, true);
		}

		$fp = fopen("../REPORTS/" . $reportFile, "w");
		if (!$fp) {
            $result['rslt'] = "fail";
			$result['reason'] = "CAN NOT OPEN REPORT FILE";
			return $result;
		}

		$len = count($rows);
		fwrite($fp, "REPORT FILE NAME: ".$reportFile."\n\n");
		for ($i=0; $i<$len; $i++) {
			fwrite($fp, $rows[$i]["detail"] . "\n\n");
		}
		fclose($fp);

	}


	// function createUrl() {
	// 	$ipAddress = $_SERVER['SERVER_ADDR'];
	// 	if($ipAddress == '::1') {
	// 		$ipAddress = '127.0.0.1';
	// 	}

    //     $directory = dirname(__FILE__);
		
	// 	//split it into array
	// 	$dirArray = explode("/",$directory);
	// 	$htmlIndex = array_search("html",$dirArray);
	// 	$phpIndex = array_search("php1",$dirArray);
	// 	$fullpath = 'http://'.$ipAddress;
		
	// 	for($i=($htmlIndex+1); $i<$phpIndex; $i++) {
	// 		$fullpath .= '/'.$dirArray[$i];
			
    //     }
    //     $fullpath .=  "/REPORTS/";
    //     return $fullpath;
	// }
	
	function filesQuery () {
		
		// $url = createUrl();
				
		$directory = "../REPORTS/";
        $folder=opendir("../REPORTS/");

        $dirArray = [];
        while (false !== ($file = readdir($folder))) {
            if ($file != "." && $file != "..") {
               $dirArray[filemtime($directory.$file)] = $file;
            }
        }
        closedir($folder);
        $indexCount=count($dirArray);
        krsort($dirArray);
        $fileList = [];
        
        foreach($dirArray as $file){
			$name=$file;
			$size=number_format(filesize($directory.$file));
			// $path = $url.$name;
			$path = "REPORTS/";
			array_push($fileList,[
				'name'=> $name,
				'size'=> $size,
				'link'=> $path
				]
			);
        }
        return $fileList;
    }
?>
