
<label class="extra-phone">Доп. телефон
	@include('includes.inputs.phone', ['value'=>isset($extra_phone) ? $extra_phone->phone : null, 'name'=>'extra_phones[]', 'required'=>''])
</label>

<script type="text/javascript">
    // Телефон
    $('.phone-field').mask('8 (999) 999-99-99',{placeholder:"_"});
</script>
