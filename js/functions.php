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
				var obj = JSON.stringify(data);
				if (obj.rslt == "fail") {
					alert(obj.reason);
				} 
				else {
					console.log('success');
				}
			}
		});
	}
</script>