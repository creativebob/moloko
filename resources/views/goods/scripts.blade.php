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
      data: {id: id, entity: 'goods_categories'},
      success: function(html){
        $('#units-list').html(html);
        $('#units-list').prop('disabled', false);
      }
    }); 
  });

  $(document).on('change', '#goods-categories-list', function() {

    var id = $(this).val();
    // alert(id);

    if (id == 0) {
      $('#mode').html('');
      // $('#goods_groups-list').prop('disabled', true);

    } else {


      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/ajax_goods_count',
        type: "POST",
        data: {id: id, entity: 'goods'},
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
        url: '/admin/ajax_goods_modes',
        type: "POST",
        data: {mode: id, entity: 'goods'},
        success: function(html){
        // alert(html);
        $('#mode').html(html);
      }
    }); 
  });

</script>


