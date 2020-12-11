<div class="grid-x">
    <div class="cell small-12">

        {{-- Основная инфа --}}
        <div class="grid-x grid-margin-x">
            <div class="cell small-12">
            	<table class="table-invoice">
            	<caption>История поступлений:</caption>
            		<thead>
            			<th>ID</th>
            			<th>Документ</th>
            			<th>Дата</th>
            			<th>Номер</th>
            			<th>Кол-во</th>
            			<th>Себестоимость</th>
            			<th>Клиент</th>
            			<th>Вес</th>
            			<th>Объем</th>
            			<th>Автор</th>
            		</thead>
                    <tbody>

                    @foreach($stock->receipts as $receipt)
                        <tr>
                            <td>{{ $receipt->document_id }}</td>
                            <td>
                                <a href="{{ route(getDocumentRouteByModel($receipt->document_type), isset($receipt->document->lead_id) ? $receipt->document->lead_id : $receipt->document_id) }}">{{ getDocumentNameByModel($receipt->document_type) }}</a>
                            </td>
                            <td>{{ $receipt->document->conducted_at->format('d.m.Y') }}</td>
                            <td>{{ $receipt->document->number }}</td>
                            <td>{{ num_format($receipt->count, 2) }}</td>
                            <td>{{ num_format($receipt->costTotal, 2) }} руб.</td>
                            <td>
                                @isset($receipt->document->client_id)
                                    <a href="{{ route($receipt->document->client->clientable_type == 'App\User' ? 'clients.editClientUser' : 'clients.editClientCompany', $receipt->document->client->id) }}">{{ $reserve->document->client->clientable->name }}</a>
                                @endif
                            </td>
                            <td>{{ num_format($receipt->weightTotal, 2) }}</td>
                            <td>{{ num_format($receipt->volumeTotal, 2) }}</td>
                            <td>{{ $receipt->author->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4"></td>
                        <td>{{ num_format($stock->receipts->sum('count'), 2) }}</td>
                        <td>{{ num_format($stock->receipts->sum('costTotal'), 2) }} руб.</td>
                        <td></td>
                        <td>{{ num_format($stock->receipts->sum('weightTotal'), 2) }}</td>
                        <td>{{ num_format($stock->receipts->sum('volumeTotal'), 2) }}</td>
                        <td></td>
                    </tr>
                    </tfoot>
            	</table>
            </div>
        </div>

    </div>
</div>
