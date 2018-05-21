<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
  <legend>Фильтры:</legend>
  <div class="grid-x">
    <div class="small-12 medium-6 cell">
      {{-- @include('includes.inputs.checkboxer', ['name'=>'city', 'value'=>$filter]) --}}   
    </div>
  </div>
  <div class="grid-x">
    <div class="small-12 medium-6 cell">
      {{-- @include('includes.inputs.checkboxer', ['name'=>'sector', 'value'=>$filter]) --}}     
    </div>
  </div>
</div>
<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
  <legend>Мои списки:</legend>
  <div id="booklists">
    @include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])
  </div>
</div>

