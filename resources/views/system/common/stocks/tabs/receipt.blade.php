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
                            <td>{{ getDocumentNameByModel($receipt->document_type) }}</td>
                            <td>{{ $receipt->document->conducted_at->format('d.m.Y') }}</td>
                            <td>{{ $receipt->document->number }}</td>
                            <td>{{ num_format($receipt->count, 0) }}</td>
                            <td>{{ num_format($receipt->amount, 0) }} руб.</td>
                            <td></td>
                            <td>1680</td>
                            <td>0</td>
                            <td>{{ $receipt->author->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4"></td>
                        <td>{{ num_format($stock->receipts->sum('count'), 0) }}</td>
                        <td>{{ num_format($stock->receipts->sum('amount'), 0) }} руб.</td>
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
