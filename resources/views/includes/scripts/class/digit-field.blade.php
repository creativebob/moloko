<script>

	{{ $name }} = new DigitField('{{ $name }}', {{ $limit }}, {{ $decimal_place }});


	$("#digitfield-{{$name}}").keydown(function(event) {
		{{ $name }}.KeyDown($("#digitfield-{{$name}}").val());
	});

	$("#digitfield-{{$name}}").keyup(function(event) {
		{{ $name }}.KeyUp($("#digitfield-{{$name}}").val(), {{ $decimal_place }} );
	});

	$("#digitfield-{{$name}}").blur(function(event) {
		{{ $name }}.Blur($("#digitfield-{{$name}}").val());
	});

	$("#digitfield-{{$name}}").keypress(function( b ){

		if({{ $decimal_place }} == 0){	    		
	    	var C = /[0-9\x25\x24\x23]/;
		} else {
	    	var C = /[0-9\x25\x24\x23\x2e]/;
		};

	    var a = b.which;
	    var c = String.fromCharCode(a);
	    return !!(a==0||a==8||a==9||a==13||c.match(C));

	});

</script>