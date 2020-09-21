<template>
    <div class="grid-x">

        <div class="cell small-12 large-shrink margin-left-15">
            <div class="grid-x grid-padding-x lead-contacts-block">
                <div class="large-shrink cell">
                    <label>Телефон
                        <phone-component
                            :phone="phone"
                            :required="true"
                            v-model="number"
                            @input="changePhone"
                        ></phone-component>
                        <span class="form-error">Укажите номер</span>
                    </label>
                </div>
                <div class="large-auto cell">
                    <label>Контактное лицо
                        <string-component
                            :value="lead.name"
                            :required="true"
                            v-model="name"
                            @change="change = true"
                            ref="name"
                        ></string-component>
                        <!--                        <input type="hidden" name="lead_id" value="{{$lead->id }}" id="lead_id" data-lead-id="{{$lead->id }}" class="wrap-lead-name">-->
                    </label>
                </div>
                <div id="port-autofind" class="small-12 cell">
<!--                    <div-->
<!--                        v-if="searchResults.length"-->
<!--                        class="wrap-autofind"-->
<!--                    >-->

<!--                        <legend>Найдены клиенты:</legend>-->
<!--                        <div class="grid-x">-->
<!--                            <div class="small-12 medium-12 large-12 cell">-->
<!--                                <table class="">-->
<!--                                    <tr-->
<!--                                        v-for="client in searchResults"-->
<!--                                    >-->
<!--                                        <td-->
<!--                                            @click="updateLead(client)"-->
<!--                                        >{{ client.clientable.name }}-->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                                </table>-->
<!--                            </div>-->
<!--                        </div>-->

<!--                    </div>-->
                </div>

                <div class="large-shrink cell">
                    <search-city-component
                        :start-cities="cities"
                        :city="city"
                        @change="changeCityId"
                        ref="cityId"
                    ></search-city-component>
                </div>
                <div class="large-auto cell wrap-lead-address">
                    <label>Адрес
                        <string-component
                            name="address"
                            :value="lead.location.address"
                            v-model="address"
                            @change="change = true"
                            ref="address"
                        ></string-component>
                    </label>
                </div>
            </div>
        </div>
        <div class="small-12 large-auto cell wrap-lead-add-contacts">
            <div class="grid-x grid-padding-x">

                <div class="small-12 cell wrap-lead-company">
                    <label>Компания
                        <string-component
                            name="company_name"
                            :value="lead.company_name"
                            v-model="companyName"
                            @change="change = true"
                            ref="companyName"
                        ></string-component>
                    </label>
                </div>

                <div class="small-12 cell wrap-lead-email">
                    <label>E-mail
                        <string-component
                            name="email"
                            :value="lead.email"
                            v-model="email"
                            @change="change = true"
                            ref="email"
                        ></string-component>
                        <!--                        @include('includes.inputs.email', ['value'=>$lead->email, 'name'=>'email'])-->
                    </label>
                </div>

            </div>
        </div>

        <div
            v-if="change"
            class="cell small-12"
        >
            <a
                @click="update"
                class="button"
            >Сохранить</a>
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'string-component': require('../inputs/StringComponent'),
            'phone-component': require('../inputs/PhoneComponent'),
            'search-city-component': require('../search/SearchCityComponent')
        },

        props: {
            lead: Object,
            cities: Array,
            city: Object
        },

        data() {
            return {
                change: false,
                name: this.lead.name,
                companyName: this.lead.company_name,
                email: this.lead.email,
                cityId: this.lead.location.city_id,
                address: this.lead.location.address,
                number: null,

                client: this.lead.client,
            }
        },

        computed: {
            phone() {
                if (this.lead.main_phones.length) {
                    return this.lead.main_phones[0];
                } else {
                    return {phone: null};
                }
            }
        },

        methods: {
            changePhone(value) {
                if (value.length == 17) {
                    let number = value.replace(/\D+/g, "");
                    axios
                        .post('/admin/clients/search/' + number)
                        .then(response => {
                            if (response.data.length) {
                                this.client = response.data[0];

                                this.updateLead(this.client);
                                this.update();
                            }

                        })
                        .catch(error => {
                            console.log(error)
                        });
                }
            },
            changeCityId(value) {
                this.change = true;
                this.cityId = value;
            },
            updateLead(client) {

                if (client.clientable_type == 'App\\Company') {
                    this.companyName = client.clientable.name;
                    this.$refs.companyName.update(this.companyName);
                } else {
                    this.name = client.clientable.name;
                    this.$refs.name.update(this.name);
                }

                this.email = client.clientable.email;
                this.$refs.email.update(this.email);

                this.cityId = client.clientable.location.city_id;
                this.$refs.cityId.updateCityId(this.cityId);

                this.address = client.clientable.location.address;
                this.$refs.address.update(this.address);
            },
            update() {
                this.change = false;

                let clientId = this.client.id ? this.client.id : null;
                axios
                    .patch('/admin/leads/axios_update/' + this.lead.id, {
                        name: this.name,
                        company_name: this.companyName,
                        email: this.email,
                        city_id: this.cityId,
                        address: this.address,
                        main_phone: this.number,
                        client_id: clientId,
                    })
                    .then(response => {
                        // console.log(response);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
        }
    }
</script>
