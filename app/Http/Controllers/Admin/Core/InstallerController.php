<?php

namespace App\Http\Controllers\Admin\Core;

use App\Models\CacheSetting;
use App\Models\Company;
use App\Models\Core\Module;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Provider;
use App\Models\ProviderCategory;
use App\Models\PurchaseInvoiceType;
use App\Models\Role;
use App\Models\Service;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\Agency;
use App\Models\User;
use App\Models\WebserviceMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Html, Croppa, Auth,Cache, Setting, DB, Mail;

class InstallerController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $versionsList = DB::connection('mysql_enovo')
            ->table('setup_settings')
            ->where('category', 'version')
            ->pluck('name', 'key')
            ->toArray();

        $colors = trans('admin/global.colors');

        $agencies = Agency::pluck('name', 'id')->toArray();

        $services = Service::groupBy('code')
                        ->ordered()
                        ->pluck('name', 'id')
                        ->toArray();

        $status = ShippingStatus::ordered()->pluck('name', 'id')->toArray();

        $coreAgencies = \App\Models\Core\Agency::get();
        $coreAgenciesMaxId = $coreAgencies->max('id') + 1;
        $coreAgenciesIds = $coreAgencies->pluck('id')->toArray();
        $allPossibleIds = range(1, $coreAgenciesMaxId);
        $availableIds = array_values(array_diff($allPossibleIds, $coreAgenciesIds));

        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'method')
            ->toArray();

        $agency = 0; //Agency::filterSource()->first();
        if(!$agency) {
            $agency = new Agency();
        }

        $data = compact(
            'agency',
            'colors',
            'agencies',
            'services',
            'status',
            'availableIds',
            'webserviceMethods',
            'versionsList'
        );

        return $this->setContent('admin.core.installer.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        Cache::flush();

        $input = $request->all();
        $input['customer_types'] = $request->get('customer_types', false);
        $input['customer_pvp']   = $request->get('customer_pvp', false);
        $input['expenses']       = $request->get('expenses', false);


        $agency = $this->createAgency($input, $request);

        //ativa modulos
        $this->installModules($input);

        //preenche tabelas por defeito
        $this->createFinalCustomer($agency);
        $this->createCustomerTypes();
        $this->createProviderCategories();
        $this->createPurchaseInvoicesTypes();
        $this->createExpenses();


        $user      = $this->createAgencyUser($agency, $input);
        $providers = $this->assignProviders($agency, $input);
        $services  = $this->assignServices($agency, $input);
        $status    = $this->assignStatus($agency, $input);


        //configura definições gerais
        $this->setSettings($agency, $input);

        return Redirect::back()->with('success', 'Agência criada com sucesso.');
    }

    /**
     * Store new agency
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createAgency($input, $request) {


        $company = new Company();
        $company->fill($input);
        $company->source = config('app.source');
        $company->save();

        $coreAgency = new \App\Models\Core\Agency();
        $coreAgency->fill($input);
        if(@$input['agency_id']) {
            $coreAgency->id = $input['agency_id'];
        }
        $coreAgency->save();


        $agency = new Agency();
        $agency->company_id = $company->id;
        $agency->fill($input);
        $agency->id = $coreAgency->id;


        if($request->hasFile('image')) {

            $company->filehost = env('APP_URL').'/';

            if (!$company->upload($request->file('image'))) {
                return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
            }
        }

        //upload image black
        if($request->hasFile('image_black')) {

            if ($agency->exists && !empty($company->filepath_black) && File::exists(public_path(). '/'.$company->filepath_black)) {
                Croppa::delete($company->filepath_black);
            }

            $overrideColumns = [
                'filename' => 'filename_black',
                'filepath' => 'filepath_black'
            ];

            if (!$agency->upload($request->file('image_black'), true, -1, $overrideColumns)) {
                return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
            }
        }

        $company->save();

        $agency->agencies = [strval($agency->id)];
        $agency->save();

        return $agency;
    }

    /**
     * Store new agency
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createAgencyUser($agency, $input) {

        $user = new User();
        $user->source   = config('app.source');
        $user->code     = 'M001';
        $user->agencies = [(string) $agency->id];
        $user->name     = $input['user_name'];
        $user->email    = $input['user_email'];
        $user->password = bcrypt($input['password']);
        $user->login_admin     = 1;
        $user->allowed_actions = '{"edit_prices":"1","edit_blocked":"1","show_budget_providers":"1"}';
        $user->save();

        $roles = Role::whereIn('name', ['agencia', 'acesso-a-licenca'])->pluck('id')->toArray();
        $user->roles()->sync($roles);

        return $user;
    }

    /**
     * Store agency
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createFinalCustomer($agency) {

        $customer = new Customer();
        $customer->final_consumer = true;
        $customer->source    = config('app.source');
        $customer->company_id= 1;
        $customer->agency_id = $agency->id;
        $customer->vat       = '999999990';
        $customer->code      = 'CFINAL';
        $customer->name      = 'Consumidor Final';
        /*$customer->address   = $agency->address;
        $customer->zip_code  = $agency->zip_code;
        $customer->city      = $agency->city;*/
        $customer->country   = Setting::get('app_country');
        $customer->save();
    }

    /**
     * Assign services
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignServices($agency, $input) {

        $services = @$input['services'];

        if(!empty($services)) {

            $services = Service::whereIn('id', $services)->get();

            foreach ($services as $service) {

                $newService = $service->replicate();

                $agencies = [(string) $agency->id];

                $newService->source   = config('app.source');
                $newService->agencies = $agencies;
                $newService->save();
            }
        }

    }

    /**
     * Assign status
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignStatus($agency, $input) {

       /* $status = $input['status'];
        $status = ShippingStatus::whereIn('id', $status)->get();
        foreach ($status as $item) {
            $item->sources = [config('app.source')];
            $item->save();
        }*/

        ShippingStatus::where('id', '>=', '1')->update(['sources' => '["'.config('app.source').'"]']);

    }


    /**
     * Assign providers
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignProviders($agency, $input) {

        $providers = $input['providers'];

        foreach ($providers as $item) {
            $id = strval($agency->id);
            if(!empty($item['name'])) {
                $provider = new Provider();
                $provider->source   = config('app.source');
                $provider->agencies = [$id];
                $provider->name     = $item['name'];
                $provider->color    = $item['color'];
                $provider->webservice_method = @$item['webservice_method'];
                $provider->type              = 'carrier';
                $provider->category_id       = 1;
                $provider->setCode();
            }
        }
    }

    /**
     * Store agency
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createCustomerTypes() {

        $type = new CustomerType();
        $type->name = 'Clientes Gerais';
        $type->save();

        return $type;
    }

    /**
     * Store agency
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createProviderCategories() {

        DB::table('providers_categories')
            ->truncate();

        $type = new ProviderCategory();
        $type->name      = 'Transportador Subcontratado';
        $type->source    = config('app.source');
        $type->color     = '#FFF176';
        $type->slug      = 'transporter';
        $type->is_static = 1;
        $type->save();

        $type = new ProviderCategory();
        $type->name      = 'Gasolineiras';
        $type->source    = config('app.source');
        $type->color     = '#f95400';
        $type->slug      = 'gas_station';
        $type->is_static = 1;
        $type->save();

        $type = new ProviderCategory();
        $type->name      = 'Oficinas e Mecânicos';
        $type->source    = config('app.source');
        $type->color     = '#FFAB00';
        $type->slug      = 'mechanic';
        $type->is_static = 1;
        $type->save();

        $type = new ProviderCategory();
        $type->name      = 'Autoestradas e Portagens';
        $type->source    = config('app.source');
        $type->color     = '#1abc9c';
        $type->slug      = 'tolls';
        $type->is_static = 1;
        $type->save();

        $type = new ProviderCategory();
        $type->name      = 'Centros de Inspeção';
        $type->source    = config('app.source');
        $type->color     = '#27A9E1';
        $type->slug      = 'car_inspection';
        $type->is_static = 1;
        $type->save();

        $type = new ProviderCategory();
        $type->name      = 'Restaurantes';
        $type->source    = config('app.source');
        $type->color     = '#b33f04';
        $type->slug      = 'restaurant';
        $type->is_static = 1;
        $type->save();

        $type = new ProviderCategory();
        $type->name      = 'Comunicações e Informática';
        $type->source    = config('app.source');
        $type->color     = '#b33f04';
        $type->slug      = 'restaurant';
        $type->is_static = 1;
        $type->save();

        $type = new ProviderCategory();
        $type->name      = 'Outros Fornecedores';
        $type->source    = config('app.source');
        $type->color     = '#2c3e50';
        $type->slug      = 'others';
        $type->is_static = 1;
        $type->save();

        return $type;
    }


    /**
     * Store agency
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createPurchaseInvoicesTypes() {

        /*DB::table('purchase_invoices_types')
            ->truncate();*/

        $type = new PurchaseInvoiceType();
        $type->name        = 'Subcontratos Transporte';
        $type->target_type = 'Shipment';
        $type->is_static   = 1;
        $type->sort        = 1;
        $type->save();

        $type = new PurchaseInvoiceType();
        $type->name        = 'Manutenções e Oficina';
        $type->target_type = 'Vehicle';
        $type->is_static   = 1;
        $type->sort        = 2;
        $type->save();

        $type = new PurchaseInvoiceType();
        $type->name        = 'Viagens e Portagens';
        $type->target_type = 'Vehicle';
        $type->is_static   = 1;
        $type->sort        = 3;
        $type->save();

        $type = new PurchaseInvoiceType();
        $type->name        = 'Abastecimentos';
        $type->target_type = 'Vehicle';
        $type->is_static   = 1;
        $type->sort        = 4;
        $type->save();

        $type = new PurchaseInvoiceType();
        $type->name        = 'Alimentação';
        $type->target_type = 'User';
        $type->is_static   = 1;
        $type->sort        = 5;
        $type->save();

        $type = new PurchaseInvoiceType();
        $type->name        = 'Ordenados';
        $type->target_type = 'User';
        $type->is_static   = 1;
        $type->sort        = 6;
        $type->save();

        $type = new PurchaseInvoiceType();
        $type->name        = 'Seguradoras';
        $type->target_type = '';
        $type->is_static   = 1;
        $type->sort        = 7;
        $type->save();

        $type = new PurchaseInvoiceType();
        $type->name        = 'Despesas Administrativas';
        $type->target_type = '';
        $type->is_static   = 1;
        $type->sort        = 8;
        $type->save();

        return $type;
    }

    /**
     * Store agency
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createExpenses() {

        $expense = new ShippingExpense();
        $expense->code = 'REC';
        $expense->name = 'Taxa de Recolha';
        $expense->type = 'pickup';
        $expense->zones_arr = null;
        $expense->complementar_service   = 0;
        $expense->customer_customization = 0;
        $expense->account_complementar_service = 0;
        $expense->save();
    }

    /**
     * Store agency
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function installModules($input) {

        //ativar modulos e espaço
        $modules = DB::connection('mysql_enovo')
            ->table('setup_settings')
            ->where('key', $input['version'])
            ->first();

        $modules = explode(',', $modules->value);

        Module::setActiveModules($modules);

        CacheSetting::set('plan_version', $input['version']);
    }

    /**
     * Store agency
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setSettings($agency, $input) {

        $allSettings = [
            'app_mode' => $input['app_mode'],
            'app_skin' => $input['app_skin'],
            'shipment_default_provider' => 1,

            'company_name'      => @$agency->company,
            'vat'               => @$agency->vat,
            'company_address'   => @$agency->address,
            'company_zip_code'  => @$agency->zip_code,
            'company_city'      => @$agency->city,
            'company_country'   => @$agency->country,
            'company_phone'     => @$agency->phone,
            'company_mobile'    => @$agency->mobile,
            'company_email'     => @$agency->email,
            'company_website'   => @$agency->web,
            'company_permit'    => @$agency->charter,
            'support_phone_1'   => @$agency->phone,
            'support_mobile_1'  => @$agency->mobile,
        ];


        //ativar modulos e espaço
       $defaultSettings = DB::connection('mysql_enovo')
           ->table('setup_settings')
           ->where('category', 'settings')
           ->where('key', $input['app_mode'])
           ->first();

       if($defaultSettings) {

           $defaultSettings = json_decode(@$defaultSettings->value, true);
           unset($defaultSettings['maintenance_ignore_ip'], $defaultSettings['debug_ignore_ip']);

           $allSettings = array_merge($allSettings, $defaultSettings);
       }

        foreach ($allSettings as $attribute => $value) {
            Setting::set($attribute, $value);
        }

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



        //dados contacto


        //fornecedor por defeito
    }


    /**
     * Test e-mail
     */
    public function testEmail() {

        try {
            $email = Auth::user()->email;

            Mail::send('emails.test_email', [], function ($message) use($email) {
                $message->to($email)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('E-mail de teste');
            });

            if (count(Mail::failures()) > 0) {
                return Redirect::back()->with('error', 'E-mail não enviado.');
            }

            return Redirect::back()->with('success', 'E-mail enviado com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage() . ' FILE ' . $e->getFile() . ' LINE '.$e->getLine());
        }
    }

    /**
     * Upload Logos
     */
    public function uploadLogos(Request $request) {

        $files = $request->file('files');

        $errors = [];
        foreach ($files as $file) {

            $filename     = $file->getClientOriginalName();
            $uploadFolder = public_path('/assets/img/logo');

            if($filename == 'favicon.png') {
                $uploadFolder = public_path();
            }

            if (!$file->move($uploadFolder, $filename)) {
                $errors[] = basename($filename);
            }
        }

        if(!empty($errors)) {
            $files = implode(',', $errors);
            return Redirect::back()->with('error', 'Algumas imagens não foram carregadas: '.$files);
        }

        return Redirect::back()->with('success', 'Logos carregados com sucesso.');
    }
}
