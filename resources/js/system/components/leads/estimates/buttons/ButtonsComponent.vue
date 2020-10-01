<template>
    <div class="grid-x">
        <div
            v-if="! isRegistered"
            class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top"
        >
            <a
                class="button"
                @click="save"
            >Сохранить</a>
        </div>

        <div
            v-if="! isRegistered && showRegisterButton"
            class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top"
        >
            <a
                class="button"
                @click="registerEstimate"
             >Оформить</a>
        </div>

        <!--            <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">-->
        <!--                <production-button-component></estimate-production-button-component>-->
        <!--            </div>-->

        <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">
            <sale-button-component></sale-button-component>
        </div>

        <div
            v-if="isRegistered"
            class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top"
        >
            <a
                :href="'/admin/leads/' + lead.id + '/print'"
                target="_blank"
                class="button"
            >Печать</a>
        </div>

        <div
            v-if="loading"
            class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top"
        >Обновление</div>

    </div>
</template>

<script>
    export default {
        components: {
            'sale-button-component': require('./SaleButtonComponent'),
        },
        data() {
            return {
                loading: false,
            }
        },
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

            isRegistered() {
                return this.estimate.is_registered === 1;
            },

            showRegisterButton() {
                return this.$store.state.lead.goodsItems.length > 0 || this.$store.state.lead.servicesItems.length > 0;
            }
        },
        methods: {
            save() {
                let res = window.submitAjax('form-lead');
                if (res) {

                    let data = {
                        lead: this.lead,
                        client: this.client,
                        estimate: this.estimate,
                        goods_items: this.goodsItems,
                    };

                    this.update(data);
                }
            },
            registerEstimate() {
                let res = window.submitAjax('form-lead');
                if (res) {

                    let data = {
                        lead: this.lead,
                        client: this.client,
                        estimate: this.estimate,
                        goods_items: this.goodsItems,
                        is_registered: true
                    };

                    this.update(data);
                }
            },
            update(data) {
                this.loading = true;
                axios
                    .patch('/admin/leads/' + this.lead.id, data)
                    .then(response => {
                        console.log(response.data);
                        this.$store.commit('SET_LEAD', response.data.lead);
                        this.$store.commit('SET_ESTIMATE', response.data.estimate);
                        this.$store.commit('SET_GOODS_ITEMS', response.data.goods_items);
                    })
                    .catch(error => {
                        console.log(error)
                    })
                    .finally(() => (this.loading = false));

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
