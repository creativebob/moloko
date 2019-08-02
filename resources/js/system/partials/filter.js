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