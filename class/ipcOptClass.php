<?php

class OPT {
    public $rslt;
    public $reason;
    public $rows = [];

    public function __construct() {
        global $db;
        
        $qry = "SELECT * FROM t_options";
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
            $this->reason = "OPTIONS_LOADED";
            return true;
        }
    }

}

?>