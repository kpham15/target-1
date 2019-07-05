<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: ipcLogin.php
 * Change history: 
 * 2018-10-16: created (Thanh)
 */

    /**
     * Initialize Expected inputs
     */

	$pw = "";
	if (isset($_POST['pw']))
		$pw = $_POST['pw'];

    $newPw = "";
    if (isset($_POST['newPw']))
        $newPw = $_POST['newPw'];
            
    $act = "";
    if (isset($_POST['act']))
        $act = $_POST['act'];

    $evtLog = new EVENTLOG($user, "USER MANAGEMENT", "USER ACCESS", $act);

    /**
     * Dispatch to functions
     */
    if ($act == "login") {
        $result = userLogin($user, $pw, $newPw, $userObj);
        $evtLog->log($result["rslt"], $result["reason"]);
        echo json_encode($result);
		mysqli_close($db);
		return;
    }
    
    if ($act == "continue") {
        $result = userContinue($user);
        $evtLog->log($result["rslt"], $result["reason"]);
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
    /**
     * Functions
     */
    function userContinue($user) {
        $userObj = new USERS($user);
        if ($userObj->rslt == 'fail') {
            $result['rslt'] = 'fail';
            $result['reason'] = 'INVALID USER: ' . $user;
            $result['rows'] = [];
            return $result;
        }
        $userObj->updateLogin();
        $result['rslt'] = $userObj->rslt;
        $result['reason'] = $userObj->reason;
        $result['rows'] = [];
        return $result;
    }

    function testPwReuse($newpw) {
        $refObj = new REF();
        $userObj = new USERS($user);
        $now = time();
        $pwDate = strtotime($userObj->pwdate);
        $pwAge = round(($now - $pwDate) / (60 * 60 * 24));

        $pw0Date = strtotime($userObj->pw0);
        $pw0Age = round(($now - $pw0Date) / (60 * 60 * 24));

        $pw1Date = strtotime($userObj->pw1);
        $pw1Age = round(($now - $pw1Date) / (60 * 60 * 24));

        $pw2Date = strtotime($userObj->pw2);
        $pw2Age = round(($now - $pw2Date) / (60 * 60 * 24));

        $pw3Date = strtotime($userObj->pw3);
        $pw3Age = round(($now - $pw3Date) / (60 * 60 * 24));

        $pw4Date = strtotime($userObj->pw4);
        $pw4Age = round(($now - $pw4Date) / (60 * 60 * 24));

        if ($refObj->ref['pw_reuse'] == "0") {
            return true;
        } else if ($refObj->ref['pw_reuse'] > "0") {
            if ($refObj->ref['pw_reuse'] == "1") {
                if (decryptData($newpw) == decryptData($userObj->pw)) {
                    // check age
                    if ($pwAge > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw0)) {
                    // check age
                    if ($pw0Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else if ($refObj->ref['pw_reuse'] == "2") {
                if (decryptData($newpw) == decryptData($userObj->pw)) {
                    // check age
                    if ($pwAge > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw0)) {
                    // check age
                    if ($pw0Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw1)) {
                    // check age
                    if ($pw1Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else if ($refObj->ref['pw_reuse'] == "3") {
                if (decryptData($newpw) == decryptData($userObj->pw)) {
                    // check age
                    if ($pwAge > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw0)) {
                    // check age
                    if ($pw0Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw1)) {
                    // check age
                    if ($pw1Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw2)) {
                    // check age
                    if ($pw2Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else if ($refObj->ref['pw_reuse'] == "4") {
                if (decryptData($newpw) == decryptData($userObj->pw)) {
                    // check age
                    if ($pwAge > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw0)) {
                    // check age
                    if ($pw0Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw1)) {
                    // check age
                    if ($pw1Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw2)) {
                    // check age
                    if ($pw2Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw3)) {
                    // check age
                    if ($pw3Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else if ($refObj->ref['pw_reuse'] == "5") {
                if (decryptData($newpw) == decryptData($userObj->pw)) {
                    // check age
                    if ($pwAge > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw0)) {
                    // check age
                    if ($pw0Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw1)) {
                    // check age
                    if ($pw1Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw2)) {
                    // check age
                    if ($pw2Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw3)) {
                    // check age
                    if ($pw3Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (decryptData($newpw) == decryptData($userObj->pw4)) {
                    // check age
                    if ($pw4Age > $refObj->ref['pw_repeat']) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
    }

	function userLogin($user, $pw, $newPw, $userObj) {
        try{
            $userObj = new USERS($user);
            if($user == "" || $userObj->rslt == FAIL) {
                $result["rslt"]     = FAIL;
                $result["reason"]   = "INVALID USER";
                return $result;
            }

            // Lock user if login pw fail count more than 3 times
            if (decryptData($pw) != decryptData($userObj->pw)) {
                if($userObj->ugrp != 'ADMIN') {
                    $userObj->increasePwcnt();
                    if($userObj->pwcnt >= 3) {
                        $userObj->lckUser();
                        $result["rslt"]     = FAIL;
                        $result["reason"]   = $userObj->reason;
                        return $result;
                    }
                }
                $result["rslt"]     = FAIL;
                $result["reason"]   = "INVALID PW";
                return $result;
            }

            $wcObj = new WC();
            if($wcObj->stat == "OOS") {
                if($userObj->ugrp != 'ADMIN') {
                    $result["rslt"]     = FAIL;
                    $result["reason"]   = "DENIED - WIRE CENTER IS OOS";
                    return $result;
                }
            }

            // Deny if user is currently LOCKED or DISABLED
            if ($userObj->stat == "LOCKED" || $userObj->stat == "DISABLED") {
                $result['rslt'] = FAIL;
                $result['reason'] = "USER IS " . $userObj->stat;
                return $result;
            }
            
            // if login pw success, reset pw fail count to 0
            $userObj->resetPwcnt();
            if ($userObj->rslt == FAIL) {
                $result["rslt"]     = FAIL;
                $result["reason"]   = $userObj->reason;
                return $result;
            } 
            
            $refObj = new REF();

            //if not the case of changing password
            if (decryptData($newPw) == "") {
                // Deny if first time login without providing a newPw
                if (decryptData($userObj->pw) == $userObj->ssn) {   
                    $result['rslt'] = FAIL;
                    $result['reason'] = "FIRST TIME LOGIN, PLEASE PROVIDE NEW PASSWORD";
                    return $result;
                }
                //if not first time login, Deny if password has expired
                $pwDurationSec = strtotime(date("Y-m-d H:i:s")) - strtotime($userObj->pwdate) ;
                $pwDurationDay = ceil($pwDurationSec/(60*60*24));

                if($pwDurationDay >= $refObj->ref["pw_expire"]) {
                    $result['rslt'] = FAIL;
                    $result['reason'] = "PLEASE CHANGE PASSWORD, CURRENT PASSWORD HAS EXPIRED";
                    return $result;
                }
                
                $pwAlertDay = floor($refObj->ref["pw_expire"] - ((strtotime(date("Y-m-d H:i:s")) - strtotime($userObj->pwdate))/(60*60*24)));

                if($pwAlertDay > 0 && $pwAlertDay <= $refObj->ref["pw_alert"]) {
                    $result['pw_exp_alert'] = $pwAlertDay;
                }

            }
            // otherwise, update user pw
            else {
                $userObj = new USERS($user);
                $now = time();
                $pwDate = strtotime($userObj->pwdate);
                $pwAge = ceil(($now - $pwDate) / (60 * 60 * 24));
        
                $pw0Date = strtotime($userObj->pw0);
                $pw0Age = ceil(($now - $pw0Date) / (60 * 60 * 24));
        
                $pw1Date = strtotime($userObj->pw1);
                $pw1Age = ceil(($now - $pw1Date) / (60 * 60 * 24));
        
                $pw2Date = strtotime($userObj->pw2);
                $pw2Age = ceil(($now - $pw2Date) / (60 * 60 * 24));
        
                $pw3Date = strtotime($userObj->pw3);
                $pw3Age = ceil(($now - $pw3Date) / (60 * 60 * 24));
        
                $pw4Date = strtotime($userObj->pw4);
                $pw4Age = ceil(($now - $pw4Date) / (60 * 60 * 24));

                if (decryptData($userObj->pw) == $userObj->ssn) {  
                    $pwReuseTest = testPwReuse($newPw);
                    if ($pwReuseTest == true) {
                        $userObj->updatePw_firstTime($newPw);
                    } else {
                        $result['rslt'] = FAIL;
                        // $result["reason"] = "REUSE OF LAST " . $refObj->ref['pw_reuse'] . " PASSWORD IS NOT ALLOWED";
                        $result["reason"] = $now . " : " . $pwDate;
                        return $result;
                    }
                }
                else {
                    $pwReuseTest = testPwReuse($newPw);
                    if ($pwReuseTest == true) {
                        $userObj->updatePw($newPw);
                    } else {
                        $result['rslt'] = FAIL;
                        // $result["reason"] = "REUSE OF LAST " . $refObj->ref['pw_reuse'] . " PASSWORD IS NOT ALLOWED";
                        $result["reason"] = $now . " : " . $pwDate;
                        return $result;
                    }
                }
                if ($userObj->rslt == FAIL) {
                    $result["rslt"]     = FAIL;
                    $result["reason"]   = $userObj->reason;
                    return $result;
                }
            }

            // now update user login
            $userObj->updateLogin();
            if ($userObj->rslt == FAIL) {
                $result["rslt"]     = FAIL;
                $result["reason"]   = $userObj->reason;
                return $result;
            }    

            $result["rslt"]     = SUCCESS;
            if ($userObj->pw == $newPw) {
                $result['reason'] = "PASSWORD CHANGED/RESET SUCCESSFUL";
            }
            else {
                $result["reason"]   = "LOGIN SUCCESSFUL";
            }

            // if user_idle_to is 0/empty/null, set value to default, if default is 0/empty/null, set value = 45
            $userIdleInfo = $refObj->ref['user_idle_to'];
            if($userIdleInfo == 0){
                $userIdleInfo = $refObj->default['user_idle_to'];
                if($userIdleInfo == 0)
                    $userIdleInfo = 45;
            }

            $result['rows'] = array(array('uname' => $userObj->uname,
                                        'lname'=>$userObj->lname,
                                        'fname'=>$userObj->fname,
                                        'mi'=>$userObj->mi,
                                        'grp'=>$userObj->grp, 
                                        'ugrp'=>$userObj->ugrp,
                                        'loginTime'=>$userObj->login,
                                        'com'=>$userObj->com,
                                        'user_idle_to'=>$userIdleInfo));
            return $result;
            
        }
        catch (Exception $e) {
            if($e->getCode() == 100) {
                $result["rslt"]     = 'fail';
                $result["reason"]   = $e->getMessage();
                return $result;
            }
        }    
    }



?>
