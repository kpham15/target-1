<?php

    /* Initialize expected inputs */
    $act = "";
    if (isset($_POST['act']))
        $act = $_POST['act'];

    // $evtLog = new Evtlog($user, "OPTION", $act);

    // DISPATCH
    if ($act == "queryOpt") {
        $optObj = new OPT();
        $result['rslt'] = $optObj->rslt;
        $result['reason'] = $optObj->reason;
        $result['rows'] = $optObj->rows;
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);
        
    }
    else if ($act == "queryOrt"){
        $ortObj = new ORT();
        $result['rslt'] = $ortObj->rslt;
        $result['reason'] = $ortObj->reason;
        $result['rows'] = $ortObj->rows;
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);
        
    }
    else if ($act == "querySpcfnc"){
        $spcfncObj = new SPCFNC();
        $result['rslt'] = $spcfncObj->rslt;
        $result['reason'] = $spcfncObj->reason;
        $result['rows'] = $spcfncObj->rows;
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);
        
    }
    else if ($act == "queryFtyp"){   
        $ftypObj = new FTYP();
        $result['rslt'] = $ftypObj->rslt;
        $result['reason'] = $ftypObj->reason;
        $result['rows'] = $ftypObj->rows;
        mysqli_close($db);
        $debugObj->close();
        echo json_encode($result);     
    }
    
  

?>