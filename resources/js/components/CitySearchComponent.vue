<template>
    <div>
        <label id="" class="city-input-parent">Город

                <input
                    type="text"
                    v-model.lazy="name"
                     name="city_name"
                    class="varchar-field city_check-field"
                    maxlength="30"
                    autocomplete="off"
                    pattern="[А-Яа-яЁё0-9-_\s]{3,30}"
                >

            <div class="sprite-input-right find-status city-check"></div>
            <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>


            <input
                    type="hidden"
                    name="city_id"
                    class="city_id-field"
                    maxlength="3"
                    autocomplete="off"
                    pattern="[0-9]{3}"
            >
            <input
                    type="hidden"
                    name="country_id_default"
            >

        </label>

        <table class="content-table-search table-over" v-if="results.length > 0">
            <tbody>

             <tr  v-for="result in results" data-tr="result.id" data-city-id="result.id">
                <td>
                    <a class="city-add city-name">{{ result.name }}</a>
                </td>

            </tr>



            </tbody>
        </table>
<!--    <ul class="menu vertical" v-if="results.length > 0">-->
<!--        <li v-for="result in results" :key="result.id">{{ result.name }}</li>-->
<!--    </ul>-->
    </div>

</template>

<script>

    var debounce = require('lodash.debounce');


    export default {
        mounted() {
            console.log('CitySearchComponent mounted.')
        },
        data() {
            return {
                name: '',
                results: []
            };
        },
        watch: {
            name(after, before) {
                this.check();
            }
        },
        methods: {
            check: _.debounce(function () {
                alert(this.name);
                    axios.post('/admin/cities_list', { params: {
                        name: this.name
                    } })
                        .then(response => this.results = response.data)
                        .catch(error => {})
                    ;
                    // alert(this.results);

            }, 300)
        }
    }

</script>
