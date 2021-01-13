<template>
    <div class="grid-x grid-margin-x tabs-margin-top">
        <div class="cell small-12 medium-auto">

            <template
                v-if="change"
            >
                <a
                    class="button"
                    @click="save"
                >Сохранить</a>
            </template>

            <template
                v-if="! isRegistered && showRegisterButton"
            >
                <a
                    class="button"
                    @click="registerEstimate"
                >Оформить</a>
            </template>

            <template
                v-if="isRegistered && showProductionButton"
            >
                <a
                    class="button"
                    @click="producedEstimate"
                >Произвести</a>
            </template>

            <template
                v-if="showSaleButton"
            >
                <button
                    class="button"
                    @click.prevent="conductedEstimate"
                >Закрыть чек
                </button>
            </template>
        </div>

        <div class="cell small-12 medium-shrink medium-text-right">
            <template
                v-if="isRegistered"
            >
                <a
                    :href="'/admin/leads/' + lead.id + '/print'"
                    target="_blank"
                    class="button button-print"
                ><span class="icon-print-order"></span>
            Печать заказа</a><br>


            <!-- <a
                :href="'/admin/leads/' + lead.id + '/print_sticker_stock'"
                target="_blank"
                class=""
            ><span class="icon-print-sticker"></span>
                Стикер
            </a> -->


            </template>

            <div
                v-if="loading"
                class="cell small-12 medium-2 small-text-center medium-text-left"
            >Идет обновление...
            </div>

        </div>

    </div>
</template>

<script>
export default {
    // data() {
    //     return {
    //         loading: false,
    //     }
    // },
    computed: {
        lead() {
            return this.$store.state.lead.lead;
        },
        client() {
            return this.$store.state.lead.client;
        },

        estimate() {
            return this.$store.state.lead.estimate;
        },
        goodsItems() {
            return this.$store.state.lead.goodsItems;
        },
        servicesItems() {
            return this.$store.state.lead.servicesItems;
        },
        labels() {
            return this.$store.state.lead.labels;
        },

        isRegistered() {
            return this.$store.state.lead.estimate.registered_at !== null;
        },

        showRegisterButton() {
            return this.$store.state.lead.goodsItems.length > 0 || this.$store.state.lead.servicesItems.length > 0;
        },

        showProductionButton() {
            return this.$store.state.lead.needProduction && this.$store.state.lead.goodsItems.length > 0 && this.$store.state.lead.estimate.produced_at == null;
        },

        showSaleButton() {
            return this.$store.state.lead.estimate.registered_at !== null && this.$store.state.lead.estimate.conducted_at === null && (this.$store.getters.PAYMENTS_TOTAL >= parseFloat(this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total));
        },
        change() {
            return this.$store.state.lead.change;
        },
        loading() {
            return this.$store.state.lead.loading;
        },
    },
    methods: {
        save() {
            let res = window.submitAjax('form-lead-personal');
            if (res) {

                let data = {
                    lead: this.lead,
                    client: this.client,
                    estimate: this.estimate,
                    goods_items: this.goodsItems,
                    services_items: this.servicesItems,
                    labels: this.labels,
                };

                this.update(data);
            }
        },
        registerEstimate() {
            let res = window.submitAjax('form-lead-personal');
            if (res) {

                let data = {
                    lead: this.lead,
                    client: this.client,
                    estimate: this.estimate,
                    goods_items: this.goodsItems,
                    services_items: this.servicesItems,
                    labels: this.labels,
                    is_registered: true
                };

                this.update(data);
            }
        },
        producedEstimate() {
            this.$store.dispatch('PRODUCED_ESTIMATE');
        },
        conductedEstimate() {
            this.$store.dispatch('CONDUCTED_ESTIMATE');
        },
        update(data) {
            this.$store.dispatch('UPDATE', data);
        },

        // update() {
        //     // Обновляем лида
        //     axios
        //         .patch('/admin/leads/' + this.lead.id, {
        //             main_phone: this.lead.main_phone,
        //             name: this.lead.name,
        //             company_name: this.lead.company_name,
        //             city_id: this.lead.location.city_id,
        //             address: this.lead.location.address,
        //             email: this.lead.email,
        //         })
        //         .then(response => {
        //             this.$store.commit('SET_LEAD', response.data.lead);
        //         })
        //         .catch(error => {
        //             console.log(error)
        //         });
        //
        //     // Обновляем товары в смете
        //     axios
        //         .patch('/admin/estimates_goods_items/' + this.lead.id, {
        //             main_phone: this.lead.main_phone,
        //             name: this.lead.name,
        //             company_name: this.lead.company_name,
        //             city_id: this.lead.location.city_id,
        //             address: this.lead.location.address,
        //             email: this.lead.email,
        //         })
        //         .then(response => {
        //             this.$store.commit('SET_LEAD', response.data.lead);
        //         })
        //         .catch(error => {
        //             console.log(error)
        //         });
        //
        // },
        // async update(data) {
        //     try {
        //         const response = axios.patch('/admin/leads/' + this.lead.id, data);
        //         console.log(response);
        //         this.$store.commit('SET_LEAD', response.lead);
        //         this.$store.commit('SET_GOODS_ITEMS', response.goods_items);
        //     } catch (error) {
        //         console.log(error)
        //     }
        // }
    }
}
</script>
