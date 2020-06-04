<template>

    <div>
        <div
            class="dropdown-pane properties-dropdown"
            id="properties-dropdown"
            data-dropdown
            data-position="bottom"
            data-alignment="center"
            data-close-on-click="true"
            v-show="!open"
        >

            <div class="grid-x grid-padding-x">
                <div class="small-12 cell">

                    <ul class="checker" id="properties-list">
                        <li
                            v-for="property in propertiesWithMetrics"
                        >
                            <span
                                class="parent"
                                :data-open="'property-' + property.id"
                                @click="openMetrics('property-' + property.id)"
                            >{{ property.name }}</span>
                            <div
                                class="checker-nested"
                                 :id="'property-' + property.id"
                            >
                                <ul class="checker">
                                    <li
                                        v-for="metric in property.metrics"
                                        class="checkbox"
                                    >
                                        <input
                                            type="checkbox"
                                            name="metrics[]"
                                            :value="metric.id"
                                            :id="'checkbox-metric-' + metric.id"
                                            class="change-metric"
                                            @click="changeMetric(metric, $event)"
                                            :checked="getMetric(metric.id)"
                                        >
                                        <label
                                            :for="'checkbox-metric-' + metric.id"
                                        >
                                            <span>{{ metric.name }}</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>

                        </li>
                    </ul>

                </div>

                <div class="small-12 cell wrap-add-new-metric">
                    <label>Создать свойство
                        <select
                            @change="openForm"
                            v-model="propertyId"
                        >
                        <option
                            value="0"
                            @change="openForm"
                        >Выберите свойство</option>
                            <option
                                v-for="property in properties"
                                :value="property.id"
                            >{{ property.name }}</option>
                        </select>
                    </label>
                </div>
            </div>

        </div>

        <template id="properties-form">
            <fieldset>
                <legend>
                    <a
                        v-if="!open"
                        @click="resetPropertyId"
                        data-toggle="properties-dropdown">Добавить метрику</a>
                    <a
                        v-else
                        @click="resetMetricForm"
                    >Отменить</a>
                </legend>

<!--                <form-->
<!--                    v-abide-->
<!--                    data-abide-->
<!--                    novalidate-->
<!--                >-->
                    <div
                        v-if="open"
                        class="grid-x grid-padding-x"
                        id="propertyForm"
                    >

                        <div class="small-12 cell">
                            <label>Название:
                                <input
                                    type="text"
                                    v-model="name"
                                    required
                                >
                                <span class="form-error">Имя обязательно</span>
                            </label>

                        </div>
                        <div class="small-12 cell">
                            <label>Алиас:
                                <input
                                    type="text"
                                    v-model="alias"
                                >
                            </label>
                        </div>
                        <div class="small-12 cell">
                            <label>Описание:
                                <textarea
                                    v-model="description"
                                ></textarea>
                            </label>
                        </div>

                        <template v-if="type == 'numeric'">
                            <div class="small-6 cell">
                                <label>Минимум
                                    <input
                                        type="number"
                                        v-model="min"
                                        required
                                    >
                                    <span class="form-error">Имя обязательно</span>
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Максимум
                                    <input
                                        type="number"
                                        v-model="max"
                                        required
                                    >
                                    <span class="form-error">Имя обязательно</span>
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Единица измерения
                                    <select
                                        v-model="unitId"
                                    >
                                        <option
                                            v-for="unit in actualUnits"
                                            :value="unit.id"
                                        >{{ unit.abbreviation }}</option>
                                    </select>
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Знаки после запятой
                                    <select
                                        v-model="decimalPlace"
                                    >
                                        <option
                                            v-for="(decimalPlace, index) in decimalPlaces"
                                            :value="index"
                                            :selected="index === 0"
                                        >{{ decimalPlace }}</option>
                                    </select>
                                    <!--                                {{ Form::select('decimal_place', ['0' => '0', '1' => '0,0', '2' => '0,00', '3' => '0,000'], null) }}-->
                                </label>
                            </div>
                        </template>

                        <template v-else-if="type == 'percent'">
                            <div class="small-6 cell">
                                <label>Минимум
                                    <input
                                        type="number"
                                        v-model="min"
                                    >
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Максимум
                                    <input
                                        type="number"
                                        v-model="max"
                                    >
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Единица измерения
                                    <select
                                        v-model="unitId"
                                    >
                                        <option
                                            v-for="unit in actualUnits"
                                            :value="unit.id"
                                        >{{ unit.abbreviation }}</option>
                                    </select>
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Знаки после запятой
                                    <select
                                        v-model="decimalPlace"
                                    >
                                        <option
                                            v-for="(decimalPlace, index) in decimalPlaces"
                                            :value="index"
                                            :selected="index == 0"
                                        >{{ decimalPlace }}</option>
                                    </select>
                                    <!--                                {{ Form::select('decimal_place', ['0' => '0', '1' => '0,0', '2' => '0,00', '3' => '0,000'], null) }}-->
                                </label>
                            </div>
                        </template>

                        <template v-else="type == 'list'">
                            <div class="small-12 cell">
                                <div class="radiobutton">
                                    <input
                                        type="radio"
                                        name="list_type"
                                        value="list"
                                        v-model="listType"
                                        id="metric-list-type"
                                        checked
                                    >
                                    <label for="metric-list-type"><span>Выбор нескольких значений</span></label>
                                    <input
                                        type="radio"
                                        name="list_type"
                                        value="select"
                                        v-model="listType"
                                        id="metric-select-type"
                                    >
                                    <label for="metric-select-type"><span>Выбор одного значения</span></label>
                                </div>
                            </div>

                            <div class="small-12 cell input-group inputs wrap-add-list-metric">

                                <input
                                    type="text"
                                    placeholder="Введите значение"
                                    v-model="value"
                                    @keydown.enter.prevent="addValue"
                                >
                                <!--                            {{ Form::text('value', null, ['id' => 'value', 'placeholder' => 'Введите значение']) }}-->

                                <div class="input-group-button">
                                    <a
                                        class="button add-value"
                                        @click="addValue"
                                    >Добавить в список</a>
                                </div>
                            </div>

                            <div class="small-12 cell wrap-table-list-metric">
                                <table id="values-table" class="tablesorter unstriped table-list-metric">
                                    <tbody id="values-tbody">
                                    <tr
                                        v-for="(value, index) in values"
                                        class="item"
                                    >
                                        <td class="td-drop"><div class="sprite icon-drop"></div></td>
                                        <td>{{ value }}</td>
                                        <td class="td-delete">
                                            <a
                                                @click="removeValue(index)"
                                                class="icon-delete sprite"
                                                data-open="delete-metric-value"
                                            ></a>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>

                        </template>

                        <div class="small-12 cell checkbox">
                            <input
                                type="checkbox"
                                v-model="isRequired"
                                id="checkbox-required"
                            >
                            <label for="checkbox-required">
                                <span>Обязательна для заполнения</span>
                            </label>
                        </div>

                        <div
                            class="small-12 cell"
                            v-if="errors.length"
                        >
                            <b>Пожалуйста исправьте указанные ошибки:</b>
                            <ul>
                                <li v-for="error in errors">{{ error }}</li>
                            </ul>
                        </div>

                        <div class="small-12 cell text-center">
                            <input
                                type="submit"
                                @click.prevent="addMetric"
                                value="Создать метрику"
                                class="button green-button"
                            >
<!--                            <a-->
<!--                                type="submit"-->
<!--                                class="button green-button"-->
<!--                                @click.prevent="addMetric"-->
<!--                            >Создать метрику</a>-->
                        </div>

                    </div>

<!--                </form>-->

            </fieldset>
        </template>
    </div>

</template>

<script>
	export default {
		data() {
			return {
                propertyId: 0,
                open: false,
                name: null,
                alias: null,
                description: null,
                isRequired: false,
                type: null,

                // Список
                listType: 'list',
                value: null,
                values: [],

                // Числа или проценты
                min: null,
                max: null,
                unitId: null,
                units: [],
                decimalPlace: 0,
                decimalPlaces: ['0', '0,0', '0,00', '0,000'],

                // Ошибки
                errors: [],
			}
		},
		props: {
            properties: Array,
            entity: String,
            entityId: Number,
            metrics: Array,
		},
        computed: {
            propertiesWithMetrics() {
                return this.properties.filter(property => {
                    return property.metrics.length;
                })
            },
            actualUnits() {
                if (this.units.length) {
                    let found = this.units.find(unit => parseInt(unit.ratio) === 1);
                    this.unitId = found.id;

                    return this.units;
                }
            },
        },
        methods: {
            openMetrics(id) {
                // Скрываем все состав
                $('.checker-nested').hide();
                // Показываем нужную
                $('#' + id).show();
            },
		    changeMetric(metric, event) {
                if (event.target.checked === true) {
                    this.$emit('add-metric', metric);
                } else {
                    this.$emit('remove-metric', metric.id);
                }
            },
            getMetric(metricId) {
                if (this.metrics.length) {
                    let found = this.metrics.find(metric => metric.id == metricId);
                    if (found) {
                        return true;
                    } else {
                        return false;
                    }
                }
            },
            resetPropertyId() {
                this.propertyId = 0;
            },
            resetMetricForm() {
                this.open = false;
                this.name = null;
                this.description = null;
                this.alias = null;
                this.type = null;

                this.propertyId = 0;
                this.isRequired = null;
                this.listType = 'list';
                this.values = [];

                this.decimalPlace = 0;
                this.min = null;
                this.max = null;
                this.unitId = null;
            },
            openForm() {
		        let property = this.properties.find(obj => obj.id == this.propertyId);
                if (property) {
                    this.type = property.type;

                    if (property.type !== 'list') {
                        this.units = property.units_category.units;
                    }

                    this.open = true;
                }
            },
            addValue() {
                this.values.push(this.value);
                this.value = null;
            },
            removeValue(index) {
                this.values.splice(index, 1);
            },
            addMetric() {

                this.errors = [];

                if (! this.name) {
                    this.errors.push('Укажите имя');
                }

                if(! this.errors.length) {

                    let data = {};
                    data.name = this.name;
                    data.description = this.description;
                    data.alias = this.alias;
                    data.type = this.type;
                    data.property_id = this.propertyId;
                    data.is_required = this.isRequired;
                    data.entity_id = this.entityId;

                    switch (this.type) {
                        case 'list':
                            data.list_type = this.listType;
                            data.metric_values = this.values;
                            break;

                        case 'numeric':
                            data.decimal_place = this.decimalPlace;
                            data.min = parseInt(this.min);
                            data.max = parseInt(this.max);
                            data.unit_id = this.unitId;
                            break;

                        case 'percent':
                            data.decimal_place = this.decimalPlace;
                            data.min = this.min;
                            data.max = this.max;
                            data.unit_id = this.unitId;
                            break;
                    }

                    // console.log(data);

                    axios
                        .post('/admin/metrics', data)
                        .then(response => {
                            this.$emit('add-new-metric', response.data);
                            this.resetMetricForm();

                            // Foundation.reInit($('#pproperties-list'));
                        })
                        .catch(error => {
                            console.log(error)
                        });
                }
            }
        },
        directives: {
            'dropdown': {
                bind: function (el) {
                    new Foundation.Dropdown($(el))
                }
            },
            'abide': {
                bind: function (el) {
                    new Foundation.Abide($(el))
                }
            },
        }
	}
</script>
