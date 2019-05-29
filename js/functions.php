<script>
	// =============== General Purpose Functions ============== //
	function startup() {
		systemInfoInterval = setInterval(getSystemInfo, 10000);
		updateUsername();
		updateNodeStatus();
		updateHeaderInfo();
		sysviewStartup();
	}

	function loginSuccess() {
		$('body').attr('class','skin-blue sidebar-mini sidebar-collapse fixed');
		$('#login-page').hide();
		$('#nav-wrapper').show();

		getSystemInfo();
	}

	function getSystemInfo() {
		$.ajax({
			type: 'POST',
			url: ipcDispatch,
			data: {
				"api":        "ipcWc",
				"act":        "getHeader",
				"user":       "SYSTEM",
				"uname":			user.uname
			},
			dataType: 'json'
		}).done(function(data) {
			let res = data.rows[0];
			let modal = {};

			if (data.rslt == "fail") {
				modal.title = data.rslt;
				modal.body = data.reason;
				modal.type = 'danger';
				modalHandler(modal);
			} 
			else {
				nodeInfo = res.node_info;
				delete res.node_info;
				wcInfo = res;
			}

			// check if first time loading information
			if (firstload) {
				startup();
				firstload = false;
			}
		});
	}
</script>