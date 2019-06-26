<?php

class FOMS {
	public $ord			= [];
  public $ckts		= [];
  public $ctString = [];
  public $string = "";

	public $rslt		= "";
  public $reason	= "";
  public $rows    = [];

	public function __construct($CTString=NULL) {
		global $db;

		if ($CTString === NULL) {
			$this->rslt 	= FAIL;
			$this->reason	= "MISSING CONTRACT STRING FOR INPUT";
			return;	
		}

		if ($this->parseCTString($CTString) !== FAIL) {
			// if ($this->parseOrdInfo() !== FAIL) {
			// 	if ($this->parseCktInfo() !== FAIL) {

			// 	}
			// }
			$this->rslt		= SUCCESS;
			$this->reason	= "SUCCESSFULLY PARSED CTSTRING";
		}
  }

  public function createCkt() {
    global $db;

    foreach ($this->ckts as $ckt) {
      $ordno = "";
      $cttype = "";
      $ctid = "";
      $octtype = "";
      $octid = "";
      $adsr = "";
      $ssm = "";
      $ssp = "";
      $oc = "";
      $act = "";
      $lst = "";
      $cls = "";
      $noscm = "";
      $relordno = "";
      $relcttype = "";
      $relctid = "";
      $relot = "";
      $relact = "";
      
      if (array_key_exists('ORDNO',$this->ord)) {
        $ordno = $this->ord['ORDNO'];
      }
      if (array_key_exists('CTTYPE',$ckt)) {
        $cttype = $ckt['CTTYPE'];
      }
      if (array_key_exists('CTID',$ckt)) {
        $ctid = $ckt['CTID'];
      }
      if (array_key_exists('OCTTYPE',$ckt)) {
        $octtype = $ckt['OCTTYPE'];
      }
      if (array_key_exists('OCTID',$ckt)) {
        $octid = $ckt['OCTID'];
      }
      if (array_key_exists('ADSR',$ckt)) {
        $adsr = $ckt['ADSR'];
      }
      if (array_key_exists('SSM',$ckt)) {
        $ssm = $ckt['SSM'];
      }
      if (array_key_exists('SSP',$ckt)) {
        $ssp = $ckt['SSP'];
      }
      if (array_key_exists('OC',$ckt)) {
        $oc = $ckt['OC'];
      }
      if (array_key_exists('ACT',$ckt)) {
        $act = $ckt['ACT'];
      }
      if (array_key_exists('LST',$ckt)) {
        $lst = $ckt['LST'];
      }
      if (array_key_exists('CLS',$ckt)) {
        $cls = $ckt['CLS'];
      }
      if (array_key_exists('NOSCM',$ckt)) {
        $noscm = $ckt['NOSCM'];
      }

      $relIndex = $this->checkRelOrd($ckt);

      if ($relIndex !== -1) {
        $relord = $ckt['OPS'][$relIndex]['RELORD'];

        if (array_key_exists('ORDNO',$relord)) {
          $relordno = $relord['ORDNO'];
        }
        if (array_key_exists('CTTYPE',$relord)) {
          $relcttype = $relord['CTTYPE'];
        }
        if (array_key_exists('CTID',$relord)) {
          $relctid = $relord['CTID'];
        }
        if (array_key_exists('OT',$relord)) {
          $relot = $relord['OT'];
        }
        if (array_key_exists('ACT',$relord)) {
          $relact = $relord['ACT'];
        }
      }

      $qry = "INSERT INTO
              t_Ckt (ordno, cttype, ctid, octtype, octid, adsr, ssm, ssp, oc, act, lst, cls, noscm, relordno, relcttype, relctid, relot, relact)
              VALUES ('$ordno', '$cttype', '$ctid', '$octtype', '$octid', '$adsr', '$ssm', '$ssp', '$oc', '$act', '$lst', '$cls', '$noscm', '$relordno', '$relcttype', '$relctid', '$relot', '$relact')";

      $res = $db->query($qry);
      if (!$res) {
        $this->rslt = "fail";
        $this->reason = mysqli_error($db);
        $this->rows = [];
      } else {
        $this->rslt = "success";
        $this->reason = "SUCCESSFUL - FOMS CKTS INSERTED";
        $this->rows = [];
      }

      
    }

    return;
  }

  public function createOperations() {
    global $db;

    foreach ($this->ckts as $ckt) {
      $ordno = $this->ord['ORDNO'];
      $ctid = $ckt['CTID'];
      $act = $ckt['ACT'];
      $currentDate = new DateTime('now'); // DATETIME
      $cd = $currentDate->format('Y-m-d H:i:s');
      $t = strtotime($this->ord['DD']);
      $dd = date('y-m-d',$t); // DATE
  
      foreach ($ckt['OPS'] as $cktop) {
        $op = "";
        $ffactyp = "";
        $ffacid = "";
        $ffrloc = "";
        $tfactyp = "";
        $tfacid = "";
        $tfrloc = "";
  
        $facs = [];
  
        if (array_key_exists('RELORD',$cktop)) {
          continue;
        } else if (array_key_exists('IN',$cktop)) {
          $op = 'IN';
          $facs = $cktop['IN'];
        } else if (array_key_exists('OUT',$cktop)) {
          $op = 'OUT';
          $facs = $cktop['OUT'];
        } else if (array_key_exists('REU',$cktop)) {
          $op = 'REU';
          $facs = $cktop['REU'];
        }
  
  
        if (count($facs) !== 0) {
          $ffactyp = $facs[0]['TYPE'];
          $ffacid = $facs[0]['ID'];
          $ffrloc = $facs[0]['FRLOC'];
  
          if ($facs[1] !== NULL) {
            $tfactyp = $facs[1]['TYPE'];
            $tfacid = $facs[1]['ID'];
            $tfrloc = $facs[1]['FRLOC'];
          }
        }
  
        $qry = "INSERT INTO
                t_Ckcon (ordno, ctid, act, op, ffactyp, ffacid, ffrloc, tfactyp, tfacid, tfrloc, cd, dd)
                VALUES ('$ordno', '$ctid', '$act', '$op', '$ffactyp', '$ffacid', '$ffrloc', '$tfactyp', '$tfacid', '$tfrloc', '$cd', '$dd')";
  
        $res = $db->query($qry);
        if (!$res) {
          $this->rslt = "fail";
          $this->reason = mysqli_error($db);
          $this->rows = [];
        } else {
          $this->rslt = "success";
          $this->reason = "SUCCESSFUL - FOMS OPS INSERTED";
          $this->rows = [];
        }
      }
    }

    return;
  }

  public function checkRelOrd($ckt) {
    $relIndex = -1;

    for ($i=0;$i<count($ckt['OPS']);$i++) {
      if (array_key_exists('RELORD',$ckt['OPS'][$i])) {
        $relIndex = $i;
      }
    }

    return $relIndex;
  }
  
  public function createOrd() {
    global $db;

    $ordno = "";
    $ot = "";
    $cdd = "";
    $dd = ""; // DATE
    $fdd = ""; // DATE
    $fdt = "";
    $wc = "";
    $pri = "";

    if (array_key_exists('ORDNO',$this->ord)) {
      $ordno = $this->ord['ORDNO'];
    }
    if (array_key_exists('OT',$this->ord)) {
      $ot = $this->ord['OT'];
    }
    if (array_key_exists('CDD',$this->ord)) {
      $cdd = $this->ord['CDD'];
    }
    if (array_key_exists('DD',$this->ord)) {
      $t = strtotime($this->ord['DD']);
      $dd = date('y-m-d',$t);
    }
    if (array_key_exists('FDD',$this->ord)) {
      $t = strtotime($this->ord['FDD']);
      $fdd = date('y-m-d',$t);
    }
    if (array_key_exists('FDT',$this->ord)) {
      $fdt = $this->ord['FDT'];
    }
    if (array_key_exists('WC',$this->ord)) {
      $wc = $this->ord['WC'];
    }
    if (array_key_exists('PRI',$this->ord)) {
      $pri = $this->ord['PRI'];
    }

    $qry = "INSERT INTO t_Ord (ordno, ot, cdd, dd, fdd, fdt, wc, pri, stat) VALUES ('$ordno', '$ot', '$cdd', '$dd', '$fdd', '$fdt', '$wc', '$pri', 'NEW')";

    $res = $db->query($qry);
    if (!$res) {
      $this->rslt = "fail";
      $this->reason = mysqli_error($db);
      $this->rows = [];
    } else {
      $this->rslt = "success";
      $this->reason = "SUCCESSFUL - FOMS ORDER INSERTED";
      $this->rows = [];
    }

    return;

  }

	public function parseCTString($CTString=NULL) {
    global $db;
    
		if ($CTString === NULL) {
			$this->rslt		= FAIL;
			$this->reason	="MISSING CONTRACT STRING FOR INPUT";
			return FAIL;
    }
    
    $this->string = $CTString;

		$strExtract = explode("*",$CTString);
    array_shift($strExtract);
    $fomstc = $strExtract[1];
    $fomstc = str_replace("\r\n", "", $fomstc);
    $fomstc = substr($fomstc,0,-1);

    $json_ord = str_replace(';',',',$fomstc);
    $json_ord = str_replace('=',':',$json_ord);
    $json_ord = str_replace(',}','},',$json_ord);
    $json_ord = str_replace('}}','}},',$json_ord);
    $json_ord = str_replace('{','"{"',$json_ord);
    $json_ord = str_replace('}','"}"',$json_ord);
    $json_ord = str_replace(',','","',$json_ord);
    $json_ord = str_replace(':','":"',$json_ord);
    $json_ord = substr($json_ord, 0, -1);
    $json_ord = '"' . $json_ord;
    $json_ord = str_replace('""','"',$json_ord);
    $json_ord = str_replace('}",','},',$json_ord);
    $json_ord = str_replace(',"}','}',$json_ord);
    $json_ord = str_replace('}"}','}}',$json_ord);
    $json_ord = str_replace('{',':{',$json_ord);
    $json_ord = '{' . $json_ord . '}';
    
    preg_match_all('/"IN(.+?)}}/',$json_ord,$inMatches);
    
    foreach ($inMatches[0] as $match) {
      $replace = str_replace('"IN','{"IN',$match);
      $replace = str_replace('{"FAC":','[',$replace);
      $replace = str_replace('"FAC":','',$replace);
      $replace = str_replace("}}","}]},",$replace);

      $json_ord = str_replace($match,$replace,$json_ord);
    }

    preg_match_all('/"OUT(.+?)}}/',$json_ord,$outMatches);

    foreach($outMatches[0] as $match) {
      $replace = str_replace('"OUT','{"OUT',$match);
      $replace = str_replace('{"FAC":','[',$replace);
      $replace = str_replace('"FAC":','',$replace);
      $replace = str_replace("}}","}]},",$replace);

      $json_ord = str_replace($match,$replace,$json_ord);
    }

    preg_match_all('/"REU(.+?)}}/',$json_ord,$reuMatches);

    foreach($reuMatches[0] as $match) {
      $replace = str_replace('"REU','{"REU',$match);
      $replace = str_replace('{"FAC":','[',$replace);
      $replace = str_replace('"FAC":','',$replace);
      $replace = str_replace("}}","}]},",$replace);

      $json_ord = str_replace($match,$replace,$json_ord);
    }

    preg_match_all('/"RELORD(.+?)}}/',$json_ord,$relordMatches);

    foreach($relordMatches[0] as $match) {
      $replace = str_replace('"RELORD','{"RELORD',$match);
      $replace = str_replace("}}","}}},",$replace);

      $json_ord = str_replace($match,$replace,$json_ord);
    }

    $json_ord = str_replace(',}}','}}',$json_ord);
    $json_ord = str_replace('{"ORD":',"",$json_ord);
    $json_ord = substr($json_ord,0,-1);

    $ckts = explode('"CKT":{',$json_ord);
    $json_ord = array_shift($ckts);
    $json_ord = substr($json_ord,0,-1);
    $json_ord = $json_ord . '}';

    $cktArray = [];
    foreach($ckts as $ckt) {
      $cktStr = "";

      preg_match('/{"IN(.+?)}}|{"OUT(.+?)}}|{"REU(.+?)}}|{"RELORD(.+?)}}/',$ckt,$matches,PREG_OFFSET_CAPTURE);

      $cktStr = substr($ckt,0,-2);

      $opStr = array();
      array_push($opStr,substr($cktStr,0,$matches[0][1]));
      array_push($opStr,substr($cktStr,$matches[0][1]));
      $cktStr = $opStr[0] . '"OPS":[' . $opStr[1] . ']';
      
      $cktStr = "{" . $cktStr . "}";

      array_push($this->ctString,$ckt);
      array_push($cktArray,json_decode(utf8_decode($cktStr), true));
    }

    unset($ckt);
		
		$this->ord = json_decode(utf8_decode($json_ord), true);
    $this->ckts = $cktArray;


    $currentDate = new DateTime('now'); // DATETIME
    $currentDate = $currentDate->format('Y-m-d H:i:s');
    $ordno = $this->ord['ORDNO'];

    $qry = "INSERT INTO t_foms (`user`, `date`, `ordno`, `foms`)
            VALUES ('ninh', '$currentDate', '$ordno', '$fomstc')";

    $res = $db->query($qry);
    if (!$res) {
      $this->rslt = "fail";
      $this->reason = mysqli_error($db);
      $this->rows = [];
    } else {
      $this->rslt = "success";
      $this->reason = "SUCCESSFUL - FOMS STRING PARSED";
      $this->rows = [];
    }

		return;
	}
}

?>