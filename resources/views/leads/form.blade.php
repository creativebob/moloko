


<div class="grid-x tabs-wrap">
  <div class="small-12 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Учетные данные</a></li>
    </ul>
  </div>
</div>

<div class="grid-x tabs-wrap inputs">
  <div class="small-12 medium-6 large-5 cell tabs-margin-top">
    <div class="tabs-content" data-tabs-content="tabs">


      @if ($errors->any())

      <div class="alert callout" data-closable>
        <h5>Неправильный формат данных:</h5>
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      @endif

      <!-- Учетные данные -->
      <div class="tabs-panel is-active" id="content-panel-1">
        <div class="grid-x grid-padding-x">
          <div class="small-12 large-6 cell">
            <label>Контактное лицо
              @include('includes.inputs.name', ['name'=>'name', 'value'=>$lead->name, 'required'=>'required'])
            </label>
          </div>
          <div class="small-12 large-6 cell">
            <label>Выберите аватар
              {{ Form::file('photo') }}
            </label>

</div>
</div>


          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
              <label>Телефон
                @include('includes.inputs.phone', ['value'=>$lead->phone, 'name'=>'phone', 'required'=>'required'])
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Телефон
                @include('includes.inputs.phone', ['value'=>$lead->extra_phone, 'name'=>'extra_phone', 'required'=>''])
              </label>
            </div>
          </div>

          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
              <label>Страна
                @php
                $country_id = null;
                if (isset($lead->location->country_id)) {
                  $country_id = $lead->location->country_id;
                }
                @endphp
                {{ Form::select('country_id', $countries_list, $country_id)}}
              </label>
            </div>
            <div class="small-12 medium-6 cell">
                <label class="input-icon">Введите город
                  @php
                $city_name = null;
                $city_id = null;
                if(isset($lead->location->city->name)) {
                $city_name = $lead->location->city->name;
                $city_id = $lead->location->city->id;
                }
                @endphp
                @include('includes.inputs.city_search', ['city_value'=>$city_name, 'city_id_value'=>$city_id, 'required'=>'required'])
              </label>
            </div>


            <div class="small-12 medium-6 cell">
              <label>Адрес
                @php
                $address = null;
                if (isset($lead->location->address)) {
                  $address = $lead->location->address;
                }
                @endphp
                @include('includes.inputs.address', ['value'=>$address, 'name'=>'address', 'required'=>''])
              </label>
            </div>

            <div class="small-12 medium-6 cell">
              <label>Почта
                @include('includes.inputs.email', ['value'=>$lead->email, 'name'=>'email', 'required'=>''])
              </label> 
            </div>

<!--             <div class="small-12 medium-6 cell">
              <label>Телеграм ID
                {{ Form::text('telegram_id', $lead->telegram_id, ['class'=>'telegram-id-field', 'pattern'=>'[0-9]{9,12}', 'maxlength'=>'12', 'autocomplete'=>'off']) }}
                <span class="form-error">Укажите номер Telegram</span>
              </label>
            </div> -->
        </div>

      </div>

    </div>
  </div>
  <div class="small-12 medium-5 medium-offset-1 large-5 large-offset-2 cell">
    <fieldset class="fieldset-access">
      <legend>Пустой блок</legend>
      <div class="grid-x grid-padding-x"> 

      </div>
      <div class="grid-x grid-padding-x"> 

      </div>
      <div class="grid-x grid-padding-x">

      </div>
    </fieldset> 
  </div>

{{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $lead])  

  <div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>




