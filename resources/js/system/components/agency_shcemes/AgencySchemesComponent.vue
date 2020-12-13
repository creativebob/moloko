<template>
    <div class="grid-x">

        <div class="cell small-12">
            <store-component
                :catalog-id="catalog.id"
                :alias="alias"
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
                    <agency-scheme-component
                        v-for="scheme in schemes"
                        :scheme="scheme"
                        :key="scheme.id"
                        @open-modal-archive="openModalArchive"
                    ></agency-scheme-component>
                </tbody>
            </table>
        </div>

        <modal-archive-component
            :item="acrchivingScheme"
            @remove="remove"
        ></modal-archive-component>
    </div>
</template>

<script>
    export default {
        components: {
            'store-component': require('./StoreComponent'),
            'agency-scheme-component': require('./AgencySchemeComponent'),
            'modal-archive-component': require('./ModalArchiveComponent'),
        },
        props: {
            catalog: Object,
            alias: String
        },
        data() {
            return {
                schemes: this.catalog.agency_schemes,
                acrchivingScheme: {
                    id: null,
                    name: null
                },
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
                this.schemes.push(data);
            },
            openModalArchive(data) {
                this.acrchivingScheme = data
            },
            remove(id) {
                const index = this.schemes.findIndex(obj => obj.id === id);
                this.schemes.splice(index, 1);
            },
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        },
        // directives: {
        //     'reveal': {
        //         bind: function (el) {
        //             new Foundation.Reveal($(el))
        //         },
        //     }
        // }
    }
</script>
