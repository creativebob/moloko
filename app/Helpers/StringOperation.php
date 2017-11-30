<?php

	function cleanPhone($str) {
		$ptn = "/[^0-9]/";
		$rpltxt = "";
		$result = preg_replace($ptn, $rpltxt, $str);
		return $result;
	};

	// function date_to_mysql($fdate){
	//     $mydate = explode(".", $fdate);
	// 	$fdate = $mydate[2]."-".$mydate[1]."-".$mydate[0];
	// 	return $fdate;
 //    };

		function date_to_mysql($ddd)
	{
			/*  Подстраиваем дату под календарь  */	
			list($dd, $dm, $dy) = sscanf($ddd, "%d.%d.%d");
			$ddd = $dy."-".$dm."-".$dd;
			return $ddd;
	};

?>