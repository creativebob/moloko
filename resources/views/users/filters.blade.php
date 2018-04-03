<div class="small-12 medium-12 large-6 cell checkbox checkboxer">
  <legend>Основные фильтры:</legend>
  @include('includes.inputs.checkboxer', ['name'=>'city', 'value'=>$filter])
</div>
<div class="small-12 medium-12 large-6 cell checkbox checkboxer" id="booklists">
  <legend>Мои списки:</legend>
  @include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])
</div>
