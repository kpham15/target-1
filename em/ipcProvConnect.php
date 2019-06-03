<?php
/*  Filename: ipcProvConnect.php
    Date: 2018-12-14
    By: Ninh
    Copyright: BHD SOLUTIONS, LLC @ 2018
*/  
        
    
    function provConnect($userObj, $ordno, $mlo, $ckid, $cls, $adsr, $prot, $ctyp, $ffac, $tfac) {
        try {
    
            $newckt = false;        
            
            $result['log'] = "ACTION = $act | ORDNO = $ordno | MLO = $mlo | CKID = $ckid | CLS = $cls | ADSR = $adsr | PROT = $prot | CONTYP = $ctyp | FAC(X) = $ffac | FAC(Y) = $tfac";

            if ($userObj->grpObj->prov != "Y") {
                $result['rslt'] = 'fail';
                $result['jeop'] = 'SP5:PERMISSION DENIED'; 
                $result['reason'] = "PROVISIONING CONNECT - " . 'PERMISSION DENIED';
                return $result;
            }
        
            // Check REF table for auto_ordno & auto_ckid
            $refObj = new REF();
            if ($ordno == '' || $ordno == null) {
                if ($refObj->ref[0]['auto_ordno'] == 'Y') {
                    $time = new DateTime('now');
                    $timestr = $time->format('H:i:s');
                    $ordno = strtoupper(substr($userObj->uname, 0, 4)) . $timestr;
                } else {
                    $result['rslt'] = 'fail';
                    $result['reason'] = "PROVISIONING CONNECT - " . "ORDNO MISSING & AUTO_ORDNO SET TO 'N'";
                    return $result;
                }
            }
    
            if ($ckid == '' || $ckid == null) {
                if ($refObj->ref[0]['auto_ckid'] == 'Y') {
                    $ckid = $ffac;
                } else {
                    $result['rslt'] = 'fail';
                    $result['reason'] = "PROVISIONING CONNECT - " . "CKID MISSING & AUTO_CKID SET TO 'N'";
                    return $result;
                }
            }
                
            // if ckid is not in DB, then this is a new CKT
            $cktObj = new CKT($ckid);
            if ($cktObj->rslt == FAIL) {
                $newckt = true;
            }

            if ($newckt == true) {
                // if MLO=N then ORDNO must not already existed
                if (($mlo == '' || $mlo == 'N') && $ordno !="") {
                    $ordObj = new CKT();
                    $ordObj->queryCkidByOrdno($ordno);
                    if (count($ordObj->rows) > 0) {
                        $result['rslt'] = 'fail';
                        $result['jeop'] = 'SP2: INVALID ORDNO'; 
                        $result['reason'] = "PROVISIONING CONNECT - " . 'ORDNO ALREADY EXISTS (MLO=N)';
                        return $result;    
                    }
                }

                // cls must be specified
                if (!in_array($cls,CLS_LST)) {
                    $result['rslt'] = "fail";
                    $result['jeop'] = 'A3:MISSING CLS'; 
                    $result['reason'] = "PROVISIONING CONNECT - " . "MISSING CLS";
                    return $result;
                }
            }

            if ($ffac == "") {
                $result['rslt'] = "fail";
                $result['jeop'] = 'SP2:MISSING FAC';
                $result['reason'] = "PROVISIONING CONNECT - " . "MISSING FAC(X)";
                return $result;
            }
            // the ffac must exist in DB and must be currently mapped
            $ffacObj = new FAC($ffac);
            if ($ffacObj->rslt == FAIL) {
                $result['rslt'] = "fail";
                $result['jeop'] = 'SP2:INVALID FAC';
                $result['reason'] = "PROVISIONING CONNECT - " . "FAC(X) " . $ffac . " DOES NOT EXIST";
                return $result;
            }
            
            if ($ffacObj->port_id == 0) {
                $result['rslt'] = "fail";
                $result['jeop'] = 'SP3:FAC UNQ';
                $result['reason'] = "PROVISIONING CONNECT - " . "FAC(X) " . $ffac . " IS NOT MAPPED TO A PORT";
                return $result;
            }
            
            if ($ffacObj->portObj->ptyp != "X") {
                $result['rslt'] = "fail";
                $result['jeop'] = 'SP4:FAC IS MAPPED TO DIFFERENT PORT';
                $result['reason'] = "PROVISIONING CONNECT - " . "FAC(X) " . $ffac . " IS NOT MAPPED TO X-PORT";
                return $result;
            }
            $ffacObj->setPortObj();

            if ($tfac == "") {
                $result['rslt'] = "fail";
                $result['jeop'] = 'SP2:MISSING FAC';
                $result['reason'] = "PROVISIONING CONNECT - " . "MISSING FAC(Y)";
                return $result;
            }
            // the tfac must exist in DB and must be currently mapped
            $tfacObj = new FAC($tfac);
            if ($tfacObj->rslt == FAIL) {
                $result['rslt'] = "fail";
                $result['jeop'] = 'SP2:INVALID FAC';
                $result['reason'] = "PROVISIONING CONNECT - " . "FAC(Y) " . $tfac . " DOES NOT EXIST";
                return $result;
            }
            
            if ($tfacObj->port_id == 0) {
                $result['rslt'] = "fail";
                $result['jeop'] = 'SP3:FAC UNQ';
                $result['reason'] = "PROVISIONING CONNECT - " . "FAC(Y) " . $tfac . " IS NOT MAPPED TO A PORT";
                return $result;
            }

            if ($tfacObj->portObj->ptyp != "Y") {
                $result['rslt'] = "fail";
                $result['jeop'] = 'SP4:FAC IS MAPPED TO DIFFERENT PORT';           
                $result['reason'] = "PROVISIONING CONNECT - " . "FAC(Y) " . $tfac . " IS NOT MAPPED TO Y-PORT";
                return $result;
            }
            $tfacObj->setPortObj();

            // allow only CTYP=GEN for now (i.e. X-port and Y-port)
            // if ($ctyp != 'GEN' && $ctyp != 'MLPT') {
            if ($ctyp != 'GEN') {
                $result["rslt"] = FAIL;
                $result['jeop'] = "SP5:INVALID CONTYP ($ctyp)";
                $result["reason"] = "PROVISIONING CONNECT - " . "CONTYP: " . $ctyp . " is not supported yet";
                return $result;
            }

            if ($ffacObj->portObj->ptyp != 'X') {
                $result["rslt"] = FAIL;
                $result['jeop'] = 'SP4:FAC IS MAPPED TO DIFFERENT PORT';            
                $result["reason"] = "PROVISIONING CONNECT - " . "FAC(X):" . $ffacObj->fac . " IS NOT MAPPED TO A X-PORT";
                return $result;
            }

            if ($tfacObj->portObj->ptyp != 'Y') {
                $result["rslt"] = FAIL;
                $result['jeop'] = 'SP4:FAC IS MAPPED TO DIFFERENT PORT';            
                $result["reason"] = "PROVISIONING CONNECT - " . "FAC(Y):" . $ffacObj->fac . " IS NOT MAPPED TO A Y-PORT";
                return $result;
            }

            if ($ffacObj->portObj->ckt_id > 0) {
                $result["rslt"] = FAIL;
                $result['jeop'] = 'SP4:FAC IS ALREADY IN CKT';            
                $result["reason"] = "PROVISIONING CONNECT - " . "FAC(X) " . $ffac . " IS ALREADY IN OTHER CKT CONNECTION";
                return $result;
            }
            
            if ($tfacObj->portObj->ckt_id > 0) {
                $result["rslt"] = FAIL;
                $result['jeop'] = 'SP4:FAC IS ALREADY IN CKT';             
                $result["reason"] = "PROVISIONING CONNECT - " . "FAC(Y) " . $tfac . " IS ALREADY IN OTHER CKT CONNECTION";
                return $result;
            }
            
            // extract node number from portObj
            $fNode = $ffacObj->portObj->node;
            $tNode = $tfacObj->portObj->node;

            // get fromNode stat and toNode stat
            $fNodeObj = new NODE($fNode);
            $tNodeObj = new NODE($tNode);

            // deny action if fnode is not in service
            if ($fNodeObj->stat !== 'INS') {
                $result['rslt'] = 'fail';
                $result['jeop'] = "SP5:ACCESS DENIED; NODE ($fNode) HAS BEEN LOCKED BY SYSTEM ADMINISTRATOR $fNodeObj->user";            
                $result['reason'] = "ACCESS DENIED; NODE ($fNode) HAS BEEN LOCKED BY SYSTEM ADMINISTRATOR $fNodeObj->user";
                return $result;
            }

            // deny action if tnode is not in service
            if ($tNodeObj->stat !== 'INS') {
                $result['rslt'] = 'fail';
                $result['jeop'] = "SP5:ACCESS DENIED; NODE ($fNode) HAS BEEN LOCKED BY SYSTEM ADMINISTRATOR $fNodeObj->user";            
                $result['reason'] = "ACCESS DENIED; NODE ($tNode) HAS BEEN LOCKED BY SYSTEM ADMINISTRATOR $tNodeObj->user";
                return $result;
            }


            // validate state-event            $result['jeop'] = 'SP4';            

            $sms = new SMS($ffacObj->portObj->psta, $ffacObj->portObj->ssta, "SV_CONN");
            if ($sms->rslt == FAIL) {
                $result['rslt'] = FAIL;
                $result['jeop'] = "SP3:FAC STATUS (".$ffacObj->portObj->psta.")";            
                $result['reason'] = "PROVISIONING CONNECT - " . $sms->reason;
                return $result;
            }
            $ffacObj->portObj->npsta = $sms->npsta;
            $ffacObj->portObj->nssta = $sms->nssta;

            $sms = new SMS($tfacObj->portObj->psta, $tfacObj->portObj->ssta, "SV_CONN");
            if ($sms->rslt == FAIL) {
                $result['rslt'] = FAIL;
                $result['jeop'] = "SP3:FAC STATUS (".$tfacObj->portObj->psta.")";            
                $result['reason'] = "PROVISIONING CONNECT - " . $sms->reason;
                return $result;
            }
            $tfacObj->portObj->npsta = $sms->npsta;
            $tfacObj->portObj->nssta = $sms->nssta;

            $path_id = 0;
            
            /* this section will be enabled when testing of 100% non-blocking is done */
            // create PATH. The t_pathXY requires all stages to be unique, so createPath() can return fail if 
            // any of the stage are duplicated.
            $pathObj = new PATH($ffacObj->portObj->port, $tfacObj->portObj->port);
            $pathObj->createPath();
            if($pathObj->rslt == 'fail') {
                $result['rslt'] = 'fail'; 
                $result['jeop'] = 'SP5:UNABLE TO ESTABLISH PATH'; 
                $result['reason'] = "PROVISIONING CONNECT - " . $pathObj->reason;
                return $result; 
            }

            $pathObj->setPath();
            $path_id = $pathObj->id;
            /* end of create path section */

            // Ready for DB updates
            // For a new CKT
            if ($newckt == true) {
            
                // 1) create new CKT
                $cktObj->addCkt($ckid, $cls, $adsr, $prot, $ordno, $mlo, "");
                if ($cktObj->rslt == FAIL) {
                    $result["rslt"] = FAIL;
                    $result['jeop'] = "SP5:$cktObj->reason";            
                    $result["reason"] = "PROVISIONING CONNECT - " . $cktObj->reason;
                    return $result;
                }

                // 2) create new CKTCON
                $cktconObj = new CKTCON();
                $cktconObj->addCon($cktObj->id, $cktObj->ckid, $ctyp, $ffacObj->port_id, $ffacObj->port, 1, $tfacObj->port_id, $tfacObj->port, 1, $path_id);
                if ($cktconObj->rslt != SUCCESS) {
                    $result['rslt'] = $cktconObj->rslt;
                    $result['jeop'] = "SP5:$cktconObj->reason"; 
                    $result['reason'] = "PROVISIONING CONNECT - " . $cktconObj->reason;
                    return $result;
                }
                
                // 3) link CKT with CKTCON
                $cktObj->setCktcon($cktconObj->con);
                if ($cktObj->rslt != SUCCESS) {
                    $result['rslt'] = $cktObj->rslt;
                    $result['jeop'] = "SP5:$cktObj->reason"; 
                    $result['reason'] = "PROVISIONING CONNECT - " . $cktObj->reason;
                    return $result;
                }
            }
            else {
                // locate current CKTCON
                $cktconObj = new CKTCON($cktObj->cktcon);
                if ($cktconObj->rslt != SUCCESS) {
                    $result['rslt'] = $cktconObj->rslt;
                    $result['jeop'] = "SP5:$cktconObj->reason"; 
                    $result['reason'] = "PROVISIONING CONNECT - " . $cktconObj->reason;
                    return $result;
                }
                
                // add new CON_IDX
                $cktconObj->addIdx($cktObj->cktcon, $cktObj->id, $cktObj->ckid, $ctyp, $ctyp, $ffacObj->port_id, $ffacObj->port, 1, $tfacObj->port_id, $tfacObj->port, 1, $path_id);
                if ($cktconObj->rslt != SUCCESS) {
                    $result['rslt'] = $cktconObj->rslt;
                    $result['jeop'] = "SP5:$cktconObj->reason"; 
                    $result['reason'] = "PROVISIONING CONNECT - " . $cktconObj->reason;
                    return $result;
                }
                
            }
            
            
            // 4) update PORT's PSTA and link with CKT, CKTCON
            $ffacObj->portObj->updPsta($ffacObj->portObj->npsta, $ffacObj->portObj->nssta, "-");
            if ($ffacObj->portObj->rslt != SUCCESS) {
                $result['rslt'] = $ffacObj->portObj->rslt;
                $result['jeop'] = "SP5:".$ffacObj->portObj->reason; 
                $result['reason'] = "PROVISIONING CONNECT - " . $ffacObj->portObj->reason;
                return $result;
            }

            $ffacObj->portObj->updCktLink($cktObj->id, $cktconObj->con, $cktconObj->idx);
            if ($ffacObj->portObj->rslt != SUCCESS) {
                $result['rslt'] = $ffacObj->portObj->rslt;
                $result['jeop'] = "SP5:".$ffacObj->portObj->reason; 
                $result['reason'] = "PROVISIONING CONNECT - " . $ffacObj->portObj->reason;
                return $result;
            }
            
            $tfacObj->portObj->updPsta($tfacObj->portObj->npsta, $tfacObj->portObj->nssta, "-");
            if ($tfacObj->portObj->rslt != SUCCESS) {
                $result['rslt'] = $tfacObj->portObj->rslt;
                $result['jeop'] = "SP5:".$tfacObj->portObj->reason; 
                $result['reason'] = "PROVISIONING CONNECT - " . $tfacObj->portObj->reason;
                return $result;
            }

            $tfacObj->portObj->updCktLink($cktObj->id, $cktconObj->con, $cktconObj->idx);
            if ($tfacObj->portObj->rslt != SUCCESS) {
                $result['rslt'] = $tfacObj->portObj->rslt;
                $result['jeop'] = "SP5:".$tfacObj->portObj->reason; 
                $result['reason'] = "PROVISIONING CONNECT - " . $tfacObj->portObj->reason;
                return $result;
            }

            $result['rows'] = [];
            $result["rslt"] = SUCCESS;
            $result["reason"] = "PROVISIONING CONNECT - " . "SUCCESSFUL";
            return $result;
        } catch (Throwable $e) {
            $result['rslt'] = FAIL;
            $result['reason'] = $e->getMessage();
            return $result;
        }


	}

	
?>