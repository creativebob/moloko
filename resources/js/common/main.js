// Подключаем foundation
$(document).foundation();

// Csrf для axios
window.axios = require('axios/index');

window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
};

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