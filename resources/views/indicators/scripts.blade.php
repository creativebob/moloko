<script type="text/javascript">


    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#photo').attr('src', e.target.result);
                createDraggable();
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("input[name='photo']").change(function () {
        readURL(this);
    });

</script>




