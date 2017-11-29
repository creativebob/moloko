<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Page;

class PageController extends Controller
{

      public function create(Request $request)
    {


    	$page = new Page;

    	$page->page_name = "Новости";
    	$page->site_id = "2";
    	$page->page_title = "Страница новостей";
    	$page->page_description = "Большое и длинное описание";
    	$page->page_alias = "news";
    	$page->mydate = "12.11.2018";

		$page->save();
		return view('users');
    }


}
