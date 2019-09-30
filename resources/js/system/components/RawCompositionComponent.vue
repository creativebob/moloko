<template>

	<tr class="item" id="table-raws-" data-name="">
		<td></td>
		<td>{{ raw.article.name }}</td>
		<td>

			<div class="wrap-input-table">
				<input
						type="text"
						class="digit-field name-field compact w12 padding-to-placeholder raw-value"
						autocomplete="off"
						pattern="[0-9\W\s]{0,10}"
						placeholder="0"
						maxlength="7"
						required
				>
				<label class="text-to-placeholder">

				</label>
				<div class="sprite-input-right find-status" id="name-check"></div>
				<span class="form-error">Введите количество</span>
			</div>
		</td>
		<td>

		</td>
		<td>

		</td>
		<td>

		</td>
		<td>

		</td>

		<td class="td-delete">
			<a
					class="icon-delete sprite"
					data-open="delete-item"
					v-show="disabled"
			></a>
		</td>
	</tr>

</template>

<script>
	export default {
		data(){
			return {
				value: 1
			}
		},
		props: {
			raw: Object,
			disabled: Boolean,
			value: {
				type: Number,
				default: 0
			},
		},
	}
</script>