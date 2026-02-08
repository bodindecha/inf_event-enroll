<center><p>
	<a role="button" class="gray hollow dont-ripple" style="font-size: 12.5px;"
		data-href="/go?url=mailto%3Anoc%40bodin.ac.th%3Fsubject%3Dเว็บ%20INF-BD%3A%20งานรับนักเรียน"
		data-href="/go?url=https%3A%2F%2Fmail.google.com%2Fa%2Fbodin.ac.th%2F%3Ffs%3D1%26tf%3Dcm%26to%3Dnoc%40bodin.ac.th%26su%3Dเว็บ%20INF-BD%3A%20งานรับนักเรียน"
		href="/v2/ticket/create#service=ADM"
	>ติดต่อสอบถาม/แจ้งปัญหาการใช้งาน</a>
</p></center>
<section class="nav">
	<style type="text/css">
		/* ALL version */
		aside.navigator_tab section.nav > ul { margin: 0px 0px 10px; padding-left: 5px !important; }
		aside.navigator_tab section.nav ul {
			padding-left: 25px;
			list-style-type: disc; white-space: nowrap;
		}
		aside.navigator_tab section.nav ul > li.this-page, aside.navigator_tab section.nav ul > li.this-page a {
			color: var(--clr-bs-indigo); font-weight: bold;
			pointer-events: none;
		}
		/* V1 */
		aside.navigator_tab section.nav ul > li {
			padding-right: 10px;
			width: fit-content; height: 20px; line-height: 20px;
		}
		aside.navigator_tab section.nav ul > li.sub-detail { list-style-type: none; }
		aside.navigator_tab section.nav ul > li.seperator {
			width: 80%; height: 10px;
			background-image: linear-gradient(to bottom, transparent 0%, transparent 42.5%, var(--fade-black-7) 42.5%, var(--fade-black-7) 57.5%, transparent 57.5%, transparent 100%);
			list-style-type: none;
		}
		/* V2 */
		aside.navigator_tab section.nav ul > li::marker { transform: translateX(5px); }
		aside.navigator_tab section.nav ul > li.epdb {
			min-height: 20px; height: auto;
			list-style-type: none;
		}
		aside.navigator_tab section.nav ul > li.epdb > details summary { transform: translateX(-10px); }
	</style>
	<ul>
		<div class="group">
			<label>เมนู</label>
			<ul>
				<li><a href="/e/enroll/"><span>หน้าหลัก</span></a></li>
				<?php if (isset($_SESSION["auth"]["user"]) && $_SESSION["auth"]["type"]=="s") { ?>
					<li class="epdb"><details open>
						<summary>นักเรียนเดิม</summary>
						<ul>
							<li><a href="/e/enroll/M4/present"><span>รายงานตัว</span></a></li>
							<li><a href="/e/enroll/M4/change"><span>เปลี่ยนกลุ่มการเรียน</span></a></li>
							<li><a href="/e/enroll/M4/confirm"><span>ยืนยันสิทธิ์</span></a></li>
						</ul>
					</details></li>
					<!-- <li class="seperator">&nbsp;</li> -->
					<li><a onClick="sys.auth.out()" href="javascript:"><span>ออกจากระบบ</span></a></li>
				<?php } else if (!isset($_SESSION["auth"]["user"])) { ?>
					<li><a onClick="sys.auth.orize('e%2Fenroll%2FM4%2F')" href="javascript:"><span>นักเรียนเดิม</span></a></li>
					<li><a href="/e/enroll/new"><span>นักเรียนใหม่</span></a></li>
				<?php } ?>
			</ul>
		</div>
		<?php if (!isset($_SESSION["auth"]["user"]) || has_perm("admission")) { ?>
		<div class="group">
			<label>เจ้าหน้าที่</label>
			<ul><?php if (has_perm("admission")) { ?>
				<li><a href="/e/enroll/report/"><span>เมนูหลัก</span></a></li>
				<li class="epdb"><details open>
					<summary>รายงานการตอบกลับ</summary>
					<ul>
						<li class="epdb"><details open>
							<summary>นักเรียนเดิม</summary>
							<ul>
								<li><a href="/e/enroll/report/response/M4-present-v2"><span>รายงานตัว</span></a></li>
								<li><a href="/e/enroll/report/response/M4-change-v2"><span>เปลี่ยนกลุ่มการเรียน</span></a></li>
								<li><a href="/e/enroll/report/response/M4-confirm-v2"><span>ยืนยันสิทธิ์</span></a></li>
								<li><a href="/e/enroll/report/response/M4-switch"><span>เปลี่ยนแปลงสิทธิ์</span></a></li>
							</ul>
						</details></li>
						<li class="epdb"><details open>
							<summary>นักเรียนใหม่</summary>
							<ul>
								<li><a href="/e/enroll/report/response/new-student-v2"><span>รายงานตัว</span></a></li>
							</ul>
						</details></li>
					</ul>
				</details></li>
				<li class="epdb"><details open>
					<summary>จัดการข้อมูล</summary>
					<ul>
						<li><a href="/e/enroll/report/print-form"><span>พิมพ์เอกสารใบมอบตัว</span></a></li>
						<li><a href="/e/enroll/report/delete-response"><span>ลบรายการการตอบกลับ</span></a></li>
					</ul>
				</details></li>
				<li class="epdb"><details open>
					<summary>กระทำการ</summary>
					<ul>
						<li><a href="/e/enroll/report/time-control"><span>ตั้งค่าเวลา</span></a></li>
						<li><a href="/e/enroll/report/edit-direction"><span>แก้ไขคำชี้แจง</span></a></li>
						<li class="seperator">&nbsp;</li>
						<li><a href="/e/enroll/report/import-data"><span>นำเข้าข้อมูล</span></a></li>
						<li><a href="/e/enroll/report/export-result"><span>นำออกข้อมูล</span></a></li>
						<li><a href="/e/enroll/report/download-doc"><span>รวมหลักฐาน</span></a></li>
						<?php if ($_SESSION["auth"]["level"]>=75) { ?>
						<li class="seperator">&nbsp;</li>
						<li><a href="/e/enroll/report/file-manager"><span>จัดการเอกสารแม่แบบ</span></a></li>
						<?php } ?>
					</ul>
				</details></li>
				<li><a onClick="sys.auth.out()" href="javascript:"><span>ออกจากระบบ</span></a></li>
			<?php } else { ?>
				<li><a onClick="sys.auth.orize('e%2Fenroll%2Freport%2F')" href="javascript:"><span>เข้าสู่ระบบ</span></a></li>
			<?php } ?>
			</ul>
		</div>
		<?php } ?>
		<div class="group">
			<label>กลับสู่</label>
			<ul>
				<li><a href="/"><span>เว็บสารสนเทศ</span></a></li>
				<li><a href="/go?url=http%3A%2F%2Freg.bodin.ac.th"><span>เว็บทะเบียน</span></a></li>
			</ul>
		</div>
	</ul>
</section>