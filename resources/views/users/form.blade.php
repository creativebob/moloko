<div class="grid-x tabs-wrap">
	<div class="small-12 cell">
		<ul class="tabs-list" data-tabs id="tabs">
			<li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Учетные данные</a></li>

            {{-- Подключаемые специфические разделы --}}

            @if(!empty($employee))
            <li class="tabs-title">
                <a data-tabs-target="content-panel-employee" href="#content-panel-employee">Должность</a>
            </li>
            @endif

            @if(!empty($client))
            <li class="tabs-title">
                <a data-tabs-target="content-panel-client" href="#content-panel-client">О клиенте</a>
            </li>
            @endif

            @if(!empty($dealer))
            <li class="tabs-title">
                <a data-tabs-target="content-panel-dealer" href="#content-panel-dealer">Информация о дилере</a>
            </li>
            @endif

			<li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Персональные данные</a></li>
			{{-- <li class="tabs-title"><a data-tabs-target="content-panel-3" href="#content-panel-3">Представитель компании</a></li> --}}
			<li class="tabs-title"><a data-tabs-target="content-panel-4" href="#content-panel-4">Образование и опыт</a></li>
		</ul>
	</div>
</div>

<div class="grid-x tabs-wrap inputs">
	<div class="small-12 medium-6 large-5 cell tabs-margin-top">
		<div class="tabs-content" data-tabs-content="tabs">

			{{-- Учетные данные --}}
			<div class="tabs-panel is-active" id="content-panel-1">
				<div class="grid-x grid-padding-x">
					<div class="small-12 large-6 cell">
						<label>Фамилия
							@include('includes.inputs.name', ['name'=>'second_name', 'value'=>$user->second_name, 'required' => true])
						</label>
						<label>Имя
							@include('includes.inputs.name', ['name'=>'first_name', 'value'=>$user->first_name, 'required' => true])
						</label>
						<label>Отчество
							@include('includes.inputs.name', ['name'=>'patronymic', 'value'=>$user->patronymic])
						</label>
					</div>
					<div class="small-12 large-6 cell">

						@if($user->nickname != null)
							<label>Временное имя
								@include('includes.inputs.name', ['name'=>'nickname', 'value'=>$user->nickname])
							</label>
						@endif

						<label>Выберите аватар
							{{ Form::file('photo') }}
						</label>

						<div class="text-center">
							<img id="photo" src="{{ getPhotoPath($user) }}">
						</div>
					</div>
				</div>

				<!--<div class="grid-x grid-padding-x">
				<div class="small-12 medium-6 cell">
				<label>Короткое имя
				{{ Form::text('nickname', $user->nickname, ['class'=>'nickname-field', 'maxlength'=>'20', 'autocomplete'=>'off', 'required', $param]) }}
				</label>
				</div>
				</div> -->

				<div class="grid-x grid-padding-x tabs-margin-top">
					<div class="small-12 medium-6 cell">
						<label>Телефон
							@include('includes.inputs.phone', ['value' => isset($user->main_phone->phone) ? $user->main_phone->phone : null, 'name'=>'main_phone', 'required' => true, 'id' => 'main-phone'])
						</label>
					</div>
					<div class="small-12 medium-6 cell" id="extra-phones">
						@if (count($user->extra_phones) > 0)
							@foreach ($user->extra_phones as $extra_phone)
								@include('includes.extra-phone', ['extra_phone' => $extra_phone])
							@endforeach
						@else
							@include('includes.extra-phone')
						@endif
						<!-- <span id="add-extra-phone">Добавить номер</span> -->

					</div>
				</div>

				<div class="grid-x grid-padding-x tabs-margin-top">
					<div class="small-12 medium-6 cell">
						<label>
							@include('includes.selects.countries', ['value'=>isset($user->location) ? $user->location->country_id : null])
						</label>
					</div>
					<div class="small-12 medium-6 cell">
						@include('system.common.includes.city_search', ['item' => isset($user->location) ? $user : $auth_user, 'required' => true])
					</div>
					<div class="small-12 medium-6 cell">
						<label>Адрес
							@php
								$address = null;
								if (isset($user->location->address)) {
									$address = $user->location->address;
								}
							@endphp
							@include('includes.inputs.address', ['value'=>$address, 'name'=>'address'])
						</label>
					</div>
					<div class="small-12 medium-6 cell">
						<label>Почта
						@include('includes.inputs.email', ['value'=>$user->email, 'name'=>'email'])
						</label>
					</div>
					<div class="small-12 medium-6 cell">
						<label>Телеграм ID
							{{ Form::text('telegram', $user->telegram, ['class'=>'telegram-id-field', 'pattern'=>'[0-9]{9,12}', 'maxlength'=>'12', 'autocomplete'=>'off']) }}
							<span class="form-error">Укажите номер Telegram</span>
						</label>
					</div>

					<div class="small-12 medium-6 cell">
						<label>Метка пользователя (литер):
							@include('includes.inputs.string', ['name'=>'liter', 'value'=>$user->liter])
						</label>
					</div>
				</div>
			</div>

		    <!-- Блок сотрудника -->
            @if(!empty($employee))
            <div class="tabs-panel" id="content-panel-employee">
                <div class="grid-x grid-padding-x inputs">
    				<div class="small-12 medium-12 large-12 cell">
						@if($list_user_employees->isNotEmpty())
							<table class="content-table list-employees">
								<thead>
									<tr>
										<th>Должность</th>
										<th>Ставка</th>
										<th>Отдел</th>
										<th>Дата принятия</th>
										<th>Дата увольнения</th>
<!-- 										<th>Причина увольнения</th> -->
										<th>Операции</th>
									</tr>
								</thead>
								<tbody id="table-raws">
									@if($list_user_employees->isNotEmpty())
										@foreach($list_user_employees as $employee_item)
											<tr id="">
												<td>
													{{ $employee_item->staffer->position->name }}
												</td>
												<td>
													{{ $employee_item->staffer->rate }}
												</td>
												<td>
													{{ $employee_item->staffer->department->name }}

													@if($employee_item->staffer->filial_id != $employee_item->staffer->department_id)
														<br>
														<span>{{ $employee_item->staffer->filial->name }}</span>
													@endif
												</td>
												<td>
													{{ $employee_item->employment_date->format('d.m.Y') }}
												</td>
												<td>
													@if(isset($employee_item->dismissal_date)){{ $employee_item->dismissal_date->format('d.m.Y') }} @endif
												</td>
												{{-- <td>
													{{ $employee_item->dismissal_description }}
												</td> --}}
												<td class="actions">
														@if($employee_item->dismissal_date == null)
															<a class="button alert tiny" id="employee-dismiss" data-id="{{ $employee_item->id }}">Уволить</a>
														@else
															{{-- <a class="button tiny">Изменить</a> --}}
														@endif
												</td>
											<tr>
										@endforeach
								    @endif
								</tbody>
							</table>
						@endif

					</div>

					
					@if(($list_user_employees->where('dismissal_date', null)->sum('staffer.rate') < 1)&&($list_empty_staff->count() > 0))
						@if(isset($employee->id))
			    			<div class="small-12 cell">
								<a class="button green-button" id="employee-employment" data-user-id="{{ $employee->user->id }}">Трудоустроить</a>
							</div>
						@else
			    			<div class="small-12 cell">
			                	<div class="grid-x grid-padding-x inputs">
						            <div class="small-12 medium-8 cell">
								        <label>Вакантная должность:
								            @include('includes.selects.empty_staff', ['disabled' => true, 'mode' => 'default'])
								        </label>
									</div>
						            <div class="small-12 medium-4 cell">
						                <label>Дата приема
						                    @include('includes.inputs.date', ['name'=>'employment_date', 'required' => true])
						                </label>
						            </div>
						        </div>
							</div>
						@endif
				    @endif

				</div>
			</div>

            <!-- Конец блока сотрудника -->
            @endif


            @if(!empty($client))

            <!-- Блок клиента -->
            <div class="tabs-panel" id="content-panel-client">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-12 cell">
                        @include('includes.selects.loyalties', ['value'=>$client->loyalty_id])
                    </div>

                    <div class="small-12 medium-12 cell">
                        <label>Комментарий к клиенту
                            @include('includes.inputs.textarea', ['name'=>'description_client', 'value'=>$client->description])
                        </label>
                    </div>
                </div>
            </div>
            <!-- Конец блока клиента -->
            @endif

            @if(!empty($dealer))
            <!-- Блок дилера -->
            <div class="tabs-panel" id="content-panel-dealer">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">
                        <label>Комментарий к дилеру
                            @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$dealer->description])
                        </label>
                    </div>
                    <div class="small-6 medium-3 cell">
                        <label>Скидка
                            @include('includes.inputs.digit', ['name'=>'discount', 'value'=>$dealer->discount])
                        </label>
                    </div>
                </div>
            </div>
            <!-- Конец блока дилера -->
            @endif


			<!-- Персональные данные -->
			<div class="tabs-panel" id="content-panel-2">
				<div class="grid-x grid-padding-x">
					<div class="small-5 medium-4 cell">
						<label>Дата рождения
							@include('includes.inputs.date', ['name'=>'birthday', 'value'=>$user->birthday])
						</label>
					</div>
					<div class="small-6 small-offset-1 medium-6 medium-offset-2 cell radiobutton">Пол<br>

						{{ Form::radio('sex', 1, true, ['id'=>'man']) }}
						<label for="man"><span>Мужской</span></label>

						{{ Form::radio('sex', 0, false, ['id'=>'woman']) }}
						<label for="woman"><span>Женский</span></label>

					</div>
				</div>
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-6 cell">
						<label>Паспорт (серия, номер)
							{{ Form::text('passport_number', $user->passport_number, ['class'=>'passport-number-field', 'pattern'=>'[0-9]{2} [0-9]{2} №[0-9]{6}', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
						</label>
					</div>
					<div class="small-5 medium-6 cell">
						<label>Когда выдан
							@include('includes.inputs.date', ['name'=>'passport_date', 'value'=>$user->passport_date])
						</label>
					</div>
				</div>
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-12 cell">
						<label>Кем выдан
							{{ Form::text('passport_released', $user->passport_released, ['class'=>'varchar-field passport-released-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
						</label>
					</div>
				</div>
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-6 cell">
						<label>Адрес прописки
							@include('includes.inputs.address', ['value'=>$user->passport_address, 'name'=>'passport_address'])
						</label>
					</div>
				</div>
			</div>

			<!-- Представитель компании -->
			{{-- <div class="tabs-panel" id="content-panel-3">
				<div class="grid-x grid-padding-x">
					<div class="small-12 cell checkbox">
						{{ Form::checkbox('orgform_status', 1, $user->orgform_status==1, ['id'=>'orgform-status-checkbox']) }}
						<label for="orgform-status-checkbox"><span>Директор компании (Юридическое лицо)</span></label>
					</div>
				</div>
				<div class="grid-x grid-padding-x tabs-margin-top">
					<div class="small-12 medium-6 cell">
						<label>Название компании
							{{ Form::text('company_name', $user->company_name, ['class'=>'varchar-field company-name-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё0-9.,_-/\s()]{3,40}']) }}
						</label>
					</div>
				</div>
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-6 cell">
						<label>ИНН
							@include('includes.inputs.inn', ['value'=>$user->inn, 'name'=>'inn'])
						</label>
					</div>
					<div class="small-12 medium-6 cell">
						<label>КПП
							@include('includes.inputs.kpp', ['value'=>$user->kpp, 'name'=>'kpp'])
						</label>
					</div>
				</div>
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-12 cell">
						<label>Банк
							@include('includes.inputs.bank', ['value'=>$user->bank, 'name'=>'bank'])
						</label>
					</div>
				</div>
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-6 cell">
						<label>Р/С
							@include('includes.inputs.account', ['value'=>$user->account_settlement, 'name'=>'account_settlement'])
						</label>
					</div>
					<div class="small-12 medium-6 cell">
						<label>К/С
							@include('includes.inputs.account', ['value'=>$user->account_correspondent, 'name'=>'account_correspondent'])
						</label>
					</div>
				</div>
			</div> --}}

			<!-- Образование и опыт -->
			<div class="tabs-panel" id="content-panel-4">
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-6 cell">
						<label>Специальность
							@include('includes.inputs.string', ['name'=>'specialty', 'value'=>$user->specialty])
						</label>
					</div>
					<div class="small-12 medium-6 cell">
						<label>Ученая степень, звание
							@include('includes.inputs.string', ['name'=>'degree', 'value'=>$user->degree])
						</label>
					</div>
				</div>
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-12 cell">
						<label>Информация о человеке (Для сайта):
							{{ Form::textarea('about', $user->about, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
						</label><br>
					</div>
				</div>
				<div class="grid-x grid-padding-x">
					<div class="small-12 medium-12 cell">
						<label>Фраза
							@include('includes.inputs.string', ['name'=>'quote', 'value'=>$user->quote])
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="small-12 medium-5 medium-offset-1 large-5 large-offset-2 cell">
		<fieldset class="fieldset-access">
			<legend>Настройка доступа</legend>

			@if(isset($employee))

				<input name="user_type" type="hidden" value='1'>

			@else
				<div class="grid-x grid-padding-x">
					<div class="small-12 cell">
						<label>Статус пользователя
							{{ Form::select('user_type', [ '0' => 'Чужой', '1' => 'Свой']) }}
						</label>
					</div>

					<div class="small-12 cell tabs-margin-bottom">
						<label>
							@include('includes.selects.filials_for_user', ['value'=>$user->filial_id])
						</label>
					</div>
				</div>
			@endif

			<div class="grid-x grid-padding-x">
				<div class="small-12 cell">
					<label>Логин
						{{ Form::text('login', $user->login, ['class'=>'login-field', 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[A-Za-z0-9._-]{6,30}']) }}
						<span class="form-error">Обязательно нужно логиниться!</span>
					</label>
					<label>Пароль
						{{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '20', 'id' => 'password', 'pattern'=>'[A-Za-z0-9]{6,20}', 'autocomplete'=>'off']) }}
						<span class="form-error">Введите пароль и повторите его, ну а что поделать, меняем ведь данные!</span>
					</label>
					<label>Пароль повторно
						{{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '30', 'id' => 'password-repeat', 'data-equalto' => 'password', 'pattern'=>'[A-Za-z0-9]{6,20}', 'autocomplete'=>'off']) }}
						<span class="form-error">Пароли не совпадают!</span>
					</label>
				</div>
			</div>
			<div class="grid-x grid-padding-x">

				@if (isset($user->login))
					@can('index', App\Role::class)
						<div class="small-12 cell tabs-margin-top">
							<table class="content-table">
								<caption>Уровень доступа</caption>
								<thead>
									<tr>
										<th>Роль</th>
										<th>Филиал</th>
										<th>Должность</th>
										<th>Инфа</th>
										<th class="td-delete"></th>
									</tr>
								</thead>
								<tbody class="roleuser-table">
									@if (!empty($user->role_user))
										@foreach ($user->role_user as $role_user)
											@include('users.roles', $role_user)
										@endforeach
									@endif
								</tbody>
							</table>
						</div>
						<div class="small-8 small-offset-2 medium-8 medium-offset-2 tabs-margin-top text-center cell">
							<a class="button" data-open="role-add">Настройка доступа</a>
						</div>
					@endcan
				@endif

				<div class="small-12 text-center cell checkbox">
					{{ Form::checkbox('access_block', 1, $user->access_block == 1, ['id'=>'access-block-checkbox']) }}
					<label for="access-block-checkbox"><span>Блокировать доступ</span></label>
				</div>
			</div>
		</fieldset>
	</div>

	{{-- Чекбоксы управления --}}
	@include('includes.control.checkboxes', ['item' => $user])

	<div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
		{{ Form::submit($submitButtonText, ['class'=>'button']) }}
	</div>
</div>




