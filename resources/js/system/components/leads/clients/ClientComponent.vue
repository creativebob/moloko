<template>
    <div
        v-if="client"
        class="grid-x grid-padding-x"
    >
        <div class="cell small-12">

            <div
                v-if="client.clientable_type === 'App\\User'"
                class="grid-x grid-padding-x"
            >
                <div class="cell small-12 medium-6 large-4">

                    <label>Фамилия
                        <input type="text" :value="client.clientable.second_name">
                    </label>
                    <label>Имя
                        <input type="text" :value="client.clientable.first_name">
                    </label>
                    <label>Отчество
                        <input type="text" :value="client.clientable.patronymic">
                    </label>
                    <label>Дата рождения
                        <pickmeup-component
                            :value="client.clientable.birthday_date"
                        ></pickmeup-component>
                    </label>
                </div>
                <div class="cell small-12 medium-6 large-4">
                    <search-city-component
                        :start-cities="cities"
                        :city="client.clientable.location.city"
                        :disabled="isDisabled"
                        @input="change"
                        ref="cityId"
                    ></search-city-component>
                    <label>Адрес
                        <string-component
                            name="address"
                            :value="client.clientable.location.address"
                            :disabled="isDisabled"
                            v-model="client.clientable.location.address"
                            @input="change"
                            ref="address"
                        ></string-component>
                    </label>
                </div>
            </div>

            <div
                v-if="client.clientable_type === 'App\\Company'"
                class="grid-x"
            >
                <div class="cell small-12">
                    {{ client.clientable.name }}
                </div>
            </div>

            <div class="grid-x">
                <div class="cell small-12">
                    Скидка: {{ client.discount }}<br>
                    Поинты: {{ client.points }}
                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    computed: {
        client() {
            return this.$store.state.lead.client;
        }
    }
}
</script>
