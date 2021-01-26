<template>
    <tr>
        <td>{{ employee.staffer.position.name }}</td>
        <td>{{ employee.staffer.rate }}</td>
        <td>{{ employee.staffer.department.name }}
            <template
                v-if="employee.staffer.filial_id != employee.staffer.department_id"
            >
            <br>
            <span>{{ employee.staffer.filial.name }}</span>
            </template>
        </td>
        <td>{{ employee.employment_date | formatDate }}</td>
        <td>
            <template
                v-if="employee.dismissal_date"
            >{{ employee.dismissal_date | formatDate }}</template>
        </td>
<!--        <td>{{ employee.dismissal_description }}</td>-->
        <td class="actions">
            <a
                v-if="employee.dismissal_date == null"
                class="button alert tiny"
                @click="openModal"
                data-open="modal-dismiss"
            >Уволить</a>
        </td>
    </tr>

</template>

<script>
import moment from 'moment'

export default {
    props: {
        employee: Object,
    },
    methods: {
        openModal() {
            this.$emit('open-modal', this.employee);
        }
    },
    filters: {
        formatDate: function (value) {
            if (value) {
                return moment(String(value)).format('DD.MM.YYYY');
            }
        },
        decimalPlaces(value) {
            return parseFloat(value).toFixed(2);
        },
        decimalLevel: function (value) {
            return parseFloat(value).toLocaleString();
        },
    },
}
</script>
