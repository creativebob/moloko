<div class="grid-x grid-padding-x">
    <main class="cell small-12 main-content">
        {!! Form::model($user, ['route' => 'project.user.update', 'data-abide', 'novalidate', 'id'=>'profile-form']) !!}

        {{-- Заголовок --}}
        @include('mwtour.pages.common.title')

		@include('project.composers.navigations.navigation_by_align', ['align' => 'left'])

        <div class="grid-x">
            <div class="cell small-12 medium-6 large-4 fields-column">
                <div class="grid-x">
                    <div class="small-12 cell">
                        <label>Имя
                            @include('includes.inputs.name', ['name'=>'first_name', 'value'=>$user->first_name, 'required' => true])
                        </label>
                    </div>
                    <div class="small-12 cell">
                        <label>Фамилия
                            @include('includes.inputs.name', ['name'=>'second_name', 'value'=>$user->second_name, 'required' => true])
                        </label>
                    </div>
                    <div class="small-12 cell">
                        <label>Отчество
                            @include('includes.inputs.name', ['name'=>'patronymic', 'value'=>$user->patronymic, 'required' => false])
                        </label>
                    </div>
                    <div class="small-12 cell">
                        <label>Телефон
                            <input type="text" value="{{ decorPhone($user->main_phone->phone) }}" readonly>
                        </label>
                    </div>
                    <div class="cell small-12">
                        <label>Почта
                            {!! Form::email('email', $user->email, ['class' => 'email-field', 'maxlength' => 30, 'autocomplete' => 'off', 'pattern' => '^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+.)+[a-z]{2,6}$']) !!}
                        </label>
                    </div>
                </div>
            </div>

            <div class="cell small-12 medium-6 large-4 fields-column">
                <div class="grid-x grid-padding-x">
                    <div class="cell small-6">
                        <label>Дата рождения
                            <pickmeup-component
                                name="birthday_date"
                                value="{{ $user->birthday_date }}"
                            ></pickmeup-component>
                        </label>
                    </div>
                    <div class="cell small-6">
                        <label>Укажите ваш пол
                            {!! Form::select('gender', [0 => 'Не указано', 1 => 'Мужской', 2 => 'Женский'], null) !!}
                        </label>
                    </div>

					<div class="small-12 medium-12 cell">
						<label>Опишите состояние здоровья и ограничения в питании (если такие есть):
						{{ Form::textarea('about', $user->about) }}
						</label><br>
					</div>

                    <div class="grid-x grid-padding-x">
                        @if($site->notifications->isNotEmpty())
                            @foreach($site->notifications as $notification)
                                <div class="small-12 cell checkbox checkbox-item">
                                    {{ Form::checkbox('notifications[]', $notification->id, null, ['id' => 'checkbox-notification-' . $notification->id]) }}
                                    <label for="checkbox-notification-{{ $notification->id }}">
                                        <span>{{ $notification->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        @endif
                        {{--			                    <div class="small-12 cell checkbox checkbox-item">--}}
                        {{--			                        {!! Form::hidden('is_allow', 0) !!}--}}
                        {{--			                        {!! Form::checkbox('is_allow', 1, isset($user->subscriber) ? empty($user->subscriber->denied_at) : true, ['id' => 'checkbox-allow']) !!}--}}
                        {{--			                        <label for="checkbox-allow">--}}
                        {{--			                            <span>Согласен (на) Email на рассылку</span>--}}
                        {{--			                        </label>--}}
                        {{--			                    </div>--}}
                    </div>
                    <div class="small-12 cell wrap-submit-button">
                        <input class="button" type="submit" id="update-profile" value="Сохранить">
                    </div>

                </div>
            </div>

            <div class="cell small-12 medium-12 large-4 fields-column">

            </div>

        </div>
        {!! Form::close() !!}
    </main>
</div>
