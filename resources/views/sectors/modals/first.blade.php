<div class="grid-x grid-padding-x align-center modal-content inputs">
    <div class="small-10 cell">

        <label>Название индустрии
            @include('includes.inputs.name', ['value'=>$sector->name, 'name'=>'name', 'required'=>'required'])
            <div class="item-error">Такая индустрия уже существует!</div>
        </label>

        <label>Тег
            @include('includes.inputs.text-en', ['value'=>$sector->tag, 'name'=>'tag', 'required'=>''])
            <div class="sprite-input-right find-status"></div>
            <div class="item-error">Такой тег индустрии уже существует!</div>
        </label>

        {{ Form::hidden('sector_id', $sector->id, ['id' => 'sector-id']) }}

        @include('includes.control.checkboxes', ['item' => $sector])
    </div>
</div>

<div class="grid-x align-center">
    <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button '.$class]) }}
    </div>
</div>
