<?php

$act = "";
if (isset($_POST['act'])) {
    $act = $_POST['act'];
}

if ($act == "querySw") {
    $folderList = getSwInfo();
    echo json_encode($folderList);
    return;
}

if ($act == "queryVersion") {
    $ver = readme("readme.txt");
    echo json_encode($ver);
    return;    
}

if ($act == "queryReadMe") {
    $result = displayReadMe("readme.txt");
    echo json_encode($result);
    return;
}
/////////////----------FOR INDEX.PHP------------/////////


function getSwInfo() {
    $folderList = [
        'CURRENT'=> [
            'sw'=> 'CURRENT SW',
            'status'=>'',
            'dir'=> '',
            'ver'=>'',
            'date'=>''
        ], 
        'UPDATE'=> [
            'sw'=> 'UPDATE SW',
            'status'=>'',
            'dir'=> '',
            'ver'=>'',
            'date'=>''
        ], 
        'DEFAULT'=> [
            'sw'=>'DEFAULT SW',
            'status'=>'',
            'dir'=> '',
            'ver'=>'',
            'date'=>''
        ]
    ];

    $runningDir = basename(getcwd());
    foreach ($folderList as $key => $value) {
        if($key == $runningDir){
            $folderList[$key]['status'] = 'Running';
            $folderList[$key]['dir'] = '.';
            if(file_exists($folderList[$key]['dir'].'/readme.txt'))
                $folderList[$key]['ver'] = readme($folderList[$key]['dir'].'/readme.txt');
            // if(file_exists($folderList[$key]['dir'].'/index.php'))
            //     $folderList[$key]['date'] = date ("m/d/Y H:i:s", filemtime($folderList[$key]['dir'].'/index.php'));

            if(file_exists("../".$key)) {
                $folderInfor = stat("../".$key);
                $folderList[$key]['date'] = date ("m/d/Y H:i:s",$folderInfor['mtime']);
            }
        }
        else {
            $folderList[$key]['status'] = 'Stand By';
            $folderList[$key]['dir'] = '../'.$key;
            if(file_exists($folderList[$key]['dir'].'/readme.txt'))
                $folderList[$key]['ver'] = readme($folderList[$key]['dir'].'/readme.txt');
            // if(file_exists($folderList[$key]['dir'].'/index.php'))
            //     $folderList[$key]['date'] = date ("m/d/Y H:i:s", filemtime($folderList[$key]['dir'].'/index.php'));
            if(file_exists("../".$key)) {
                $folderInfor = stat("../".$key);
                $folderList[$key]['date'] = date ("m/d/Y H:i:s",$folderInfor['mtime']);
            }
        }
    }
    return $folderList;

}
function readme($dir) {
    $ver="";
    $file = fopen($dir, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            // process the line read.
            $lineExtract = explode(":", $line);
            if($lineExtract[0] == "ver") {
                $ver = str_replace(array("\r\n", "\r", "\n", "\t"),"",$lineExtract[1]);
                break;
            }
        }

        fclose($file);
    }
    return $ver;
}
function readCfg($dir) {
    $ver="";
    $file = fopen($dir, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            // process the line read.
            $lineExtract = explode(":", $line);
            if($lineExtract[0] == "RUNNING") 
                $ver = str_replace(array("\r\n", "\r", "\n", "\t"),"",$lineExtract[1]);
        }

        fclose($file);
    }
    return $ver;
}
function checkRunningFolder(){
    $runningDir = basename(__DIR__);
    $correctRunningDir = readCfg("../bhd.cfg");

    if($runningDir == $correctRunningDir)
        return true;
    else 
        return false;
}


function displayReadMe($dir){
    $result['ver'] = readme($dir);
    $file = fopen($dir, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            // process the line read.
            $result['descr'] .= $line;
        }

        fclose($file);
    }
    return $result;
}

?>