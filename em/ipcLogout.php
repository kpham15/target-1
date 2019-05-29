
<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: coQueryLogout.php
 * Change history: 
 * 2018-10-16: created (Thanh)
 */

    /* Initialize expected inputs */

    // $evtLog = new EVTLOG($user, "USERS", "LOGOUT");

    $evtLog = new EVENTLOG($user, "USER MANAGEMENT", "USER ACCESS", "logout", '');

        
    $userObj->updateLogout();
    $result["rslt"] = $userObj->rslt;
    $result["reason"] = $userObj->reason;
    $evtLog->log($result["rslt"], $result["reason"]);
    echo json_encode($result);
    mysqli_close($db);
    $debugObj->close();
    return;


?>
