<template>
    <div
        v-if="isShow"
        class="grid-x grid-padding-x"
    >
        <div class="cell small-12">
            <select
                v-model="estimatesCancelGroundId"
            >
                <option
                    v-for="estimatesCancelGround in estimatesCancelGrounds"
                    :value="estimatesCancelGround.id"
                >{{ estimatesCancelGround.name }}</option>
            </select>
        </div>
        <div class="cell small-6 medium-4">
            <button
                class="button"
                @click.prevent="dismissed(true)"
            >С убытком</button>
        </div>
        <div class="cell small-6 medium-4">
            <button
                class="button"
                @click.prevent="dismissed(false)"
            >Без убытка</button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            estimatesCancelGrounds: [],
            estimatesCancelGroundId: null
        }
    },
    mounted() {
        axios
            .post('/admin/estimates_cancel_grounds/get')
            .then(response => {
                this.estimatesCancelGrounds = response.data;
                if (this.estimatesCancelGrounds.length) {
                    this.estimatesCancelGroundId = this.estimatesCancelGrounds[0].id;
                }
            })
            .catch(error => {
                alert('Ошибка загрузки списаний заказа, перезагрузите страницу!')
                console.log(error)
            });
    },
    computed: {
        isShow() {
            return this.$store.state.lead.estimate.is_dismissed == 0 && this.$store.getters.ACTUAL_PAYMENTS.length > 0 && (this.$store.getters.HAS_OUTLET_SETTING('dismiss-with-loss') || this.$store.getters.HAS_OUTLET_SETTING('dismiss-without-loss'));
        }
    },
    methods: {
        dismissed(loss) {
            const data = {
                loss: loss,
                estimates_cancel_ground_id: this.estimatesCancelGroundId
            };
            this.$store.dispatch('DISMISSED_ESTIMATE', data);
        }
    }
}
</script>
