<?php
include '../em/ipcClasses.php';
include 'ipcCpsClass2.php';
include 'ipCmdClass.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

error_reporting(E_ALL);

////////////////////////////////////////////
try {

createDB:

    $dbObj = new Db();
    if ($dbObj->rslt == "fail") {
        echo $dbObj->rslt.": ".$dbObj->reason;
        goto createDB;
    }
    $db = $dbObj->con;

    ////////-------------------------------------
    //--------Get ip address of live CPS
    $address = gethostbyname('8fde09f396e4.sn.mynetname.net');
    //this OBJ is to query, update t_cmdque
    $cmdObj = new CMD();

start: 

    //create CPS OBJ and connect socket to device
    // $cpsObj = new CPS($argv[0], $argv[1]);
    $cpsObj = new CPS($address, 8000);
    if($cpsObj->rslt == 'fail') {
        echo $cpsObj->rslt.": ".$cpsObj->reason;
        goto start;
    }

    //------------get cmd from database
    getCmd:

        $curTime = time();
        //--------------Go to database to get cmdList
        // $cmdObj = new CMD();
        $cmdList = [];
        $cmdObj->getCmdList();
        if($cmdObj->rslt == 'fail') {
            echo $cmdObj->rslt.": ".$cmdObj->reason;
            goto getCmd;
        }

        /////////////////////////////////////////////
        for($i = 0; $i<count($cmdList); $i++) {
            if((time() - $curTime) < 5) {
                echo "\nsendCommand $i";
                $cpsObj->sendCommand($cmdList[$i]['ackid'], $cmdList[$i]['cmd']);
                if($cpsObj->rslt == 'fail') {
                    $cmdObj->updCmd($cmdList[$i]['ackid'], 'COMPL',$cpsObj->reason);
                }
                else {
                    $cmdObj->updCmd($cmdList[$i]['ackid'], 'COMPL',$cpsObj->read);
                }
            }
            else {
                goto getCmd;
            }
        }

        goto getCmd;
}
catch (Throwable $t)
{   
    echo $t->getMessage();
    if(strpos($t->getMessage(), 'unable to connect') !== FALSE) {

        $cpsObj->endConnection();
        goto start;
    }

    
}
    




?>