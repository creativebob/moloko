<template>
    <div class="grid-x">

        <div class="cell small-12 large-shrink margin-left-15">
            <div class="grid-x grid-padding-x lead-contacts-block">
                <div class="large-shrink cell">
                    <label>Телефон
                        <phone-component
                            :phone="phone"
                            :required="true"
                            @input="changePhone"
                            ref="phone"
                        ></phone-component>
                        <span class="form-error">Укажите номер</span>
                    </label>
                </div>
                <div class="large-auto cell">
                    <label>Контактное лицо
                        <string-component
                            :value="name"
                            :required="true"
                            v-model="name"
                            @change="change = true"
                            ref="name"
                        ></string-component>
                        <input
                            type="hidden"
                            name="user_id"
                            :value="userId"
                        >
                        <input
                            type="hidden"
                            name="client_id"
                            :value="clientId"
                        >
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
                            :value="address"
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

                <organization-component
                    :organization="organization"
                    :legal-forms="legalForms"
                    :companies="companies"
                    @change="updateOrganization"
                    ref="organization"
                ></organization-component>

                <!--                <div class="small-12 cell wrap-lead-company">-->
                <!--                    <label>Компания-->
                <!--                        <string-component-->
                <!--                            name="company_name"-->
                <!--                            :value="lead.company_name"-->
                <!--                            v-model="companyName"-->
                <!--                            @change="change = true"-->
                <!--                            ref="companyName"-->
                <!--                        ></string-component>-->
                <!--                    </label>-->
                <!--                </div>-->

                <div class="small-12 cell wrap-lead-email">
                    <label>E-mail
                        <string-component
                            name="email"
                            :value="email"
                            v-model="email"
                            @change="change = true"
                            ref="email"
                        ></string-component>
                        <!--                        @include('includes.inputs.email', ['value'=>$lead->email, 'name'=>'email'])-->
                    </label>
                </div>

            </div>
        </div>

<!--        <div-->
<!--            v-if="change"-->
<!--            class="cell small-12"-->
<!--        >-->
<!--            <a-->
<!--                @click="update"-->
<!--                class="button"-->
<!--            >Сохранить</a>-->
<!--        </div>-->
    </div>
</template>

<script>
    export default {
        components: {
            'phone-component': require('../inputs/PhoneComponent'),
            'string-component': require('../inputs/StringComponent'),
            'organization-component': require('./OrganizationComponent'),
            'search-city-component': require('../search/SearchCityComponent')
        },
        props: {
            lead: Object,
            users: Array,
            legalForms: Array,
            companies: Array,
            cities: Array,
            city: Object
        },
        data() {
            return {
                change: false,

                user: this.lead.user,
                phone: this.lead.main_phones.length ? this.lead.main_phones[0] : {
                    id: null,
                    phone: null
                },
                name: this.lead.name,

                organization: this.lead.organization,
                companyName: this.lead.company_name,

                cityId: this.lead.location ? this.lead.location.city_id : null,
                address: this.lead.location ? this.lead.location.address : null,
                email: this.lead.email,

                client: this.lead.client,
            }
        },
        mounted() {
            this.$store.commit('SET_CLIENT', this.client);
        },
        computed: {
            userId() {
                if (this.user) {
                    return this.user.id;
                } else {
                    return null;
                }
            },
            clientId() {
                if (this.client) {
                    return this.client.id;
                } else {
                    return null;
                }
            },
        },
        methods: {
            changePhone(value) {
                if (value.length == 17) {
                    let number = value.replace(/\D+/g, "");
                    let found = this.users.find(user => user.main_phones[0].phone == number);
                    this.updateUser(found);
                }
            },
            changeCityId(value) {
                // this.change = true;
                this.cityId = value;
            },
            updateUser(user) {
                if (user) {
                    this.user = user;

                    this.phone = user.main_phones[0];

                    this.name = user.name;
                    this.$refs.name.update(this.name);

                    if (user.organizations.length) {
                        let organization = user.organizations[0];

                        if (organization.client) {
                            this.client = organization.client;
                        } else {
                            this.client = null;
                        }

                        this.organization = organization;
                        this.companyName = organization.name;
                        this.$refs.organization.update(organization);

                        this.cityId = organization.location.city_id;
                        this.$refs.cityId.updateCityId(this.cityId);

                        this.address = organization.location.address;
                        this.$refs.address.update(this.address);

                        this.email = organization.email;
                        this.$refs.email.update(this.email);

                    } else {
                        if (user.client) {
                            this.client = user.client;
                        } else {
                            this.client = null;
                        }

                        this.organization = null;
                        this.companyName = null;
                        this.$refs.organization.update(null);

                        this.cityId = user.location.city_id;
                        this.$refs.cityId.updateCityId(this.cityId);

                        this.address = user.location.address;
                        this.$refs.address.update(this.address);

                        this.email = user.email;
                        this.$refs.email.update(this.email);
                    }


                } else {
                    this.user = null;
                    this.client = null;
                }

                this.$store.commit('SET_CLIENT', this.client);
            },
            updateOrganization(organization) {
                if (organization) {
                    this.organization = organization;
                    this.companyName = organization.name;

                    if (organization.client) {
                        this.client = organization.client;
                    } else {
                        this.client = null;
                    }

                    this.email = organization.email;
                    this.$refs.email.update(this.email);

                    this.cityId = organization.location.city_id;
                    this.$refs.cityId.updateCityId(this.cityId);

                    this.address = organization.location.address;
                    this.$refs.address.update(this.address);

                    if (!this.phone.phone && !this.name) {

                        if (organization.representatives.length) {
                            let user = organization.representatives[0];

                            this.user = user;

                            this.name = user.name;
                            this.$refs.name.update(this.name);

                            this.phone = user.main_phones[0];
                            this.$refs.phone.update(this.phone.phone);
                        }
                    }

                } else {
                    this.organization = null;
                    this.companyName = null;
                    this.client = null;

                    if (this.user) {
                        this.cityId = this.user.location.city_id;
                        this.$refs.cityId.updateCityId(this.cityId);

                        this.address = this.user.location.address;
                        this.$refs.address.update(this.address);

                        this.email = this.user.email;
                        this.$refs.email.update(this.email);

                        if (this.user.client) {
                            this.client = this.user.client;
                        }
                    }
                }

                this.$store.commit('SET_CLIENT', this.client);
            },

            // update() {
            //     this.change = false;
            //
            //     let clientId = this.client.id ? this.client.id : null;
            //     axios
            //         .patch('/admin/leads/axios_update/' + this.lead.id, {
            //             name: this.name,
            //             organization_id: this.organizationId,
            //             company_name: this.companyName,
            //             email: this.email,
            //             city_id: this.cityId,
            //             address: this.address,
            //             main_phone: this.number,
            //             client_id: clientId,
            //         })
            //         .then(response => {
            //             // console.log(response);
            //         })
            //         .catch(error => {
            //             console.log(error)
            //         });
            // },
        }
    }
</script>
