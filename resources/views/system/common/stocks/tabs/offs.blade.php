<div class="grid-x">
    <div class="cell small-12">

        {{-- Основная инфа --}}
        <div class="grid-x grid-margin-x">
            <div class="cell small-12">
				<table class="table-invoice">
            		<caption>История списаний:</caption>
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
                    @foreach($stock->offs as $off)
            			<tr>
	            			<td>{{ $off->document_id }}</td>
                            <td>
                                <a href="{{ route(getDocumentRouteByModel($off->document_type), isset($off->document->lead_id) ? $off->document->lead_id : $off->document_id) }}">{{ getDocumentNameByModel($off->document_type) }}</a>
                            </td>
	            			<td>{{ $off->document->conducted_at->format('d.m.Y') }}</td>
	            			<td>{{ $off->document->number }}</td>
	            			<td>{{ num_format($off->count, 2) }}</td>
	            			<td>{{ num_format($off->costTotal, 2) }} руб.</td>
                            <td>
                                @isset($off->document->client_id)
                                    <a href="{{ route($off->document->client->clientable_type == 'App\User' ? 'clients.editClientUser' : 'clients.editClientCompany', $off->document->client->id) }}">{{ $off->document->client->clientable->name }}</a>
                                @endif
                            </td>
                            <td>{{ num_format($off->weightTotal, 4) }}</td>
                            <td>{{ num_format($off->volumeTotal, 4) }}</td>
	            			<td>{{ $off->author->name }}</td>
            			</tr>
                        @endforeach
            		</tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <td>{{ num_format($stock->offs->sum('count'), 2) }}</td>
                            <td>{{ num_format($stock->offs->sum('costTotal'), 2) }} руб.</td>
                            <td></td>
                            <td>{{ num_format($stock->offs->sum('weightTotal'), 4) }}</td>
                            <td>{{ num_format($stock->offs->sum('volumeTotal'), 4) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
            	</table>
            </div>
        </div>

    </div>
</div>
