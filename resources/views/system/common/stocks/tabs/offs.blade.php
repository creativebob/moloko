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
	            			<td>{{ getDocumentNameByModel($off->document_type) }}</td>
	            			<td>{{ $off->document->conducted_at->format('d.m.Y') }}</td>
	            			<td>{{ $off->document->number }}</td>
	            			<td>{{ num_format($off->count, 0) }}</td>
	            			<td>{{ num_format($off->amount, 0) }} руб.</td>
	            			<td></td>
	            			<td>1680</td>
	            			<td>0</td>
	            			<td>{{ $off->author->name }}</td>
            			</tr>
                        @endforeach
            		</tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <td>{{ num_format($stock->offs->sum('count'), 0) }}</td>
                            <td>{{ num_format($stock->offs->sum('amount'), 0) }} руб.</td>
                            <td></td>
                            <td>7280</td>
                            <td>0</td>
                            <td></td>
                        </tr>
                    </tfoot>
            	</table>
            </div>
        </div>

    </div>
</div>
