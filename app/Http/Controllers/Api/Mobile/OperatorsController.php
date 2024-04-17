<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Core\ApkInstalation;
use App\Models\LogViewer;
use App\Models\User;
use App\Models\UserLocation;
use App\Models\Equipment\Location;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Date, Log, File, Croppa;

class OperatorsController extends \App\Http\Controllers\Api\Mobile\BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Set authorized fields to store
     * @var array
     */
    protected $authorizedFields = [
        'name',
        'email',
        'phone',
        'filehost',
        'filepath',
        'location_lat',
        'location_lng',
        'location_enabled',
        'location_denied',
        'location_last_update',
        'location_marker'
    ];

    /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = [
        'id',
        'code',
        'name',
        'email',
        'phone',
        'filehost',
        'filepath',
        'vehicle',
        'location_lat',
        'location_lng',
        'location_enabled',
        'location_denied',
        'location_last_update',
        'location_marker'
    ];

    /**
     * Login operator into mobile app
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request) {

        $appSource = config('app.source');

        $curApkVersion = \App\Models\Core\Setting::where('field', 'apk_version')->first();
        $curApkVersion = $curApkVersion->apk_version;

        if($curApkVersion == '1.0.4') {
            $curApkVersion = '2.5.5';
        }

        $curApkVersion = '2.5.5';


        //return $this->responseError('login', '-001', 'App indisponível temporariamente.');
        if(!hasModule('app_apk')) {
            return $this->responseError('login', '-001', 'A sua licença não permite o uso da applicação móvel.');
        }

        $email      = $request->get('email');
        $password   = $request->get('password');
        $apkVersion = $request->get('apk_version');

        if(empty($apkVersion) || $apkVersion < $curApkVersion) {
            return $this->responseError('login', '-001', 'Atualização necessária. Versão '.$curApkVersion.' disponível. Aceda à PlayStore para proceder à atualização.');
        }

        $authData = [
            'password'  => $password,
            'active'    => 1,
            'login_app' => 1,
            'source'    => $appSource
        ];

        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $authData['email'] = $email;
        } else {
            $authData['code'] = $email;
        }

        if (Auth::attempt($authData)) {

            if(!Auth::user()->login_app) {
                return $this->responseError('login', '-003');
            }

            $apkInstalation = ApkInstalation::firstOrNew([
                'source'  => config('app.source'),
                'user_id' => Auth::user()->id
            ]);

            $apkInstalation->source      = $appSource;
            $apkInstalation->user_id     = Auth::user()->id;
            $apkInstalation->name        = Auth::user()->name;
            $apkInstalation->apk_name    = 'enovo_tms';
            $apkInstalation->apk_version = $apkVersion;
            $apkInstalation->last_login  = date('Y-m-d H:i:s');
            $apkInstalation->save();


            if($appSource == 'asfaltolargo') {
                if(strtoupper($email) == 'X001') { //TESTES
                    $token = 'nIWZQonNJN9ERHGj';
                } else if(strtoupper($email) == 'A002') { //zekinha
                    $token = 'asfaltolargo_002';
                } else if(strtoupper($email) == 'A003') { //fred
                    $token = 'asfaltolargo_003';
                } else {
                    $token = str_random();
                }
            } else {
                $token = str_random();
            }

            Auth::user()->api_token   = $token;
            Auth::user()->apk_version = $apkVersion;
            Auth::user()->save();


            $settings = new SettingsController();
            $settings = $settings->getSettings($request, Auth::user(), true);

            $infoLocationEquipment   = null;
            if($settings['mobile_app_menu_equipment'] == 1){
                $infoLocationEquipment = Location::where('operator_id', Auth::user()->id)->select('id', 'name')->first();
            }

            $data = [
                'code'       => '',
                'error'      => '',
                'message'    => 'Login com sucesso.',
                'feedback'   => 'Login com sucesso.',
                'auth_token' => $token,
                'source'     => config('app.source'),
                'id'         => Auth::user()->id,
                'code'       => Auth::user()->code,
                'name'       => Auth::user()->name,
                'email'      => Auth::user()->email,
                'phone'      => Auth::user()->phone,
                'filehost'   => Auth::user()->filehost,
                'filepath'   => Auth::user()->filepath,
                'vehicle'    => Auth::user()->vehicle,
                'location_lat'      => Auth::user()->location_lat,
                'location_lng'      => Auth::user()->location_lng,
                'location_enabled'  => Auth::user()->location_enabled,
                'location_denied'   => Auth::user()->location_denied,
                'location_last_update' => Auth::user()->location_last_update,
                'info_location_equipment' => $infoLocationEquipment,
                'settings' => $settings
            ];

            return response($data, 200)->header('Content-Type', 'application/json');
        }

        return $this->responseError('login', '-001');
    }

    /**
     * Lists all operators
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $operators = User::where('source', config('app.source'))
            ->where('active', 1);

        if($request->has('order')) {
            if($request->order == 'last_update') {
                $operators = $operators->orderBy('location_last_update', 'desc');
            } else {
                $operators = $operators->orderBy($request->order, 'asc');
            }
        } else {
            $operators = $operators->orderBy('name', 'asc');
        }

        $allOperators = $operators->get($this->bindings);

        foreach ($allOperators as $operator) {
            $marker = $operator->getLocationMarker();
            $operator->location_marker = $marker;
        }

        if(!$allOperators) {
            return $this->responseError('lists', '-001') ;
        }

        return response($allOperators, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Get current user information
     *
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $operator = User::where('source', config('app.source'))
            ->where('id', $user->id)
            ->first($this->bindings);

        if(!$operator) {
            return $this->responseError('lists', '-001') ;
        }

        return response($operator, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Update user information
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request) {

        $authorizedFields = [
            'name',
            'email',
            'uploaded_file',
            'password'
        ];

        $input = $request->all();
        $input = array_filter_keys($input, $authorizedFields);

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }


        //get file if exists
        $filepath = $filename = '';
        if(!empty($input['uploaded_file'])) {

            $input['old_filepath'] = $user->filepath;
            $fileContent = $input['uploaded_file'];
            $folder = User::DIRECTORY;

            if(!File::exists(public_path($folder))) {
                File::makeDirectory(public_path($folder));
            }

            $filename = 'avatar_' . str_pad($user->id, 6, '0', STR_PAD_LEFT) . '_' . time() .'.png';
            $filepath = $folder.'/'.$filename;
            $result = File::put(public_path($filepath), base64_decode($fileContent));

            if ($result && !empty($user->filepath) && File::exists(public_path($user->filepath))) {
                Croppa::delete($user->filepath);
                File::delete(public_path($user->filepath));
            }

        }

 /*       Mail::raw(print_r($input, true), function($message) {
            $message->to('paulo.costa@enovo.pt')
                ->subject('RESPONSE');
        });*/


        $user->fill($input);

        if(empty($input['password'])) {
            unset($user->password);
        } else {
            $user->password = bcrypt($input['password']);
            $user->uncrypted_password = $input['password'];
        }

        if($filepath) {
            $user->filehost = config('app.url');
            $user->filepath = @$filepath;
            $user->filename = @$filename;
        }

        $result = $user->save();

        if($result) {

            $result = [
                'error'    => '',
                'message'  => 'Data saved successfully.',
                'filepath' => asset($user->filepath)
            ];

            return response($result, 200)->header('Content-Type', 'application/json');
        }

        return $this->responseError('store', '-001') ;
    }

    /**
     * Update user information
     *
     * @param Request $request
     * @return mixed
     */
    public function storeLocationHistory(Request $request) {

        $input   = $request->all();
        $content = $request->get('content');

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        /*
        //REGISTA HISTORICO DE TRANSACAO
        $totalRows = count(@$content);
        $trace = LogViewer::getTrace(null, 'Location history request for user: ' . $user->name. '. Total '. $totalRows. ' rows.');
        Log::info(br2nl($trace));
        */

        $lastLat = $lastLng = null;
        foreach ($content as $location) {

            $curLat = @$location['latitude'];
            $curLng = @$location['longitude'];

            if(!empty($curLat) && !empty($curLat) && $curLat != 0.0 && $curLng != 0.0 && $lastLat != $curLat && $lastLng != $curLng) {

                $lastLat = $curLat;
                $lastLng = $curLng;

                $userLocation = new UserLocation();
                $userLocation->operator_id = $user->id;
                $userLocation->latitude    = $curLat;
                $userLocation->longitude   = $curLng;
                $userLocation->created_at  = @$location['created_at'];
                $userLocation->save();
            }
        }

        /*Mail::raw($content, function($message) {
            $message->to('paulo.costa@enovo.pt')
                ->subject('RESPONSE');
        });*/

        $result = [
            'error'   => '',
            'message' => 'Data saved successfully.',
        ];

        return response($result, 200)->header('Content-Type', 'application/json');
    }
}