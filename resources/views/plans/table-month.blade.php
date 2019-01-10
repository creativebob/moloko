@if(!empty($indicators))
    @foreach($indicators as $indicator)

        <table class="widget-table stack unstriped hover responsive-card-table">
            <thead>
                <tr>

                    <th class="right-border">Показатель:</th>
                    <th>Январь</th>
                    <th>Февраль</th>
                    <th>Март</th>
                    <th>Апрель</th>
                    <th>Май</th>
                    <th>Июнь</th>
                    <th>Июль</th>
                    <th>Август</th>
                    <th>Сентябрь</th>
                    <th>Октябрь</th>
                    <th>Ноябрь</th>
                    <th>Декабрь</th>

                </tr>
            </thead>
            <tbody>
                <tr>

                    <td data-label="Показатель" class="right-border">{{ $indicator->name }}</td>
                    <td data-label="Январь">{{ num_format(3500000, 0) }}</td>
                    <td data-label="Февраль">{{ num_format(4000000, 0) }}</td>
                    <td data-label="Март">{{ num_format(4700000, 0) }}</td>
                    <td data-label="Апрель">{{ num_format(5200000, 0) }}</td>
                    <td data-label="Май">{{ num_format(6000000, 0) }}</td>
                    <td data-label="Июнь">{{ num_format(7800000, 0) }}</td>
                    <td data-label="Июль">{{ num_format(7000000, 0) }}</td>
                    <td data-label="Август">{{ num_format(8100000, 0) }}</td>
                    <td data-label="Сентябрь">{{ num_format(9000000, 0) }}</td>
                    <td data-label="Октябрь">{{ num_format(9000000, 0) }}</td>
                    <td data-label="Ноябрь">{{ num_format(6500000, 0) }}</td>
                    <td data-label="Декабрь">{{ num_format(3000000, 0) }}</td>

                </tr>
            </tbody>
        </table>

    @endforeach
@endif