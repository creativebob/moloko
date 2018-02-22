



  <div class="grid-x tabs-wrap">
    <div class="small-12 cell">
      <ul class="tabs-list" data-tabs id="tabs">
        <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Общая информация</a></li>
        <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Реквизиты</a></li>
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

        <!-- Учетные данные -->
        <div class="tabs-panel is-active" id="content-panel-1">

          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>Название компании
              {{ Form::text('company_name', $company->company_name, ['class'=>'varchar-field company-name-field', 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё0-9/s]{3,30}']) }}
              </label>
            </div>
          </div>

          <div class="grid-x grid-padding-x tabs-margin-top">

            <div class="small-12 medium-6 cell">
              <label>Телефон
                @include('includes.inputs.phone', ['value'=>$company->phone, 'name'=>'phone', 'required'=>'required'])
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Доп. телефон
                @include('includes.inputs.phone', ['value'=>$company->extra_phone, 'name'=>'extra_phone', 'required'=>''])
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Почта
                @include('includes.inputs.email', ['value'=>$company->email, 'name'=>'email'])
              </label>  
            </div>
            <div class="small-12 medium-6 cell">
              <label class="input-icon">Город
                @php
                  $city_name = null;
                  $city_id = null;
                  if(isset($company->city->city_name)) {
                    $city_name = $company->city->city_name;
                    $city_id = $company->city->city_id;
                  }
                @endphp
                @include('includes.inputs.city_name', ['value'=>$city_name, 'name'=>'city_name', 'required'=>'required'])
                @include('includes.inputs.city_id', ['value'=>$city_id, 'name'=>'city_id'])
              </label>
              <label>Адрес
                @include('includes.inputs.address', ['value'=>$company->address, 'name'=>'address'])
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 cell checkbox">
              {{ Form::checkbox('orgform_status', 1, $company->orgform_status==1, ['id'=>'orgform-status-checkbox']) }}
              <label for="orgform-status-checkbox"><span>Директор компании (Юридическое лицо)</span></label>
            </div>
          </div>
        </div>

        <!-- Реквизиты -->
        <div class="tabs-panel" id="content-panel-2">

            <div class="grid-x grid-padding-x"> 
              <div class="small-12 medium-6 cell">
                <label>ИНН
                  @include('includes.inputs.inn', ['value'=>$company->inn, 'name'=>'inn'])
                </label>
              </div>
              <div class="small-12 medium-6 cell">
                <label>КПП
                  @include('includes.inputs.kpp', ['value'=>$company->kpp, 'name'=>'kpp'])
                </label>
              </div>
              <div class="small-12 medium-12 cell">
                <label>Банк
                  @include('includes.inputs.bank', ['value'=>$company->bank, 'name'=>'bank'])
                </label>
              </div>
              <div class="small-12 medium-6 cell">
                <label>Р/С
                  @include('includes.inputs.account_settlement', ['value'=>$company->account_settlement, 'name'=>'account_settlement'])
                </label>
              </div>
              <div class="small-12 medium-6 cell">
                <label>К/С
                  @include('includes.inputs.account_correspondent', ['value'=>$company->account_correspondent, 'name'=>'account_correspondent'])
                </label>
              </div>
            </div>
        </div>

      </div>
    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    </div>

    {{-- Чекбокс модерации --}}
    @can ('moderator', $company)
      @if ($company->moderation == 1)
        <div class="small-12 cell checkbox">
          @include('includes.inputs.moderation', ['value'=>$company->moderation, 'name'=>'moderation'])
        </div>
      @endif
    @endcan

    {{-- Чекбокс системной записи --}}
    @can ('god', $company)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>$company->system_item, 'name'=>'system_item']) 
      </div>
    @endcan 
    
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>

