<?php
	$default = "คำชี้แจงเอกสารการมอบตัว v2.pdf";
	header("Content-Type: application/pdf");
	header("Content-Length: ".filesize($default));
	fpassthru(fopen($default, "rb")); # readfile($default);
	exit(0);
?>