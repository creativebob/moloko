<template>
    <div>
        <b>{{ results.length }}</b>
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
                    v-model="id"
                    maxlength="3"
                    pattern="[0-9]{3}"
            >
            <input
                    type="hidden"
                    name="country_id_default"
            >

        </label>

        <table v-show="!check" class="content-table-search table-over">
            <tbody>

            <template v-if="results.length > 0">
                <tr  v-for="result in results">
                    <td>
                        <a @click="add(result.id, result.name)" class="city-add city-name">{{ result.name }}</a>
                    </td>
                    <td>
                        <a @click="add(result.id, result.name)">{{ result.region.name }}</a>
                    </td>
                </tr>
            </template>

            <template v-else>
                <tr v-show="check" class="no-city">
                    <td>Населенный пункт не найден в базе данных, <a href="/admin/cities" target="_blank">добавьте его!</a></td>
                </tr>
            </template>

            </tbody>
        </table>
     </div>



</template>

<script>
    export default {
        mounted() {
            console.log('CitySearchComponent mounted.');
        },
        // props: [
        //     this.id => 'id',
        //     this.name => 'name'
        // ],
        data() {
            return {
                id: null,
                name: null,
                results: [],
                check: true
            };
        },
        watch: {
            name(after, before) {
                this.fetch();
            }
        },
        methods: {
            fetch() {
                if (this.name.length > 2 && this.check == true) {
                      axios.get('/admin/cities_list', {
                        params: {
                            name: this.name
                        }
                    })
                    .then(response => this.results = response.data)
                          .then(this.check = false)
                    .catch(function (error) {
                        console.log(error);
                    });
                } else {
                    this.id = null;
                    this.check = true
                }
            },
            add(id, name) {
                this.id = id;
                this.name = name;
                this.results = [];
                this.check = false;
            }
        }
    }
</script>
