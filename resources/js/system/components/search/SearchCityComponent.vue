<template>
    <div>
        <label id="" class="label-icon">Город
            <input
                type="text"
                v-model="text"
                @input="input"
                maxlength="30"
                autocomplete="off"
                pattern="[А-Яа-яЁё0-9-_\s]{3,30}"
                :required="required"
                :disabled="disabled"
                @focus="focus"
                @blur="blur"
                @keydown.enter.prevent="onEnter"
            >

            <div
                    class="sprite-input-right"
                    :class="status"
                    @click="clear"
            >
            </div>
            <span class="form-error">Уж постарайтесь, введите город!</span>

        </label>

        <input
                type="hidden"
                :name="name"
                v-model="id"
                maxlength="3"
                pattern="[0-9]{3}"
        >

        <table class="content-table-search table-over">
            <tbody>

                <template v-if=search>
                    <tr v-for="(result, index) in results">
                        <td>
                            <a @click="add(index)">{{ result.name }}</a>
                        </td>
                        <td>
                            <a v-if="(result.area != null)" @click="add(index)">{{ result.area.name }}</a>
                        </td>
                        <td>
                            <a @click="add(index)">{{ result.region.name }}</a>
                        </td>
                        <td>
                            <a @click="add(index)">{{ result.country.name }}</a>
                        </td>
                    </tr>
                </template>

                <tr v-if=error class="no-city">
                    <td>Населенный пункт не найден в базе данных, <a href="/admin/cities" target="_blank">добавьте его!</a></td>
                </tr>

            </tbody>
        </table>
     </div>

</template>

<script>
    export default {
        // mounted() {
        //     axios.get('/api/v1/cities')
        //         .then(response => {
        //         this.cities = response.data
        //     })
        //         .catch(error => {
        //             console.log(error)
        //         })
        // },
        props: {
            startCities: Array,
            city: {
                type: Object,
                default: function(){
                    return {
                        id: null,
                        name: null
                    }
                }
            },
            required: {
                type: Boolean,
                default: false
            },
            disabled: {
                type: Boolean,
                default: false
            },
            name: {
                type: String,
                default: 'city_id'
            }
        },
        data() {
            return {
                id: this.city.id,
                text: this.city.name,
                results: [],
                search: false,
                found: (this.city.id != null) ? true : false,
                error: false,
                cities: this.startCities
            };
        },
        computed: {
            status() {
                let result;

                if (this.found) {
                    result = 'sprite-16 icon-success'
                }
                if (this.error) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            }
        },
        methods: {
            check() {
                // console.log('Ищем введеные данные в наших городах (подгруженных), затем от результата меняем состояние на поиск или ошибку');
                this.results = this.cities.filter(item => {
                    return item.name.toLowerCase().includes(this.text.toLowerCase());
                });

                if (this.results.length == 0) {
                    axios
                        .get('/api/v1/cities_list', {
                            params: {
                                name: this.text
                            }
                        })
                        .then(response => {
                            this.results = response.data;
                            this.search = (this.results.length > 0)
                            this.error = (this.results.length == 0)
                        })
                        .catch(error => {
                            console.log(error)
                        });
                } else {
                    this.search = (this.results.length > 0)
                    this.error = (this.results.length == 0)
                }
            },
            add(index) {
                // console.log('Клик по пришедшим данным, добавляем в инпут');
                this.id = this.results[index].id;
                this.text = this.results[index].name;
                this.found = true;
                this.error = false;
                this.search = false;
                this.results = [];

                this.$emit('change', this.id);
            },
            updateCityId(cityId) {
                this.id = cityId;

                let city = this.cities.find(city => city.id == cityId);
                this.text = city.name;

                this.found = true;
                this.error = false;
                this.search = false;
                this.results = [];
            },
            clear() {
                if (this.error) {
                    // console.log('Клик по иконке ошибки на инпуте, обнуляем');
                    this.text = '';
                    this.id = null;
                    this.found = false;
                    this.error = false;
                    this.results = [];
                }
            },
            input() {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.id = null;
                this.found = false;
                this.error = false;
                this.search = false;
                this.results = [];

                if (this.text.length > 0) {
                    this.check();
                }
            },
            focus() {
                this.$emit('focus', this.text);
            },
            blur() {
                this.$emit('blur', this.text);
            },
            onEnter() {
                if (this.results.length == 1) {
                    this.add(0);
                }
            }
        }
    }
</script>
