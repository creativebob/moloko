<div class="small-12 medium-6 large-6 cell">
  <legend>Фильтры:</legend>
  <div class="grid-x">
    <div class="small-12 cell">
      <div class="grid-x">
        <div class="small-12 medium-6 cell checkbox checkboxer">
          @include('includes.inputs.checkboxer', ['name'=>'author', 'value' => $filter])      
        </div>
      </div>
    </div>
    <div class="small-12 cell">
      <div class="grid-x">
        <div class="small-12 medium-6 cell checkbox checkboxer">
          @include('includes.inputs.checkboxer', ['name'=>'company', 'value' => $filter])      
        </div>
      </div>
    </div>
    <div class="small-12 medium-12 cell date-interval-block">
      <div class="grid-x">
        <div class="small-12 medium-6 cell checkbox checkboxer">
          @include('includes.inputs.checkboxer', ['name'=>'products_category', 'value' => $filter])      
        </div>
      </div>
    </div> 
  </div>
</div>

<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
  <legend>Мои списки:</legend>
  <div id="booklists">
    @include('includes.inputs.booklister', ['name'=>'booklist', 'value' => $filter])
  </div>
</div>