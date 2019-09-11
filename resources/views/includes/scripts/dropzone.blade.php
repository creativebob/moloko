<script type="application/javascript">

    // Настройки dropzone
    Dropzone.options.myDropzone = {
        paramName: 'photo',
        maxFilesize: '{{ $settings['img_max_size'] }}',
        maxFiles: 20,
        acceptedFiles: '{{ $settings['img_formats'] }}',
        addRemoveLinks: true,
        init: function() {
            this.on("success", function(file, responseText) {
                file.previewTemplate.setAttribute('id',responseText[0].id);

                $.post('/admin/photo_index', {
                    id: '{{ $item_id }}',
                    entity: '{{ $item_entity }}'
                }, function(html){
                    // alert(html);
                    $('#photos-list').html(html);
                })
            });
            this.on("thumbnail", function(file) {
                if (file.width < '{{ $settings['img_min_width'] }}' || file.height < '{{ $settings['img_min_height'] }}') {
                    file.rejectDimensions();
                } else {
                    file.acceptDimensions();
                }
            });
        },
        accept: function(file, done) {
            file.acceptDimensions = done;
            file.rejectDimensions = function() { done("Размер фото мал, нужно минимум {{ $settings['img_min_width'] }} px в ширину"); };
        },
        // success: function(file, response)
        // {
        //     $.post('/admin/photo_index', {id: item_id, entity: entity}, function(html){
        //             // alert(html);
        //             $('#photos-list').html(html);
        //         })
        // }
    };

    // Оставляем ширину у вырванного из потока элемента
    // var fixHelper = function(e, ui) {
    //     ui.children().each(function() {
    //         $(this).width($(this).width());
    //     });
    //     return ui;
    // };

    // // Включаем перетаскивание
    // $("#values-table tbody").sortable({
    //     axis: 'y',
    //     helper: fixHelper, // ширина вырванного элемента
    //     handle: 'td:first', // указываем за какой элемент можно тянуть
    //     placeholder: "table-drop-color", // фон вырванного элемента
    //     update: function( event, ui ) {

    //         var entity = $(this).children('.item').attr('id').split('-')[0];
    //     }
    // });
</script>
