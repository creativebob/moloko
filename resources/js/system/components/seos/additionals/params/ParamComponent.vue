<template>
    <tr class="item">
        <td>{{ param.param }}</td>
        <td>{{ param.value }}</td>
        <td class="td-delete">
            <div
                @click="remove"
                class="icon-delete sprite"
            ></div>
        </td>

<!--        <template-->
<!--            v-for="column in columns"-->
<!--        >-->
<!--            <input-->
<!--                type="hidden"-->
<!--                :name="'additional_SEOs[' + index + '][' + column + ']'"-->
<!--                :value="seo[column]"-->
<!--            >-->
<!--        </template>-->

    </tr>
</template>

<script>
export default {
    props: {
        param: Object,
        index: Number,
    },
    methods: {
        remove() {
            this.$emit('remove', this.index);
        },
    },
}
</script>
