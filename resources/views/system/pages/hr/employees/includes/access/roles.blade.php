@can('index', App\Role::class)
    <div class="small-12 cell tabs-margin-top">
        <table class="content-table">
            <caption>Уровень доступа</caption>
            <thead>
            <tr>
                <th>Роль</th>
                <th>Филиал</th>
                <th>Должность</th>
                <th>Инфа</th>
                <th class="td-delete"></th>
            </tr>
            </thead>
            <tbody class="roleuser-table">
            @if (!empty($user->role_user))
                @foreach ($user->role_user as $role_user)
                    @include('system.pages.marketings.users.roles', $role_user)
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <div class="small-8 small-offset-2 medium-8 medium-offset-2 tabs-margin-top text-center cell">
        <a class="button" data-open="role-add">Настройка доступа</a>
    </div>
@endcan
