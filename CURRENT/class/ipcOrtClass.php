<?php
class ORT {
    public $rslt;
    public $reason;
    public $rows = [];

    public function __construct($ort=NULL) {
        global $db;
        
        $qry = "SELECT * FROM t_ort";
        if ($ort !== NULL) {
            $qry .= " WHERE ort='$ort'";
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
                $this->reason = "ORT_LOADED";
                return true;
            }  
            else {
                $this->rslt = FAIL;
                $this->reason = "ORT_NOT_EXIST";
                return false;
            }
        }
    }

    public function find($ort) {
        for ($i=0; $i<count($this->rows); $i++) {
            if ($this->rows[$i]['ort'] == $ftyp) {
                $this->rslt = SUCCESS;
                $this->reason = "ORT_EXIST";
                return true;
            }
        }
        $this->rlst = FAIL;
        $this->reason = "ORT_NOT_EXIST";
        return false;
    }

    public static function validate($ort) {
        global $db;
        $obj = new self;
        $qry = "SELECT * FROM t_ort";
        if ($ort !== NULL) {
            $qry .= " WHERE ort='$ort'";
        }
        
        $res = $db->query($qry);
        if(!$res) {
            $obj->rslt = FAIL;
            $obj->reason = mysqli_error($db);
        }
        else {
            if($res->num_rows > 0) {
                $obj->rslt = SUCCESS;
                $obj->reason = "ORT_QUERIED";
            }  
            else {
                $obj->rslt = FAIL;
                $obj->reason = "INVALID_ORT";
            }
            
        }
        return $obj;
    }
}


?>