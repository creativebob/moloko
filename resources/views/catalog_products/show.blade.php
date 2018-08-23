@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('section', $parent_page_info, $site, $page_info))

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => null, 'type' => 'menu'])
@endsection

@section('control-content')
<div class="grid-x grid-padding-x">
    <div class="small-12 cell inputs">

        <div class="grid-x grid-margin-x">
            @if($catalog->site->company->sites_count > 1)
            <div class="small-12 medium-6 cell">
                

            </div>
            @endif

            <div class="small-12 medium-6 cell">
                <label>Каталоги
                    <select name="catalog_id" id="catalogs-list">
                        @php
                        echo $catalogs_list;
                        @endphp
                    </select>
                    
                </label>
            </div>

        </div>

    </div>
</div>
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">
        <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="services">
            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-name">Название продукции</th>
                    <th class="td-type">Тип продукции</th>
                    <th class="td-cost">Цена</th>

                    @if(Auth::user()->god == 1) 
                    <th class="td-company-id">Компания</th>
                    @endif

                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>
            <tbody data-tbodyId="1" class="tbody-width">
                @if(!empty($catalog))

                @if (count($catalog->services) > 0)
                @foreach ($catalog->services as $service)
                <tr class="item @if($service->moderation == 1)no-moderation @endif" id="catalog_products-{{ $service->pivot->id }}" data-name="{{ $service->services_article->name }}">
                    <td class="td-drop"><div class="sprite icon-drop"></div></td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="service_id" id="check-{{ $service->id }}"
                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($service->id, $filter['booklist']['booklists']['default'])) checked 
                        @endif
                        @endif
                        >
                        <label class="label-check" for="check-{{ $service->id }}"></label>
                    </td>

                    <td class="td-name"><a href="/admin/services/{{ $service->id }}/edit">{{ $service->services_article->name }}</a></td>
                    <td class="td-type">Услуга</td>

                    <td class="td-cost">{{ $service->price }}</td>

                    @if(Auth::user()->god == 1) 
                    <td class="td-company-id">@if(!empty($service->company->name)) {{ $service->company->name }} @else @if($service->system_item == null) Шаблон @else Системная @endif @endif</td>
                    @endif

                    <td class="td-author">@if(isset($service->author->first_name)) {{ $service->author->first_name . ' ' . $service->author->second_name }} @endif</td>

                    {{-- Элементы управления --}}
                    <td class="td-control">

                        {{-- Отображение на сайте --}}
                        @can ('display', App\CatalogProduct::class)
                        @display ($service->pivot)
                        <div class="icon-display-show black sprite" data-open="item-display"></div>
                        @else
                        <div class="icon-display-hide black sprite" data-open="item-display"></div>
                        @enddisplay
                        @endcan

                    </td>

                    <td class="td-delete">
                        @if ($service->system_item != 1)
                        @can('delete', $service)
                        <a class="icon-delete sprite" data-open="item-delete"></a>
                        @endcan
                        @endif
                    </td>       
                </tr>

                @endforeach
                @endif

                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('modals')
<section id="modal"></section>

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

@endsection

@section('scripts')

{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')

@include('includes.scripts.inputs-mask')
@include('catalog_products.scripts')

<script type="text/javascript">

    var alias = '{{ $site->alias }}';
    // Мягкое удаление с refresh
    $(document).on('click', '[data-open="item-delete"]', function() {
        // находим описание сущности, id и название удаляемого элемента в родителе
        var parent = $(this).closest('.item');
        var type = parent.attr('id').split('-')[0];
        var id = parent.attr('id').split('-')[1];
        var name = parent.data('name');
        $('.title-delete').text(name);
        $('.delete-button').attr('id', 'del-' + type + '-' + id);
        $('#form-item-del').attr('action', '/admin/sites/'+ alias + '/' + type + '/' + id);
    });


    $(document).on('change', '#catalogs-list', function(event) {
        event.preventDefault();
        window.location = "/admin/sites/" + alias + "/catalog_products/" + $(this).val();

    });


    // Обозначаем таймер для проверки
    // var timerId;
    // var time = 400;

    // // Первая буква заглавная
    // function newParagraph (name) {
    //   name = name.charAt(0).toUpperCase() + name.substr(1).toLowerCase();
    //   return name;
    // };

    // ------------------- Проверка на совпадение имени --------------------------------------
    // function serviceCheck (name, submit, db) {

    //   // Блокируем аттрибут базы данных
    //   $(db).val(0);

    //   // Смотрим сколько символов
    //   var lenname = name.length;

    //   // Если символов больше 3 - делаем запрос
    //   if (lenname > 3) {

    //     // Первая буква сектора заглавная
    //     name = newParagraph (name);

    //     // Сам ajax запрос
    //     $.ajax({
    //       headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //       },
    //       url: "/admin/service_check",
    //       type: "POST",
    //       data: {name: name},
    //       beforeSend: function () {
    //         $('.find-status').addClass('icon-load');
    //       },
    //       success: function(date){
    //         $('.find-status').removeClass('icon-load');
    //         var result = $.parseJSON(date);
    //         // Если ошибка
    //         if (result.error_status == 1) {
    //           $(submit).prop('disabled', true);
    //           $('.item-error').css('display', 'block');
    //           $(db).val(0);
    //         } else {
    //           // Выводим пришедшие данные на страницу
    //           $(submit).prop('disabled', false);
    //           $('.item-error').css('display', 'none');
    //           $(db).val(1);
    //         };
    //       }
    //     });
    //   };
    //   // Удаляем все значения, если символов меньше 3х
    //   if (lenname <= 3) {
    //     $(submit).prop('disabled', false);
    //     $('.item-error').css('display', 'none');
    //     $(db).val(0);
    //   };
    // };

    // ---------------------------- Продукция -----------------------------------------------

    // ----------- Добавление -------------
    // Открываем модалку
    $(document).on('click', '[data-open="first-add"]', function() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/services/create',
            type: "GET",
            success: function(html){
                $('#modal').html(html);
                $('#first-add').foundation();
                $('#first-add').foundation('open');
            }
        }); 
    });

    // Проверка существования
    // $(document).on('keyup', '#form-first-add .name-field', function() {

    //   // Получаем фрагмент текста
    //   var name = $('#form-first-add .name-field').val();

    //   // Указываем название кнопки
    //   var submit = '.modal-button';

    //   // Значение поля с разрешением
    //   var db = '#form-first-add .first-item';

    //   // Выполняем запрос
    //   clearTimeout(timerId);   
    //   timerId = setTimeout(function() {
    //     serviceCheck (name, submit, db)
    //   }, time); 
    // });

    $(document).on('click', '.close-modal', function() {
      // alert('lol');
      $('.reveal-overlay').remove();
  });
</script>
@endsection
