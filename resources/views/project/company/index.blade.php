@extends('project.layouts.app')

@section('title')
<title>{{ $page->title }} | Воротная компания "Марс"</title>
<meta name="description" content="{{ $page->description }} ">
@endsection

@section('content')
<div class="wrap-main grid-x grid-padding-x">
    <h1 class="index">Безупречное производство и профессиональный монтаж <br>автоматических&nbsp;ворот</h1>
    <div class="bg cls cell small-12 medium-12 large-12">
        <div id="wrap-for-img-main" class="hide-for-small-only">
            <div class="myslider">
                <ul>

                    <li>
                        <div class="style-opacity"></div>
                        <img src="{{ asset('/project/img/slider/slider-calc-logo.jpg') }}" alt="Рассчитать стоимость">
                        <div class="advertise">
                            <div>
                                <fieldset>
                                    <a href="/garage_gates#cost-video" class="more" style="margin-top: 320px; padding: 6px 45px 10px 45px; background-color: #ff6600;">Рассчитать стоимость</a>
                                </fieldset>
                            </div>
                        </div>
                    </li>

                    {{-- <li>
                        <div class="style-opacity"></div>
                        <img src="{{ asset('/project/img/slider/balka.jpg') }}" alt="Балка со склада">
                        <div class="advertise">
                            <div>
                                {{ Form::open(['url' => '/'.$city.'/sending', 'data-abide']) }}
                                <fieldset>
                                    <label>
                                        {{ Form::text('phone', null, ['required', 'class'=>'phone-field cls-marg-all', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'placeholder'=>'Ваш телефон']) }}
                                    </label>
                                    {{ Form::submit('Позвоните мне!', ['class'=>'button small right']) }}
                                    <a href="/street_gate_set" class="more">Узнать побольше</a>
                                </fieldset>
                                <fieldset>
                                    {{ Form::hidden('name', 'Имя не указано') }}
                                    {{ Form::hidden('remark', 'Меня интересует консольная балка') }}
                                    {{ Form::hidden('form', 'form-call') }}

                                </fieldset>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </li> --}}

                    <li>
                        <div class="style-opacity"></div>
                        <img src="{{ asset('/project/img/slider/slider-7.jpg') }}" alt="">
                        <div class="advertise">
                            <a href="/measurement">Запланировать замер</a>
                            <b>Высокая точность!</b>
                        </div>
                    </li>

                  {{--   <li>
                        <div class="style-opacity-none"></div>
                        <img src="{{ asset('/project/img/slider/slider-6.jpg') }}" alt="Скидка на монтаж 50%">
                        <div class="advertise">
                            <div>
                                {{ Form::open(['url' => '/'.$city.'/sending', 'data-abide']) }}
                                <fieldset>
                                    <label>Телефон:
                                        {{ Form::text('phone', null, ['required', 'class'=>'phone-field', 'pattern'=>'8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}']) }}
                                    </label>
                                </fieldset>
                                <fieldset>
                                    {{ Form::hidden('name', 'Имя не указано') }}
                                    {{ Form::hidden('remark', 'Меня интересует акция "Скидка на монтаж"') }}
                                    {{ Form::hidden('form', 'form-call') }}
                                    {{ Form::submit('Позвоните мне!', ['class'=>'button small right']) }}
                                </fieldset>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </li> --}}

                </ul>
            </div>
            <div class="sliderbut-l"></div>
            <div class="sliderbut-r"></div>
        </div>
        <h2 class="strong-h2">Уверенный лидер отрасли <br><span>2014 - @php echo date("Y"); @endphp</span></h2>

        <div class="grid-x">
            <div class="cell small-12 utp-block">
                <p class="utp-text">Экономия до 40% за счет соблюдения технологий и внимания к мелочам. <br>Мы даем индивидуальные рекомендации по снижению стоимости на замере.
                </p>
                <a href="/measurement" class="button small centered">Запланировать замер</a>
            </div>
        </div>

        <ul class="grid-x small-up-1 medium-up-3 list-gerb">
            <li class="cell">
                100%<br>качество&nbsp;ваших<br>ворот
                <div class="list-item-gerb">
                    <p>Крупнейший в Иркутске поставщик гаражных секционных ворот российского завода DoorHan</p>
                    <ul>
                        <li>Официальный партнер Doorhan</li>
                        <li>Разработано для Сибирского климата</li>
                        <li>Надежная упаковка и бережная доставка</li>
                        <li>Срок изготовления от 1 дня</li>
                    </ul>
                </div>
            </li>
            <li class="cell">
                Гарантированно<br>низкие<br>цены
                <div class="list-item-gerb">
                    <p>Лидер по производству откатных и распашных ворот в Иркутской области</p>
                    <ul>
                        <li>Работаем без посредников</li>
                        <li>Собственное производство и монтажная служба в Иркутске</li>
                        <li>Производство конкретно под ваши размеры</li>
                        <li>Крупнейший поставщик в регионе</li>
                    </ul>
                </div>
            </li>
            <li class="cell">
                Фокус<br>на&nbsp;качестве<br>монтажа
                <div class="list-item-gerb">
                  <p>Профессиональная установка ворот сертифицированными специалистами</p>
                  <ul>
                    <li>Опыт монтажа более чем 7000 ворот</li>
                    <li>Даем рекомендации по подготовке проемов</li>
                    <li>Соблюдаем нормативы параметров монтажа</li>
                    <li>Проводим инструктаж по эксплуатации</li>
                </ul>
            </div>
        </li>
    </ul>
</div>


<section class="cell small-12 cont-section">
    <h2>Наши клиенты</h2>
    <div class="grid-x align-center">
        <div class="cell small-12 medium-10 large-8">
            <img class="thumbnail" src="{{ asset('/projec/img/clients/111.jpg') }}" alt="Довольный клиент">
        </div>
    </div>
</section>

<section class="cell small-12 cont-section">
    <h2>Нам доверяют</h2>
    <ul class="grid-x grid-margin-x gallery small-up-3 medium-up-4 large-up-5" data-equalizer data-equalize-by-row="true">
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/audi.jpg') }}" alt="Audi">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/binbank.jpg') }}" alt="Бинбанк">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/bmw.jpg') }}" alt="БМВ">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/fortuna.jpg') }}" alt="Фортуна">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/gm.jpg') }}" alt="Gregory Motors">
        </li>

        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/wv.jpg') }}" alt="Volkswagen">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/grstr.jpg') }}" alt="ГрандСтрой">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/irkut.jpg') }}" alt="Иркут">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/mav.jpg') }}" alt="Мир Автомасел">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/ostin.jpg') }}" alt="Остин">
        </li>

        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/port.jpg') }}" alt="Международный аэропорт Иркутск">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/shp.jpg') }}" alt="ГрандСтрой">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/skoda.jpg') }}" alt="Шкода">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/subaru.jpg') }}" alt="Субару">
        </li>
        <li class="cell">
            <img class="thumbnail" src="{{ asset('/project/img/trust/tele2.jpg') }}" alt="Tele2">
        </li>

    </ul>
</section>
<section id="callme" class="cell small-12 cont-section" data-magellan-target="callme">
    <h2>Заказать звонок</h2>
    <div class="grid-x align-center">
        <div class="cell small-12 medium-8 large-6 cls">
            @include('project.includes.forms.call', ['remark' => 'Будьте любезны, перезвоните мне! (Сообщение с ГЛАВНОЙ страницы)', 'category_id' => null])
        </div>
    </div>
</section>
</div>
@endsection