<script type="text/javascript">

	'use strict';

	class CheckBoxer {

		constructor(name, count_mass) {
			this.name = name;
			this.count_mass = count_mass;
		}

		getCount() {
			return this.count_mass;
		}

		CheckBoxerClean(){
			$('.checkboxer-menu.' + this.name + ' :checkbox').removeAttr("checked")
			$('.' + this.name + ' .checkboxer-clean').addClass('hide-elem');
			$('.' + this.name + ' .checkboxer-title').css("width", "100%");
			$('.' + this.name + '.checkboxer-wrap').css("paddingRight", "40px");

			this.count_mass = 0;
			$('#count_filter_' + this.name).html('('+ this.count_mass +')');
		}

		CheckBoxerDelShow(){

			$('.' + this.name + ' .checkboxer-clean').removeClass('hide-elem');
			$('.' + this.name + ' .checkboxer-title').css("width", "100%");
			$('.' + this.name + '.checkboxer-wrap').css("paddingRight", "60px");
		}	



		CheckBoxerSetWidth(){

			this.width = $('.' + this.name + '.checkboxer-wrap').css("width");
			$('.' + this.name + '.dropdown-pane.checkboxer-pane').css("width", this.width);
			
		}	

		CheckBoxerAddDel(elem){

			this.CheckBoxerDelShow();

			if($(elem).prop('checked')){

				this.count_mass = (this.count_mass + 1)*1;

				$('#count_filter_' + this.name).html('('+ this.count_mass +')');

			} else {
				this.count_mass = (this.count_mass - 1)*1;
				$('#count_filter_' + this.name).html('('+ this.count_mass +')');

				if(this.count_mass == 0){

					$('.checkboxer-menu.' + this.name + ' :checkbox').removeAttr("checked")
					$('.' + this.name + ' .checkboxer-clean').addClass('hide-elem');
					$('.' + this.name + ' .checkboxer-title').css("width", "100%");
				};

			};
		}

	}


</script>