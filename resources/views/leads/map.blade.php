<!DOCTYPE html>
<html>
<head>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
    <script src="https://code.jquery.com/jquery-latest.min.js"></script>
</head>
<body>
    <div id="map" style="width: 1024; height: 840px"></div>


    <script type="application/javascript">

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

            var myGeoObjects = [];

            for (var i = 0; i < coords.length; i++) {

                var claim = '';

                if (coords[i]['info']['claims_count'] > 0) {
                    var color = 'islands#redIcon';
                    claim = '<br>(Рекламация)'
                } else {
                    if (coords[i]['info']['stage']['id'] === 12) {
                        var color = 'islands#greenIcon';
                    } else {
                        var color = 'islands#blueIcon';
                    }
                }

                myGeoObjects[i] = new ymaps.Placemark(
                    coords[i]['coords'],
                    {
                        // clusterCaption: '№: ' + coords[i]['info']['order'] + claim,
                        balloonContentHeader: '№: ' + coords[i]['info']['order'] + claim,
                        balloonContentBody: 'Имя: ' + coords[i]['info']['name'] + "<br>Телефон: " + coords[i]['info']['phone'] + "<br>Адрес: " + coords[i]['info']['address'] + "<br>Этап: " + coords[i]['info']['stage']['name'],
                    }, {
                        preset: color
                    }
                    );
            }

            var myClusterer = new ymaps.Clusterer({
                clusterDisableClickZoom: true,
                clusterIconLayout: 'default#pieChart'
            });
            myClusterer.add(myGeoObjects);
            myMap.geoObjects.add(myClusterer);



        }


    </script>
</body>
</html>
