<div class="grid-x grid-padding-x">
    <div class="small-6 cell">
        <label>Рубрика
            {!! Form::select('rubricator_id', $rubricators->pluck('name', 'id'), isset($cur_news->rubricator_id) ? $cur_news->rubricator_id : $rubricators->first()->id, ['id' => 'select-rubricators']) !!}
        </label>
    </div>
    <div class="small-6 cell">
        <label>Пункт рубрики
            @include('news.rubricators.select_rubricators_items', ['rubricator_id' => isset($cur_news->rubricator_id) ? $cur_news->rubricator_id : $rubricators->first()->id, 'rubricators_item_id' => $cur_news->rubricators_item_id])
        </label>
    </div>
</div>

@push('scripts')
<script>
// Смена рубрики
$(document).on('change', '#select-rubricators', function(event) {
    event.preventDefault();
    $.post("/admin/rubricators/" + $(this).val() + "/get_rubricators_items", function(html){
        $('#select-rubricators_items').html(html);
    });
});
</script>
@endpush
