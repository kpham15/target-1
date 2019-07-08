<?php
/*
    Filename: ipcFacClass.php

*/

    class FAC {
        public $id = 0;
        public $fac = "";
        public $ftyp = "";
        public $ort = "";
        public $spcfnc = "";
        public $port = "";
        public $port_id = 0;
        public $portObj = null;

        public $rows = [];
        public $rslt = "";
        public $reason = "";
        
        
        public function __construct($fac=NULL) {
            global $db;

            if ($fac == NULL) {
                $this->rslt = SUCCESS;
                return;
            }
            
            $qry = "SELECT * FROM t_facs WHERE fac='$fac' LIMIT 1";
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt     = FAIL;
                $this->reason   = mysqli_error($db);
            }
            else {
                $rows = [];
                if ($res ->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    $this ->id      = $rows[0]["id"];
                    $this ->fac     = $rows[0]["fac"];
                    $this ->ftyp    = $rows[0]["ftyp"];
                    $this ->ort     = $rows[0]["ort"];
                    $this ->spcfnc  = $rows[0]["spcfnc"];
                    $this ->port    = $rows[0]["port"];
                    $this ->port_id = $rows[0]["port_id"];
                    $this ->setPortObj();
                    $this ->rslt    = "success";
                    $this ->reason  = "FACILITY EXIST";
                }
                else {
                    $this ->rslt    = "fail";
                    $this ->reason  = "FACILITY DOES NOT EXIST";
                }
                $this->rows = $rows;
            }
        }
        
        public function setPortObj() {
            if ($this->port_id > 0) {
                $this->portObj = new PORT($this->port_id);
            }
        }
        

        public function query($fac=NULL) {
            global $db;

            if ($fac == NULL)
                $fac = "";

            $fac = str_replace('?','%',$fac);

            $qry = "SELECT * FROM t_facs WHERE fac LIKE '$fac' ORDER BY fac";
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt     = FAIL;
                $this->reason   = mysqli_error($db);
            }
            else {
                $rows = [];
                if ($res ->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    $this ->rslt    = SUCCESS;
                    $this ->reason  = QUERY_MATCHED;
                }
                else {
                    $this ->rslt    = FAIL;
                    $this ->reason  = QUERY_NOT_MATCHED;
                }
                $this->rows = $rows;
            }
        }

        public function findFacAll() {
            global $db;

            $qry = "SELECT * FROM t_facs ORDER BY fac";
            $res = $db->query($qry);
            if (!$res) {
                $this ->rslt    = FAIL;
                $this->reason = mysqli_error($db);
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $rows[] = $row;
                    }
                }
                $this->rows = $rows;
                $this->rslt = SUCCESS;
                $this->reason = "FIND_FAC_ALL";
            }
        }

        public function findFacLike($fac, $ftyp, $ort, $spcfnc, $psta) {
            global $db;

            $fac = str_replace('?','%',$fac);

            $qry = "SELECT t_facs.id, t_facs.fac, t_facs.ftyp, t_facs.ort, t_facs.spcfnc, t_facs.port, t_ports.psta";
            $qry .= " FROM t_facs LEFT JOIN t_ports ON t_facs.port_id = t_ports.id WHERE t_facs.fac LIKE '$fac'";
            $qry .= " AND t_facs.ftyp LIKE '$ftyp%' AND t_facs.ort LIKE '$ort%' AND t_facs.spcfnc LIKE '$spcfnc%' ORDER BY fac";

			//$qry = "SELECT * FROM t_facs WHERE fac LIKE '%$fac%' AND ftyp LIKE '%$ftyp%' AND ort LIKE '%$ort%' AND spcfnc LIKE '%$spcfnc%' ORDER BY fac";
            $res = $db->query($qry);
            if (!$res) {
                $this ->rslt    = FAIL;
                $this->reason = mysqli_error($db);
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        if ($row['psta'] == NULL)
                            $row['psta'] = 'UAS';

                        if ($psta != '' && $row['psta'] != $psta)
                            continue;
                        $rows[] = $row;
                    }
                }
                $this->rows = $rows;
                $this->rslt = SUCCESS;
                $this->reason = "FIND_FAC_ALL";
            }
        }

        public function findAvailFac() {
            global $db;

            $qry = "SELECT * FROM t_facs WHERE port_id=0";
            $res = $db->query($qry);
            if (!$res) {
                $this ->rslt    = FAIL;
                $this->reason = mysqli_error($db);
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $rows[] = $row;
                    }
                }
                $this->rows = $rows;
                $this->rslt = SUCCESS;
                $this->reason = "FIND_AVAIL_FAC";
            }

        }

        public function findFacByFtyp($ftyp, $ort, $spcfn) {
            global $db;

            $qry = "SELECT * FROM t_facs WHERE ftyp LIKE '$ftyp' AND ort LIKE '$ort' AND spcfnc LIKE '$spcfnc' ORDER BY fac"; 
            $res = $db->query($qry);
            if (!$res) {
                $this ->rslt    = FAIL;
                $this->reason = mysqli_error($db);
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $rows[] = $row;
                    }
                }
                $this->rows = $rows;
                $this->rslt = SUCCESS;
                $this->reason = "FIND_FAC_BY_FTYP";
            }
        }

        public function queryFacByPtyp($ptyp) {
            global $db;
            
            $qry = "SELECT t_facs.fac, t_facs.port FROM t_facs left join t_ports on t_facs.port_id=t_ports.id WHERE t_ports.ptyp='" . $ptyp . "' AND t_ports.psta='SF' ORDER BY fac";
        
            $res = $db->query($qry);
            if (!$res) {
                $this ->rslt    = FAIL;
                $this->reason = mysqli_error($db);
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $rows[] = $row;
                    }
                }
                $this->rows = $rows;
                $this->rslt = SUCCESS;
                $this->reason = QUERY_MATCHED;
            }
        }

        public function queryTestFacByNode($node) {
            global $db;
            
            $qry = "SELECT t_facs.fac, t_facs.port FROM t_facs left join t_ports on t_facs.port_id=t_ports.id WHERE t_ports.ptyp='Z' AND t_ports.psta='SF' AND t_ports.node='$node' ORDER BY fac";
        
            $res = $db->query($qry);
            if (!$res) {
                $this ->rslt    = FAIL;
                $this->reason = mysqli_error($db);
            }
            else {
                $rows = [];
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $rows[] = $row;
                    }
                }
                $this->rows = $rows;
                $this->rslt = SUCCESS;
                $this->reason = QUERY_MATCHED;
            }
        }

        
        public function add($fac, $ftyp, $ort, $spcfnc) {
            global $db;

            $lastFacCharPosition = strlen($fac) - 1;

            if ($fac == '') {
                $this->rslt = FAIL;
                $this->reason = "emtpy value error";
                return false;
            }

            // check dash in beginning and end
            if (strpos($fac, '-') == 0 || strpos($fac, '-') == $lastFacCharPosition) {
                $this->rslt = FAIL;
                $this->reason = "dash in begin or end";
                return false;
            }

            // check contiguous dash
            if (strpos($fac, '--') != false) {
                $this->rslt = FAIL;
                $this->reason = "contigous dash";
                return false;
            }

            // check white space
            if (preg_match('/\s/', $fac) == 1) {
                $this->rslt = FAIL;
                $this->reason = "white space";
                return false;
            }

            $ftypObj = new FTYP($ftyp);
            if ($ftypObj->rslt == FAIL) {
                $this->rslt = FAIL;
                $this->reason = $ftypObj->reason;
                return false;
            }
        
            $ortObj = new ORT($ort);
            if ($ortObj->rslt == FAIL) {
                $this->rslt = FAIL;
                $this->reason = $ortObj->reason;
                return false;
            }

            if ($spcfnc != '') {
                $spcfncObj = new SPCFNC($spcfnc);
                if ($spcfncObj->rslt == FAIL) {
                    $this->rslt = FAIL;
                    $this->reason = $spcfncObj->reason;
                    return false;
                }
            }
            
            if (preg_match('/^[a-zA-Z0-9-]+$/', $fac)) {
                $qry = "INSERT INTO 
                        t_facs 
                        (fac, ftyp, ort, spcfnc) 
                        VALUES 
                        ('$fac', '$ftyp', '$ort', '$spcfnc')";
                        
                $res = $db->query($qry);
                if (!$res) {
                    $this->rslt = FAIL;
                    $this->reason = mysqli_error($db);
                    return false;
                }
                else {
                    $this->rslt = SUCCESS;
                    $this->reason = 'FAC_ADDED';
                    $this->id = $db->insert_id;
                    $this->fac = $fac;
                    $this->ftyp = $ftyp;
                    $this->ort = $ort;
                    $this->spcfnc = $spcfnc;
                    $this->port = '';
                    $this->port_id = 0;
                    return true;
                }
            }
            else {
                $this->rslt = FAIL;
                $this->reason = "FAC_ID must not contain weird characters";
            }
        }

        public function update($ftyp, $ort, $spcfnc) {
            global $db;

            $qryArray = [];
            if ($ftyp == '') {
                $this->rslt = FAIL;
                $this->reason = "MISSING FTYP";
                return false;
            }
            else {
                $ftypObj = new FTYP($ftyp);
                if ($ftypObj->rslt == FAIL) {
                    $this->rslt = FAIL;
                    $this->reason = $ftypObj->reason;
                    return false;
                }
                $qryArray[] = "ftyp='$ftyp'";
            }

            if ($ort == '') {
                $this->rslt = FAIL;
                $this->reason = "MISSING ORT";
                return false;
            }
            else {
                $ortObj = new ORT($ort);
                if ($ortObj->rslt == FAIL) {
                    $this->rslt = FAIL;
                    $this->reason = $ortObj->reason;
                    return false;
                }
                $qryArray[] = "ort='$ort'";
            }

            // spcfnc can be empty as an option
            if ($spcfnc != '') {
                $spcfncObj = new SPCFNC();
                if ($spcfncObj->rslt == FAIL) {
                    $this->rslt = FAIL;
                    $this->reason = $spcfncObj->reason;
                    return false;
                }
            }
            $qryArray[] = "spcfnc='$spcfnc'";

            $len = count($qryArray);
            if ($len > 0) {
                $qry = "UPDATE t_facs SET ";
                for ($i=0; $i<$len; $i++) {
                    if ($i > 0)
                        $qry .= ",";
                    $qry .= $qryArray[$i];
                }
            }
            
            $qry .= " WHERE fac='$this->fac'";

            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = FAIL;
                $this->reason = mysqli_error($db);
            }
            else {
                $this->rslt = SUCCESS;
                $this->reason = 'FAC_UPDATED';
            }
        }

        public function delete($fac) {
            global $db;

            $port_id = 0;
            if ($fac != $this->fac) {
                $facObj = new FAC($fac);
                if ($facObj->rslt == "fail") {
                    $this->rslt = $facObj->rslt;
                    $this->reason = $facObj->reason;
                    return;
                }
                $port_id = $facObj->port_id;
            }
            else {
                $port_id = $this->port_id;
            }

            // fac must be unmapped from the port
            if ($port_id > 0) {
                $this->rslt = FAIL;
                $this->reason = "FAC_NOT_DELETED, MUST BE UNMAPPED FROM THE PORT FIRST";
                return;
            }

            $qry = "DELETE FROM t_facs WHERE fac='$fac'";
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = FAIL;
                $this->reason = mysqli_error($db);
            }
            else {
                if (mysqli_affected_rows($db) > 0) {
                    $this->rslt = SUCCESS;
                    $this->reason = 'FAC_DELETED';
                }
                else {
                    $this->rslt = FAIL;
                    $this->reason = 'INVALID_FAC';
                }
            }
        }

        public function updPortLink($port_id, $port) {
            global $db;

            $qry = "UPDATE t_facs SET port_id='$port_id', port='$port' WHERE id='$this->id'";
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = FAIL;
                $this->reason = mysqli_error($db);
                return false;
            }
            else {
                $this->rslt = SUCCESS;
                $this->reason = 'UPDATE_FAC_PORT_LINK';
                return true;
            }
        }
    }


?>
