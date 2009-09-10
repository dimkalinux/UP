jQuery(function() {
	$("#nicemenu img.arrow").mousedown(function () {
		$("span.head_menu").removeClass('active');
		submenu = $(this).parent().parent().find("div.sub_menu");

		if(submenu.css('display') === 'block') {
			$(this).parent().removeClass("active");
			submenu.hide();
			$(this).attr('src','/img/menu/arrow_hover.png');
		} else {
			$(this).parent().addClass("active");
			submenu.show();
			$(this).attr('src','/img/menu/arrow_select.png');
		}

		$("div.sub_menu:visible").not(submenu).hide();
		$("#nicemenu img.arrow").not(this).attr('src','/img/menu/arrow.png');

	})
	.mouseover(function (){ $(this).attr('src','/img/menu/arrow_hover.png'); })
	.mouseout(function (){
		if ($(this).parent().parent().find("div.sub_menu").css('display') !== 'block') {
			$(this).attr('src','/img/menu/arrow.png');
		} else {
			$(this).attr('src','/img/menu/arrow_select.png');
		}
	});

	$("#nicemenu span.head_menu")
		.mouseover(function () { $(this).addClass('over')})
		.mouseout(function () { $(this).removeClass('over') });

	$("#nicemenu div.sub_menu")
		.mouseover(function (){ $(this).show(); })
		.blur(function (){
			$(this).hide();
			$("span.head_menu").removeClass('active');
	});

	$(document).mousedown(function(event) {
			var target = $(event.target);

			if (target.parents("#nicemenu").length === 0) {
				$("#nicemenu span.head_menu").removeClass('active');
				$("#nicemenu div.sub_menu").hide();
				$("#nicemenu img.arrow").attr('src','/img/menu/arrow.png');
			}
	});
});
