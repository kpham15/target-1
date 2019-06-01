<?php
/*  Filename: ipcCktconClass.php
    Date: 2018-11-20
    By: Ninh
    Copyright: BHD SOLUTIONS, LLC @ 2018
*/
    class CKT {
        
        public $id = 0;
        public $ckid = "";
        public $cls = "";
        public $adsr = "";
        public $prot = "";
        public $ordno = "";
        public $mlo = "";
        public $date = "";
        public $cktcon = 0;
        public $tktno = 0;

        public $rows = [];
        public $rslt = "";
        public $reason = "";
        
        public function __construct($ckid=NULL) {
            global $db;
            
            if ($ckid === NULL) {
                $this->rslt = SUCCESS;
                $this->reason = CKT_CONSTRUCTED;
                return;
            }

            $qry = "SELECT * FROM t_ckts WHERE ckid= '$ckid' LIMIT 1";
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
                    }
                    $this->id      = $rows[0]["id"];
                    $this->ckid    = $rows[0]["ckid"];
                    $this->cls     = $rows[0]["cls"];
                    $this->adsr    = $rows[0]["adsr"];
                    $this->prot    = $rows[0]["prot"];
                    $this->ordno   = $rows[0]["ordno"];
                    $this->mlo     = $rows[0]["mlo"];
                    $this->date    = $rows[0]["date"];
                    $this->cktcon  = $rows[0]["cktcon"];
                    $this->tktno   = $rows[0]["tktno"];
                    $this->rslt    = SUCCESS;
                    $this->reason  = CKT_CONSTRUCTED;
                    
                }
                else {
                    $this->rslt    = FAIL;
                    $this->reason  = "INVALID CKID";
                }
                $this->rows = $rows;
            }
        }

        public function queryCkid($ckid, $cls, $adsr, $prot) {

            global $db;
          
            $qry = "SELECT * FROM t_ckts WHERE ckid LIKE '%$ckid%' AND cls LIKE '%$cls%' AND adsr LIKE '%$adsr%' AND prot LIKE '%$prot%'";
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
                        if ($row['ckid'] == $ckid) {
                            $this->id      = $row["id"];
                            $this->ckid    = $row["ckid"];
                            $this->cls     = $row["cls"];
                            $this->adsr    = $row["adsr"];
                            $this->prot    = $row["prot"];
                            $this->ordno   = $row["ordno"];
                            $this->mlo     = $row["mlo"];
                            $this->date    = $row["date"];
                            $this->cktcon  = $row["cktcon"];
                            $this->tktno   = $row["tktno"];
                            $this->rslt    = SUCCESS;
                            $this->reason  = QUERY_MATCHED;
                        }
                        $rows[] = $row;
                    }
                }

                $this->rows = $rows;
                $this->rslt = SUCCESS;
                $this->reason = "QUERY BY CKID";
            }
        }


        public function updateCktStat($stat) {
            global $db;

            $qry = "UPDATE t_ckts SET stat='$stat' WHERE ckid='$this->ckid'";
            
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt   = FAIL;
                $this->reason = mysqli_error($db);
                return;
            }
            
            $this->stat = $stat;
            
            $this->rslt = SUCCESS;
            $this->reason = "CKT STAT UPDATED";
            return;
        }


        public function updateCkt($cls, $adsr, $prot, $ordno, $mlo) {
            global $db;

            if ($this->ckid == '') {
                $this->rslt = FAIL;
                $this->reason = INVALID_CTID;
                return;
            }

            if (!in_array($cls, CLS_LST)) {
                $this->rslt = FAIL;
                $this->reason = INVALID_CLS;
                return;
            };
          
            if ($adsr == "") {
                $adsr = "N";
            }
            
            if (!in_array($adsr, ADSR_LST)) {
                $this->rslt = FAIL;
                $this->reason = INVALID_ADSR;
                return;
            };

            
            if (!in_array($prot, PROT_LST)) {
                $this->rslt = FAIL;
                $this->reason = INVALID_PROT;
                return;
            }

            $qry = "UPDATE t_ckts SET cls='$cls', adsr='$adsr', prot='$prot', date=now() WHERE ckid='$this->ckid'";
            
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt   = FAIL;
                $this->reason = mysqli_error($db);
                return;
            }
            
            $this->cls = $cls;
            $this->adsr = $adsr;
            $this->prot = $prot;
            
            $this->rslt = SUCCESS;
            $this->reason = CKT_UPDATED;
            
        }

        public function setCktcon($cktcon) {
            global $db;

            if (!is_numeric($cktcon) || (int)$cktcon < 0 ) {
                $this->rslt = FAIL;
                $this->reason = INVALID_CKTCON;
                return;
            }
            
            $qry = "UPDATE t_ckts SET cktcon='$cktcon' WHERE ckid='$this->ckid'";
            
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt   = FAIL;
                $this->reason = mysqli_error($db);
                return;
            }
            
            $this->cktcon = $cktcon;
            $this->rslt = SUCCESS;
        }


        public function addCkt($ckid, $cls, $adsr, $prot, $ordno, $mlo, $stat) {
            global $db;

            if ($ckid == "") {
                $this->rslt = FAIL;
                $this->reason = INVALID_CTID;
                return;
            }

            if (!in_array($cls, CLS_LST)) {
                $this->rslt = FAIL;
                $this->reason = INVALID_CLS;
                return;
            }

            if ($adsr == ""){
                $adsr = "N";
            }
            
            if (!in_array($adsr, ADSR_LST)) {
                $this->rslt = FAIL;
                $this->reason = INVALID_ADSR;
                return;
            };

            
            if (!in_array($prot, PROT_LST)) {
                $this->rslt = FAIL;
                $this->reason = INVALID_PROT;
                return;
            }

            if ($ordno == "") {
                $this->rslt = FAIL;
                $this->reason = INVALID_ORDNO;
                return;
            }

            if ($mlo == "") {
                $mlo = "N";
            }

            if (!in_array($mlo, MLO_LST)) {
                $this->rslt = FAIL;
                $this->reason = INVALID_MLO;
                return;
            };

            // setup new ckt
            // $qry = "INSERT INTO t_ckts values(0,'$ckid','$cls','$adsr','$prot','$ordno','$mlo',now(),0,'$stat')";
            $qry = "INSERT INTO 
                    t_ckts 
                    (ckid, cls, adsr, prot, ordno, 
                    mlo, stat) 
                    VALUES 
                    ('$ckid', '$cls', '$adsr', '$prot', '$ordno', 
                    '$mlo', '$stat')";
            
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = FAIL;
                $this->reason = mysqli_error($db);
                return;
            }
            $this->id      = $db->insert_id;;
            $this->ckid    = $ckid;
            $this->cls     = $cls;
            $this->adsr    = $adsr;
            $this->prot    = $prot;
            $this->ordno   = $ordno;
            $this->mlo     = $mlo;
            $this->date    = $time;
            $this->cktcon  = 0;
            $this->rslt = SUCCESS;
            $this->reason = CKT_ADDED;
        }


        public function deleteCkt($ckid) {
            global $db;

            if ($ckid == "") {
                $this->rslt = FAIL;
                $this->reason = INVALID_CTID;
                return;
            }

            $qry = "DELETE FROM t_ckts WHERE ckid = '$ckid'";
            // $qry = "UPDATE t_ckts SET cktcon=0, stat='DISCONNECT', date=now() WHERE ckid = '$ckid'";
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = FAIL;
                $this->reason = mysqli_error($db);
                return;
            }
            $this->id = 0;
            $this->rslt = SUCCESS;
            $this->reason = "CKT DISCONNECTED";
        }

        public function setTktno($tktno) {
            global $db;
            
            $qry = "UPDATE t_ckts SET tktno='$tktno' WHERE ckid='$this->ckid'";
            
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt   = FAIL;
                $this->reason = mysqli_error($db);
                return;
            }

            $this->tktno = $tktno;
            $this->rslt = SUCCESS;
        }
    }
        
    
    // class ORDER
    class ORDER {
        
        public $rows = [];
        public $rslt = '';
        public $reason = '';

        public function __construct() {

            $this->rows = [];
            $this->rslt = 'success';
            $this->reason = 'CONSTRUCT ORDER';
        }

        public function queryOrder($ordno) {
            global $db;

            $qry = "SELECT * FROM t_orders WHERE ordno = '$ordno%'";
            
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt    = FAIL;
                $this->reason  = mysqli_error($db);
                return false;
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) { 
                        $rows[] = $row;   
                    } 
                }
                
                $this->rslt    = SUCCESS;
                $this->reason  = "QUERY ORDERS";
                $this->rows = $rows;
                return true;
            }
        }

        public function updateOrderStat($ordno, $stat, $ckid, $ffac, $tfac) {
            global $db;

            $qry = "UPDATE t_orders SET ordno='$ordno', stat='$stat', date=now() WHERE ckid='$ckid' AND ffac='$ffac' AND tfac='$tfac'";
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt    = FAIL;
                $this->reason  = mysqli_error($db);
                return false;
            }
            
            $this->rslt    = SUCCESS;
            $this->reason  = "UPDATE ORDER";
            $this->rows = [];
            return true;
        }
    }
    



?>