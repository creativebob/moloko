
<div class="grid-x tabs-wrap">
  <div class="small-12 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Общая информация</a></li>
      <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Реквизиты</a></li>
      <li class="tabs-title"><a data-tabs-target="content-panel-3" href="#content-panel-3">График работы</a></li>
      <li class="tabs-title"><a data-tabs-target="content-panel-4" href="#content-panel-4">Настройка</a></li>
    </ul>
  </div>
</div>


<div class="grid-x tabs-wrap inputs">
  <div class="small-12 medium-7 large-5 cell tabs-margin-top">
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

      <!-- Общая информация -->
      <div class="tabs-panel is-active" id="content-panel-1">
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 medium-6 cell">
            <label>Название компании
              @include('includes.inputs.name', ['value'=>$company->name, 'name'=>'name', 'required'=>'required'])
            </label>
          </div>
          <div class="small-12 medium-6 cell">
            <label>Вид деятельности компании
              <select name="sector_id" class="sectors-list">
                @php
                echo $sectors_list;
                @endphp
              </select>
            </label>
          </div>
          <div class="small-12 medium-6 cell">
            <label>Телефон
              @include('includes.inputs.phone', ['value' => isset($company->main_phone->phone) ? $company->main_phone->phone : null, 'name'=>'main_phone', 'required'=>'required'])
            </label>
          </div>
          <div class="small-12 medium-6 cell" id="extra-phones">
            @if (count($company->extra_phones) > 0)
            @foreach ($company->extra_phones as $extra_phone)
            @include('includes.extra-phone', ['extra_phone' => $extra_phone])
            @endforeach
            @else
            @include('includes.extra-phone')
            @endif

            <!-- <span id="add-extra-phone">Добавить номер</span> -->
          </div>

          <div class="small-12 medium-6 cell">
            <label>Почта
              @include('includes.inputs.email', ['value'=>$company->email, 'name'=>'email', 'required'=>''])
            </label>  
            <label>Страна
              @php
              $country_id = null;
              if (isset($company->location->country_id)) {
              $country_id = $company->location->country_id;
            }
            @endphp
            {{ Form::select('country_id', $countries_list, $country_id)}}
          </label>
        </div>

        <div class="small-12 medium-6 cell">
          <label class="input-icon">Город
            @php
            $city_name = null;
            $city_id = null;
            if (isset($company->location->city->name)) {
            $city_name = $company->location->city->name;
            $city_id = $company->location->city->id;
          }
          @endphp
          @include('includes.inputs.city_search', ['city_value'=>$city_name, 'city_id_value'=>$city_id, 'required'=>'required'])
        </label>
        <label>Адрес
          @php
          $address = null;
          if (isset($company->location->address)) {
          $address = $company->location->address;
        }
        @endphp
        @include('includes.inputs.address', ['value'=>$address, 'name'=>'address', 'required'=>''])
      </label>
    </div>
          <!-- <div class="small-12 cell checkbox">
            {{ Form::checkbox('orgform_status', 1, $company->orgform_status==1, ['id'=>'orgform-status-checkbox']) }}
            <label for="orgform-status-checkbox"><span>Директор компании (Юридическое лицо)</span></label>
          </div> -->
        </div>
      </div>

      <!-- Реквизиты -->
      <div class="tabs-panel" id="content-panel-2">

        <div class="grid-x grid-padding-x"> 
          <div class="small-12 medium-6 cell">
            <label>ИНН
              @include('includes.inputs.inn', ['value'=>$company->inn, 'name'=>'inn', 'required'=>''])
            </label>
          </div>
          <div class="small-12 medium-6 cell">
            <label>КПП
              @include('includes.inputs.kpp', ['value'=>$company->kpp, 'name'=>'kpp', 'required'=>''])
            </label>
          </div>
          <div class="small-12 medium-12 cell">
            <label>Банк
              @include('includes.inputs.bank', ['value'=>$company->bank, 'name'=>'bank', 'required'=>''])
            </label>
          </div>
          <div class="small-12 medium-6 cell">
            <label>Р/С
              @include('includes.inputs.account', ['value'=>$company->account_settlement, 'name'=>'account_settlement', 'required'=>''])
            </label>
          </div>
          <div class="small-12 medium-6 cell">
            <label>К/С
              @include('includes.inputs.account', ['value'=>$company->account_correspondent, 'name'=>'account_correspondent', 'required'=>''])
            </label>
          </div>
          <div class="small-12 medium-6 cell">
            <label>БИК
              @include('includes.inputs.bic', ['value'=>$company->bic, 'name'=>'bic', 'required'=>''])
            </label>
          </div>
        </div>
      </div>

      <!-- Настройки -->
      <div class="tabs-panel" id="content-panel-4">
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 medium-6 cell">
            <label>Алиас
              @include('includes.inputs.alias', ['value'=>$company->alias, 'name'=>'alias', 'required'=>''])
            </label>
          </div>

          @include('includes.scripts.class.checkboxer')
          <div class="small-12 medium-12 cell checkbox checkboxer">
            @include('includes.inputs.checkboxer', ['name'=>'services_types', 'value'=>$services_types_checkboxer])      
          </div>

          {{-- Чекбоксы управления --}}
          @include('includes.control.checkboxes', ['item' => $company])

        </div>
      </div>

      <!-- Схема работы -->
      <div class="tabs-panel" id="content-panel-3">
        <div class="grid-x grid-padding-x">
          <div class="small-12 medium-6 cell">
            @include('includes.inputs.schedule', ['value'=>$worktime]) 
          </div>
        </div>
      </div>

    </div>

    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    </div>

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>
</div>

