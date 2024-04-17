<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\HelpcenterAuthToken;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use File;

class AccountController extends \App\Http\Controllers\Admin\Controller {
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    /**
     * Change current user password
     * 
     * @return type
     */
    public function edit(Request $request) 
    {  
        if($request->action == 'password') {
            return view('admin.users.account.password')->render();
        }

        return view('admin.users.account.edit')->render();
    }

    /**
     * Change current user password
     * 
     * @return type
     */
    public function update(Request $request) 
    {  
        if($request->action == 'password') {
            return $this->updatePassword($request);
        }

        $input = $request->all();

        $user = Auth::user();
        $user->fill($input);
        $user->save();

        return Redirect::back()->with('success', __('Definições gravadas com sucesso.'));
    }

    /**
     * Change current user password
     * 
     * @return type
     */
    public function updatePassword(Request $request) 
    {  
        $input = $request->only(['current_password', 'password', 'password_confirmation']);
        $input['password'] = trim($input['password']);

        $user = Auth::user();

        if (!Hash::check($input['current_password'], $user->password)) {
            return Redirect::back()->with('error', __('A palavra-passe atual está incorreta.'));
        }
        
        if(strlen($input['password']) < 6) {
            return Redirect::back()->with('error', __('A nova palavra-passe deve ter pelo menos 6 dígitos.'));
        }

        if($input['password'] != $input['password_confirmation']) {
            return Redirect::back()->with('error', __('A palavra-passe e a sua confirmação não correspondem.'));
        }
    
        $user->password = bcrypt($input['password']);
        $user->save();
        
        return Redirect::back()->with('success', __('Palavra-passe alterada com sucesso.'));
    }

    /**
     * Set notice readed by customer
     *
     * @param $noticeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function setNoticeReaded($noticeId = null) {

        $user = Auth::user();

        try {
            //set current notice id as readed
            if(!empty($noticeId)) {

                if($noticeId == 'all') {

                    $allNotices = Auth::user()
                        ->notices()
                        ->wherePivot('readed', 0)
                        ->get();


                    if(!$allNotices->isEmpty()) {
                        foreach ($allNotices as $notice) {
                            $notice->pivot->readed = 1;
                            $notice->pivot->save();
                        }
                    }

                } else {
                    Auth::user()
                        ->notices()
                        ->updateExistingPivot($noticeId, ['readed' => 1]);
                }

            }

            //update total notices
            $totalNotices = Auth::user()
                ->notices()
                ->wherePivot('readed', 0)
                ->count();

            $user->count_notices = $totalNotices;
            $user->save();

            return Redirect::back()->with('success', 'Aviso marcado como lido.');
            /*return response()->json([
                'result'        => true,
                'html'          => view('admin.partials.notices'),
                'total_notices' => $user->count_notices,
                'feedback'      => 'Marcado com lido com sucesso.',
            ]);*/
        } catch (\Exception $e) {
            /*return response()->json([
                'result'   => false,
                'feedback' => $e->getMessage()
            ]);*/
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show full notice
     *
     * @param $noticeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showNotice($noticeId) {

        $notification = Notice::find($noticeId);

        return view('admin.notifications.show', compact('notification'))->render();
    }

    /**
     * Confirm payment notification
     */
    public function paymentConfirm() {

        $user = Auth::user();
        $user->update(['popup_notification' => null]);

        return Redirect::back();
    }
}
