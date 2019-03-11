<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="AT" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-2.png') }}">
						<p class="name-person">Алексей Тимошенко</p>	
						<p class="status-person">Директор</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Руковожу компанией которая производит отличные ворота"</blockquote>
					<p>С удовольствием отвечу на все ваши вопросы которые касаются деятельности нашей компании. Если у вас есть замечания к работе наших сотрудников - напишите мне!</p>

					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Вопрос с сайта для Тимошенко Алексея:">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->

<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="NB" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-17.png') }}">
						<p class="name-person">Николай Бутин</p>	
						<p class="status-person">Технический специалист</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Решаю все технические вопросы. Решаю хорошо!"</blockquote>
					<p>Не знаете как правильно подготовить проем, какую автоматику выбрать, или на что стоит обратить внимание при выбре ворот? Просто спросите меня.</p>
					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Вопрос с сайта для Бутина Николая:">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->


<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="IT" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-4.png') }}">
						<p class="name-person">Ирина Торохова</p>	
						<p class="status-person">Бухгалтер</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Принимаю поздравления в день бухгалтера! :)"</blockquote>
					<p>Люблю шоколадные конфеты и черный чай, лето и хорошую погоду. По вопросам бухгалтерии можете связаться со мной через форму ниже. Но, лучше, позвоните!</p>
					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Сообщение с сайта для Ирины Тороховой:">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->


<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="AS" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-9.png') }}">
						<p class="name-person">Алексей Солтысяк</p>	
						<p class="status-person">Маркетолог</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Пристегните ремни, мы взлетаем!"</blockquote>
					<p>Есть хорошие предложения по рекламе и продвижению? Давайте обсудим. Можете написать мне по вопросам работы сайта и его качества. Все ли удобно?</p>
					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Вопрос с сайта для Алексея Солтысяк:">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->


<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="VC" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-9.png') }}">
						<p class="name-person">Владимир Черкашин</p>	
						<p class="status-person">Менеджер по закупкам</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Финансовые дела под контролем"</blockquote>
					<p></p>
					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Сообщение с сайта для Владимира Черкашина:">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->


<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="KF" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-7.png') }}">
						<p class="name-person">Константин Фионов</p>	
						<p class="status-person">Начальник производства</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Лично отвечаю за качество ваших ворот и монтажные работы!"</blockquote>
					<p></p>
					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Сообщение с сайта для Владимира Черкашина:">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->

<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="AM" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-11.png') }}">
						<p class="name-person">Александр Матвеев</p>	
						<p class="status-person">Водитель-экспедитор</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Бережно и вовремя доставлю ваши ворота!"</blockquote>
					<p>Можете мне что-нибудь написать если хотите... Но, лучше позвоните своей маме ;)</p>
					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Сообщение с сайта для Владимира Черкашина:">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->


<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="EU" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-10.png') }}">
						<p class="name-person">Константин Фионов</p>	
						<p class="status-person">Специалист по замеру</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Самое время произвести замер! Не так ли?"</blockquote>
					<p>Готов поведать о всех тонкостях воротного производства и монтажа. Ваша экономия - моя задача. Расскажу о материалах, помогу выбрать конструктив. Ну, и конечно, сделаю точный замер.</p>
					<form action="sendmaster.php" method="post" data-abide>
						<fieldset>
							<div class="small-12 medium-6 large-6 cell clean-pad-left">
								<label>Дата замера:
									<input type="text" id="date" class="datezamer" name="date_zamer" value="18.07.2016">
								</label>
							</div>

							<div class="small-12 medium-6 large-6 cell clean-pad-left">
								<label>Удобное время:
									<input type="text" id="tz-begin" maxlength="5" class="time-field" pattern="([0-1][0-9]|[2][0-3]):[0-5][0-9]" placeholder="10:00"  onkeyup="proTime(this);" name="time_zamer">
								</label>
							</div>

							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Адрес где будет производиться замер:
									<input name="user_address" type="text" placeholder="" value="" required maxlength="50">
								</label>
							</div>

							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваше имя:
									<input name="user_name" type="text" placeholder="" value="" required maxlength="24">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="submit" class="button small right" value="Отправить заявку на замер!">
							</div>
						</fieldset>
					</form>	
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->

<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="AV" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-16.png') }}">
						<p class="name-person">Александр Выпроцких</p>	
						<p class="status-person"></p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Готов продать большую партию ворот по низкой цене"</blockquote>
					<p>Только если напишите ему прямо сейчас. Скажем так, предлагаемая цена - потрясающая!</p>
					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Для Выпроцких Александра: клиент хочет большую партию ворот по бросовой цене!">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->

<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="IP" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-15.png') }}">
						<p class="name-person">Игорь Попов</p>	
						<p class="status-person">Менеджер отдела продаж</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Продам майку от гольфа человеку который ненавидит гольф!"</blockquote>
					<p>Профессиональный продавец. Не стоит ему звонить, если вы не планируете покупку.</p>
					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Сообщение для Игоря: клиент не верит, чот ты ему можешь продать ворота! ">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->

<!-- ФОРМА ДЛЯ ПИСЬМА - НАЧАЛО -->
<div class="reveal person-feed" id="UM" data-reveal>
	<h2>Обратная связь</h2>
	<div class="raw">
		<div class="cell small-12 medium-12 large-12 large-centered cls">
			<div class="media-object">
				<div class="media-object-section">
					<div class="thumbnail">
						<img src= "{{ asset('/project/img/personal/person-14.png') }}">
						<p class="name-person">Юрий Миронов</p>	
						<p class="status-person">Менеджер отдела продаж</p>
					</div>
				</div>
				<div class="media-object-section">
					<blockquote>"Что тут говорить? Работаем..."</blockquote>
					<p>Профессиональный продавец. Нужны ворота - звоните Юрию!</p>
					<form action="sendmail.php" method="post" data-abide>
						<fieldset>
							<!-- 										<legend>Контакты:</legend>	 -->
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Ваш телефон:
									<input name="user_phone" pattern="8\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" type="text" placeholder="" value="" required class="phone-field">
								</label>
							</div>
							<div class="small-12 medium-12 large-12 cell clean-pad-left">
								<label>Сообщение:
									<textarea required name="question" type="text" placeholder="" value="" required maxlength="200"></textarea>
								</label>
							</div>
						</fieldset>
						<fieldset>
							<div class="small-12 cell clean-pad-left">
								<input type="hidden" name="remark" value="Сообщение для Юрия: клиент не верит, чот ты ему можешь продать ворота! ">
								<input type="submit" class="button small right" value="Отправить">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<!-- ФОРМА ДЛЯ ПИСЬМА - ОКОНЧАНИЕ -->