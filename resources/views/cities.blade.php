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
        <a href="#" class="icon-add sprite" data-open="add"></a>
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
      <li class="first-item">
        <ul class="icon-list">
          <li><div class="icon-list-add sprite" data-open="add"></div></li>
          <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
          <li><div class="icon-list-delete sprite" data-open="del"></div></li>
        </ul>
        <a data-list="1" class="first-link">
          <div class="list-title">
            <div class="icon-open sprite"></div>
            <span>Красноярский край</span><span class="number">12</span>
          </div>
        </a>
      </li>
      <li class="first-item">
        <ul class="icon-list">
          <li><div class="icon-list-add sprite" data-open="add"></div></li>
          <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
          <li><div class="icon-list-delete sprite" data-open="del"></div></li>
        </ul>
        <a data-list="2" class="first-link">
          <div class="list-title">
            <div class="icon-open sprite"></div>
            <span>Иркутская область</span><span class="number">4</span>
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
      <li class="first-item">
        <ul class="icon-list">
          <li><div class="icon-list-add sprite" data-open="add"></div></li>
          <li><div class="icon-list-edit sprite" data-open="edit"></div></li>
          <li><div class="icon-list-delete sprite" data-open="del"></div></li>
        </ul>
        <a data-list="8" class="first-link">
          <div class="list-title">
            <div class="icon-open sprite"></div>
            <span>Республика Бурятия</span><span class="number">4</span>
          </div>
        </a>
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
{{-- Модалка добавления --}}
<div class="reveal" id="add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ НАСЕЛЕННОГО ПУНКТА</h5>
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
        <tbody>
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
        <tbody>
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
$(function() {

  // Присваиваем при клике на первый элемент списка активный класс
  $('.first-link').bind('click', function() {
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
  $('.medium-link').bind('click', function() {
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
});
</script>
@endsection