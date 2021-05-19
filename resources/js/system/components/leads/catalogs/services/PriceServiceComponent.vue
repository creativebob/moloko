<template>
    <li
        :class="[{ priority : price.is_priority}, { hit : price.is_hit}, { new : price.is_new }, {full : fullFLow}]"
    >
        <a
            @click="addPriceToEstimate"
        >
            <!-- Отрисовываем ссылку на фото только в режиме отображения товаров "Карточкой", дабы не грузить браузер -->
            <div v-if="view == 'view-card'" class="prise-photo">
                <img :src="getPhotoPath(price, 'small')">
<!--                <ul>-->
<!--                    <li>Кол-во: {{ countInStocks.count }}</li>-->
<!--                    <li>Резерв: {{ countInStocks.reserve }}</li>-->
<!--                    <li>Свободно: {{ countInStocks.free }}</li>-->
<!--                </ul>-->
            </div>

            <div class="grid-x">
                <div class="cell main-block">
                    <div class="grid-x">
                        <div class="cell auto price-name">
                            <h4>
                                <span class="items-product-name">{{ price.service.process.name }}</span>


                                <!--                                                    <span class="items-product-manufacturer"> ({{ $cur_prices_service->service->process->manufacturer->name ?? '' }})</span>-->
                            </h4>
                        </div>

                        <div class="cell shrink wrap-product-price">
                            <span
                                class="items-product-price"
                                :class="[{'with-discount' : price.price != price.total_catalogs_item_discount }]"
                            >{{ price.total_catalogs_item_discount | roundToTwo | level }}</span>
                            <span
                                v-if="showPoints"
                                  class="points"
                            >({{ price.points | roundToTwo | level }})</span>
                        </div>
                    </div>
                </div>

                <div class="cell extra-block">
                    <div class="grid-x extra-info">
                        <div class="cell auto">
                                <span v-if="((price.price - price.total_catalogs_item_discount) * 100 / price.price)>0"
                                      class="price-discount-extra">{{ (price.price - price.total_catalogs_item_discount) * 100 / price.price }}%</span>
                            <span v-if="price.is_hit" class="price-hit">Hit</span>
                            <span v-if="price.is_new" class="price-new">New</span>
                        </div>
                        <div
                            v-if="countInEstimate > 0"
                            class="cell shrink counter-price-service"
                        >
                            {{ countInEstimate | level }}
                        </div>
                    </div>
                </div>

                <!--                                                 <div class="cell desc-block">
                                                        <p class="items-product-description">{{ price.service.description }}</p>
                                                    </div> -->
            </div>
        </a>
    </li>
</template>

<script>
    export default {
        props: {
            price: Object,
            view: String
        },
        data() {
            return {
                // stocks: this.price.service.stocks,
            }
        },
        computed: {
            showPoints() {
                const canPaymentPoints = this.$store.getters.HAS_OUTLET_SETTING('payment-point');
                return this.price.points > 0 && canPaymentPoints;
            },
            // countInStocks() {
            //     let count = 0,
            //         reserve = 0,
            //         free = 0;
            //     if (this.price.service.stocks) {
            //         this.price.service.stocks.forEach(stock => {
            //             count += parseFloat(stock.count);
            //             reserve += parseFloat(stock.reserve);
            //             free += parseFloat(stock.free);
            //         })
            //     }
            //     return {
            //         count: count,
            //         reserve: reserve,
            //         free: free
            //     }
            // },
            countInEstimate() {
                return this.$store.getters.COUNT_SERVICE_ITEM_IN_ESTIMATE(this.price.id);
            },
            fullFLow() {
                return this.price.service.process.is_auto_initiated == 0 && !this.price.service.actual_flows.length;
            }
        },
        methods: {
            addPriceToEstimate() {
                this.$store.commit('ADD_SERVICE_ITEM_TO_ESTIMATE', this.price);
            },
            getPhotoPath(price, format = 'medium') {
                if (this.price.service.process.photo) {
                    // Умолчание по формату. Плюс защита от ошибок при указании формата
                    format = (format !== ('small' || 'medium' || 'large')) ? 'medium' : format;
                    return '/storage/' + price.company_id + '/media/processes/' + price.service.process.id + '/img/' + format + '/' + price.service.process.photo.name;
                } else {
                    return '';
                }
            },
        },

        filters: {
            roundToTwo: function (value) {
                return Math.trunc(parseFloat(Number(value).toFixed(2)) * 100) / 100;
            },

            // Создает разделители разрядов в строке с числами
            level: function (value) {
                return Number(value).toLocaleString();
            },
        },
    }
</script>
