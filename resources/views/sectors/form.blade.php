<div class="grid-x grid-padding-x modal-content inputs">
    <div class="small-10 small-offset-1 cell">

        @isset($parent_id)
        <label>Расположение
            @include('includes.selects.categories_select', ['parent_id' => $parent_id])
        </label>
        @endisset

        <label>Название сектора
            @include('includes.inputs.name', ['name' => 'name', 'required' => 'required', 'check' => 'check-field'])
            <div class="sprite-input-right find-status"></div>
            <div class="item-error">Такой сектор уже существует!</div>
        </label>

        <label>Тег
            @include('includes.inputs.text-en', ['name' => 'tag', 'check' => 'check-field'])
            <div class="sprite-input-right find-status"></div>
            <div class="item-error">Такой тег индустрии уже существует!</div>
        </label>

        {{ Form::hidden('id', null, ['id' => 'item-id']) }}
        {{ Form::hidden('category_id', null, ['id' => 'category-id']) }}

        @include('includes.control.checkboxes')
    </div>
</div>

<div class="grid-x align-center">
    <div class="small-6 medium-4 cell">
        {{ Form::submit($submit_text, ['data-close', 'class'=>'button modal-button '.$class]) }}
    </div>
</div>

<script type="text/javascript">
    $.getScript("/crm/js/jquery.maskedinput.js");
    $.getScript("/crm/js/inputs_mask.js");
</script>