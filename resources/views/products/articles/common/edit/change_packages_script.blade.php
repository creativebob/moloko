<script>

	let unit = '{{ $package_unit }}';
    // Порции
    $(document).on('click', '#package', function() {
        $('#package-block div').toggle();
        // $('#package-fieldset').toggleClass('package-fieldset');
        $('#unit').text( $(this).prop('checked') ? 'компановку' : unit );
    });
</script>