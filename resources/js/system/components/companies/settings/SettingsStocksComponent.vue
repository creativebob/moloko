<template>
    <fieldset>
        <legend>{{ category.name }}</legend>

        <ul>
            <li
                v-for="setting in category.settings"
                class="checkbox"
            >
                <input
                    type="checkbox"
                    name="settings[]"
                    :value="setting.id"
                    :id="'checkbox-setting-' + setting.id"
                    :checked="checkChecked(setting.id)"
                    :disabled="checkDisabled"
                >
                <label
                    :for="'checkbox-setting-' + setting.id"
                >
                    <span>{{ setting.name }}</span>
                </label>
            </li>
        </ul>

        <p
            v-if="error"
        >В компании не заведены склады, заведите их!</p>

    </fieldset>
</template>

<script>
	export default {
	    props: {
	        category: Object,
            item: Object
        },
		data() {
			return {
                error: false,
                itemSettings: this.item.settings,
			}
		},
        mounted() {
	        // this.checkStocksCount();

            axios
                .post('/admin/stocks/count')
                .then(response => {
                    let count = response.data;
                    if (count === 0) {
                        this.error = true;
                    }
                })
                .catch(error => {
                    console.log(error)
                });
        },
        computed: {
            checkDisabled() {
                return this.error;
            }
        },
        methods: {
            // checkStocksCount() {
            //     if (this.error === false) {
            //
            //     }
            // },
            checkChecked(id) {
                var setting = null;
                if (this.itemSettings.length) {
                    setting = this.itemSettings.find(setting => setting.id == id);
                }
                if (setting) {
                    return true;
                }  else {
                    return false;
                }
            }
        }
	}
</script>
