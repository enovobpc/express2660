<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Core\LicenseController;
use App\Models\Agency;
use App\Models\Budget\Budget;
use App\Models\Budget\Ticket;
use App\Models\CacheSetting;
use App\Models\CustomerType;
use App\Models\PaymentCondition;
use App\Models\PriceTable;
use App\Models\Shipment;
use Setting, File, Auth, Response, Croppa, Date;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

use App\Models\ShippingStatus;
use App\Models\Billing;
use App\Models\Service;
use App\Models\Provider;
use App\Models\ShippingExpense;

class SettingsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'admin_settings';

    /**
     * List of stoage directories
     *
     * @var array
     */
    protected $storageDirectories = [
        '/framework/cache',
        '/framework/views',
        '/framework/sessions',
        '/logs',
        '/debugbar',
        '/importer',
        '/invoices',
        '/keyinvoice-logs'
    ];

    /**
     * List of stoage directories
     *
     * @var array
     */
    protected $directoryModels = [
        '/uploads/agencies' => 'Agency',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',admin_settings']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $customersTypes = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();


        $statusPickups = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->isPickup()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $expenses = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $billingProducts = Billing\Item::filterSource()
            ->pluck('name', 'reference')
            ->toArray();

        $pricesTables = PriceTable::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $uploadDirectories  = [];

        $storageDirectories = [];
        foreach ($this->storageDirectories as $directory) {

            $folderName = explode('/', $directory);
            $folderName = end($folderName);

            $storageDirectories[$folderName] = ['filepath' => storage_path() . $directory];
        }


        $notificationSounds = [];
        $soundsDir = public_path('assets/sounds');
        $soundsFiles = collect(File::allFiles($soundsDir));
        $soundsFiles = $soundsFiles->sortBy(function ($file) {
            return $file->getFilename();
        });

        $soundId = 1;
        foreach ($soundsFiles as $soundFile) {

            $filename = $soundFile->getFilename();
            $filename = str_replace('.mp3', '', $filename);
            if (str_contains($filename, 'notification')) {
                $notificationSounds[$filename] = 'Notificação ' . $soundId;
                $soundId++;
            }
        }

        $pdfBgVertical = null;
        if (File::exists(public_path() . '/uploads/pdf/bg_v.png')) {
            $pdfBgVertical = '/uploads/pdf/bg_v.png';
        }


        $minutes = ['00', '01', '29', '30', '31', '59'];
        $fullHoursList = [];
        for ($i = 0; $i < 24; $i++) {
            $hour = $i <= 9 ? '0' . $i : $i;
            foreach ($minutes as $minute) {
                $fullHoursList[$hour . ':' . $minute] = $hour . ':' . $minute;
            }
        }

        $data = compact(
            'providers',
            'services',
            'status',
            'statusPickups',
            'billingProducts',
            'storageDirectories',
            'uploadDirectories',
            'pricesTables',
            'notificationSounds',
            'pdfBgVertical',
            'customersTypes',
            'agencies',
            'fullHoursList',
            'paymentConditions',
            'expenses'
        );

        return $this->setContent('admin.settings.settings.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = $request->except('_token');
        $input['customization_disable_notification_sound'] = $request->get('customization_disable_notification_sound', false);
        $input['customization_fixed_menu'] = $request->get('customization_fixed_menu', false);

        $excludeAdminFields = new LicenseController();
        $excludeAdminFields = $excludeAdminFields->settingFields;

        $allSettings = Setting::all();
        foreach ($allSettings as $setting => $value) {
            if (!in_array($setting, $excludeAdminFields)) {
                Setting::forget($setting);
            }
        }
        Setting::save();

        if ($request->has('delete_pdf_bg')) {
            File::delete(public_path('uploads/pdf/bg_v.png'));
        }

        if ($request->hasFile('pdf_bg')) {
            $file = $request->file('pdf_bg');
            $file->move(public_path('uploads/pdf'), 'bg_v.png');
        }

        $customizationFields = [];
        foreach ($input as $attribute => $value) {

            if (!empty($value) && $attribute == 'shipments_limit_search') {

                $today = Date::today();
                $minDate = $today->subMonth($value)->format('Y-m-d');

                $sourceAgencies = Agency::filterSource()->pluck('id')->toArray();
                $minShipment = Shipment::whereIn('agency_id', $sourceAgencies)
                    ->where('date', '>=', $minDate)->first(['id', 'date']);

                if ($minShipment) {
                    CacheSetting::set('shipments_limit_search', $minShipment->id);
                    CacheSetting::set('shipments_limit_search_date', $minShipment->date);
                }

                if (hasModule('budgets')) {
                    $minBudget = Budget::where('source', config('app.source'))
                        ->where('date', '>=', $minDate)
                        ->first(['id', 'date']);

                    if ($minBudget) {
                        CacheSetting::set('budgets_limit_search', $minBudget->id);
                    }
                }
            }

            if (!empty($value)) {
                Setting::set($attribute, $value);
            }


            if (str_contains($attribute, 'customization_')) {
                $attr = str_replace('customization_', '', $attribute);
                $customizationFields[$attr] = empty($value) ? null : $value;
            }
        }

        //Setting::save();

        /*//maintenance mode
        if(Setting::get('maintenance_mode')) {
            $ips = Setting::get('maintenance_ignore_ip');
            if(!empty($ips)) {
                $ips = explode(',', Setting::get('maintenance_ignore_ip'));
            }

            if(empty($ips) || !in_array(client_ip(), $ips)) { //force to set current ip
                $ips[] = client_ip();
            }

            touch(storage_path() . '/framework/down');
        } else {
            File::delete(storage_path() . '/framework/down');
        }

        //debug mode
        if(Setting::get('debug_mode')) {
            $ips = Setting::get('debug_ignore_ip');
            if(!empty($ips)) {
                $ips = explode(',', Setting::get('debug_ignore_ip'));
            }

            if(empty($ips) || !in_array(client_ip(), $ips)) { //force to set current ip
                $ips[] = client_ip();
            }

            $filename = storage_path() . '/framework/debug_ips';
            File::put($filename, implode(',', $ips));

        } else {
            File::delete(storage_path() . '/framework/debug_ips');
        }*/


        //Personaliza preferencias regionais
        $currency = Setting::get('app_currency');
        if ($currency == '€') {
            Setting::set('app_currency_icon', 'fa-euro-sign');
        } elseif ($currency == '$') {
            Setting::set('app_currency_icon', 'fa-dollar-sign');
        } else {
            Setting::set('app_currency_icon', 'fa-coins');
        }

        //Personaliza preferencias regionais
        $country = Setting::get('app_country');
        if ($country == 'br') {
            Setting::set('locale_label_vat', 'CPF');
        } elseif ($country == 'us') {
            Setting::set('locale_label_vat', 'CPF');
        } else {
            Setting::set('locale_label_vat', 'NIF');
        }


        Setting::save();

        if (!empty($customizationFields)) {
            Auth::user()->setSettingArray($customizationFields);
        }

        return Redirect::back()->with('success', 'Alterações gravadas com sucesso');
    }
}
