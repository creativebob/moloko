// Валидируем кнопку при клике
window.submitAjax = function (id) {
    this.event.preventDefault();
    $('#' + id).foundation('validateForm');
    let valid = $('#' + id + ' .is-invalid-input').length;
    let result = valid == 0;
    return result;
};