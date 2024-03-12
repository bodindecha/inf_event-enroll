<header>
	<section class="slider hscroll sscroll">
		<div class="ocs">
			<div class="head-item menu">
				<a onClick="app.ui.toggle.navtab()" href="javascript:" opened="<?php echo ($_COOKIE['sui_open-nt'] ?? "false"); ?>">
					<div>
						<span class="bar"></span>
						<span class="bar"></span>
						<span class="bar"></span>
					</div>
				</a>
			</div>
			<div class="head-item logo contain-img text">
				<a href="/e/enroll/" draggable="true"><img src="/resource/images/logo/blue.png" data-dark="false"><span>งานรับนักเรียน</span></a>
			</div>
			<?php if (has_perm("admission")) { ?>
				<div class="head-item text icon">
					<a href="/e/enroll/report/<?=""/*!empty($home_menu ?? "")?"#menu=$home_menu":""*/?>"><i class="material-icons">dashboard</i><span>เมนูหลัก</span></a>
				</div>
				<div class="head-item super text icon">
					<div class="menu">
						<a data-onClick="app.ui.toggle.hmenu(this)" href="javascript:"><i class="material-icons">assessment</i><span>การตอบกลับ</span></a>
						<ul class="dropdown">
							<a class="title"><span>นักเรียนเดิม</span></a>
							<a href="/e/enroll/report/response/M4-present-v2"><span>รายงานตัว</span></a>
							<a href="/e/enroll/report/response/M4-change-v2"><span>เปลี่ยนกลุ่มการเรียน</span></a>
							<a href="/e/enroll/report/response/M4-confirm-v2"><span>ยืนยันสิทธิ์</span></a>
							<a href="/e/enroll/report/response/M4-switch"><span>เปลี่ยนแปลงสิทธิ์</span></a>
							<a class="title"><span>นักเรียนใหม่</span></a>
							<a href="/e/enroll/report/response/new-student-v2"><span>รายงานตัว</span></a>
						</ul>
					</div>
					<div class="menu">
						<a data-onClick="app.ui.toggle.hmenu(this)" href="javascript:"><i class="material-icons">receipt</i><span>จัดการข้อมูล</span></a>
						<ul class="dropdown">
							<a href="/e/enroll/report/print-form"><i class="material-icons">print</i><span>พิมพ์เอกสาร</span></a>
							<a href="/e/enroll/report/delete-response"><i class="material-icons">delete</i><span>ลบการตอบกลับ</span></a>
						</ul>
					</div>
					<div class="menu">
						<a data-onClick="app.ui.toggle.hmenu(this)" href="javascript:"><i class="material-icons">settings</i><span>กระทำการ</span></a>
						<ul class="dropdown">
							<a href="/e/enroll/report/time-control"><i class="material-icons">date_range</i><span>ตั้งค่าเวลา</span></a>
							<a href="/e/enroll/report/edit-direction"><i class="material-icons">web</i><span>แก้ไขคำชี้แจง</span></a>
							<hr>
							<a href="/e/enroll/report/import-data"><i class="material-icons">unarchive</i><span>นำเข้าข้อมูล</span></a>
							<a href="/e/enroll/report/export-result"><i class="material-icons">archive</i><span>นำออกข้อมูล</span></a>
							<a href="/e/enroll/report/download-doc"><i class="material-icons">download</i><span>รวมหลักฐาน</span></a>
							<?php if ($_SESSION["auth"]["level"]>=75) { ?>
							<hr>
							<a href="/e/enroll/report/file-manager"><i class="material-icons">source</i><span>จัดการเอกสารแม่แบบ</span></a>
							<?php } ?>
						</ul>
					</div>
				</div>
			<?php } else { ?>
				<div class="head-item text">
					<a href="/e/enroll/new"><span>นักเรียนใหม่</span></a>
					<a <?= isset($_SESSION["auth"]) ? 'href="/e/enroll/M4/"' : 'onClick="sys.auth.orize(\'e%2Fenroll%2FM4%2F\')" href="javascript:"' ?>><span>นักเรียนเดิม<?= isset($_SESSION["auth"]) ? "" : " (เข้าสู่ระบบ)" ?></span></a>
				</div>
			<?php } ?>
		</div>
	</section>
	<section class="slider hscroll sscroll">
		<div class="ocs">
			<div class="head-item text">
				<?php if (has_perm("admission") || (isset($_SESSION["auth"]["user"]) && $_SESSION["auth"]["type"] == "s")) { ?>
					<a onClick="sys.auth.out()" href="javascript:"><span>ออกจากระบบ</span></a>
				<?php } else { ?>
					<a onClick="sys.auth.orize('e%2Fenroll%2Freport%2F')" href="javascript:"><span>เจ้าหน้าที่</span></a>
				<?php } ?>
			</div>
			<!--div class="head-item lang"><select name="hl">
			<option>th</option>
			<option>en</option>
		</select></div-->
			<div class="head-item clrt icon">
				<a onClick="app.ui.change.theme('dark')" href="javascript:"><i class="material-icons">brightness_6</i></a>
			</div>
		</div>
	</section>
</header>