<?php

namespace App\Http\Controllers\Project;

use App\Http\Requests\Project\UserUpdateRequest;
use App\Subscriber;

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
        $page = $site->pages_public->firstWhere('alias', 'profile');

        // TODO - 29.10.20 - Будем убирать оповещения
        $site->load('notifications');

        return view($site->alias . '.pages.profile.index', compact('site', 'page', 'user'));
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

        $data = $request->input();
        $user->update($data);

        $user->notifications()->sync($request->notifications);

        // Проверка подписчика
        $user->load([
            'archiveSubscriber',
            'client'
        ]);

        $subscriber = $user->archiveSubscriber;

        $allow = $request->allow == 1 ? true : false;

        if (isset($request->email)) {

            if (isset($subscriber)) {
                if (isset($subscriber->archived_at)) {
                    $subscriber->unarchive();
                }
                $subscriber->update([
                    'email' => $request->email,
                    'denied_at' => $allow == true ? null : now(),
                    'client_id' => optional($user->client)->id,
                ]);
            } else {
                $subscriber = Subscriber::firstOrCreate([
                    'email' => $user->email,
                    'site_id' => $user->site_id
                ]);

                $subscriber->update([
                    'subscriberable_id' => $user->id,
                    'subscriberable_type' => 'App\User',
                    'name' => $user->name,
                    'denied_at' => $allow == true ? null : now(),
                    'is_self' => 1,
                    'client_id' => optional($user->client)->id,
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
