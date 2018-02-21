



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
              {{ Form::text('company_name', $company->company_name, ['class'=>'varchar-mask company-name-field', 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё0-9/s]{3,30}']) }}
              </label>
            </div>
          </div>

          <div class="grid-x grid-padding-x tabs-margin-top">

            <div class="small-12 medium-6 cell">
              <label>Телефон
                {{ Form::text('company_phone', $company->company_phone, ['class'=>'phone-mask company-phone', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'maxlength'=>'17', 'autocomplete'=>'off', 'required']) }}
                <span class="form-error">Введите все символы телефонного номера!</span>
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Доп. телефон
                {{ Form::text('company_extra_phone', $company->company_extra_phone, ['class'=>'phone-mask company-extra-phone', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'maxlength'=>'17', 'autocomplete'=>'off']) }}
                <span class="form-error">Введите все символы телефонного номера!</span>
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Почта
                {{ Form::email('company_email', $company->company_email, ['class'=>'email-field company-email-field', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
                <span class="form-error">Укажите почту</span>
              </label>
              
            </div>
            <div class="small-12 medium-6 cell">
               <label class="input-icon">Введите город
                @php
                  $city_name = null;
                  $city_id = null;
                  if(isset($company->city)) {
                    $city_name = $company->city->city_name;
                    $city_id = $company->city->city_id;
                  }
                @endphp
                {{ Form::text('city_name', $city_name, ['class'=>'varchar-mask city-check-field', 'autocomplete'=>'off', 'maxlength'=>'30', 'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}', 'required']) }}
                <div class="sprite-input-right find-status"></div>
                <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
                {{ Form::hidden('city_id', $city_id, ['class'=>'city-id-field', 'maxlength'=>'3', 'pattern'=>'[0-9]{3}']) }}
              </label>
              <label>Адрес
              {{ Form::text('company_address', $company->company_address, ['class'=>'varchar-mask company-address-field', 'maxlength'=>'60', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9.,_-\s/]{3,60}']) }}
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
                {{ Form::text('company_inn', $company->user_id, ['class'=>'company_inn-field', 'id'=>'company_inn-field', 'maxlength'=>'10', 'pattern'=>'[0-9]{10}', 'autocomplete'=>'off']) }}
                </label>
              </div>
              <div class="small-12 medium-6 cell">
                <label>КПП
                {{ Form::text('kpp', $company->kpp, ['class'=>'kpp-field', 'maxlength'=>'9', 'pattern'=>'[0-9]{9}', 'autocomplete'=>'off']) }}
                </label>
              </div>
              <div class="small-12 medium-12 cell">
                <label>Банк
                {{ Form::text('bank', $company->bank, ['class'=>'varchar-mask bank-field', 'maxlength'=>'60', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё0-9-_/s]{3,60}']) }}
                </label>
              </div>
              <div class="small-12 medium-6 cell">
                <label>Р/С
                {{ Form::text('account_settlement', $company->account_settlement, ['class'=>'account-settlement-field', 'maxlength'=>'20', 'pattern'=>'[0-9]{20}', 'autocomplete'=>'off']) }}
                </label>
              </div>
              <div class="small-12 medium-6 cell">
                <label>К/С
                {{ Form::text('account_correspondent', $company->account_correspondent, ['class'=>'account-correspondent-field', 'maxlength'=>'20', 'pattern'=>'[0-9]{20}', 'autocomplete'=>'off']) }}
                </label>
              </div>
            </div>
        </div>

      </div>
    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    </div>

    @can ('moderator', $company)
      @if ($company->moderated == 1)
        <div class="small-12 cell checkbox">
          {{ Form::checkbox('moderation_status', 1, $company->moderated, ['id'=>'moderation-checkbox']) }}
          <label for="moderation-checkbox"><span>Временная запись!</span></label>
        </div>
      @endif
    @endcan

    @can ('god', $company)
      <div class="small-12 cell checkbox">
        {{ Form::checkbox('system_item', 1, $company->system_item, ['id'=>'system-checkbox']) }}
        <label for="system-checkbox"><span>Сделать запись системной.</span></label>
      </div>
    @endcan
    
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>

