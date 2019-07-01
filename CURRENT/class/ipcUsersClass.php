<?php


class USERS {
    public $id          = 0;
    public $uname       = "";
    public $lname       = "";
    public $fname       = "";
    public $mi          = "";
    public $ssn         = "";
    public $addr        = "";
    public $city        = "";
    public $state       = "";
    public $zip         = "";
    public $title       = "";
    public $tel         = "";
    public $email       = "";
    public $stat        = "";
    public $pw          = "";
    public $pwdate      = "";
    public $pw0         = "";
    public $t0          = "";
    public $pw1         = "";
    public $t1          = "";
    public $pw2         = "";
    public $t2          = "";
    public $pw3         = "";
    public $t3          = "";
    public $pw4         = "";
    public $t4          = "";
    public $exp         = "";
    public $pwcnt       = "";
    public $supv        = "";
    public $com         = "";
    public $grp         = "";
    public $ugrp        = "";
    public $login       = "";
    public $lastlogin   = "";
    public $grpObj      = "";

    public $rslt        = "";
    public $reason      = "";
    public $row         = [];
    public $rows        = [];

    public $superUsers = array('ADMIN', 'IPCADMIN', 'SYSTEM');

    public function __construct($uname=NULL) {
        global $db;
        
        if ($uname === NULL) {
            $this->rslt = SUCCESS;
            $this->reason = "";
            return;
        }
        else {
            $qry = "SELECT * FROM t_users WHERE upper(uname)  = upper('$uname') ";
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt   = FAIL;
                $this->reason = mysqli_error($db);
                return;
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    $this->rslt      = SUCCESS;
                    $this->reason    = "USER FOUND";
                    $this->rows      = $rows;
                    $this->id        = $rows[0]['id'];
                    $this->uname     = $rows[0]['uname'];
                    $this->lname     = $rows[0]['lname'];
                    $this->fname     = $rows[0]['fname'];
                    $this->mi        = $rows[0]['mi'];
                    $this->ssn       = $rows[0]['ssn'];
                    $this->addr      = $rows[0]['addr'];
                    $this->city      = $rows[0]['city'];
                    $this->state     = $rows[0]['state'];
                    $this->zip       = $rows[0]['zip'];
                    $this->title     = $rows[0]['title'];
                    $this->tel       = $rows[0]['tel'];
                    $this->email     = $rows[0]['email'];
                    $this->stat      = $rows[0]['stat'];
                    $this->pw        = $rows[0]['pw'];
                    $this->pwdate    = $rows[0]['pwdate'];
                    $this->pw0       = $rows[0]['pw0'];
                    $this->t0        = $rows[0]['t0'];
                    $this->pw1       = $rows[0]['pw1'];
                    $this->t1        = $rows[0]['t1'];
                    $this->pw2       = $rows[0]['pw2'];
                    $this->t2        = $rows[0]['t2'];
                    $this->pw3       = $rows[0]['pw3'];
                    $this->t3        = $rows[0]['t3'];
                    $this->pw4       = $rows[0]['pw4'];
                    $this->t4        = $rows[0]['t4'];
                    $this->exp       = $rows[0]['exp'];
                    $this->pwcnt     = $rows[0]['pwcnt'];
                    $this->supv      = $rows[0]['supv'];
                    $this->com       = $rows[0]['com'];
                    $this->grp       = $rows[0]['grp'];
                    $this->ugrp       = $rows[0]['ugrp'];
                    $this->login     = $rows[0]['login'];
                    $this->lastlogin = $rows[0]['lastlogin'];
                    $this->grpObj    = new GRP($rows[0]['grp']);
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = "INVALID USER - " .  $uname;
                }
            }
        }
    }

    public function query() {
        global $db;

        $qry = "SELECT * FROM t_users";
        $res = $db->query($qry);
        if (!$res) {
            $this ->rslt    = FAIL;
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    if (!in_array(strtoupper($row['uname']), $this->superUsers))
                        $rows[] = $row;
                }
            }
            $this->rows = $rows;
            $this->rslt = SUCCESS;
            $this->reason = "QUERY_USER_SUCCESS";
        }
    }
    
    public function queryByStatus($uname, $stat) {
        global $db;

        $qry = "SELECT t_users.id, t_users.uname, t_users.stat, t_users.lastlogin, t_users.lname, t_users.fname, t_users.mi";
        $qry .= ", t_users.ssn, t_users.tel, t_users.email, t_users.title, t_users.grp, t_grp.ugrp FROM t_users left join t_grp on";
        $qry .= " t_users.grp=t_grp.id";


        $qry .= " WHERE upper(t_users.uname) LIKE upper('%$uname%')";
           
        if ($stat !="") {
            $qry .= " AND t_users.stat = '$stat'";
        }

        $res = $db->query($qry);
            if (!$res) {
                $this->rslt = "fail";
                $this->reason = mysqli_error($db);
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        if (!in_array(strtoupper($row['uname']), $this->superUsers))
                        $rows[] = $row;
                    }  
                }
                $this->rslt = "success";
                $this->reason = "QUERY_BY_STATUS_SUCCESS";
                $this->rows = $rows;
            }

    }

    public function queryByUName($lname, $fname) {
        global $db;

        $qry = "SELECT t_users.id, t_users.uname, t_users.stat, t_users.lastlogin, t_users.lname, t_users.fname, t_users.mi";
		$qry .= ", t_users.ssn, t_users.tel, t_users.email, t_users.title, t_users.grp, t_grp.ugrp FROM t_users left join t_grp on";
		$qry .= " t_users.grp=t_grp.id";
		 
		if ($lname != "") {
			$qry .= " WHERE t_users.lname LIKE '%$lname%'";
			if ($fname != "") {
				$qry .= " AND t_users.fname LIKE '%$fname%'";
			}
		}
		else if ($fname != "") {
			$qry .= " WHERE t_users.fname LIKE '%$fname%'";
		}
		
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
				while ($row = $res->fetch_assoc()) {
                    if (!in_array(strtoupper($row['uname']), $this->superUsers))
                        $rows[] = $row;
				}  
			}
            $this->rslt = "success";
            $this->reason = "QUERY_BY_UNAME_SUCCESS";
			$this->rows = $rows;
        }

    }

    public function queryByTel($tel, $email) {

        global $db;

        $qry = "SELECT t_users.id, t_users.uname, t_users.stat, t_users.lastlogin, t_users.lname, t_users.fname, t_users.mi";
		$qry .= ", t_users.ssn, t_users.tel, t_users.email, t_users.title, t_users.grp, t_grp.ugrp FROM t_users left join t_grp on";
		$qry .= " t_users.grp=t_grp.id";
		 
		if ($tel != "") {
			$qry .= " WHERE t_users.tel LIKE '%$tel%'";
			if ($email != "") {
				$qry .= " AND t_users.email LIKE '%$email%'";
			}
		}
		else if ($email != "") {
			$qry .= " WHERE t_users.email LIKE '%$email%'";
		}
		
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
				while ($row = $res->fetch_assoc()) {
                    if (!in_array(strtoupper($row['uname']), $this->superUsers))
                        $rows[] = $row;
				}  
			}
            $this->rslt = "success";
            $this->reason = "QUERY_BY_TEL_SUCCESS";
			$this->rows = $rows;
        }
    }

    public function queryByUserGrp($title, $ugrp) {
        global $db;
		
		$qry = "SELECT t_users.id, t_users.uname, t_users.stat, t_users.lastlogin, t_users.lname, t_users.fname, t_users.mi";
		$qry .= ", t_users.ssn, t_users.tel, t_users.email, t_users.title, t_users.grp, t_grp.ugrp FROM t_users left join t_grp on";
		$qry .= " t_users.grp=t_grp.id";
		 
		if ($title != "") {
			$qry .= " WHERE t_users.title LIKE '%$title%'";
			if ($ugrp != "") {
				$qry .= " AND t_grp.ugrp LIKE '%$ugrp%'";
			}
		}
		else if ($ugrp != "") {
			$qry .= " WHERE t_grp.ugrp LIKE '%$ugrp%'";
		}
		
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
				while ($row = $res->fetch_assoc()) {
                    if (!in_array(strtoupper($row['uname']), $this->superUsers))
                        $rows[] = $row;
				}  
			}
            $this->rslt = "success";
            $this->reason = "QUERY_BY_GROUP_SUCCESS";
			$this->rows = $rows;
        }

    }

    public function addUser($uname, $lname, $fname, $mi, $ssn, $tel, $email, $title, $ugrp) {
        global $db;
		
		
		// if ($user == "") {
		// 	$this->rslt = "fail";
        //     $this->reason = "Missing USER";
        //     return false;
		// }

		if ($uname == "") {
			$this->rslt = "fail";
            $this->reason = "Missing UNAME";
            return false;
		}
 			
		if ($lname == "") {
			$this->rslt = "fail";
            $this->reason = "Missing LNAME";
            return false;
		}
 			
		if ($fname == "") {
			$this->rslt = "fail";
            $this->reason = "Missing FNAME";
            return false;
		}
 			
		if ($ssn == "") {
			$this->rslt = "fail";
            $this->reason = "Missing SSN";
            return false;
		}
 			
		if ($ugrp == "") {
			$this->rslt = "fail";
            $this->reason= "Missing GROUP";
            return false;
		}
		
		else {
			$grp = $this->getGrpId($ugrp);
			if ($grp["rslt"] == "fail") {
				$this->rslt = "fail";
                $this->reason = $grp["reason"];
                return false;
			}
			$grpId = $grp["id"];
		}
        $pw = encryptData($ssn);

        
        $qry = "INSERT INTO t_users 
                (uname, lname, fname, mi, ssn, 
                addr, city, state, zip, title, 
                tel, email, stat, pw, pwdate, 
                pw0, t0, pw1, t1, pw2, 
                t2, pw3, t3, pw4, t4, 
                exp, pwcnt, supv, com, grp, 
                ugrp, login, lastlogin) 
                VALUES 
                ('$uname', '$lname', '$fname', '$mi', '$ssn', 
                '', '', '', '', '$title', 
                '$tel', '$email', 'INACTIVE', '$pw', now(), 
                '', '', '', '', '', 
                '', '', '', '', '', 
                '', 0, '', '', '$grpId',
                 '$ugrp', null, now())";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "USER_ADDED_SUCCESS";
            return true;
        }
    }

    public function updUser($lname, $fname, $mi, $ssn, $tel, $email, $title, $ugrp){
        global $db;
			
		$grp = $this->getGrpId($ugrp);
		if ($grp["rslt"] == "fail") {
			$this->rslt = "fail";
            $this->reason = $grp["reason"];
		}
		$grpId = $grp["id"];
		
		$qry = "UPDATE t_users SET grp='$grpId'";
		
		
		if ($lname != "") {
            $qry .= ",lname='$lname'";
		}
		if ($fname != "") {
            $qry .= ",fname='$fname'";
		}
		if ($mi != "") {
			$qry .= ",mi='$mi'";
		}
		if ($ssn != "") {
			$qry .= ",ssn='$ssn'";
		}
		if ($title != "") {
			$qry .= ",title='$title'";
		}
		if ($tel != "") {
			$qry .= ",tel='$tel'";
		}
		if ($email != "") {
			$qry .= ",email='$email'";
        }
        
		
		$qry .= " WHERE upper(uname)=upper('$this->uname')";
		
		
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "USER_UPDATED";
            return true;
        }

    }


    public function updateLogin() {
        global $db;

        $now = date("Y-m-d H:i:s", time());

        $qry = "UPDATE t_users set stat = 'ACTIVE', login='$now' WHERE upper(uname) = upper('$this->uname')";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
            return FALSE;
        }
        $this->login = $now;
        $this->rslt = SUCCESS;
        $this->reason = "USER LOGIN UPDATED";
        return TRUE;
    }

    public function updateLogout() {
        global $db;

        $qry = "UPDATE t_users SET stat = 'INACTIVE',lastlogin=now() WHERE upper(uname) = upper('$this->uname')";
         
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        
        $this->rslt = SUCCESS;
        $this->reason = "USER LOGOUT UPDATED";
    }
    
    public function validateNewPw($newPw) {
        if (preg_match("/^(?=.*\d)(?=.*[a-z] || ?=.*[A-Z]).{6,16}$/", $newPw))
            return TRUE;
        else 
            return FALSE;
    }


    public function updateUserLogin() {
        global $db;

        $qry = "UPDATE t_users SET login = now() WHERE upper(uname)=upper('$this->uname')";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            $this->rslt     = SUCCESS;
            $this->reason   = "USER_LOGIN_TIME_UPDATED";
        }
    }

    private function getGrpId($ugrp) {
		global $db;
		
		$qry = "SELECT id FROM t_grp WHERE ugrp='$ugrp'";
		$res = $db->query($qry);
		$rows = [];
        if ($res->num_rows > 0) {
			while ($row = $res->fetch_assoc()) {
				$rows[] = $row;
			}  
            $grp["rslt"] = "success";
            $grp['reason'] = 'GRP_ID_RECEIVED';
			$grp["id"] = $rows[0]["id"];
		}
		else {
			$grp["rslt"] = "fail";
			$grp["reason"] = "Invalid ugrp";
		}
		return $grp;
    }
    
    public function increasePwcnt() {
        global $db;
        if ($this->pwcnt === NULL)
            $this->pwcnt = 0;

        $this->pwcnt = (int)$this->pwcnt +1;
		$qry = "UPDATE t_users SET pwcnt='$this->pwcnt' WHERE upper(uname)=upper('$this->uname')";
		
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "USER_UPDATED_PWCNT";
            return true;
        }
    }

    public function resetPwcnt() {
        global $db;

		$qry = "UPDATE t_users SET pwcnt= 0 WHERE upper(uname)=upper('$this->uname')";
		
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "USER_RESET_PWCNT";
            return true;
        }
    }

    public function updateUserImage($com) {
        global $db;

        $qry = "UPDATE t_users SET com='$com' WHERE id='$this->id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = SUCCESS;
            $this->reason = 'USER IMAGE UPDATED';
            return true;
        }
    }

    ///////////////-------------/////////////
   
    public function lckUser(){
        global $db;

		$qry = "UPDATE t_users SET stat='LOCKED' WHERE upper(uname)=upper('$this->uname')";
		
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "USER_LOCKED";
            return true;
        }

    }

    public function unlckUser(){
        global $db;

		$qry = "UPDATE t_users SET stat='INACTIVE', pwcnt=0 WHERE upper(uname)=upper('$this->uname')";
		
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "USER_UNLOCKED";
            return true;
        }

    }

    public function disableUser(){
        global $db;

		$qry = "UPDATE t_users SET stat='DISABLED' WHERE upper(uname)=upper('$this->uname')";
		
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "USER_DISABLED";
            return true;
        }
    }

    public function enableUser(){
        global $db;
        $pw = encryptData($this->ssn);
		$qry = "UPDATE t_users SET stat='INACTIVE', lastlogin=now(), pw='$pw', pwcnt=0 WHERE upper(uname)=upper('$this->uname')";
		
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "USER_ENABLED";
            return true;
        }
    }

    public function deleteUser(){
        global $db;

		$qry = "DELETE FROM t_users WHERE upper(uname)=upper('$this->uname')";
		
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "USER_DELETED";
            return true;
        }
    }

    public function resetPw(){
        global $db;

        $pw = encryptData($this->ssn);
		$qry = "UPDATE t_users SET pw='$pw', pwcnt=0 WHERE upper(uname)=upper('$this->uname')";
		
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "RESET_PW";
            return true;
        }
    }

    public function updatePw_firstTime($newPw) {
        global $db;

        if ($this->validateNewPw(decryptData($newPw)) === FALSE) {
            $this->rslt = FAIL;
            $this->reason = "INVALID PASSWORD FORMAT: MUST BE 6-15 CHARACTERS, AT LEAST 1 LETTER, 1 NUMBER";
            return FALSE;
        }
        $qry = "UPDATE t_users set pw='$newPw', pwdate=now(), pwcnt=0 WHERE upper(uname)=upper('$this->uname')";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
            return FALSE;
        }
        $this->rslt = SUCCESS;
        $this->reason = "USER PW UPDATED";
        return TRUE;
    }

	public function updatePw($newPw) {
        global $db;

        if ($this->validateNewPw(decryptData($newPw)) === FALSE) {
            $this->rslt = 'fail';
            $this->reason = "INVALID PASSWORD FORMAT: MUST BE 6-15 CHARACTERS, AT LEAST 1 LETTER, 1 NUMBER";
            return FALSE;
        }

        if ((decryptData($newPw) !== decryptData($this->pw)) && (decryptData($newPw) !== decryptData($this->pw0)) && (decryptData($newPw) !== decryptData($this->pw1)) && (decryptData($newPw) !== decryptData($this->pw2)) && (decryptData($newPw) !== decryptData($this->pw3)) && (decryptData($newPw) !== decryptData($this->pw4))) {
            $this->pw4 = $this->pw3;
            $this->pw3 = $this->pw2;
            $this->pw2 = $this->pw1;
            $this->pw1 = $this->pw0;
            $this->pw0 = $this->pw;

            $this->pw = $newPw;
            
            $qry = "UPDATE t_users SET pw='$this->pw', pw0='$this->pw0',pw1='$this->pw1',pw2='$this->pw2',pw3='$this->pw3',pw4='$this->pw4', pwdate=now(), pwcnt=0 WHERE upper(uname)=upper('$this->uname')";
            $res = $db->query($qry);

            if (!$res) {
                $this->rslt = "fail";
                $this->reason = mysqli_error($db);
                return false;
            }
            
            $this->rslt = "success";
            $this->reason = "USER PW CHANGE SUCCESS";
            return true;

        }
        else {
            $this->rslt = "fail";
            $this->reason = "Password cannot be the same as previous 5 passwords!";
            return false;
        }
    }

}


?>
