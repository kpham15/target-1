

<?php

include "coCommonFunctions.php";
$user = "";
if (isset($_POST['user']))
    $user = $_POST['user'];

$act = "";
if (isset($_POST['act']))
    $act = $_POST['act'];

$s = "";
if (isset($_POST['s']))
    $s = $_POST['s'];

$n = "";
if (isset($_POST['n']))
    $n = $_POST['n'];

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
    $result = queryStg();
    mysqli_close($db);
    echo json_encode($result);
    
}

function queryStg () {
    global $db, $s, $n;
    $qry = "select * from t_stg where s ='$s' and n ='$n' ";

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