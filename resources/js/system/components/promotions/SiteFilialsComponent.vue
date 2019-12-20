<template>

        <fieldset
            class="fieldset-access"
        >
            <legend>Филиалы сайта {{ site.name }}</legend>

            <ul>
                <li
                    v-for="filial in site.filials"
                    class="checkbox"
                >
                    <input
                        type="checkbox"
                        :name="'sites[' + site.id + '][filial_id]'"
                        :value="filial.id"
                        :id="'checkbox-filial-' + filial.id"
                        class="filial-checkbox"
                        @click="toggleFilial(site.id, filial.id, $event)"
                    >
                    <label :for="'checkbox-filial-' + filial.id"><span>{{ filial.name }}</span></label>
                </li>
            </ul>

        </fieldset>

</template>

<script>
    export default {
        props: {
            site: Object,
        },
		data() {
			return {

			}
		},

        methods: {
            toggleFilial(siteId, FilialId, event) {
                var checked = event.target.checked;

                if (checked === true) {

                } else {

                }
            }
        }
	}
</script>
