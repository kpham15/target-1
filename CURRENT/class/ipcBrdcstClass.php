<?php

class BRDCST{
    public $id = 0;
    public $stamp = 0;
    public $userObj = NULL;
    public $ownerObj = NULL;
    public $user = "";
    public $owner = "";
    public $date = "";
    public $wcc = "";
    public $frm_id = "";
    public $sa = "";
    public $msg = "";
    public $detail = "";

    public $rows = [];
    public $rslt = "";
    public $reason = "";

    public function __construct($id = NULL) {
        global $db;

        if($id === NULL) {
            $rslt = SUCCESS;
            $reason = BRDCST_CONSTRUCTED;
            return;
        }

        $qry = "SELECT * FROM t_brdcst WHERE id='$id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt    = FAIL;
            $this->reason  = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->id   = $rows[0]["id"];
                $this->u    = $rows[0]["stamp"];
                $this->user    = $rows[0]["user"];
                
                $this->userObj = new USERS($this->user);
                $this->owner    = $rows[0]["owner"];
            
                $this->ownerObj = new USERS($this->owner);
                $this->date         = $rows[0]["date"];
                $this->wcc          = $rows[0]["wcc"];
                $this->frm_id       = $rows[0]["frm_id"];
                $this->sa           = $rows[0]["sa"];
                $this->msg          = $rows[0]["msg"];
                $this->detail       = $rows[0]["detail"];
                $this->rslt         = SUCCESS;
                $this->reason       = BRDCST_CONSTRUCTED;
            }
            else {
                $this->rslt    = FAIL;
                $this->reason  = INVALID_BRDCST;
            }
            $this->rows = $rows;
        }
    }

    public function query($id) {
        global $db;

        $qry = "SELECT * FROM t_brdcst WHERE id = '$id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt    = FAIL;
            $this->reason  = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row; 
                }
                $this->rslt = SUCCESS;
                $this->reason= QUERY_MATCHED;    
            }
            else {
                $this->rslt    = FAIL;
                $this->reason  = QUERY_NOT_MATCHED;
            }
            $this->rows = $rows;
        }
    }

    public function addBroadcast($user, $owner, $owner_id, $sa, $msg, $detail){
        global $db;

        if ($user === "") {
			$this->rslt = "fail";
			$this->reason = "Missing USER";
			return $false;
		}

		if ($msg === "") {
			$this->rslt = "fail";
			$this->reason = "Missing MSG TITLE";
			return $false;
		}

		if ($detail === "") {
			$this->rslt = "fail";
			$this->reason = "Missing MSG DETAILS";
			return $false;
		}

		if ($sa === "") {
			$this->rslt = "fail";
			$this->reason = "Missing SA";
			return $false;
		}

		$userObj = new USERS($user);
		if ($userObj->rslt == "fail") {
			$this->rslt = "fail";
			$this->reason = $userObj->reason;
			return $false;
		}

		if ($owner_id == "") {
			$owner = $userObj->fname . " " . $userObj->mi . " " . $userObj->lname;
			$owner_id = $userObj->uname;
		}
		else {
			$ownerObj = new USERS($owner_id);
			if ($ownerObj->rslt == "fail") {
				$this->rslt = "fail";
				$this->reason = $ownerObj->reason;
				return $false;
			}
		}
		
       
        $qry = "INSERT INTO 
                t_brdcst 
                (user, owner, owner_id, date, sa, 
                msg, detail) 
                VALUES 
                ('$user', '$owner', '$owner_id', now(), '$sa', 
                '$msg', '$detail')";
        
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
			$this->reason = mysqli_error($db);
        }
        else {
            // return result with query for data
            //$this->queryBroadcast("", "");
            $this->rslt = "success";
            $this->reason = "BRDCST_ADDED";
            $this->rows = [];
        }
		return $true;

    }

    public function delBroadcast($id){
        global $db;

        if ($id == "") {
			$this->rslt = "fail";
			$this->reason = "Invalid BROADCAST MESSAGE";
			return false;
        }
        
		$qry = "DELETE FROM t_brdcst WHERE id=" . $id;
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "BRDCST_DELETED";
            $this->rows = [];
            return true;
        }
    }

    private function queryBroadcast($uname, $sa) {
        global $db, $user;
        
        $qry = "SELECT * FROM t_brdcst WHERE user LIKE '%$uname%' AND sa LIKE '%$sa%' ORDER BY date DESC";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            $this->rslt = "success";
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rows = $rows;
        }
    }

    public function updBroadcast($id,$stamp, $date, $wcc, $frm_id, $sa, $msg, $detail){
        global $db;

        if ($id == "") {
			$this->rslt = "fail";
			$this->reason = "Missing MESSAGE";
			return $false;
		}

		if ($sa == "") {
			$this->rslt = "fail";
			$this->reason = "Missing SA";
			return $false;
		}

		if ($msg == "") {
			$this->rslt = "fail";
			$this->reason = "Invalid MSG TITLE";
			return $false;
		}

		if ($detail == "") {
			$this->rslt = "fail";
			$this->reason = "Invalid MSG DETAILS";
			return $false;
		}

		$qry = "UPDATE t_brdcst SET date=now(), sa='$sa', msg='$msg' , detail='$detail'  WHERE id=" . $id ;
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            // return result with query for data
            //$this->queryBroadcast("", "");
            $this->rslt = "success";
            $this->reason = "BRDCST_UPDATED";
        }
		return $true;
    }


    public function findBcByUser($uname, $sa) {
        global $db;

        $qry = "SELECT * FROM t_brdcst WHERE user LIKE '%$uname%' AND sa LIKE '%$sa%' ORDER BY date DESC";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rslt = "success";
            $this->reason = "FIND_BRDCST_BY_USER";
            $this->rows = $rows;
            return true;
        }
    }
    //delete record based on ipcRef setting
    //expire_date format should be 'YYYY-MM-DD'
    public function deleteExpiredLog($expired_date) {
        global $db;
        
        $qry = "DELETE FROM t_brdcst WHERE date < '$expired_date'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            $this->rslt = "success";
            $this->reason = "EXPIRED LOGS DELETED";
        }
            
    }

}

?>