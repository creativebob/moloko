<script>
    const imgMaxSize = parseInt({{ $settings['img_max_size'] }}),
        imgFormats = '{{ $settings['img_formats'] }}',
        strictMode = parseInt({{ $settings['strict_mode'] }}),
        imgMinWidth = parseInt({{ $settings['img_min_width'] }}),
        imgMinHeight = parseInt({{ $settings['img_min_height'] }});

    Dropzone.options.dropzone = {
        paramName: 'photo',
        maxFiles: 20,
        addRemoveLinks: true,

        dictDefaultMessage: "Перетащите сюда фотографии или кликните по этому полю",
        dictCancelUpload: "Прервать загрузку",
        dictUploadCanceled: "Загрузка прервана",
        dictRemoveFile: "Удалить",

        parallelUploads: 1,
        uploadMultiple: false,

        maxFilesize: imgMaxSize,
        {{--acceptedFiles: '{{ $settings['img_formats'] }}',--}}
        acceptedFiles: '.jpg,.jpeg',

        init: function() {
            this.on("thumbnail", function (file) {
                if (strictMode == 0) {
                    if (file.width < imgMinWidth || file.height < imgMinHeight) {
                        file.rejectDimensions()
                    } else {
                        file.acceptDimensions();
                    }
                } else {
                    if (file.width != imgMinWidth || file.height != imgMinHeight) {
                        file.rejectDimensions()
                    } else {
                        file.acceptDimensions();
                    }
                }
            });
            this.on("error", function (file, message) {
                alert(message);
                this.removeFile(file);
            });
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
        },
        accept: function (file, done) {
            file.acceptDimensions = done;
            file.rejectDimensions = function () {
                if (strictMode == 0) {
                    done("Размер фото мал, нужно минимум " + imgMinWidth + " px в ширину, и " + imgMinHeight + " в высоту.");
                } else {
                    done("Размер должен быть " + imgMinWidth + " px в ширину, и " + imgMinHeight + " в высоту.");
                }
            };
        }
    };

</script>
