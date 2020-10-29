<?php

namespace App\Http\Controllers\Project;

use App\Http\Requests\Project\UserUpdateRequest;

class UserController extends BaseController
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth_usersite');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        $user = auth()->user();

        $user->load([
            'subscriber'
        ]);

        $site = $this->site;
        $page = $site->pages_public->firstWhere('alias', 'cabinet');

        // TODO - 29.10.20 - Будем убирать оповещения
        $site->load('notifications');

        return view($site->alias . '.pages.cabinet.index', compact('site', 'page', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserUpdateRequest $request)
    {
        $user = auth()->user();

        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->email = $request->email;
        $user->birthday_date = $request->birthday_date;
        $user->saveQuietly();

        $user->notifications()->sync($request->notifications);

        // Проверка подписчика
        $user->load([
            'subscriber',
        ]);

        $allow = $request->allow == 1 ? true : false;

        if (isset($request->email)) {

            if (isset($user->subscriber)) {
                if (isset($user->subscriber->archived_at)) {
                    $user->subscriber()->unarchive();
                }
                $user->subscriber()->update([
                    'email' => $request->email,
                ]);
            } else {
                $subscriber = \App\Subscriber::firstOrCreate([
                    'email' => $user->email,
                    'site_id' => $user->site_id
                ]);

                $subscriber->update([
                    'subscriberable_id' => $user->id,
                    'subscriberable_type' => 'App\User',
                    'name' => $user->name,
                    'denied_at' => $allow == true ? null : now(),
                    'is_self' => 1,
                ]);
            }
        } else {
            if (isset($user->subscriber)) {
                $user->subscriber()->archive();
            }
        }

        return redirect()->route('project.user.edit');
    }

    /**
     * Разавторизация пользователя
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        auth()->logout();
        return redirect()->route('project.start');
    }
}
