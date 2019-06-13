<?php

class TBUS {

    public $id = 0;
    public $node;
    public $tb;
    public $zport='';
    public $port = "";

    public $rows = [];
    public $rslt = "";
    public $reason = "";

    public function __construct() {

    }

    
    
    public function queryTBpath($node, $tb) {
        global $db;

        $qry = "SELECT * FROM t_tbus WHERE node='$node' and tb='$tb' ";
        $res = $db->query($qry);
        if(!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
            $this->rows = [];
        }
        else {
            $rows = [];
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $this->rows = $rows;
            $this->rslt = "success";
            $this->reason = "TBUS_QUERIED";
            
        }
    }

    public function addTBpath($node, $tb, $zport, $port) {
        global $db;

        $qry = "INSERT INTO t_tbus (node,tb,zport,port) VALUES($node,'$tb','$zport','$port')";
        $res = $db->query($qry);
        if(!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
        }
        else {
            $this->rslt = "success";
            $this->reason = "TBUS PATH ADDED";
            $this->id = $db->insert_id;
        }
    }

    public function deleteTBpath($id) {
        global $db;

        $qry = "DELETE FROM t_tbus WHERE id=$id";
        $res = $db->query($qry);
        if(!$res) {
            $this->rslt = 'fail';
            $this->reason = mysqli_error($db);
        }
        else {
            $this->rslt = "success";
            $this->reason = "TBUS PATH DELETED";
        }
    }
  

}


?>