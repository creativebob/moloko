<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="grid-x grid-padding-x">

                <div class="medium-6 cell">
                    <label>Название группы товара
                        @include('includes.inputs.name', ['required' => true])
                    </label>
                </div>
                <div class="medium-6 cell">
                    <label>Описание
                        @include('includes.inputs.varchar', ['name' => 'description'])
                    </label>
                </div>

                <div class="small-12 medium-6 cell">
                    @include('includes.selects.units_categories', ['default' => isset($articles_group->unit_id) ? $articles_group->unit->units_category_id : 6])
                </div>

                <div class="small-12 medium-6 cell">
                    @include('includes.selects.units', ['default' => isset($articles_group->unit_id) ? $articles_group->unit_id : 26, 'units_category_id' => isset($articles_group->unit_id) ? $articles_group->unit->units_category_id : 6])
                </div>

            </div>

        </div>
    </div>

    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
    </div>

    @if ($articles_group->articles->count() == 0)
    <div class="small-12 cell checkbox set-status">
        <input type="checkbox" name="set_status" id="set-status" value="set" {{ $articles_group->set_status == 'set' ? 'checked' : '' }}>
        {{-- {{ Form::checkbox('set_status', 'set', $articles_group->set_status, ['id' => 'set-status']) }} --}}
        <label for="set-status"><span>Набор</span></label>
    </div>
    @endif

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $articles_group])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class' => 'button']) }}
    </div>
</div>

