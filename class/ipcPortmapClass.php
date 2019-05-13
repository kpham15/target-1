<?php
/*
    Filename: ipcPortmapClass.php

*/

    class PORTMAP {
        public $id;
        public $facObj;
        public $portObj;

        public $rows;
        public $rslt;
        public $reason;
        
        public function __construct() {
            $this->id = 0;
            $this->facObj = NULL;
            $this->portObj = NULL;

            $this->rows = [];
            $this->rslt = "";
            $this->reason = "";
        }

        public static function setByPortID($portId) {
            $obj = new self();
            $obj->loadByPortId($portId);
            return $obj;
        }

        public static function setByFacNum($fac) {
            $obj = new self();
            $obj->loadByFacNum($fac);
            return $obj;
        }

        private function loadByPortId($portId) {
            $portObj = new Port($portId);
            if($portObj->rslt == FAIL) {
                $this->rslt = FAIL;
                $this->reason = $portObj->reason;
                return;
            }
            $facObj= FAC::setByID($portObj->fac_id);
            if($facObj->rslt == FAIL) {
                $this->rslt = FAIL;
                $this->reason = $facObj->reason;
                return;
            }
            $this->portObj = $portObj;
            $this->facObj = $facObj;
            $this->rslt = SUCCESS;

        }

        private function loadByFacNum($fac) {
            $facObj = FAC::setByFac($fac);
            if($facObj->rslt == FAIL) {
                $this->rslt = FAIL;
                $this->reason = $facObj->reason;
                return;
            }
            $portObj = new Port($facObj->port_id);
            if($portObj->rslt == FAIL) {
                $this->rslt = FAIL;
                $this->reason = $portObj->reason;
                return;
            }
            $this->portObj = $portObj;
            $this->facObj = $facObj;
            $this->rslt = SUCCESS;
        }
    }

?>
