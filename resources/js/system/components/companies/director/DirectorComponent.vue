<template>
    <div class="cell small-12 medium-7">

        <input
            type="hidden"
            :value="setDirector ? 1 : 0"
            name="set_user"
        >
        <div class="checkbox">
            <input
                type="checkbox"
                id="checkbox-set_director"
                value="1"
                name="set_user"
                @change="setDirector = $event.target.checked"
                :checked="setDirector"
                :disabled="director.first_name"
            >
            <label
                for="checkbox-set_director"
            >Указать директора</label>
        </div>
        <fieldset
            v-show="setDirector"
        >
            <legend>Директор (руководитель)</legend>
            <div class="grid-x grid-padding-x">
                <div class="cell small-12">
                    <label>Фамилия
                        <string-component
                            name="user_second_name"
                            :value="director.second_name"
                            :required="true"
                        ></string-component>
                    </label>
                </div>
                <div class="cell small-12">
                    <label>Имя
                        <string-component
                            name="user_first_name"
                            :value="director.first_name"
                            :required="true"
                        ></string-component>
                    </label>
                </div>
                <div class="cell small-12">
                    <label>Отчество
                        <string-component
                            name="user_patronymic"
                            :value="director.patronymic"
                        ></string-component>
                    </label>
                </div>
                <div class="cell small-12 medium-6">
                    <label>Телефон
                        <phone-component
                            name="user_main_phone"
                            :phone="phone"
                            :required="true"
                        ></phone-component>
                    </label>
                </div>
                <div class="cell small-12 medium-6">
                    <search-city-component
                        name="user_city_id"
                        :start-cities="cities"
                        :city="city"
                    ></search-city-component>
                </div>
                <div class="cell small-12">
                    <label>Адрес
                        <string-component
                            name="user_address"
                            :value="director.location.address"
                        ></string-component>
                    </label>
                </div>
            </div>

            <fieldset class="fieldset-access">
                <legend>Настройка доступа</legend>
                <div class="grid-x grid-padding-x">
                    <div class="small-12 cell">
                        <label>Логин
                            <input
                                type="text"
                                name="user_login"
                                class="login-field"
                                maxlength="30"
                                autocomplete="off"
                                pattern="[A-Za-z0-9._-]{6,30}"
                                :value="director.login"
                            >
                            <span class="form-error">Обязательно нужно логиниться!</span>
                        </label>
                        <label>Пароль
                            <input
                                type="password"
                                name="user_password"
                                class="password password-field"
                                maxlength="20"
                                id="password"
                                autocomplete="off"
                                pattern="[A-Za-z0-9]{6,20}"
                            >
                            <span class="form-error">Введите пароль и повторите его, ну а что поделать, меняем ведь данные!</span>
                        </label>
                        <label>Пароль повторно
                            <input
                                type="password"
                                name="user_password"
                                class="password password-field"
                                maxlength="20"
                                id="password-repeat"
                                autocomplete="off"
                                pattern="[A-Za-z0-9]{6,20}"
                                data-equalto="password"
                            >
                            <span class="form-error">Пароли не совпадают!</span>
                        </label>
                    </div>
                    <div
                        v-if="accessBlock"
                        class="cell small-12 text-center checkbox"
                    >
                        <input
                            type="hidden"
                            name="access_block"
                            value="0"
                        >
                        <input
                            type="checkbox"
                            name="access_block"
                            id="checkbox-access_block"
                            value="1"
                            :checked="director.access_block == 1"
                        >
                        <label for="checkbox-access_block"><span>Блокировать доступ</span></label>
                    </div>
                </div>

            </fieldset>
        </fieldset>
    </div>
</template>

<script>
    export default {
        components: {
            'string-component': require('../../inputs/StringComponent'),
            'phone-component': require('../../inputs/PhoneComponent'),
            'search-city-component': require('../../search/SearchCityComponent')
        },
        props: {
            director: {
                type: Object,
                default: () => ({
                    second_name: null,
                    first_name: null,
                    patronymic: null,
                    main_phones: [
                        () => ({
                            phone: {
                                phone: null
                            }
                        })
                    ],
                    location: {
                        address: null,
                        city: {
                            id: null,
                            name: null
                        }
                    },
                    login: null,
                    access_block: null,
                })
            },
            cities: Array,
            city: Object,
            accessBlock: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                setDirector: false,
            }
        },
        mounted() {
            if (this.director.first_name) {
                this.setDirector = true;
            }
        },
        computed: {
            phone() {
                if (this.director.main_phones.length) {
                    return this.director.main_phones[0];
                } else {
                    return null;
                }
            }
        }
    }
</script>
