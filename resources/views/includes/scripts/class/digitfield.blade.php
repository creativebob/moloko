<script type="application/javascript">

'use strict';

class DigitField {
	constructor(name, limit_value, decimal_place_value) {
		this.name = name;

		// Проверяем адекватность указанных в поле данных:
		var reg=/^\d+$/
		if (reg.test(limit_value) || limit_value == null){
			this.limit = limit_value;
		} else {
			alert('Ограничивающее значение для цифрового поля ID: digitfield-' + this.name + ' задано не верно!');
		};

		this.decimal_place = decimal_place_value;
		this.old_value = '';
		this.new_value = '';
	}

	KeyDown(before_value){
		this.old_value = $("#digitfield-" + this.name).val();
	};

	Blur(after_value){

		// Исправляем дублирование запятых
		after_value = after_value.replace(/\.$/g,"");

		// Изменяем значение поля на значения с нашими исправлениями
		$("#digitfield-" + this.name).val(after_value);

		if(after_value > this.limit){
			$("#digitfield-" + this.name).val(this.limit);			
		};
	};

	KeyUp(after_value, decimal_place){

		// Проверка на число
		var reg=/^(\d+)(\.{0,1})(\d{0,2})$/
		if (reg.test(after_value)){

			while(after_value > this.limit){
				after_value = after_value.substring(0, after_value.length - 1);
			};
			$("#digitfield-" + this.name).val(after_value);

		// Вносим коррективы в ввод пользователя
		} else {

			if(decimal_place > 0){

				// Исправляем дублирование запятых
				after_value = after_value.replace(/\.{2,}/g,".");

				// Запрещаем ввод запятой в качестве первого символа
				after_value = after_value.replace(/^\./g,"");

				// Запрещаем вводить запятую, после того как она уже была введена в поле
				after_value = after_value.replace(/(:?\.\d{1,})(\.)/g, '$1');

			};

			if(decimal_place == 1){
				// Запрещаем вводить лишние знаки после запятой
				after_value = after_value.replace(/(:?\.\d{1})(.+)/g,'$1');
			};

			if(decimal_place == 2){
				// Запрещаем вводить лишние знаки после запятой
				after_value = after_value.replace(/(:?\.\d{2})(.+)/g,'$1');
			};

			if(decimal_place == 3){
				// Запрещаем вводить лишние знаки после запятой
				after_value = after_value.replace(/(:?\.\d{3})(.+)/g,'$1');
			};

			if(decimal_place == 4){
				// Запрещаем вводить лишние знаки после запятой
				after_value = after_value.replace(/(:?\.\d{4})(.+)/g,'$1');
			};

			// Изменяем значение поля на значения с нашими исправлениями
			$("#digitfield-" + this.name).val(after_value);
		}
	};
}

// function checkNumberFields(n){ // РћР±С‹С‡РЅРѕРµ С†РµР»РѕРµ С‡РёСЃР»Рѕ
// 	var reg=/^\d+$/
// 	if (!reg.test(n)) return false;
// };

// function checkNumberFields_3(n){  // РџСЂРѕС†РµРЅС‚ СЃ РґРІСѓРјСЏ Р·РЅР°РєР°РјРё РїРѕСЃР»Рµ С‚РѕС‡РєРё
// 	var reg=/^(\d{1,3}|\d\.?|\d\.\d?|\d{1,3}\.\d?|\d\.\d{2}?|\d{1,3}\.\d{2}?)$/
// 	if (!reg.test(n)) return false;
// };

// function checkNumberFields_simp(n){  // Р”СЂРѕР±РЅРѕРµ С‡РёСЃР»Рѕ
// 	var reg=/^(\d{1,6}|\d\.?|\d\.\d?|\d{1,6}\.\d?|\d\.\d{2}?|\d{1,6}\.\d{2}?)$/
// 	if (!reg.test(n)) return false;
// };

</script>