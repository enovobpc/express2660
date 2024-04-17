<?php

namespace App\Http\Controllers\Auth;

use App\Models\Customer;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Validator, Setting, Mail, Auth, Redirect, App;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/area-cliente';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * Custom login guard
     *
     * @return type
     */
    protected function guard()
    {
        return Auth::guard('customer');
    }

    /**
     *
     * Login index controller
     *
     */
    public function index()
    {

        if (!hasModule('account_signup') || !Setting::get('account_signup')) {
            App::abort('403');
        }

        return $this->setContent('auth.login');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(Request $request)
    {

        $input = $request->toArray();

        $customer = Customer::where('email', $input['email'])->first();

        if ($customer) {
            return Redirect::back()->withInput()->with('error', 'Já existe outro utilizador com o e-mail indicado. Se perdeu a palavra-passe, experimente a opção recuperar palavra-passe');
        }

        if (!str_contains($input['name'], ' ') || (!Setting::get('account_signup_fast') && !str_contains($input['address'], ' '))) {
            return Redirect::back()->withInput()->with('error', 'Dados inválidos. Verifique o nome e/ou morada.');
        }

        try {
            $customer = new Customer();
            $customer->name             = trim($input['name']);
            $customer->display_name     = trim($input['name']);
            $customer->email            = strtolower(trim($input['email']));
            $customer->contact_email    = strtolower(trim($input['email']));
            $customer->mobile           = @$input['mobile'];
            $customer->phone            = @$input['phone'];
            $customer->vat              = @$input['vat'];
            $customer->address          = @$input['address'];
            $customer->zip_code         = @$input['zip_code'];
            $customer->city             = @$input['city'];
            $customer->country          = @$input['country'];
            $customer->billing_name     = @$input['billing_name'];
            $customer->billing_address  = @$input['billing_address'];
            $customer->billing_zip_code = @$input['billing_zip_code'];
            $customer->billing_city     = @$input['billing_city'];
            $customer->billing_country  = @$input['billing_country'];
            $customer->payment_method   = config('app.source') == 'entregaki' ? 'wallet' : '30d';
            $customer->password         = bcrypt($input['password']);
            $customer->uncrypted_password = $input['password'];
            $customer->is_particular    = $input['type'] == 'particular' ? 1 : 0;
            $customer->price_table_id   = Setting::get('account_signup_prices_table');
            $customer->has_prices       = Setting::get('account_signup_prices_table') ? 1 : 0;
            $customer->is_validated     = Setting::get('account_signup_validate') ? 0 : 1;
            $customer->type_id          = Setting::get('account_signup_type');
            $customer->agency_id        = Setting::get('account_signup_agency');
            $customer->agency_id        = Setting::get('account_signup_agency');
            $customer->enabled_services = Setting::get('account_signup_services');
            $customer->login_created_at = date('Y-m-d H:i:s');
            $customer->is_mensal        = config('app.source') == 'entregaki' ? 0 : 1;
            $customer->setCode();

            //set notification
            if (!$customer->is_validated) {
                $customer->setSignupNotification();
            }

            //start session
            Auth::guard('customer')->login($customer);

            if (empty(Setting::get('account_signup_validate'))) {
                //send wellcome message
                Mail::send('emails.customers.validated', compact('input', 'customer'), function ($message) use ($input, $customer) {
                    $message->to($customer->email)
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject('Dados de acesso à área de cliente');
                });

                return Redirect::route('account.index')->with('success', 'Conta criada com sucesso.');
            }

            return Redirect::route('account.index')->with('success', 'Obrigado! O seu pedido aguarda aprovação. Poderá aceder após aprovarmos o seu pedido.');
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with('error', 'Erro ao criar conta. Não foi possível criar a sua conta devido a um erro desconhecido.');
        }
    }
}
