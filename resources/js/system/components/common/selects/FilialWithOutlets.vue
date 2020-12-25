<template>
    <div class="grid-x grid-padding-x">

        <div class="cell small-12">
            <label>Филиал
                <select
                    name="filial_id"
                    v-model="filialId"
                >
                    <option
                        v-for="filial in filials"
                        :value="filial.id"
                    >{{ filial.name }}
                    </option>
                </select>
            </label>
        </div>

        <div class="cell small-12">
            <label>Торговая точка
                <select
                    name="outlet_id"
                    v-model="outletId"
                >
                    <option
                        :value="null"
                    >Нет торговой точки</option>
                    <option
                        v-for="outlet in outletsForFilial"
                        :value="outlet.id"
                      >{{ outlet.name }}
                    </option>
                </select>
            </label>
        </div>

    </div>
</template>

<script>
export default {
    props: {
        entity: String
    },
    data() {

        return {
            filialId: null,
            outletId: null,
            filials: [],
            outlets: []
        }
    },
    mounted() {
        axios
            .post('/admin/departments/get_user_filials_with_outlets', {
                entity: 'leads'
            })
            .then(response => {
                if (response.data.filials.length) {
                    this.filials = response.data.filials;
                    this.filialId = this.filials[0].id;
                }

                if (response.data.outlets.length) {
                    this.outlets = response.data.outlets;
                }
            })
            .catch(error => {
                alert('Ошибка загрузки, перезагрузите страницу!')
                console.log(error)
            });
    },
    computed: {
        outletsForFilial() {
            return this.outlets.filter(item => {
                if (item.filial_id == this.filialId) {
                    return item;
                }
            });
        }
    },
}
</script>
