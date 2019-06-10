<?php

class RC {
    public $rslt;
    public $reason;
    public $rows;

    public function __construct(){

    }

    public function queryRC($rel) {
        global $db;
        $this->rows = [];
        $qry = "SELECT * FROM t_rc WHERE rel = '$rel'";
        $res = $db->query($qry);
        if(!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
        }
        else {
            if($res->num_rows == 1) {
                $row = $res->fetch_assoc();
                $this->rows[] = $row;

                $this->rslt = 'success';
                $this->reason = "RC_QUERIED";
            }
            else {
                $this->rslt = 'fail';
                $this->reason = "INVALID RELAY ($rel)";
            }

        }
    }
}




?>