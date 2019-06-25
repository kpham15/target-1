<?php

class FTRELEASE {
    public $id          = 0;
    public $sot         = "";
    public $ot          = "";
    public $rot         = "";
    public $cls         = "";
    public $oc          = "";
    public $adsr        = "";
    public $act         = "";
    public $ftyp        = "";
    public $fac         = "";
    public $fdd_int     = "";
    public $dd_int      = "";
    public $rtyp        = "";
    public $rt          = "";
    public $jcond       = "";
    public $jeop        = "";

    public function __construct($id = NULL) {
        global $db;
        
        if ($id == NULL) {
            $qry = "SELECT * FROM t_ftrelease";
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
                    $this->reason = "FTRELEASE constructed";
                }
            }
        }
        else {
            $qry = "SELECT * FROM t_ftrelease WHERE id = '$id'";
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
                    $this->id           = $rows[0]['id'];
                    $this->sot          = $rows[0]['sot'];
                    $this->ot           = $rows[0]['ot'];
                    $this->rot          = $rows[0]['rot'];
                    $this->cls          = $rows[0]['cls'];
                    $this->oc           = $rows[0]['oc'];
                    $this->adsr         = $rows[0]['adsr'];
                    $this->act          = $rows[0]['act'];
                    $this->ftyp         = $rows[0]['ftyp'];
                    $this->fac          = $rows[0]['fac'];
                    $this->fdd_int      = $rows[0]['fdd_int'];
                    $this->dd_int       = $rows[0]['dd_int'];
                    $this->rtyp         = $rows[0]['rtyp'];
                    $this->rt           = $rows[0]['rt'];
                    $this->jcond        = $rows[0]['jcond'];
                    $this->jeop         = $rows[0]['jeop'];
                }
            }
        }
    }

    public function queryFtRelease($sot, $ot, $rot, $cls, $oc, $adsr, $act, $ftyp, $fac, $fdd_int, $dd_int, $rtyp, $rt, $jcond, $jeop) {
        global $db;
        
        $qry = "SELECT * FROM t_ftrelease WHERE 
                sot      LIKE '%$sot%'      AND 
                ot       LIKE '%$ot%'       AND 
                rot      LIKE '%$rot%'      AND 
                cls      LIKE '%$cls%'      AND 
                oc       LIKE '%$oc%'       AND 
                adsr     LIKE '%$adsr%'     AND 
                act      LIKE '%$act%'    AND 
                ftyp     LIKE '%$ftyp%'  AND 
                fac      LIKE '%$fac%'    AND 
                fdd_int  LIKE '%$fdd_int%'   AND 
                dd_int   LIKE '%$dd_int%'    AND 
                rtyp     LIKE '%$rtyp%'     AND 
                rt       LIKE '%$rt%'       AND 
                jcond    LIKE '%$jcond%'    AND 
                jeop     LIKE '%$jeop%'
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

    public function add($sot, $ot, $rot, $cls, $oc, $adsr, $act, $ftyp, $fac, $fdd_int, $dd_int, $rtyp, $rt, $jcond, $jeop) {
        global $db;
        if($ot == "") {
            $this->rslt = "fail";
            $this->reason = "MISSING OT";
            return;
        }

        if($rt == "") {
            $this->rslt = "fail";
            $this->reason = "MISSING RT";
            return;
        }

        $this->checkDuplication($sot, $ot, $rot, $cls, $oc, $adsr, $act, $ftyp, $fac, $fdd_int, $dd_int, $rtyp, $rt, $jcond, $jeop);
        if($this->rslt == "fail") {
            return;
        }

        if ($fdd_int == "") {
            $fdd_int = 0;
        }
        if ($dd_int == "") {
            $dd_int = 0;
        }

        $qry = "INSERT INTO 
                t_ftrelease 
                (sot, ot, rot, cls, oc, 
                adsr, act, ftyp, fac, fdd_int, 
                dd_int, rtyp, rt, jcond, jeop) 
                VALUES 
                ('$sot', '$ot', '$rot', '$cls', '$oc', 
                '$adsr', '$act', '$ftyp', '$fac', '$fdd_int', 
                '$dd_int', '$rtyp', '$rt', '$jcond', '$jeop')";

        $res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
            $this->reason = $qry . "\n" . mysqli_error($db);
		}
		else {
			$this->rslt = "success";
            $this->reason = "NEW FTRELEASE CREATED";
		}

    }

    public function delete($id) {
        global $db;

        if ($id == "") {
			$this->rslt = "fail";
			$this->reason = "Invalid FtRelease ID";
			return false;
        }
        
		$qry = "DELETE FROM t_ftrelease WHERE id = '$id'";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            $this->rslt = "success";
            $this->reason = "FtRelease Deleted";
            $this->rows = [];
            return true;
        }
    }

    public function update($id, $sot, $ot, $rot, $cls, $oc, $adsr, $act, $ftyp, $fac, $fdd_int, $dd_int, $rtyp, $rt, $jcond, $jeop) {
        global $db;
        if($ot == "") {
            $this->rslt = "fail";
            $this->reason = "MISSING OT";
            return;
        }

        if($rt == "") {
            $this->rslt = "fail";
            $this->reason = "MISSING RT";
            return;
        }

        $duplicationList = $this->checkDuplication($sot, $ot, $rot, $cls, $oc, $adsr, $act, $ftyp, $fac, $fdd_int, $dd_int, $rtyp, $rt, $jcond, $jeop);
        if($this->rslt == "fail") {
            if(in_array($id,$duplicationList)) {
                $this->reason = "There is no change in all inputs";
            }
            return;
        }
        if ($fdd_int == "") {
            $fdd_int = 0;
        }
        if ($dd_int == "") {
            $dd_int = 0;
        }

        $qry = "UPDATE t_ftrelease SET 
                sot      = '$sot', 
                ot       = '$ot', 
                rot      = '$rot', 
                cls      = '$cls', 
                oc       = '$oc', 
                adsr     = '$adsr', 
                act      = '$act', 
                ftyp     = '$ftyp', 
                fac      = '$fac', 
                fdd_int  = '$fdd_int', 
                dd_int   = '$dd_int', 
                rtyp     = '$rtyp', 
                rt       = '$rt', 
                jcond    = '$jcond', 
                jeop     = '$jeop' 
                WHERE id = '$id'";
                
        $res = $db->query($qry);
		if (!$res) {
			$this->rslt = "fail";
            $this->reason = $qry . "\n" . mysqli_error($db);
		}
		else {
			$this->rslt = "success";
            $this->reason = "FTRELEASE UPDATED";
		}

 
    }

    public function checkDuplication($sot, $ot, $rot, $cls, $oc, $adsr, $act, $ftyp, $fac, $fdd_int, $dd_int, $rtyp, $rt, $jcond, $jeop) {
        global $db;
        $duplication_list = [];

        $qry = "SELECT * FROM t_ftrelease WHERE 
                sot      = '$sot'      AND 
                ot       = '$ot'       AND 
                rot      = '$rot'      AND 
                cls      = '$cls'      AND 
                oc       = '$oc'       AND 
                adsr     = '$adsr'     AND 
                act      = '$act'    AND 
                ftyp     = '$ftyp'  AND 
                fac      = '$fac'    AND 
                fdd_int  = '$fdd_int'   AND 
                dd_int   = '$dd_int'    AND 
                rtyp     = '$rtyp'     AND 
                rt       = '$rt'       AND 
                jcond    = '$jcond'    AND 
                jeop     = '$jeop'
                ";
        
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $this->rslt = "success";
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $duplication_list[] = $row['id'];
                }
                $this->rslt = "fail";
                $this->reason = "Duplication Error";
            }
            else {
                $this->rslt = "success";
                $this->reason = "Ready to proceed task";
            }
            
        }
        return $duplication_list;
    }
}



?>