<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "การตอบกลับ - งานรับนักเรียน";
	$header["desc"] = "นักเรียนเดิม: เปลี่ยนแปลงสิทธิ์";
	$home_menu = "response";
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] main .students .table { border-radius: .3rem; }
	app[name=main] main .history hr {
		margin: 2.5px 0;
		width: 75%;
	}
</style>
<script type="text/javascript">
	const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
	$(document).ready(function() {
		page.init();
	});
	const page = (function(d) {
		const cv = {
			API_URL: AppConfig.APIbase + "enroll/v1/mod-response",
			option: chose => chose ? "ยืนยันสิทธิ์" : "สละสิทธิ์"
		};
		var sv = {inited: false, reasons: {}};
		var initialize = function() {
			if (sv.inited) return;
			listStd();
			$("app[name=main] input[name=filter]").on("input change", filter);
			$("app[name=main] select[name=order]").on("change", listStd);
			sv.inited = true;
		};
		var listStd = function() {
			var order = $("app[name=main] select[name=order]").val();
			if (order == "name") order += "a" + app.settings["lang"].toLowerCase();
			app.Util.ajax(cv.API_URL, {act: "list", cmd: "switch", param: order}).then(function(dat) {
				if (!dat) return;
				else if (!dat.length) return app.UI.notify(1, app.UI.language.getMessage("no-std-avail"));
				var list = '', tableTemplate = $("app[name=main] .tableTemplate").html();
				dat.forEach(es => {
					list += '<li class="accordian" data-student="' + es.ID + '">'+
						'<div class="head" onClick="page.show(this)"><div class="css-flex css-flex-split css-flex-gap-5">'+
							`<span class="txtoe">${es.fullname[app.settings["lang"]]}</span><span>${es.ID}</span>`.toString()+
						'</div></div><div class="body" data-loaded="false"><div class="history">'+
							tableTemplate+
						'</div></div>'+
					'</li>';
				}); $("app[name=main] .students").html(list);
				app.UI.language.load();
			});
		},
		show = function(me) {
			var infobox = $(me).next();
			me = $(me.parentNode);
			if (me.is("[open]")) {
				me.removeAttr("open");
				me.children().last().animate({height: 0}, 500);
			} else if (infobox.attr("data-loaded") == "false") {
				var Student_ID = me.attr("data-student");
				app.Util.ajax(cv.API_URL, {act: "get", cmd: "switch", param: {data: "history", ID: Student_ID}}).then(function(dat) {
					if (!dat) return;
					var decrement = dat.length, table = [], options;
					dat.forEach(er => {
						options = "";
						if (er.hasMemorandum) {
							if (er.reference in sv.reasons) options = sv.reasons[er.reference];
							else options = '<div class="center"><a role="button" class="blue bare small" onClick="page.getNotes(this)" href="javascript:"><i class="material-icons">description</i> <span class="text">แสดงบันทึก</span></a></div>';
						}
						table.push('<td class="center">' + (decrement--).toString() + '</td>'+
							'<td>' + er.timestamp + '</td>'+
							'<td class="center">' + cv.option(!er.newChoice) + '</td>'+
							'<td class="center">' + cv.option(er.newChoice) + '</td>'+
							'<td data-reference="' + er.reference + '">' + options + '</td>'
						);
					}); $("app[name=main] .students [data-student=" + Student_ID + "] tbody").html('<tr>' + table.join('</tr><tr>') + '</tr>');
					app.UI.language.load();
					app.UI.refineElements();
					infobox.attr("data-loaded", "true");
					show(me.children()[0]);
				});
			} else {
				me.siblings(".accordian[open]").removeAttr("open").children().last().animate({height: 0}, 500);
				me.attr("open", "");
				infobox.animate({height: infobox.children().first().outerHeight()}, 500, $.bez([0.65, 0, 0.35, 1]));
			}
		},
		getNotes = function(me) {
			var box = $(me.parentNode.parentNode),
				infobox = box.parent().parent().parent().parent().parent().parent();
			me = $(me);
			var reference = box.attr("data-reference");
			if (reference in sv.reasons) box.html(sv.reasons[reference]);
			else {
				me.attr("disabled", "");
				app.Util.ajax(cv.API_URL, {act: "get", cmd: "switch", param: {data: "memorandum", ID: reference}}).then(function(dat) {
					if (!dat) return me.removeAttr("disabled");
					box.html(dat);
					sv.reasons[reference] = dat;
					infobox.animate({height: infobox.children().first().outerHeight()}, 250, $.bez([0.65, 0, 0.35, 1]));
				});
			}
		},
		filter = function() {
			var query = $("app[name=main] input[name=filter]").val();
			w3.filterHTML("app[name=main] .students", "li", query);
		};
		return {
			init: initialize,
			show, getNotes
		};
	}(document));
</script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/w3.min.js"></script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/jQuery/bez.min.js"></script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main>
	<section class="container">
		<h2>ประวัติการเปลี่ยนแปลงสิทธิ์</h2>
		<div class="form form-bs"><div class="group split">
			<div class="group">
				<label><i class="material-icons">filter_list</i></label>
				<input type="search" name="filter" placeholder="ค้นหา..." />
			</div>
			<div class="group">
				<label class="ref-00001">เรียงตาม</label>
				<select name="order">
					<option value="time">เวลาล่าสุด</option>
					<option value="name">ชื่อ-สกุล</option>
				</select>
			</div>
		</div></div>
		<ul class="students accordian-group"></ul>
		<div class="tableTemplate" hidden>
			<div class="table static responsive striped"><table><thead class="center">
				<tr>
					<th rowspan="2">ลำดับ</th>
					<th rowspan="2">ประทับเวลา</th>
					<th colspan="2">การเปลี่ยนแปลงสิทธิ์</th>
					<th rowspan="2">เหตุผล</th>
				</tr>
				<tr>
					<th>จาก</th><th>เป็น</th>
				</tr>
			</thead><tbody></tbody></table></div>
		</div>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials(side_panel: "enroll");
	$APP_PAGE -> print -> footer();
?>