<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Agency;
use App\Models\IncidenceType;
use App\Models\Provider;
use App\Models\Route;
use App\Models\Service;
use App\Models\ShippingStatus;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\PickupPoint;
use Illuminate\Http\Request;
use Auth, Validator, Setting;

class SettingsController extends \App\Http\Controllers\Api\Mobile\BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}


    /**
     * Login operator into mobile app
     *
     * @param Request $request
     * @return mixed
     */
    public function getSettings(Request $request, $user = null, $returnArr = false, $onlySettings = false) {

        if(empty($user)) {
            $user = $this->getUser($request->get('user'));

            if(!$user) {
                return $this->responseError('login', '-002') ;
            }
        }

        $acceptedSettings = [
            'app_mode',
            'mobile_app_autoreturn',
            'mobile_app_autotasks',
            'shipment_notify_operator',
            'mobile_app_menu_tasks',
            'mobile_app_menu_customers',
            'mobile_app_menu_operators',
            'mobile_app_menu_fuel',
            'mobile_app_menu_drive',
            'mobile_app_menu_checklists',
            'mobile_app_menu_timer',
            'mobile_app_menu_balance',
            'mobile_app_menu_traceability',
            'mobile_app_menu_traceability_weight',
            'mobile_app_opt_mark_as_collected',
            'mobile_app_opt_schedule_horary',
            'mobile_app_opt_edit_shipment',
            'mobile_app_details_full_sender',
            'app_list_show_both',
            'mobile_app_status_delivery',
            'mobile_app_receiver_required',
            'mobile_app_transfer_shipments',
            'mobile_app_status_pending_operator',
            'mobile_app_enable_read_service',
            'mobile_app_show_vat',
            'mobile_app_show_wainting_time',
            'mobile_app_receiver_vat_required',
            'mobile_app_edit_fields', 
            'mobile_app_menu_stats',
            'mobile_app_menu_equipment',
            'mobile_app_vat_required',
            'mobile_app_hide_drivers_tab',
            'mobile_app_warehouse',
            'mobile_app_photo_multiple',
            'mobile_app_show_internal_obs',
            'mobile_app_register_user_logs',
            'mobile_app_photo_required',
            'mobile_app_traceability_list',
            'mobile_app_receiver_email_show',
            'mobile_app_receiver_email_required',
            'shipments_reference2_name',
            'shipments_reference3_name',
            'mobile_app_download_guide',
            'mobile_app_download_cmr'
        ];

        $allSettings = Setting::all();

        $mobileSettings = [];
        foreach ($acceptedSettings as $settingName) {
            if($settingName == 'mobile_app_status_delivery') {
                $allSettings[$settingName] = empty($allSettings[$settingName]) ? ShippingStatus::OPERATORS_DELIVERY_DEFAULT_STATUS : $allSettings[$settingName];
            }

            $mobileSettings[$settingName] = @$allSettings[$settingName] ? $allSettings[$settingName] : '0';
        }

        $mobileSettings['app_mode']                             = Setting::get('app_mode');
        $mobileSettings['mobile_app_tasks_show_operators_tab']  = 0;
        $mobileSettings['mobile_app_status_mark_as_collected']  = [37,16,38,20];
        $mobileSettings['mobile_app_schedule_horaries_list']    = $this->getHorariesList();
        $mobileSettings['mobile_app_location_refresh_secs']     = Setting::get('mobile_app_location_refresh_secs') ? Setting::get('mobile_app_location_refresh_secs') : 1;
        $mobileSettings['mobile_app_menu_equipment']            = Setting::get('mobile_app_menu_equipment') ? Setting::get('mobile_app_menu_equipment') : 0;
        $mobileSettings['mobile_app_refresh_delay_secs']        = 15; //segundos em que não é possível fazer novo pedido à API
        $mobileSettings['mobile_app_autosync_mins']             = 10; //tempo do sincronizador automático em minutos
        $mobileSettings['transport_guide_url']                  = route('api.mobile.shipments.guide.download');
        //$mobileSettings['labels_url']                           = route('api.mobile.shipments.labels.download').'/';
        $mobileSettings['mobile_app_menu_incidences']           = 1;
        $mobileSettings['mobile_app_color']                     = env('APP_COLOR_PRIMARY');

        $mobileSettings['pusher_key']                           = env('PUSHER_KEY');
        $mobileSettings['pusher_secret']                        = env('PUSHER_SECRET');
        $mobileSettings['pusher_app_id']                        = env('PUSHER_APP_ID');
        $mobileSettings['pusher_cluster']                       = env('PUSHER_CLUSTER');


        if(!$onlySettings) {
            //agencies
            $agenciesIds = $user->agencies;
            if (empty($agenciesIds)) {
                $agencies = Agency::whereSource(config('app.source'))->pluck('name', 'id')->toArray();
            } else {
                $agencies = Agency::whereIn('id', $agenciesIds)->pluck('name', 'id')->toArray();
            }
            $mobileSettings['agencies'] = $agencies;

            //vehicles
            $vehicles = Vehicle::listVehicles(false, 'name');
            $mobileSettings['vehicles'] = $vehicles;

            //status
            $status = ShippingStatus::ordered()->get(['id', 'name', 'color', 'is_final', 'is_traceability'])->toArray();
            $mobileSettings['shipping_status'] = $status;

            //services
            $services = Service::whereSource(config('app.source'))->ordered()->pluck('name', 'id')->toArray();
            $mobileSettings['services'] = $services;

            //services
            $incidences = IncidenceType::filterSource()->isActive()->ordered()->get()->toArray();
            $mobileSettings['incidences_types'] = $incidences;

             //pickup_points
             $pickupPoints = PickupPoint::select('id', 'code', 'provider_code', 'name', 'address', 'zip_code', 'city', 'country', 'mobile', 'phone', 'email', 'horary')->get()->toArray();
             $mobileSettings['pickup_points'] = $pickupPoints;
            
             //fuel station
            if (hasModule('fleet')) {
                $providers = Provider::ordered()->categoryGasStation()->pluck('name', 'id')->toArray();
                if(!empty($providers)) {
                    $mobileSettings['fuel_providers'] = $providers;
                }
            }

            $mobileSettings['version'] = $this->getApkVersion();
        }

        if($returnArr) {
            return $mobileSettings;
        }

        return response($mobileSettings, 200);
    }

    /**
     * Lists all routes
     *
     * @param Request $request
     * @return mixed
     */
    public function listsRoutes(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $bindings = [
            'id',
            'code',
            'name',
            'color',
            'zip_codes',
            'operator_id',
            'provider_id',
            'vehicle'
        ];

        $operators = Route::where('source', config('app.source'))
            ->ordered()
            ->get($bindings);

        if(!$operators) {
            return $this->responseError('lists', '-001') ;
        }

        return response($operators, 200)->header('Content-Type', 'application/json');
    }

    public function getHorariesList() {

        $default = [
            '07:00 - 08:00',
            '08:00 - 09:00',
            '09:00 - 10:00',
            '10:00 - 11:00',
            '11:00 - 12:00',
            '12:00 - 13:00',
            '13:00 - 14:00',
            '14:00 - 15:00',
            '15:00 - 16:00',
            '16:00 - 17:00',
            '17:00 - 18:00',
            '18:00 - 19:00',
            '19:00 - 20:00',
            '20:00 - 21:00',
            '21:00 - 22:00',
            '22:00 - 23:00',
            '23:00 - 00:00',
        ];

        $horaries = null;
        if(Setting::get('mobile_app_horaries_list')) {
            $horaries = trim(Setting::get('mobile_app_horaries_list'));
            if(!empty($horaries)) {
                $horaries = explode("\r\n", Setting::get('mobile_app_horaries_list'));
                $horaries = array_filter($horaries);
            }
        }

        if(empty($horaries)) {
            $horaries = $default;
        }

        return $horaries;
    }
    /**
     * Return version info
     */
    public function getApkVersion() {
        return [
            'version'  => \App\Models\Core\Setting::get('apk_version'),
            'download' => coreUrl('mobile/enovo_tms.apk')
        ];
    }

    /**
     * Get user from api auth token
     *
     * @param $authToken
     * @return bool
     */
    public function getUser($authToken) {

        if(empty($authToken)) {
            return false;
        }

        $user = User::where('api_token', $authToken)->first();

        if(empty($user)) {
            return false;
        }

        return $user;
    }

    /**
     * Store shipment custom attributes
     * @return array
     */
    public function responseError($method, $code, $message = null) {

        $errors = trans('api.shipments.errors');

        $data = [
            'error'   => $code,
            'message' => $message ? $message : $errors[$method][$code]
        ];

        return response($data, 404)->header('Content-Type', 'application/json');
    }
}