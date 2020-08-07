<template>
    <div>
        <label class="label-icon">Город
            <input
                type="text"
                v-model="text"
                @input="reset"
                maxlength="30"
                autocomplete="off"
                pattern="[А-Яа-яЁё0-9-_\s]{3,30}"
                @keydown.enter.prevent="onEnter"
            >

            <div
                    class="sprite-input-right"
                    :class="status"
                    @click="clear"
            >
            </div>

        </label>

        <table class="content-table-search table-over">
            <tbody>

                <template v-if=search>
                    <tr v-for="result in results">
                        <td>
                            <a @click="add(result)">{{ result.name }}</a>
                        </td>
                        <td>
                            <a v-if="(result.area)" @click="add(result)">{{ result.area.name }}</a>
                        </td>
                        <td>
                            <a @click="add(result)">{{ result.region.name }}</a>
                        </td>
<!--                        <td>-->
<!--                            <a @click="add(result)">{{ result.country.name }}</a>-->
<!--                        </td>-->
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
        props: {
            cities: Array,
        },
        data() {
            return {
                results: [],
                search: false,
                error: false,
                text: null
            };
        },
        computed: {
            status() {
                let result;

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
            add(item) {
                this.$emit('add', item);
                this.error = false;
                this.search = false;
                this.results = [];
                this.text = null;
            },
            clear() {
                if (this.error) {
                    // console.log('Клик по иконке ошибки на инпуте, обнуляем');
                    this.error = false;
                    this.results = [];
                    this.text = null;
                }
            },
            reset() {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.error = false;
                this.search = false;
                this.results = [];

                if (this.text.length > 0) {
                    this.check();
                }
            },
            onEnter() {
                if (this.results.length == 1) {
                    this.add(this.results[0]);
                }
            }
        }
    }
</script>
