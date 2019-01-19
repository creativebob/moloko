<script type="text/javascript">

    $(document).on('click', '#toggler-view-block', function() {
        $( "#items-list-products" ).removeClass( "view-card" );
        $( "#items-list-products" ).removeClass( "view-list" );
        $( "#items-list-products" ).addClass( "view-block" );

        $( "#toggler-view-card" ).removeClass( "active" );
        $( "#toggler-view-list" ).removeClass( "active" );
        $( "#toggler-view-block" ).addClass( "active" );
    });

    $(document).on('click', '#toggler-view-list', function() {
        $( "#items-list-products" ).removeClass( "view-card" );
        $( "#items-list-products" ).removeClass( "view-block" );
        $( "#items-list-products" ).addClass( "view-list" );

        $( "#toggler-view-card" ).removeClass( "active" );
        $( "#toggler-view-block" ).removeClass( "active" );
        $( "#toggler-view-list" ).addClass( "active" );
    });

    $(document).on('click', '#toggler-view-card', function() {
        $( "#items-list-products" ).removeClass( "view-list" );
        $( "#items-list-products" ).removeClass( "view-block" );
        $( "#items-list-products" ).addClass( "view-card" );

        $( "#toggler-view-list" ).removeClass( "active" );
        $( "#toggler-view-block" ).removeClass( "active" );
        $( "#toggler-view-card" ).addClass( "active" );
    });

</script>