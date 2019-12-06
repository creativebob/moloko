<template>

    <tr
        class="item"
        :id="'table-metrics-' + metric.id"
        :data-name="metric.name"
    >
        <td>{{ metric.name }}</td>
        <td>{{ metric.min }}</td>
        <td>{{ metric.max }}</td>
        <td>{{ metric.boolean_true }}</td>
        <td>{{ metric.boolean_false }}</td>
        <td>{{ metric.color }}</td>
        <td>
<!--            @isset($metric->values)-->
<!--            <ul>-->
<!--                @foreach ($metric->values as $value)-->
<!--                <li>{{ $value->value }}</li>-->
<!--                @endforeach-->
<!--            </ul>-->
<!--            @endisset-->
        </td>
        <td class="td-delete">
            <a class="icon-delete sprite" data-open="delete-metric"></a>
        </td>
    </tr>

</template>

<script>
	export default {
		props: {
            metric: Object,
		},
        filters: {
            decimal: function (value) {
                return Math.trunc(parseFloat(parseInt(value).toFixed(value)) * 100) / 100;
            },
        }
	}
</script>
