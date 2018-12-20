<script type="text/javascript">

    var item_id = '{{ $item_id }}';

    // При клике на фотку подствляем ее значения в блок редактирования
    $(document).on('click', '#photos-list .edit', function(event) {
        event.preventDefault();

        // Удаляем всем фоткам активынй класс
        $('#photos-list img').removeClass('active');
        $('#photos-list img').removeClass('updated');

        // Наваливаем его текущей
        $(this).addClass('active');

        // Получаем инфу фотки
        $.post('/admin/photo_edit/' + $(this).data('id'), function(html){
            // alert(html);
            $('#photo-edit-partail').html(html);
        })
    });

    // При сохранении информации фотки
    $(document).on('click', '#form-photo-edit .button', function(event) {
        event.preventDefault();

        var button = $(this);
        button.prop('disabled', true);

        var id = $(this).closest('#form-photo-edit').find('input[name=id]').val();
        // alert(id);

        // Записываем инфу и обновляем
        $.ajax({
            url: '/admin/photo_update/' + id,
            type: 'PATCH',
            data: $(this).closest('#form-photo-edit').serialize(),
            success: function(res) {

                if (res == true) {
                    button.prop('disabled', false);

                    $('#photos-list').find('.active').addClass('updated').removeClass('active');
                } else {
                    alert(res);
                };
            }
        })
    });

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

                $.post('/admin/photo_index', {id: item_id, entity: entity}, function(html){
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
        }
    };

    // Оставляем ширину у вырванного из потока элемента
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    // Включаем перетаскивание
    $("#values-table tbody").sortable({
        axis: 'y',
        helper: fixHelper, // ширина вырванного элемента
        handle: 'td:first', // указываем за какой элемент можно тянуть
        placeholder: "table-drop-color", // фон вырванного элемента
        update: function( event, ui ) {

            var entity = $(this).children('.item').attr('id').split('-')[0];
        }
    });
</script>