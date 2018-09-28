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
				put_count_challenges();
				Foundation.reInit($('#portal-challenges-for-me'));
				// alert('ReInit должен сработать!');
			}
		});
	}

	function put_count_challenges(){

		var last_challenges_count = $('input[name = last_challenges_count]').val();
		var today_challenges_count = $('input[name = today_challenges_count]').val();
		var tomorrow_challenges_count = $('input[name = tomorrow_challenges_count]').val();

		var last_challenges_count_from = $('input[name = last_challenges_count_from]').val();
		var today_challenges_count_from = $('input[name = today_challenges_count_from]').val();
		var tomorrow_challenges_count_from = $('input[name = tomorrow_challenges_count_from]').val();

		// alert(last_challenges_count + ' - ' + today_challenges_count + ' - ' + tomorrow_challenges_count + ' - ' + last_challenges_count_from + ' - ' + today_challenges_count_from + ' - ' + tomorrow_challenges_count_from);

		$('#last-challenges-count').html(last_challenges_count);
		$('#today-challenges-count').html(today_challenges_count);
		$('#tomorrow-challenges-count').html(tomorrow_challenges_count);

		$('#last-challenges-count-from').html(last_challenges_count_from);
		$('#today-challenges-count-from').html(today_challenges_count_from);
		$('#tomorrow-challenges-count-from').html(tomorrow_challenges_count_from);

		$('#challenges-count').html(last_challenges_count * 1 + today_challenges_count * 1 + tomorrow_challenges_count * 1);
	}

</script>