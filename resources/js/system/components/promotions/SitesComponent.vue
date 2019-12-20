<template>

<!--    <div>-->
<!--        <site-filials-component-->
<!--            v-for="(site, index) in sites"-->
<!--            :site="site"-->
<!--            :index="index"-->
<!--            :key="site.id"-->
<!--        ></site-filials-component>-->
<!--    </div>-->

    <select
        name="site_id"
        v-model="siteId"
    >
        <option
            v-for="site in sites"
            :value="site.id"
            @change="changeSite(site)"
        >{{ site.name }}</option>
    </select>

<!--    <fieldset-->
<!--        class="fieldset-access"-->
<!--    >-->
<!--        <legend>Сайты</legend>-->
<!--        <ul>-->
<!--            <li-->
<!--                v-for="site in sites"-->
<!--                class="checkbox"-->
<!--            >-->
<!--                <input-->
<!--                    type="checkbox"-->
<!--                    :name="'sites[]'"-->
<!--                    :value="site.id"-->
<!--                    :id="'checkbox-site-' + site.id"-->
<!--                    @click="toggleSite(site, $event)"-->
<!--                    :checked="getSite(site.id)"-->
<!--                >-->
<!--                <label :for="'checkbox-site-' + site.id"><span>{{ site.name }}</span></label>-->
<!--            </li>-->
<!--        </ul>-->
<!--    </fieldset>-->



</template>

<script>
    export default {
        // components: {
        //     'site-filials-component': require('./SiteFilialsComponent')
        // },
        props: {
            sites: Array,
            site: {
                type: Object,
                default() {
                    return {
                        id: null
                    }
                }
            }
        },
		data() {
			return {
                siteId: this.sites[0] ? this.sites[0].id : null,
			}
		},

        created() {
            if (this.sites.length) {
                this.$store.commit('SET_SITE', this.sites[0]);
            } else {

            }

        },

        methods: {
            changeSite(site) {

            }
            // toggleSite(site, event) {
            //     var checked = event.target.checked;
            //
            //     if (checked === true) {
            //         this.$store.commit('ADD_SITE', site);
            //     } else {
            //         this.$store.commit('REMOVE_SITE', site.id);
            //     }
            // },
            // getSite(siteId) {
            //     if (this.sites.length) {
            //         let found = this.promotion.sites.find(site => site.id == siteId);
            //         if (found) {
            //             return true;
            //         } else {
            //             return false;
            //         }
            //     }
            // },
        }
	}
</script>
