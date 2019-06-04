<?php

class PATH {
    public $id;
    public $r1;
    public $r2;
    public $r3f;
    public $rco;
    public $r4;
    public $rci;
    public $r3t;
    public $r5;
    public $r6;
    public $r7;
    public $x;
    public $y;
    public $log;
    
    public $rslt;
    public $reason;

    // x and y are ports with base-1 format, for example 1-1-X-3 is node=1, slot=1, type=X, index=3
    // they need to be converted to base-0, for example  0.X.0.2
    public function __construct($x, $y) {
        $X = explode('-', $x);
        //$pX = $X[0].'.'.$X[2].'.'.$X[1].'.'.$X[3];
        $pX = (string)((int)($X[0])-1) . '.' . $X[2] . '.' . (string)((int)($X[1])-1) . '.' . (string)((int)($X[3])-1);

        $Y = explode('-', $y);
        //$pY = $Y[0].'.'.$Y[2].'.'.$Y[1].'.'.$Y[3];
        $pY = (string)((int)($Y[0])-1) . '.' . $Y[2] . '.' . (string)((int)($Y[1])-1) . '.' . (string)((int)($Y[3])-1);

        $this->id = 0;
        $this->x = $pX;
        $this->y = $pY;
        $this->r1 = new Rel();
        $this->r2 = new Rel();
        $this->r3f = new Rel();
        $this->rco = new Rel();
        $this->r4 = new Rel();
        $this->rci = new Rel();
        $this->r3t = new Rel();
        $this->r5 = new Rel();
        $this->r6 = new Rel();
        $this->r7 = new Rel();
    }

    public function load() {
        global $db;
        // $qry = "SELECT * from t_pathXY WHERE x='" . $this->x . "' AND y='" . $this->y . "'";
        $qry = "SELECT * from t_path WHERE x='" . $this->x . "' AND y='" . $this->y . "'";
        $res = 	$db->query($qry);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $this->id = $row["id"];
                $this->r1->a = $row["s1"];
                $this->r1->x = $row["s1x"];
                $this->r1->y = $row["s1y"];

                $this->r2->a = $row["s2"];
                $this->r2->x = $row["s2x"];
                $this->r2->y = $row["s2y"];
                
                $this->r3f->a = $row["s3f"];
                $this->r3f->x = $row["s3fx"];
                $this->r3f->y = $row["s3fy"];
                
                $this->rco->a = $row["sco"];
                $this->rco->x = $row["scox"];
                $this->rco->y = $row["scoy"];
                
                $this->r4->a = $row["s4"];
                $this->r4->x = $row["s4x"];
                $this->r4->y = $row["s4y"];
                
                $this->rci->a = $row["sci"];
                $this->rci->x = $row["scix"];
                $this->rci->y = $row["sciy"];
                
                $this->r3t->a = $row["s3t"];
                $this->r3t->x = $row["s3tx"];
                $this->r3t->y = $row["s3ty"];
                
                $this->r5->a = $row["s5"];
                $this->r5->x = $row["s5x"];
                $this->r5->y = $row["s5y"];
                
                $this->r6->a = $row["s6"];
                $this->r6->x = $row["s6x"];
                $this->r6->y = $row["s6y"];
                
                $this->r7->a = $row["s7"];
                $this->r7->x = $row["s7x"];
                $this->r7->y = $row["s7y"];
            }
            $this->rslt = 'success';
            $this->reason = 'PATH_LOADED';
            return true;
        }
        else {
            $this->rslt = 'fail';
            $this->reason = 'NO_PATH_LOADED BETWEEN: '.$this->x." AND ".$this->y;
            return false;
        }
    }

    public function save() {
        global $db;

        $qry = "INSERT INTO t_path (id,x";
        $values = "VALUES(0,'" . $this->x . "'";

        if ($this->r1->a != '' && $this->r1->a !== NULL) {
            $qry .= ",s1,s1x,s1y";
            $values .= ",'" . $this->r1->a . ":" . $this->r1->x . "'," . $this->r1->x . "," . $this->r1->y;
        }
        else {
            $qry .= ",s1x,s1y";
            $values .= "," . $this->r1->x . "," . $this->r1->y;
        }
        
        if ($this->r2->a != '' && $this->r2->a !== NULL) {
            $qry .= ",s2,s2x,s2y";
            $values .= ",'" . $this->r2->a . ":" . $this->r2->x . "'," . $this->r2->x . "," . $this->r2->y;
        }
        else {
            $qry .= ",s2x,s2y";
            $values .= "," . $this->r2->x . "," . $this->r2->y;
        }

        if ($this->r3f->a != '' && $this->r3f->a !== NULL) {
            $qry .= ",s3f,s3fx,s3fy";
            $values .= ",'" . $this->r3f->a . ":" . $this->r3f->x . "'," . $this->r3f->x . "," . $this->r3f->y;
        }
        else {
            $qry .= ",s3fx,s3fy";
            $values .= "," . $this->r3f->x . "," . $this->r3f->y;
        }

        if ($this->rco->a != '' && $this->rco->a !== NULL) {
            $qry .= ",sco,scox,scoy";
            $values .= ",'" . $this->rco->a . ":" . $this->rco->x . "'," . $this->rco->x . "," . $this->rco->y;
        }
        else {
            $qry .= ",scox,scoy";
            $values .= "," . $this->rco->x . "," . $this->rco->y;
        }

        if ($this->r4->a != '' && $this->r4->a !== NULL) {
            $qry .= ",s4,s4x,s4y";
            $values .= ",'" . $this->r4->a . ":" . $this->r4->x . "'," . $this->r4->x . "," . $this->r4->y;
        }
        else {
            $qry .= ",s4x,s4y";
            $values .= "," . $this->r4->x . "," . $this->r4->y;
        }

        if ($this->rci->a != '' && $this->rci->a !== NULL) {
            $qry .= ",sci,scix,sciy";
            $values .= ",'" . $this->rci->a . ":" . $this->rci->x . "'," . $this->rci->x . "," . $this->rci->y;
        }
        else {
            $qry .= ",scix,sciy";
            $values .= "," . $this->rci->x . "," . $this->rci->y;
        }

        if ($this->r3t->a != '' && $this->r3t->a !== NULL) {
            $qry .= ",s3t,s3tx,s3ty";
            $values .= ",'" . $this->r3t->a . ":" . $this->r3t->x . "'," . $this->r3t->x . "," . $this->r3t->y;
        }
        else {
            $qry .= ",s3tx,s3ty";
            $values .= "," . $this->r3t->x . "," . $this->r3t->y;
        }

        if ($this->r5->a != '' && $this->r5->a !== NULL) {
            $qry .= ",s5,s5x,s5y";
            $values .= ",'" . $this->r5->a . ":" . $this->r5->x . "'," . $this->r5->x . "," . $this->r5->y;
        }
        else {
            $qry .= ",s5x,s5y";
            $values .= "," . $this->r5->x . "," . $this->r5->y;
        }

        if ($this->r6->a != '' && $this->r6->a !== NULL) {
            $qry .= ",s6,s6x,s6y";
            $values .= ",'" . $this->r6->a . ":" . $this->r6->x . "'," . $this->r6->x . "," . $this->r6->y;
        }
        else {
            $qry .= ",s6x,s6y";
            $values .= "," . $this->r6->x . "," . $this->r6->y;
        }

        if ($this->r7->a != '' && $this->r7->a !== NULL) {
            $qry .= ",s7,s7x,s7y";
            $values .= ",'" . $this->r7->a . ":" . $this->r7->x . "'," . $this->r7->x . "," . $this->r7->y;
        }
        else {
            $qry .= ",s7x,s7y";
            $values .= "," . $this->r7->x . "," . $this->r7->y;
        }

        $qry .= ",y) ";
        $values .= ",'" . $this->y . "')";
        $qry .= $values;

        $res = 	$db->query($qry);
        if (!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            return;
        }
        $this->id = $db->insert_id;
        $this->rslt = 'success';
        $this->reason = "SAVE PATH";
    }	

    public function drop() {
        global $db;
        $qry = "DELETE FROM t_path WHERE x='$this->x' AND y='$this->y'";
        $res = 	$db->query($qry);
        if (!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            return;
        }
        $this->rslt = 'success';
        $this->reason = "DROP PATH";
    }


    public function getS2ByS1($s1d) {
        global $db;

        $qry = "SELECT * FROM t_stg WHERE a LIKE '$s1d'";
        $rows = [];
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


    //////////////////-----------------------/////////////
    public function createPath() {
        $X=explode('.', $this->x);
        $Y=explode('.',$this->y);
        if ($X[0] == $Y[0]) {
            if (($X[2]<5 && $Y[2]<5) || ($X[2]>4 && $Y[2]>4)) {
                $this->connectSameNodeSameSection();
            }
            else {
                $this->connectSameNodeDiffSections();
            }
        }
        else {
            if (($X[2]<5 && $Y[2]<5) || ($X[2]>4 && $Y[2]>4)) {
                $this->connectBetweenNodesSameSection();
            }
            else {
                $this->connectBetweenNodesDiffSections();
            }
        }

    }

    private function connectSameNodeSameSection() {
        $done = FALSE;
        $this->log = '';

        $this->log .= "begin connect Same Node Same Section() \n";

        // 1) locate s1 array
        $X = new X($this->x);
        $this->log .= 'X port = ' . $this->x;
        $this->log .= "\nY port = " . $this->y;
        //$this->log .= "\n1) locate s1:";
        $s1 = new Stg($X->d);
        $this->log .= "\n\tX->d => s1->a: " . $X->d;

        // if input x=p is connected to any output y, the port is already in use
        if ($s1->getYx($s1->p) != -1) {
            $this->rslt = 'fail';
            $this->log .= "\nPORT: " . $this->x . "IS BUSY";
            return;
        }

        // 2) locate s7 array
        $Y = new Y($this->y);
        $this->log .= "\n\n" . "2) locate s7:";
        $s7 = new Stg($Y->d);
        $this->log .= "\n\tY->d => s7->a:" . $Y->d;
        // if output y=p is connected to any input x, the port is already in use
        if ($s7->getXy($s7->p) != -1) {
            $this->rslt = 'fail';
            $this->log .= 'PORT: ' . $this->y . 'IS BUSY';
            return;
        }

        // get list of Possible s6's
        $s6List = [];
        $s5List = [];
        // get list of Possible s6's
        $s6rows = $s7->findListOfPossibleS6($Y->d);
        $this->log .= "\nList of possible s6's and s5's:\n";

        $s6len = count($s6rows);
        for ($i=0; $i<$s6len; $i++) {
            $e6 = explode('.',$s6rows[$i]['a']);
            $s6List[] = $e6[0] . '.' . $e6[1] . '.' . $e6[2];
            $this->log .= "\n" . $s6rows[$i]['a'] . "\n";
            $s5rows = $s7->findListOfPossibleS5($s6rows[$i]['a']);
            $s5len = count($s5rows);
            for ($j=0; $j<$s5len; $j++) {
                $e5 = explode('.',$s5rows[$j]['a']);
                $s5List[] = $e5[0] . '.' . $e5[1] . '.' . $e5[2];
                $this->log .= "\t" . $s5rows[$j]['a'];
            }
        }
 

        // 3) Search for avail output y path to s2 array
        $s1len = count($s1->y);
        for ($k1=0; $k1<$s1len; $k1++) {

            $this->log .= "\n\ts1: " . $s1->a . ", getYx(".$s1->p.")=".$s1->getYx($s1->p)." , getXy(".$k1.")=".$s1->getXy($k1);
            if ($s1->getYx($s1->p) == -1 && $s1->getXy($k1) == -1) {
                $this->r1->a = $s1->a;
                $this->r1->x = $s1->p;
                $this->r1->y = $s1->y[$k1];
                $this->log .= "\n\t==> s2: " . $s1->d[$k1];

                $s2 = new Stg($s1->d[$k1]);
                $s2len = count($s2->y);

                for ($k2=0; $k2<$s2len; $k2++) {
                    
                    $this->log .= "\n\ts2: " . $s2->a . ", getYx(".$s2->p.")=".$s2->getYx($s2->p)." , getXy(".$k2.")=".$s2->getXy($k2);
                    if ($s2->getYx($s2->p) == -1 && $s2->getXy($k2) == -1) {
                        $this->r2->a = $s2->a;
                        $this->r2->x = $s2->p;
                        $this->r2->y = $s2->y[$k2];
                        $this->log .= "\n\t\t==> s3f: " . $s2->d[$k2];

                        $s3f = new Stg($s2->d[$k2]);
                        // $this->log .= "\n\t\ts3f->x[0]=" . $s3f->x[0];
                        // $this->log .= "\n\t\ts3f->x[1]=" . $s3f->x[1];
                        // $this->log .= "\n\t\ts3f->x[2]=" . $s3f->x[2];

                        $s5 = null;
                        
                        $this->log .= "\n\ts3f: " . $s3f->a . ", getYx(".$s3f->p.")=".$s3f->getYx($s3f->p)." , getXy(0)=".$s3f->getXy(0);
                        if ($s3f->getYx($s3f->p) == -1 && $s3f->getXy(0) == -1){
                                                
                            $this->log .= "\n\n\t\t==> s5: " . $s3f->d[0];
                            $this->r3f->a = $s3f->a;
                            $this->r3f->x = $s3f->p; //p=input pin
                            $this->r3f->y = 0;

                            $s5 = new Stg($s3f->d[0]);
                        }
                        
                        if ($s5 == null) {
                            continue;   //continue with next k2 (next avail s2)
                        }

                        $s5len = count($s5->y);
                        for ($k5=0; $k5<$s5len; $k5++) {

                            $e = explode('.',$s5->d[$k5]);
                            $s6a = $e[0] . '.' . $e[1] . '.' . $e[2];
                            $this->log .= "\n" . $s6a;

                            if (!in_array($s6a,$s6List)) {
                                continue;
                            }

                            $this->log .= "\n\ts5->d[".$k5."]=" . $s5->d[$k5];
                            $this->log .= "\n\ts5: " . $s5->a . ", getYx(".$s5->p.")=".$s5->getYx($s5->p)." , getXy(".$k5.")=".$s5->getXy($k5);
                            if ($s5->getYx($s5->p) == -1 && $s5->getXy($k5) == -1) {
                                $this->r5->a = $s5->a;
                                $this->r5->x = $s5->p;
                                $this->r5->y = $s5->y[$k5];
                                $this->log .= "\n\t\t\t==> s6: " . $s5->d[$k5];

                                $s6 = new Stg($s5->d[$k5]);
                                $s6len = count($s6->y);
                                for ($k6=0; $k6<$s6len; $k6++) {
                                    $e = explode(".", $s6->d[$k6]);
                                    $s7a = $e[0] . "." . $e[1] . "." . $e[2];
                                    $s7x = $e[3];
                                    $this->log .= "\n\t\t\t\t==> s7: " . $s7a;
                                    
                                    $this->log .= "\n\ts6: " . $s6->a . ", getYx(".$s6->p.")=".$s6->getYx($s6->p)." , getXy(".$k6.")=".$s6->getXy($k6);
                                    if ($s7a == $s7->a && $s6->getYx($s6->p) == -1 && $s6->getXy($k6) == -1) {                 
                                        $this->r6->a = $s6->a;
                                        $this->r6->x = $s6->p;
                                        $this->r6->y = $s6->y[$k6];
                                        
                                        $this->log .= "\n\ts7: " . $s7->a . ", getYx(".$s7x.")=".$s7->getYx($s7x)." , getXy(".$s7->p.")=".$s7->getXy($s7->p);
                                        if ($s7->getYx($s7x) == -1 && $s7->getXy($s7->p) == -1) {
                                            $this->r7->a = $s7->a;
                                            $this->r7->x = $s7x;
                                            $this->r7->y = $s7->p;
                                            $done = true;
                                            break;
                                        }
                                    }
                                } // k6
                                if ($done === true) {
                                    $this->log .= "\n\nfound a full PATH, done=true";
                                    $this->log .= "\n\n" . $s1->a . ':' . $s1->p . '.' . $k1;
                                    $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                                    $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.0';
                                    $this->log .= " - " . $s5->a . ':' . $s5->p . '.' . $k5;
                                    $this->log .= " - " . $s6->a . ':' . $s6->p . '.' . $k6;
                                    $this->log .= " - " . $s7->a . ':' . $s7x . '.' . $s1->p;
                                    $this->log .= "\n\n";
                                    break;
                                }
                                else {
                                    $this->log .= "\n" . $this->r6->a . ' has no avail output, continue next k5';
                                    $this->log .= "\n\n" . $s1->a . ':' . $s1->p . '.' . $k1;
                                    $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                                    $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.0';
                                    $this->log .= " - " . $s5->a . ':' . $s5->p . '.' . $k5;
                                    $this->log .= "\n\n";
                                }
                            }
                        } // k5

                        if ($done === true) {
                            $this->log .= "\n\nfound a full PATH, done=true";
                            break;
                        }
                        else {
                            $this->log .= "\n" . $this->r5->a . ' has no avail output, continue next k2';
                            $this->log .= " - " . $s1->a . ':' . $s1->p . '.' . $k1;
                            $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                            $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.0';
                            $this->log .= "\n\n";}
                    } //k2++                    
                } // end of s2
                if ($done === true) {
                    $this->log .= "\n\nfound a full PATH, done=true";
                    break;
                }
                else {
                    $this->log .= "\n" . $this->r2->a . " has no avail output, continue next k1";
                }
            } //k1++
        } // end of s1
        if ($done === true) {

            $this->save();           
            if ($this->rslt == 'success') {
                $this->reason = "PATH_CREATED";
            }
        }
        else {
            $this->rslt = 'fail';
            $this->reason = "INCOMPLETE_PATH";
        }



    }

    private function connectSameNodeDiffSections() {
        $done = FALSE;
        $this->log = '';

        $this->log .= "begin connect Same Node Diff Sections() \n";

        // 1) locate s1 array and list of possible s2's
        
        $X = new X($this->x);
        $this->log .= 'X port = ' . $this->x;
        $this->log .= "\nY port = " . $this->y;

        $this->log .= "\n1) locate s1 and list of possible s2's\n";
        $s1 = new Stg($X->d);
        $this->log .= "\n\tX->d => s1->a: " . $X->d;

        $s2List = [];
        $s2rows = $s1->findListOfNextAvailS2($s1->a);
        $s2len = count($s2rows);
        for ($m=0; $m<$s2len; $m++) {
            $e2 = explode('.',$s2rows[$m]['d']);
            $s2List[] = $e2[0] . '.' . $e2[1] . '.' . $e2[2];
            $this->log .= "\t" . $s2rows[$m]['d'];
        }

        // if input x=p is connected to any output y, the port is already in use
        if ($s1->getYx($s1->p) != -1) {
            $this->rslt = 'fail';
            $this->log .= "\nPORT: " . $this->x . "IS BUSY";
            return;
        }

        // 2) locate s7 array
        $Y = new Y($this->y);
        $this->log .= "\n\n" . "2) locate s7:";
        $s7 = new Stg($Y->d);
        $this->log .= "\nY->d => s7->a:" . $Y->d;
        // if output y=p is connected to any input x, the port is already in use
        if ($s7->getXy($s7->p) != -1) {
            $this->rslt = 'fail';
            $this->log .= 'PORT: ' . $this->y . 'IS BUSY';
            return;
        }

        $s6List = [];
        $s5List = [];
        $s3List = [];
        // get list of Possible s6's
        $s6rows = $s7->findListOfPossibleS6($Y->d);
        $this->log .= "\nList of possible s6's and s5's:\n";

        $s6len = count($s6rows);
        for ($i=0; $i<$s6len; $i++) {
            $e6 = explode('.',$s6rows[$i]['a']);
            $s6List[] = $e6[0] . '.' . $e6[1] . '.' . $e6[2];
            $this->log .= "\n" . $s6rows[$i]['a'] . "\n";
            $s5rows = $s7->findListOfPossibleS5($s6rows[$i]['a']);
            $s5len = count($s5rows);
            for ($j=0; $j<$s5len; $j++) {
                $e5 = explode('.',$s5rows[$j]['a']);
                $s5List[] = $e5[0] . '.' . $e5[1] . '.' . $e5[2];
                $this->log .= "\t" . $s5rows[$j]['a'] . "\n\t";
                $s3rows = $s7->findListOfPossibleS3($s5rows[$j]['a']);
                $s3len = count($s3rows);
                for ($k=0; $k<$s3len; $k++) {
                    $e3 = explode('.', $s3rows[$k]['a']);
                    $s3List[] = $e3[0] . '.' . $e3[1] . '.' . $e3[2];
                    $this->log .= "\t" . $s3rows[$k]['a'];
                }
            }
        }
        
        
        if ($X->n < $Y->n)
            $done = $this->findPathInSameNodeDiffSectionsAscendingOrder($s1, $s3List, $s5List, $s6List, $s7);
        else
            $done = $this->findPathInSameNodeDiffSectionsDescendingOrder($s1, $s3List, $s5List, $s6List, $s7);

        if ($done === true) {

            $this->save();           
            if ($this->rslt == 'success') {
                $this->reason = "PATH_CREATED";
            }
        }
        else {
            $this->rslt = 'fail';
            $this->reason = "INCOMPLETE_PATH";
        }


    }

    private function findPathInSameNodeDiffSectionsAscendingOrder($s1, $s3List, $s5List, $s6List, $s7) {
        $done = false;
        $this->log .= "\nFind Path In Same Node Diff Sections Ascending Order";

        $s1len = count($s1->y);
        for ($k1=0; $k1<$s1len; $k1++) {

            $this->log .= "\n\ts1: " . $s1->a . ": getYx(". $s1->p . ")= " . $s1->getYx($s1->p) ." , getXy(" . $k1 . ")=" . $s1->getXy($k1);
            if ($s1->getYx($s1->p) == -1 && $s1->getXy($k1) == -1) {
                $this->r1->a = $s1->a;
                $this->r1->x = $s1->p;
                $this->r1->y = $s1->y[$k1];
                $this->log .= "\n\t==> " . $s1->d[$k1];

                $s2 = new Stg($s1->d[$k1]);
                $s2len = count($s2->y);

                // $this->log .= "\n\t" . $s2->a . "->x[0]=" . $s2->x[0];
                // $this->log .= "\n\t" . $s2->a . "->x[1]=" . $s2->x[1];
                // $this->log .= "\n\t" . $s2->a . "->x[2]=" . $s2->x[2];
                // $this->log .= "\n\t" . $s2->a . "->x[3]=" . $s2->x[3];
                // $this->log .= "\n\t" . $s2->a . "->x[4]=" . $s2->x[4];
                // $this->log .= "\n\t" . $s2->a . "->x[5]=" . $s2->x[5];
                // $this->log .= "\n\t" . $s2->a . "->x[6]=" . $s2->x[6];
                // $this->log .= "\n\t" . $s2->a . "->x[7]=" . $s2->x[7];
                // $this->log .= "\n\t" . $s2->a . "->x[8]=" . $s2->x[8];
                // $this->log .= "\n\t" . $s2->a . "->x[9]=" . $s2->x[9];
                for ($k2=0; $k2<$s2len; $k2++) {

                    $this->log .= "\n\ts2: " . $s2->a . ": getYx(". $s2->p . ")= " . $s2->getYx($s2->p) ." , getXy(" . $k2 . ")=" . $s2->getXy($k2);
                    if ($s2->getYx($s2->p) == -1 && $s2->getXy($k2) == -1) {
                        $this->r2->a = $s2->a;
                        $this->r2->x = $s2->p;
                        $this->r2->y = $s2->y[$k2];
                        $this->log .= "\n\t\t==> s3f: " . $s2->d[$k2];

                        $s3f = new Stg($s2->d[$k2]);
                        
                        // $this->log .= "\n\t\t" . $s3f->a . "->x[0]=" . $s3f->x[0];
                        // $this->log .= "\n\t\t" . $s3f->a . "->x[1]=" . $s3f->x[1];
                        // $this->log .= "\n\t\t" . $s3f->a . "->x[2]=" . $s3f->x[2];

                        $s5 = null;
                        $this->log .= "\n\ts3f: " . $s3f->a . ": getYx(". $s3f->p . ")= " . $s3f->getYx($s3f->p) ." , getXy(1)=" . $s3f->getXy(1);
                        if ($s3f->getYx($s3f->p) == -1 && $s3f->getXy(1) == -1) {
                            
                            $this->log .= "\n\n\t\t==> sco: " . $s3f->d[1];
                            $this->r3f->a = $s3f->a;
                            $this->r3f->x = $s3f->p; //p=input pin
                            $this->r3f->y = 1;

                            $sco = new Stg($s3f->d[1]);
                            // $this->log .= "\n\t\t" . $sco->a . "->x[0]=" . $sco->x[0];
                            // $this->log .= "\n\t\t" . $sco->a . "->x[1]=" . $sco->x[1];
                            // $this->log .= "\n\t\t" . $sco->a . "->x[2]=" . $sco->x[2];
                            if (!in_array($sco->a, $s3List)) {
                                continue;
                            }
                            $this->log .= "\n\tsco: " . $sco->a . ": getYx(". $sco->p . ")= " . $sco->getYx($sco->p) ." , getXy(0)=" . $sco->getXy(0);
                            if ($sco->getYx($sco->p) == -1 && $sco->getXy(0) == -1) {
                                
                                $this->log .= "\n\n\t\t==> s5: " . $sco->d[0];
                                $this->rco->a = $sco->a;
                                $this->rco->x = $sco->p; //p=input pin
                                $this->rco->y = 0;
                            
                                $s5 = new Stg($sco->d[0]);
                            }
                        }
                        
                        if ($s5 == null) {
                            continue;   //continue to next k2 (next avail s2)
                        }

                        $this->log .= "\nFound s5->a = " . $s5->a;
                        $this->log .= "\s5List[0] = " .$s5List[0];

                        if (!in_array($s5->a, $s5List)) {
                            continue;
                        }

                        // $this->log .= "\n\t" . $s5->a . "->x[0]=" . $s5->x[0];
                        // $this->log .= "\n\t" . $s5->a . "->x[1]=" . $s5->x[1];
                        // $this->log .= "\n\t" . $s5->a . "->x[2]=" . $s5->x[2];
                        // $this->log .= "\n\t" . $s5->a . "->x[3]=" . $s5->x[3];
                        // $this->log .= "\n\t" . $s5->a . "->x[4]=" . $s5->x[4];
                
                        $s5len = count($s5->y);
                        for ($k5=0; $k5<$s5len; $k5++) {

                            $e = explode('.',$s5->d[$k5]);
                            $s6a = $e[0] . '.' . $e[1] . '.' . $e[2];
                            $this->log .= "\n" . $s6a;

                            if (!in_array($s6a,$s6List)) {
                                continue;
                            }

                            $this->log .= "\n\ts5: " . $s5->a . ": getYx(". $s5->p . ")= " . $s5->getYx($s5->p) ." , getXy(" . $k5 . ")=" . $s5->getXy($k5);
                            if ($s5->getYx($s5->p) == -1 && $s5->getXy($k5) == -1) {
                                $this->r5->a = $s5->a;
                                $this->r5->x = $s5->p;
                                $this->r5->y = $s5->y[$k5];
                                $this->log .= "\n\t\t\t==> s6: " . $s5->d[$k5];

                                $s6 = new Stg($s5->d[$k5]);
                                // $this->log .= "\n\t" . $s6->a . "->x[0]=" . $s6->x[0];
                                // $this->log .= "\n\t" . $s6->a . "->x[1]=" . $s6->x[1];
                                // $this->log .= "\n\t" . $s6->a . "->x[2]=" . $s6->x[2];
                                // $this->log .= "\n\t" . $s6->a . "->x[3]=" . $s6->x[3];
                                // $this->log .= "\n\t" . $s6->a . "->x[4]=" . $s6->x[4];
                                
                                $s6len = count($s6->y);
                                for ($k6=0; $k6<$s6len; $k6++) {
                                    $e = explode(".", $s6->d[$k6]);
                                    $s7a = $e[0] . "." . $e[1] . "." . $e[2];
                                    $s7x = $e[3];
                                    $this->log .= "\n\t\t\t\t==> " . $s7a;
                                    $this->log .= "\n\ts6: " . $s6->a . ": getYx(". $s6->p . ")= " . $s6->getYx($s6->p) ." , getXy(" . $k6 . ")=" . $s6->getXy($k6);
                                    if ($s7a == $s7->a && $s6->getYx($s6->p) == -1 && $s6->getXy($k6) == -1) {                 
                                        $this->r6->a = $s6->a;
                                        $this->r6->x = $s6->p;
                                        $this->r6->y = $s6->y[$k6];
                                        $this->log .= "\n\ts7: " . $s7->a . ": getYx(". $s7x . ")= " . $s7->getYx($s7x) ." , getXy(" . $s7->p . ")=" . $s7->getXy($s7->p);
                                        if ($s7->getYx($s7x) == -1 && $s7->getXy($s7->p) == -1) {
                                            $this->r7->a = $s7->a;
                                            $this->r7->x = $s7x;
                                            $this->r7->y = $s7->p;
                                            $done = true;
                                            break;
                                        }
                                    }
                                } // k6
                                if ($done === true) {
                                    $this->log .= "\n\nfound a full PATH, done=true";
                                    $this->log .= "\n\n" . $s1->a . ':' . $s1->p . '.' . $k1;
                                    $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                                    $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.1';
                                    $this->log .= " - " . $sco->a . ':' . $sco->p . '.0';
                                    $this->log .= " - " . $s5->a . ':' . $s5->p . '.' . $k5;
                                    $this->log .= " - " . $s6->a . ':' . $s6->p . '.' . $k6;
                                    $this->log .= " - " . $s7->a . ':' . $s7x . '.' . $s1->p;
                                    $this->log .= "\n\n";
                                    break;
                                }
                                else {
                                    $this->log .= "\n" . $this->r6->a . ' has no avail output, continue next k5';
                                    $this->log .= "\n\n" . $s1->a . ':' . $s1->p . '.' . $k1;
                                    $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                                    $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.1';
                                    $this->log .= " - " . $sco->a . ':' . $sco->p . '.0';
                                    $this->log .= " - " . $s5->a . ':' . $s5->p . '.' . $k5;
                                    $this->log .= "\n\n";
                                }
                            }
                        } // k5
                        if ($done === true) {
                            $this->log .= "\n\nfound a full PATH, done=true";
                            break;
                        }
                        else {
                            $this->log .= "\n" . $this->r5->a . ' has no avail output, continue next k2';
                            $this->log .= " - " . $s1->a . ':' . $s1->p . '.' . $k1;
                            $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                            $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.1';
                            $this->log .= " - " . $sco->a . ':' . $sco->p . '.0';
                            $this->log .= "\n\n";}
                    } //k2++                    
                } // end of s2
                if ($done === true) {
                    $this->log .= "\n\nfound a full PATH, done=true";
                    break;
                }
                else {
                    $this->log .= "\n" . $this->r2->a . " has no avail output, continue next k1";
                }
            } //k1++
        } // end of s1
        return $done;
    }



    private function findPathInSameNodeDiffSectionsDescendingOrder($s1, $s3List, $s5List, $s6List, $s7) {
        $done = false;
        $this->log .= "\nFind Path In Same Node Diff Sections Descending Order";

        $s1len = count($s1->y);
        for ($k1=$s1len-1; $k1>=0; $k1--) {

            $this->log .= "\n\ts1: " . $s1->a . ": getYx(". $s1->p . ")= " . $s1->getYx($s1->p) ." , getXy(" . $k1 . ")=" . $s1->getXy($k1);
            if ($s1->getYx($s1->p) == -1 && $s1->getXy($k1) == -1) {
                $this->r1->a = $s1->a;
                $this->r1->x = $s1->p;
                $this->r1->y = $s1->y[$k1];
                $this->log .= "\n\t==> " . $s1->d[$k1];

                $s2 = new Stg($s1->d[$k1]);
                $s2len = count($s2->y);

                // $this->log .= "\n\t" . $s2->a . "->x[0]=" . $s2->x[0];
                // $this->log .= "\n\t" . $s2->a . "->x[1]=" . $s2->x[1];
                // $this->log .= "\n\t" . $s2->a . "->x[2]=" . $s2->x[2];
                // $this->log .= "\n\t" . $s2->a . "->x[3]=" . $s2->x[3];
                // $this->log .= "\n\t" . $s2->a . "->x[4]=" . $s2->x[4];
                // $this->log .= "\n\t" . $s2->a . "->x[5]=" . $s2->x[5];
                // $this->log .= "\n\t" . $s2->a . "->x[6]=" . $s2->x[6];
                // $this->log .= "\n\t" . $s2->a . "->x[7]=" . $s2->x[7];
                // $this->log .= "\n\t" . $s2->a . "->x[8]=" . $s2->x[8];
                // $this->log .= "\n\t" . $s2->a . "->x[9]=" . $s2->x[9];
                for ($k2=$s2len-1; $k2>=0; $k2--) {

                    $this->log .= "\n\ts2: " . $s2->a . ": getYx(". $s2->p . ")= " . $s2->getYx($s2->p) ." , getXy(" . $k2 . ")=" . $s2->getXy($k2);
                    if ($s2->getYx($s2->p) == -1 && $s2->getXy($k2) == -1) {
                        $this->r2->a = $s2->a;
                        $this->r2->x = $s2->p;
                        $this->r2->y = $s2->y[$k2];
                        $this->log .= "\n\t\t==> s3f: " . $s2->d[$k2];

                        $s3f = new Stg($s2->d[$k2]);
                        // $this->log .= "\n\t\t" . $s3f->a . "->x[0]=" . $s3f->x[0];
                        // $this->log .= "\n\t\t" . $s3f->a . "->x[1]=" . $s3f->x[1];
                        // $this->log .= "\n\t\t" . $s3f->a . "->x[2]=" . $s3f->x[2];

                        $s5 = null;
                        $this->log .= "\n\ts3f: " . $s3f->a . ": getYx(". $s3f->p . ")= " . $s3f->getYx($s3f->p) ." , getXy(1)=" . $s3f->getXy(1);
                        if ($s3f->getYx($s3f->p) == -1 && $s3f->getXy(1) == -1) {
                            
                            $this->log .= "\n\n\t\t==> sco: " . $s3f->d[1];
                            $this->r3f->a = $s3f->a;
                            $this->r3f->x = $s3f->p; //p=input pin
                            $this->r3f->y = 1;

                            $sco = new Stg($s3f->d[1]);
                            // $this->log .= "\n\t\t" . $sco->a . "->x[0]=" . $sco->x[0];
                            // $this->log .= "\n\t\t" . $sco->a . "->x[1]=" . $sco->x[1];
                            // $this->log .= "\n\t\t" . $sco->a . "->x[2]=" . $sco->x[2];
                            if (!in_array($sco->a, $s3List)) {
                                continue;
                            }
                            $this->log .= "\n\tsco: " . $sco->a . ": getYx(". $sco->p . ")= " . $sco->getYx($sco->p) ." , getXy(0)=" . $sco->getXy(0);
                            if ($sco->getYx($sco->p) == -1 && $sco->getXy(0) == -1) {
                                
                                $this->log .= "\n\n\t\t==> s5: " . $sco->d[0];
                                $this->rco->a = $sco->a;
                                $this->rco->x = $sco->p; //p=input pin
                                $this->rco->y = 0;
                            
                                $s5 = new Stg($sco->d[0]);
                            }
                        }
                        
                        if ($s5 == null) {
                            continue;   //continue to next k2 (next avail s2)
                        }

                        $this->log .= "\nFound s5->a = " . $s5->a;
                        $this->log .= "\s5List[0] = " .$s5List[0];
                        

                        if (!in_array($s5->a, $s5List)) {
                            continue;
                        }

                        // $this->log .= "\n\t" . $s5->a . "->x[0]=" . $s5->x[0];
                        // $this->log .= "\n\t" . $s5->a . "->x[1]=" . $s5->x[1];
                        // $this->log .= "\n\t" . $s5->a . "->x[2]=" . $s5->x[2];
                        // $this->log .= "\n\t" . $s5->a . "->x[3]=" . $s5->x[3];
                        // $this->log .= "\n\t" . $s5->a . "->x[4]=" . $s5->x[4];
                
                        $s5len = count($s5->y);
                        for ($k5=$s5len-1; $k5>=0; $k5--) {

                            $e = explode('.',$s5->d[$k5]);
                            $s6a = $e[0] . '.' . $e[1] . '.' . $e[2];
                            $this->log .= "\n" . $s6a;

                            if (!in_array($s6a,$s6List)) {
                                continue;
                            }

                            $this->log .= "\n\ts5: " . $s5->a . ": getYx(". $s5->p . ")= " . $s5->getYx($s5->p) ." , getXy(" . $k5 . ")=" . $s5->getXy($k5);
                            if ($s5->getYx($s5->p) == -1 && $s5->getXy($k5) == -1) {
                                $this->r5->a = $s5->a;
                                $this->r5->x = $s5->p;
                                $this->r5->y = $s5->y[$k5];
                                $this->log .= "\n\t\t\t==> s6: " . $s5->d[$k5];

                                $s6 = new Stg($s5->d[$k5]);
                                // $this->log .= "\n\t" . $s6->a . "->x[0]=" . $s6->x[0];
                                // $this->log .= "\n\t" . $s6->a . "->x[1]=" . $s6->x[1];
                                // $this->log .= "\n\t" . $s6->a . "->x[2]=" . $s6->x[2];
                                // $this->log .= "\n\t" . $s6->a . "->x[3]=" . $s6->x[3];
                                // $this->log .= "\n\t" . $s6->a . "->x[4]=" . $s6->x[4];
                                
                                $s6len = count($s6->y);
                                for ($k6=$s6len-1; $k6>=0; $k6--) {
                                    $e = explode(".", $s6->d[$k6]);
                                    $s7a = $e[0] . "." . $e[1] . "." . $e[2];
                                    $s7x = $e[3];
                                    $this->log .= "\n\t\t\t\t==> " . $s7a;
                                    $this->log .= "\n\ts6: " . $s6->a . ": getYx(". $s6->p . ")= " . $s6->getYx($s6->p) ." , getXy(" . $k6 . ")=" . $s6->getXy($k6);
                                    if ($s7a == $s7->a && $s6->getYx($s6->p) == -1 && $s6->getXy($k6) == -1) {                 
                                        $this->r6->a = $s6->a;
                                        $this->r6->x = $s6->p;
                                        $this->r6->y = $s6->y[$k6];
                                        $this->log .= "\n\ts7: " . $s7->a . ": getYx(". $s7x . ")= " . $s7->getYx($s7x) ." , getXy(" . $s7->p . ")=" . $s7->getXy($s7->p);
                                        if ($s7->getYx($s7x) == -1 && $s7->getXy($s7->p) == -1) {
                                            $this->r7->a = $s7->a;
                                            $this->r7->x = $s7x;
                                            $this->r7->y = $s7->p;
                                            $done = true;
                                            break;
                                        }
                                    }
                                } // k6--
                                if ($done === true) {
                                    $this->log .= "\n\nfound a full PATH, done=true";
                                    $this->log .= "\n\n" . $s1->a . ':' . $s1->p . '.' . $k1;
                                    $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                                    $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.1';
                                    $this->log .= " - " . $sco->a . ':' . $sco->p . '.0';
                                    $this->log .= " - " . $s5->a . ':' . $s5->p . '.' . $k5;
                                    $this->log .= " - " . $s6->a . ':' . $s6->p . '.' . $k6;
                                    $this->log .= " - " . $s7->a . ':' . $s7x . '.' . $s1->p;
                                    $this->log .= "\n\n";
                                    break;
                                }
                                else {
                                    $this->log .= "\n" . $this->r6->a . ' has no avail output, continue next k5';
                                    $this->log .= "\n\n" . $s1->a . ':' . $s1->p . '.' . $k1;
                                    $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                                    $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.1';
                                    $this->log .= " - " . $sco->a . ':' . $sco->p . '.0';
                                    $this->log .= " - " . $s5->a . ':' . $s5->p . '.' . $k5;
                                    $this->log .= "\n\n";
                                }
                            }
                        } // k5--
                        if ($done === true) {
                            $this->log .= "\n\nfound a full PATH, done=true";
                            break;
                        }
                        else {
                            $this->log .= "\n" . $this->r5->a . ' has no avail output, continue next k2';
                            $this->log .= " - " . $s1->a . ':' . $s1->p . '.' . $k1;
                            $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                            $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.1';
                            $this->log .= " - " . $sco->a . ':' . $sco->p . '.0';
                            $this->log .= "\n\n";}
                    } //k2--
                } // end of s2
                if ($done === true) {
                    $this->log .= "\n\nfound a full PATH, done=true";
                    break;
                }
                else {
                    $this->log .= "\n" . $this->r2->a . " has no avail output, continue next k1";
                }
            } //k1--
        } // end of s1
        return $done;
    }

    private function connectBetweenNodesSameSection() {
        $done = FALSE;
        $this->log = '';

        $this->log .= "begin connect Between Nodes Same Section() \n";

        // 1) locate s1 array
        $X = new X($this->x);
        $this->log .= 'X port = ' . $this->x;
        $this->log .= "\nY port = " . $this->y;
        $this->log .= "\n1) locate s1:";
        $s1 = new Stg($X->d);
        $this->log .= "\n\tX->d => s1->a: " . $X->d;

        // if input x=p is connected to any output y, the port is already in use
        if ($s1->getYx($s1->p) != -1) {
            $this->rslt = 'fail';
            $this->log .= "\nPORT: " . $this->x . "IS BUSY";
            return;
        }

        // 2) locate s7 array
        $Y = new Y($this->y);
        $this->log .= "\n\n" . "2) locate s7:";
        $s7 = new Stg($Y->d);
        $this->log .= "\n\tY->d => s7->a:" . $Y->d;
        // if output y=p is connected to any input x, the port is already in use
        if ($s7->getXy($s7->p) != -1) {
            $this->rslt = 'fail';
            $this->log .= 'PORT: ' . $this->y . 'IS BUSY';
            return;
        }

        // get list of Possible s6's
        $s6List = [];
        $rows = $s7->findListOfPossibleS6($Y->d);
        $this->log .= "\n\tList of possible s6:\n";

        $len = count($rows);
        for ($i=0; $i<$len; $i++) {
            $e = explode('.',$rows[$i]['a']);
            $s6List[] = $e[0] . '.' . $e[1] . '.' . $e[2];
            $this->log .= "\t" . $rows[$i]['a'];
        }
        
        // 3) Search for avail output y path to s2 array
        $s1len = count($s1->y);

        for ($k1=0; $k1<$s1len; $k1++) {

            $this->log .= "\n\ts1: " . $s1->a . ", getYx(".$s1->p.")=".$s1->getYx($s1->p)." , getXy(".$k1.")=".$s1->getXy($k1);
            if ($s1->getYx($s1->p) == -1 && $s1->getXy($k1) == -1) {
                $this->r1->a = $s1->a;
                $this->r1->x = $s1->p;
                $this->r1->y = $s1->y[$k1];
                $this->log .= "\n\t==> s2: " . $s1->d[$k1];

                $s2 = new Stg($s1->d[$k1]);
                $s2len = count($s2->y);

                for ($k2=0; $k2<$s2len; $k2++) {

                    $this->log .= "\n\ts2: " . $s2->a . ", getYx(".$s2->p.")=".$s2->getYx($s2->p)." , getXy(".$k2.")=".$s2->getXy($k2);
                    if ($s2->getYx($s2->p) == -1 && $s2->getXy($k2) == -1) {
                        $this->r2->a = $s2->a;
                        $this->r2->x = $s2->p;
                        $this->r2->y = $s2->y[$k2];
                        $this->log .= "\n\t\t==> s3f: " . $s2->d[$k2];

                        $s3f = new Stg($s2->d[$k2]);
                        // $this->log .= "\n\t\ts3f->x[0]=" . $s3f->x[0];
                        // $this->log .= "\n\t\ts3f->x[1]=" . $s3f->x[1];
                        // $this->log .= "\n\t\ts3f->x[2]=" . $s3f->x[2];

                        $s5 = null;
                        $this->log .= "\n\ts3f: " . $s3f->a . ", getYx(".$s3f->p.")=".$s3f->getYx($s3f->p)." , getXy(2)=".$s3f->getXy(2);
                        if ($s3f->getYx($s3f->p) == -1 && $s3f->getXy(2) == -1) {
                            
                            $this->log .= "\n\n\t\t==> s4: " . $s3f->d[2];
                            $this->r3f->a = $s3f->a;
                            $this->r3f->x = $s3f->p; //p=input pin
                            $this->r3f->y = 2;

                            $s4 = new Stg($s3f->d[2]);
                            $this->log .= "\n\ts4: " . $s4->a . ", getYx(".$s4->p.")=".$s4->getYx($s4->p)." , getXy(".$s4->p.")=".$s4->getXy($s7->u);
                            if ($s4->getYx($s4->p) == -1 && $s4->getXy($s7->u) == -1) {
                               
                                $this->r4->a = $s4->a;
                                $this->r4->x = $s4->p; //from node
                                $this->r4->y = $s7->u;  //to node

                                $this->log .= "\n\n\t\t==> sci: " . $s4->d[$s7->u];
                                $sci = new Stg($s4->d[$s7->u]);

                                $this->log .= "\n\tsci: " . $sci->a . ", getYx(".$sci->p.")=".$sci->getYx($sci->p)." , getXy(2)=".$sci->getXy(2);
                                if ($sci->getYx(2) == -1 && $sci->getXy(0) == -1) {
                                    
                                    $this->rci->a = $sci->a;
                                    $this->rci->x = 2; //from external
                                    $this->rci->y = 0;  //to same section
                                    $this->log .= "\n\n\t\t==> s5: " . $sci->d[0];

                                    $s5 = new Stg($sci->d[0]);
                                }
                            }
                        }

                        if ($s5 == null) {
                            continue;   //continue to next k2 (next avail s2)
                        }
                            
                        $s5len = count($s5->y);
                        for ($k5=0; $k5<$s5len; $k5++) {

                            $e = explode('.',$s5->d[$k5]);
                            $s6a = $e[0] . '.' . $e[1] . '.' . $e[2];
                            $this->log .= "\n" . $s6a;

                            if (!in_array($s6a,$s6List)) {
                                continue;
                            }

                            $this->log .= "\n\ts5: " . $s5->a . ", getYx(".$s5->p.")=".$s5->getYx($s5->p)." , getXy(".$k5.")=".$s5->getXy($k5);
                            if ($s5->getYx($s5->p) == -1 && $s5->getXy($k5) == -1) {
                                $this->r5->a = $s5->a;
                                $this->r5->x = $s5->p;
                                $this->r5->y = $s5->y[$k5];
                                $this->log .= "\n\t\t\t==> s6: " . $s5->d[$k5];

                                $s6 = new Stg($s5->d[$k5]);
                                $s6len = count($s6->y);
                                for ($k6=0; $k6<$s6len; $k6++) {

                                    $e = explode(".", $s6->d[$k6]);
                                    $s7a = $e[0] . "." . $e[1] . "." . $e[2];
                                    $s7x = $e[3];
                                    $this->log .= "\n\t\t\t\t==> s7: " . $s7a;

                                    $this->log .= "\n\ts6: " . $s6->a . ", getYx(".$s6->p.")=".$s6->getYx($s6->p)." , getXy(".$k6.")=".$s6->getXy($k6);
                                    if ($s7a == $s7->a && $s6->getYx($s6->p) == -1 && $s6->getXy($k6) == -1) {                 
                                        $this->r6->a = $s6->a;
                                        $this->r6->x = $s6->p;
                                        $this->r6->y = $s6->y[$k6];

                                        $this->log .= "\n\ts7: " . $s7->a . ", getYx(".$s7x.")=".$s7->getYx($s7x)." , getXy(".$s7->p.")=".$s7->getXy($s7->p);

                                        if ($s7->getYx($s7x) == -1 && $s7->getXy($s7->p) == -1) {
                                            $this->r7->a = $s7->a;
                                            $this->r7->x = $s7x;
                                            $this->r7->y = $s7->p;
                                            $done = true;
                                            break;
                                        }
                                    }
                                } // k6
                                if ($done === true) {
                                    $this->log .= "\n\nfound a full PATH, done=true";
                                    $this->log .= "\n\n" . $s1->a . ':' . $s1->p . '.' . $k1;
                                    $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                                    $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.2';
                                    $this->log .= " - " . $s4->a . ':' . $s4->p . '.' . $s7->u;
                                    $this->log .= " - " . $sci->a . ':' . $sci->p . '.0';
                                    $this->log .= " - " . $s5->a . ':' . $s5->p . '.' . $k5;
                                    $this->log .= " - " . $s6->a . ':' . $s6->p . '.' . $k6;
                                    $this->log .= " - " . $s7->a . ':' . $s7x . '.' . $s1->p;
                                    $this->log .= "\n\n";
                                    break;
                                }
                                else {
                                    $this->log .= "\n" . $this->r6->a . ' has no avail output, continue next k5';
                                    $this->log .= "\n\n" . $s1->a . ':' . $s1->p . '.' . $k1;
                                    $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                                    $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.2';
                                    $this->log .= " - " . $s4->a . ':' . $s4->p . '.' . $s7->u;
                                    $this->log .= " - " . $sci->a . ':' . $sci->p . '.0';
                                    $this->log .= " - " . $s5->a . ':' . $s5->p . '.' . $k5;
                                    $this->log .= "\n\n";
                                }
                            }
                        } // k5
                        if ($done === true) {
                            $this->log .= "\n\nfound a full PATH, done=true";
                            break;
                        }
                        else {
                            $this->log .= "\n" . $this->r5->a . ' has no avail output, continue next k2';
                            $this->log .= " - " . $s1->a . ':' . $s1->p . '.' . $k1;
                            $this->log .= " - " . $s2->a . ':' . $s2->p . '.' . $k2;
                            $this->log .= " - " . $s3f->a . ':' . $s3f->p . '.2';
                            $this->log .= "\n\n";}
                    } //k2++                    
                } // end of s2
                if ($done === true) {
                    $this->log .= "\n\nfound a full PATH, done=true";
                    break;
                }
                else {
                    $this->log .= "\n" . $this->r2->a . " has no avail output, continue next k1";
                }
            } //k1++
        } // end of s1
        if ($done === true) {

            $this->save();           
            if ($this->rslt == 'success') {
                $this->reason = "PATH_CREATED";
            }
            // else {
            //     $this->reason = "PATH_HAS_DUPLICATED_STG";
            // }
        }
        else {
            $this->rslt = 'fail';
            $this->reason = "INCOMPLETE_PATH";
        }

    }

    private function connectBetweenNodesDiffSections() {
        $done = FALSE;
        $this->log = '';

        $this->log .= "begin connect Between Nodes Diff Sections() \n";

        // 1) locate s1 array
        $X = new X($this->x);
        $this->log .= 'X port = ' . $this->x;
        $this->log .= "\nY port = " . $this->y;
        $this->log .= "\n1) locate s1:";
        $s1 = new Stg($X->d);
        $this->log .= "\tX->d => s1->a: " . $X->d;

        // if input x=p is connected to any output y, the port is already in use
        if ($s1->getYx($s1->p) != -1) {
            $this->rslt = 'fail';
            $this->log .= "\nPORT: " . $this->x . "IS BUSY";
            return;
        }

        // 2) locate s7 array
        $Y = new Y($this->y);
        $this->log .= "\n\n" . "2) locate s7:";
        $s7 = new Stg($Y->d);
        $this->log .= "\tY->d => s7->a: " . $Y->d;
        // if output y=p is connected to any input x, the port is already in use
        if ($s7->getXy($s7->p) != -1) {
            $this->rslt = 'fail';
            $this->log .= 'PORT: ' . $this->y . 'IS BUSY';
            return;
        }

        // get list of Possible s6's
        $s6List = [];
        $s5List = [];
        // get list of Possible s6's
        $s6rows = $s7->findListOfPossibleS6($Y->d);
        $this->log .= "\nList of possible s6's and s5's:\n";

        $s6len = count($s6rows);
        for ($i=0; $i<$s6len; $i++) {
            $e6 = explode('.',$s6rows[$i]['a']);
            $s6List[] = $e6[0] . '.' . $e6[1] . '.' . $e6[2];
            $this->log .= "\n" . $s6rows[$i]['a'] . "\n";
            $s5rows = $s7->findListOfPossibleS5($s6rows[$i]['a']);
            $s5len = count($s5rows);
            for ($j=0; $j<$s5len; $j++) {
                $e5 = explode('.',$s5rows[$j]['a']);
                $s5List[] = $e5[0] . '.' . $e5[1] . '.' . $e5[2];
                $this->log .= "\t" . $s5rows[$j]['a'];
            }
        }
 

        // 3) Search for avail output y path to s2 array

        // Start from X-slot, use S1-S2-S3F-S4-SCI-S3T route to find avail S5 toward Y port
        // if X-slot < Y-slot, search avail path using ascending order
        // if X-slot > Y-slot, search avail path using descending order

        if ($X->n < $Y->n)
            $done = $this->findPathInBetweenNodesDiffSectionsAscendingOrder($s1, $s5List, $s6List, $s7);
        else
            $done = $this->findPathInBetweenNodesDiffSectionsDescendingOrder($s1, $s5List, $s6List, $s7);

        if ($done === true) {

            $this->save();           
            if ($this->rslt == 'success') {
                $this->reason = "PATH_CREATED";
            }
        }
        else {
            $this->rslt = 'fail';
            $this->reason = "INCOMPLETE_PATH";
        }
    }


    private function findPathInBetweenNodesDiffSectionsDescendingOrder($s1, $s5List, $s6List, $s7) {
        $done = false;
        $this->log .= "\nFind Path In Between Nodes Diff Sections Descending Order";

        $s1len = count($s1->y);
        for ($k1=$s1len-1; $k1>=0; $k1--) {

            $this->log .= "\n\n\ts1: " . $s1->a . ", getYx(".$s1->p.")=".$s1->getYx($s1->p)." , getXy(".$k1.")=".$s1->getXy($k1);
            if ($s1->getYx($s1->p) == -1 && $s1->getXy($k1) == -1) {
                $this->r1->a = $s1->a;
                $this->r1->x = $s1->p;
                $this->r1->y = $s1->y[$k1];
                $this->log .= "\n\t==> s2: " . $s1->d[$k1];

                $s2 = new Stg($s1->d[$k1]);

                $s2len = count($s2->y);
                for ($k2=$s2len-1; $k2>=0; $k2--) {

                    $this->log .= "\n\n\ts2: " . $s2->a . ", getYx(".$s2->p.")=".$s2->getYx($s2->p)." , getXy(".$k2.")=".$s2->getXy($k2);
                    if ($s2->getYx($s2->p) == -1 && $s2->getXy($k2) == -1) {
                        $this->r2->a = $s2->a;
                        $this->r2->x = $s2->p;
                        $this->r2->y = $s2->y[$k2];
                        $this->log .= "\n\t==> s3f: " . $s2->d[$k2];

                        $s3f = new Stg($s2->d[$k2]);
                        
                        $s5 = null;
                        //$s5 = $this->findAvailS5ViaS3fSci($s3f, $s7); //cross node first then cross sections
                        //if ($s5 == null) {
                            $s5 = $this->findAvailS5ViaScoSci($s3f, $s7); //cross sections first, then cross nodes
                        //}
                    
                        
                        if ($s5 == null) {
                            continue;
                        }

                        // skip if S5 is not in the S5LIST
                        if (!in_array($s5->a, $s5List)) {
                            continue;
                        }

                        $s5len = count($s5->y);
                        for ($k5=$s5len-1; $k5>=0; $k5--) {
                            $e = explode('.',$s5->d[$k5]);
                            $s6a = $e[0] . '.' . $e[1] . '.' . $e[2];
                            $this->log .= "\n" . $s6a;

                            if (!in_array($s6a, $s6List)) {
                                continue;
                            }
                            $this->log .= "\n\ts5->a: " . $s5->a . ", getYx=(".$s5->p.")=" . $s5->getYx($s5->p) . ", getXy(".$k5.")=" . $s5->getYx($k5);
                            if ($s5->getYx($s5->p) == -1 && $s5->getXy($k5) == -1) {
                                $this->r5->a = $s5->a;
                                $this->r5->x = $s5->p;
                                $this->r5->y = $s5->y[$k5];
                                $this->log .= "\n\t==> s6: " . $s5->d[$k5];

                                $s6 = new Stg($s5->d[$k5]);
                                $s6len = count($s6->y);
                                for ($k6=$s6len-1; $k6>=0; $k6--) {
                                    $this->log .= "\n\n" . "7) Find avail path to s7: " . $s7->a;
                                    $e = explode(".", $s6->d[$k6]);
                                    $s7a = $e[0] . "." . $e[1] . "." . $e[2];
                                    $s7x = $e[3];
                                    $this->log .= "\n\t==> s7: " . $s7a;

                                    $this->log .= "\n\ts6->a: " . $s6->a . ", getYx(". $s6->p . ")=" . $s6->getYx($s6->p) . ", getXy(".$k6.")=" . $s6->getXy($k6) . ", s6->d=".$s6->d[$k6];
                                    if ($s7a == $s7->a && $s6->getYx($s6->p) == -1 && $s6->getXy($k6) == -1) {                 
                                        $this->r6->a = $s6->a;
                                        $this->r6->x = $s6->p;
                                        $this->r6->y = $s6->y[$k6];

                                        $this->log .= "\n\ts7->a: " . $s7->a . ", getYx(".$s7x.")=" . $s7->getYx($s7x) . ", getXy(".$s7->p.")=" . $s7->getXy($s7->p);
                                        if ($s7->getYx($s7x) == -1 && $s7->getXy($s7->p) == -1) {
                                            $this->r7->a = $s7->a;
                                            $this->r7->x = $s7x;
                                            $this->r7->y = $s7->p;
                                            $done = true;
                                            break;
                                        }
                                    }
                                } // k6--
                                if ($done === true) {
                                    $this->log .= "\n\nfound a full PATH, done=true";
                                    break;
                                }
                                else {
                                    $this->log .= "\n" . $this->r6->a . ' has no avail output, continue next k5';
                                    $this->log .= "\n\n";
                                }
                            }
                        } // k5--
                        if ($done === true) {
                            $this->log .= "\n\nfound a full PATH, done=true";
                            break;
                        }
                        else {
                            $this->log .= "\n" . $this->r5->a . ' has no avail output, continue next k2';
                            $this->log .= "\n\n";
                        }
                    } //k2--
                } // end of s2
                if ($done === true) {
                    $this->log .= "\n\nfound a full PATH, done=true";
                    break;
                }
                else {
                    $this->log .= "\n" . $this->r2->a . " has no avail output, continue next k1";
                }
            } //k1--
        } // end of s1
        return $done;
    }



    private function findPathInBetweenNodesDiffSectionsAscendingOrder($s1, $s5List, $s6List, $s7) {
        $done = false;
        $this->log .= "\nFind Path In Between Nodes Diff Sections Ascending Order";

        $s1len = count($s1->y);
        for ($k1=0; $k1<$s1len; $k1++) {

            $this->log .= "\n\n\ts1: " . $s1->a . ", getYx(".$s1->p.")=".$s1->getYx($s1->p)." , getXy(".$k1.")=".$s1->getXy($k1);
            if ($s1->getYx($s1->p) == -1 && $s1->getXy($k1) == -1) {
                $this->r1->a = $s1->a;
                $this->r1->x = $s1->p;
                $this->r1->y = $s1->y[$k1];
                $this->log .= "\n\t==> s2: " . $s1->d[$k1];

                $s2 = new Stg($s1->d[$k1]);

                $s2len = count($s2->y);
                for ($k2=0; $k2<$s2len; $k2++) {

                    $this->log .= "\n\n\ts2: " . $s2->a . ", getYx(".$s2->p.")=".$s2->getYx($s2->p)." , getXy(".$k2.")=".$s2->getXy($k2);
                    if ($s2->getYx($s2->p) == -1 && $s2->getXy($k2) == -1) {
                        $this->r2->a = $s2->a;
                        $this->r2->x = $s2->p;
                        $this->r2->y = $s2->y[$k2];
                        $this->log .= "\n\t==> s3f: " . $s2->d[$k2];

                        $s3f = new Stg($s2->d[$k2]);
                        
                        $s5 = null;
                        //$s5 = $this->findAvailS5ViaS3fSci($s3f, $s7); //cross node first then cross sections
                        //if ($s5 == null) {
                            $s5 = $this->findAvailS5ViaScoSci($s3f, $s7);
                        //}

                        if ($s5 == null) {
                            continue;
                        }

                        // skip if S5 is not in the S5LIST
                        if (!in_array($s5->a, $s5List)) {
                            continue;
                        }

                        $s5len = count($s5->y);
                        for ($k5=0; $k5<$s5len; $k5++) {
                            $e = explode('.',$s5->d[$k5]);
                            $s6a = $e[0] . '.' . $e[1] . '.' . $e[2];
                            $this->log .= "\n" . $s6a;

                            if (!in_array($s6a, $s6List)) {
                                continue;
                            }
                            $this->log .= "\n\ts5->a: " . $s5->a . ", getYx=(".$s5->p.")=" . $s5->getYx($s5->p) . ", getXy(".$k5.")=" . $s5->getYx($k5);
                            if ($s5->getYx($s5->p) == -1 && $s5->getXy($k5) == -1) {
                                $this->r5->a = $s5->a;
                                $this->r5->x = $s5->p;
                                $this->r5->y = $s5->y[$k5];
                                $this->log .= "\n\t==> s6: " . $s5->d[$k5];

                                $s6 = new Stg($s5->d[$k5]);
                                $s6len = count($s6->y);
                                for ($k6=0; $k6<$s6len; $k6++) {
                                    $this->log .= "\n\n" . "7) Find avail path to s7: " . $s7->a;
                                    $e = explode(".", $s6->d[$k6]);
                                    $s7a = $e[0] . "." . $e[1] . "." . $e[2];
                                    $s7x = $e[3];
                                    $this->log .= "\n\t==> s7: " . $s7a;

                                    $this->log .= "\n\ts6->a: " . $s6->a . ", getYx(". $s6->p . ")=" . $s6->getYx($s6->p) . ", getXy(".$k6.")=" . $s6->getXy($k6) . ", s6->d=".$s6->d[$k6];
                                    if ($s7a == $s7->a && $s6->getYx($s6->p) == -1 && $s6->getXy($k6) == -1) {                 
                                        $this->r6->a = $s6->a;
                                        $this->r6->x = $s6->p;
                                        $this->r6->y = $s6->y[$k6];

                                        $this->log .= "\n\ts7->a: " . $s7->a . ", getYx(".$s7x.")=" . $s7->getYx($s7x) . ", getXy(".$s7->p.")=" . $s7->getXy($s7->p);
                                        if ($s7->getYx($s7x) == -1 && $s7->getXy($s7->p) == -1) {
                                            $this->r7->a = $s7->a;
                                            $this->r7->x = $s7x;
                                            $this->r7->y = $s7->p;
                                            $done = true;
                                            break;
                                        }
                                    }
                                } // k6
                                if ($done === true) {
                                    $this->log .= "\n\nfound a full PATH, done=true";
                                    break;
                                }
                                else {
                                    $this->log .= "\n" . $this->r6->a . ' has no avail output, continue next k5';
                                    $this->log .= "\n\n";
                                }
                            }
                        } // k5
                        if ($done === true) {
                            $this->log .= "\n\nfound a full PATH, done=true";
                            break;
                        }
                        else {
                            $this->log .= "\n" . $this->r5->a . ' has no avail output, continue next k2';
                            $this->log .= "\n\n";
                        }
                    } //k2++                    
                } // end of s2
                if ($done === true) {
                    $this->log .= "\n\nfound a full PATH, done=true";
                    break;
                }
                else {
                    $this->log .= "\n" . $this->r2->a . " has no avail output, continue next k1";
                }
            } //k1++
        } // end of s1
        return $done;
    }


    private function findAvailS5ViaS3fSco($s3f, $s5List) {
        $this->log .= "\n\nFind Avail S5 Via S3f Sco\n";
        $s5 = null;
        // $this->log .= "\ts3f->x[0]=" . $s3f->x[0];
        // $this->log .= "\ts3f->x[1]=" . $s3f->x[1];
        // $this->log .= "\ts3f->x[2]=" . $s3f->x[2];

        $this->log .= "\n\ts3f : ".$s3f->a . ", getYx(".$s3f->p.")=" . $s3f->getYx($s3f->p) . ", getXy(1)=" . $s3f->getXy(1);
        if ($s3f->getYx($s3f->p) == -1 && $s3f->getXy(1) == -1) {
            $this->r3f->a = $s3f->a;
            $this->r3f->x = $s3f->p;
            $this->r3f->y = 1;

            $this->log .= "\n\t==> sco: " . $s3f->d[1];
            $sco = new Stg($s3f->d[1]);
            $this->log .= "\n\tsco : ".$sco->a . ", getYx(".$sco->p.")=" . $sco->getYx($sco->p) . ", getXy(0)=" . $sco->getXy(0);
            if ($sco->getYx($sco->p) == -1 && $sco->getXy(0) == -1) {
                $this->rco->a = $sco->a;
                $this->rco->x = $sco->p;
                $this->rco->y = 0;

                $this->log .= "\n\t==> s5: " . $sco->d[0];
                $s5 = new Stg($sco->d[0]);
            }
        }
        return $s5;
    }


    // Cross Sections First, then Cross Nodes
    private function findAvailS5ViaScoSci($s3f, $s7) {
        $this->log .= "\n\nFind Avail S5 Via Sco Sci\n";
        $s5 = null;
        // $this->log .= "\ts3f->x[0]=" . $s3f->x[0];
        // $this->log .= "\ts3f->x[1]=" . $s3f->x[1];
        // $this->log .= "\ts3f->x[2]=" . $s3f->x[2];

        $this->log .= "\n\ts3f : ".$s3f->a . ", getYx(".$s3f->p.")=" . $s3f->getYx($s3f->p) . ", getXy(1)=" . $s3f->getXy(1);
        if ($s3f->getYx($s3f->p) == -1 && $s3f->getXy(1) == -1) {
            /* clear s3t */
            $this->r3t->a = '';
            $this->r3t->x = -1;
            $this->r3t->y = -1;

            /* try routing thru s3f->sco->s4->sci->s5 */
            $this->r3f->a = $s3f->a;
            $this->r3f->x = $s3f->p;
            $this->r3f->y = 1;

            $this->log .= "\n\t==> sco: " . $s3f->d[1];
            $sco = new Stg($s3f->d[1]);

            $this->log .= "\n\tsco : ".$sco->a . ", getYx(".$sco->p.")=" . $sco->getYx($sco->p) . ", getXy(2)=" . $sco->getXy(2);
            if ($sco->getYx($sco->p) == -1 && $sco->getXy(2) == -1) {
                $this->rco->a = $sco->a;
                $this->rco->x = $sco->p;
                $this->rco->y = 2;

                $this->log .= "\n\t==> s4: " . $sco->d[2];
                $s4 = new Stg($sco->d[2]);

                $this->log .= "\n\ts4 : ".$s4->a . ", getYx(".$s4->p.")=" . $s4->getYx($s4->p) . ", getXy(".$s7->u.")=" . $s3f->getXy($s7->u);
                if ($s4->getYx($s4->p) == -1 && $s4->getXy($s7->u) == -1) {
                    $this->r4->a = $s4->a;
                    $this->r4->x = $s4->p;
                    $this->r4->y = $s7->u;

                    $this->log .= "\n\t==> sci: " . $s4->d[$s7->u];
                    $sci = new Stg($s4->d[$s7->u]);

                    $this->log .= "\n\tsci : ".$sci->a . ", getYx(".$sci->p.")=" . $sci->getYx($sci->p) . ", getXy(0)=" . $sci->getXy(0);
                    if ($sci->getYx($sci->p) == -1 && $sci->getXy(0) == -1) {
                        $this->rci->a = $sci->a;
                        $this->rci->x = $sci->p;
                        $this->rci->y = 0;

                        $this->log .= "\n\t==> s5: " . $sci->d[0];
                        $s5 = new Stg($sci->d[0]);
                    }
                }
            }
        }
        return $s5;
    }

    // private function findAvailS5ViaS3fSci($s3f, $s7) {

    //     $this->log .= "\n\nFind Avail S5 Via S3f Sci";

    //     $s5 = null;
    //     // $this->log .= "\ts3f->x[0]=" . $s3f->x[0];
    //     // $this->log .= "\ts3f->x[1]=" . $s3f->x[1];
    //     // $this->log .= "\ts3f->x[2]=" . $s3f->x[2];

    //     $this->log .= "\n\ts3f: " . $s3f->a . ", getYx(".$s3f->p.")=".$s3f->getYx($s3f->p)." , getXy(2)=".$s3f->getXy(2);
    //     if ($s3f->getYx($s3f->p) == -1 && $s3f->getXy(2) == -1){
    //         /* clear sco */
    //         $this->rco->a = '';
    //         $this->rco->x = -1;
    //         $this->rco->y = -1;

    //         /* first, try routing thru s3f->s4->sci->s3t->s5 */
    //         $this->r3f->a = $s3f->a;
    //         $this->r3f->x = $s3f->p; //p=input pin
    //         $this->r3f->y = 2;

    //         $this->log .= "\n\t==> s4: " . $s3f->d[2];
    //         $s4 = new Stg($s3f->d[2]);

    //         $this->log .= "\n\ts4: " . $s4->a . ", getYx(".$s4->p.")=".$s4->getYx($s4->p)." , getXy(".$s7->u.")=".$s4->getXy($s7->u);
    //         if ($s4->getYx($s4->p) == -1 && $s4->getXy($s7->u) == -1) {

    //             $this->r4->a = $s4->a;
    //             $this->r4->x = $s4->p; //from node
    //             $this->r4->y = $s7->u;  //to node

    //             $this->log .= "\n\t==> sci: " . $s4->d[$s7->u];
    //             $sci = new Stg($s4->d[$s7->u]);
    //             // $this->log .= "\tsci->x[0]=" . $sci->x[0];
    //             // $this->log .= "\tsci->x[1]=" . $sci->x[1];
    //             // $this->log .= "\tsci->x[2]=" . $sci->x[2];

    //             $this->log .= "\n\tsci: " . $sci->a . "getYx(".$sci->p.")=" . $sci->getYx($sci->p) . ", getXy(1)=" . $sci->getXy(1);
    //             if ($sci->getYx($sci->p) == -1 && $sci->getXy(1) == -1) {

    //                 $this->rci->a = $sci->a;
    //                 $this->rci->x = $sci->p; //from external
    //                 $this->rci->y = 1;  //to diff section

    //                 $this->log .= "\n\t==> s3t: " . $sci->d[1];
    //                 $s3t = new Stg($sci->d[1]);
    //                 // $this->log .= "\ts3t->x[0]=" . $s3t->x[0];
    //                 // $this->log .= "\ts3t->x[1]=" . $s3t->x[1];
    //                 // $this->log .= "\ts3t->x[2]=" . $s3t->x[2];

    //                 $this->log .= "\n\ts3t: " . $s3t->a . ", getYx(" . $s3t->p . ")=" . $s3t->getYx($s3t->p) . ", getXy(0)=" . $s3t->getXy(0);
    //                 if ($s3t->getYx($s3t->p) == -1 && $s3t->getXy(0) == -1) {
    //                     $this->r3t->a = $s3t->a;
    //                     $this->r3t->x = $s3t->p; //from other section
    //                     $this->r3t->y = 0;  //to same section

    //                     $this->log .= "\n\t==> s5: " . $s3t->d[0];
    //                     $s5 = new Stg($s3t->d[0]);
    //                 }
    //             }
    //         }
    //     }
    //     return $s5;
    // }



    public function setPath() {
        if ($this->r1->a != "" && $this->r1->a !== NULL) {
            $a = explode(":",$this->r1->a);
            $d = $a[0] . "." . $this->r1->x;
            $s = new Stg($d);
            $s->setXY($this->r1->x, $this->r1->y, $this->id);
        }
        if ($this->r2->a != "" && $this->r2->a !== NULL) {
            $a = explode(":",$this->r2->a);
            $d = $a[0] . "." . $this->r2->x;
            $s = new Stg($d);
            $s->setXY($this->r2->x, $this->r2->y, $this->id);
        }
        if ($this->r3f->a != "" && $this->r3f->a !== NULL) {
            $a = explode(":",$this->r3f->a);
            $d = $a[0] . "." . $this->r3f->x;
            $s = new Stg($d);
            $s->setXY($this->r3f->x, $this->r3f->y, $this->id);
        }
        if ($this->rco->a != "" && $this->rco->a !== NULL) {
            $a = explode(":",$this->rco->a);
            if ($a[0] != '') {
                $d = $a[0] . "." . $this->rco->x;
                $s = new Stg($d);
                $s->setXY($this->rco->x, $this->rco->y, $this->id);
            }
        }
        if ($this->r4->a != "" && $this->r4->a !== NULL) {
            $a = explode(":",$this->r4->a);
            if ($a[0] != '') {
                $d = $a[0] . "." . $this->r4->x;
                $s = new Stg($d);
                $s->setXY($this->r4->x, $this->r4->y, $this->id);
            }
        }
        if ($this->rci->a != "" && $this->rci->a !== NULL) {
            $a = explode(":",$this->rci->a);
            if ($a[0] != '') {
                $d = $a[0] . "." . $this->rci->x;
                $s = new Stg($d);
                $s->setXY($this->rci->x, $this->rci->y, $this->id);
            }
        }
        if ($this->r3t->a != "" && $this->r3t->a !== NULL) {
            $a = explode(":",$this->r3t->a);
            $d = $a[0] . "." . $this->r3t->x;
            $s = new Stg($d);
            $s->setXY($this->r3t->x, $this->r3t->y, $this->id);
        }
        if ($this->r5->a != "" && $this->r5->a !== NULL) {
            $a = explode(":",$this->r5->a);
            $d = $a[0] . "." . $this->r5->x;
            $s = new Stg($d);
            $s->setXY($this->r5->x, $this->r5->y, $this->id);
        }
        if ($this->r6->a != "" && $this->r6->a !== NULL) {
            $a = explode(":",$this->r6->a);
            $d = $a[0] . "." . $this->r6->x;
            $s = new Stg($d);
            $s->setXY($this->r6->x, $this->r6->y, $this->id);
        }
        if ($this->r7->a != "" && $this->r7->a !== NULL) {
            $a = explode(":",$this->r7->a);
            $d = $a[0] . "." . $this->r7->x;
            $s = new Stg($d);
            $s->setXY($this->r7->x, $this->r7->y, $this->id);
        }

        $this->rslt = 'success';
        $this->reason = "PATH_SET_SUCCESS";
        
    }

    public function resetPath() {
        $this->log .= "\nresetPath():\n";
        if ($this->r1->a != "" && $this->r1->a !== NULL) {
            $this->log .= "\t" . $this->r1->a . "." . $this->r1->y;
            $a = explode(":",$this->r1->a);
            $d = $a[0] . "." . $this->r1->x;
            $s = new Stg($d);
            $s->resetXY($this->r1->x, $this->r1->y);
        }
        if ($this->r2->a != "" && $this->r2->a !== NULL) {
            $this->log .= "\t" . $this->r2->a . "." . $this->r2->y;
            $a = explode(":",$this->r2->a);
            $d = $a[0] . "." . $this->r2->x;
            $s = new Stg($d);
            $s->resetXY($this->r2->x, $this->r2->y);
        }
        if ($this->r3f->a != "" && $this->r3f->a !== NULL) {
            $this->log .= "\t" . $this->r3f->a . "." . $this->r3f->y;
            $a = explode(":",$this->r3f->a);
            $d = $a[0] . "." . $this->r3f->x;
            $s = new Stg($d);
            $s->resetXY($this->r3f->x, $this->r3f->y);
        }
        if ($this->rco->a != "" && $this->rco->a !== NULL) {
            $this->log .= "\t" . $this->rco->a . "." . $this->rco->y;
            $a = explode(":",$this->rco->a);
            if ($a[0] != '') {
                $d = $a[0] . "." . $this->rco->x;
                $s = new Stg($d);
                $s->resetXY($this->rco->x, $this->rco->y);
            }
        }
        if ($this->r4->a != "" && $this->r4->a !== NULL) {
            $this->log .= "\t" . $this->r4->a . "." . $this->r4->y;
            $a = explode(":",$this->r4->a);
            if ($a[0] != '') {
                $d = $a[0] . "." . $this->r4->x;
                $s = new Stg($d);
                $s->resetXY($this->r4->x, $this->r4->y);
            }
        }
        if ($this->rci->a != "" && $this->rci->a !== NULL) {
            $this->log .= "\t" . $this->rci->a . "." . $this->rci->y;
            $a = explode(":",$this->rci->a);
            if ($a[0] != '') {
                $d = $a[0] . "." . $this->rci->x;
                $s = new Stg($d);
                $s->resetXY($this->rci->x, $this->rci->y);
            }
        }
        if ($this->r3t->a != "" && $this->r3t->a !== NULL) {
            $this->log .= "\t" . $this->r3t->a . "." . $this->r3t->y;
            $a = explode(":",$this->r3t->a);
            $d = $a[0] . "." . $this->r3t->x;
            $s = new Stg($d);
            $s->resetXY($this->r3t->x, $this->r3t->y);
        }
        if ($this->r5->a != "" && $this->r5->a !== NULL) {
            $this->log .= "\t" . $this->r5->a . "." . $this->r5->y;
            $a = explode(":",$this->r5->a);
            $d = $a[0] . "." . $this->r5->x;
            $s = new Stg($d);
            $s->resetXY($this->r5->x, $this->r5->y);
        }
        if ($this->r6->a != "" && $this->r6->a !== NULL) {
            $this->log .= "\t" . $this->r6->a . "." . $this->r6->y;
            $a = explode(":",$this->r6->a);
            $d = $a[0] . "." . $this->r6->x;
            $s = new Stg($d);
            $s->resetXY($this->r6->x, $this->r6->y);
        }
        if ($this->r7->a != "" && $this->r7->a !== NULL) {
            $this->log .= "\t" . $this->r7->a . "." . $this->r7->y;
            $a = explode(":",$this->r7->a);
            $d = $a[0] . "." . $this->r7->x;
            $s = new Stg($d);
            $s->resetXY($this->r7->x, $this->r7->y);
        }

        $this->rslt = 'success';
        $this->reason = "PATH_RESET_SUCCESS";

        //$this->saveLog();
    }

//     public function saveLog() {
//         $debugFile = fopen("./createPathLog.txt", "w");
//         $logString = "\n--------------------------------\n";
//         $logString .= "\n" . date('Y-m-d H:i:s') . "\n"  . $this->log;
//         fwrite($debugFile,$logString);
//         fclose($debugFile);

//    }
   

    public function queryCrossNodesPaths() {
        global $db;

        $qry = "SELECT id, x, y, s4 FROM t_path WHERE s4<>'NULL'";
        
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt =  'fail';
            $this->reason = mysqli_error($db);
            return;
        }
        $this->rows = [];
        $this->rslt = 'success';
        return $res->num_rows;
    }

}

class PATHS {

    public $rslt;
    public $reason;
    public $rows = array();

    public function __construct() {
        $this->rslt = 'success';
        $this->reason = '';
        $this->rows = [];
    }

    public function queryPathByNode($node, $slot) {
    
        $qry = "SELECT  t_path.id, x, y, s1, s1y, s2, s2y, s3f, s3fy, sco, scoy, s4, s4y, sci, sciy, s3t, s3ty, s5, s5y, s6, s6y, s7, s7y,
                     t_cktcon.path, t_cktcon.ckid, t_cktcon.idx, t_cktcon.ctyp FROM t_path LEFT JOIN t_cktcon ON
                     t_path.id = t_cktcon.path WHERE (x LIKE '$node.X.$slot.%' OR y LIKE '$node.Y.$slot.%')";
    
        return $this->queryPath($qry);
    }
    

    public function queryPathsByCkid($ckid) {
    
        $qry = "SELECT  t_path.id, x, y, s1, s1y, s2, s2y, s3f, s3fy, sco, scoy, s4, s4y, sci, sciy, s3t, s3ty, s5, s5y, s6, s6y, s7, s7y,
                     t_cktcon.path, t_cktcon.ckid, t_cktcon.idx, t_cktcon.ctyp FROM t_path LEFT JOIN t_cktcon ON
                     t_path.id = t_cktcon.path WHERE t_cktcon.ckid LIKE '$ckid'";
    
        return $this->queryPath($qry);
    }
    
    public function queryPathById($id) {
    
        $qry = "SELECT  t_path.id, x, y, s1, s1y, s2, s2y, s3f, s3fy, sco, scoy, s4, s4y, sci, sciy, s3t, s3ty, s5, s5y, s6, s6y, s7, s7y,
                     t_cktcon.path, t_cktcon.ckid, t_cktcon.idx, t_cktcon.ctyp FROM t_path LEFT JOIN t_cktcon ON
                     t_path.id = t_cktcon.path WHERE t_path.id LIKE '$id'";
        
        return $this->queryPath($qry);
    }

    public function queryPath($qry) {
        global $db;

        $res = $db->query($qry);
        if (!$res) {
            $this->rslt =  'fail';
            $this->reason = mysqli_error($db);
            return;
        }

        $this->rows = [];
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $path='';
                if ($row['s1'] !== NULL)
                    $path .= $row['s1'] . '.' . $row['s1y'] . ' - ';

                if ($row['s2'] !== NULL)
                    $path .= $row['s2'] . '.' . $row['s2y'] . ' - ';

                if ($row['s3f'] !== NULL)
                    $path .= $row['s3f'] . '.' . $row['s3fy'] . ' - ';

                if ($row['sco'] !== NULL)
                    $path .= $row['sco'] . '.' . $row['scoy'] . ' - ';
                
                if ($row['s4'] !== NULL)
                    $path .= $row['s4'] . '.' . $row['s4y'] . ' - ';

                if ($row['sci'] !== NULL)
                    $path .= $row['sci'] . '.' . $row['sciy'] . ' - ';

                if ($row['s3t'] !== NULL)
                    $path .= $row['s3t'] . '.' . $row['s3ty'] . ' - ';
                
                if ($row['s5'] !== NULL)
                    $path .= $row['s5'] . '.' . $row['s5y'] . ' - ';

                if ($row['s6'] !== NULL)
                    $path .= $row['s6'] . '.' . $row['s6y'] . ' - ';

                if ($row['s7'] !== NULL)
                    $path .= $row['s7'] . '.' . $row['s7y'];


                $row['path'] = $path;
                $this->rows[] = $row;
            }
        }
            
        $this->rslt = 'success';
        $this->reason = "QUERY PATHS";
    }
}

?>