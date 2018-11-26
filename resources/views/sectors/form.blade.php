<div class="grid-x grid-padding-x modal-content inputs">
    <div class="small-10 small-offset-1 cell">

        @isset($sector_id)
        <label>Расположение
            @include('includes.selects.sectors', ['sector_id' => $sector_id])
        </label>
        @endisset

        <label>Название сектора
            @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required'=>'required'])
            <div class="sprite-input-right find-status"></div>
            <div class="item-error">Такой сектор уже существует!</div>
        </label>

        <label>Тег
            @include('includes.inputs.text-en', ['value'=>null, 'name'=>'tag', 'required'=>''])
            <div class="sprite-input-right find-status"></div>
            <div class="item-error">Такой тег индустрии уже существует!</div>
        </label>

        {{ Form::hidden('id', null) }}

        @include('includes.control.checkboxes', ['item' => $sector])
    </div>
</div>

<div class="grid-x align-center">
    <div class="small-6 medium-4 cell">
        {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button '.$class]) }}
    </div>
</div>