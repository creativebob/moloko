<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>


<script type="text/javascript">

CKEDITOR.replace('content-ckeditor');

  // Конфигурация 
  CKEDITOR.config.toolbar = [
    ['Bold', 'Italic', 'NumberedList', 'BulletedList', 'Maximize', 'Source']
  ];

// // Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
// config.toolbar = [
//   { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
//   { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
//   { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
//   { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
//   '/',
//   { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
//   { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
//   { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
//   { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
//   '/',
//   { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
//   { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
//   { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
//   { name: 'others', items: [ '-' ] },
//   { name: 'about', items: [ 'About' ] }
// ];


  $(function() {

    $(document).on('click', '#submit-role-add', function(event) {
      event.preventDefault();

      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/get_role",
        type: "POST",
        data: $(this).closest('form').serialize(),
        success: function(html){
          $('.table-content > tbody').append(html);
        }
      });
    });

    // Мягкое удаление с ajax
    $(document).on('click', '[data-open="item-delete-ajax"]', function() {

      // Находим описание сущности, id и название удаляемого элемента в родителе
      var parent = $(this).closest('.item');
      var entity_alias = parent.attr('id').split('-')[0];
      var role = parent.attr('id').split('-')[1];
      var department = parent.attr('id').split('-')[2];
      var name = parent.data('name');
      $('.title-delete').text(name);
      $('.delete-button-ajax').attr('id', entity_alias + '-' + role + '-' + department);
    });

    // Подтверждение удаления и само удаление
    $(document).on('click', '.delete-button-ajax', function(event) {

      // Блочим отправку формы
      event.preventDefault();
      var entity_alias = $(this).attr('id').split('-')[0];
      var role = $(this).attr('id').split('-')[1];
      var department = $(this).attr('id').split('-')[2];

      $('#' + entity_alias + '-' + role + '-' + department).remove();
    });

  });
</script>




