<?php
class BATCH {
    public $rslt;
    public $reason;
    public $rows;

    public function queryBatch($filename){
        global $db;

		$qry = "SELECT * FROM t_batch WHERE filename LIKE '%$filename%'";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }
    
        $rows = [];
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        $this->rslt = "success";
        $this->reason = "QUERY_SUCCESS";
        $this->rows = $rows;
        
    }

    public function queryBats($id){
        global $db;

		$qry = "SELECT * FROM t_bats WHERE batch_id = '$id'";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }
        $rows = [];
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        $this->rslt = "success";
        $this->reason = "QUERY_BAT_SUCCESS";
        $this->rows = $rows;
        
    }

    public function addBatch($user, $fileName, $fileContent) {
		global $db;

        $date = now();
     
        $qry = "INSERT INTO 
                t_batch 
                (user, filename, content, date) 
                VALUES 
                ('$user', '$fileName', '$fileContent', '$date')";

		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }

        $batch_id = $db->insert_id;
    
        //Break the text file into lines
        $commandArray = preg_split ('/$\R?^/m', $fileContent);
        for($i=0; $i < count($commandArray); $i++) {
            $cmd_id = $i+1;

            $qry = "INSERT INTO 
                    t_bats 
                    (batch_id, cmd_id, cmd) 
                    VALUES 
                    ('$batch_id', '$cmd_id', $commandArray[$i]')";
            
            // echo $qry.PHP_EOL;
            $res = $db->query($qry);
            if (!$res) {
                $this->rslt = "fail";
                $this->reason = mysqli_error($db);
                return;
            }
        }
        $this->queryBatch('');
        $this->rslt = "success";
        $this->reason =  "Add Batch Successful";
        
    }

    public function deleteBatch($id) {
		global $db;

		$qry = "DELETE FROM t_batch WHERE id = '$id'";
		$res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }
        $qry = "DELETE FROM t_bats WHERE batch_id = '$id'";
        $res = $db->query($qry);
        if (!$res) {
            $this->rslt = "fail";
            $this->reason = mysqli_error($db);
            return;
        }
        $this->queryBatch('');
        $this->rslt = "success";
        $this->reason = "Batch deleted successfully";
        
    }
}



?>