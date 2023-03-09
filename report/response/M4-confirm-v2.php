<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "การตอบกลับ - งานรับนักเรียน";
	$header_desc = "นักเรียนเดิม: ยืนยันสิทธิ์";
	$home_menu = "response";

	$forceExternalBrowser = true;
	$permitted = has_perm("admission");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			div.group.s div.list div.table tr td:nth-child(1),
			div.group.s div.list div.table tr td:nth-child(2),
			div.group.s div.list div.table tr td:nth-child(5),
			div.group.s div.list div.table tr td:nth-child(6),
			div.group.s div.list div.table tr td:nth-child(7) { text-align: center; }
			main .form { padding: 10px; }
			main .form select option:checked { font-weight: bold; }
		</style>
		<link rel="stylesheet" href="/resource/css/extend/mod-directory.css">
		<script type="text/javascript">
			$(document).ready(function() {
				$(document).on("keypress", function(e) {
					let prik = e.which || e.keyCode, ckeyp = String.fromCharCode(prik) || e.key || e.code, isCrtling = e.ctrlKey, isShifting = e.shiftKey, isAlting = e.altKey;
					if (prik == 47 && !$(":focus").is(sS.slt.v)) {
						if (e.preventDefault) e.preventDefault();
						$(sS.slt.v).focus();
					}
				});
				$(sS.slt.v).on("input change", sS.find);
				ajax("/e/enroll/resource/php/api", {type: "app", act: "loadFilterOpt", param: "cnf"}).then(function(dat) {
					if (dat && dat.length) dat.forEach(eo => $('main .form [name="group"]').append('<option value="'+eo.ref+'">'+eo.title+'</option>') );
				});
				// Add event listener
				$('div.group.f .form').on("change", () => { sD.load("filter"); });
				// Initialize
				sD.load();
			});
			var sv = { sq: null };
			const sS = {
				slt: {
					v: 'div.group.f div.search div.live input[name="search"]'
				}, find: function() {
					var search_for = document.querySelector(sS.slt.v).value.trim();
					sv.sq = (/^[^%_+]{1,75}$/.test(search_for) ? search_for : null);
					sD.load("search");
				}, clear: function() {
					document.querySelector(sS.slt.v).value = "";
				}
			};
			const sD = {
				slt: "",
				load: function(change="filter") {
					document.querySelector("div.group.s div.list").disabled = true;
					$.post("/e/enroll/resource/php/response?list=cnf&change="+change+(sv.sq!=null?("&q="+encodeURIComponent(sv.sq)):""), {
						filter: {class: $('main .form [name="class"]').val(), group: $('main .form [name="group"]').val()},
						page: sF.ctrl.page.current,
						show: sF.ctrl.page.disp,
						sortBy: sF.ctrl.sort.col,
						sortOrder: (sF.ctrl.sort.order ? "ASC" : "DESC")
					}, function(res, hsc) {
						var dat = JSON.parse(res);
						if (dat.success) {
							sF.ctrl = dat.intl;
							sF.render(dat.info);
						} else document.querySelector(sF.slt).innerHTML = '<div class="msg"><center class="message red"><?=$_COOKIE['set_lang']=="th"?"เกิดปัญหาระหว่างการโหลดรายชื่อ":"Error while trying to fetch user list."?></center></div>';
						document.querySelector("div.group.s div.list").disabled = false;
					});
				}
			};
			const sF = {
				slt: "div.group.s div.list",
				render: function(data) {
					if (data.users.length == 0) document.querySelector(sF.slt).innerHTML = '<div class="msg"><center class="message gray"><?=$_COOKIE['set_lang']=="th"?"ไม่มีชื่อในหมวดหมู่นี้":"There are no user in this category."?></center></div>';
					else {
						var htmlPL = '<div class="table"><table>';
						if (typeof data.column !== "undefined" && data.column.length > 0) {
							htmlPL += "<thead><tr>";
							sF.ctrl.colList = [];
							data.column.forEach(function(ec) {
								let newHTML = "<th";
								if (ec.sortable) newHTML += ' onClick="sF.list.sort(\''+ec.ref+'\')"';
								newHTML += '>'+ec.name+'</th>';
								htmlPL += newHTML;
								sF.ctrl.colList.push(ec.ref);
							});
							htmlPL += "</tr></thead>";
						} htmlPL += "<tbody>";
						data.users.forEach(function(eu) {
							let newHTML = "<tr>";
							sF.ctrl.colList.forEach(function(bc) {
								newHTML += "<td>";
								if (typeof eu[bc].link === "string") newHTML += '<a href="'+eu[bc].link+'" onClick="return intercept(this,event)" draggable="false">';
								newHTML += eu[bc].val;
								if (typeof eu[bc].link === "string") newHTML += '</a>';
								newHTML += "</td>";
							});
							htmlPL += newHTML+"</tr>";
						});
						htmlPL += '</tbody></table></div><div class="flow">'+'<div class="perpage"><span>Show </span><select onChange="sF.list.disp(this.value)">';
						[10,20,25,30,50].forEach(function(pa) {
							let as = pa.toString(), defppv = (pa == sF.ctrl.page.disp);
							htmlPL += '<option value="'+as+'" '+(defppv ?"selected":"")+'>'+as+'</option>';
						}); htmlPL += '</select></div><div class="pages"><a onClick="sF.list.page(\'first\')" data-title="First Page" href="javascript:void(0)" draggable="false"><i class="material-icons">first_page</i></a><a onClick="sF.list.page(\'prev\')" data-title="Previous Page" href="javascript:void(0)" draggable="false"><i class="material-icons">chevron_left</i></a><select onChange="sF.list.page(this.value)">';
						for (let page = 1; page <= sF.ctrl.page.max; page++) {
							let p = page.toString(), defpgn = (page == sF.ctrl.page.current);
							htmlPL += '<option value="'+p+'" '+(defpgn ?"selected":"")+'>'+p+'</option>';
						} htmlPL += '</select><a onClick="sF.list.page(\'next\')" data-title="Next Page" href="javascript:void(0)" draggable="false"><i class="material-icons">chevron_right</i></a><a onClick="sF.list.page(\'last\')" data-title="Last Page" href="javascript:void(0)" draggable="false"><i class="material-icons">last_page</i></a></div></div>';
						// Display HTML
						document.querySelector(sF.slt).innerHTML = htmlPL;
						// Page controller
						var pageBtn = [
							$("div.group.s div.list div.flow div.pages a:nth-of-type(1)"),
							$("div.group.s div.list div.flow div.pages a:nth-of-type(2)"),
							$("div.group.s div.list div.flow div.pages a:nth-of-type(3)"),
							$("div.group.s div.list div.flow div.pages a:nth-of-type(4)"),
							document.querySelector("div.group.s div.list div.flow div.pages select")
						];
						if (sF.ctrl.page.current == 1) { pageBtn[0].attr("disabled", ""); pageBtn[1].attr("disabled", ""); }
						else { pageBtn[0].removeAttr("disabled"); pageBtn[1].removeAttr("disabled"); }
						if (sF.ctrl.page.current == sF.ctrl.page.max) { pageBtn[2].attr("disabled", ""); pageBtn[3].attr("disabled", ""); }
						else { pageBtn[2].removeAttr("disabled"); pageBtn[3].removeAttr("disabled"); }
						pageBtn[4].disabled = (sF.ctrl.page.max == 1);
					}
				}, ctrl: {
					page: { current: 1, max: 1, disp: 20 },
					sort: { col: "A", order: 1 }
				}, list: {
					sort: function(col) {
						if (col != sF.ctrl.sort.col) {
							sF.ctrl.sort.col = col;
							sD.load("sortBy");
						} else {
							sF.ctrl.sort.order = (sF.ctrl.sort.order ? 0 : 1);
							sD.load("sortOrder");
						}
					}, page: function(pgn) {
						switch (pgn) {
							case "first": sF.ctrl.page.current = 1; break;
							case "prev": sF.ctrl.page.current -= 1; break;
							case "next": sF.ctrl.page.current += 1; break;
							case "last": sF.ctrl.page.current = sF.ctrl.page.max; break;
							default: sF.ctrl.page.current = pgn; break;
						} sD.load("page");
					}, disp: function(amt) {
						sF.ctrl.page.disp = amt;
						sD.load("show");
					}
				}
			};
			function intercept(m, e) {
				(function() {
					// e.preventDefault();
					if (e.ctrlKey) window.open(m.href);
					else app.ui.lightbox.open("mid", {title: "ไฟล์หลักฐานของ \""+m.parentNode.parentNode.children[1].innerText+"\"", allowclose: true, autoclose: 300000,
						html: '<iframe src="'+m.href+'" style="width:90vw;height:80vh;border:none">Loading...</iframe>'
					});
				}()); return false;
			};
		</script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if (!$permitted) echo '<iframe src="/error/901">901: No Permission</iframe>'; else { ?>
			<div class="container">
				<div class="wrapper">
					<div class="group f">
						<div class="search">
							<div class="live filter">
								<!input name="search" type="hidden">
								<input type="search" name="search" placeholder="Search ... (ค้นหา)">
							</div>
						</div>
						<div class="dir slider">
							<div class="form">
								<select name="class">
									<option value="*" selected>*นักเรียนที่มีสิทธิ์ทุกสถานะ*</option>
									<option value="ans">นักเรียนที่ยื่นคำร้อง</option>
									<option value="una">ไม่มีการยื่นคำร้อง</option>
								</select>
								<select name="group">
									<option value="*" selected>*ทุกกลุ่มการเรียน*</option>
								</select>
							</div>
						</div>
					</div>
					<div class="group s">
						<div class="list">
							
						</div>
					</div>
				</div>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>