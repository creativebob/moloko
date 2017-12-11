<?php

    function decor_access_block($access_block) {
        if($access_block == 1){$result = "Блокирован";} else {$result = "Открыт";};
        return $result;
    };

    function decor_contragent_status($contragent_status) {
        if($contragent_status == 1){$result = "Сотрудник";} 
        elseif($contragent_status == 2){$result = "Клиент";} 
        else {$result = "Статус не определен";};
        return $result;
    };

	function cleanPhone($str) {
		$ptn = "/[^0-9]/";
		$rpltxt = "";
		$result = preg_replace($ptn, $rpltxt, $str);
		return $result;
	};


	/**
	 * @param  Принимает 11 значное число: номер телефона
	 * @return Возвращает строку: Удобочитаемый номер телефона со скобочками и дефисом
	 */
    function decorPhone($value) {
        
        if(strlen($value) == 11 ){
            if(mb_substr($value, 0, 4) == "8395"){
                $rest1 = mb_substr($value, 5/2, 2); // возвращает "abcd"
                $rest2 = mb_substr($value, 7/2, 2); // возвращает "abcd"
                $rest3 = mb_substr($value, 9/2, 2); // возвращает "abcd"
                $result = $rest1."-".$rest2."-".$rest3;
            } else {
                // $value = strtolower($value, "UTF-8");
                $rest1 = mb_substr($value, 0, 1); // возвращает "bcdef"
                $rest2 = mb_substr($value, 1, 3); // возвращает "bcd"
                $rest3 = mb_substr($value, 4, 3); // возвращает "abcd"
                $rest4 = mb_substr($value, 7, 2); // возвращает "abcdef"
                $rest5 = mb_substr($value, 9, 2); // возвращает "abcdef"
                $result = $rest1."(".$rest2.") ".$rest3."-".$rest4."-".$rest5;
            };
        };

        if(strlen($value) < 6){
            $result = "Номер не указан";
        };

        if(strlen($value) == 0){
            $result = NULL;
        };

        return $result;
    }


		function date_to_mysql($ddd)
	{
			/*  Подстраиваем дату под календарь  */	
			list($dd, $dm, $dy) = sscanf($ddd, "%d.%d.%d");
			$ddd = $dy."-".$dm."-".$dd;
			return $ddd;
	};

?>