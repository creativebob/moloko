<template>
    <tr>
        <td>{{ history.created_at | formatDate }}</td>
        <td>
            <span
                class="lead-name"
                title="Использовать данные"
            >{{ history.client ? history.client.clientable.name : history.name }}</span>
            <br>
            {{ history.location.city ? history.location.city.name : '- Город не указан -' }} {{ history.location.address ? ', ' + history.location.address  : '' }}
        </td>
        <td>
            {{ history.choice ? history.choice.name : '' }}<br>
            {{ history.stage.name }}
        </td>
        <td>
            {{ history.manager ? history.manager.name : 'Не назначен' }}
            <br>
            №: <a
            :href="'/admin/leads/' + history.id + '/edit'"
            :id="history.id"
            title="Перейти"
        >{{ history.case_number }}</a>
        </td>
    </tr>
</template>

<script>
import moment from 'moment';

export default {
    props: {
        history: Object
    },
    filters: {
        formatDate: function (value) {
            if (value) {
                return moment(String(value)).format('DD.MM.YYYY')
            }
        },
    },
}
</script>
