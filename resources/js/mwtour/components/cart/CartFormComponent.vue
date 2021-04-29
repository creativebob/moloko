<template>

        <form
            @submit.prevent="checkCart"
            action="/cart"
            method="post"
            data-abide
            novalidate
            ref="form"
            id="form-cart"
            class="form-cart"
        >
            <slot></slot>

            <div class="grid-x grid-padding-x wrap-fields">
                <div class="cell small-12">
                    <input
                        type="text"
                        placeholder="Ваше имя"
                        name="first_name"
                        v-model="firstName"
                    >
                    <span class="form-error">Введите ваше имя</span>
                </div>
                <div class="cell small-12">
                    <input
                        type="tel"
                        placeholder="Телефон"
                        name="main_phone"
                        class="phone-field"
                        required
                        v-model="mainPhone"
                        v-mask="'# (###) ###-##-##'"
                        @keyup="mainPhone = this.event.target.value;"
                        :readonly="readonly"
                    >
                    <span class="form-error">Введите ваш телефон!</span>
                </div>
            </div>

            <div class="grid-x grid-padding-x">
                <div class="cell small-12 wrap-submit">
                    <cart-personal-data-component :disabled="disabledButton"></cart-personal-data-component>
                </div>
            </div>

        </form>

</template>

<script>
import VueMaskDirective from 'v-mask'

export default {
    components: {
        'cart-personal-data-component': require('./CartPersonalDataComponent'),
    },

    props: {
        token: String,
        name: String,
        phone: String
    },
    mounted() {
        this.$store.dispatch('UPDATE_COOKIES');
    },
    data() {
        return {
            firstName: this.name,
            mainPhone: this.phone,
            openDetails: false,
            disabledButton: false,
        }
    },
    computed: {
        readonly() {
            return this.phone != '';
        },
    },
    methods: {
        checkCart() {
            $('#form-cart').foundation('validateForm');
            let valid = $('#form-cart .is-invalid-input').length;
            let result = valid == 0;

            if (result) {
                this.disabledButton = true;

                return this.$store.dispatch('UPDATE_COOKIES')
                    .then(res => {
                        if (res) {

                            axios
                                .get('/check_prices')
                                .then(response => {
                                    // console.log(response.data);

                                    if (response.data.success) {
                                        window.localStorage.removeItem('goodsItems');
                                        this.$refs.form.submit()
                                    } else {
                                        if (response.data.changePrice) {
                                            this.$store.commit('CHANGE_ITEMS_PRICE', response.data.changePrice);
                                        }
                                        if (response.data.notEnough) {
                                            this.$store.commit('NOT_ENOUGH_ITEMS', response.data.notEnough);
                                        }
                                        this.$store.dispatch('UPDATE_COOKIES');
                                        this.disabledButton = false;
                                    }
                                })
                                .catch(error => {
                                    console.log(error)
                                });
                        } else {
                            this.disabledButton = false;
                        }
                    });
            }
        }
    },
    directives: {
        // TODO - 11.12.19 - Рабочее решение с маской телефона на Vue
        // Хочется пересадить на пакет inpunmask, но там трабл при заполнении чисел в скобках
        'mask': {
            bind: function (el) {
                VueMaskDirective;
            }
        },
    },
}
</script>

