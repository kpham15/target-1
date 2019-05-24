<script>
	// =============== General Purpose Functions ============== //
	function loginSuccess() {
		$('body').attr('class','skin-blue sidebar-mini fixed');
		$('#login-page').hide();
		$('#nav-wrapper').show();

		updateUsername();
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
			dataType: 'json',
			success: function(data, status) {
				var obj = JSON.parse(data);
				console.log(obj);
				if (obj.rslt == "fail") {
					alert(obj.reason);
				} 
				else {
					console.log('obj.rslt');
					console.log(obj.rslt);
					let nodeInfo = obj.rows[0].node_info;
					console.log('nodeinfo');
					console.log(nodeInfo);
					// updateNodeStatus(nodeInfo);
				}
			}
		});
	}
</script>