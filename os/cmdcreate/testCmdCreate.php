<?php
include 'ipcDbClass.php';
include 'ipcCmdClass.php';
include 'ipcRcClass.php';

$dbObj = new DB();
$db = $dbObj->con;

$cmdCreateObj = new CMD();
$cmdCreateObj->createCmd("open", 12345, " 1.1A.11:3.3 - 1.2A.13:1.1 - 1.3A.131:0.0 - 2.5A.31:1.1 - 2.6A.13:1.1 - 3.7A.11:3.3 ");



?>