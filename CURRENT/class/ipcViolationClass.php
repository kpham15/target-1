<?php 
    class VIO {
        public $vio_cnt = 0;
        public $last_vio = 0;
        public $rows = [];
        public $result;
        public $reason;

        public function __construct() {

        }

        public function setUnameViolation() {
            global $db;

            // get data from table
            $qry = "SELECT vio_cnt, last_vio FROM t_vio WHERE type='uname'";
            
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = "fail";
                $this->reason = mysqli_error($db);
                return false;
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    $this->vio_cnt = $rows[0]['vio_cnt'];
                    $this->last_vio = $rows[0]['last_vio'];
                }
            }

            // hard code the threshold and max violation count for now
            // set the current time as new violation time
            $vioThreshold = 600;
            $vioCntMax = 3;
            $newLast_vio = time();

            // compare time between current violation and last violation
            // if alm exists, add to counter only
            // if alm doesnt exist, record time and reset counter
        
            if ($newLast_vio - $this->last_vio > $vioThreshold) {
                $almObj = new ALMS('INV-USER');
                if (count($almObj->rows) > 0) {
                    $this->vio_cnt = $this->vio_cnt + 1;
                }
                else {
                    $this->last_vio = $newLast_vio;
                    $this->vio_cnt = 0;    
                }
            }
            else {
                $this->vio_cnt = $this->vio_cnt + 1;
            }


            // updates the count in the database
            $qry = "UPDATE t_vio SET vio_cnt = '$this->vio_cnt', last_vio = '$this->last_vio' WHERE type = 'uname'";
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = "fail";
                $this->reason = mysqli_error($db);
                return false;
            }

            // create alarm if violation count makes it to the maximum number
            if ($this->vio_cnt > $vioCntMax) {
                $almid = 'INV-USER';
                $almObj = new ALMS($almid);
                if (count($almObj->rows) == 0) {
                    $src = 'SECURITY ALARM';
                    $almtype = 'SECURITY';
                    $cond = 'INVALID USER';
                    $sa = 'N';
                    $sev = 'MIN';
                    $remark = 'INTRUSION - TOO MANY LOGIN ATTEMPTS';
                    $almObj = new ALMS();
                    $almObj->newAlm($almid, $src, $almtype, $cond, $sev, $sa, $remark);
                }
            }
            return true;
        }
    }  
?>