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
            			<th>Себестоимость</th>
            			<th>Клиент</th>
            			<th>Вес</th>
            			<th>Объем</th>
            			<th>Автор</th>
            		</thead>
                    <tbody>
                    @foreach($stock->reserves as $reserve)
                        <tr>
                            <td>{{ $reserve->document_id }}</td>
                            <td>{{ getDocumentNameByModel($reserve->document_type) }}</td>
                            <td>{{ $reserve->document->registered_at->format('d.m.Y') }}</td>
                            <td>{{ $reserve->document->number }}</td>
                            <td>{{ num_format($reserve->count, 0) }}</td>
                            <td>{{ num_format($reserve->amount, 0) }} руб.</td>
                            <td></td>
                            <td>1680</td>
                            <td>0</td>
                            <td>{{ $reserve->author->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4"></td>
                        <td>{{ num_format($stock->reserves->sum('count'), 0) }}</td>
                        <td>{{ num_format($stock->reserves->sum('amount'), 0) }} руб.</td>
                        <td></td>
                        <td>7280</td>
                        <td>0</td>
                        <td></td>
                    </tr>
            	</table>
            </div>
        </div>

    </div>
</div>
