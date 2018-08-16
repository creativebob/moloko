<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">

    <label>Название категории
      @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required'=>'required'])
      <div class="item-error">Такая категория уже существует!</div>
    </label>

    <!--     dd($services_types_list); -->

    @if(count($services_types_list) == 1)

      <input type="hidden" name="services_types" value="$services_types_list[0]">
    @else

      <label>Тип
        {{ Form::select('services_types', $services_types_list) }}
      </label>
    @endif

    {{--    <!-- Bottom Left -->
    <button class="button" type="button" data-toggle="example-dropdown-bottom-left">Тип услуги</button>
    <div class="dropdown-pane" data-position="bottom" data-alignment="left" id="example-dropdown-bottom-left" data-dropdown data-auto-focus="true">
      <!-- My dropdown content in here -->
    </div> --}}

    @include('includes.control.checkboxes', ['item' => $services_category])

  </div>
</div>

{{ Form::hidden('first_item', 0, ['class' => 'first-item', 'pattern' => '[0-9]{1}']) }}
{{ Form::hidden('services_category_id', $services_category->id, ['id' => 'services-category-id']) }}

<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>

@include('services_categories.scripts')



