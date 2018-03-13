<?php

    function decor_access_block($access_block) {
        if($access_block == 1){$result = "Блокирован";} else {$result = "Открыт";};
        return $result;
    };

    function decor_user_type($user_type) {
        if($user_type == 1){$result = "Сотрудник";} 
        elseif($user_type == 2){$result = "Клиент";} 
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
                $rest1 = mb_substr($value, 5, 2); // возвращает "abcd"
                $rest2 = mb_substr($value, 7, 2); // возвращает "abcd"
                $rest3 = mb_substr($value, 9, 2); // возвращает "abcd"
                $result = $rest1."-".$rest2."-".$rest3;
            } else {
                // $value = strtolower($value, "UTF-8");
                $rest1 = mb_substr($value, 0, 1); // возвращает "bcdef"
                $rest2 = mb_substr($value, 1, 3); // возвращает "bcd"
                $rest3 = mb_substr($value, 4, 3); // возвращает "abcd"
                $rest4 = mb_substr($value, 7, 2); // возвращает "abcdef"
                $rest5 = mb_substr($value, 9, 2); // возвращает "abcdef"
                $result = $rest1." (".$rest2.") ".$rest3."-".$rest4."-".$rest5;
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


	// 	function date_to_mysql($ddd)
	// {
	// 		/*  Подстраиваем дату под календарь  */	
	// 		list($dd, $dm, $dy) = sscanf($ddd, "%d.%d.%d");
	// 		$ddd = $dy."-".$dm."-".$dd;
	// 		return $ddd;
	// };

        function date_to_mysql($value)
    {
            $date_parts = explode('.', $value);
            $result = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
            return $result;
    }



        // Отдает нужное название метода для отправки на проверку права
        function getmethod($method){

        if($method == 'index'){return 'index';};
        if(($method == 'current_city')||($method == 'current_department')||($method == 'current_sector')){return 'index';};

        if($method == 'show'){return 'view';};
        if(($method == 'edit')||($method == 'update')){return 'update';};
        if(($method == 'create')||($method == 'store')){return 'create';};
        if($method == 'destroy'){return 'delete';};
        if($method == 'setting'){return 'update';};

         }


        // Отдает нужное название метода для отправки на проверку права
        function getMassArguments($request, $name_argument){

            if($request->server('argv') != null){
                $mass_arg = explode("&", $request->server('argv')[0]);
                foreach($mass_arg as $key=>$mystring){
                    $id_city = str_after($mystring, $name_argument . '=');
                    $mass_arg[$key] = $id_city;
                }
            } else {$mass_arg = null;};

            return $mass_arg;

        }


?>