<template>
    <div class="grid-x grid-padding-x" id="form-prices_service">

        <input
            type="hidden"
            name="service_id"
        >

        <div class="medium-3 cell">
            <label>Каталог
                <select
                    v-model="catalogs"
                    name="catalogs_service_id"
                >
                    <option v-for="catalog in catalogs" :value="catalog.id">
                        {{ catalog.name }}
                    </option>
                </select>
              </label>
        </div>
        <div class="medium-3 cell">
            <label>Пункты каталога
                <select
                        v-model="catalogsItems"
                        name="catalogs_service_id"
                >
                    <option v-for="catalogsItem in catalogsItems" :value="catalogsItem.id">
                        {{ catalogsItem.name }}
                    </option>
                </select>
                           </label>
        </div>
        <div class="medium-3 cell">
            <label>Филиал
                <select
                        v-model="filials"
                        name="filial_id"
                >
                    <option v-for="filial in filials" :value="filial.id">
                        {{ filial.name }}
                    </option>
                </select>

            </label>
        </div>
        <div class="medium-3 cell">
            <label>Цена
                <input
                    v-model="price"
                    type="number"
                    name="price"
                    required
                >
            </label>
            <span class="form-error">Введите цену!</span>
        </div>
        <div class="medium-3 cell">
            <button class="button" id="button-store-prices_service">Добавить</button>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('PricesComponent mounted.')
        },
        data() {
            return {
                catalogs
            };
        },
    }
</script>
