    <?php

    class DEBUG{
        public $enable = '0';
        public $debugFile = null;
        public $rslt;
        public $reason;

        public function __construct($mode=NULL){

            $str = file_get_contents("../../ipc-debug.cfg");
            if ($str == '0' || $str == '1') {
                $this->enable = $str;
            }
        
            if ($this->enable == '1') {
                $this->debugFile =  fopen("../report/debug.log", "a");
            }
            
            $this->rslt = 'success';
            $this->reason = "DEBUG CONSTRUCTED";

        }

        public function close() {
            if ($this->enable == 0) {
                return;
            }
            fclose($this->debugFile);
        }

        public function log($string) {
            if ($this->enable == 0) {
                return;
            }

            fwrite($this->debugFile, date("Y-m-d H:i:s") . "\n \n" . $string . "\n-----------------------\n");

            // sample string for debugging classes
            // $debugObj->log(__FILE__ . "->" . __FUNCTION__ . " : \n \$qry = " . $qry . "\n reason = " . $this->reason);

            // sample string for debugging API
            // Will need to make global variable $debugObj inside whichever function that is being debugged. 
            // $debugObj->log(__FILE__ . "->" . __FUNCTION__ . " : \n \ **INPUT STRING HERE**);
        }

        

    }





    ?>