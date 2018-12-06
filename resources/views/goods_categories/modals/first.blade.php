<div class="grid-x grid-padding-x align-center modal-content inputs">
    <div class="small-10 cell">

        <label>Название категории
            @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required' => true])
            <div class="item-error">Такая категория уже существует!</div>
        </label>

        @if(count($goods_modes->pluck('name', 'id')) == 1)
        <input type="hidden" name="goods_mode_id" value="1">
        @else
        <label>Тип
            {{ Form::select('goods_mode_id', $goods_modes->pluck('name', 'id')) }}
        </label>
        @endif

        @include('includes.control.checkboxes', ['item' => $goods_category])

    </div>
</div>

{{ Form::hidden('first_item', 0, ['class' => 'first-item', 'pattern' => '[0-9]{1}']) }}
{{ Form::hidden('goods_category_id', $goods_category->id, ['id' => 'goods-category-id']) }}

<div class="grid-x align-center">
    <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button '.$class]) }}
    </div>
</div>

@include('goods_categories.scripts')



