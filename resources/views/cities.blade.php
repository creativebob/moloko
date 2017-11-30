@extends('layouts.app')
 
@section('inhead')

@endsection

@section('title', 'Страницы')

@section('title-content')
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-options="stickyOn: small;" data-top-anchor="head-content:top">
    <div class="top-bar head-content">
      <div class="top-bar-left">
        <h2 class="header-content">НАСЕЛЕННЫЕ ПУНКТЫ</h2>
        <a class="icon-add sprite" data-open="region-add"></a>
      </div>
      <div class="top-bar-right">
        <a class="icon-filter sprite"></a>
        <input class="search-field" type="search" name="search-field" placeholder="Поиск" />
        <button type="button" class="icon-search sprite button"></button>
      </div>
    </div>
    {{-- Блок фильтров --}}
	  <div class="grid-x">
      <div class="small-12 cell filters" id="filters">
        <fieldset class="fieldset-filters">
          <legend>Фильтрация</legend>
          <div>lol</div>
          <div>lol</div>
          <div>lol</div>
          <div>lol</div>
        </fieldset>
      </div>
    </div>
  </div>
</div>
@endsection
 
@section('content')
{{-- Список --}}
<div class="grid-x">
  <div class="small-12 cell">
    <ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
      @foreach ($dates as $data) 
      <li class="first-item">
        <ul class="icon-list">
          <li><div class="icon-list-add sprite" data-open="city-add"></div></li>
          <li><div class="icon-list-edit sprite" data-open="region-edit"></div></li>
          <li><div class="icon-list-delete sprite" data-open="region-del"></div></li>
        </ul>
        <a data-list="{{ $data->region_id }}" class="first-link">
          <div class="list-title">
            <div class="icon-open sprite"></div>
            <span>{{ $data->region_name }}</span><span class="number">4</span>
          </div>
        </a>
      <!--   <ul class="menu vertical medium accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
          <li class="medium-item">
            <a class="medium-link" data-list-link="3">
              <div class="list-title">
                <div class="icon-open sprite"></div>
                <span>{{ $data->area_name }}</span><span class="number">10</span>
              </div>
            </a>
            <ul class="icon-list">
              <li><div class="icon-list-add sprite" data-open="add"></div></li>
              <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
              <li><div class="icon-list-delete sprite" data-open="del"></div></li>
            </ul>
          </li>
        </ul> -->
      </li>
      @endforeach



      <li class="first-item">
        <ul class="icon-list">
          <li><div class="icon-list-add sprite" data-open="add"></div></li>
          <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
          <li><div class="icon-list-delete sprite" data-open="del"></div></li>
        </ul>
        <a data-list="0" class="first-link">
          <div class="list-title">
            <div class="icon-open sprite"></div>
            <span>Тестовая область</span><span class="number">4</span>
          </div>
        </a>
        <ul class="menu vertical medium accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
          <li class="medium-item">
            <a class="medium-link" data-list-link="3">
              <div class="list-title">
                <div class="icon-open sprite"></div>
                <span>Ангарский район</span><span class="number">10</span>
              </div>
            </a>
            <ul class="icon-list">
              <li><div class="icon-list-add sprite" data-open="add"></div></li>
              <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
              <li><div class="icon-list-delete sprite" data-open="del"></div></li>
            </ul>
          </li>
          <li class="medium-item">
            <a class="medium-link" data-list-link="4">
              <div class="list-title">
                <div class="icon-open sprite"></div>
                <span>Зиминский район</span><span class="number">2</span>
              </div>
            </a>
            <ul class="icon-list">
              <li><div class="icon-list-add sprite" data-open="add"></div></li>
              <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
              <li><div class="icon-list-delete sprite" data-open="del"></div></li>
            </ul>
            <!-- Начало вложенного списка в середине -->

            <ul class="menu vertical medium nested accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
              <li class="medium-item">
                <a class="medium-link" data-list-link="5">
                  <div class="list-title">
                    <div class="icon-open sprite"></div>
                    <span>Ангарский район</span><span class="number">10</span>
                  </div>
                </a>
                <ul class="icon-list">
                  <li><div class="icon-list-add sprite" data-open="add"></div></li>
                  <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                  <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                </ul>
              </li>
              <li class="medium-item">
                <a class="medium-link" data-list-link="6">
                  <div class="list-title">
                    <div class="icon-open sprite"></div>
                    <span>Куйтунский район</span><span class="number">1</span>
                  </div>
                </a>
                <ul class="icon-list">
                  <li><div class="icon-list-add sprite" data-open="add"></div></li>
                  <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                  <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                </ul>
                <ul class="menu vertical nested last">
                  <li class="last-item">
                    <a class="last-link">Березовка
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Уян
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Сосновка
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Кимильтей
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Осиновка
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Андрюшино
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Хаихта
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Хаихта
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Хаихта
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Хаихта
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Хаихта
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Хаихта
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Хаихта
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  <li class="last-item">
                    <a class="last-link">Хаихта
                      <ul class="icon-list">
                        <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                        <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                      </ul>
                    </a>
                  </li>
                  
                </ul>
              </li>
              <li class="medium-item">
                <div class="medium-as-last">Иркутск
                  <ul class="icon-list">
                  <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                  <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                </ul>
                </div>
              </li>
              <li class="medium-item">
                <div class="medium-as-last">Ангарск
                  <ul class="icon-list">
                  <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                  <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                </ul>
                </div>
              </li>
              <li class="medium-item">
                <div class="medium-as-last">Усть-Илимск
                  <ul class="icon-list">
                  <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                  <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                </ul>
                </div>
              </li>
            </ul>

            <!-- Конец вложенного списка в середине -->
          </li>
          <li class="medium-item">
            <a href="#" class="medium-link" data-list-link="7">
              <div class="list-title">
                <div class="icon-open sprite"></div>
                <span>Куйтунский район</span><span class="number">1</span>
              </div>
            </a>
            <ul class="icon-list">
              <li><div class="icon-list-add sprite" data-open="add"></div></li>
              <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
              <li><div class="icon-list-delete sprite" data-open="del"></div></li>
            </ul>
            <ul class="menu vertical nested last">
              <li class="last-item">
                <div class="last-link">Березовка
                  <ul class="icon-list">
                    <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                    <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                  </ul>
                </div>
              </li>
              <li class="last-item">
                <div class="last-link">Уян
                  <ul class="icon-list">
                    <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                    <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                  </ul>
                </div>
              </li>
              <li class="last-item">
                <div class="last-link">Сосновка
                  <ul class="icon-list">
                    <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                    <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                  </ul>
                </div>
              </li>
              <li class="last-item">
                <div class="last-link">Кимильтей
                  <ul class="icon-list">
                    <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                    <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                  </ul>
                </div>
              </li>
              <li class="last-item">
                <div class="last-link">Осиновка
                  <ul class="icon-list">
                    <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                    <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                  </ul>
                </div>
              </li>
              <li class="last-item">
                <div class="last-link">Андрюшино
                  <ul class="icon-list">
                    <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                    <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                  </ul>
                </div>
              </li>
              <li class="last-item">
                <div class="last-link">Хаихта
                  <ul class="icon-list">
                    <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                    <li><div class="icon-list-delete sprite" data-open="del"></div></li>
                  </ul>
                </div>
              </li>
            </ul>
          </li>
          <li class="medium-item">
            <div class="medium-as-last">Иркутск
              <ul class="icon-list">
                <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                <li><div class="icon-list-delete sprite" data-open="del"></div></li>
              </ul>
            </div>
          </li>
          <li class="medium-item">
            <div class="medium-as-last">Ангарск
              <ul class="icon-list">
                <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                <li><div class="icon-list-delete sprite" data-open="del"></div></li>
              </ul>
            </div>
          </li>
          <li class="medium-item">
            <div class="medium-as-last">Усть-Илимск
              <ul class="icon-list">
                <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
                <li><div class="icon-list-delete sprite" data-open="del"></div></li>
              </ul>
            </div>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</div>
{{-- Pagination --}}
<div class="grid-x" id="pagination">
  <div class="small-12 cell">
    <div class="right">
      <a href="#"><div class="sprite icon-deleted"></div>6</a>
    </div>
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка добавления области --}}
<div class="reveal" id="region-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ Области</h5>
    </div>
  </div>
  <form action="/region" method="post" id="form-region">
    {{ csrf_field() }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-4 cell">
        <label>Название области
          <input type="text" name="region_name" id="region-name-field" autocomplete="off" required>
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        <input type="hidden" name="region_vk_external_id" id="region-id-field">
      </div>
      <div class="small-12 medium-8 cell">
        <table class="table-content-search">
          <caption>Результаты поиска в сторонней базе данных:</caption>
          <tbody id="tbody-region-add">
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        <button data-close class="button modal-button" id="submit-region" type="submit" disabled>Сохранить</button>
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки добавления области --}}

{{-- Модалка редактирования области --}}
<div class="reveal" id="region-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ Области</h5>
    </div>
  </div>
  <form action="/cities" method="post">
    {{ csrf_field() }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-4 cell">
        <label>Название области
          <input type="text" name="region_name" id="region-title-field" value="lol" autocomplete="off" required>
          <span class="form-error">Уж постарайтесь, введните хотя бы 3 символа!</span>
        </label>
        <input type="hidden" name="region_vk_external_id" id="region-id-field" value="lol">
      </div>
      <div class="small-12 medium-8 cell">
        <table class="table-content-search">
          <caption>Результаты поиска в сторонней базе данных:</caption>
          <tbody id="tbody-region-add">
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        <button class="button modal-button" id="submit-city" type="submit" disabled>Сохранить</button>
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки редактирования области --}}

{{-- Модалка добавления города и района --}}
<div class="reveal" id="city-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ НАСЕЛЕННОГО ПУНКТА</h5>
    </div>
  </div>
  <form action="/city" method="post">
    {{ csrf_field() }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-4 cell">
        <label>Название населенного пункта
          <input type="text" name="city_name" id="city-name-field" required>
          <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
        </label>
        <label>Район
          <input type="text" name="area_name" id="area-name">
        </label>
        <label>Область
          <input type="text" name="region_name" id="region-name">
        </label>
        <input type="hidden" name="city_vk_external_id" id="city-id-field">
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
        <a data-close class="button modal-button" id="submit-city" type="submit" disabled>Сохранить</a>
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки добавления --}}

{{-- Модалка редактирования --}}
<div class="reveal" id="edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>РЕДАКТИРОВАНИЕ НАСЕЛЕННОГО ПУНКТА</h5>
    </div>
  </div>
  <div class="grid-x grid-padding-x modal-content inputs">
    <div class="small-10 medium-4 cell">
      <label>Область
        <input type="text" name="" required>
        <span class="form-error">Уж постарайтесь, придумайте что-нибудь!</span>
      </label>
      <label>Район
        <input type="text" name="" required>
        <span class="form-error">Уж постарайтесь, придумайте что-нибудь!</span>
      </label>
    </div>
    <div class="small-12 medium-8 cell">
      <div class="grid-x grid-padding-x">
        <div class="small-10 medium-8 cell">
          <label>Название населенного пункта
            <input type="text" name="" required>
            <span class="form-error">Уж постарайтесь, придумайте что-нибудь!</span>
          </label>
        </div>
      </div>
      <table class="table-content-search">
        <caption>Результаты поиска в сторонней базе данных:</caption>
        <tbody id="tbody-content-search">
          <tr>
            <td><a href="#">Кимильтей</a></td>
            <td><a href="#">Куйтунский район</a></td>
            <td><a href="#">Иркутская область</a></td>
          </tr>
          <tr>
            <td><a href="#">Кимильтей</a></td>
            <td><a href="#">Куйтунский район</a></td>
            <td><a href="#">Иркутская область</a></td>
          </tr>
          <tr>
            <td><a href="#">Кимильтей</a></td>
            <td><a href="#">Куйтунский район</a></td>
            <td><a href="#">Иркутская область</a></td>
          </tr>
        </tbody>
      </table>
      <div class="grid-x ">
        <div class="small-6 small-centered cell">
          <a href="#" class="button modal-button">Сохранить</a>
        </div>
      </div>
    </div>
  </div>
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки редактирования --}}
@endsection

@section('scripts')
<script type="text/javascript">
// При клике на регион в модальном окне заполняем инпуты
function regionAdd (a) {
  var itemId = $(a).closest('tr').data('tr');
  var regionId = $('[data-region-vk-external-id="' + itemId + '"]').html();
  var regionName = $('[data-region-name="' + itemId + '"]').html();
  $('#region-id-field').val(regionId);
  $('#region-name-field').val(regionName);

  if($('#region-id-field').val() != '') {
    $('#submit-region').prop('disabled', false);
  };
};
// При клике на город в модальном окне заполняем инпуты
function cityAdd (a) {
  var itemId = $(a).closest('tr').data('tr');
  var cityId = $('[data-city-id="' + itemId + '"]').data('city-vk-external-id');
  var cityName = $('[data-city-id="' + itemId + '"]').html();
  var areaName = $('[data-area-id="' + itemId + '"]').html();
  var regionName = $('[data-region-id="' + itemId + '"]').html();
  $('#city-id-field').val(cityId);
  $('#city-name-field').val(cityName);
  $('#area-name').val(areaName);
  $('#region-name').val(regionName);

  if($('#city-id-field').val() != '') {
    $('#submit-city').prop('disabled', false);
  };
};
// function contentMenuFirstItemClick () {
// };

$(function() {
  // Присваиваем при клике на первый элемент списка активный класс
  $('.first-link').click(function() {
    if ($(this).parent('.first-item').hasClass('first-active')) {
    $(this).parent('.first-item').removeClass('first-active');
    $('.medium-active').removeClass('medium-active');
  } else {
    $('.content-list .first-active').removeClass('first-active');
    $(this).parent('.first-item').addClass('first-active');
    $('.medium-active').removeClass('medium-active');
  };
  });
  // Отслеживаем плюсики во вложенных элементах
  $('.medium-link').click(function() {
    console.log('Видим клик по среднему пункту');
    var link = $(this).data('list-link');
    if ($('[data-list-link="' + link + '"]').hasClass('medium-active')) {
      $(".medium-active").removeClass('medium-active');
      console.log('Видим что имеет medium-active');
      $('[data-list-link="' + link + '"]').removeClass('medium-active')
    } else {
      $(".medium-active").removeClass('medium-active');
      console.log('Видим что имеет не medium-active');
      $('[data-list-link="' + link + '"]').addClass('medium-active');
    };
    // Если пустое меню то закрываем остальные
    // if (($(this).next('ul').is('.last')) || ($(this).next('ul').is('.medium'))) {
    // // Непонятно почему в условии ($(this).next('ul').is('.last') == false) || ($(this).next('ul').is('.medium') == false) не отрабатывает
    // } else {
    //   console.log('Видим что пустой список и нужно нужно сворачивать остальные');
    //   $(this).parent('li').parent('.medium').foundation('hideAll');
    // };
    // Перебираем родителей и посвечиваем их
    var parents = $('.medium-link[data-list-link="' + link + '"]').parents('.medium');
    for (var i = 0; i < parents.length; i++) {
      var active = $(parents[i]).parent('li');
      $(active).children('a').addClass('medium-active');
    };
  });

  // Отображение области по ajax через api vk
  $('#region-name-field').keyup(function() {
    $('#submit-region').prop('disabled', true);
    // Получаем фрагмент текста
    var region = $('#region-name-field').val();
    // Смотрим сколько символов
    var lenRegion = region.length;
    // Если символов больше 3 - делаем запрос
    if (lenRegion > 3) {
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/region",
        type: "POST",
        data: "region=" + region,
        success: function (date) {
          var result = $.parseJSON(date);
          var count = result.response.count;
          var data = '';
          if (count >= 1) {
            // Удаляем все значения чтобы вписать новые
            $('#tbody-region-add>tr').remove();
            // Перебираем циклом
            for (var i = 0; i < count; i++) {
              data = data + "<tr data-tr=\"" + i + "\"><td><a onClick=\"regionAdd(this);\" data-region-vk-external-id=\"" + i + "\">" + result.response.items[i].id + "</a></td><td><a onClick=\"regionAdd(this);\" data-region-name=\"" + i + "\">" + result.response.items[i].title + "</a></td></tr>";
            };
            // Выводим пришедшие данные на страницу
            $('#tbody-region-add').append(data);
          };
        }
      });
    };
    if (lenRegion <= 3) {
      // Удаляем все значения, если символов меньше 3х
      $('#tbody-region-add>tr').remove();
      $('#region-id-field').val('');
    };
  });
  // Сохранияем область в базу и отображаем на странице по ajax   
  $('#submit-region').click(function (e) {
    //чтобы не перезагружалась форма
    e.preventDefault(); 
    // Дергаем все данные формы
    var formRegion = $('#form-region').serialize();
    // var region = {region_vk_external_id: $('#region-id-field').val(), region_name:$('#region-name-field').val()};
    // Ajax
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/regions",
      type: "POST",
      // data: region,
      data: formRegion,
      success: function (data) {
        var result = $.parseJSON(data);
        // alert(data);
        // $('#content-list>li:last').after(data);

          result = "<li class=\"first-item\"><ul class=\"icon-list\"><li><div class=\"icon-list-add sprite\" data-open=\"city-add\"></div></li><li><div class=\"icon-list-edit sprite\" data-open=\"region-edit\"></div></li><li><div class=\"icon-list-delete sprite\" data-open=\"region-del\"></div></li></ul><a onclick=\"contentMenuFirstItemClick (this);\" data-list=\"\" class=\"first-link\"><div class=\"list-title\"><div class=\"icon-open sprite\"></div><span>" + result.region_name + "</span><span class=\"number\">4</span></div></a></li>";
          // Выводим пришедшие данные на страницу
          $('#content-list>li:last').after(result);
          $('#content-list').foundation('_destroy');
          var elem = new Foundation.AccordionMenu($('#content-list'));
      }
    });
  });
  // Отображение города по ajax через api vk
  $('#city-name-field').keyup(function() {
    $('#submit-city').prop('disabled', true);
    // Получаем фрагмент текста
    var city = $('#city-name-field').val();
    // Смотрим сколько символов
    var lenCity = city.length;
    // Если символов больше 2 - делаем запрос
    if(lenCity > 2){
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/city",
        type: "POST",
        data: "city=" + city,
        success: function(date){
          var result = $.parseJSON(date);
          var count = result.response.count;
          var data = '';
          if (count >= 1) {
            // Удаляем все значения чтобы вписать новые
            $('#tbody-city-add>tr').remove();
            // Перебираем циклом
            for (var i = 0; i < count; i++) {
              data = data + "<tr data-tr=\"" + i + "\"><td><a onClick=\"cityAdd(this);\" data-city-id=\"" + i + "\" data-city-vk-external-id=\"" + result.response.items[i].id + "\">" + result.response.items[i].title + "</a></td><td><a onClick=\"cityAdd(this);\" data-area-id=\"" + i + "\">" + result.response.items[i].area + "</a></td><td><a onClick=\"cityAdd(this);\" data-region-id=\"" + i + "\">" + result.response.items[i].region + "</a></td></tr>";
            };
            $('#tbody-city-add').append(data);
          };
        }
      });
    };
    if (lenCity <= 2) {
      // Удаляем все значения, если символов меньше 3х
      $('#tbody-city-add>tr').remove();
      $('#city-id-field').val('');
      $('#area-name').val('');
      $('#region-name').val('');
    };
  });

          // $count = $result->response->count;
          // $data = "";

          // for (var i=0; i < count; i++) { 
          // $
          // }
          // if(date.length > 2){
          //   // Удаляем содержимое UL
          //   $('#city').html('');
          //   // Вставляем новое содержимое из поиска
          //   $('#city').html(date);
          //   // Удаляем содержимое UL
          //   $('#city').css('display', 'block');
          // } 

  

});

</script>
@endsection