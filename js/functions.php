<script>
	// =============== General Purpose Functions ============== //
	function startup() {
		systemInfoInterval = setInterval(getSystemInfo, 10000);
		updateUsername();
		updateNodeStatus();
		updateHeaderInfo();
		sysviewStartup();
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

				updateNodeStatus();
				updateHeaderInfo();				
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

	function clearErrors() {
    $('span.help-block').remove();
    $('.form-group').removeClass('has-error');
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
</script>