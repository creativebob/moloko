<template>
    <tr>
        <td>{{ scheme.name }}</td>
        <td>{{ scheme.description }}</td>
        <td>{{ scheme.percent_default | decimalPlaces | decimalLevel }}</td>
        <td class="td-delete">
            <div
                @click="openModalArchive"
                class="icon-delete sprite"
                data-open="modal-scheme-archive"
            ></div>
        </td>
    </tr>
</template>

<script>

    export default {
        props: {
            scheme: Object,
        },
        methods: {
            openModalArchive() {
                this.$emit('open-modal-archive', this.scheme);
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
