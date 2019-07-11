<div class="grid-x grid-padding-x modal-content inputs">
    <div class="small-10 small-offset-1 cell">

        @isset($parent_id)
        <label>Расположение
            @include('includes.selects.categories_select', ['id' => $item->id, 'parent_id' => $parent_id])
        </label>
        @endisset

        <label>Название
            @include('includes.inputs.name', ['required' => true, 'check' => true])
            <div class="item-error">Такое название уже существует!</div>
        </label>

        {{-- @includeIf($page_info->entity->view_path . '.form') --}}

        {{ Form::hidden('id', null, ['id' => 'item-id']) }}
        {{ Form::hidden('category_id', null, ['id' => 'category-id']) }}

        @include('includes.control.checkboxes')
    </div>
</div>

<div class="grid-x align-center">
    <div class="small-6 medium-4 cell">
        {{ Form::submit($submit_text, ['class'=>'button modal-button '.$class]) }}
    </div>
</div>

@push('scripts')
<script>
    $.getScript("/crm/js/jquery.maskedinput.js");
    $.getScript("/crm/js/inputs_mask.js");
</script>
@endpush


