<!DOCTYPE html>
<html>
<head>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
</head>
<body>
    <div id="map" style="width: 1024; height: 840px"></div>


    <script type="text/javascript">

        var longitude = '{{ Auth::user()->staff->first()->company->location->longitude }}';
        var latitude = '{{ Auth::user()->staff->first()->company->location->latitude }}';
        // alert(longitude);
        // var data = '{{ $coords }}';
        // var coords = JSON.parse(data);
        // var coords = [
        // [56.023, 36.988],
        // [56.025, 36.981],
        // [56.020, 36.981],
        // [56.021, 36.983],
        // [56.027, 36.987]
        // ];

        // alert(coords.length);

        // var coords = eval('{{ $coords }}');

        var coords = $.parseJSON('{!! $coords !!}');

        // alert(coords);       

        // Функция ymaps.ready() будет вызвана, когда
        // загрузятся все компоненты API, а также когда будет готово DOM-дерево.

        ymaps.ready(init);

        function init(){ 

            // Создаем и центрируем карту
            var myMap = new ymaps.Map("map", {
                center: [latitude, longitude],
                zoom: 10
            });

            // Создание экземпляра маршрута.
            var multiRoute = new ymaps.multiRouter.MultiRoute({   
                referencePoints: [
                    [latitude, longitude],
                    coords['coords'],
                ]
            }, {
                reverseGeocoding: true,
                boundsAutoApply: true
            });

            // Добавление маршрута на карту.
            myMap.geoObjects.add(multiRoute);
        }
    </script>
</body>
</html>