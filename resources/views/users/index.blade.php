@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('site-section-index', $site, $page_info))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($users))
    {{ num_format($users->total(), 0) }}
  @endif
@endsection

@section('title-content')
    {{-- Таблица --}}
    {{-- Заголовок и фильтры --}}
    <div data-sticky-container id="head-content">
        <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
            <div class="top-bar head-content">
                <div class="top-bar-left">
                    <h2 class="header-content">{{ $page_info->title }}
                        <span class="content-count" title="Общее количество">
                        {{ $users->isNotEmpty() ? num_format($users->total(), 0) : 0 }}
                    </span>
                    </h2>

                    @can('create', App\User::class)

                        {{ link_to_route($page_info->alias.'.create', '', $parameters = ['site_id' => $site_id], $attributes = ['class' => 'icon-add sprite']) }}

                    @endcan
                </div>
                <div class="top-bar-right">
                    @if (isset($filter))
                        <a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
                    @endif

                    <input class="search-field" type="search" id="search_field" name="search_field" placeholder="Поиск" />

                    <button type="button" class="icon-search sprite button"></button>
                </div>

            </div>

            <div id="port-result-search">
            </div>
            {{-- Подключаем стандартный ПОИСК --}}
            @include('includes.scripts.search-script')

            {{-- Блок фильтров --}}
            @if (isset($filter))

                {{-- Подключаем класс Checkboxer --}}
                @include('includes.scripts.class.checkboxer')

                <div class="grid-x">
                    <div class="small-12 cell filters fieldset-filters" id="filters">
                        <div class="grid-padding-x">
                            <div class="small-12 cell text-right">
                                {{ link_to(Request::url() . '?filter=disable', 'Сбросить', ['class' => 'small-link']) }}
                            </div>
                        </div>
                        <div class="grid-padding-x">
                            <div class="small-12 cell">
                                {{ Form::open(['url' => Request::url(), 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                                @include($page_info->alias.'.filters')

                                <div class="small-12 cell text-center">
                                    {{ Form::submit('Фильтрация', ['class'=>'button']) }}
                                    <input hidden name="filter" value="active">
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <div class="grid-x">
                            <a class="small-12 cell text-center filter-close">
                                <button type="button" class="icon-moveup sprite"></button>
                            </a>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="users">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-second-name">Пользователь</th>
          <th class="td-login">Логин</th>
          <!--           <th class="td-first-name">Имя</th> -->
          <th class="td-phone">Телефон</th>
          <th class="td-email">Почта</th>
          <th class="td-contragent-status">Статус</th>
          <th class="td-staffer">Должность</th>
            @if(extra_right('auth-under-user'))<th class="td-getauth">Действие</th> @endif
          <th class="td-access-block">Доступ</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($users))

        @foreach($users as $user)
        <tr class="item @if($user->moderation == 1)no-moderation @endif" id="users-{{ $user->id }}" data-name="{{ $user->first_name.' '.$user->second_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">

            <input type="checkbox" class="table-check" name="user_id" id="check-{{ $user->id }}"
            @if(!empty($filter['booklist']['booklists']['default']))
            @if (in_array($user->id, $filter['booklist']['booklists']['default'])) checked
            @endif
            @endif
            ><label class="label-check" for="check-{{ $user->id }}"></label>
          </td>
          <td class="td-name">

              @can('update', $user)
                  <a href="{{ route('users.edit', [$site_id, $user->id]) }}">{{ $user->name ?? "Имя не указано" }}</a>
              @else
                  {{ $user->name ?? "Имя не указано" }}
              @endcan


          </td>
          <td class="td-login">{{ $user->login }}</td>

          <td class="td-phone">{{ isset($user->main_phone->phone) ? decorPhone($user->main_phone->phone) : 'Телефон не указан' }}</td>
          <td class="td-email">{{ $user->email }}</td>
          <td class="td-contragent-status">{{ decor_user_type($user->user_type) }}</td>
          <td class="td-staffer">@if(!empty($user->staff->first()->position->name)) {{ $user->staff->first()->position->name }} @endif</td>

            @if(extra_right('auth-under-user') && !empty($user->company_id))
                <td class="td-getauth">
                    {{ link_to_route('users.getauthuser', "Авторизоваться", ['user_id' => $user->id], ['class' => "tiny button"]) }}
                </td>
            @endif

          <td class="td-access-block">{{ decor_access_block($user->access_block) }}</td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $user])

          <td class="td-delete">
            @if (($user->system != 1) && ($user->god != 1))
            @can('delete', $user)
            <a class="icon-delete sprite" data-open="item-delete"></a>
            @endcan
            @endif
          </td>
        </tr>
        @endforeach
        @endif
      </tbody>
    </table>
  </div>
</div>

{{-- Pagination --}}
<div class="grid-x" id="pagination">
  <div class="small-6 cell pagination-head">
    <span class="pagination-title">Кол-во записей: {{ $users->count() }}</span>
    {{ $users->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления с refresh --}}
{{--@include('includes.modals.modal-delete-ajax')--}}

@endsection

@section('scripts')
{{-- Скрипт сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

<script type="application/javascript">

    $(function() {

        // Берем алиас сайта
        var site_id = '{{ $site_id }}';

        // Мягкое удаление с refresh
        $(document).on('click', '[data-open="item-delete"]', function() {

            // находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var type = parent.attr('id').split('-')[0];
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');
            $('.title-delete').text(name);
            $('.delete-button').attr('id', 'del-' + type + '-' + id);
            $('#form-item-del').attr('action', '/admin/sites/'+ site_id + '/' + type + '/' + id);
        });
    });

</script>

{{-- Скрипт модалки удаления --}}
{{--@include('includes.scripts.modal-delete-script')--}}
{{--@include('includes.scripts.delete-ajax-script')--}}

@endsection
