<?php

class WC {
    public $id          = 0;
    public $wcname      = "";
    public $wcc         = "";
    public $clli        = "";
    public $npanxx      = "";
    public $tzone       = "";
    public $tz          = 0;
    public $frloc       = "";
    public $ipcmod      = "";
    public $ipctyp      = "";
    public $stat        = "";
    public $nodes       = "";
    public $termid      = "";
    public $addr        = "";
    public $city        = "";
    public $state       = "";
    public $zip         = "";
    public $gps         = "";
    public $gw          = "";
    public $ipadr       = "";
    public $iport       = 0;
    public $netmask     = "";
    public $gateway     = "";
    public $mainthour   = 0;
    public $company     = "";
    public $region      = "";
    public $area        = "";
    public $district    = "";
    public $manager     = "";
    
    public $rslt        = "";
    public $reason      = "";
    public $rows        = [];

    public function __construct($id=NULL) {
        global $db;

        if ($id == NULL) {
            $qry = "SELECT * FROM t_wc ORDER BY id ASC";
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

                    $this->rslt      = SUCCESS;
                    $this->reason    = QUERY_MATCHED;
                    $this->rows      = $rows;
                    $this->id        = $rows[1]['id'];
                    $this->wcname    = $rows[1]['wcname'];
                    $this->wcc       = $rows[1]['wcc'];
                    $this->clli      = $rows[1]['clli'];
                    $this->npanxx    = $rows[1]['npanxx'];
                    $this->tzone     = $rows[1]['tzone'];
                    $this->tz        = $rows[1]['tz'];
                    $this->frloc     = $rows[1]['frloc'];
                    $this->ipcmod    = $rows[1]['ipcmod'];
                    $this->ipctyp    = $rows[1]['ipctyp'];
                    $this->stat      = $rows[1]['ipcstat'];
                    $this->nodes     = $rows[1]['nodes'];
                    $this->termid    = $rows[1]['termid'];
                    $this->addr      = $rows[1]['loc'];
                    $this->city      = $rows[1]['city'];
                    $this->state     = $rows[1]['state'];
                    $this->zip       = $rows[1]['zip'];
                    $this->gps       = $rows[1]['gps'];
                    $this->gw        = $rows[1]['gw'];
                    $this->ipadr     = $rows[1]['ipadr'];
                    $this->iport     = $rows[1]['iport'];
                    $this->netmask   = $rows[1]['netmask'];
                    $this->gateway   = $rows[1]['gateway'];
                    $this->mainthour = $rows[1]['mainthour'];
                    $this->company   = $rows[1]['company'];
                    $this->region    = $rows[1]['region'];
                    $this->area      = $rows[1]['area'];
                    $this->district  = $rows[1]['district'];
                    $this->manager   = $rows[1]['manager'];
                }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
        else {
            $qry = "SELECT * FROM t_wc
                    WHERE id = '$id'
                    LIMIT 1
                    ";
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
                    $this->rslt      = SUCCESS;
                    $this->reason    = QUERY_MATCHED;
                    $this->rows      = $rows;
                    $this->id        = $rows[0]['id'];
                    $this->wcname    = $rows[0]['wcname'];
                    $this->wcc       = $rows[0]['wcc'];
                    $this->clli      = $rows[0]['clli'];
                    $this->npanxx    = $rows[0]['npanxx'];
                    $this->tzone     = $rows[0]['tzone'];
                    $this->tz        = $rows[0]['tz'];
                    $this->frloc     = $rows[0]['frloc'];
                    $this->ipcmod    = $rows[0]['ipcmod'];
                    $this->ipctyp    = $rows[0]['ipctyp'];
                    $this->stat      = $rows[0]['ipcstat'];
                    $this->termid    = $rows[0]['termid'];
                    $this->addr      = $rows[0]['loc'];
                    $this->city      = $rows[0]['city'];
                    $this->state     = $rows[0]['state'];
                    $this->zip       = $rows[0]['zip'];
                    $this->gps       = $rows[0]['gps'];
                    $this->gw        = $rows[0]['gw'];
                    $this->ipadr     = $rows[0]['ipadr'];
                    $this->iport     = $rows[0]['iport'];
                    $this->netmask   = $rows[0]['netmask'];
                    $this->gateway   = $rows[0]['gateway'];
                    $this->mainthour = $rows[0]['mainthour'];
                    $this->company   = $rows[0]['company'];
                    $this->region    = $rows[0]['region'];
                    $this->area      = $rows[0]['area'];
                    $this->district  = $rows[0]['district'];
                    $this->manager   = $rows[0]['manager'];
                    
                        }
                else {
                    $this->rslt   = FAIL;
                    $this->reason = QUERY_NOT_MATCHED;
                    $this->rows   = $rows;
                }
            }
        }
    }

    public function query($id) {
        global $db;

        $qry = "SELECT * FROM t_wc WHERE id LIKE '$id'";
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

    public function queryWc() {
        global $db;

        $qry = "SELECT * FROM t_wc";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            $this->rslt = "success";
            $this->reason = "QUERY_SUCCESS";
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rows = $rows;
        }
    }

    public function updateWc($wcname, $wcc, $clli, $npanxx, 
    $frloc, $tzone, $stat, $addr, $city, $state, $zip, $gps, 
    $company, $region, $area, $district, $manager, $id) {

        global $db;

        //NPANXX validation must be 6 digits
        if (ctype_digit($npanxx) == false || strlen($npanxx)!=6) {
            $this->rslt = FAIL;
            $this->reason = "INVALID NPANXX";
            return; 
        }
        //END NPANXX validation
        
        // set timezone offset
        if ($tzone == 'PST')        // Pacific Standard Time
            $tz = -8;
        else if ($tzone == 'MST')   // Mountain Standard Time
            $tz = -7;
        else if ($tzone == 'CST')   // Central Standard Time
            $tz = -6;
        else if ($tzone == 'EST')   // Estern Standard Time
            $tz = -5;
        else if ($tzone == 'AKST')  // Alaska Standard Time
            $tz = -9;
        else if ($tzone == 'HST')   // Hawaii Standard Time
            $tz = -10;
        else
            $tz = 0;

        // "update t_wc set ipcstat='$ipcstat' where wcname='$wcname' and wcc='$wcc' and ....."
        $qry = "UPDATE t_wc SET
                 wcname     = '$wcname',
                 wcc        = '$wcc',
                 clli       = '$clli',
                 npanxx     = '$npanxx',
                 frloc      = '$frloc',
                 tzone      = '$tzone',
                 tz         = '$tz',
                 loc        = '$addr',
                 city       = '$city',
                 state      = '$state',
                 zip        = '$zip',
                 gps        = '$gps',
                 company    = '$company',
                 region     = '$region',
                 area       = '$area',
                 district   = '$district',
                 manager    = '$manager' 
                 WHERE id   = '$id'
                 ";

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $this->queryWc();
            $this->rslt = "success";
            $this->reason = "WC_UPDATED";
        }
    }

    public function updateNetwork( $ipadr, $gateway, $netmask, $iport, $id) {

        global $db;

        if (filter_var($ipadr, FILTER_VALIDATE_IP) === false) {
            $this->rslt = FAIL;
            $this->reason = "Invalid IP Address";
            return; 
        }
        if (filter_var($gateway, FILTER_VALIDATE_IP) === false) {
            $this->rslt = FAIL;
            $this->reason = "Invalid Gateway";
            return;
        }
        if (filter_var($netmask, FILTER_VALIDATE_IP) === false) {
            $this->rslt = FAIL;
            $this->reason = "Invalid Netmask";
            return;
        }
        
        if (filter_var($iport, FILTER_VALIDATE_INT) === false) {
            $this->rslt = FAIL;
            $this->reason = "Invalid IP Port";
            return; 
        }

        if ($iport <= 9000 || $iport >= 10000 ) {
            $this->rslt = FAIL;
            $this->reason = "Invalid IP_PORT";
            return;
        }

        $qry = "UPDATE t_wc SET
                 ipadr      = '$ipadr',
                 gateway    = '$gateway',
                 netmask    = '$netmask',
                 iport      = '$iport' 
                --  make so id = 2 only! --> WHERE id = 2
                 WHERE id   = '$id'
                 ";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $this->queryWc();
            $this->rslt = "success";
            $this->reason = "WC_UPDATED";
        }
    }

    public function resetWc($id) {
        global $db;

        $qry = "SELECT * FROM t_wc WHERE id = 1";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            $this->rslt = "success";
    
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->wcname    = $rows[0]['wcname'];
            $this->wcc       = $rows[0]['wcc'];
            $this->clli      = $rows[0]['clli'];
            $this->npanxx    = $rows[0]['npanxx'];
            $this->tzone     = $rows[0]['tzone'];
            $this->frloc     = $rows[0]['frloc'];
            $this->ipcmod    = $rows[0]['ipcmod'];
            $this->ipctyp    = $rows[0]['ipctyp'];
            $this->stat      = $rows[0]['ipcstat'];
            $this->termid    = $rows[0]['termid'];
            $this->addr      = $rows[0]['loc'];
            $this->city      = $rows[0]['city'];
            $this->state     = $rows[0]['state'];
            $this->zip       = $rows[0]['zip'];
            $this->gps       = $rows[0]['gps'];
            $this->gw        = $rows[0]['gw'];
            $this->ipadr     = $rows[0]['ipadr'];
            $this->iport     = $rows[0]['iport'];
            $this->netmask   = $rows[0]['netmask'];
            $this->gateway   = $rows[0]['gateway'];
            $this->mainthour = $rows[0]['mainthour'];
            $this->company   = $rows[0]['company'];
            $this->region    = $rows[0]['region'];
            $this->area      = $rows[0]['area'];
            $this->district  = $rows[0]['district'];
            $this->manager   = $rows[0]['manager'];

            $qry = "UPDATE t_wc SET 
                    wcname    = '$this->wcname', 
                    wcc       = '$this->wcc', 
                    clli      = '$this->clli', 
                    npanxx    = '$this->npanxx', 
                    frloc     = '$this->frloc', 
                    tzone     = '$this->tzone', 
                    ipcmod    = '$this->ipcmod', 
                    ipctyp    = '$this->ipctyp', 
                    ipcstat   = '$this->stat', 
                    termid    = '$this->termid', 
                    loc       = '$this->addr', 
                    city      = '$this->city', 
                    state     = '$this->state', 
                    zip       = '$this->zip', 
                    gps       = '$this->gps', 
                    gw        = '$this->gw', 
                    ipadr     = '$this->ipadr', 
                    iport     = '$this->iport', 
                    netmask   = '$this->netmask', 
                    gateway   = '$this->gateway', 
                    mainthour = '$this->mainthour', 
                    company   = '$this->company', 
                    region    = '$this->region', 
                    area      = '$this->area', 
                    district  = '$this->district', 
                    manager   = '$this->manager' 
                    where id  = '$id'
                    ";

            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = "fail";
                $this->reason = mysqli_error($db);
            }
            else {
                $this->queryWc();
                $this->rslt= "success";
                $this->reason = "WC_RESET_SUCCESS";
            }
        }
    }

    public function turnup($wcname, $wcc, $clli, $npanxx, $frloc) {
        global $db;

            // Enter the "AND wcname AND wcc AND clli ... etc."
          $qry = "UPDATE t_wc SET ipcstat='INS' WHERE id = 2 AND wcname='$wcname' AND wcc='$wcc' AND clli='$clli' AND npanxx='$npanxx' AND frloc='$frloc'";
          $res = $db->query($qry);
          if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
          }
          else {
            $rows = [];
            $this->rslt = "success";
            $this->reason = "WC TURN UP";
          }
    }

    public function holdWc($wcname, $wcc, $clli, $npanxx, $frloc) {
        global $db;

          $qry = "UPDATE t_wc SET ipcstat='OOS' WHERE id = 2 AND wcname='$wcname' AND wcc='$wcc' AND clli='$clli' AND npanxx='$npanxx' AND frloc='$frloc'";
          $res = $db->query($qry);
          if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
          }
          else {
            $rows = [];
            $this->rslt = "success";
            $this->reason = "WC HOLD";
          }
        
    }
    public function getWCTime() {
        global $db;

		// establish ipc_time
		date_default_timezone_set("UTC");
		$utc_tz = date_default_timezone_get();
		$utc_t = time();
		$ipc_tz_offset = $this->tz * 3600;
		$ipc_t = $utc_t + ($this->tz * 3600);

		$secondSundayInMarch = date("d-M-Y", strtotime("second sunday " . date('Y') . "-03"));
		$firstSundayInNovember = date("d-M-Y", strtotime("first sunday " . date('Y') . "-11"));
		$dst_begin_t = strtotime($secondSundayInMarch) + $ipc_tz_offset;
		$dst_end_t = strtotime($firstSundayInNovember) + $ipc_tz_offset;

		$ipc_tzone = $this->tzone;
		if ($ipc_t > $dst_begin_t && $ipc_t < $dst_end_t) {
			if (date('I', $ipc_t) == 0) {
				$ipc_t = $ipc_t + 3600;
				$ipc_tzone = substr_replace($this->tzone,"DT",-2);
			}
		}
		$ipc_time = date("Y-m-d_H:i:s", $ipc_t);
        return $ipc_time;
    }

/**
 * end of class
 */
}

?>