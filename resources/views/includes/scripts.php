<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>

<!-- Наши скрипты -->
<script type="text/javascript">

$(function() {
console.log('Начало обработки страницы');
});

$(window).on('load', function () {
  $("body").removeClass("block-refresh");
   renderContent ();

setTimeout(function(){
	$('#wrapper').css({'transition': 'margin 0.3s ease'});
	$('#sidebar').css({'transition': 'width 0.3s ease'});
	$('#task-manager').css({'transition': 'margin-right 0.3s ease'});

	if ($("div").is("#head-content")) {
		$('.head-content').css({'transition': 'width 0.3s ease'});
	};

	if ($("table").is("#table-content")) {
		// $('#thead-sticky').css({'transition': 'margin 0.1s ease'});
		$('#thead-content').css({'transition': 'width 0.3s ease'});
		$('#thead-content>th').css({'transition': 'width 0.3s ease'});
	};

	// $('#filters').css({'transition': 'height 1s ease'});

	$('.td-drop').width(32);
  $('.td-checkbox').width(32);
  $('.td-delete').width(32);
	getMassWidth ();
	fixedThead ();
	// alert('lol');
},1);
 	

   

});


$(window).resize(function() {
  renderContent ();
});

// Иконка в футере при клике
// $('.icon-footer').bind('click', function() {
//   $('#foot-drop').toggleClass('active-foot-drop');
// });


</script>

