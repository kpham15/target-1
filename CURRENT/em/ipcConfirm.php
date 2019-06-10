<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcConfirm.php
 * Change history: 
 * 2019-04-15 created (Thanh)
 */

 /* Initialize expected inputs */

    $act = "";
    if (isset($_POST['act'])) {
		$act = $_POST['act'];
	}

	// Dispatch to Functions	
	
	if ($act == "confirm") {
		$result = confirm();
        echo json_encode($result);
		return;
    }
    else {
        $result["rslt"] = "fail";
		$result["reason"] = "ACTION " . $act . " is under development or not supported";
        echo json_encode($result);
		return;
    }
    	
   
	function confirm() {

        $key = createKey();

        $result['rslt'] = "success";
        $result['reason'] = "confirmed";
		$result['key'] = $key;
        return $result;
    }
	
	
?>
