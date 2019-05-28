<script>

	let unit = '{{ $portion_unit }}';
    // Порции
    $(document).on('click', '#portion', function() {
        $('#portion-block div').toggle();
        // $('#portion-fieldset').toggleClass('portion-fieldset');
        $('#unit').text( $(this).prop('checked') ? 'порцию' : unit );
    });
</script>