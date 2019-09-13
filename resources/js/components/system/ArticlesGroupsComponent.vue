<template>
	<div class="grid-x grid-margin-x">

		<div class="small-12 medium-6 cell">
			<label>Категория

				<select
						v-model="categoryId"
						name="category_id"
						@change="getGroupsList()"

				>
					<option
							v-for="category in categories"
							:value="category.id"
					>{{ category.name}}</option>
				</select>

			</label>
		</div>

		<div class="small-12 medium-6 cell">
			<label>Группа

				<select
						v-model="groupId"
						name="articles_group_id"
				>
					<option
							v-for="group in groupsList"
							:value="group.id"
					>{{ group.name}}</option>
				</select>

			</label>
		</div>

	</div>
</template>

<script>
    export default {
		data() {
			return {
				categories: [],
				groups: [],
				groupsList: [],
				categoryId: this.item.category_id,
				groupId: this.article.articles_group_id,
				group: []
			}
		},
		props: {
			item: {
				type: Object,
			},
			article: {
				type: Object,
			},
			entity: {
				type: String,
			}
		},
		methods: {
			getGroup() {
				let obj = this.groups;
				for (var item in obj) {
					let el = obj[item]
					if (el.id == this.groupId) {
						this.group = el;
					}
				}
			},
			getGroupsList() {
				this.groupsList = []
				let obj = this.groups;
				for (var item in obj) {
					let el = obj[item]
					if (el.category_id == this.categoryId) {
						this.groupsList.push(el);
					}
				}
				if (this.categoryId != this.group.category_id) {
					this.groupsList.push(this.group);
				}
			}
		},
		mounted() {
			axios.get('/api/v1/categories/' + this.entity)
				.then(response => {
					this.categories = response.data.categories
					this.groups = response.data.groups
					this.getGroup()
					this.getGroupsList()
				})
				.catch(error => {
					console.log(error)
				})
		},
	}
</script>
