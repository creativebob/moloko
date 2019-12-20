<template>

	<div class="small-12 cell">
		<label>{{ options.title }}
			<input
				:name="options.name"
				type="file"
			    @change="onChange"
			>
		</label>
		<div class="text-center wrap-article-photo">
			<img
				v-show="path"
				:src="path">
		</div>
	</div>

</template>

<script>
	export default {
		props: {
			options: {
				type: Object,
				default() {
				    return {
                        title: 'Фотография',
                        name: 'photo',
                    }
				}
			},
			photo: {
				type: Object,
                default() {
                    return {
                        path: '',
                    }
                }
			},
			// imageSrc: ''
		},
		data(){
			return {
				imageSrc: '',
			}
		},
		computed: {
			path() {
				if (this.photo === null) {
					if (this.imageSrc === '') {
						return ''
					} else {
						return this.imageSrc;
					}
				} else {
					if (this.imageSrc === '' ) {
						return this.photo.path
					} else {
						if (this.imageSrc !== this.photo.path) {
							return this.imageSrc;
						} else {
							return this.photo.path
						}
					}
				}
			}
		},
		methods: {
			onChange (event) {
				var input = event.target;
				// console.log(input);

				if (input.files && input.files[0]) {
					var reader = new FileReader();
					var vm = this;
					// console.log(this);

					reader.onload = function(e) {
						vm.imageSrc = e.target.result;

					};

					reader.readAsDataURL(input.files[0]);
				}
			}
		}
	}
</script>
