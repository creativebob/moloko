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

			$('#challenges-count').html(last_challenges_count + today_challenges_count + tomorrow_challenges_count);

			$('#last-challenges-count').html(last_challenges_count);
			$('#today-challenges-count').html(today_challenges_count);
			$('#tomorrow-challenges-count').html(tomorrow_challenges_count);

			}
		});
	}

</script>