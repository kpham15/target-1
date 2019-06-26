<?php

class Y {
    public $u;
    public $s;
    public $n;
    public $i;
    public $d;
    public $y;

    public $tb_row;
    public $tb_col;
    public $tb_conn;

    public $rslt;
    public $reason;

    public function __construct($Y) {
        global $db;
        
        $p = explode('.', $Y);
        $qry = "select * from t_Y where u='$p[0]' and s='$p[1]' and n='$p[2]' and i='$p[3]'";
        $res = 	$db->query($qry);
        if (!$res) {
            $this->rslt   = 'fail';
            $this->reason = mysqli_error($db);
        }
        else {
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $this->u = $row["u"];
                    $this->s = $row["s"];
                    $this->n = $row["n"];
                    $this->i = $row["i"];
                    $this->d = $row["d"];
                    $e = explode(".", $row["d"]);
                    $this->y = $e[3];
                    $this->tb_row = $row["tb_row"];
                    $this->tb_col = $row["tb_col"];
                    $this->tb_conn = $row["tb_conn"];
                }
                $this->rslt = SUCCESS;
                $this->reason = "Y FOUND";
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = "Y NOT FOUND";
            }
            
        }
    }

    public function allowTestConn($node) {
        global $db;
        
        $rows = [];
        $qry = "SELECT * FROM t_Y WHERE u='$node' AND tb_conn <> 0";
        $res = $db->query($qry);
        
        if(!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
        }

        if(count($rows) >0) {
            $this->rslt = "fail";
            $this->reason = "";
            for($i=0; $i<count($rows); $i++)
                 $this->reason .= $rows[$i]['a']." "; 
            return false;
        }
        return true;
    }


    public function updateTestConn($tb_conn) {
        global $db;
        
        $rows = [];
        $qry = "UPDATE t_Y SET tb_com='$tb_conn' WHERE u='$this->u' AND d='$this->d'";
        $res = $db->query($qry);
        
        if(!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            return false;
        }
        else {
            return true;
        }
    }
}


?>