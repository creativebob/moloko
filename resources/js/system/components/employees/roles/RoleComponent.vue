<template>
    <tr class="item">
        <td>{{ item.role.name }}</td>
        <td>{{ item.department.name }}</td>
        <td>
            <template
                v-if="item.position"
            >{{ item.position.name }}
            </template>
            <template
                v-else
            >Спецправо
            </template>
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
        <!--        {{ Form::hidden('access[]', item->role->id .','. item->department->id .','. $position->id) }}-->
    </tr>
</template>

<script>
export default {
    props: {
        item: Object
    },
    // data() {
    //     return {
    //         value: this.item.role_id + ',' + this.item.department_id + ',' + this.item.position_id,
    //     }
    // },
    computed: {
        value() {
            return this.item.role_id + ',' + this.item.department_id + ',' + this.item.position_id;
        }
    },
    methods: {
        remove() {
            this.$emit('remove', this.item);
        }
    }
}
</script>
