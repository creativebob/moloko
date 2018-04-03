<div class="small-12 medium-12 large-6 cell checkbox checkboxer">
  <legend>Основные фильтры:</legend>
  @include('includes.inputs.checkboxer', ['name'=>'city', 'value'=>$filter])
  @include('includes.inputs.checkboxer', ['name'=>'sector', 'value'=>$filter])
</div>
<div class="small-12 medium-12 large-6 cell checkbox checkboxer">
  <legend>Мои списки:</legend>
  	<div id="booklists">
  		@include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])
  	</div>

</div>



