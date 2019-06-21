<div class="small-12 medium-6 large-6 cell">
  <legend>Фильтры:</legend>
  <div class="grid-x grid-padding-x">


    <div class="small-12 medium-6 cell">
        <div class="grid-x">

            <div class="small-12 cell">
              <div class="grid-x">
                <div class="small-12 cell checkbox checkboxer">
                  @include('includes.inputs.checkboxer', ['name'=>'position', 'value' => $filter])      
                </div>
              </div>
            </div>

            <div class="small-12 cell">
              <div class="grid-x">
                <div class="small-12 cell checkbox checkboxer">
                  @include('includes.inputs.checkboxer', ['name'=>'department', 'value' => $filter])      
                </div>
              </div>
            </div>

            <div class="small-12 cell">
                <div class="grid-x">
                    <div class="small-12 cell checkbox checkboxer">
                        @include('includes.inputs.checkboxer', ['name'=>'access_block', 'value' => $filter])
                    </div>
                </div>
            </div>

            <div class="small-12 cell date-interval-block">

              <div class="grid-x">
                <div class="small-5 medium-5 cell">
                  <label>Начало периода:
                    @include('includes.inputs.date', ['name'=>'date_start', 'value' => ''])
                  </label>
                </div>
                <div class="small-2 medium-2 cell">
                </div>
                <div class="small-5 medium-5 cell">
                  <label>Окончание периода:
                    @include('includes.inputs.date', ['name'=>'date_end', 'value' => ''])
                  </label>
                </div>
              </div>

            </div>

        </div>
    </div>


    <div class="small-12 medium-6 cell">
            <div class="small-12 cell">
                <div class="grid-x">
                    <div class="small-12 cell checkbox checkboxer">
                        @include('includes.inputs.checkboxer', ['name'=>'city', 'value' => $filter])
                    </div>
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