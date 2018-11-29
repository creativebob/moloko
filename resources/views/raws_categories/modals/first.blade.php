<div class="grid-x grid-padding-x align-center modal-content inputs">
    <div class="small-10 cell">

        <label>Название категории
            @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required' => true])
            <div class="item-error">Такая категория уже существует!</div>
        </label>

        @if(count($raws_modes_list) == 1)
        <input type="hidden" name="raws_mode_id" value="1">
        @else
        <label>Тип
            {{ Form::select('raws_mode_id', $raws_modes_list) }}
        </label>
        @endif



        @include('includes.control.checkboxes', ['item' => $raws_category])

    </div>
</div>

{{ Form::hidden('first_item', 0, ['class' => 'first-item', 'pattern' => '[0-9]{1}']) }}
{{ Form::hidden('raws_category_id', $raws_category->id, ['id' => 'raws-category-id']) }}

<div class="grid-x align-center">
    <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button '.$class]) }}
    </div>
</div>

@include('raws_categories.scripts')



