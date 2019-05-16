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

        // $url = "";
        // $file = fopen("ipc-db.cfg", "r");
        // if ($file) {
        //     while (($line = fgets($file)) !== false) {
        //         // process the line read.
        //         $lineExtract = explode(",", $line);
        //         if ($lineExtract[0] == "localhost") {
        //             $url = $lineExtract[0];
        //             break;
        //         }
        //     }

        // }
            // fclose($file);

        // echo "URL = $url";
        $file = __DIR__ . "/ipc-db.cfg";
        $dbString = file_get_contents($file);
        // echo "DBSTRING = $dbString";
        // print_r('dbstring : ' . $dbString);
        // var_dump($dbString);
        $parseDbString = explode(",", $dbString);
        $dbHost = $parseDbString[0];
        $dbName = $parseDbString[1];
        $dbUser = $parseDbString[2];
        $dbPw = $parseDbString[3];
        // var_dump($dbPw);

        // $this->host = "localhost";
        $this->host = $dbHost;
        // $this->dbname = "co5k"; $this->ui = "ninh"; $this->pw = "C0nsulta!!!";
        // $this->dbname = "co5k"; $this->ui = "ninh"; $this->pw = "c0nsulta";
        // $this->dbname = "co5k"; $this->ui = "root"; $this->pw = "Qaz!2345";
        // $this->dbname = "wizzis5_co5k"; $this->ui = "wizzis5_co5kuser"; $this->pw = "co5kuser1";
        // $this->dbname = "vznfoa"; $this->ui = "ninh"; $this->pw = "c0nsulta";
        $this->dbname = $dbName; $this->ui = $dbUser; $this->pw = $dbPw;
        
        
        $this->con = mysqli_connect($this->host, $this->ui, $this->pw, $this->dbname);
        if (mysqli_connect_errno())
        {
            $this->rslt = FAIL;
            $this->reason = mysqli_connect_error();
        }
        else {
            $this->rslt = SUCCESS;
            $this->reason = "DB Connection established";
        }
    }
}

?>