<div class="small-12 medium-6 large-6 cell">
  <legend>Фильтры:</legend>
  <div class="grid-x">
    <div class="small-12 cell">
      <div class="grid-x">
        <div class="small-12 medium-6 cell checkbox checkboxer">
          @include('includes.inputs.checkboxer', ['name'=>'city', 'value' => $filter])      
        </div>

        <div class="small-12 cell">
            <div class="grid-x">
                <div class="small-12 medium-6 cell checkbox checkboxer">
                    @include('includes.inputs.checkboxer', ['name'=>'user_type', 'value' => $filter])
                </div>

            </div>
        </div>

        <div class="small-12 cell">
            <div class="grid-x">
                <div class="small-12 medium-6 cell checkbox checkboxer">
                    @include('includes.inputs.checkboxer', ['name'=>'access_block', 'value' => $filter])
                </div>
            </div>
        </div>


      </div>
    </div>

    <div class="small-12 medium-6 large-6 cell date-interval-block">


    </div> 
  </div>
</div>

<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
  <legend>Мои списки:</legend>
  <div id="booklists">
    @include('includes.inputs.booklister', ['name'=>'booklist', 'value' => $filter])
  </div>
</div>