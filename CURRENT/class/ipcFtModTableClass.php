<?php

class FTMODTABLE {
    public $id              = 0;
    public $ot              = "";
    public $pri             = "";
    public $cdd             = "";
    public $noscm           = "";
    public $rtype           = "";
    public $processingfile  = "";

    public function __construct($id = NULL) {
        global $db;
        
        if ($id == NULL) {
            $qry = "SELECT * FROM t_ftmodification";
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
                    $this->rows = $rows;
                    $this->rslt = SUCCESS;
                    $this->reason = "FT_MODIFICATION constructed";
                }
            }
        }
        else {
            $qry = "SELECT * FROM t_ftmodification WHERE id = '$id'";
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
                    $this->id               = $rows[0]['id'];
                    $this->ot               = $rows[0]['ot'];
                    $this->pri              = $rows[0]['pri'];
                    $this->cdd              = $rows[0]['cdd'];
                    $this->noscm            = $rows[0]['noscm'];
                    $this->rtype            = $rows[0]['rtype'];
                    $this->processingfile   = $rows[0]['processingfile'];
                    $this->rslt             = "success";
                    $this->reason           = "Success";
                }
                else {
                    $this->rslt = "fail";
                    $this->reason = "No Such Id";
                }
            }
        }
    }

    public function queryFtModTable($ot, $pri, $cdd, $noscm, $rtype, $processingfile) {
        global $db;
        
        $qry = "SELECT * FROM t_ftmodification WHERE 
                id LIKE '%$id%' AND 
                ot LIKE '%$ot%' AND 
                pri LIKE '%$pri%' AND 
                cdd LIKE '%$cdd%' AND 
                noscm LIKE '%$noscm%' AND 
                rtype LIKE '%$rtype%' AND 
                processing_file LIKE '%$processingfile%'
                ";
        
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

    public function add($ot, $pri, $cdd, $noscm, $rtype, $processingfile) {
        global $db;

        $qry = "INSERT INTO 
                t_ftmodification 
                (ot, pri, cdd, noscm, rtype, 
                processing_file) 
                VALUES 
                ('$ot', '$pri', '$cdd', '$noscm', '$rtype', 
                '$processingfile')";
                
        $res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
            $this->reason = $qry . "\n" . mysqli_error($db);
		}
		else {
			$this->rslt = "success";
            $this->reason = "NEW FT_MODIFICATION CREATED";
		}

    }

    public function delete($id) {
        global $db;

        if ($id == "") {
			$this->rslt = "fail";
			$this->reason = "Invalid FT_MODIFICATION ID";
			return false;
        }
        
		$qry = "DELETE FROM t_ftmodification WHERE id = '$id'";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "FT_MODIFICATION Deleted";
            $this->rows = [];
            return true;
        }
    }

    public function update($id, $ot, $pri, $cdd, $noscm, $rtype, $processingfile) {
        global $db;

        $qry = "UPDATE t_ftmodification SET 
                id = '$id', 
                ot = '$ot', 
                pri = '$pri', 
                cdd = '$cdd', 
                noscm = '$noscm', 
                rtype = '$rtype', 
                processing_file = '$processingfile' 
                WHERE id = '$id'";
                
        $res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
            $this->reason = $qry . "\n" . mysqli_error($db);
		}
		else {
			$this->rslt = "success";
            $this->reason = "FT_MODIFICATION UPDATED";
		}

 
    }
}



?>