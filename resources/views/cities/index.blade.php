@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты меню в шапке --}}
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', $page_info->name)

@section('content-count')
{{-- Количество элементов --}}
{{ !empty($count) ? $count : 0 }}
@endsection

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\City::class, 'type' => 'menu'])
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
    <div class="small-12 cell">
        <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="cities">

            @if($regions->isNotEmpty())
            @include('cities.cities_list', $regions)
            @else
            <li class="empty-item"></li>
            @endif

        </ul>
    </div>
</div>
@endsection

@section('modals')
{{-- Модалка добавления города --}}
<div class="reveal rev-large" id="modal-create" data-reveal>
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>ДОБАВЛЕНИЕ НАСЕЛЕННОГО ПУНКТА</h5>
        </div>
    </div>
    {{ Form::open(['id' => 'form-add']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
        <div class="small-10 medium-4 cell">
            <label class="input-icon">Название населенного пункта
                {{-- Form::text('city_name', null, ['class'=>'varchar-field', 'id'=>'city-name-field' 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}', 'required']) --}}
                {!! Form::text('city_name', null, ['id' => 'city-name-field', 'autocomplete' => 'off', 'pattern' => '[А-Яа-я0-9-_\s]{3,30}', 'required']) !!}
                <div class="sprite-input-right find-status"></div>
                <div class="item-error">Такой населенный пункт уже существует!</div>
                <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
            </label>
            <label>Район
                {!! Form::text('area_name', null, ['id' => 'area-name', 'pattern' => '[А-Яа-яЁё0-9-_\s]{3,30}', 'readonly']) !!}
            </label>
            <label>Область
                {!! Form::text('region_name', null, ['id'=>'region-name', 'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}', 'readonly']) !!}
            </label>
            <div class="small-12 cell checkbox">
                {!! Form::checkbox('search_all', null, null, ['id' => 'search-all-checkbox']) !!}
                <label for="search-all-checkbox">
                    <span class="search-checkbox">Искать везде</span>
                </label>
            </div>
            {!! Form::hidden('vk_external_id', null, ['id' => 'city-id-field', 'pattern' => '[0-9]{1,20}']) !!}
            {!! Form::hidden('city_db', 0, ['id' => 'city-db', 'pattern' => '[0-9]{1}']) !!}
        </div>
        <div class="small-12 medium-8 cell">
            <table class="table-content-search">
              <caption>Результаты поиска в сторонней базе данных:</caption>
              <tbody id="tbody-city-add">
              </tbody>
          </table>
      </div>
  </div>
  <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-add', 'disabled']) }}
    </div>
</div>
{{ Form::close() }}
<div data-close class="icon-close-modal sprite close-modal"></div>
</div>
{{-- Конец модалки добавления города и района --}}

{{-- Модалка удаления ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
{{-- Скрипт модалки удаления ajax --}}
@include('includes.scripts.delete-ajax-script')

{{-- Маска ввода --}}
@include('includes.scripts.inputs-mask')

{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')
<script type="text/javascript">
    $(function() {

        // Обозначаем таймер для проверки
        var timerId;
        var time = 400;

        // Функция получения городов из вк или с фильтром по нашей базе
        function getCityVk () {
            $('.find-status').removeClass('icon-find-ok');

            // alert($('#search-all-checkbox').prop('checked'));
            // Сам ajax запрос
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/admin/city_vk",
                type: "POST",
                data: {city: $('#city-name-field').val(), search_all: $('#search-all-checkbox').prop("checked")},
                beforeSend: function () {
                    $('.find-status').addClass('icon-load');
                },
                success: function(html){
          	        // alert(date);
                    $('.find-status').removeClass('icon-load');
                    // Вставляем
                    $('#tbody-city-add').html(html);
                }
            });
        };
        // Функция появления окна с ошибкой
        function showError (msg) {
            var error = "<div class=\"callout item-error\" data-closable><p>" + msg + "</p><button class=\"close-button error-close\" aria-label=\"Dismiss alert\" type=\"button\" data-close><span aria-hidden=\"true\">&times;</span></button></div>";
            return error;
        };

        // Отображение городов из api vk
        $(document).on('keyup', '#city-name-field', function() {
            $('.item-error').css('display', 'none');
            $('#city-db').val(0);
            $('#submit-add').prop('disabled', true);
            $('#area-name').val('');
            $('#region-name').val('');
            // Если символов больше 2 - делаем запрос
            if($('#city-name-field').val().length > 2) {
                // Выполняем запрос
                clearTimeout(timerId);
                timerId = setTimeout(function() {
                    getCityVk ();
                }, time);
            } else {
                // Удаляем все значения, если символов меньше 2х
                $('#tbody-city-add').html('');
                $('#city-db').val(0);
                // $('#form-add')[0].reset();
                $('#city-id-field').val('');
                $('#area-name').val('');
                $('#region-name').val('');
                $('.item-error').css('display', 'none');
                $('.city-error').remove();
                $('.find-status').removeClass('icon-find-ok').removeClass('icon-find-no');
            };
        });

        // Отправляем запрос при клике на чекбокс
        $(document).on('change', '#search-all-checkbox', function() {
            // Если символов больше 2 - делаем запрос
            if($('#city-name-field').val().length > 2) {
                // Выполняем запрос
                clearTimeout(timerId);
                timerId = setTimeout(function() {
                    getCityVk ();
                }, time);
            };
        });

        // При клике на город в модальном окне заполняем инпуты
        $(document).on('click', '.city-add', function() {

            $('#city-id-field').val($(this).closest('tr').data('tr'));
            $('#city-name-field').val($(this).closest('tr').find('.city-name').text());
            $('#area-name').val($(this).closest('tr').find('.area-name').text());
            $('#region-name').val($(this).closest('tr').find('.region-name').text());

            // Выполняем запрос
            clearTimeout(timerId);

            timerId = setTimeout(function() {

                if($('#city-id-field').val() != '') {

                    // Ajax
                    $.post( "/admin/city_check", $('#form-add').serialize(), function (data) {
                        // alert(data);
                        // Город не существует
                        if (data == 0) {
                            $('#city-db').val(1);
                            $('#submit-add').prop('disabled', false);
                            $('.item-error').css('display', 'none');
                            $('.find-status').addClass('icon-find-ok');

                        } else {
                            // Город существует
                            $('#submit-add').prop('disabled', true);
                            $('#city-database').val(0);
                            $('.item-error').css('display', 'block');
                        };

                    });
                };
            }, 200);
        });

        // Добавляем город
        $(document).on('click', '#submit-add', function(event) {
            event.preventDefault();

            $.post('/admin/cities', $(this).closest('#form-add').serialize(), function(html){
                $('#content').html(html);
                Foundation.reInit($('#content'));
            });
        });

        // При закрытии модалки очищаем поля
        $(document).on('click', '.close-modal, #submit-add', function() {
            $('#tbody-city-add>tr').remove();
            $('#form-add')[0].reset();
            $('#city-db').val(0);
        });

        // При закрытии окна с ошибкой очищаем модалку
        $(document).on('click', '.error-close', function() {
            $('.item-error').remove();
            $('#tbody-city-add>tr').remove();
            $('#tbody-region-add>tr').remove();
            $('#form-add')[0].reset();
            $('#city-db').val(0);
            // $('#city-name-field').val('');
            // $('#region-name-field').val('');
            // $('#area-name').val('');
            // $('#region-name').val('');
            $('.find-status').removeClass('icon-find-ok');
            $('.find-status').removeClass('icon-find-no');
        });
    });
</script>
@endsection