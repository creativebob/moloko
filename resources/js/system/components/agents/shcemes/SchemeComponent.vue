<template>
    <tr>
        <td>{{ scheme.name }}</td>
        <td>{{ scheme.description }}</td>
        <td>{{ scheme.percent_default | decimalPlaces | decimalLevel }}</td>
        <td class="td-delete">
            <div
                @click="remove"
                class="icon-delete sprite"
            ></div>
        </td>
        <input
            type="hidden"
            name="schemes[]"
            :value="scheme.id"
        >
    </tr>
</template>

<script>

    export default {
        props: {
            scheme: Object,
        },
        methods: {
            remove() {
                this.$emit('remove', this.scheme.id);
            },
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        }
    }
</script>
