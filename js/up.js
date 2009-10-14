jQuery.fn.extend({
	fancyAnimate: function(startBG, finishBG, interval) {
		if (interval === 'undefined') {
			interval = 450;
		}

		return this.each(function() {
			$(this).animate({backgroundColor: startBG}, interval, function () {
				$(this).animate({backgroundColor: finishBG}, interval);
			});
		});
	},
});



if (typeof UP === "undefined" || !UP) {
	var UP = {};
}


UP.env = UP.env || {
	uploadWarnMsg: 'Идет закачка. Хотите остановить?',

	// Status MSG types
	msgInfo: 1,
	msgError: 2,
	msgWarn: 3,
	msgWait: 4,

	// AJAX backends
	ajaxBackend: '/script/ajax_backend.php',
	ajaxAdminBackend: '/script/ajax_admin_backend.php',

	// AJAX actions
	actionOwnerRemove: 1,
	actionOwnerUnRemove: 2,
	actionOwnerRename: 3,
	actionOwnerPassword: 4,
	actionOwnerIm: 5,
	actionSearch: 10,
	actionLive: 11,
	actionGetPage: 12,
	actionGetUploadUrl: 13,
	actionGetComments: 14,
	actionOwnerDeleteItem: 15,
	actionOwnerUnDeleteItem: 16,
	actionOwnerHideItem: 17,
	actionOwnerUnHideItem: 18,

	actionAdminRemoveFeedbackMessage: 50,
	actionAdminRemoveComment: 51,

	// AJAX admin action
	actionAdminUnDeleteItem: 24,
	actionAdminDeleteItem: 25,
	actionAdminMarkItemAsSpam: 26,
	actionAdminUnMarkItemAsSpam: 27,
	actionAdminMarkItemAsAdult: 28,
	actionAdminUnMarkItemAsAdult: 29,
	actionAdminHideItem: 30,
	actionAdminUnHideItem: 31,
	debug: true,

	// cookie
	itemInfoStatusCookie: 'up_itemInfoStatusCookie'
};


// class for upload process
UP.uploadForm = function () {
	// private
	var active = false,
		startTime = null,
		bigTimeoutCount = 0,
		cProgress = 0;


	function getProgress(id) {
		if (!id || active == false) {
			return;
		}

		var numStr = '',
			speedTime = '',
			timeRemain = '',
			speed = '',
			avSpeed = 0,
			avSpeedText = '',
			text = '',
			timeGone = 0,
			reqStartTime = 0,
			reqTimeout = 0,
			movingAverageHistory = [];

		reqStartTime = UP.utils.gct();


		$.ajax({
			type: 'GET',
			url: '/progress?X-Progress-ID=' +id,
			dataType: 'json',
			error: function () {
				UP.statusMsg.show('Внимание: мониторинг закачки отключен из-за ошибки', UP.env.msgWarn, true);
				$('#upload_progress').hide(200);
				return;
			},

			success: function (upload) {
				reqTimeout = UP.utils.gct() - reqStartTime;
				if (upload.state === 'error') {
					return;
				}

				if (upload.state === 'done') {
					active = false;
					upload.percents = 100;
					$('#upload_progress').hide();
					$('#upload_status').html('Ожидайте, файл обрабатывается&hellip;');
				} else if (upload.state === 'uploading') {
					timeGone = UP.utils.gct() - startTime;


					upload.percents = parseInt(Math.floor(((upload.received / upload.size) * 100)), 10);

					if (isNaN(upload.percents)) {
						upload.percents = 0;
					}


					if (!isNaN(upload.received) && !isNaN(upload.size)) {
						numStr = UP.utils.formatSize(upload.received) +'&nbsp;из&nbsp;' +UP.utils.formatSize(upload.size);
					} else {
						numStr = '';
					}

					speedTime = upload.received / (timeGone / 1000); // bytes/s
					speed = (upload.received * 8) / (timeGone / 1000);

					// average speed
					movingAverageHistory.push(speed);
					if (movingAverageHistory.length > 10) {
						movingAverageHistory.shift();
					}

					avSpeed = calculateMovingAverage(movingAverageHistory);
					avSpeedText = UP.utils.formatSpeed(calculateMovingAverage(movingAverageHistory));

					timeRemain = UP.utils.formatTime((upload.size - upload.received) * 8 / avSpeed);

					// progress text result
					text = [numStr, '&nbsp;— ', timeRemain, ' (', avSpeedText, ')'].join('');

					// show progress
					if (upload.percents > cProgress && upload.percents > 1 && parseInt(upload.percents - cProgress, 10) > 1) {
						$("#num_progress").css('width', upload.percents +'%');
						$("#progress_text").html(text);
						cProgress = upload.percents;
					}
				}

				if ((upload.state === 'uploading') || (upload.state === 'starting')) {

					// compute timeout value
					if (reqTimeout < 500) {
						reqTimeout = 500;
					} else if (reqTimeout > 500 && reqTimeout < 750) {
						reqTimeout = 800;
					} else if (reqTimeout > 800 && reqTimeout < 1500) {
						reqTimeout = 1500;
					} else if (reqTimeout > 1500 && reqTimeout < 5000) {
						reqTimeout = 5000;
					} else {
						reqTimeout = 6000;
						bigTimeoutCount++;
					}

					reqTimeout = 5000;

					if (bigTimeoutCount === 5) {
						UP.statusMsg.show('Мониторинг закачки отключен из-за таймаутов. Загрузка файла продолжается&hellip;', UP.env.msgWarn, true);
						return;
					}

					// start new progress
					$('#uploadForm').oneTime(reqTimeout, 'progressTime', function () { getProgress(id); });
				}
			}
		});
	}


	function confirmExit() {
		if (active === true) {
			return UP.env.uploadWarnMsg;
		}
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
		start: function () {
			UP.statusMsg.clear(); 		// clear status area
			active = true; 	// set start

			$('#uploadSubmit').attr("disabled", "disabled");

			$('#upload_status')
				.html('Ожидайте, файл загружается&hellip; <a href="/" id="link_abort_upload">отменить</a>')
				.fadeIn(350);

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

			// start deffered upload progress
			var progressId = $('#progress_id').val();
			if (progressId && active) {
				$('#uploadForm')
					.stopTime('progressTime')
					.oneTime(2000, 'progressTime',
					function () { getProgress(progressId); });
			}

			$('#upload_progress').fadeIn(450);

			startTime = UP.utils.gct();
			return true;
		},


		//
		finish: function (id, pass) {
			active = false;
			$('#uploadFile').removeAttr("disabled");
			$('#uploadForm').stopTime('progressTime');

			var options = {
				pass: pass,
			}

			UP.utils.makePOSTRequest('/'+id+'/', options);
		},


		//
		error: function (msg) {
			active = false;
			$('#uploadFile').removeAttr('disabled').focus();
			$('#uploadForm').stopTime('progressTime');

			$('#upload_progress,#upload_status').hide();
			UP.formCheck.upload();

			if (msg && msg.length > 0) {
				UP.statusMsg.show(msg, UP.env.msgError, true);
			}
		}
	};
}();


// class for media functions
UP.media = {
	mp3: function (blockId, link) {
		var mp3obj = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"' +
			'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0">' +
			'<param name="allowScriptAccess" value="sameDomain"/>' +
			'<param name="movie" value="/player/mp3.swf?file='+link+'&startplay=true"/>' +
			'<param name="quality" value="high"/>' +
			'<param name="startplay" value="true"/>' +
			'<param name="bgcolor" value="#ffffff"/>' +
			'<embed src="/player/mp3.swf?file='+link+'&startplay=true"' +
				'startplay="true" quality="high" bgcolor="#ffffff" width="96" height="20"' +
				'name="own_flashplayer" align="middle" allowScriptAccess="sameDomain"' +
				'type="application/x-shockwave-flash"' +
				'pluginspage="http://www.macromedia.com/go/getflashplayer"/>' +
		'</object>';

		$('#'+blockId).html(mp3obj);
	},


	flv: function (blockId, link) {
		var so = new SWFObject('/player/player.swf',blockId, '512', '384','0','#FFFFFF');
		so.addParam('allowFullscreen', 'false');
		so.addVariable('bgColor', '#000000');
		so.addVariable('video',link);
		so.addVariable('css','/style/default.css');
		so.addVariable('skin','/player/default.swf');
		so.addVariable('cover','');
		so.write(blockId);
	}
};


// class form owner functions
UP.owner = function () {
	var waitTime = 500,
		primary = null;

	function wait(label, timeout) {
		UP.statusMsg.clear();
		$(document)
			.stopTime(label)
			.oneTime(timeout, label, function () {
					UP.statusMsg.show('Ожидайте&hellip;', UP.env.msgWait, false);
				});
	}

	function startWait() {
		wait('waitTimer', waitTime);
	}

	function stopWait() {
		$(document).stopTime('waitTimer');
	}

	function onError(msg) {
		if (msg === undefined) {
			UP.statusMsg.show('<strong>Ошибка: </strong>AJAX запроса', UP.env.msgError, true);
		} else {
			UP.statusMsg.show('<strong>Ошибка: </strong>'+msg, UP.env.msgError, true);
		}
		$("#owner_delete_link, #owner_rename_link").attr("status", "on");
	}


	return {
		rename: function(id, magic) {
			if ($("#owner_rename_link").attr("status") === 'off') {
				return false;
			}

			var currentName = $('#item_info_filename').text(),
				newName = prompt("Введите новое имя для файла", currentName);

			if (!newName || newName.length < 1) {
				return false;
			}

			// same name
			if (currentName === newName) {
				return;
			}

			startWait();

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerRename, t_id: id, t_magic: magic, t_new_name: newName },
				dataType: 'json',
				complete: function () {
					stopWait();
				},
				error: function () {
					onError();
				},
				success: function(data) {
					if (parseInt(data.result, 10) === 1) {
						$('#item_info_filename').fadeOut(350, function() {
							$(this).text(data.message);
						}).fadeIn(250);
					} else {
						onError(data.message);
					}
				}
			});
		},


		changePassword: function(id, isChangeCurrent, magic) {
			if ($("#owner_password_link").attr("status") === 'off') {
				return false;
			}

			UP.statusMsg.clear();

			if (parseInt(isChangeCurrent, 10) === 1) {
				newPassword = prompt("Введите новый пароль для файла");
			} else {
				newPassword = prompt("Введите пароль для файла");
			}


			startWait();

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerPassword, t_id: id, t_magic: magic, t_password: newPassword },
				dataType: 'json',
				complete: function () {
					stopWait();
				},
				error: function () {
					onError();
				},
				success: function(data) {
					if (parseInt(data.result, 10) === 1) {
						var okMsg = (parseInt(isChangeCurrent, 10) === 1) ? 'Пароль успешно изменён' : 'Пароль успешно установлен';
						UP.statusMsg.show(okMsg, UP.env.msgInfo, true);
					} else {
						onError(data.message);
					}
				}
			});
		},


		remove: function(id, magic) {
			if ($("#owner_delete_link").attr("status") === 'off') {
				return false;
			}

			primary = $('#primary').html();
			startWait();

			$.ajax({
				type: 	'GET',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerRemove, t_id: id, t_magic: magic },
				dataType: 'json',
				beforeSend: function() {
					$("#owner_delete_link, #owner_rename_link").attr("status", "off");
				},
				complete: function() {
					stopWait();
				},
				error: function() {
					onError();
				},
				success: function(data)	{
					if (parseInt(data.result, 10) === 1) {
						$('#primary').fadeOut(350, function() {
							$('#primary').html('<div id="status">&nbsp;</div><div id="r1"><h2>Файл удалён</h2>' +
								'<p>Примечание: удалён владельцем файла</p></div>' +
								'<div id="unremoveBlock" style="margin-top: .6em;">' +
								'<span class="as_js_link" id="unremoveLink">Отменить</span> или ' +
								'<a href="/">перейти на главную страницу</a>?</div>');

								$('#unremoveLink').mousedown(function() { UP.owner.unRemove(id, magic); });
							}).fadeIn(250);
					} else {
						onError(data.message);
					}
				}
			});
		},

		unRemove: function(id, magic) {
			startWait();

			$.ajax({
				type: 	'GET',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerUnRemove, t_id: id, t_magic: magic },
				dataType: 'json',
				complete: function() {
					stopWait();
				},
				error: function() {
					onError();
				},
				success: function(data) {
					if (parseInt(data.result, 10) === 1) {
						$('#primary').fadeOut(200, function() {
							$('#primary').html(primary);
							primary = '';
						}).fadeIn(300);
					} else {
						onError(data.message);
					}
				}
			});
		},

		makeMeOWner: function(user_id, item_id, magic) {
			startWait();

			$.ajax({
				type: 	'GET',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionOwnerIm, t_id: item_id, t_magic: magic, t_uid: user_id },
				dataType: 'json',
				complete: function() {
					stopWait();
				},
				error: function() {
					onError();
				},
				success: function(data) {
					if (parseInt(data.result, 10) === 1) {
						$('#im_owner_block').hide(350, function () {
							UP.statusMsg.show(data.message, UP.env.msgInfo, true);
					});
					} else {
						onError(data.message);
					}
				}
			});
		}
	};
}();


// class for pretty messaging
UP.statusMsg = function () {
	// Remove message if mouse is moved or key is pressed
	function bindEvents() {
		jQuery(window).mousemove(UP.statusMsg.clear).click(UP.statusMsg.clear).keypress(UP.statusMsg.clear);
	}


	// publics
	return {
		// Unbind mouse & keyboard
		clear: function() {
			// clear
			$(document).stopTime('t1').stopTime('t2');

			// unbind events
			jQuery(window)
				.unbind('mousemove', this.clear)
				.unbind('click', this.clear)
				.unbind('keypress', this.clear);

			var empty = function () {
				$("#status").css({opacity: "0"}).html("&nbsp;");
			};

			$("#status").stop();
			if ($("#status").css('opacity') > 0) {
				$("#status").animate({ opacity: "0.1" }, 150, empty);
			} else {
				empty();
			}
		},

		// show message
		show: function (msg, type, clearAfter) {
			var msgClass = '';

			// clearTimeouts
			$(document).stopTime('t1').stopTime('t2');
			$("#status").stop();

			jQuery(window)
				.unbind('mousemove', this.clear)
				.unbind('click', this.clear)
				.unbind('keypress', this.clear);

			switch (type) {
				case UP.env.msgError:
					msgClass = 'error';
					break;

				case UP.env.msgWarn:
					msgClass = 'warning';
					break;

				case UP.env.msgWait:
					msgClass = 'waiting';
					break;

				case UP.env.msgInfo:
					msgClass = 'info';
					break;

				default:
					msgClass = 'info';
			}

			// stop animation and show our msg
			$("#status")
				.html(['<span type="', msgClass, '">', msg, '</span>'].join(''))
				.css({opacity: "1.0"});

			if ((clearAfter === 'undefined') || (clearAfter === true)) {
				this.defferedClear();
			}
		},


		defferedClear: function() {
			// set mouse and keyboard
			$(document).stopTime('t1').oneTime(1500, 't1', function () { bindEvents(); });

			// set just timeout gone
			var that = this;
			$(document).stopTime('t2').oneTime(5000, 't2', function () { that.clear(); });
		}
	};
}();


UP.fancyLogin = function () {
	var form = $("form[name='fancyLogin']"),
		submit = form.find("input[type='submit']"),
		fancy = $('#fancyLogin');

	function close() {
		fancy.fadeOut(50);
		$('#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();
	}

	function show() {
		if (typeof document.body.style.maxHeight === "undefined") {//if IE 6
			$("body","html").css({height: "100%", width: "100%"});
			$("html").css("overflow","hidden");
			if (document.getElementById("TB_HideSelect") === null) {//iframe to hide select elements in ie6
				$("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div>");
				$("#TB_overlay").click(close);
			}
		} else {//all others
			if (document.getElementById("TB_overlay") === null){
				$("body").append("<div id='TB_overlay'></div>");
				$("#TB_overlay").click(close);
			}
		}


		$("#TB_overlay").addClass("TB_overlayBG");//use background and opacity

		// just show for Opera
		if (jQuery.browser.opera) {
			fancy.show();
		} else {
			fancy.fadeIn(100);
		}
	}


	return {
		init: function () {
			if (!$('.mainMenuLogin')) {
				return;
			}


			UP.formCheck.register(form);

			form.find("input[required],textarea[required]")
				.change(function () { UP.formCheck.register(form); })
				.keyup(function () { UP.formCheck.register(form);	})

			$(document)
				.stopTime('checkFancyLoginFormTimer')
				.everyTime(500, 'checkFancyLoginFormTimer', function () { UP.formCheck.register(form); });

			$('.mainMenuLogin').click(function () {
				if (fancy.is(":visible")) {
					close();
				} else {
					show();
					UP.formCheck.register(form);
				}

				$("[required][value='']:first").focus();
				return false;
			}).addClass("as_js_link");

			$("input[name='close']").click(function () {
				close();
			});

			$(document).bind('keydown click', function (e) {
            	if ( ((e.keyCode == 27) && !(e.ctrlKey || e.altKey))) {
					if ($('#fancyLogin').is(":visible")) {
						close();
					}
				}
			});

			// form
			var options = {
				url:	'/login/?json',
				dataType: 'json',
				resetForm: false,
				cleanForm: false,
				beforeSubmit: function (formArray, jqForm) {
					UP.wait.start();
					$(document).stopTime('checkFancyLoginFormTimer');
					submit.attr("disabled", "disabled");
					return true;
				},

				error: function () {
					UP.wait.stop();
					$(document).everyTime(500, 'checkFancyLoginFormTimer', function () { UP.formCheck.register(form); });
					UP.statusMsg.show('Невозможно авторизироваться. Попробуйте позже.', UP.env.msgError, false);
				},

				success: function (r) {
					UP.wait.stop();
					submit.removeAttr("disabled");

					if (r) {
						$("label").each(function () {
							$(this).removeClass('bad').addClass('good');
						});

						if (parseInt(r.error, 10) === 0) {
							form.clearForm().resetForm();
							$("#fancyLogin").fadeTo(300, 0.01, function() {
								location.reload();
								$("[required='1'][value='']:first").focus();
							});
						} else {
							UP.statusMsg.show(r.message, UP.env.msgError, true);

							if (r.fields) {
								var fields = r.fields.split(' ');
								jQuery.each(fields, function() {
									$("label#label_" + this).removeClass('good').addClass('bad');
								});
							}

							$(".bad:first").focus();
							$(document).everyTime(500, 'checkFancyLoginFormTimer', function () { UP.formCheck.register(form); });
						}
					} else {
						UP.statusMsg.show('Невозможно авторизироваться. Попробуйте позже.', UP.env.msgError, false);
					}
				}
			};

			form.submit(function () {
				$(this).ajaxSubmit(options);
				return false;
			});
		}
	};
}();


//
UP.utils = function () {
	return {
		JSLinkListToggle: function (t) {
			var itemShowID = t.attr("rel"),
				list = t.parent().parent();

			// hide all
			list.children("li").each(function () {
				var itemHideID = $(this).children("span.as_js_link").attr("rel");
				if (itemHideID != itemShowID) {
					$("#"+itemHideID).hide();
				}
			});

			$("#"+itemShowID).toggle();

			if ($("#"+itemShowID).is(":visible")) {
				$.cookie(UP.env.itemInfoStatusCookie, itemShowID, { expires: 24, path: '/' });
				$("[required='1'][value='']:first").focus();
			} else {
				$.cookie(UP.env.itemInfoStatusCookie, '', { expires: 24, path: '/' });
			}
		},

		makePOSTRequest: function (url, options) {
			try {
			  	var form = $('<form/>');

			  	form.attr('action', url);
			  	form.attr('method', 'post');
			  	form.appendTo('body');

				 if (options) {
	             	for (var n in options) {
		                $('<input type="hidden" name="'+n+'" value="'+options[n]+'"/>').appendTo(form);
					}
				}

				form.submit();
			} finally {
				form.remove();
			}
		},

		formatSize: function(bytes) {
			// bytes
			if (bytes < 1024) {
				return [bytes, ' б'].join('');
			} else if (bytes < 1048576) {
				return [Math.round(bytes/1024), ' КБ'].join('');
			} else if (bytes < 1073741824) {
				return [Math.round(bytes/1048576), ' МБ'].join('');
			} else if (bytes < 1099511627776) {
				return [Math.round((bytes / 1073741824) * 100) / 100, ' ГБ'].join('');
			} else {
				return [Math.round((bytes/1099511627776) * 100) / 100, ' ТБ'].join('');
			}
		},


		formatSpeed: function(bit) {
			if (bit < 1000) {
				return Math.round(bit) +'&nbsp;б/с';
			} else if (bit < 1000000) {
				return Math.round(bit/1000) +'&nbsp;кб/с';
			} else if (bit < 1000000000) {
				return Math.round(bit/1000000) +'&nbsp;мб/с';
			} else if (bit < 1000000000000) {
				return Math.round(bit/1000000000) +'&nbsp;гб/с';
			} else {
				return Math.round(bit/1000000000000) +'&nbsp;тб/с';
			}
		},


		formatTime: function(sec) {
			var sec = parseInt(sec, 10) || 0,
				minutes = ["минут","минуты","минуту"],
				seconds = ["секунд","секунды","секунду"];

			if (sec < 11) {
				return 'меньше 10 секунд';
			} else if (sec < 91) {
				return [sec, '&nbsp;', UP.utils.getCase(parseInt(sec, 10),seconds[0],seconds[1],seconds[2])].join('');
			} else if (sec < 3601) {
				return [Math.round(sec/60), '&nbsp;', UP.utils.getCase(parseInt(Math.round(sec/60), 10),minutes[0],minutes[1],minutes[2])].join('');
			} else {
				return [Math.round(sec/3600), '&nbsp;часов'].join('');
			}
		},


		getCase: function (value, gen_pl, gen_sg, nom_sg)
		{
			if ((value % 100 >= 5) & (value % 100 <= 20)) {
				return gen_pl;
			}

			value = value % 10;
			if (((value >= 5) & (value <= 9)) | (value === 0)) {
				return gen_pl;
			}

			if ((value >= 2) & (value <= 4)) {
				return gen_sg;
			}

			if (value === 1) {
				return nom_sg;
			}
		},

		gct: function () {
			return new Date().getTime();
		},

		getPE: function () {
			$.ajax({
	   			type: 	'GET',
	   			url: 	UP.env.ajaxBackend,
	   			data: 	{ t_action: UP.env.actionLive },
				dataType: 'json',
				error: function() { UP.statusMsg.show('<strong>Ошибка: </strong>AJAX запроса', UP.env.msgError, true); },
	   			success: function(r) {
					if (parseInt(r.result, 10) === 1) {
						var chash = $.sha1(r.message),
							hash = $('#wrap').data('hash');

						if (hash !== chash) {
							$('#wrap').data('hash', chash);
							$('#result').html(r.message);
						}
					} else {
						UP.statusMsg.show(r.message, UP.env.msgError, true);
					}
				}
			 });
		}
	};
}();


UP.wait = function () {
	return {
		start: function (msg) {
			UP.statusMsg.clear();

			msg = msg || 'Ожидайте&hellip;';

			$(document)
				.stopTime('waitTimer')
				.oneTime(400, 'waitTimer', function () {
						UP.statusMsg.show(msg, UP.env.msgWait, false);
					});
		},

		stop: function () {
			$(document).stopTime('waitTimer');
			UP.statusMsg.clear();
		}
	};
}();

//
UP.formCheck = {
	upload: function () {
		var submit = $("input[type='file']").parent().find("input[type='submit']");
		if ($("input[type='file'][value!='']").size() != 0) {
			submit.removeAttr("disabled");
		} else {
			submit.attr("disabled", "disabled");
		}
	},


	search: function (s, e) {
		var elm = e || $(this),
			minLength = parseInt(elm.attr("minLength"), 10) || 1;

		$("input[type='submit']").attr("disabled", (elm.val().length < minLength ? 'disabled' : ''));
	},

	register: function (form) {
		var input = 0,
			checkbox = 0,
			all = 0,
			minRequired = 0;

		if (!form || !form.not(":visible")) {
			//UP.log.debug('no form or not visible');
			return;
		}

		// set minRequired
		minRequired = parseInt($(form).find("input[name='form_check_required_num']").val(), 10) || 0;

		$(form).find('"input[required], textarea[required]').each(function () {
			var el = $(this),
				elMinLength = parseInt($(el).attr('minlength'), 10) || 0,
				label;

				if (elMinLength > ($(el).val().length)) {
					input++;

					$(el).removeClass('good').addClass('bad');
				} else {
					// check for pattern
					if ($(el).attr("pattern")) {
						var pattern = new RegExp($(el).attr("pattern").toString(), "i");

						if (pattern.test($(el).val())) {
							$(el).removeClass('bad').addClass('good');
						} else {
							$(el).removeClass('good').addClass('bad');
							input++;
						}
					} else {
						$(el).removeClass('bad').addClass('good');
					}
				}
		});

		form.find("input[type='checkbox'][required]").each(function () {
			var el = $(this);
			if ($(el).attr('checked') === false) {
				checkbox++;
			}
		});

		all = input + checkbox;
		form.find("input[type='submit']").attr("disabled", !!(all > minRequired));
	}
};


UP.comments = function () {
	var lastCommentID = 0;

	function updateNumComments() {
		$("#commentsNum").text($(".commentList").children("li").size());
	}

	return {
		remove: function (item_id) {
			var id = parseInt(item_id, 10);

			if (id < 0) {
				alert('Отсутствует номер комментария');
				return;
			}

			$.ajax({
				type: 	'GET',
				url: 	UP.env.ajaxAdminBackend,
				data: 	{ t_action: UP.env.actionAdminRemoveComment, t_id: id },
				dataType: 'json',
				error: function() {
					UP.wait.stop();
					UP.statusMsg.show('Невозможно удалить комментарий', UP.env.msgError, false);
				},
				success: function(data) {
					UP.wait.stop();
					var comment = $("#comment_"+id);

					if (parseInt(data.result, 10) === 1) {
						comment.hide(350, function () {
							$(this).remove();
							updateNumComments();
						});
					} else {
						//UP.utils.fancyAnimate(comment, "#fa9cac", "#ffffff");
						comment.fancyAnimate("#fa9cac", "#ffffff");
						UP.statusMsg.show(data.message, UP.env.msgError, false);
					}
				}
			});
		},

		loadCommentsList: function (item_id, owner_id) {
			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: UP.env.actionGetComments, t_id: item_id, t_last_id: lastCommentID, t_owner_id: owner_id },
				dataType: 'json',
				beforeSend: function () {
					$(document).oneTime(250, 'commentAddWaitTimer', function () {
						$("#commentStatus").html('<span type="waiting">Ожидайте, загружаются новые комментарии&hellip;</span>').show(200);
					});
				},
				error: function () {
					$(document).stopTime('commentAddWaitTimer');
					$("#commentStatus").html('<span type="error">Невозможно загрузить комментарии</span>').show(200);
				},
				success: function (data) {
					$(document).stopTime('commentAddWaitTimer');
					$("#commentStatus").html('&nbsp;');
					if (parseInt(data.result, 10) === 1) {
						$(".commentList").append(data.message);
						// mark new comments
						if (lastCommentID != 0) {
							$(".commentList").children("li").each(function () {
								var item = $(this),
									item_id = parseInt(item.attr('id').split('comment_')[1], 10);

									//UP.log.debug("Flash new : #"+item_id)

								if (lastCommentID < item_id) {
									//UP.utils.fancyAnimate($("#comment_"+item_id), "#e4f2fd", "#ffffff");
									$("#comment_"+item_id).fancyAnimate("#e4f2fd", "#ffffff");
								}
							});
						}
						// get last comment ID
						if ($(".commentList").children("li").size() > 0) {
							lastCommentID = parseInt($(".commentList").children("li:last").attr('id').split('comment_')[1], 10) || 0;
						} else {
							lastCommentID = 1;
						}

						// update coments counter
						updateNumComments();
					} else {
						$("#commentStatus").html('<span type="error">'+data.message+'</span>').show(200);
					}
				}
			});
		}
	};
}();


UP.log = function () {
	return {
		debug: function () {
			 if (UP.env.debug === true && window.console && window.console.log) {
        		window.console.log('[АП] ' + Array.prototype.join.call(arguments,''));
			}
		}
	};
}();


// class for admin functions
UP.userFiles = function () {
	var waitTimeout = 400,
		maxItems = 100,

		// type of value of cb
		normal = 1,
		deleted = 2,
		hidden = 3;


	function getCheckedItemsID() {
		var items = [],
			i = 0;

		$(':checkbox:checked').each(function () {
				if (i >= maxItems) {
					return false;
				}
				var id = parseInt($(this).attr('id').split('item_cb_')[1], 10);

				if (! isNaN(id)) {
					items.push(id);
					i = i + 1;
				}
			}
		);

		return items.join(':');
	}

	function getAffectedItemsID(type) {
		var items = [],
			i = 0;

		$([':checkbox[value=', type, ']'].join(''))
			.each(function () {
				if (i >= maxItems) {
					return false;
				}
				var id = parseInt($(this).attr('id').split('item_cb_')[1], 10);

				if (! isNaN(id)) {
					items.push(id);
					i = i + 1;
				}
			}
		);

		return items.join(':');
	}


	function showNumCheckedCB() {
		return $(":checkbox:checked[value='1']:visible").size();
	}


	function cbResultSuccess(itemsAsString, undo, type) {
		var itemsOK = itemsAsString.split(':'),
			ok_num = 0,
			i = 0,
			max = 0,
			id;

		if (!undo) {
			$(':checkbox[value='+type+']').each(function () {
				var hiddenID = parseInt($(this).attr('id').split('item_cb_')[1], 10);
				$('#row_item_'+hiddenID).remove();
				UP.log.debug('RemoveHidden: '+hiddenID);
			});
		}

		for (i = 0, max = itemsOK.length; i <= max; i = i + 1) {
			id = parseInt(itemsOK[i], 10);

			if (!isNaN(id) && id > 0) {
				if (undo === true) {
					$('#item_cb_' + id).attr('value', 1).attr("checked", true);
					$('#row_item_' + id).addClass('selected').show();
				} else {
					$('#item_cb_' + id).removeAttr('checked').attr('value', type);
					$('#row_item_' + id).removeClass('selected').hide();
					UP.log.debug('hide: '+id);
				}
				ok_num = ok_num + 1;
			}
		}

		checkButtonsDisabled();

		//
		return ok_num;
	}


	function wait(label, timeout) {
		$('#wrap').stopTime(label).oneTime(timeout, label, function () {
			UP.statusMsg.show('Ожидайте&hellip;', UP.env.msgWait, false);
		});
	}

	function onComplete(timerLabel) {
		$('#wrap').stopTime(timerLabel);
		$(':checkbox').removeAttr('disabled');
		$('#allCB').removeAttr('checked');
		checkButtonsDisabled();
	}

	function onError() {
		UP.statusMsg.show('<strong>Ошибка: </strong>AJAX запроса', UP.env.msgError, true);
	}

	function checkButtonsDisabled() {
		var m = showNumCheckedCB();
		$(':button').attr("disabled", (m < 1 ? 'disabled' : ''));
	}


	// public
	return {

		//
		deleteItem: function (undo) {
			var items = getCheckedItemsID(),
				actions = UP.env.actionOwnerDeleteItem,
				undoLink = '<span class="as_js_link" onclick="UP.userFiles.deleteItem(true);" title="Отменить удаление">Отменить</span>';

			if (undo === true) {
				items = getAffectedItemsID(deleted);
				actions = UP.env.actionOwnerUnDeleteItem;
				undoLink = '';
			}

			if (!items) {
				return;
			}

			wait('deleteTimer', waitTimeout);

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: actions, t_ids: items },
				dataType: 'json',
				beforeSend: function () {
					$(':checkbox, :button').attr('disabled', 'disabled'); // disable all input
				},
				complete: function () {
					onComplete('deleteTimer');
				},
				error: 	function () {
					onError();
				},
				success: function (data) {
					if (parseInt(data.result, 10) === 1) {
						var ok_num = cbResultSuccess(data.message, undo, deleted);

						if (undo === true) {
							UP.statusMsg.clear();
						} else {
							var msgDelete = UP.utils.getCase((ok_num), 'Удалено ', 'Удалены ', 'Удалён '),
								msgOK = [msgDelete, ok_num, UP.utils.getCase((ok_num), ' файлов', ' файла', ' файл'), undoLink].join('');
							UP.statusMsg.show(msgOK, UP.env.msgInfo, false);
						}
					} else {
						UP.statusMsg.show(data.message, UP.env.msgError, true);
					}
				}
			});
		},

		cbStuffStart: function () {
			var state,
				n = 0,
				m = 0,
				box,
				id;
			$(':checkbox').attr('checked', false); 	// make all unchecked

			//
			$('#allCB').bind('change', function () {
				state = $(this).attr('checked');
				$(":checkbox[value='1']:visible").attr('checked', state);

				n = showNumCheckedCB();
				$(':button').attr("disabled", (n < 1 ? 'disabled' : ''));

				$("tr.row_item").toggleClass('selected', state);
			});

			//
			$(":checkbox[value='1']:visible").bind('change', function () {
				m = showNumCheckedCB();
				$(':button').attr("disabled", (m < 1 ? 'disabled' : ''));

				// select
				box = $(this);
				id = parseInt(box.attr('id').split('item_cb_')[1], 10);

				if (!isNaN(id)) {
					if (box.attr('checked')) {
						$('#row_item_' + id).addClass('selected');
					} else {
						$('#row_item_' + id).removeClass('selected');
					}
				}
			});
		}

	};
}();



// On start on every page
jQuery(function () {
	UP.fancyLogin.init();
});

