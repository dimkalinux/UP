"use strict";

// class for admin functions
UP.admin = function () {
	var waitTimeout = 400,
		maxItems = 100,

		// AJAX admin action
		ACTION_ADMIN_DELETE_FILE = 25,
		ACTION_ADMIN_UNDELETE_FILE = 24,
		ACTION_ADMIN_MARK_AS_SPAM_FILE = 26,
		ACTION_ADMIN_UNMARK_AS_SPAM_FILE = 27,
		ACTION_ADMIN_MARK_AS_ADULT_FILE = 28,
		ACTION_ADMIN_UNMARK_AS_ADULT_FILE = 29,


		// type of value of cb
		normal = 1,
		deleted = 2,
		spamed = 3,
		adulted = 4;


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

		$(['input[type=checkbox][value=', type, ']'].join(''))
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

		// flush page cache
		UP.cache.flush();

		for (i = 0, max = itemsOK.length; i <= max; i = i + 1) {
			id = parseInt(itemsOK[i], 10);

			if (!isNaN(id) && id > 0) {
				if (undo === true) {
					$('#item_cb_' + id).attr('value', 1);
					$('#row_item_' + id).addClass('canceled').show();
				} else {
					$('#item_cb_' + id).removeAttr('checked').attr('value', type);
					$('#row_item_' + id).removeClass('canceled').hide();
				}
				ok_num = ok_num + 1;
			}
		}

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
	}

	function onError() {
		UP.statusMsg.show('<strong>Ошибка: </strong>AJAX запроса', UP.env.msgError, true);
	}


	// public
	return {

		//
		deleteItem: function (undo) {
			var items = getCheckedItemsID(),
				actions = ACTION_ADMIN_DELETE_FILE,
				undoLink = '<span class="as_js_link" onclick="UP.admin.deleteItem(true);">Отменить</span>';

			if (undo === true) {
				items = getAffectedItemsID(deleted);
				actions = ACTION_ADMIN_UNDELETE_FILE;
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
					if (data.result === '1') {
						var ok_num = cbResultSuccess(data.message, undo, deleted);

						if (undo === true) {
							UP.statusMsg.clear();
						} else {
							var msgOK = ['Удалены ', ok_num, UP.utils.getCase((ok_num), ' файлов', ' файла', ' файл'), undoLink].join('');
							UP.statusMsg.show(msgOK, UP.env.msgInfo, false);
						}
					} else {
						UP.statusMsg.show(data.message, UP.env.msgError, true);
					}
				}
			});
		},


		//
		markItemSpam: function (undo) {
			var items = getCheckedItemsID(),
				actions = ACTION_ADMIN_MARK_AS_SPAM_FILE,
				undoLink = '<span class="as_js_link" onclick="UP.admin.markItemSpam(true);">Отменить</span>';

			if (undo === true) {
				items = getAffectedItemsID(spamed);
				actions = ACTION_ADMIN_UNMARK_AS_SPAM_FILE;
				undoLink = '';
			}

			if (!items) {
				return false;
			}

			wait('markSpamTimer', waitTimeout);

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: actions, t_ids: items },
				dataType: 'json',
				beforeSend: function () {
					$(':checkbox, :button').attr('disabled', 'disabled'); // disable all input
				},
				complete: function () {
					onComplete('markSpamTimer');
				},
				error: 	function () {
					onError();
				},
				success: function (data) {
					if (data.result === '1') {
						var ok_num = cbResultSuccess(data.message, undo, spamed);

						if (undo === true) {
							UP.statusMsg.clear();
						} else {
							var msgOK = ['Установлена метка «СПАМ» на ', ok_num, UP.utils.getCase((ok_num), ' файлов', ' файла', ' файл'), undoLink].join('');
							UP.statusMsg.show(msgOK, UP.env.msgInfo, false);
						}
					} else {
						UP.statusMsg.show(data.message, UP.env.msgError, true);
					}
				}
			});

			return false;
		},

		//
		unmarkItemSpam: function (undo) {
			var items = getCheckedItemsID(),
				actions = ACTION_ADMIN_UNMARK_AS_SPAM_FILE,
				undoLink = '<span class="as_js_link" onclick="UP.admin.unmarkItemSpam(true);">Отменить</span>';

			if (undo === true) {
				items = getAffectedItemsID(spamed);
				actions = ACTION_ADMIN_MARK_AS_SPAM_FILE;
				undoLink = '';
			}

			if (!items) {
				return false;
			}

			wait('unmarkSpamTimer', waitTimeout);

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: actions, t_ids: items },
				dataType: 'json',
				beforeSend: function () {
					$(':checkbox, :button').attr('disabled', 'disabled');
				},
				complete: function () {
					onComplete('unmarkSpamTimer');
				},
				error: 	function () {
					onError();
				},
				success: function (data) {
					if (data.result === '1') {
						var ok_num = cbResultSuccess(data.message, undo, spamed);

						if (undo === true) {
							UP.statusMsg.clear();
						} else {
							var msgOK = ['Снята метка «СПАМ» c ', ok_num, UP.utils.getCase((ok_num), ' файлов', ' файлов', ' файла'), undoLink].join('');
							UP.statusMsg.show(msgOK, UP.env.msgInfo, false);
						}
					} else {
						UP.statusMsg.show(data.message, UP.env.msgError, true);
					}
				}
			});

			return false;
		},

//
		markItemAdult: function (undo) {
			var items = getCheckedItemsID(),
				actions = ACTION_ADMIN_MARK_AS_ADULT_FILE,
				undoLink = '<span class="as_js_link" onclick="UP.admin.markItemAdult(true);">Отменить</span>';

			if (undo === true) {
				items = getAffectedItemsID(adulted);
				actions = ACTION_ADMIN_UNMARK_AS_ADULT_FILE;
				undoLink = '';
			}

			if (!items) {
				return false;
			}

			wait('markAdultTimer', waitTimeout);

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: actions, t_ids: items },
				dataType: 'json',
				beforeSend: function () {
					$(':checkbox, :button').attr('disabled', 'disabled'); // disable all input
				},
				complete: function () {
					onComplete('markAdultTimer');
				},
				error: 	function () {
					onError();
				},
				success: function (data) {
					if (data.result === '1') {
						var ok_num = cbResultSuccess(data.message, undo, adulted);

						if (undo === true) {
							UP.statusMsg.clear();
						} else {
							var msgOK = ['Установлена метка «XXX» на ', ok_num, UP.utils.getCase((ok_num), ' файлов', ' файла', ' файл'), undoLink].join('');
							UP.statusMsg.show(msgOK, UP.env.msgInfo, false);
						}
					} else {
						UP.statusMsg.show(data.message, UP.env.msgError, true);
					}
				}
			});

			return false;
		},

		//
		unmarkItemAdult: function (undo) {
			var items = getCheckedItemsID(),
				actions = ACTION_ADMIN_UNMARK_AS_ADULT_FILE,
				undoLink = '<span class="as_js_link" onclick="UP.admin.unmarkItemAdult(true);">Отменить</span>';

			if (undo === true) {
				items = getAffectedItemsID(adulted);
				actions = ACTION_ADMIN_MARK_AS_ADULT_FILE;
				undoLink = '';
			}

			if (!items) {
				return false;
			}

			wait('unmarkAdultTimer', waitTimeout);

			$.ajax({
				type: 	'POST',
				url: 	UP.env.ajaxBackend,
				data: 	{ t_action: actions, t_ids: items },
				dataType: 'json',
				beforeSend: function () {
					$(':checkbox, :button').attr('disabled', 'disabled');
				},
				complete: function () {
					onComplete('unmarkAdultTimer');
				},
				error: 	function () {
					onError();
				},
				success: function (data) {
					if (data.result === '1') {
						var ok_num = cbResultSuccess(data.message, undo, adulted);

						if (undo === true) {
							UP.statusMsg.clear();
						} else {
							var msgOK = ['Снята метка «XXX» c ', ok_num, UP.utils.getCase((ok_num), ' файлов', ' файлов', ' файла'), undoLink].join('');
							UP.statusMsg.show(msgOK, UP.env.msgInfo, false);
						}
					} else {
						UP.statusMsg.show(data.message, UP.env.msgError, true);
					}
				}
			});

			return false;
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

				/*if (state == true) {
					$('#row_item_').addClass('selected');
				} else {
					$('#row_item_').removeClass('selected').removeClass('canceled');
				}*/

				n = showNumCheckedCB();
				$(':button').attr("disabled", (n < 1 ? 'disabled' : ''));
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
						$('#row_item_' + id).removeClass('selected').removeClass('canceled');
					}
				}
			});
		}

	};
}();
