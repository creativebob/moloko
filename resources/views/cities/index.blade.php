@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты меню в шапке --}}
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', $page_info->name)

@section('content-count')
{{-- Количество элементов --}}
{{ isset($count) ? num_format($count, 0) : 0 }}
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
            {{-- @else
            <li class="empty-item"></li> --}}
            @endif

        </ul>
    </div>
</div>

<div id="modals"></div>
@endsection

{{-- @section('modals')
Модалка удаления ajax
@include('includes.modals.modal-delete-ajax')
@endsection --}}

@push('scripts')
{{-- Скрипт модалки удаления ajax --}}
{{-- @include('includes.scripts.delete-ajax-script') --}}

{{-- Маска ввода --}}
@include('includes.scripts.inputs-mask')

{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox_control_menu')

<script type="application/javascript">
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
                data: {
                    country_id: $('#select-countries').val(),
                    city: $('#city-name-field').val(),
                    search_all: $('#search-all-checkbox').prop("checked")
                },
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

        // ----------- Добавление -------------
        $(document).on('click', '[data-open="modal-create"]', function() {
            $.get('/admin/cities/create', function(html) {
                $('#modals').html(html).foundation();
                $('#modal-create').foundation('open');
            });
        });

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

        // Отправляем запрос при изменении списка стран
        $(document).on('change', '#select-countries', function() {
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
            $(this).prop('disabled', true);
            var form = $(this).closest('form');

            $.post('/admin/cities', $(this).closest('#form-add').serialize(), function(html){
                form.closest('.reveal-overlay').remove();
                $('#content').html(html);
                Foundation.reInit($('#content'));
            });
        });

        // Закрытие модалки
        $(document).on('click', '.remove-modal', function(event) {
            $(this).closest('.reveal-overlay').remove();
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
@endpush