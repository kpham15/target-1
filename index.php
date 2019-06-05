


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

	include './js/dependencies.php';

	echo '<link id="headerlinks" rel="shortcut icon" href="#">';
	echo '</head>';

	echo '<body class="login-page skin-blue sidebar-mini sidebar-collapse">';	

	//IPCv2
	include './pages/nav-wrapper/nav-wrapper.php';
	include './pages/login.html';
	include './pages/modals.html';
	
	include './js/functions.php';
	include './js/variables.php';
	
	echo '</body>';
	echo '</html>';
	
?>

<script src="./js/ipcOpt.js"></script>
<script src="./js/hmac-sha256.js"></script>
<script src="./js/enc-base64-min.js"></script>

<script>

var folderSwList = <?php echo json_encode($folderList); ?>;

var ipcDispatch = "./em/ipcDispatch.php";


$(document).ready(function() {

	if (user.uname == '')
	{
		$('#login-page').show();
	}

});

</script>


