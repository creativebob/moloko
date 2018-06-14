<script type="text/javascript">
  $(document).on('change', '#units-categories-list', function() {
    var id = $(this).val();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/get_units_list',
      type: "POST",
      data: {id: id, entity: 'products'},
      success: function(html){
        $('#units-list').html(html);
        $('#units-list').prop('disabled', false);
      }
    }); 
  });

  function readURL(input) {

    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $('#photo').attr('src', e.target.result);
        createDraggable();
      };

      reader.readAsDataURL(input.files[0]);
    }
  }

  $("input[name='photo']").change(function () {
    readURL(this);
  });
</script>




