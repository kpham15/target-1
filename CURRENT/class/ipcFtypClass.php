<?php
class FTYP {
    public $rslt;
    public $reason;
    public $rows = [];

    public function __construct($ftyp=NULL) {
        global $db;
        
        $qry = "SELECT * FROM t_ftyp";
        if ($ftyp !== NULL) {
            $qry .= " WHERE ftyp='$ftyp'";
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
                $this->rslt = SUCCESS;
                $this->reason = "FTYP_LOADED";
                return true;
            }  
            else {
                $this->rslt = FAIL;
                $this->reason = "FTYP_NOT_EXIST";
                return false;
            }
        }
    }

    public function find($ftyp) {
        for ($i=0; $i<count($this->rows); $i++) {
            if ($this->rows[$i]['ftyp'] == $ftyp) {
                $this->rslt = SUCCESS;
                $this->reason = "FTYP_EXIST";
                return true;
            }
        }
        $this->rlst = FAIL;
        $this->reason = "FTYP_NOT_EXIST";
        return false;
    }
}


?>