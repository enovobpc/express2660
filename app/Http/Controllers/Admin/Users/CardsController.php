<?php

namespace App\Http\Controllers\Admin\Users;

use Html, Response, Cache, Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\UserCard;

class CardsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'cards';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',users_cards']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function index() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create(Request $request, $userId) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId) {

        UserCard::flushCache(UserCard::CACHE_TAG);

        UserCard::where('user_id', $userId)->forceDelete();

        $input = $request->all();


        foreach ($input['card_no'] as $it => $cardNo) {

            if(!empty($input['card_no'][$it])) {

                $notificationDays = $input['notification_days'][$it] ? $input['notification_days'][$it] : 0;
                $notificationDate = new Date($input['validity_date'][$it]);
                $notificationDate = $notificationDate->subDays($notificationDays);

                $card = new UserCard();
                $card->name              = @$input['name'][$it];
                $card->card_no           = @$input['card_no'][$it];
                $card->type              = @$input['type'][$it];
                $card->issue_date        = @$input['issue_date'][$it];
                $card->validity_date     = @$input['validity_date'][$it];
                $card->notification_days = @$notificationDays;
                $card->notification_date = @$notificationDate;
                $card->obs               = @$input['obs'][$it];
                $card->user_id           = $userId;
                $card->source            = config('app.source');
                $card->save();
            }
        }

        return Redirect::back()->with('success', 'Alterações gravadas com sucesso.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function edit(Request $request, $userId, $contractId = null) {
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $userId, $id = null) {
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($userId, $id) {
//    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function massDestroy(Request $request, $userId) {
//    }
}
