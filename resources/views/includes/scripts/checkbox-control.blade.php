<script type="text/javascript">

	$(function() {


		// Чекбоксы
	    var checkboxes = document.querySelectorAll('input.table-check');
	    var checkall = document.getElementById('check-all');

	    // Если есть кнопка группового изменения чекбоксов
	    if(checkall != null){

		    // Функция группового выставления чекбоксов
		    checkall.onclick = function() {
		      for(var i=0; i<checkboxes.length; i++) {
		        checkboxes[i].checked = this.checked;
		        console.log('Видим клик по главному, ставим его положение всем = ' + this.checked);
		      };
		    };
	    };


	    // Счетчики для контроля изменений (Для booklister)
	    counter_checkbox = 0;
	    storage_counter_checkbox = 0;

	    // Функция на каждый чекбокс
	    for(var i=0; i<checkboxes.length; i++) {
	      checkboxes[i].onclick = function() {



	        counter_checkbox = counter_checkbox + 1;

	        var parent = $(this).closest('.item');
	        var entity_alias = parent.attr('id').split('-')[0];
	        var item_entity = parent.attr('id').split('-')[1];

	      	alert(entity_alias + ' - ' + item_entity);
	      	
	        // Если есть кнопка группового изменения чекбоксов
	    	if(checkall != null){
		        var checkedCount = document.querySelectorAll('input.table-check:checked').length;
		        checkall.checked = checkedCount > 0;
		        checkall.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
		    };

	        // Ajax
	        $.ajax({
	          headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	          },
	          url: '/booklists',
	          type: "POST",
	          data: {item_entity: item_entity, entity_alias: entity_alias},
	          success: function (data) {
	            // alert(data);

	            var result = $.parseJSON(data);
	            if (result.status == 0) {

	            } else {

	              // alert(result.msg);

	            };
	          }
	        });
	      };
	    };

	});


	// Очищаем все чекбоксы (Для booklister)
	function cleanAllCheckboxes() {
	  var checkboxes = document.querySelectorAll('input.table-check');
	  for(var i=0; i<checkboxes.length; i++) {
	    checkboxes[i].checked = false;
	  };
	};

</script>