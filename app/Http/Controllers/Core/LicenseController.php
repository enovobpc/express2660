<?php

namespace App\Http\Controllers\Core;

use App, File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class LicenseController extends \App\Http\Controllers\Admin\Controller {
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Check license
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request, $key)
    {

        //Encriptar
        /* $key = ($key*4) + 872342001;
        $key = base64_encode(base64_encode($key));*/

        //desencriptar
        $key = base64_decode(base64_decode($key));
        $key = ($key-872342001) / 4;

        $now  = Carbon::now();
        $time = Carbon::createFromTimestamp($key);
        $diff = $now->diffInSeconds($time);

        if($diff >= 30) {
            App::abort(404);
        }

        $status      = $request->get('status', 'expired');
        $countUnpaid = $request->get('count');
        $totalUnpaid = $request->get('total');
        $refMb       = $request->get('ref');
        $refEntity   = $request->get('entity');

       /* if($key && config('app.env') != 'local') {
            return Redirect::back();
        }*/

        $filename = storage_path() . '/license.json';

        $fileContent = [
            'total_unpaid' => $totalUnpaid,
            'count_unpaid' => $countUnpaid,
            'mb_ref'       => $refMb,
            'mb_entity'    => $refEntity
        ];

        if($status == 'expired') {
            File::put($filename, json_encode($fileContent));
            File::delete(storage_path() . '/enovo_payments.json');
        } else {
            File::delete($filename);
        }

        return response()->json([
            'result' => true
        ], 200);
    }

    /**
     * Check license
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function payments(Request $request, $key)
    {

        $html = $request->get('html');
        $type = $request->get('type');

/*        //desencriptar key
        $key = base64_decode(base64_decode($key));
        $key = ($key-872342001) / 4;

        $now  = Carbon::now();
        $time = Carbon::createFromTimestamp($key);
        $diff = $now->diffInSeconds($time);

        if($diff >= 300000) {
            App::abort(404);
        }*/


        $data = [
            'title'   => $request->title ? $request->title : 'Notificação de pagamento',
            'size'    => $request->size ? $request->size : '',
            'button'  => $request->button ? $request->button : 'Tomei conhecimento',
            'content' => $html
        ];

        if($type == 'payment') {
            App\Models\User::filterSource()
                ->whereHas('roles', function ($query) {
                    $query->whereHas('perms', function ($query) {
                        $query->where('name', '=', 'licenses');
                    });

                })->update(['popup_notification' => json_encode($data)]);
        }

        return response()->json([
            'result' => true
        ], 200);
    }
}
