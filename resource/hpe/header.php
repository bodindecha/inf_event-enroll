<header>
    <section class="slider hscroll sscroll"><div class="ocs">
		<div class="head-item menu">
			<a onClick="app.ui.toggle.navtab()" href="javascript:void(0)" opened="<?php echo ($_COOKIE['sui_open-nt']??"false"); ?>"><div>
				<span class="bar"></span>
				<span class="bar"></span>
				<span class="bar"></span>
			</div></a>
		</div>
		<div class="head-item logo contain-img text">
			<a href="/e/enroll/" draggable="true"><img src="/resource/images/logo-5.png" data-dark="false"><span>งานรับนักเรียน</span></a>
		</div>
		<div class="head-item text">
			<?php if (has_perm("admission")) { ?>
				<a href="/e/enroll/report/<?=!empty($home_menu??"")?"#menu=$home_menu":""?>"><span>เมนูหลัก</span></a>
				<a href="/e/enroll/report/print-form"><span>พิมพ์เอกสาร</span></a>
				<!--a href="/e/enroll/report/response/M4-present"><span>รายงานตัว</span></a>
				<a href="/e/enroll/report/response/M4-change"><span>เปลี่ยนกลุ่มการเรียน</span></a>
				<a href="/e/enroll/report/response/M4-confirm"><span>ยืนยันสิทธิ์</span></a>
				<a href="/e/enroll/report/response/new-student"><span>นักเรียนใหม่</span></a-->
				<a href="/e/enroll/report/delete-response"><span>ลบการตอบกลับ</span></a>
			<?php } else { ?>
				<a href="/e/enroll/new"><span>นักเรียนใหม่</span></a>
				<a <?=isset($_SESSION['auth']) ? 'href="/e/enroll/M4/"' : 'onClick="sys.auth.orize(\'e%2Fenroll%2FM4%2F\')" href="javascript:void(0)"'?>><span>นักเรียนเดิม<?=isset($_SESSION['auth'])?"":" (เข้าสู่ระบบ)"?></span></a>
			<?php } ?>
		</div>
	</div></section>
    <section class="slider hscroll sscroll"><div class="ocs">
		<div class="head-item text">
			<?php if (has_perm("admission") || (isset($_SESSION['auth']['user']) && $_SESSION['auth']['type']=="s")) { ?>
				<a onClick="sys.auth.out()" href="javascript:void(0)"><span>ออกจากระบบ</span></a>
			<?php } else { ?>
				<a onClick="sys.auth.orize('e%2Fenroll%2Freport%2F')" href="javascript:void(0)"><span>เจ้าหน้าที่</span></a>
			<?php } ?>
		</div>
		<!--div class="head-item lang"><select name="hl">
			<option>th</option>
			<option>en</option>
		</select></div-->
		<div class="head-item clrt contain-img">
			<a onClick="app.ui.change.theme('dark')" href="javascript:void(0)"><i class="material-icons">brightness_6</i></a>
		</div>
	</div></section>
</header>