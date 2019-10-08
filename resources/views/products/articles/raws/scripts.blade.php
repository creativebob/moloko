<script type="application/javascript">

// ----------- Изменение -------------

    if(!{{ $raw->portion_status }} ){
        $('#portion-block').hide();
    }

    $('#portion_status').click(function(){
        $('#portion-block').slideToggle(200);
    });

</script>