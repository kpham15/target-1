<?php

class STG {
    private $id = [];
    public $u = 0;
    public $s = "";
    public $n = 0;
    public $a = "";
    public $X = -1;
    public $Y = -1;
    public $x = [];
    public $y = [];
    public $d = [];
    public $da = "";
    public $dx = 0;
    public $rows = [];
    public $rslt = "";
    public $reason = "";
    
    private $maxIndexY = ['1A'=>9, '1B'=>9, '2A'=>9, '2B'=>9, '3A'=>2, '3B'=>2, '5A'=>4, '5B'=>4, '6A'=>9, '6B'=>9, '7A'=> 4, '7B'=>4];
    private $maxIndexX = ['1A'=>4, '1B'=>4, '2A'=>9, '2B'=>9, '3A'=>2, '3B'=>2, '5A'=>4, '5B'=>4, '6A'=>9, '6B'=>9, '7A'=> 9, '7B'=>9];

    public function __construct($stg=NULL){
        global $db;

        if($stg === NULL){
            $rslt = SUCCESS;
            $reason = STG_CONSTRUCTED;
            return;
        }

        $this->a = $stg;
        $inputArray = explode(".", $stg);
        if(count($inputArray) != 3) {
            $this->rslt    = FAIL;
            $this->reason  = INVALID_STG;
            return;
        }
        $u = (int)$inputArray[0];
        $s = $inputArray[1];
        $n = (int)$inputArray[2];

        $qry = "SELECT * FROM t_stg WHERE u= '$u' and s= '$s' and n= '$n'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt    = FAIL;
            $this->reason  = mysqli_error($db);
            return;
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                    $this->id[]   = $row["id"];
                    $this->u    = $row["u"];
                    $this->s    = $row["s"];
                    $this->n    = $row["n"];
                    $this->x[]    = $row["x"];
                    $this->y[]    = $row["y"];
                    $this->d[]    = $row["d"];
                }
        
                $this->rslt = SUCCESS;
                $this->reason= STG_CONSTRUCTED;
                
            }
            else {
                $this->rslt    = FAIL;
                $this->reason  = INVALID_STG;
            }
            $this->rows = $rows;
        }
    }

    public function query($u, $s, $n=NULL) {
        global $db;

        $qry = "SELECT * FROM t_stg WHERE u = '$u' AND s = '$s'";
        if($n !== NULL) {
            $qry .= " AND n = '$n'";
        }
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt    = FAIL;
            $this->reason  = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row; 
                }
                $this->rslt = SUCCESS;
                $this->reason= QUERY_MATCHED;    
            }
            else {
                $this->rslt    = FAIL;
                $this->reason  = QUERY_NOT_MATCHED;
            }
            $this->rows = $rows;
        }
    }

    public function setXByIndex($x, $i) {
        global $db;
        
        if(!(is_numeric($x) && $x>=0 && $x <= $this->maxIndexX[$this->s]) || $x == -1){
            $this->rslt    = FAIL;
            $this->reason  = INVALID_X;
            return;
        }
        if($i < 0 || $i > count($this->x)){
            $this->rslt    = FAIL;
            $this->reason  = INVALID_X_INDEX;
            return;
        }

        $this->x[$i] = $x;
        $id = $this->id[$i];
        $qry = "UPDATE t_stg SET x = '$x' WHERE u = '$this->u' AND s = '$this->s' AND n = '$this->n' AND id = '$id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt   = FAIL;
            $this->reason = mysqli_error($db);
            return;
        }
        if (mysqli_affected_rows($db) == 0) {
            $this->rslt   = FAIL;
            $this->reason = STG_NOT_UPDATED;
            return;
        }

        $this->rslt = SUCCESS;
        $this->reason = STG_UPDATED;
    }

    public function loadXY($x,$y) {
        if (in_array($x, $this->x)) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    public function findAvailY() {
        $this->Y = -1;
        $len = count($this->x);
        for ($i=0; $i<$len; $i++) {
            if ($this->x[$i] == -1) {
                $this->Y = $i;
                break;
            }
        }
        return $this->Y;
    }

}


class X {
    public $u;
    public $s;
    public $n;
    public $i;
    public $d;
    public $da;
    public $dx;

    public function __construct($u, $n, $i) {
        global $db;

        $qry = "select * from t_X where u=" . $u . " and n=" . $n . " and i=" . $i;
        $res = 	$db->query($qry);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $this->u = $row["u"];
                $this->s = $row["s"];
                $this->n = $row["n"];
                $this->i = $row["i"];
                $this->d = $row["d"];
                $spl = str_split($this->d, strlen($this->d)-2);
                $this->da = $spl[0];
                $spl = str_split($this->d, strlen($this->d)-1);
                $this->dx = $spl[1];
            }
        }
    }
}

class Y {
    public $u;
    public $s;
    public $n;
    public $i;
    public $d;
    public $da;
    public $dx;

    public function __construct($u, $n, $i) {
        global $db;
        $qry = "select * from t_Y where u=" . $u . " and n=" . $n . " and i=" . $i;
        $res = 	$db->query($qry);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $this->u = $row["u"];
                $this->s = $row["s"];
                $this->n = $row["n"];
                $this->i = $row["i"];
                $this->d = $row["d"];
                $spl = str_split($this->d, strlen($this->d) -2);
                $this->da = $spl[0];
                $spl = str_split($this->d, strlen($this->d) -1);
                $this->dx = $spl[1];
            }
        }
    }
}

class PA {
    public $X = [];
    public $Y = [];
    public $S1 = [];
    public $S2 = [];
    public $S3FS = [];
    public $S3FD = [];
    public $S4 = [];
    public $S3DS = [];
    public $S3DD = [];
    public $S5 = [];
    public $S6 = [];
    public $S7 = [];
    public $PATH = '';

    public function __construct($X, $Y) {
        $this->X["a"] = $X->u . '.' . $X->s . '.' . $X->n . '.' . $X->i;
        $this->Y["a"] = $Y->u . '.' . $Y->s . '.' . $Y->n . '.' . $Y->i;
    }
}




// $Xobj = new X(0,2,23);
// $Yobj = new Y(0,0,2);

// $pa = new PA($Xobj, $Yobj);
// $s1 = new STG($Xobj->da);

// $s7 = new STG($Yobj->da);

// // find avail s1(X,Y)
// if (in_array($Xobj->dx, $s1->x)) {
//     echo "s1: X=" . $Xobj->dx . " already in use\n";
//     return;
// }
// $s1->X = $Xobj->dx;

// $y = $s1->findAvailY();
// if ($y == -1) {
//     echo "s1: has no avail Y\n";
//     return;
// }
// $s1->Y = $y;
// $spl = str_split($s1->d[$y], strlen($s1->d[$y]) -2);
// $s1->da = $spl[0];
// $spl = str_split($s1->d[$y], strlen($s1->d[$y]) -1);
// $s1->dx = $spl[1];
// $pa->S1['a'] = $s1->a;
// $pa->S1['x'] = $s1->X;
// $pa->S1['Y'] = $s1->Y;
// print_r($pa->S1);


// // find avail s2(X,Y)
// $s2 = new STG($s1->da);
// if (in_array($s1->dx, $s2->x)) {
//     echo "s2: X=" . $s1->dx . " already in use\n";
//     return;
// }
// $s2->X = $s1->dx;
// $y = $s2->findAvailY();
// if ($y == -1) {
//     echo "S2: has no avail Y\n";
//     return;
// }
// $s2->Y = $y;
// $spl = str_split($s2->d[$y], strlen($s2->d[$y]) -2);
// $s2->da = $spl[0];
// $spl = str_split($s2->d[$y], strlen($s2->d[$y]) -1);
// $s2->dx = $spl[1];
// $pa->S2['a'] = $s2->a;
// $pa->S2['x'] = $s2->X;
// $pa->S2['Y'] = $s2->Y;
// print_r($pa->S2);



// // find avail s2(X,Y)
// $s3 = new STG($s2->da);
// if (in_array($s2->dx, $s3->x)) {
//     echo "s2: X=" . $s2->dx . " already in use\n";
//     return;
// }
// $s3->X = $s2->dx;
// $y = $s3->findAvailY();
// if ($y == -1) {
//     echo "S2: has no avail Y\n";
//     return;
// }
// $s3->Y = $y;
// $pa->S3['a'] = $s3->a;
// $pa->S3['x'] = $s3->X;
// $pa->S3['Y'] = $s3->Y;
// print_r($pa->S3);






// mysqli_close($db);



?>