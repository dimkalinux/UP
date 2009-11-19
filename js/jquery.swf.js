"use strict";

if (typeof UP === "undefined" || !UP) {
	var UP = {};
}



// class for upload process
UP.uploadFlash = function () {

	var uploader,
		startTime = 0,
		lastProgressTime = 0,
		lastActivity = 0,
		active = false,
		movingAverageHistory = [];


	function confirmExit() {
		if (active === true) {
			return UP.env.uploadWarnMsg;
		}
	}


	// When contentReady event is fired, you can call methods on the uploader.
	function onContentReady () {
		uploader.enable();
		uploader.setAllowLogging(false);
		uploader.setAllowMultipleFiles(false);
	}


	function handleCancel () {
		uploader.cancel();
		uploader.enable();
		$('#upload_progress,#upload_status').hide();
		$(document).stopTime('checkActivity');
		UP.statusMsg.show('Закачка отменена', UP.env.msgInfo, true);
		active = false;
		return false;
	}


	function handleUpload (id) {
		var uploadURL = '/upload15',
			options = {
				uploadPassword: $('#uploadPassword').val(),
				uploadHidden: 0,
				uploadDesc: $('#uploadDesc').val()
			};

		if ($('#uploadHidden').attr('checked') == true) {
			options.uploadHidden = 1;
		}

		$.ajaxSetup({async: false});
		$.getJSON(UP.env.ajaxBackend +'?t_action=' +UP.env.actionGetUploadUrl +'&t=' +UP.utils.gct(), function (data) {
			$.ajaxSetup({async: true});
			$('#wrap').stopTime('selectUploadServer');
			if (parseInt(data.result, 10) === 1) {
				uploadURL = data.message;
			} else {
				UP.statusMsg.show("Нет свободных серверов. Попробуйте немного позже.", UP.env.msgError, true);
				uploader.enable();
			}
		});

		uploader.setSimUploadLimit(1);
		uploader.upload(id, uploadURL, "POST", options, "file");
		return false;
	}


	// Fired when the user selects files in the "Browse" dialog
	// and clicks "Ok".
	function onFileSelect(e) {
  		for(var f in e.fileList) if (e.fileList.hasOwnProperty(f)) {
    		var file = e.fileList[f];

			if (file.size < 1) {
				UP.statusMsg.show("Нельзя загрузить пустой файл", UP.env.msgError, true);
				return;
			}

			$('#selectedFiles').html(file.name +"&nbsp;— "+UP.utils.formatSize(file.size) +' | <span class="as_js_link dfs" id="link_start_upload">закачать</span>').focus().fadeIn(250);
			$("#link_start_upload").click(function () { return handleUpload(file.id); });
			break;
		}
	}

		// Do something on each file's upload start.
	function onUploadStart(event) {
		active = true;
		startTime = lastActivity = lastProgressTime = UP.utils.gct();

		uploader.disable();
		$('#selectedFiles').fadeOut(250).html();
		$('#upload_status')
			.html('Ожидайте, файл загружается&hellip; <span class="as_js_link dfs" id="link_abort_upload">отменить</span>')
			.fadeIn(250);

		$("#link_abort_upload").click(function () { return handleCancel(); });

		$('#upload_progress').show();
		$(document).stopTime('checkActivity').everyTime('10s', 'checkActivity', function () { checkActivity(); });
	}


	function checkActivity() {
		if ((UP.utils.gct() - lastActivity) > 120*1000) {
			// cancel Uploaded
			uploader.cancel();
			uploader.enable();
			$('#upload_progress,#upload_status').hide();
			$(document).stopTime('checkActivity');
			UP.statusMsg.show('Закачка отменена из-за таймаута', UP.env.msgError, true);
			active = false;
		}
	}

	// Do something on each file's upload progress event.
	function onUploadProgress(event) {
		var received = event["bytesLoaded"],
			total = event["bytesTotal"],
			percent = parseInt(Math.round(100*(received/total)), 10),
			goneTime = UP.utils.gct() - startTime,
			avSpeed,
			avSpeedText,
			timeRemain,
			text,
			speed;

		lastActivity = UP.utils.gct();

		if ((UP.utils.gct() - lastProgressTime) < 500) {
		    return;
		} else {
		    lastProgressTime = UP.utils.gct();
		}


		if (!isNaN(received) && !isNaN(total)) {
			numStr = UP.utils.formatSize(received) +'&nbsp;из&nbsp;' +UP.utils.formatSize(total);
		} else {
			numStr = '';
		}

		speed = (received * 8) / (goneTime / 1000);

		movingAverageHistory.push(speed);
		if (movingAverageHistory.length > 10) {
			movingAverageHistory.shift();
		}

		avSpeed = calculateMovingAverage(movingAverageHistory);
		avSpeedText = UP.utils.formatSpeed(calculateMovingAverage(movingAverageHistory));
		timeRemain = UP.utils.formatTime((total - received) * 8 / avSpeed);

		// progress text result
		text = [numStr, '&nbsp;— ', timeRemain, ' (', avSpeedText, ')'].join('');

		// show progress
		$("#num_progress").css('width', percent +'%');
		$("#progress_text").html(text);
	}

	// Do something when each file's upload is complete.
	function onUploadComplete(event) {
		uploader.enable();
		$('#upload_progress,#upload_status').hide();
		$(document).stopTime('checkActivity');
		active = false;
	}


	function onUploadResponse(event) {
		var data = eval('('+event.data+')');

		if (parseInt(data.error, 10) === 0) {
			document.location = ['http://up.lluga.net/', data.id, '/', data.pass].join('');
		} else {
			UP.statusMsg.show('Ошибка: '+data.message, UP.env.msgError, true);
			$('#upload_progress,#upload_status').hide();
		}
	}


	// Do something if a file upload throws an error.
	// (When uploadAll() is used, the Uploader will
	// attempt to continue uploading.
	function onUploadError(event) {
		uploader.enable();
		UP.statusMsg.show('Ошибка: '+event.status, UP.env.msgError, true);
		$(document).stopTime('checkActivity');
		active = false;
	}


	function calculateMovingAverage (history) {
		var vals = [], size, sum = 0.0, mean = 0.0, varianceTemp = 0.0, variance = 0.0, standardDev = 0.0;
		var i,
			mSum = 0,
			mCount = 0;

		size = history.length;

		// Check for sufficient data
		if (size >= 8) {
			// Clone the array and Calculate sum of the values
			for (i = 0; i < size; i++) {
				vals[i] = history[i];
				sum += vals[i];
			}

			mean = sum / size;

			// Calculate variance for the set
			for (i = 0; i < size; i++) {
				varianceTemp += Math.pow((vals[i] - mean), 2);
			}

			variance = varianceTemp / size;
			standardDev = Math.sqrt(variance);

			//Standardize the Data
			for (i = 0; i < size; i++) {
				vals[i] = (vals[i] - mean) / standardDev;
			}

			// Calculate the average excluding outliers
			var deviationRange = 2.0;
			for (i = 0; i < size; i++) {

				if (vals[i] <= deviationRange && vals[i] >= -deviationRange) {
					mCount++;
					mSum += history[i];
				}
			}

		} else {
			// Calculate the average (not enough data points to remove outliers)
			mCount = size;
			for (i = 0; i < size; i++) {
				mSum += history[i];
			}
		}

		return mSum / mCount;
	}



	//public
	return {
		init: function () {
			var uiLayer = YAHOO.util.Dom.getRegion('selectLink'),
				overlay = YAHOO.util.Dom.get('uploaderOverlay');

			YAHOO.util.Dom.setStyle(overlay, 'width', uiLayer.right-uiLayer.left + "px");
			YAHOO.util.Dom.setStyle(overlay, 'height', uiLayer.bottom-uiLayer.top + "px");

			// Custom URL for the uploader swf file (same folder).
			YAHOO.widget.Uploader.SWFURL = "/yui/build/uploader/assets/uploader.swf";

    		// Instantiate the uploader and write it to its placeholder div.
			uploader = new YAHOO.widget.Uploader("uploaderOverlay");

			// Add event listeners to various events on the uploader.
			// Methods on the uploader should only be called once the
			// contentReady event has fired.

			uploader.addListener('contentReady', onContentReady);
			uploader.addListener('fileSelect', onFileSelect)
			uploader.addListener('uploadStart', onUploadStart);
			uploader.addListener('uploadProgress', onUploadProgress);
			//uploader.addListener('uploadCancel', onUploadCancel);
			uploader.addListener('uploadComplete', onUploadComplete);
			uploader.addListener('uploadCompleteData', onUploadResponse);
			uploader.addListener('uploadError', onUploadError);

				// set onunload event
			var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
			if (typeof root.onbeforeunload !== "undefined") {
				root.onbeforeunload = confirmExit;
			} else {
				window.onbeforeunload = function (o) {
					if (confirmExit()) {
						o.returnValue = confirmExit();
					}
				};
			}
		}
	};
}();



