// Подключаем foundation
$(document).foundation();

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