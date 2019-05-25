<script>
	// =============== General Purpose Functions ============== //
	function loginSuccess() {
		$('body').attr('class','skin-blue sidebar-mini fixed');
		$('#login-page').hide();
		$('#nav-wrapper').show();

		updateUsername();
		getSystemInfo();
		systemInfoInterval = setInterval(getSystemInfo, 10000);
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
			dataType: 'json',
			success: function(data, status) {
				if (data.rslt == "fail") {
					alert(obj.reason);
				} 
				else {
					nodeInfo = data.rows[0].node_info;
					updateNodeStatus();
					updateNodeTables();
					updateHeaderInfo(data.rows[0]);

					firstLoad = false;
				}
			}
		});
	}
</script>