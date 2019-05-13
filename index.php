
<?php
	include 'indexFunc.php';
	/// Read the config file to get the name of running folder
	// if(basename(getcwd()) != readCfg("../bhd.cfg")) {
	// 	echo '<h1>Permission Denied</h1>';
	// 	return;
	// }
	// $folderList = getSwInfo();


	///////////////////---------------------------//////////////////////
	
	echo '<!DOCTYPE html>';
	echo '<html lang="en">';
	echo '<head>';
	echo '<title>VZN-FOA</title>';
	echo '<meta charset="utf-8">';
		
	echo '<meta name="viewport" content="width=device-width">';

	// For production
	echo '<link rel="stylesheet" href="./css/bootstrap.min.css">';
	echo '<link rel="stylesheet" type="text/css" href="./css/co-ipc.css">';
	echo '<script src="./js/jquery.min.js"></script>';
	echo '<script src="./js/bootstrap.min.js"></script>';
		
	// For workspace
	// echo '<link rel="stylesheet" href="../../css/bootstrap.min.css">';
	// echo '<link rel="stylesheet" type="text/css" href="../../css/co-ipc.css">';
	// echo '<script src="../../js/jquery.min.js"></script>';
	// echo '<script src="../../js/bootstrap.min.js"></script>';


	echo '<link id="headerlinks" rel="shortcut icon" href="#">';
	echo '</head>';
	echo '<body>';	

	// For production
	include './html/ipcLogin.html';
	include './html/ipcMainBegin.html';
	include './html/ipcBulletinBoard.html';
	include './html/ipcFac.html';
	include './html/ipcPortmap.html';
	include './html/ipcTestAccess.html';
	include './html/ipcSvc.html';
	include './html/ipcMaint.html';
	include './html/ipcAlm.html';
	include './html/ipcUser.html';
	include './html/ipcRef.html';
	include './html/ipcNodeRef.html';
	include './html/ipcSearchAdmin.html';
	include './html/ipcNodeAdmin.html';
	include './html/ipcMxc.html';
	include './html/ipcEvtLog.html';
	include './html/ipcBrdcst.html';
	include './html/ipcBatchExc.html';
	include './html/ipcOrd.html';
	include './html/ipcSysAdmin.html';
	include './html/ipcSysView.html';
	include './html/ipcWc.html';
	include './html/ipcBkup.html';
	include './html/ipcNodeView.html';
	include './html/ipcPath.html';
	include './html/ipcTestPortBus.html';
	include './html/ipcFtRelease.html';
	include './html/ipcFtModTable.html';
	include './html/ipcFtOrders.html';
	include './html/ipcOverview.html';
	include './html/ipcReports.html';
	include './html/ipcAlmReport.html';
	include './html/ipcConfigReport.html';
	include './html/ipcEventReport.html';
	include './html/ipcMaintReport.html';
	include './html/ipcProvReport.html';
	include './html/ipcSwUpdate.html';
	include './html/ipcMainEnd.html';
	

	// For workspace
	// include '../../html/ipcLogin.html';
	// include '../../html/ipcMainBegin.html';
	// include '../../html/ipcBulletinBoard.html';
	// include '../../html/ipcFac.html';
	// include '../../html/ipcPortmap.html';
	// include '../../html/ipcSvc.html';
	// include '../../html/ipcMaint.html';
	// include '../../html/ipcAlm.html';
	// include '../../html/ipcUser.html';
	// include '../../html/ipcSearchAdmin.html';
	// include '../../html/ipcRef.html';
	// include '../../html/ipcNodeAdmin.html';
	// include '../../html/ipcMxc.html';
	// include '../../html/ipcEvtLog.html';
	// include '../../html/ipcBrdcst.html';
	// include '../../html/ipcBatchExc.html';
	// include '../../html/ipcOrd.html';
	// include '../../html/ipcSysAdmin.html';
	// include '../../html/ipcSysView.html';
	// include '../../html/ipcNodeRef.html';
	// include '../../html/ipcWc.html';
	// include '../../html/ipcBkup.html';
	// include '../../html/ipcNodeView.html';
	// include '../../html/ipcPath.html';
	// include '../../html/ipcFtRelease.html';
	// include '../../html/ipcFtModTable.html';
	// include '../../html/ipcFtOrders.html';
	// include '../../html/ipcOverview.html';
	// include '../../html/ipcReports.html';
	// include '../../html/ipcAlmReport.html';
	// include '../../html/ipcConfigReport.html';
	// include '../../html/ipcMaintReport.html';
	// include '../../html/ipcProvReport.html';
	// include '../../html/ipcMainEnd.html';

	echo '</body>';
	echo '</html>';	
?>

<!-- For production -->
 <script src="./js/ipcOpt.js"></script>
 <script src="./js/hmac-sha256.js"></script>
 <script src="./js/enc-base64-min.js"></script>

<!-- For workspace -->
<!-- <script src="../../js/ipcOpt.js"></script> -->

<script>

var folderSwList = <?php echo json_encode($folderList); ?>;

//For Production
//var logoimg = '<img src="./resources/telepath_logo.jpg" style="float:left" height="30px" width="180px">';               
var logoimg = '<img src="./resources/Telepath_Logo_Part.JPG" style="float:left" height="40px" width="40px">';               

var chassisimg = '<img src="./resources/co500-chassis.jpg" align="center" style="width:100%;">';


//For Workspace
// var logoimg = '<img src="../../resources/Telepath_Logo_Part.JPG" style="float:left" height="42" width="42">';               
// var chassisimg = '<img src="../../resources/co500-chassis.jpg" align="center" style="width:100%;">';

$('#logo').before(logoimg);
$('#login_logo').before(logoimg);
$('#MainBegin_logo').before(logoimg);
$('#chassis').after(chassisimg);

// $(document).ready(function() {
// 	wc.wcAlmStat();
// 	wc.wcTime();
// 	wc.wcHeaderInfo();
	
// 	if ($("#main_currentUser").text() == '')
// 	{
// 		//$("#warning").show();
// 		$("#login").show();
// 		$("#mainPage").hide();

// 	}
// 	else
// 	{
// 		//$("#warning").hide();
// 		$("#login").hide();
// 		$("#mainPage").show();
// 	}

// });



// For production
var ipcDispatch = "./em/ipcDispatch.php";
var ipcSwInfo = "./indexFunc.php";
var ipcSwUpd = "./em/ipcSwUpdate.php";

var ipcAlm = "./em/ipcAlm.php";
var ipcAlmReport = "./em/ipcAlmReport.php";
var ipcBatchExc = "./em/ipcBatchExc.php";
var ipcBroadcast = "./em/ipcBroadcast.php";
var ipcCfgReport = "./em/ipcCfgReport.php";
var ipcEvtlog = "./em/ipcEvtlog.php";
var ipcFacilities = "./em/ipcFacilities.php";
var ipcFindOrder = "./em/ipcFindOrder.php";
var ipcFtOrd = "./em/ipcFtOrd.php";
var ipcFtRelease	= "./em/ipcFtRelease.php";
var ipcFtModTable	= "./em/ipcFtModTable.php";

var ipcLogin = "./em/ipcLogin.php";
var ipcLogout = "./em/ipcLogout.php";
var ipcMaintConnect = "./em/ipcMaintConnect.php";
var ipcMaintDiscon = "./em/ipcMaintDiscon.php";
var ipcMaintReport = "./em/ipcMaintReport.php";
var ipcMaintRestoreMtcd = "./em/ipcMaintRestoreMtcd.php";
var ipcMaintRestore = "./em/ipcMaintRestore.php";
var ipcMxc = "./em/ipcMxc.php";
var ipcNode = "./em/ipcNode.php";
var ipcOpt = "./em/ipcOpt.php";
var ipcPath = "./em/ipcPath.php";
var ipcPortmap = "./em/ipcPortmap.php";
var ipcProv = "./em/ipcProv.php";
var ipcProvConnect = "./em/ipcProvConnect.php";
var ipcProvDisconnect = "./em/ipcProvDisconnect.php";
var ipcProvReport = "./em/ipcProvReport.php";
var ipcRef = "./em/ipcRef.php";
var ipcSearch = "./em/ipcSearch.php";
var ipcUser = "./em/ipcUser.php";
var ipcWc = "./em/ipcWc.php";

// For workspace
// var ipcAlm = "../../em/ipcAlm.php";
// var ipcAlmReport = "../../em/ipcAlmReport.php";
// var ipcBatchExc = "../../em/ipcBatchExc.php";
// var ipcBroadcast = "../../em/ipcBroadcast.php";
// var ipcCfgReport = "../../em/ipcCfgReport.php";
// var ipcEvtlog = "../../em/ipcEvtlog.php";
// var ipcFacilities = "../../em/ipcFacilities.php";
// var ipcFindOrder = "../../em/ipcFindOrder.php";
// var ipcFtOrd = "../../em/ipcFtOrd.php";
// var ipcLogin = "../../em/ipcLogin.php";
// var ipcLogout = "../../em/ipcLogout.php";
// var ipcMaintConnect = "../../em/ipcMaintConnect.php";
// var ipcMaintDiscon = "../../em/ipcMaintDiscon.php";
// var ipcMaintReport = "../../em/ipcMaintReport.php";
// var ipcMaintRestoreMtcd = "../../em/ipcMaintRestoreMtcd.php";
// var ipcMxc = "../../em/ipcMxc.php";
// var ipcNode = "../../em/ipcNode.php";
// var ipcOpt = "../../em/ipcOpt.php";
// var ipcPath = "../../em/ipcPath.php";
// var ipcPortmap = "../../em/ipcPortmap.php";
// var ipcProv = "../../em/ipcProv.php";
// var ipcProvConnect = "../../em/ipcProvConnect.php";
// var ipcProvDisconnect = "../../em/ipcProvDisconnect.php";
// var ipcProvReport = "../../em/ipcProvReport.php";
// var ipcRef = "../../em/ipcRef.php";
// var ipcSearch = "../../em/ipcSearch.php";
// var ipcUser = "../../em/ipcUser.php";
// var ipcWc = "../../em/ipcWc.php";


$(document).ready(function() {

if ($("#main_currentUser").text() == '')
{
	$("#mainPage").hide();
	// $("#login").show();
	login.start();
}
else
{
	$("#login").hide();
	// $("#mainPage").show();
	mB.start();
}

});

</script>


