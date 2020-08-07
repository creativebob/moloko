<template>
    <div class="grid-x">
        <div class="cell small-12 medium-6">
            <search-component
                :cities="cities"
                @add="addCity"
            ></search-component>
        </div>

        <div class="cell small-12">
            <table
                class="hover unstriped"
                v-if="curCities.length"
            >
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Район</th>
                        <th>Область</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <city-component
                        v-for="city in curCities"
                        :city="city"
                        :key="city.id"
                        :filial-city-id="filialCityId"
                        @remove="removeCity"
                    ></city-component>
                </tbody>
            </table>
        </div>
     </div>
</template>

<script>
    export default {
        components: {
            'search-component': require('./SearchComponent'),
            'city-component': require('./CityComponent'),
        },
        props: {
            cities: Array,
            filialCities: Array,
            filialCityId: {
                type: Number,
                default: null
            },
        },
        data() {
            return {
                curCities: this.filialCities
            };
        },
        computed: {

        },
        methods: {
            addCity(city) {
                let found = this.curCities.find(item => item.id == city.id);
                if (! found) {
                    this.curCities.push(city);
                }
            },
            removeCity(id) {
                let index = this.curCities.findIndex(item => item.id === id);
                this.curCities.splice(index, 1);
            }
        }
    }
</script>
