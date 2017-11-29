<?php

	function cleanPhone($str) {
		$ptn = "/[^0-9]/";
		$rpltxt = "";
		$result = preg_replace($ptn, $rpltxt, $str);
		return $result;
	};

?>