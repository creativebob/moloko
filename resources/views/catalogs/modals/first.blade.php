<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">

    <label>Название каталога
      @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required'=>'required'])
      <div class="item-error">Такой каталог уже существует!</div>
    </label>


    {{--    <!-- Bottom Left -->
    <button class="button" type="button" data-toggle="example-dropdown-bottom-left">Тип услуги</button>
    <div class="dropdown-pane" data-position="bottom" data-alignment="left" id="example-dropdown-bottom-left" data-dropdown data-auto-focus="true">
      <!-- My dropdown content in here -->
    </div> --}}

    @include('includes.control.checkboxes', ['item' => $catalog])

  </div>
</div>

{{ Form::hidden('first_item', 0, ['class' => 'first-item', 'pattern' => '[0-9]{1}']) }}
{{ Form::hidden('catalog_id', $catalog->id, ['id' => 'catalog-id']) }}
{{ Form::hidden('site_id', $site->id) }}

<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>

@include('catalogs.scripts')



