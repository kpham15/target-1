<?php

    include "coCommonFunctions.php";
    
    $user = "";
    if (isset($_POST['user']))
        $user = $_POST['user'];

    $act = "";
    if (isset($_POST['act']))
        $act = $_POST['act'];

    $dbObj = new Db();
    if ($dbObj->rslt == "fail") {
        $result["rslt"] = "fail";
        $result["reason"] = $dbObj->reason;
        echo json_encode($result);
        return;
    }
    $db = $dbObj->con;

    if ($act == "query")
    {
        $result = queryFtRelease();
        mysqli_close($db);
        echo json_encode($result);
        return;
    }
    else {
        $result["rslt"] = "fail";
        $result["reason"] = "ACTION " . $act . " is under development or not supported";
        echo json_encode($result);
        mysqli_close($db);
        return;
    }
   

    function queryFtRelease () {
        global $db;
        
        $qry = "select * from t_ftrelease";
        
        $res = $db->query($qry);
        if (!$res) {
            $result["rslt"] = "fail";
            $result["reason"] = mysqli_error($db);
        }
        else
        {
            $rows = [];
            $result["rslt"] = "success";
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $result["rows"] = $rows;
        }
        return $result;
    }

?>