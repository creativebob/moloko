<template>
    <tbody>
    <estimates-services-item-component
        v-for="(item, index) in items"
        :id="item.id"
        :index="index"
        :key="item.id"
        :settings="settings"
        @open-modal-remove="openModal(item, index)"
    ></estimates-services-item-component>
    <div
        class="reveal rev-small"
        id="delete-estimates_services_item"
        data-reveal
        v-reveal
    >
        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>Удаление</h5>
            </div>
        </div>
        <div class="grid-x align-center modal-content ">
            <div class="small-10 cell text-center">
                <p>Удаляем "{{ itemName }}", вы уверены?</p>
            </div>
        </div>
        <div class="grid-x align-center grid-padding-x">
            <div class="small-6 medium-4 cell">
                <button
                    @click.prevent="deleteItem"
                    data-close
                    class="button modal-button"
                    type="submit"
                >Удалить
                </button>
            </div>
            <div class="small-6 medium-4 cell">
                <button data-close class="button modal-button" type="submit">Отменить</button>
            </div>
        </div>
    </div>
    </tbody>
</template>

<script>
export default {
    components: {
        'estimates-services-item-component': require('./EstimatesServicesItemComponent'),
    },
    props: {
        items: Array,
        settings: Array,
    },
    data() {
        return {
            item: null,
            itemName: null,
            itemIndex: null,
        }
    },
    mounted() {
        Foundation.reInit($('#delete-estimates_services_item'));
    },
    computed: {
        estimate() {
            return this.$store.state.lead.estimate;
        },
        isRegistered() {
            return this.$store.state.lead.estimate.registered_at;
        },
        itemsAmount() {
            let amount = 0;
            this.items.forEach(item => {
                return amount += parseFloat(item.amount)
            });
            return amount;
        },
        itemsDiscount() {
            let discount = 0;
            this.items.forEach(item => {
                return discount += parseFloat(item.discount_currency)
            });
            return discount;
        },
        itemsTotal() {
            let total = 0;
            this.items.forEach(item => {
                return total += parseFloat(item.total)
            });
            return total;
        },
        itemsTotalPoints() {
            let points = 0;
            this.items.forEach(item => {
                return points += parseFloat(item.points)
            });
            return points;
        },
    },
    methods: {
        openModal(item, index) {
            this.itemIndex = index;
            this.item = item;
            this.itemName = item.service.process.name;
        },
        deleteItem() {
            this.$store.commit('REMOVE_SERVICE_ITEM', this.item.id);
            $('#delete-estimates_services_item').foundation('close');
        },
    },
    filters: {
        decimalPlaces(value) {
            return parseFloat(value).toFixed(2);
        },
        decimalLevel: function (value) {
            return parseFloat(value).toLocaleString();
        },
        onlyInteger(value) {
            return Math.floor(value);
        },
    },
    directives: {
        'reveal': {
            bind: function (el) {
                new Foundation.Reveal($(el))
            },
        }
    }
}
</script>
