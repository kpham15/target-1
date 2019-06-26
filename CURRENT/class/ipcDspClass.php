<?php

class DSP {
    public $rslt;
    public $reason;

    public function __construct(){

    }

    public function generate_50msSingleTone($node, $tpName) {

        if($tpName == 'DSP-1')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=X,FREQUENCY=1000,AMPLITUDE=-10.0,DURATION=50';
        else if($tpName == 'DSP-2')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=Y,FREQUENCY=1000,AMPLITUDE=-10.0,DURATION=50';
        else {
            $this->rslt = 'fail';
            $this->reason = "WRONG TEST PORT NAME: $tpName";
            return false;
        }

        $ackid = "ST_".$node."_".$tpName;
        $cmdString .=",ACKID=$ackid*";

        $cmdObj = new CMD();
		$cmdObj->addCmd($node,$ackid, $cmdString);
		if($cmdObj->rslt == 'fail') {
            $this->rslt = 'fail';
            $this->reason = $cmdObj->reason;
            return false;
		}
    }
    public function generate_50msDualTone($node, $tpName) {
        if($tpName == 'DSP-1')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=X,FREQUENCY1=697,AMPLITUDE=-10.0,FREQUENCY2=1209,AMPLITUDE=-10.0,DURATION=50';
        else if($tpName == 'DSP-2')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=Y,FREQUENCY1=697,AMPLITUDE=-10.0,FREQUENCY2=1209,AMPLITUDE=-10.0,DURATION=50';
        else {
            $this->rslt = 'fail';
            $this->reason = "WRONG TEST PORT NAME: $tpName";
            return false;
        }

        $ackid = "DT_".$node."_".$tpName;
        $cmdString .=",ACKID=$ackid*";

        $cmdObj = new CMD();
		$cmdObj->addCmd($node,$ackid, $cmdString);
		if($cmdObj->rslt == 'fail') {
            $this->rslt = 'fail';
            $this->reason = $cmdObj->reason;
            return false;
		}
    }
    public function generate_foreverSingleTone() {
        if($tpName == 'DSP-1')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=X,FREQUENCY=1000,AMPLITUDE=-10.0';
        else if($tpName == 'DSP-2')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=Y,FREQUENCY=1000,AMPLITUDE=-10.0';
        else {
            $this->rslt = 'fail';
            $this->reason = "WRONG TEST PORT NAME: $tpName";
            return false;
        }

        $ackid = "FST_".$node."_".$tpName;
        $cmdString .=",ACKID=$ackid*";

        $cmdObj = new CMD();
        $cmdObj->addCmd($node,$ackid, $cmdString);
        if($cmdObj->rslt == 'fail') {
            $this->rslt = 'fail';
            $this->reason = $cmdObj->reason;
            return false;
        }
    }
    public function generate_foreverDualTone() {
        if($tpName == 'DSP-1')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=X,FREQUENCY1=697,AMPLITUDE=-10.0,FREQUENCY2=1209,AMPLITUDE=-10.0';
        else if($tpName == 'DSP-2')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=X,FREQUENCY1=697,AMPLITUDE=-10.0,FREQUENCY2=1209,AMPLITUDE=-10.0';
        else {
            $this->rslt = 'fail';
            $this->reason = "WRONG TEST PORT NAME: $tpName";
            return false;
        }

        $ackid = "FDT".$node."_".$tpName;
        $cmdString .=",ACKID=$ackid*";

        $cmdObj = new CMD();
        $cmdObj->addCmd($node,$ackid, $cmdString);
        if($cmdObj->rslt == 'fail') {
            $this->rslt = 'fail';
            $this->reason = $cmdObj->reason;
            return false;
        }
    }

    public function stopTone($node, $tpName) {
        if($tpName == 'DSP-1')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=X,FREQUENCY=0';
        else if($tpName == 'DSP-2')
            $cmdString = '$COMMAND,ACTION=TONEGEN,BUS=Y,FREQUENCY=0';
        else {
            $this->rslt = 'fail';
            $this->reason = "WRONG TEST PORT NAME: $tpName";
            return false;
        }

        $ackid = "TT_".$node."_".$tpName;
        $cmdString .=",ACKID=$ackid*";

        $cmdObj = new CMD();
		$cmdObj->addCmd($node,$ackid, $cmdString);
		if($cmdObj->rslt == 'fail') {
            $this->rslt = 'fail';
            $this->reason = $cmdObj->reason;
            return false;
		}
    }
}



?>