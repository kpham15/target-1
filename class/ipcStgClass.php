<?php

class Stg {
    public $u;
    public $s;
    public $n;
    public $a;
    public $p;      //input pin
    public $x = array();
    public $y = array();
    public $d = array();
    public $pa;

    public $rslt;
    public $reason;
    // This is the constructor function for this class.
    //public function __construct($u, $s, $n) {
    public function __construct($d) {
        global $db;

        $e = explode(".", $d);
        $this->u = $e[0];
        $this->s = $e[1];
        $this->n = $e[2];
        $this->p = $e[3];
        $this->a = $e[0] . "." . $e[1] . "." . $e[2];

        $qry = "select x,y,d,pa from t_stg where s='$this->s' and u='$this->u' and n='$this->n'";
        $res = 	$db->query($qry);
        if(!$res){
            $this->rslt   = 'fail';
            $this->reason = mysqli_error($db);
        } 
        else {
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $this->x[] = $row["x"];
                    $this->y[] = $row["y"];
                    $this->d[] = $row["d"];
                    $this->pa[] = $row["pa"];
                }
                $this->rslt = SUCCESS;
                $this->reason = "STG_CONSTRUCTED";
            }
            else {
                $this->rslt   = FAIL;
                $this->reason = "STG_NOT_CONSTRUCTED";
            }
        }
        
    }

    public function setXY($x, $y, $pa) {
        global $db;

        $qry = "update t_stg set x='$x', pa='$pa' where u='$this->u' and s='$this->s' and n='$this->n' and y='$y'";
        $res = 	$db->query($qry);
        if(!$res){
            $this->rslt   = 'fail';
            $this->reason = mysqli_error($db);
        } 
        else {
            $this->rslt   = 'success';
            $this->reason = "Set XY:'$this->a'('$x','$y')\n";
        }
    }

    public function resetXY($x, $y) {
        global $db;

        $qry = "update t_stg set x=-1, pa=0 where u='$this->u' and s='$this->s' and n='$this->n' and x='$x' and y='$y'";
        $res = 	$db->query($qry);
        if(!$res){
            $this->rslt   = 'fail';
            $this->reason = mysqli_error($db);
        } 
        else {
            $this->rslt   = 'success';
            $this->reason = "Reset XY: " . $this->a . "(" . $x . "," . $y . ")\n";
        }
    }

    // return the y which $x is connected to
    public function getYx($x) {
        for ($i=0; $i<count($this->y); $i++) {
            if ($this->x[$i] == $x)
                return $this->y[$i];
        }
        return -1;
    }

    // return the x which $y is connected to
    public function getXy($y) {
            return $this->x[$y];
    }

    public function findListOfNextAvailS2($s1) {
        global $db;

        $e = explode(".",$s1);
        $a = $e[0] . '.' . $e[1] . '.' . $e[2];

        $qry = "SELECT a,x,y,d FROM t_stg WHERE a LIKE '$a.%' AND x=-1";
        $rows = [];
        $res = 	$db->query($qry);
        if(!$res){
            return $rows;
        } 
        else {
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            return $rows;
        }
    }

    public function findListOfNextAvailS3($s2) {
        global $db;

        $e = explode(".",$s2);
        $a = $e[0] . '.' . $e[1] . '.' . $e[2];

        $qry = "SELECT a,x,y,d FROM t_stg WHERE a LIKE '$a.%' AND x=-1";

        $rows = [];
        $res = 	$db->query($qry);
        if(!$res){
            return $rows;
        } 
        else {
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            return $rows;
        }
    }


    
    public function findListOfPossibleS6($s7) {
        global $db;

        $e = explode(".", $s7);
        $d = $e[0] . '.' . $e[1] . '.' . $e[2];

        $qry = "SELECT a,x,y,d FROM t_stg WHERE d LIKE '$d.%' AND x=-1";
        $rows = [];
        $res = 	$db->query($qry);
        if(!$res){
            return $rows;
        } 
        else {
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            return $rows;
        }
    }

    public function findListOfPossibleS5($s6) {
        global $db;

        $e = explode(".",$s6);
        $d = $e[0] . '.' . $e[1] . '.' . $e[2];

        $qry = "SELECT a,x,y,d FROM t_stg WHERE d LIKE '$d.%' AND x=-1";
        $rows = [];
        $res = 	$db->query($qry);
        if(!$res){
            return $rows;
        } 
        else {
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            return $rows;
        }
    }


}



class Stgs{

    public $rslt;
    public $reason;
    public $rows = [];


    public function __construct() {
        
    }

    // checks to see if path exists on given card
    public function queryForExistPath($card) {
        global $db;
    
        $qry = "SELECT pa FROM t_stg WHERE b = '$card' AND pa > 0";
        
        $res = 	$db->query($qry);
        $rows = [];
        if(!$res){
            $this->rslt   = 'fail';
            $this->reason = mysqli_error($db);
        } 
        else {
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rows = $rows;        
        }
    }

}



?>