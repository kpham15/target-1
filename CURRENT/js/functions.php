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

	window.addEventListener('beforeunload', function(e) {
		logout('close');
	});

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
				} else if (action === 'close') {
					return;
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
		let helpBlock = '<span class="help-block">'+string.toUpperCase()+'</span>';
		if (selector.closest('form').hasClass('form-horizontal')) {
			selector.parent().append(helpBlock);
		} else {
			selector.closest('.form-group').append(helpBlock);
		}
		selector.closest('.form-group').addClass('has-error');
		return;
	}

	function postResponse(element, rslt, reason) {
		let html = 	'<div class="row post-response">' +
          				'<div class="col-md-12">' +
            				'<label style="text-align:left"></label>' +
          				'</div>' +
        				'</div>';
		
		$('.post-response').remove();
		element.append(html);

		let color = "";
		if (rslt.toUpperCase() == "FAIL") {
			color = 'response-fail';
		} else if (rslt.toUpperCase() == "SUCCESS") {
			color = 'response-success';
		}
		$('.post-response').addClass(color);
		$('.post-response label').text(`${rslt.toUpperCase()} - ${reason.toUpperCase()}`);
	}
	
	function inputSuccess(selector, string) {
		let helpBlock = '<span class="help-block">'+string.toUpperCase()+'</span>';
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
		$('.form-group').removeClass('has-success');
		$('.post-response').remove();
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
	
	// Used in all report pages to convert their data into csv format
	function downloadCSV(args) {
		var data, filename, link;

		var csv = convertArrayOfObjectsToCSV({
			data: provReportDataTable.data().toArray()
		});

		if (csv == null) {
			inputError($('#provReport_report_sel'),'No Report To Create');
			return;
		}

		filename = args.filename || 'export.csv';

		if (!csv.match(/^data:text\/csv/i)) {
			csv = 'data:text/csv;charset=utf-8,' + csv;
		}            
		data = encodeURI(csv);

		link = document.createElement('a');
		link.setAttribute('href', data);
		link.setAttribute('download', filename);
		link.click();
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
	
	function displayUserImg(fileName) {
		let randomNum = Math.floor(Math.random() * 100000);
        let url = "../PROFILE/"+fileName+"?"+randomNum ;
        let img = new Image();
        img.src = url;
        img.onload = function(){
            let ratio = img.width/img.height;
            let imgDropbox_height = 135;
            let imgDropbox_width = Math.floor(135 * ratio);
            $("#user_header_pic").css("width",imgDropbox_width+"px");
            $("#user_header_pic").css("height",imgDropbox_height+"px");
            $("#user_header_pic").attr("src",url);

            let imgHeader_height = 25;
            let imgHeader_width = Math.floor(25 * ratio);
            $("#dropdown_userPic").attr("width",imgHeader_width);
            $("#dropdown_userPic").attr("height",imgHeader_height);
            $("#dropdown_userPic").attr("src",url);
        } 
	}
  
</script>