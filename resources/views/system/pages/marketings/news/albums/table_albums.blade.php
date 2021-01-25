<table class="content-table tabs-margin-top">
    <caption>Прикрепленные альбомы</caption>
    <thead>
        <tr>
            <th>Альбом</th>
            <th>Категория</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="table-albums">

        @forelse ($curNews->albums as $album)
        @include('system.pages.marketings.news.albums.album', $album)
        @empty
        {{-- <tr>
            <td colspan="3">Нет альбомов</td>
        </tr> --}}
        @endforelse

        {{-- @if (isset($curNews->albums))
        @foreach ($curNews->albums as $album)
        @include('news.albums.tr_album', $album)
        @endforeach
        @endif --}}

    </tbody>
</table>

<div class="text-center">
    <a class="button tabs-margin-top" data-open="modal-albums">Прикрепить альбом</a>
</div>


@push('scripts')
<script>
// Открытие модалки
$(document).on('click', '[data-open="modal-albums"]', function(event) {
    event.preventDefault();
    /* Act on the event */
    $.post("/admin/album_add", function(html){
        $('#modal').html(html).foundation();
        $('#modal-albums').foundation('open');
        checkAlbums();
    });
});

function checkAlbums() {
    if ($('#select-albums > option').length == 0) {
        $('#select-albums').prop('disabled', true);
        $('#submit-album-add').prop('disabled', true);
    } else {
        $('#select-albums').prop('disabled', false);
        $('#submit-album-add').prop('disabled', false);
    }
}

$(document).on('change', '#select-albums_categories', function() {
    var id = $(this).val();

    if (id == 0) {
        $('#select-albums').html('');
        $('#select-albums').prop('disabled', true);
    } else {
        $.post("/admin/albums_select", {
            albums_category_id: id
        }, function(html){
            // alert(html);
            $('#select-albums').replaceWith(html);
            checkAlbums();
        });
    }
});

// Добавление альбома
$(document).on('click', '#submit-album-add', function(event) {
    // Блочим отправку формы
    event.preventDefault();
    $(this).prop('disabled', true);
    var form = $(this).closest('form');

    $.post('/admin/album_get', form.serialize(), function(html){
        $('#table-albums').append(html);
        form.closest('.reveal-overlay').remove();
    });
});

// Удаление альбома
$(document).on('click', '.delete-button-ajax', function() {
    $('#item-delete-ajax').foundation('close');
});
</script>
@endpush
