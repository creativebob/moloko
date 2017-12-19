<div class="top-bar-container header-z-index" id="header" data-sticky-container>
  <div class="sticky sticky-topbar" data-sticky data-margin-top="0" data-options="stickyOn: small;" data-top-anchor="header:top">
    <header class="grid-x header">
      <div class="small-7 left-head cell">
        <!-- Кнопка сворачивания на мобилках -->
        <div class="title-bar" data-responsive-toggle="sidebar" data-hide-for="medium" data-hide-for="large">
          <button class="menu-icon" type="button" data-toggle="sidebar"></button>
          <!-- <div class="title-bar-title"></div> -->
        </div>
        <!-- Логотип -->
        <h1><span>Mars</span>Crm</h1>
      </div>
      <div class="small-5 right-head cell">
        <ul>
          <li><a id="task-toggle"><img src="img/header/alert.png"></a></li>
          <li><a data-toggle="profile"><span>{{ Auth::user()->company->company_name }} | {{ Auth::user(); }}</span><img src="img/header/avatar.png"></a></li>
        </ul>
        <div class="dropdown-pane profile-head" id="profile" data-dropdown data-position="bottom" data-alignment="right" data-v-offset="10" data-h-offset="-30" data-close-on-click="true">
          <ul class="menu vertical">
            <li><a href="index.php">Профиль</a></li>
            <li><a href="">Настройки</a></li>
            <li><hr></li>
            <li><a href="">Нужна помощь?</a></li>
            <li><a href="index.php">Выход</a></li>
          </ul>
        </div>
      </div>
    </header>
  </div>
</div>

