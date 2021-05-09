			{!! Form::open(['route' => 'project.site_user_login', 'data-abide', 'novalidate', 'id'=>'login-form']) !!}

			<div class="reveal login-modal" id="open-modal-login" data-reveal>
			
				<noindex>
					<h4>Вход <br><span>в личный кабинет</span></h4>

					@php
						if(session('time_get_access_code')){
							$second_blocking = 180 - session('time_get_access_code')->diffInSeconds(now());
							if($second_blocking < 1){$second_blocking = 0;};

						} else {
							$second_blocking = 0;
						}
					@endphp

					<div class="grid-x">
						
							<div class="cell small-12 removable-phone-block">
								<div class="grid-x">
									<div class="cell small-12 text-center">
										<input type="text" placeholder="Ваш телефон" name="main_phone" class="phone-field" required id="phone">
										<span class="form-error">Введите ваш телефон!</span>
									</div>
									<div class="cell small-12 wrap-button-login">
										<button class="button" type="button" id="send-access-code">Войти</button>
									</div>
								</div>
							</div>
							<div class="cell small-12 removable-access-code-block">
								<div class="grid-x">
									<div class="cell small-12">
										<p class="alert-user sended_inform">На ваш номер выслан СМС-код.<br>Повторный запрос возможен через <span id="timer">{{ $second_blocking }}</span> сек.</p>
										<input type="text" placeholder="Введите СМС код" name="access_code" id="access_code" required>
									</div>
									<div class="cell small-12 wrap-button-login">
										<button class="button" type="button" id="repeat-access-code">Запросить повторно</button>
										<button class="button" id="submit-access-code">Войти</button>
									</div>
								</div>
							</div>
						
					</div>
					<button class="close-button" data-close aria-label="Close modal" type="button">
						<span aria-hidden="true">&times;</span>
					</button>
				</noindex>
			</div>

			{!! Form::close() !!}

			@push('scripts')
			    @include('includes.scripts.inputs-mask')
			    @include('mwtour.layouts.headers.includes.access_code_script')

				<script>

					// При клике на кнопку Войти - запрашиваем код СМС
				    $(document).on('click', "#send-access-code", function() {
						getCode();
				    });

				   	// При клике на кнопку Войти - запрашиваем код СМС
				    $(document).on('click', "#repeat-access-code", function() {

				        $('.removable-phone-block').show();
				        $('.removable-access-code-block').hide();
				        $('.sended_inform').hide();

				    });

			        // ------------- Ловим нажатие на enter -------------
			        $(document).on('keydown', '#phone', function(event) {
			            if ((event.keyCode == 13) && (event.shiftKey == false)) { //если нажали Enter, то true
			                event.preventDefault();
			                getCode();
			            }
			        });

			        function getCode(){
			        	var phone = $('#phone').val();

						// Если символов 17 (номер адекватный, c учетом символов оформления) - разрешаем делать запрос
        				if(phone.length == 17) {

				    	$('.removable-phone-block').hide();
				        $('.removable-access-code-block').show();
				        $('.sended_inform').show();
				        $('#timer').text(180);
				        $('#repeat-access-code').hide();

				            $.ajax({
				                headers: {
				                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				                },
				                url: "/get_sms_code",
				                type: "POST",
				                data: {phone: phone},
				                success: function(html){

				                	$('#timer').text(180);
				                }
				            });
				        }
			        }

			        $('#submit-access-code').click(function() {
						$('#login-form').submit();
					});

			        $(document).on('keydown', '#access_code', function(event) {
			            if ((event.keyCode == 13) && (event.shiftKey == false)) { //если нажали Enter, то true
			                event.preventDefault();
			                $('#login-form').submit();
			            }
			        });


				</script>

			@endpush
