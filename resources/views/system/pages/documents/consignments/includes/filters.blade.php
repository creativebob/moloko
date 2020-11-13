<div class="small-12 medium-6 large-6 cell">
  <legend>Фильтры:</legend>

  <div class="grid-x">
    {{-- <div class="cell small-12 medium-6 checkbox checkboxer">
        <checkboxer-component
            name="suppliers"
            title="Поставщики"
            :items='@json($suppliers)'
            :checkeds='@json(request()->suppliers)'
        ></checkboxer-component>
    </div>  --}}
    <div class="cell small-12 medium-6">
        @include('includes.inputs.min_max', ['name' => 'amount', 'title' => 'Сумма накладной'])
    </div>

  </div>



</div>

<div class="small-12 medium-6 large-6 cell checkbox checkboxer">

</div>