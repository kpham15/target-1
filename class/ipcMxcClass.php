<?php
/*
 * Copy Right @ 2018
 * BHD Solutions, LLC.
 * Project: CO-IPC
 * Filename: coCommonFunctions.php
 * Change history: 
 * 2018-10-10: created (Ninh)
 * 2018-12-3: updated (Alex)
 * 2018-12-28: updated (Thanh)
 */


class MXC {
    public $id;
    public $node;
    public $shelf;
    public $slot;
    public $type;
    public $psta;
    public $ssta;

    public $nodes = 0;

    public $rslt;
    public $reason;
    public $rows;
    
    public function __construct($node=NULL, $shelf=NULL, $slot=NULL, $type=NULL) {
        global $db;

        $nodesObj = new NODES();
        $nodesObj->queryAll();
        $this->nodes = count($nodesObj->rows);
        
        if($node===NULL && $shelf===NULL && $slot===NULL && $type===NULL) {
            $this->rslt = SUCCESS;
            $this->reason = "MXC_CONSTRUCTED";
            return;
        }

        
        $qry = "SELECT * FROM t_mxc WHERE node='$node' AND node <= '$this->nodes' AND shelf='$shelf' AND slot='$slot' AND type='$type'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->id = $rows[0]["id"];
                $this->node = $rows[0]['node'];
                $this->shelf = $rows[0]['shelf'];
                $this->slot = $rows[0]['slot'];
                $this->type = $rows[0]['type'];
                $this->psta = $rows[0]["psta"];
                $this->ssta = $rows[0]["ssta"];
                $this->rslt = "success";
                $this->reason = "";
            }
            else {
                $this->rslt = "fail";
                $this->reason = "Invalid node:" . $this->node . ", shelf:" . $shelf . ",slot:" . $this->slot;
            }
        }
    }

    public function removed() {
        global $db;

        // 1) set evt= MC_OUT
        $evt = "MC_OUT";

        // 2) get npsta/nssta for this MXC
        // construct new SMS object with the passed through psta and ssta and evt which will return npsta and nssta
        $smsObj = new SMS($this->psta, $this->ssta, $evt);
		if ($smsObj->rslt == "fail") {
			$this->rslt = "fail";
			$this->reason = $smsObj->reason;
			return;
        }
        
        // 3) update t_mxc with npsta/nssta
        // update t_mxc with npsta/nssta that was returned from $smsObj
        $this->psta = $smsObj->npsta;
        $this->ssta = $smsObj->nssta;

		$qry = "UPDATE t_mxc SET psta = '$this->psta', ssta = '$this->ssta'
                 WHERE node = '$this->node' 
                 AND node <= '$this->nodes' 
                 AND   shelf = '$this->shelf' 
                 AND   slot = '$this->slot' 
                 AND   type = '$this->type'";	
		$res = $db->query($qry);
		if (!$res) {
			$this->rslt= "fail";
			$this->reason = mysqli_error($db);
			return;
		}

        // 4) go through each port of this MXC and update the port.psta/ssta in t_ports with its npsta/ssta
        // go through each port and update the psta/ssta in t_ports with npsta/nssta
        if ($this->type == 'MIOX') {
            $ptyp = 'X';
        }
        else if ($this->type == 'MIOY') {
            $ptyp = 'Y';
        }
        else {
            $this->rslt = "success";
            $this->reason = "REMOVE MATRIX CARD - " . $this->type;
            return;
        }

        $portObj = new PORT();
        $portObj->updatePortStatByNodeSlot($this->node, $this->slot, $ptyp, $evt);
        $this->rslt = $portObj->rslt;
        $this->reason = $portObj->reason;
        $this->rows = [];
        return;
    }

    public function inserted() {
        global $db;

        // 1) set evt= MC_OUT
        $evt = "MC_IN";

        // 2) get npsta/nssta for this MXC
        // construct new SMS object with the passed through psta and ssta and evt which will return npsta and nssta
        $smsObj = new SMS($this->psta, $this->ssta, $evt);
		if ($smsObj->rslt == "fail") {
			$this->rslt = "fail";
			$this->reason = $smsObj->reason;
			return;
        }
        
        // 3) update t_mxc with npsta/nssta
        // update t_mxc with npsta/nssta that was returned from $smsObj
		$qry = "UPDATE t_mxc SET psta = '$smsObj->npsta', ssta = '$smsObj->nssta'
                WHERE node = '$this->node' 
                AND node <= '$this->nodes' 
                AND   shelf = '$this->shelf' 
                AND   slot = '$this->slot' 
                AND   type = '$this->type'";	
		$res = $db->query($qry);
		if (!$res) {
			$this->rslt= "fail";
			$this->reason = mysqli_error($db);
			return;
		}

        // 4) go through each port of this MXC and update the port.psta/ssta in t_ports with its npsta/ssta
        // go through each port and update the psta/ssta in t_ports with npsta/nssta
        if ($this->type == 'MIOX') {
            $ptyp = 'X';
        }
        else if ($this->type == 'MIOY') {
            $ptyp = 'Y';
        }
        else {
            $this->rslt = "success";
            $this->reason = "INSERT MATRIX CARD - " . $this->type;
            return;
        }

        $portObj = new PORT();
        $portObj->updatePortStatByNodeSlot($this->node, $this->slot, $ptyp, $evt);
        $this->rslt = $portObj->rslt;
        $this->reason = $portObj->reason;
        $this->rows = [];
        return;
    }

    public function queryByNodeSlotTypeStat($node, $shelf, $slot, $type, $stat) {
        global $db;
        if ($node === "" && $shelf === "" && $slot === "" && $type === "" && $stat === "") {
            $qry = "SELECT * FROM t_mxc";
        }
        else {
            $qry = "SELECT * FROM t_mxc WHERE node = '$node' AND node <= '$this->nodes'";

            if ($shelf !== "") {
                $qry .= " AND shelf = '$shelf'";
            }
            if ($slot !== "") {
                $qry .= " AND slot = '$slot'";
            }
            
            $qry .= " AND type LIKE '%$type%' AND psta LIKE '%$stat%'";
        }
        
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rslt = "success";
            $this->reason = "MXC_QUERY_MATCHED";
            $this->rows = $rows;
        }
    }

    public function queryByNode($node) {
        global $db;
        $qry = "SELECT * FROM t_mxc WHERE node = '$node' AND node <= '$this->nodes'";
        
        $res = $db->query($qry);
        if (!$res) {
            
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
        }
        else {
            
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rslt = "success";
            $this->reason = "MXC_QUERY_MATCHED";
            $this->rows = $rows;
        }
    }

    public function lockMxc() {
        global $db;
     
        $evt = "MC_LOCK";
        
        $sms = new SMS($this->psta, $this->ssta, $evt);
        if ($sms->rslt == "fail") {
            $this->rslt = "fail";
            $this->reason = $sms->reason;
            return;
        }
        
        $qry = "UPDATE t_mxc SET psta='$sms->npsta',ssta='$sms->nssta' WHERE node='$this->node' AND node <= '$this->nodes' AND shelf='$this->shelf' AND slot='$this->slot' AND type='$this->type'";	
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }

        // go through each port and update the psta/ssta in t_ports with npsta/nssta
        if ($this->type == 'MIOX') {
            $ptyp = 'X';
        }
        else if ($this->type == 'MIOY') {
            $ptyp = 'Y';
        }
        else {
            $this->rslt = "success";
            $this->reason = "LOCK MATRIX CARD - " . $this->type;
            return;
        }

        $portObj = new PORT();
        $portObj->updatePortStatByNodeSlot($this->node, $this->slot, $ptyp, $evt);
        $this->rslt = $portObj->rslt;
        $this->reason = $portObj->reason;
        $this->rows = [];
        return;

    }

    public function unlockMxc() {
        global $db;
        
        $evt = "MC_UNLOCK";
        
        $sms = new SMS($this->psta, $this->ssta, $evt);
        if ($sms->rslt == "fail") {
            $this->rslt = "fail";
            $this->reason = $sms->reason;
            return;
        }
        
        $qry = "UPDATE t_mxc SET psta = '$sms->npsta', ssta = '$sms->nssta' WHERE node='$this->node' AND node <= '$this->nodes' AND shelf='$this->shelf' AND slot='$this->slot' AND type='$this->type'";	
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }

        // go through each port and update the psta/ssta in t_ports with npsta/nssta
        if ($this->type == 'MIOX') {
            $ptyp = 'X';
        }
        else if ($this->type == 'MIOY') {
            $ptyp = 'Y';
        }
        else {
            $this->rslt = "success";
            $this->reason = "UNLOCK MATRIX CARD - " . $this->type;
            return;
        }

        $portObj = new PORT();
        $portObj->updatePortStatByNodeSlot($this->node, $this->slot, $ptyp, $evt);
        $this->rslt = $portObj->rslt;
        $this->reason = $portObj->reason;
        $this->rows = [];
        return;

    }
}



?>