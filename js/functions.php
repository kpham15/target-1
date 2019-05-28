<script>
	// =============== General Purpose Functions ============== //
	function loginSuccess() {
		$('body').attr('class','skin-blue sidebar-mini fixed');
		$('#login-page').hide();
		$('#nav-wrapper').show();

		getSystemInfo();
		startup();
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
				let res = data.rows[0];

				if (data.rslt == "fail") {
					alert(obj.reason);
				} 
				else {
					nodeInfo = res.node_info;
					delete res.node_info;
					wcInfo = res;
				}
			}
		});

		function startup() {
			systemInfoInterval = setInterval(getSystemInfo, 10000);
			updateUsername();
			updateNodeStatus();
			updateHeaderInfo();
			sysViewStartup();
		}
	}
</script>