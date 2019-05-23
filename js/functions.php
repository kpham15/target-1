<script>
	// =============== General Purpose Functions ============== //
	function loginSuccess() {
		$('body').attr('class','skin-blue sidebar-mini');
		$('#login-page').hide();
		$('#nav-wrapper').show();

		$('#top-nav-user-name, #profile-dropdown-user-name, #sidebar-user-name').text(user.fname + ' ' + user.lname);
		$('#profile-dropdown-user-group').text(user.ugrp);
	}
</script>