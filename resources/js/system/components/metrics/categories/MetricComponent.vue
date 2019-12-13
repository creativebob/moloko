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
            <ul
                v-if="metric.values.length"
            >
                <li
                    v-for="value in metric.values"

                >{{ value.value }}</li>
            </ul>
        </td>
        <td class="td-delete">
            <a
                @click="openModalRemove"
                class="icon-delete sprite"
                data-open="modal-delete-metric"
            ></a>
        </td>
    </tr>

</template>

<script>
	export default {
		props: {
            metric: Object,
		},
        methods: {
            openModalRemove() {
                this.$emit('open-modal-remove', this.metric);
            },
        },
        filters: {
            decimal: function (value) {
                return Math.trunc(parseFloat(parseInt(value).toFixed(value)) * 100) / 100;
            },
        }
	}
</script>
