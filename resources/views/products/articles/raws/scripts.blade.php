<script type="application/javascript">

// ----------- Изменение -------------

    if(!{{ $raw->portion_goods_status }} ){
        $('#portion-goods-block').hide();
    }

    $('#portion_goods_status').click(function(){
        $('#portion-goods-block').slideToggle(200);
    });

</script>