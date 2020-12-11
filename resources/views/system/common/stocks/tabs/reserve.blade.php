<div class="grid-x">
    <div class="cell small-12">

        {{-- Основная инфа --}}
        <div class="grid-x grid-margin-x">
            <div class="cell small-12">
                <table class="table-invoice">
                    <caption>История резервирования:</caption>
                    <thead>
                    <th>ID</th>
                    <th>Документ</th>
                    <th>Дата</th>
                    <th>Номер</th>
                    <th>Кол-во</th>
                    <th>Стоимость по прайсу</th>
                    <th>Клиент</th>
                    <th>Вес</th>
                    <th>Объем</th>
                    <th>Автор</th>
                    </thead>
                    <tbody>
                    @foreach($stock->reserves as $reserve)
                        <tr>
                            <td>{{ $reserve->document_id }}</td>
                            <td>
                                <a href="{{ route(getDocumentRouteByModel($reserve->document_type), isset($reserve->document->lead_id) ? $reserve->document->lead_id : $reserve->document_id) }}">{{ getDocumentNameByModel($reserve->document_type) }}</a>
                            </td>
                            <td>{{ $reserve->document->registered_at->format('d.m.Y') }}</td>
                            <td>{{ $reserve->document->number }}</td>
                            <td>{{ num_format($reserve->count, 0) }}</td>
                            <td>{{ num_format($reserve->priceTotal, 2) }} руб.</td>
                            <td>
                                @isset($reserve->document->client_id)
                                    <a href="{{ route($reserve->document->client->clientable_type == 'App\User' ? 'clients.editClientUser' : 'clients.editClientCompany', $reserve->document->client->id) }}">{{ $reserve->document->client->clientable->name }}</a>
                                @endif
                            </td>
                            <td>{{ num_format($reserve->weightTotal, 2) }}</td>
                            <td>{{ num_format($reserve->volumeTotal, 2) }}</td>
                            <td>{{ $reserve->author->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4"></td>
                        <td>{{ num_format($stock->reserves->sum('count'), 0) }}</td>
                        <td>{{ num_format($stock->reserves->sum('priceTotal'), 2) }} руб.</td>
                        <td></td>
                        <td>{{ num_format($stock->reserves->sum('weightTotal'), 2) }}</td>
                        <td>{{ num_format($stock->reserves->sum('volumeTotal'), 2) }}</td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>
