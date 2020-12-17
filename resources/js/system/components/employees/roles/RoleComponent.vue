<template>
    <tr class="item">
        <td>{{ item.role.name }}
<!--            <input-->
<!--                type="hidden"-->
<!--                :name="'roles[' + item.role.id + ']'"-->
<!--                :value="item.role.id"-->
<!--            >-->
        </td>
        <td>{{ item.department.name }}
<!--            <input-->
<!--                type="hidden"-->
<!--                :name="'roles[' + item.role.id + '][department_id]'"-->
<!--                :value="item.department.id"-->
<!--            >-->
        </td>
        <td>
            <template
                v-if="item.position"
            >{{ item.position.name }}
<!--                <input-->
<!--                    type="hidden"-->
<!--                    :name="'roles[' + item.role.id + '][position_id]'"-->
<!--                    :value="item.position.id"-->
<!--                >-->
            </template>
            <template
                v-else
            >Спецправо</template>
        </td>
<!--        <td>Инфа</td>-->
        <td class="td-delete">
            <a
                class="icon-delete sprite"
                data-open="modal-role-remove"
                @click="remove"
            ></a>
        </td>
        <input
            type="hidden"
            name="access[]"
            :value="value"
        >
    </tr>
</template>

<script>
export default {
    props: {
        item: Object,
        userId: Number
    },
    computed: {
        value() {
            return this.item.role_id + ',' + this.item.department_id + ',' + this.item.position_id;
        },
    },
    methods: {
        remove() {
            this.$emit('remove', this.item);
        }
    }
}
</script>
