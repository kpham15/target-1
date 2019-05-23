
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
	echo '<meta charset="utf-8">';
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
	echo '<title>Intelligent Provisioning Center</title>';
	echo '<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">';

	// Stylesheets
	echo '<link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">';
	echo '<link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">';
	echo '<link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">';
	echo '<link rel="stylesheet" href="../dist/css/AdminLte.min.css">';
	echo '<link rel="stylesheet" href="../plugins/iCheck/square/blue.css">';
	echo '<link rel="stylesheet" href="../dist/css/fonts.css">';

	echo '<link id="headerlinks" rel="shortcut icon" href="#">';
	echo '</head>';


	
	echo '<body>';	
	echo '<label style="display:none" id="main_currentUser"></label>';

	// For production
	// include './html/ipcLogin.html';
	// include './html/ipcMainBegin.html';
	// include './html/ipcBulletinBoard.html';
	// include './html/ipcFac.html';
	// include './html/ipcPortmap.html';
	// include './html/ipcTestAccess.html';
	// include './html/ipcSvc.html';
	// include './html/ipcMaint.html';
	// include './html/ipcAlm.html';
	// include './html/ipcUser.html';
	// include './html/ipcRef.html';
	// include './html/ipcSearchAdmin.html';
	// include './html/ipcNodeAdmin.html';
	// include './html/ipcNodeOperations.html';
	// include './html/ipcMxc.html';
	// include './html/ipcBrdcst.html';
	// include './html/ipcBatchExc.html';
	// include './html/ipcOrd.html';
	// include './html/ipcSysView.html';
	// include './html/ipcWc.html';
	// include './html/ipcBkup.html';
	// include './html/ipcPath.html';
	// include './html/ipcFtRelease.html';
	// include './html/ipcFtModTable.html';
	// include './html/ipcFtOrders.html';
	// include './html/ipcAlmReport.html';
	// include './html/ipcConfigReport.html';
	// include './html/ipcEventReport.html';
	// include './html/ipcMaintReport.html';
	// include './html/ipcProvReport.html';
	// include './html/ipcSwUpdate.html';
	// include './html/ipcMainEnd.html';
	

	echo '</body>';
	echo '</html>';	
?>

 <script src="./js/ipcOpt.js"></script>
 <script src="./js/hmac-sha256.js"></script>
 <script src="./js/enc-base64-min.js"></script>

 <script src="../bower_compoennts/jquery/dist/jquery.min.js"></script>
 <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
 <script src="../plugins/iCheck/icheck.min.js"></script>

<script>

var folderSwList = <?php echo json_encode($folderList); ?>;

//For Production
//var logoimg = '<img src="./resources/telepath_logo.jpg" style="float:left" height="30px" width="180px">';               
// var logoimg = '<img src="./resources/Telepath_Logo_Part.JPG" style="float:left" height="40px" width="40px">';               

// var chassisimg = '<img src="./resources/co500-chassis.jpg" align="center" style="width:100%;">';


//For Workspace
// var logoimg = '<img src="../../resources/Telepath_Logo_Part.JPG" style="float:left" height="42" width="42">';               
// var chassisimg = '<img src="../../resources/co500-chassis.jpg" align="center" style="width:100%;">';

// $('#logo').before(logoimg);
// $('#login_logo').before(logoimg);
// $('#MainBegin_logo').before(logoimg);
// $('#chassis').after(chassisimg);

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

var ipcDispatch = "./em/ipcDispatch.php";


$(document).ready(function() {

if ($("#main_currentUser").text() == '')
{
	$('body').addClass('hold-transition login-page');
	$('body').load('pages/login.html');
	// $("#mainPage").hide();
	// $("#login").show();
	// login.start();
}
else
{
	$("#login").hide();
	// $("#mainPage").show();
	mB.start();
}

});

</script>


