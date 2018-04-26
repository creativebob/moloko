<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>
  CKEDITOR.replace('content-ckeditor');

  $(function() {

  	$(document).on('change', '#albums-categories-select', function() {
			var id = $(this).val();

			// Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/albums_list",
        type: "POST",
        data: {id: id},
        success: function(html){
        	$('#albums-select').prop('disabled', false);
          $('#albums-select').html(html);
        }
      });
  	
  	});	




  });



</script>
<!-- <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script src="/vendor/unisharp/laravel-ckeditor/adapters/jquery.js"></script>
<script>
  $('#content-ckeditor').ckeditor();
  // $('.textarea').ckeditor(); // if class is prefered.
</script> -->

<!-- <script src="{{ asset('vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'summary-ckeditor' );
</script> -->







