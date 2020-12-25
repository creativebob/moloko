<template>
    <fieldset
        v-if="labels.length"
        class="fieldset-access"
    >
        <legend>Метки заказа</legend>
        <ul

        >
            <li
                v-for="label in labels"
                class="checkbox"
            >
                <input
                    type="checkbox"
                    :value="label.id"
                    :id="'checkbox-' + label.id"
                    @change="change(label.id, $event.target.checked)"
                    :checked="checkChecked(label.id)"
                    :disabled="isConducted"
                >
                <label :for="'checkbox-' + label.id">
                    <span>{{ label.name }}</span>
                </label>
            </li>
        </ul>
    </fieldset>
</template>

<script>
export default {
    props: {
        activeLabels: Array,
    },
    data() {
        return {
            labels: []
        }
    },
    computed: {
        isConducted() {
            return this.$store.getters.IS_CONDUCTED;
        }
    },
    mounted() {
        axios
            .post('/admin/labels/get')
            .then(response => {
                this.labels = response.data;
            })
            .catch(error => {
                alert('Ошибка загрузки меток заказа, перезагрузите страницу!')
                console.log(error)
            });
    },
    methods: {
        checkChecked(id) {
            return this.activeLabels.find(obj => obj.id == id);
        },
        change(id, status) {
            const data = {
                id: id,
                status: status
            }
            this.$store.commit('CHANGE_LABELS', data);
        }
    }
}
</script>
