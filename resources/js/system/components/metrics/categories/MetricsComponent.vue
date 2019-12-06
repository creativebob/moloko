<template>

    <div>
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

                <div
                    v-if="open"
                    class="grid-x grid-padding-x"
                    id="property-form"
                >

                    <div class="small-12 cell">
                        <label>Название:
                            <input
                                type="text"
                                v-model="name"
                                required
                            >
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
                                <select>
                                    <option
                                        v-for="unit in units"
                                        :value="unit.id"
                                    >{{ unit.abbreviation }}</option>
                                </select>
                            </label>
                        </div>
                        <div class="small-6 cell">
                            <label>Знаки после запятой
                                <select>
                                    <option
                                        v-for="decimalPlace in decimalPlaces"
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
                                <select>
                                    <option
                                        v-for="unit in units"
                                        :value="unit.id"
                                    >{{ unit.abbreviation }}</option>
                                </select>
                            </label>
                        </div>
                        <div class="small-6 cell">
                            <label>Знаки после запятой
                                <select>
                                    <option
                                        v-for="decimalPlace in decimalPlaces"
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
                                    name="select-type"
                                    value="list"
                                    v-model="listType"
                                    id="metric-list-type"
                                    checked
                                >
                                <label for="metric-list-type"><span>Выбор нескольких значений</span></label>
                                <input
                                    type="radio"
                                    name="select-type"
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

                    <div class="small-12 cell text-center">
                        <a
                            class="button green-button"
                            @click="addMetric"
                        >Создать метрику</a>
                    </div>

                </div>
            </fieldset>
        </template>

        <div
            class="dropdown-pane properties-dropdown"
            id="properties-dropdown"
            data-dropdown data-position="bottom"
            data-alignment="center"
            data-close-on-click="true"
            v-show="!open"
        >

            <div class="grid-x grid-padding-x">
                <div class="small-12 cell">
                    <ul class="checker" id="properties-list">
<!--                            @foreach ($properties as $property)-->
<!--                            @if($property->metrics->isNotEmpty())-->
<!--                            @include('products.common.metrics.property', ['property' => $property])-->
<!--                            @endif-->
<!--                            @endforeach-->
                        <li>
<!--                                <span class="parent" data-open="property-{{ $property->id }}">{{ $property->name }}</span>-->
<!--                                <div class="checker-nested" id="property-{{ $property->id }}">-->
<!--                                    <ul class="checker">-->
<!--&lt;!&ndash;                                        @foreach ($property->metrics as $metric)&ndash;&gt;-->
<!--&lt;!&ndash;                                        @include('products.common.metrics.metrics', ['metric' => $metric])&ndash;&gt;-->
<!--&lt;!&ndash;                                        @endforeach&ndash;&gt;-->

<!--                                    </ul>-->
<!--                                </div>-->
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
                listType: null,
                min: null,
                max: null,
                unit: null,
                units: [],
                decimalPlace: null,
                decimalPlaces: ['0', '0,0', '0,00', '0,000'],
                value: null,
                values: []
			}
		},
		props: {
            properties: Array,
            entity: String,
            entityId: Number,
            categoryId: Number,
		},
        computed: {
            // propertiesWithMetrics() {
            //     return properties.filter(property => {
            //
            //     })
            // }
        },
        methods: {
            resetPropertyId() {
                this.propertyId = 0;
            },
            resetMetricForm() {
                this.open = false;
            },
            openForm() {

                axios
                    .post('/admin/ajax_add_property', {
                        id: this.propertyId,
                        entity: this.entity
                    })
                    .then(response => {
                        this.units = response.data.units;
                        this.type = response.data.type;

                        this.open = true;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            addValue() {
                this.values.push(this.value);
                this.value = null;
            },
            removeValue(index) {
                this.values.splice(index, 1);
            },
            addMetric() {

            }
        }
	}
</script>
