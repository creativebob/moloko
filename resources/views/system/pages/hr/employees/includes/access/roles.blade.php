<employee-roles-component
    :roles='@json($roles)'
    :departments='@json($departments)'
    :user="{{ auth()->user()->load([
    'role_user' => function ($q) {
        $q->with([
            'position',
            'department',
            'role'
        ]);
    }
]) }}"

></employee-roles-component>
