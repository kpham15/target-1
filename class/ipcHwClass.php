<?php
class HW {
    public $id      = 0;
    public $node    = 0;
    public $stat    = "";
    public $alm     = "";
    public $volt    = "";
    public $temp    = "";
    public $com     = "";
    public $mx0     = "";
    public $mx1     = "";
    public $mx2     = "";
    public $mx3     = "";
    public $mx4     = "";
    public $mx5     = "";
    public $mx6     = "";
    public $mx7     = "";
    public $mx8     = "";
    public $mx9     = "";
    public $my0     = "";
    public $my1     = "";
    public $my2     = "";
    public $my3     = "";
    public $my4     = "";
    public $my5     = "";
    public $my6     = "";
    public $my7     = "";
    public $my8     = "";
    public $my9     = "";
    public $mr0     = "";
    public $mr1     = "";
    public $mr2     = "";
    public $mr3     = "";
    public $mr4     = "";
    public $mr5     = "";
    public $mr6     = "";
    public $mr7     = "";
    public $mr8     = "";
    public $mr9     = "";

    public $rslt    = "";
    public $reason  = "";
    public $rows    = [];

    public function __construct($node=NULL) {
        global $db;
        if ($node == NULL) {
            $this->rslt = SUCCESS;
            $this->reason = "";
            return;
        }
        $qry = "SELECT * FROM t_hw WHERE node = '$node' LIMIT 1";
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
                $this->rslt     = SUCCESS;
                $this->reason   = QUERY_MATCHED;
                $this->rows     = $rows;
                $this->id       = $rows[0]['id'];
                $this->node     = $rows[0]['node'];
                $this->stat     = $rows[0]['stat'];
                $this->alm      = $rows[0]['alm'];
                $this->volt     = $rows[0]['volt'];
                $this->temp     = $rows[0]['temp'];
                $this->com      = $rows[0]['com'];
                $this->mx0      = $rows[0]['mx0'];
                $this->mx1      = $rows[0]['mx1'];
                $this->mx2      = $rows[0]['mx2'];
                $this->mx3      = $rows[0]['mx3'];
                $this->mx4      = $rows[0]['mx4'];
                $this->mx5      = $rows[0]['mx5'];
                $this->mx6      = $rows[0]['mx6'];
                $this->mx7      = $rows[0]['mx7'];
                $this->mx8      = $rows[0]['mx8'];
                $this->mx9      = $rows[0]['mx9'];
                $this->my0      = $rows[0]['my0'];
                $this->my1      = $rows[0]['my1'];
                $this->my2      = $rows[0]['my2'];
                $this->my3      = $rows[0]['my3'];
                $this->my4      = $rows[0]['my4'];
                $this->my5      = $rows[0]['my5'];
                $this->my6      = $rows[0]['my6'];
                $this->my7      = $rows[0]['my7'];
                $this->my8      = $rows[0]['my8'];
                $this->my9      = $rows[0]['my9'];
                $this->mr0      = $rows[0]['mr0'];
                $this->mr1      = $rows[0]['mr1'];
                $this->mr2      = $rows[0]['mr2'];
                $this->mr3      = $rows[0]['mr3'];
                $this->mr4      = $rows[0]['mr4'];
                $this->mr5      = $rows[0]['mr5'];
                $this->mr6      = $rows[0]['mr6'];
                $this->mr7      = $rows[0]['mr7'];
                $this->mr8      = $rows[0]['mr8'];
                $this->mr9      = $rows[0]['mr9'];
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
                $this->rows   = $rows;
            }
        }
    }

    public function query($node) {
        global $db;
    
        $qry = "SELECT * FROM t_hw where node = '$node'";
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
                $this->rslt   = SUCCESS;
                $this->reason = QUERY_MATCHED;
                $this->rows   = $rows;
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = QUERY_NOT_MATCHED;
                $this->rows   = $rows;
            }
        }
    }

    public function update($node, $volt, $temp, $com, $mx0, $mx1, $mx2, $mx3, $mx4, $mx5, $mx6, $mx7, $mx8, $mx9, $my0, $my1, $my2, $my3, $my4, $my5, $my6, $my7, $my8, $my9, $mr0, $mr1, $mr2, $mr3, $mr4, $mr5, $mr6, $mr7, $mr8, $mr9) {
        global $db;

        $qry  = "UPDATE t_hw SET ";
        $qry .= "volt   =   '$volt'   ";
        $qry .= ",temp   =   '$temp'   ";
        $qry .= ",com    =   '$com'    ";
        $qry .= ",mx0    =   '$mx0'    ";
        $qry .= ",mx1    =   '$mx1'    ";
        $qry .= ",mx2    =   '$mx2'    ";
        $qry .= ",mx3    =   '$mx3'    ";
        $qry .= ",mx4    =   '$mx4'    ";
        $qry .= ",mx5    =   '$mx5'    ";
        $qry .= ",mx6    =   '$mx6'    ";
        $qry .= ",mx7    =   '$mx7'    ";
        $qry .= ",mx8    =   '$mx8'    ";
        $qry .= ",mx9    =   '$mx9'    ";
        $qry .= ",my0    =   '$my0'    ";
        $qry .= ",my1    =   '$my1'    ";
        $qry .= ",my2    =   '$my2'    ";
        $qry .= ",my3    =   '$my3'    ";
        $qry .= ",my4    =   '$my4'    ";
        $qry .= ",my5    =   '$my5'    ";
        $qry .= ",my6    =   '$my6'    ";
        $qry .= ",my7    =   '$my7'    ";
        $qry .= ",my8    =   '$my8'    ";
        $qry .= ",my9    =   '$my9'    ";
        $qry .= ",mr0    =   '$mr0'    ";
        $qry .= ",mr1    =   '$mr1'    ";
        $qry .= ",mr2    =   '$mr2'    ";
        $qry .= ",mr3    =   '$mr3'    ";
        $qry .= ",mr4    =   '$mr4'    ";
        $qry .= ",mr5    =   '$mr5'    ";
        $qry .= ",mr6    =   '$mr6'    ";
        $qry .= ",mr7    =   '$mr7'    ";
        $qry .= ",mr8    =   '$mr8'    ";
        $qry .= ",mr9    =   '$mr9'    ";
        $qry .= " WHERE node = '$node'";
        $res  = $db->query($qry);
        if (!$res) {
            $this->rslt     = FAIL;
            $this->reason   = mysqli_error($db);
        }
        else {
            if ($db->affected_rows < 1) {
                $this->rslt = FAIL;
                $this->reason = "No change was submitted";
                return;
            }
            $this->rslt       = "success";
            $this->reason     = "Hw ".$node." has been updated";
        }
    }

    public function updateAlm($alm) {
        global $db;

        $qry = "UPDATE t_hw SET alm='$alm' WHERE id='$this->id'";
       
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }

        $this->alm = $alm;

        $this->rslt = SUCCESS;
        $this->reason = "HW_ALM_UPDATED";
    }
}
?>