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
							v-for="category in categoriesList"
							:value="category.id"
					>{{ getCount(category.level) }}{{ category.name}}</option>
				</select>

			</label>
		</div>

		<div class="small-12 medium-6 cell">
			<label>Группа

				<select
						v-model="groupId"
						name="articles_group_id"
						@change="setGroup()"
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
		mounted() {
			// axios.get('/api/v1/categories/' + this.entity)
			// 		.then(response => {
			// 			this.categories = response.data.categories
			// 			this.groups = response.data.groups
			// 			this.getGroup()
			// 			this.getGroupsList()
			// 		})
			// 		.catch(error => {
			// 			console.log(error)
			// 		})
			this.getGroup()
			this.getGroupsList()
		},
		data() {
			return {
				// categories: [],
				// groups: [],
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
			// entity: {
			// 	type: String,
			// },
			categories: {
				type: Array,
			},
			groups: {
				type: Array,
			},
		},
		computed: {
			categoriesList: function() {
				return this.getCategoriesList(this.categories);
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
			setGroup() {
				this.groups.filter(item => {
					if (item.id == this.groupId) {
						return this.group = item;
					}
				});
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
			},
			getCategoriesList(flatCategories) {
				var tree = [];
				var self = this;
				flatCategories.forEach( function(category) {
					tree.push(category);

					if (typeof category.childrens !== 'undefined') {
						tree = tree.concat(self.getCategoriesList(category.childrens));
					}
				});

				return tree;
			},
			getCount(level) {
				let res = '';
				for (var i = 1; i < level; i++) {
					res = res + '_';
				}
				return res;
			}
		},

	}
</script>
