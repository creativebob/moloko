<template>
    <div class="grid-x">

        <div class="cell small-12">
            <store-component
                :catalogs="catalogs"
                @add="add"
            ></store-component>
        </div>

        <div
            class="cell small-12"
            v-if="schemes.length"
        >
            <table class="table-payments">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Процент</th>
                        <th></th>
                     </tr>
                </thead>

                <tbody>
                    <scheme-component
                        v-for="scheme in schemes"
                        :scheme="scheme"
                        :key="scheme.id"
                        @remove="remove"
                    ></scheme-component>
                    </tbody>
            </table>
        </div>

<!--        <modal-archive-component-->
<!--            :item="acrchivingScheme"-->
<!--            @remove="remove"-->
<!--        ></modal-archive-component>-->
    </div>
</template>

<script>
    export default {
        components: {
            'store-component': require('./StoreComponent'),
            'scheme-component': require('./SchemeComponent'),
            // 'modal-archive-component': require('./ModalArchiveComponent'),
        },
        props: {
            catalogs: Array,
            agentSchemes: Array,
        },
        data() {
            return {
                schemes: this.agentSchemes,
                // acrchivingScheme: {
                //     id: null,
                //     name: null
                // },
            }
        },
        // computed: {
        //     showStoreComponent() {
        //         return this.$store.getters.PAYMENTS_TOTAL < this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
        //     },
        //     payments() {
        //         return this.$store.getters.PAYMENTS;
        //     },
        //     paymentsAmount() {
        //         return this.$store.getters.PAYMENTS_TOTAL;
        //     },
        //     canCancel() {
        //         return this.$store.getters.HAS_OUTLET_SETTING('use-cash-register') && this.$store.getters.HAS_OUTLET_SETTING('payment-edit') && this.$store.state.lead.estimate.conducted_at === null;
        //     }
        // },
        methods: {
            add(data) {
                let found = this.schemes.find(obj => obj.catalog_id == data.catalog_id);
                if (!found) {
                    this.schemes.push(data);
                }
            },
            // openModalArchive(data) {
            //     this.acrchivingScheme = data
            // },
            remove(id) {
                const index = this.schemes.findIndex(obj => obj.id === id);
                this.schemes.splice(index, 1);
            },
        },
        // filters: {
        //     decimalPlaces(value) {
        //         return parseFloat(value).toFixed(2);
        //     },
        //     decimalLevel: function (value) {
        //         return parseFloat(value).toLocaleString();
        //     },
        // },
        // directives: {
        //     'reveal': {
        //         bind: function (el) {
        //             new Foundation.Reveal($(el))
        //         },
        //     }
        // }
    }
</script>
