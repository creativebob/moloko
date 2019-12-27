<template>

    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-8 cell">
            <table>
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Минимум</th>
                    <th>Максимум</th>
                    <th>Подтверждение</th>
                    <th>Отрицание</th>
                    <th>Цвет</th>
                    <th>Список</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="table-metrics">
                    <metric-component
                        v-for="(metric, index) in metrics"
                        :metric="metric"
                        :index="index"
                        :key="metric.id"
                        @open-modal-remove="openModalRemove"
                    ></metric-component>

                </tbody>
            </table>
        </div>
        <div class="small-12 medium-4 cell">

            <metrics-component
                :properties="actualProperties"
                :entity="entity"
                :entity-id="entityId"
                :metrics="metrics"
                @add-metric="addMetric"
                @remove-metric="removeMetric"
                @add-new-metric="addNewMetric"
            ></metrics-component>
        </div>

        <div class="reveal rev-small" id="modal-delete-metric" data-reveal data-close-on-click="false">
            <div class="grid-x">
                <div class="small-12 cell modal-title">
                    <h5>Удаление метрики</h5>
                </div>
            </div>
            <div class="grid-x align-center modal-content ">
                <div class="small-10 cell text-center">
                    <p>Удаляем метрику "{{ deletingMetric.name }}", вы уверены?</p>
                </div>
            </div>
            <div class="grid-x align-center grid-padding-x">
                <div class="small-6 medium-4 cell">
                    <button
                        data-close
                        class="button modal-button metric-delete-button"
                        @click="deleteMetric"
                    >Удалить</button>
                </div>
                <div class="small-6 medium-4 cell">
                    <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
                </div>
            </div>
<!--            <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>-->
        </div>
    </div>

</template>

<script>
	export default {
        components: {
            'metric-component': require('./MetricComponent.vue'),
            'metrics-component': require('./MetricsComponent.vue')
        },
		data() {
			return {
			    actualProperties: [],
                metrics: [],
                deletingMetric: {},
			}
		},
        mounted() {
            this.actualProperties = this.properties;

            if (this.category.metrics.length) {
                this.metrics = this.category.metrics;
            }
        },
        computed: {
            // actualMetrics() {
            //     return this.metrics;
            // }
        },
		props: {
            category: Object,
            properties: Array,
            entity: String,
            entityId: Number,
            categoryId: Number,
		},

        methods: {
            addMetric(metric) {
                this.metrics.push(metric);
            },
            removeMetric(metricId) {
                let index = this.metrics.findIndex(metric => metric.id === metricId);
                this.metrics.splice(index, 1);
            },
            addNewMetric(metric) {
                this.addMetric(metric);

                let property = this.actualProperties.find(obj => obj.id == metric.property_id);
                if (property) {
                    property.metrics.push(metric);
                }


                // axios
                //     .get('/admin/properties', {
                //         params: {
                //             entity_id: this.entityId
                //         }
                //     })
                //     .then(response => {
                //         this.actualProperties = response.data;
                //     })
                //     .catch(error => {
                //         console.log(error)
                //     });

            },
            openModalRemove(metric) {
                this.deletingMetric = metric;
            },
            deleteMetric() {
                this.removeMetric(this.deletingMetric.id)
                this.deletingMetric = {};
            }
        }
	}
</script>
