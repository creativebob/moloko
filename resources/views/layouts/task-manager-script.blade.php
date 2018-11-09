<script type="text/javascript">

	function get_challenges(){

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/get_challenges_user',
			type: "POST",
			success: function(html){

				$('#portal-challenges-for-me').html(html);

				Foundation.reInit($('#tabs-period-for-me'));
				Foundation.reInit($('#tabs-period-from-me'));

				var elem = new Foundation.Tabs($('#tabs-period-for-me'));
				var elem2 = new Foundation.Tabs($('#tabs-period-from-me'));

			}
		});
	}

</script>