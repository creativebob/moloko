<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Site;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $link)
    {
      $site = Site::with(['news.author', 'news' => function ($query) use ($link) {
        $query->where('news_alias', $link);
      }])->where('api_token', $request->token)->first();
      if ($site) {
      // return Cache::remember('staff', 1, function() use ($domen) {
        return $site->news;
      // });
      } else {
        return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
      }
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // Получаем новости по api
    public function news (Request $request)
    {

      $site = Site::with('news.author')->where('api_token', $request->token)->first();
      if ($site) {
      // return Cache::remember('staff', 1, function() use ($domen) {
        return $site->news;
      // });
      } else {
        return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
      }
    }
  }
