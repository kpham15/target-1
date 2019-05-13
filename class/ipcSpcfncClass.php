<?php

class SPCFNC {
    public $rslt;
    public $reason;
    public $rows = [];

    public function __construct($spcfnc=NULL) {
        global $db;
        
        $qry = "SELECT * FROM t_spcfnc";
        if ($spcfnc !== NULL) {
            $qry .= " WHERE spcfnc='$spcfnc'";
        }

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $this->rows[] = $row;
                }
            }  
            $this->rslt = SUCCESS;
            $this->reason = "SPCFNC_LOADED";
            return true;
        }
    }

    public function find($spcfnc) {
        for ($i=0; $i<count($this->rows); $i++) {
            if ($this->rows[$i]['spcfnc'] == $spcfnc) {
                $this->rslt = SUCCESS;
                $this->reason = "SPCFNC_EXIST";
                return true;
            }
        }
        $this->rlst = FAIL;
        $this->reason = "SPCFNC_NOT_EXIST";
        return false;
    }
}

?>