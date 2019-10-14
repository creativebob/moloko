{{--<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>--}}
{{--<script src="https://cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script>--}}
<script src="/js/plugins/ckeditor/ckeditor.js"></script>
<script>

    // ClassicEditor
    //     .create( document.querySelector( '#content-ckeditor' ) )
    //     .catch( error => {
    //         console.error( error );
    //     } );

    CKEDITOR.replace('content-ckeditor');

    // Конфигурация
    CKEDITOR.config.toolbar = [
    ['Bold', 'Italic', 'NumberedList', 'BulletedList', 'Maximize', 'Source']
    ];

</script>