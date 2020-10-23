<?php

namespace App\Http\Controllers;

use App\MailingListItem;
use Illuminate\Http\Request;

class MailingListItemController extends Controller
{

    protected $entityAlias;
    protected $entityDependence;

    /**
     * SubscriberController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'mailing_list_items';
        $this->entityDependence = false;
    }

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
     * @param  \App\MailingListItem  $mailingListItem
     * @return \Illuminate\Http\Response
     */
    public function show(MailingListItem $mailingListItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MailingListItem  $mailingListItem
     * @return \Illuminate\Http\Response
     */
    public function edit(MailingListItem $mailingListItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MailingListItem  $mailingListItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MailingListItem $mailingListItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MailingListItem  $mailingListItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(MailingListItem $mailingListItem)
    {
        //
    }
}
