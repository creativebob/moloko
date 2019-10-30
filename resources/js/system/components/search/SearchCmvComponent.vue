<template>

        <div id="search">
                <input
                        class="search-field"
                        type="search"
                        id="field-search"
                        name="search"
                        placeholder="Поиск"
                        v-model="text"
                        @input="dedounceSearch"
                />
            <div id="search-result-wrap" v-if=search>

                <ul class="search-result-list">

                    <li v-for="(item, index) in results">
                        <a :href="'/admin/' + this.entity + '/' + item.id + '/edit'">{{ item.article.name }}</a>
                    </li>

                </ul>
            </div>
        </div>

</template>

<script>
    import _ from 'lodash'

    export default {
        props: {
            alias: String
        },
        data() {
            return {
                text: '',
                results: [],
                search: false,
                found: false,
                error: false,
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
            },
            dedounceSearch: function() {
                let delay = 300;
                return _.debounce(this.check, delay);
            }
        },
        methods: {
            check () {
                    // console.log('Ищем введеные данные в наших городах (подгруженных), затем от результата меняем состояние на поиск или ошибку');
                    if (this.text.length >= 2) {
                        axios.get('/admin/' + this.alias + '/search/' + this.text)
                            .then(response => {
                                this.results = response.data
                                this.search = (this.results.length > 0)
                                this.error = (this.results.length == 0)
                            })
                            .catch(error => {
                                    console.log(error)
                            });



                    } else {
                        this.reset();
                    }


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
            reset() {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.found = false;
                this.error = false;
                this.search = false;
                this.results = [];

                if (this.text.length > 2) {
                    this.check();
                }
            },
            onEnter() {
                if (this.results.length == 1) {
                    this.add(0);
                }
            }
        }
    }
</script>
