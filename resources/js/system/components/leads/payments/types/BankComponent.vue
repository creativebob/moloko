<template>
    <div class="cell small-12">
        <div class="grid-x grid-padding-x">
            <div class="cell shrink self-left">

                <div class="grid-x grid-padding-x">
                    <div class="cell small-12 medium-6">
                        <label>Дата платежа:
                            <pickmeup-component
                                :required="true"
                                @change="changeDate"
                                name="payment_date"
                            ></pickmeup-component>
                        </label>
                    </div>
                    <div class="cell small-12 medium-6">
                        <label>Сумма:
                            <digit-component
                                classes="input-payment electronically"
                                @input="setElectronically"
                                ref="electronicallyComponent"
                                v-focus
                            ></digit-component>
                        </label>
                    </div>
                </div>

            </div>
            <div class="cell shrink self-right">
                <button
                    @click="resetType"
                    type="button"
                    class="button tiny button-payment-back"
                >Назад
                </button>
            </div>
        </div>
        <div class="grid-x grid-padding-x">
            <div class="cell small-12 invert-show">
                <button
                    @click="addPayment"
                    type="button"
                    class="button"
                >Добавить оплату</button>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment'

    export default {
        components: {
            'pickmeup-component': require('../../../inputs/PickmeupComponent'),
            'digit-component': require('../../../inputs/DigitComponent'),
        },
        data() {
            return {
                electronically: 0,
                paymentDate: moment(String(new Date())).format('DD.MM.YYYY')
            }
        },
        methods: {
            resetType() {
                this.$emit('reset');
            },
            changeDate(date) {
                if (date !== "") {

                    this.paymentDate = date;
                } else {
                    this.paymentDate = null;
                }
            },
            setElectronically(value) {
                this.electronically = value;
            },
            addPayment() {
                if (this.electronically > 0) {
                    const dateArray = this.paymentDate.split(".");
                    const date = dateArray[2] + '-' + dateArray[1] + '-' + dateArray[0] + ' 00:00:00';
                    const data = {
                        cash: 0,
                        electronically: this.electronically,
                        registered_at: date,
                    };
                    this.$store.dispatch('ADD_PAYMENT', data);
                    this.electronically = 0;
                    this.$refs.electronicallyComponent.update(this.electronically);
                }

            },
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },
    }
</script>
