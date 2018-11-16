$(document).foundation();
// Умолчания глобальные
// Время применения изменений
var transitionTime = 200;
// Ширина менеджера задач
var widthTask = 300;
// Выставляем отступ контента по умолчанию = ширине сайдбара
var widthSidebarSmall = 64;
// Значение развернутого сайдбара
var widthSidebarExpand = 240;
// Блок фильтра
// var heightFilterSmall = 140;

var theadTh = $('.thead-width>tr>th');
var tbodyTd = $('.tbody-width>tr:first>td');
var oldContentWidth = 0;

var oldThWidth = [];
var oldTdWidth = [];

// Если есть табличка, то берем ширины ячеек

function getMassWidth () {
	if ($("table").is("#table-content")) {
        if (tbodyTd.length == theadTh.length) {
            for (var i = 0; i < tbodyTd.length; i++) {

                oldThWidth[i] = $(theadTh[i]).width();
                oldTdWidth[i] = $(tbodyTd[i]).width();
            };
            console.table(oldThWidth);
            console.table(oldTdWidth);
        };
    };
};

// Смотрим локальное хранилище, в каком состоянии был сайдбар
var sidebar = localStorage.getItem('sidebar');
if (sidebar === null) { // Абсолютно первый запуск
    localStorage.setItem('sidebar', 1); // Пишем в локальное значение по умолчанию
};
// Смотрим в локадьом хранилище ссылку
var link = localStorage.getItem('link');
if (link === null) { // Абсолютно первый запуск
  localStorage.setItem('link', 0); // Пишем в локальное значение по умолчанию
};
// Смотрим в локадьом хранилище состояние менеджера задач
var task = localStorage.getItem('task');
if (task === null) { // Абсолютно первый запуск
  localStorage.setItem('task', 0); // Пишем в локальное значение по умолчанию
};

// Открытие/закрытие элементов управления

// Сайдбар
// Открываем сайдбар
function sidebarOpen () {
  console.log("Запуск expandSidebar: " + widthSidebarExpand);
  if ($("#sidebar").hasClass('expand') == false) {
    $("#sidebar").addClass('expand'); // Добавляем расширенный класс
};
  $("#sidebar span").show(transitionTime); // показываем текст
  // $("#sidebar").css("width", widthSidebarExpand  + 'px'); // Ставим ширину сайдбара расширенную
  // $("#wrapper").css("margin-left", "");
  // $("#wrapper").css("margin-left", widthExpand); // Ставим отступ контента расширенного
  // $('#wrapper').transition({marginLeft: widthSidebarExpand  + 'px'}, transitionTime, 'ease');
  // $('#sidebar').transition({width: widthSidebarExpand  + 'px'}, transitionTime, 'ease');
  // $('#sidebar').css('width', widthSidebarExpand  + 'px');
  $('#wrapper').css('overflow-x', 'hidden');
  if ($("#cursor").hasClass('icon-arrow-forward')){ // Меняем стрелочку
    $("#cursor").removeClass('icon-arrow-forward');
    $("#cursor").removeClass('icon-arrow-back');
};
  $("#cursor").addClass('icon-arrow-back'); // Ставим стрелочку назад
  localStorage.setItem('sidebar', 1); // Меняем значение в хранилище
  setActive(); // Подсвечиваем активное меню, и разворачиваем аккордионы
  console.log("Завершение expandSidebar");
};
// Закрываем сайдбар
function sidebarClose () {
  var log = console.log("Запуск closeSidebar: " + widthSidebarSmall);
  // По умолчанию на экранах
  if ($("#sidebar").hasClass('expand')) { // Если есть расширенный класс
    $("#sidebar").removeClass('expand'); // Удаляем расширенный класс
};
  $("#sidebar span").hide(); // убираем тескт в меню, оставляем иконки
  $("#sidebar .accordion-menu").foundation('hideAll'); // Сворачиваем аккордионы
  // $("#sidebar").css("width", widthSidebarSmall + 'px'); // Ставим ширину сайдбара по умолчанию
  // $("#wrapper").css("margin-left", "");
  // $("#wrapper").css("margin-left", widthSidebar); // Ставим отступ контента по умолчанию
  // $('#wrapper').transition({marginLeft: widthSidebarSmall + 'px'}, transitionTime, 'ease');
  // $('#sidebar').css('width', widthSidebarSmall + 'px');
  // $('#sidebar').transition({width: widthSidebarSmall + 'px'}, transitionTime, 'ease');
  $('#wrapper').css('overflow-x', 'hidden');
  if($("#cursor").hasClass('icon-arrow-back')){ // Меняем стрелочку
    $("#cursor").removeClass('icon-arrow-forward');
    $("#cursor").removeClass('icon-arrow-back');
    $("#cursor").addClass('icon-arrow-forward'); // Ставим стрелочку вперед
};
  localStorage.setItem('sidebar', 0); // Меняем значение в хранилище
  setActive(); // Подсвечиваем активное меню
  console.log("Завершение closeSidebar");
};
// Проверка наличия у ссылки родителя-директории
function checkParentDir (idMenuClick) {
  var p = 0;
  var link = localStorage.getItem('link');
  var ulParent = $('[data-link="' + link + '"]').parents('ul'); // смотрим всех родителей ul у нажатой ссылки
  var activeLi = ulParent.parents('li'); // Берем li всех ul, в котором нажата ссылка
  var activeA = activeLi.children('a'); // Выбираем все вышестоящие ссылки
  for(i = 0; i < activeA.length; i++) {
    result = $(activeA[i]).data('link');
    if (idMenuClick == result) { // Если нажатая ссылка находится в одном списке с родителем, узнаем об этом
      var p = 1;
  };
};
  // console.log(p);
  return p;
};
// Присвоение активного статуса ссылкам
function setActive() {
  console.log("Запуск setActive");
  var link = localStorage.getItem('link');
  console.log("Видим ид ссылки: " + link);
  var sidebar = localStorage.getItem('sidebar');
  console.log("Видим состояние сайдбара: " + sidebar);
  $(".nav a").removeClass('active');
  $('[data-link="' + link + '"]').addClass('active');

  // if ($('[data-link="' + link + '"]').parent('.is-accordion-submenu-parent')) {
  //   $('[data-link="' + link + '"]').next('ul').css('display', '');
  // };

  var ulParent = $('[data-link="' + link + '"]').parents('ul');
  numMainParent = ulParent.length - 1; // Минусуем самый главный ul сайдбара
  // console.log(ulParent);
  for (var i = 0; i < numMainParent; i++) {
    $(ulParent[i]).addClass('is-active');
    var activeDirectory = $(ulParent[i]).parent('li');
    $(activeDirectory).children('a').addClass('active'); // Подсвечиваем все вышестоящие ссылки
    if (sidebar == 1) {
      $(ulParent[i]).css('display', ''); // Если сайдбар раскрыт - разворачиваем аккордион
  };
  if (sidebar == 0) {
      $(ulParent[i]).css('display', 'none'); // Если сайдбар скрыт - сворачиваем аккордион
  };
};
console.log("Завершение setActive");
};

// Менеджер задач
// Открываем менеджер задач
function taskOpen () {
  // $('#task-manager').transition({'marginRight': '0'}, transitionTime, 'ease');
  // $('#task-manager').css('marginRight', '0');
  if ($('#task-manager').hasClass('task-active') == false) {
    $('#task-manager').addClass('task-active');
};
};
// Закрываем менеджер задач
function taskClose () {
  // $('#task-manager').transition({'marginRight': '-' + widthTask + 'px'}, transitionTime, 'ease');
  // $('#task-manager').css('marginRight', '-' + widthTask + 'px');
  if ($('#task-manager').hasClass('task-active')) {
    $('#task-manager').removeClass('task-active');
};
};

function fixedThead () {

	getMassWidth ();
  // Ширина th в закрепленном заголовке таблицы
  console.log('Запуск шапки таблицы');
  // console.log('Видим количество td в одной строке = ' + tbodyTd.length);
  // console.log('Видим количество th в одной строке = ' + theadTh.length);
  if (tbodyTd.length == theadTh.length) {
    for (var i = 0; i < tbodyTd.length; i++) {
    	var thWidth = oldThWidth[i];
      var tdWidth = oldTdWidth[i];
      if (tdWidth > thWidth) {
      	console.log('Старая ширина столбца = ' + tdWidth);
      	if (oldContentWidth != 0) {
            var tdWidth = computedContentWidth * tdWidth / oldContentWidth;
            console.log('Новая ширина столбца = ' + tdWidth);
        };
        $(tbodyTd[i]).width(tdWidth);
        // console.log('Ширина столбца №' + i + ' = ' + tdWidth);
        $(theadTh[i]).width(tdWidth);
        // console.log('Ставим столбец  №' + i + ' в шапке = ' + $(theadTh[i]).width());
    };
    if (tdWidth < thWidth) {
     console.log('Старая ширина столбца шапки = ' + thWidth);
     if (oldContentWidth != 0) {
        var thWidth = computedContentWidth * thWidth / oldContentWidth;
        console.log('Новая ширина столбца шапки = ' + thWidth);
    };
    $(tbodyTd[i]).width(thWidth);
        // console.log('Ширина столбца №' + i + ' = ' + thWidth);
        $(theadTh[i]).width(thWidth);
        // console.log('Ставим столбец  №' + i + ' в шапке = ' + $(theadTh[i]).width());
    };
    if (tdWidth == thWidth) {
     if (oldContentWidth != 0) {
        var tdWidth = computedContentWidth * tdWidth / oldContentWidth;
        console.log('Новая ширина столбца = ' + tdWidth);
    };
    $(tbodyTd[i]).width(tdWidth);
        // console.log('Ширина столбца №' + i + ' = ' + thWidth);
        $(theadTh[i]).width(tdWidth);
    };

};


oldContentWidth = computedContentWidth;
    // alert('Записали старую = ' + oldContentWidth);



} else {
    // alert('Братиш, в thead ' + theadTh.length + ' столбцов, а в tbody ' + tbodyTd.length + '! Непорядок, поправь!');
};



console.log('Завершаем шапку таблицы');
console.log('----');
};

// Глобальная функция отстройки
function renderContent () {
  var contentWidthFull = $(document).width();

  // Смотрим локальное хранилище, в каком состоянии был сайдбар
  var sidebar = localStorage.getItem('sidebar');
   // Смотрим, какая была открыта ссылка в последний раз
   var link = localStorage.getItem('link');
  // Смотрим локальное хранилище, в каком состоянии был менеджер задач
  var task = localStorage.getItem('task');

  if ($(document).width() > 640) { // Десктоп
    console.log("Видим что комп");


    // Показываем кнопку переключения сайдбара
    $("#sidebar-button").parent('.gen-menu-bot').show();

    // Убираем отступы по 20px с каждой стороны
    var contentWidth = contentWidthFull - 40;
    // var wrapperWidth = contentWidthFull;

    // Все свернуто
    if ((sidebar == 0) && (task == 0)) {
      computedContentWidth = contentWidth - widthSidebarSmall;

      // computedWrapperWidth = wrapperWidth - widthSidebarSmall;
      $('#wrapper').css({'marginLeft': widthSidebarSmall + 'px', 'marginRight':'0'});
      $('#sidebar').css('width', widthSidebarSmall  + 'px');
      $('#task-manager').css('marginRight', '-' + widthTask + 'px');


      // Прилипающий заголовок
      if ($("div").is("#head-content")) {
	    	// $('#head-sticky').css('maxWidth', computedContentWidth + 'px');
	    	$('.head-content').css('width', computedContentWidth  + 'px');
        };
		  // Табличка
        if ($("table").is("#table-content")) {
          $('#thead-content').css('width', computedContentWidth  + 'px');
          fixedThead ();
      };
      // Сайдбар
      sidebarClose ();
      // Менеджер задач
      taskClose ();
  };

    // Открыт менеджер задач
    if ((sidebar == 0) && (task == 1)) {
      computedContentWidth = contentWidth - widthSidebarSmall - widthTask;
      $('#wrapper').css({'marginLeft': widthSidebarSmall + 'px', 'marginRight': widthTask + 'px'});
      $('#sidebar').css('width', widthSidebarSmall  + 'px');
      $('#task-manager').css('marginRight', '0');


      // Прилипающий заголовок
      if ($("div").is("#head-content")) {
	    	// $('#head-sticky').css('maxWidth', computedContentWidth + 'px');
	    	$('.head-content').css('width', computedContentWidth  + 'px');
        };
		  // Табличка
        if ($("table").is("#table-content")) {
          $('#thead-content').css('width', computedContentWidth  + 'px');
          fixedThead ();
      };
      // Сайдбар
      sidebarClose ();
      // Менеджер задач
      taskOpen ();
  };

    // Открыт сайдбар
    if ((sidebar == 1) && (task == 0)) {
      computedContentWidth = contentWidth - widthSidebarExpand;

      // $('#wrapper').transition({'marginLeft': widthSidebarExpand + 'px', 'marginRight': '0px'}, transitionTime, 'ease');
      $('#wrapper').css({'marginLeft': widthSidebarExpand + 'px', 'marginRight': '0px'});
      $('#sidebar').css('width', widthSidebarExpand  + 'px');
      $('#task-manager').css('marginRight', '-' + widthTask + 'px');


     	// Прилипающий заголовок
      if ($("div").is("#head-content")) {
          $('#head-sticky').css('maxWidth', computedContentWidth + 'px');
          $('.head-content').css('width', computedContentWidth  + 'px');
      };
		  // Табличка
        if ($("table").is("#table-content")) {
          $('#thead-content').css('width', computedContentWidth  + 'px');
          fixedThead ();
      };
      // Сайдбар
      sidebarOpen ();
      // Менеджер задач
      taskClose ();
  };

    // Открыты сайдбар и менеджер задач
    if ((sidebar == 1) && (task == 1)) {
      computedContentWidth = contentWidth - widthSidebarExpand - widthTask;

      // $('#wrapper').transition({'marginLeft': widthSidebarExpand + 'px','marginRight': widthTask + 'px'}, transitionTime, 'ease');
      $('#wrapper').css({'marginLeft': widthSidebarExpand + 'px','marginRight': widthTask + 'px'});
      $('#sidebar').css('width', widthSidebarExpand  + 'px');
      $('#task-manager').css('marginRight', '0');


      // Прилипающий заголовок
      if ($("div").is("#head-content")) {
          $('#head-sticky').css('maxWidth', computedContentWidth + 'px');
          $('.head-content').css('width', computedContentWidth  + 'px');
      };
		  // Табличка
        if ($("table").is("#table-content")) {
          $('#thead-content').css('width', computedContentWidth  + 'px');
          fixedThead ();
      };
      // Сайдбар
      sidebarOpen ();
      // Менеджер задач
      taskOpen ();
  };
} else {
    console.log("Видим что мобила");
    // Мобила
    // По умолчанию на телефонах
    $("#sidebar").css("width", '100%');
    $("#sidebar span").show(); // На мобилках показываем текст
    if($("#sidebar").hasClass('expand')) { // Если есть расширенный класс
      $("#sidebar").removeClass('expand'); // Удаляем расширенный класс
  };
    $("#sidebar-button").parent('.gen-menu-bot').hide(); // Скрываем кнопку переключения

    computedContentWidth = contentWidth + 40;
    // alert(contentWidth);
    $('#wrapper').css({'marginLeft':'0','marginRight':'0'});

    // Все свернуто
    if ((sidebar == 0) && (task == 0)) {
      // Менеджер задач
      taskClose ();
  };
    // Менеджер
    if ((sidebar == 0) && (task == 1)) {
      // Менеджер задач
      taskOpen ();
  };
};
};



// Палим клики
// Клики по стрелке сайдбара
$("#sidebar-button").click(function() {
  var log = console.log("Клик по кнопке");
  if (localStorage.getItem('sidebar') == 0) {
    localStorage.setItem('sidebar', 1);
} else {
    localStorage.setItem('sidebar', 0);
};
renderContent ();
console.log("-------------------");
});
// При клике на иконку разворачивается сайдбар и сама директория
$(".nav a").click(function() {
  var idMenuClick = $(this).data('link');
  if ($(document).width() > 640) {
    if (localStorage.getItem('sidebar') == 1) {
      console.log("Делаем подсветку пунктов меню");
      localStorage.setItem('link', idMenuClick);
      console.log("Записали ID: " + idMenuClick);
      setActive ();
  } else {
      console.log("Видим надо развернуть по иконке");
      localStorage.setItem('sidebar', 1);
      console.log("Видим ID пункта меню по которому кликнули: " + idMenuClick);
      var a = checkParentDir (idMenuClick);
      if (a == 1) {
        console.log("Не переписываем ссылку");
    } else {
        localStorage.setItem('link', idMenuClick);
        console.log("Записали ID: " + idMenuClick);
    };
};
} else {
    console.log("Делаем подсветку для мобил");
    localStorage.setItem('link', idMenuClick);
    console.log("Записали ID: " + idMenuClick);
    setActive ();
};
renderContent ();
console.log("-------------------");
});
// На мобилах клик по иконке разворачивания сайдбара
$('.menu-icon').click(function() {
  if ($(document).width() <= 640) {
    if ($('#sidebar').css('display') == 'block') {
      $('body').css('overflow-y', 'hidden');
  } else {
      $('body').css('overflow-y', 'auto');
  };
  if (localStorage.getItem('task') == 1) {
      localStorage.setItem('task', 0);
      $('#task-manager').removeClass('task-active');
      $('#task-manager').css('marginRight', '-240px');

  };
};
renderContent ();
});
// Клики по кнопке менеджера задач
$('#task-toggle').click(function() {
  if ($(document).width() <= 640) {
    if ($('#task-manager').hasClass('task-active')) {
      $('body').css('overflow-y', 'auto');
  } else {
      $('body').css('overflow-y', 'hidden');
  };
  if ($('#sidebar').css('display') == 'block') {
      $('#sidebar').css('display', 'none');
      $('#task-toggle').addClass('task-active');
  };
};
if (localStorage.getItem('task') == 0) {
    localStorage.setItem('task', 1);
} else {
    localStorage.setItem('task', 0);
};
renderContent ();
});

function checkFilter () {

	// var marginTop = '6.2em';
	// if ($('#thead-sticky').hasClass('is-stuck')) {
	// 	var string = $('#thead-sticky').css('marginTop');
  if ($('.icon-filter').hasClass('active-filter')) {
      $('#filters').css('display', 'block');

      $('#thead-sticky').css('marginTop', '15em');
      $('#thead-sticky').attr('data-margin-top', 15);

  } else {
      $('#filters').css('display', 'none');
      $('#thead-sticky').attr('data-margin-top', 6.2);
      $('#thead-sticky').css('marginTop', '6.2em');
  };
	// };
};

// Блок фильтра
$(document).on('click', '.icon-filter', function() {
  $(this).toggleClass("active-filter");
  checkFilter ();
});
// $('.icon-filter').click(function() {

// });

$(document).on('click', '.filter-close', function() {
  $('.icon-filter').removeClass("active-filter");
  $('#filters').css('display', 'none');
  $('#thead-sticky').attr('data-margin-top', 6.2);
  $('#thead-sticky').css('marginTop', '6.2em');


});


// $(window).scroll(function () {
//    checkFilter ();
// });

// Ajax ошибка
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    error: function(date) {
        // alert(date);
        alert('К сожалению, произошла ошибка. Попробуйте перезагрузить страницу!');
    },
});

// $(document).on('ajaxSend', '#loading .find-status', function() {
//     $(this).show(); // показываем элемент
// }).on('ajaxComplete', '#loading .find-status', function(){
//     $(this).hide(); // скрываем элемент
// });
// $("#loading .find-status").bind("ajaxSend", function(){
//     $(this).show(); // показываем элемент
// }).bind("ajaxComplete", function(){
//     $(this).hide(); // скрываем элемент
// });



