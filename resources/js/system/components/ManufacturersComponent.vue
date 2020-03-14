<template>
	<div class="grid-x">

        <div class="small-12 cell">
            <select
                name="manufacturer_id"
                v-model="manufacturerId"
            >
                <option
                    :value="null"
                >Любой</option>
                <option
                    v-for="manufacturer in manufacturers"
                    :value="manufacturer.id"
                >{{ manufacturer.company.name }}</option>
            </select>
        </div>

        <div
            class="small-12 cell checkbox"
            v-if="isProduced"
        >
            <input
                type="hidden"
                name="is_produced"
                value="0"
            >
            <input
                id="checkbox-is_produced"
                name="is_produced"
                type="checkbox"
                value="1"
                :checked="item.is_produced == 1"
            >
            <label for="checkbox-is_produced"><span>Производится</span></label>
        </div>

        <div
            class="small-12 cell checkbox"
            v-else
        >
            <input
                type="hidden"
                name="is_ordered"
                value="0"
            >
            <input
                id="checkbox-is_ordered"
                name="is_ordered"
                type="checkbox"
                value="1"
                :checked="item.is_ordered == 1"
            >
            <label for="checkbox-is_ordered"><span>Под заказ</span></label>
        </div>
    </div>
</template>

<script>
	export default {
	    props: {
	        item: Object,
            manufacturers: Array,
        },
		data() {
			return {
                manufacturerId: this.item.article.manufacturer_id
			}
		},
        computed: {
	        isProduced() {
	            var companyId = this.item.company_id,
                    manufacturerId = this.manufacturerId;
                let manufacturer = this.manufacturers.find(item => item.id == manufacturerId);

                if (manufacturer) {
                    if (manufacturer.manufacturer_id == companyId) {
                        return true;
                    }
                }

                return false;
            },
        }
	}
</script>
