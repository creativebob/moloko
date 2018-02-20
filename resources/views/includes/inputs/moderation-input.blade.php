{{ Инпут модерации }}



<legend>Фильтрация</legend>
<div class="grid-x grid-padding-x"> 
  <div class="small-6 medium-4 large-3 cell">
    <label>Город
      {{ Form::select('city_id', $filter['cities_list'], null) }}
    </label>
  </div>

  <div class="small-12 medium-12 align-center cell tabs-button">
    {{ Form::submit('Фильтрация', ['class'=>'button']) }}
  </div>
</div>