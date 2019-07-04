<template>
    <div>
        <label id="" class="city-input-parent input-icon">Город
            <input
                type="text"
                name="city_name"
                v-model="name"
                maxlength="30"
                autocomplete="off"
                pattern="[А-Яа-яЁё0-9-_\s]{3,30}"
                required
            >
        </label>

        <div
                class="sprite-input-right find-status city-check"
                :class="{ 'icon-find-ok sprite-16' : findOk, 'icon-find-no sprite-16' : findNo, 'icon-load' : load}"
                @click="clear"
        >
        </div>

        <span class="form-error">Уж постарайтесь, введите город!</span>
        <input
                type="hidden"
                name="city_id"
                v-model="id"
                maxlength="3"
                pattern="[0-9]{3}"
        >

        <table v-show="this.id == null && this.name.length > 2" class="content-table-search table-over">
            <tbody>

            <template v-if="results.length > 0">
                <tr v-for="result in results">
                    <td>
                        <a @click="add(result.id, result.name)">{{ result.name }}</a>
                    </td>
                    <td>
                        <a v-if="(result.area != null)" @click="add(result.id, result.name)">{{ result.area.name }}</a>
                    </td>
                    <td>
                        <a @click="add(result.id, result.name)">{{ result.region.name }}</a>
                    </td>
                    <td>
                        <a @click="add(result.id, result.name)">{{ result.country.name }}</a>
                    </td>
                </tr>
            </template>

            <template v-else>
                <tr class="no-city">
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
            console.log('CitySearchComponent mounted.')
        },
        props: [
            'city'
        ],
        data() {
            return {
                id: this.city.id,
                name: this.city.name,
                results: [],
                findOk: (this.city.id != null) ? true : false,
                findNo: false,
                load: false
            };
        },
        watch: {
            name(after, before) {
                this.check();
            }
        },
        methods: {
            check() {
                if (this.name.length > 2) {
                    this.findNo = false;
                    this.load = true;
                        axios.get('/api/v1/cities_list', {
                            params: {
                                name: this.name
                            }
                    })
                    .then(response => this.results = response.data)
                            .then(this.load = false)
                    .catch(function (error) {
                        console.log(error);
                    });
                } else {
                    this.id = null;
                    this.findNo = false;
                }
            },
            add(id, name) {
                this.id = id;
                this.name = name;
                this.results = [];
                this.findOk = true;
            },
            clear() {
                this.id = null;
                this.name = '';
                this.findOk = false;
                this.findNo = false;
            }
        }
    }
</script>
