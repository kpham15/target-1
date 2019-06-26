<?php

class Rel {
    public $a;
    public $x;
    public $y;

    public function __construct() {
        $this->a = "";
        $this->x = -1;
        $this->y = -1;
    }

    public function set() {
        global $db;
        if ($this->a == "" || $this->x == -1 || $this->y == -1)
            return false;
        $e = explode(".", $this->a);
        $qry = "update t_stg set x='$this->x' where u='$e[0]' and s='$e[1]' and n='$e[2]' and y='$this->y'";
        $res = 	$db->query($qry);
        return true;
    }

    public function reset() {
        global $db;
        if ($this->a == "" || $this-x == -1 || $this->y == -1)
            return false;
        $e = explode(".", $this->a);
        $qry = "update t_stg set x=-1 where u='$e[0]' and s='$e[1]' and n='$e[2]' and y='$this->y' and x='$this->x'";
        $res = 	$db->query($qry);
        return true;
    }

}

?>