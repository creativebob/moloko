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
                        v-for="(metric, index) in category.metrics"
                        :metric="metric"
                        :index="index"
                        :key="metric.id"
                    ></metric-component>

                </tbody>
            </table>
        </div>
        <div class="small-12 medium-4 cell">

            <metrics-component
                :properties="properties"
                :entity="entity"
                :entity-id="entityId"
                :category-id="categoryId"
            ></metrics-component>
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

			}
		},
		props: {
            category: Object,
            properties: Array,
            entity: String,
            entityId: Number,
            categoryId: Number,
		},
	}
</script>
