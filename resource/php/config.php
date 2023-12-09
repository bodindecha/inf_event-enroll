<?php
	if (!isset($_SESSION)) session_start();
	if (!isset($dirPWroot)) $dirPWroot = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/")-1);

	/* Constants */
	function arrDump($array) { return json_encode($array, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT); }
	$CV_groupAdm = array(
		array(
			"ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ในเขตพื้นที่บริการ
			"ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ในเขตพื้นที่บริการ (คุณสมบัติไม่ครบ) [deprecated]
			"ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // นอกเขตพื้นที่บริการ
			"ห้องเรียนพิเศษคณิตศาสตร์", // ชั้นมัธยมศึกษาปีที่ 1
			"ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม ตามแนวทาง สสวท. และ สอวน.", // ชั้นมัธยมศึกษาปีที่ 1
			"ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม", // ชั้นมัธยมศึกษาปีที่ 4
			"ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 4
			"โครงการห้องเรียน พสวท. (สู่ความเป็นเลิศ)" // ชั้นมัธยมศึกษาปีที่ 4
		), array(
			"ห้องเรียนพิเศษคณิตศาสตร์", // ชั้นมัธยมศึกษาปีที่ 1
			"ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม ตามแนวทาง สสวท. และ สอวน.", // ชั้นมัธยมศึกษาปีที่ 1
			"ห้องเรียนพิเศษ English Program", // ชั้นมัธยมศึกษาปีที่ 1
			"ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม", // ชั้นมัธยมศึกษาปีที่ 4
			"โครงการห้องเรียน พสวท. (สู่ความเป็นเลิศ)", // ชั้นมัธยมศึกษาปีที่ 4
			"ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ในเขตพื้นที่บริการ
			"ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // นอกเขตพื้นที่บริการ
			"ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ความสามารถพิเศษ
			"ห้องเรียนทั่วไป" // ชั้นมัธยมศึกษาปีที่ 4
		)
	)[1]; $CV_groupAdmShort = array(
		array(
			"ม.1 ทั่วไป ในเขต",
			"⨯[ม.1 ทั่วไป ในเขต-ไม่ครบ]⨯",
			"ม.1 ทั่วไป นอกเขต",
			"ม.1 พิเศษคณิตศาสตร์",
			"ม.1 พิเศษวิทยาศาสตร์ฯ",
			"ม.4 พิเศษวิทย์-คณิตฯ",
			"ม.4 ทั่วไป",
			"ม.4 พสวท."
		), array(
			"ม.1 พิเศษคณิตศาสตร์",
			"ม.1 พิเศษวิทยาศาสตร์ฯ",
			"ม.1 พิเศษ EP",
			"ม.4 พิเศษวิทย์-คณิตฯ",
			"ม.4 พสวท.",
			"ม.1 ทั่วไป ในเขต",
			"ม.1 ทั่วไป นอกเขต",
			"ม.1 ทั่วไป ความสามารถ",
			"ม.4 ทั่วไป"
		)
	)[1];

	/* App function */
	function inTimerange($start, $stop) {
		$now = time();
		return (strtotime($start) <= $now && $now <= strtotime($stop));
	}
	function inDaterange($start, $stop) {
		$now = time();
		return (strtotime("$start 00:00:00") <= $now && $now <= strtotime("$stop 23:59:59"));
	}
?>