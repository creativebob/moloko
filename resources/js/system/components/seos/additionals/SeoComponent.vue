<template>
    <tr class="item">
        <td>{{ seo.title }}</td>
        <td>{{ seo.h1 }}</td>
        <td class="actions-list">
            <div
                class="icon-list-edit sprite"
                data-open="modal-update-additional-seo"
                @click="update"
            ></div>
        </td>
        <td class="td-delete">
            <div
                @click="remove"
                class="icon-delete sprite"
                data-open="modal-delete-additional-seo"
            ></div>
        </td>
        <template
            v-for="column in columns"
        >
            <input
                v-if="column === 'is_canonical'"
                type="hidden"
                :name="'additional_seos[' + index + '][' + column + ']'"
                :value="seo[column] == true ? 1 : 0"
            >
            <input
                v-else
                type="hidden"
                :name="'additional_seos[' + index + '][' + column + ']'"
                :value="seo[column]"
            >
        </template>

        <template
            v-for="(param, paramIndex) in seo.params"
        >
            <input
                type="hidden"
                :name="'additional_seos[' + index + '][params][' + paramIndex + '][param]'"
                :value="param.param"
            >
            <input
                type="hidden"
                :name="'additional_seos[' + index + '][params][' + paramIndex + '][value]'"
                :value="param.value"
            >
            <input
                v-if="param.id"
                type="hidden"
                :name="'additional_seos[' + index + '][params][' + paramIndex + '][id]'"
                :value="param.id"
            >
        </template>

        <input
            v-if="seo.id"
            type="hidden"
            :name="'additional_seos[' + index + '][id]'"
            :value="seo.id"
        >

    </tr>
</template>

<script>
    export default {
        props: {
            seo: Object,
            index: Number,
            columns: Array
        },
        methods: {
            update() {
                this.$store.commit('SET_UPDATING_SEO', this.index);
            },
            remove() {
                this.$store.commit('SET_DELETING_SEO', this.index);
            },
        },
    }
</script>
