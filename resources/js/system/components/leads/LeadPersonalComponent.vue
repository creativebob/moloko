<template>
    <form id="form-lead-personal" data-abide novalidate>
        <div class="grid-x">
            <div class="cell small-12 large-shrink margin-left-15">
                <div class="grid-x grid-padding-x lead-contacts-block">
                    <div class="large-shrink cell">
                        <label>Телефон
                            <phone-component
                                :phone="phone"
                                :required="true"
                                :disabled="isDisabled"
                                @input="changePhone"
                                ref="phone"
                            ></phone-component>
                            <!--                        <span class="form-error">Укажите номер</span>-->
                        </label>
                    </div>
                    <div class="large-auto cell">
                        <label>Контактное лицо
                            <string-component
                                :value="name"
                                :required="true"
                                :disabled="isDisabled"
                                v-model="name"
                                @change="change"
                                ref="name"
                            ></string-component>
                        </label>
                    </div>
                    <div id="port-autofind" class="small-12 cell">
                    </div>

                    <div class="large-shrink cell">
                        <search-city-component
                            :start-cities="cities"
                            :city="city"
                            :disabled="isDisabled"
                            @change="change"
                            ref="cityId"
                        ></search-city-component>
                    </div>
                    <div class="large-auto cell wrap-lead-address">
                        <label>Адрес
                            <string-component
                                name="address"
                                :value="address"
                                :disabled="isDisabled"
                                v-model="address"
                                @change="change"
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
                        :companies="storeCompanies"
                        :disabled="isDisabled"
                        @change="updateOrganization"
                        @input="change"
                        ref="organization"
                    ></organization-component>

                    <div class="small-12 cell wrap-lead-email">
                        <label>E-mail
                            <string-component
                                name="email"
                                :value="email"
                                :disabled="isDisabled"
                                v-model="email"
                                @change="change"
                                ref="email"
                            ></string-component>

                        </label>
                    </div>

                </div>
            </div>
        </div>
    </form>
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
                user: this.lead.user,
                phone: this.lead.main_phones.length ? this.lead.main_phones[0] : {
                    id: null,
                    phone: null
                },
                mainPhone: this.lead.main_phones.length ? this.lead.main_phones[0].phone : null,
                name: this.lead.name,

                organization: this.lead.organization ? this.lead.organization : {
                    id: null,
                    name: this.lead.company_name
                },
                companyName: this.lead.company_name,

                cityId: this.lead.location ? this.lead.location.city_id : null,
                address: this.lead.location ? this.lead.location.address : null,
                email: this.lead.email,

                client: this.lead.client,
            }
        },
        created: function () {
            this.lead.main_phone = this.lead.main_phones.length ? this.lead.main_phones[0].phone : null;

            this.$store.commit('SET_USERS', this.users);
            this.$store.commit('SET_COMPANIES', this.companies);

            this.$store.commit('SET_LEAD', this.lead);
            this.$store.commit('SET_CLIENT', this.lead.client);

            this.$store.commit('SET_ESTIMATE', this.lead.estimate);
            this.$store.commit('SET_GOODS_ITEMS', this.lead.estimate.goods_items);
            this.$store.commit('SET_SERVICES_ITEMS', this.lead.estimate.services_items);

            // this.$store.commit('SET_LEAD', this.lead);
            // this.$store.commit('SET_CLIENT', this.client);
            // this.$store.commit('SET_ESTIMATE', this.lead.estimate);
        },
        computed: {
            isDisabled() {
                return this.$store.state.lead.estimate.is_registered == 1;
            },
            storeUsers() {
                return this.$store.state.lead.users;
            },
            storeCompanies() {
                return this.$store.state.lead.companies;
            }
        },
        methods: {
            change() {
                const data = {
                    main_phone: this.mainPhone,
                    name: this.name,
                    company_name: this.companyName,
                    location: {
                        city_id: this.cityId,
                        address: this.address,
                    },
                    email: this.email,

                    user_id: this.user ? this.user.id : null,
                    organization_id: this.organization ? this.organization.id : null,
                    client_id: this.client ? this.client.id : null,
                };

                this.$store.commit('UPDATE_LEAD', data)

            },
            changePhone(value) {
                if (value.length == 17) {
                    let number = value.replace(/\D+/g, "");
                    let found = this.storeUsers.find(user => user.main_phones[0].phone == number);
                    this.updateUser(found);
                } else {
                    this.user = null;
                    this.updateOrganization(this.organization);
                }
                this.mainPhone = value;

                this.change();
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

                        this.organization = {
                            id: null,
                            name: null
                        };
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

                this.change();
                this.$store.commit('SET_CLIENT', this.client);
            },
            updateOrganization(organization) {
                if (organization.id) {
                    this.organization = organization;
                    this.companyName = organization.name;

                    this.client = organization.client ? organization.client : null;

                    this.email = organization.email;
                    this.$refs.email.update(this.email);

                    this.cityId = organization.location.city_id;
                    this.$refs.cityId.updateCityId(this.cityId);

                    this.address = organization.location.address;
                    this.$refs.address.update(this.address);

                    if (!this.mainPhone && !this.name) {

                        if (organization.representatives.length) {
                            const user = organization.representatives[0];

                            this.user = user;

                            this.name = user.name;
                            this.$refs.name.update(this.name);

                            this.phone = user.main_phones[0];
                            this.$refs.phone.update(this.phone.phone);
                        }
                    }

                } else {
                    this.organization = {
                        id: null,
                        name: null
                    };
                    this.client = null;

                    if (this.user) {
                        this.cityId = this.user.location.city_id;
                        this.$refs.cityId.updateCityId(this.cityId);

                        this.address = this.user.location.address;
                        this.$refs.address.update(this.address);

                        this.email = this.user.email;
                        this.$refs.email.update(this.email);

                        // if (this.user.client) {
                        //     this.client = this.user.client;
                        // }
                    }

                    this.companyName = organization.name;
                }

                this.change();
                this.$store.commit('SET_CLIENT', this.client);
            },
        }
    }
</script>
