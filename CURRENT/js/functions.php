<script>
	// =============== General Purpose Functions ============== //
	function startup() {
		systemInfoInterval = setInterval(getSystemInfo, 10000);
		updateUsername();
		getSwVersion();
		updateNodeStatus();
		updateHeaderInfo();
		sysviewStartup();
	}

	function getSwVersion() {
		$.ajax({
			type: 'POST',
			url: './indexFunc.php',
			data: {
				act: 'queryReadMe'
			},
			dataType: 'json'
		}).done(function(data) {
			$('#sidebar-user-name').text(data.ver)

			swVer.version = data.ver;
			swVer.description = data.descr;
		});
	}

	function logout(action) {
		clearInterval(systemInfoInterval);
		$.post(ipcDispatch,
		{
			api:		'ipcLogout',
			user:		user.uname
		},
		function (data, status) {
			var obj = JSON.parse(data);
			let modal = {
				title: obj.rslt,
				body: obj.reason
			}

			if (obj.rslt === 'fail') {
				modal.type = 'danger';
				modalHandler(modal);
			} else {
				if (action === 'manual logout') {
					$('#logout-modal .modal-body').text('You have logged out.');
					$('#logout-modal').modal('show');
				} else {
					$('#logout-modal .modal-body').text('Your session has timed out, please log in again!');
					$('#logout-modal').modal('show');
				}
			}
		});
	}

	function loginSuccess() {
		$('#login-page').hide();
		$('#nav-wrapper').show();
		
		getSystemInfo();
	}

	function cancelTimeout() {
		$.ajax({
			type: 'POST',
			url: ipcDispatch,
			data: {
				api: 'ipcLogin',
				act: 'continue',
				user: user.uname
			},
			dataType: 'json'
		}).done(function(data) {
			if (data.rslt === 'fail') {
				alert(data.reason);
			}
		});
	}

	function checkUserTimeout(data) {
		let loginTime = new Date(data.loginTime).getTime() / 1000;
		let time = new Date(data.time).getTime() / 1000;
		let idle_to = user.idle_to * 60;

		if ((time - loginTime) > idle_to) {
			$('#cancel-timeout-modal').modal('hide');
			logout();
			return;
		}

		if (((time - loginTime) > (idle_to - 60)) && ((time - loginTime) < idle_to)) {
			$('#cancel-timeout-modal .modal-body').html('Your session will be timed out in ' + (idle_to - (time - loginTime)) + ' seconds.<br/><br/>Close this window to cancel the time out.');
			$('#cancel-timeout-modal').modal('show');
		}
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

				updateNodeStatus();
				updateHeaderInfo();

				//update mxc tab and ports only when sysview page is active
				if($("#system-view-page").hasClass("active-page"))
					updateMxcInfo();
				
				// Check if user is timed out
				checkUserTimeout(res);
			}

			// check if first time loading information
			if (firstload) {
				startup();
				firstload = false;
			}



		});
	}

	function inputError(selector, string) {
		let helpBlock = '<span class="help-block">'+string+'</span>';
		if (selector.closest('form').hasClass('form-horizontal')) {
			selector.parent().append(helpBlock);
		} else {
			selector.closest('.form-group').append(helpBlock);
		}
		selector.closest('.form-group').addClass('has-error');
		return;
	}

	function postResponse(element, rslt, reason) {
		$('.post-response').text("");
		let color = "";
		if (rslt == "fail") {
			color = 'red';
		} else if (rslt == "success") {
			color = 'green';
		}
		element.css('color', color).text(`${rslt} - ${reason}`);
	}
	
	function inputSuccess(selector, string) {
		let helpBlock = '<span class="help-block">'+string+'</span>';
		if (selector.closest('form').hasClass('form-horizontal')) {
			selector.parent().append(helpBlock);
		} else {
			selector.closest('.form-group').append(helpBlock);
		}
		selector.closest('.form-group').addClass('has-success');
		return;
	}

	function clearErrors() {
    $('span.help-block').remove();
		$('.form-group').removeClass('has-error');
		$('.post-response').text("");
	}
	

	// Used in all report pages to convert their data into csv format
	function convertArrayOfObjectsToCSV(args) {
		var result, ctr, keys, columnDelimiter, lineDelimiter, data;

        data = args.data || null;
        if (data == null || !data.length) {
            return null;
        }

        columnDelimiter = args.columnDelimiter || ',';
        lineDelimiter = args.lineDelimiter || '\n';

        keys = Object.keys(data[0]);

        result = '';
        result += keys.join(columnDelimiter);
        result += lineDelimiter;

        data.forEach(function(item) {
            ctr = 0;
            keys.forEach(function(key) {
                if (ctr > 0) result += columnDelimiter;

                result += item[key];
                ctr++;
            });
            result += lineDelimiter;
        });

        return result;
	}


	// ================ Encode Password ================= //
	function encode(data) {
		var header = {
			"alg": "HS256",
			"typ": "JWT"
		};
	
		var stringifiedHeader = CryptoJS.enc.Utf8.parse(JSON.stringify(header));
		var encodedHeader = base64url(stringifiedHeader);
	
		var stringifiedData = CryptoJS.enc.Utf8.parse(JSON.stringify(data));
		var encodedData = base64url(stringifiedData);
	
		var signature = encodedHeader + "." + encodedData;
		signature = CryptoJS.HmacSHA256(signature, keyId);
		signature = base64url(signature);
		return encodedHeader + "." + encodedData + "." + signature;
    }
  
    function base64url(source) {
		// Encode in classical base64
		encodedSource = CryptoJS.enc.Base64.stringify(source);
	
		// Remove padding equal characters
		encodedSource = encodedSource.replace(/=+$/, '');
		
		// Replace characters according to base64url specifications
		encodedSource = encodedSource.replace(/\+/g, '-');
		encodedSource = encodedSource.replace(/\//g, '_');
	
		return encodedSource;
    }
  
</script>