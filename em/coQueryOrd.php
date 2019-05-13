<?php

include "coCommonFunctions.php";
    

    $act = '';
    if (isset($_POST['act']))
        $act = $_POST['act'];
    
    $user = '';
    if(isset($_POST['user']))
        $user = $_POST['user'];

    $ordno   ='';
    if(isset($_POST['ordno']))
        $ordno = $_POST['ordno'];

    $foms ='';
    if(isset($_POST['foms']))
        $foms = $_POST['foms'];

    
    
    $dbObj = new Db();
	if ($dbObj->rslt == "fail") {
		$result["rslt"] = "fail";
		$result["reason"] = $dbObj->reason;
		echo json_encode($result);
		return;
	}
	$db = $dbObj->con;
	
    if ($act == "add") {
		$result = addFoms();
		echo json_encode($result);
		mysqli_close($db);
		return;
    }
    else {
        $result["rslt"] = "fail";
        $result["reason"] = "This action is under development!";
        echo json_encode($result);
		mysqli_close($db);
		return;
    }

   
  

    function addFoms() {

		global $db, $user, $ordno, $foms;

		$qry = "INSERT INTO t_foms VALUES ('0','$user', now(),'$ordno','$foms')";
		$res = $db->query($qry);
        if (!$res) {
            $result["rslt"] = "fail";
            $result["reason"] = mysqli_error($db);
        }
        else {
            $result["rslt"] = "success";
            
        }
        return $result;
    
    }

   
?>