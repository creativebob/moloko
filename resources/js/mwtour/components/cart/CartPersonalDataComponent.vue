<template>
	<div v-show="isShow">

		<div class="small-12 cell checkbox checkbox-item">
			<input
					type="checkbox"
					v-model="personalData"
					id="checkbox-personal_data"
					name="personal_data"
					value="true"
					checked="checked"
			>
			<label for="checkbox-personal_data" id="personal-data-label">
				<span>Согласен (на) на обработку персональных данных</span>
			</label>
		</div>

		<div class="cell small-12 text-center wrap-button-cart">
			<button
					class="button"
                    type="submit"
					:disabled=isDisabled
			>Отправить заказ</button>
		</div>

	</div>
</template>

<script>
export default {

    props: {
        disabled: Boolean
    },
    data() {
        return {
            personalData: true,
        }
    },
    computed: {
        isDisabled() {
            // if (this.isShow) {
            // 	return !this.personalData;
            // } else {
            // 	this.personalData = false
            // }
            return !this.personalData || this.disabled;
        },
        isShow() {
            return this.$store.state.goodsItems.length > 0;
        },
    }
}
</script>
