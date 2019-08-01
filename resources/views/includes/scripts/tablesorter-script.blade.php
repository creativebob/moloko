<script type="application/javascript" src="/js/plugins/tablesorter/jquery.tablesorter.js"></script>
<script type="application/javascript">
    $(function() {
        // Сортировка строк таблицы
        $("#content").tablesorter({ 
            // передаем аргументы для заголовков и назначаем объект 
            headers: { 
                // работаем со второй колонкой (подсчет идет с нуля) 
                0: { 
                // запрет сортировки указанием свойства 
                sorter: false 
            }, 
                // работаем со третьей колонкой (подсчет идет с нуля) 
                1: { 
                // запрещаем, использовав свойство 
                sorter: false 
            },
        },
            // sortList: [[2,0]],
            cssHeader: "thead-header"
        });  
    });

    // Очищаем все чекбоксы (Для booklister)
    function cleanAllCheckboxes() {
        var checkboxes = document.querySelectorAll('input.table-check');
        for(var i=0; i<checkboxes.length; i++) {
            checkboxes[i].checked = false;
        };
    };

    // Размер шапки таблицы при скролле
    $(window).scroll(function () {
        if ($('#thead-sticky').hasClass('is-stuck')) {
            fixedThead ();
        };
    });


</script>
