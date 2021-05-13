<div class="grid-x grid-padding-x">
    <main class="cell small-12 main-content">
        {!! Form::model($user, ['route' => 'project.user.update', 'data-abide', 'novalidate', 'id'=>'profile-form']) !!}

        {{-- Заголовок --}}
        @include('mwtour.pages.common.title')

        <ul class="tabs tab-profile" data-tabs id="example-tabs">
            <li class="tabs-title is-active"><a href="#panel1" aria-selected="true">Мои туры</a></li>
            <li class="tabs-title"><a data-tabs-target="panel2" href="#panel2">Мой профиль</a></li>
        </ul>

        <div class="tabs-content" data-tabs-content="example-tabs">
            <div class="tabs-panel is-active" id="panel1">
                <div class="grid-x">
                    <div class="cell small-12">
                        <ul class="my-tours-list">

                            {{-- <li>
                                <div class="grid-x">
                                    <div class="small-12 medium-5 wrap-my-tour-img cell">
                                        <img src="/img/mwtour/services/1.jpg" class="service_photo" alt="" title="">
                                    </div>
                                    <div class="small-12 cell medium-7 wrap-my-tour-info">
                                        <span>Бронь №</span><span>000388</span>
                                        <h2>Байкальский трип</h2>
                                        <span>Стартуем </span><span>15 июля 2021</span>
                                    </div>
                                </div>
                            </li> --}}

                        </ul>
                    </div>
                    <div class="cell small-12">

                    </div>

                </div>

            </div>

            <div class="tabs-panel" id="panel2">
                <div class="grid-x">

                    <div class="cell small-12 medium-4 fields-column">
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

                    <div class="cell small-12 medium-4 fields-column">
                        <div class="grid-x">
                            <div class="cell small-12">
                                <label>Дата рождения
                                    <pickmeup-component
                                        name="birthday_date"
                                        value="{{ $user->birthday_date }}"
                                    ></pickmeup-component>
                                </label>
                            </div>
                            <div class="cell small-12">
                                <label>Укажите ваш пол
                                    {!! Form::select('gender', [0 => 'Не указано', 1 => 'Мужской', 2 => 'Женский'], null) !!}
                                </label>
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

                    <div class="cell small-12 medium-4 fields-column">

                    </div>

                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </main>
</div>
