<?php
/*

*/

class DB {
    public $host;
    public $dbname;
    public $ui;
    public $pw;
    public $con;
    public $rslt;
    public $reason;
    
    public function __construct() {
        $this->host = "localhost";
        // $this->dbname = "co5k"; $this->ui = "ninh"; $this->pw = "C0nsulta!!!";
        $this->dbname = "co5k"; $this->ui = "ninh"; $this->pw = "c0nsulta";
        // $this->dbname = "co5k"; $this->ui = "root"; $this->pw = "Qaz!2345";
        // $this->dbname = "wizzis5_co5k"; $this->ui = "wizzis5_co5kuser"; $this->pw = "co5kuser1";
        // $this->dbname = "vznfoa"; $this->ui = "ninh"; $this->pw = "c0nsulta";
        
        
        $this->con = mysqli_connect($this->host, $this->ui, $this->pw, $this->dbname);
        if (mysqli_connect_errno())
        {
            $this->rslt = "fail";
            $this->reason = mysqli_connect_error();
        }
        else {
            $this->rslt = "success";
            $this->reason = "DB Connection established";
        }
    }
}

?>