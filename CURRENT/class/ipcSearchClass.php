<?php
class SEARCH {
    public $item ="";
    public $descr = "";

    public $rslt = "";
    public $reason = "";
    public $rows = [];


    public function __construct() {
        global $db;

        $this->rslt = "success";
        $this->reason = "";
        $this->rows = [];
        return; 
    }

    public function searchItem($item) {
        global $db;

        // $item = strtoupper($item);
        $qry = "SELECT * FROM t_parms WHERE item LIKE '$item%'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            $this->rows = [];
        }
        else {
            $rows = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rows = $rows;
            }
            $this->rslt = "success";
            $this->reason = "QUERY_SUCCESS";
        }
    }

    public function addSearchItem($item,$descr) {
        global $db;

        $qry = "INSERT INTO t_parms (item, descr) values ('$item', '$descr')";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            $this->rows = [];
        }
        else {
            $this->rslt = "success";
            $this->reason = "Search Item and Description is added";
            $this->rows = [];
        }
    }

    public function updateSearchItem($item,$descr) {
        global $db;

        $qry = "UPDATE t_parms SET descr = '$descr' WHERE item = '$item'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            $this->rows = [];
        }
        else {
            $this->rslt = "success";
            $this->reason = "Search Item's Description is updated";
            $this->rows = [];
        }
    }

    public function deleteSearchItem($item) {
        global $db;

        $qry = "DELETE FROM t_parms WHERE item = '$item'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            $this->rows = [];
        }
        else {
            $this->rslt = "success";
            $this->reason = "Search Item has been deleted";
            $this->rows = [];
        }
    }

}


?>
