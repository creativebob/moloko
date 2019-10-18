{{-- Модалка удаления со страницы --}}
<div class="reveal rev-small" id="delete-estimates_item" data-reveal>
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Удаление</h5>
        </div>
    </div>
    <div class="grid-x align-center modal-content ">
        <div class="small-10 cell text-center">
            <p>Удаляем "<span class="title-estimates_item"></span>", вы уверены?</p>
        </div>
    </div>
    <div class="grid-x align-center grid-padding-x">
        <div class="small-6 medium-4 cell">
            <button data-close class="button modal-button button-delete-estimates_item" type="submit">Удалить</button>
        </div>
        <div class="small-6 medium-4 cell">
            <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
        </div>
    </div>
    <div data-close class="icon-close-modal sprite close-modal"></div>
</div>

@push('scripts')
<script>
    $(document).on('click', '[data-open="delete-estimates_item"]', function() {

    // Находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.item');
    var entity_alias = parent.attr('id').split('-')[0];
    var id = parent.attr('id').split('-')[1];
    var name = parent.data('name');
    $('.title-estimates_item').text(name);
    $('.button-delete-estimates_item').attr('id', entity_alias + '-' + id);
});

$(document).on('click', '.button-delete-estimates_item', function(event) {
    event.preventDefault();

    var entity = $(this).attr('id').split('-')[0];
    var id = $(this).attr('id').split('-')[1];

    var buttons = $('.button');

    $.ajax({
        url: '/admin/' + entity + '/' + id,
        type: 'DELETE',
        success: function (data) {
            if (data > 0) {
                $('#' + entity + '-' + id).remove();
                $('#delete-estimates_item').foundation('close');
                $('.button-delete-estimates_item').removeAttr('id');
                buttons.prop('disabled', false);
            }
        }
    });
});

</script>
@endpush
