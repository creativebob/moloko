<template>
    <div class="grid-x wrap-autofind">
        <div class="cell small-12">
            <legend>Найдены обращения: {{ histories.length }}</legend>
            <template
                v-if="loading"
            >Загружаем историю...</template>
            <table class="hover">
                <history-component
                    v-if="histories.length"
                    v-for="history in histories"
                    :history="history"
                    :key="history.id"
                ></history-component>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    components: {
        'history-component': require('./HistoryComponent'),
    },
    props: {
        leadHistory: {
            type: Array,
            default() {
                return []
            }
        }
    },
    data() {
        return {
            histories: this.leadHistory.length ? this.leadHistory : [],
            loading: false
        }
    },
    computed: {
        phone() {
            return this.$store.state.lead.lead.main_phone;
        }
    },
    watch: {
        phone() {
            this.getHistories();
        }
    },

    methods: {
        getHistories() {
            let mainPhone = this.$store.state.lead.lead.main_phone,
                phone;
            if (mainPhone && mainPhone.length > 0) {
                phone = mainPhone.replace(/\D+/g, "");
            }

            if (phone && phone.length == 11) {
                this.loading = true;

                axios
                    .post('/admin/leads/history', {
                        id: this.$store.state.lead.lead.id,
                        phone: phone
                    })
                    .then(response => {
                        this.histories = response.data;
                    })
                    .catch(error => {
                        console.log(error)
                        alert('Произошла ошибка зарузки истории, перезагрузите страницу!')
                    })
                    .finally(() => (this.loading = false));
            } else {
                this.histories = [];
            }
        }
    }
}
</script>
