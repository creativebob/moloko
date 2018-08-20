<script type="text/javascript">

  $(document).on('change', '#units-categories-list', function() {
    var id = $(this).val();
    // alert(id);

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/get_units_list',
      type: "POST",
      data: {id: id, entity: 'catalogs'},
      success: function(html){
        $('#units-list').html(html);
        $('#units-list').prop('disabled', false);
      }
    }); 
  });

  $(document).on('change', '#services-categories-list', function() {

    var id = $(this).val();
    // alert(id);

    if (id == 0) {
      $('#mode').html('');
      // $('#services_groups-list').prop('disabled', true);

    } else {


      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/ajax_services_count',
        type: "POST",
        data: {id: id, entity: 'catalogs'},
        success: function(html){
        // alert(html);
        $('#mode').html(html);

      }
    }); 
    }
  });


  $(document).on('click', '.modes', function(event) {
    event.preventDefault();

    var id = $(this).attr('id');
    // alert(id);

    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/ajax_services_modes',
        type: "POST",
        data: {mode: id, entity: 'catalogs'},
        success: function(html){
        // alert(html);
        $('#mode').html(html);
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


