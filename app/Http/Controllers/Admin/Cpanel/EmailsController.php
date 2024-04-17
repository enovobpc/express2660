<?php

namespace App\Http\Controllers\Admin\Cpanel;

use App\Models\CacheSetting;
use App\Models\Cpanel\Email;
use App\Models\Cpanel\Quota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Response, Auth;


class EmailsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'email_accounts';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',email_accounts']);
        validateModule('account_emails');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $emailAccount = new Email();
        $listAccounts = $emailAccount->listEmails();

        $host = Email::getHost();

        foreach ($listAccounts as $account) {

            if($host == $account['domain']) {
                $email = Email::filterSource()->where('email', $account['email'])->first();

                if(!$email) {
                    $email = new Email();
                    $email->email  = $account['email'];
                    $email->source = config('app.source');
                    $email->created_by = Auth::user()->id;
                }

                $email->quota               = @$account['diskquota'];
                $email->usage               = @$account['diskused'];
                $email->login_suspended     = @$account['suspended_login'];
                $email->incoming_suspended  = @$account['suspended_incoming'];
                $email->outgoing_suspended  = @$account['suspended_outgoing'];
                $email->save();
            }
        }

        $quota = new Quota();
        $quota = $quota->getServerQuota();

        $usageTotal = @$quota['megabytes_used']; //converte para bytes
        //$quotaTotal = @$quota['megabyte_limit']; //obtem da API
        $quotaTotal = CacheSetting::get('quota') * 1000;

        $quotaTotal = $quotaTotal * 1000000; //converte para bytes
        $usageTotal = $usageTotal * 1000000; //converte para bytes

        $percent = $quotaTotal ? ($usageTotal * 100) / $quotaTotal : 0;

        $color = '#5cb85c';
        if($percent <= 60) {
            $color = '#5cb85c'; //green
        } else if($percent > 60 && $percent <= 70) {
            $color = '#ffd400';  //yellow
        } else if($percent > 70 && $percent <= 80) {
            $color = '#FF8A18';  //orange
        } else if($percent > 80) {
            $color = '#F90000';  //red
        }

        $quota = [
            'quota'   => $quotaTotal, //$quotaTotal,
            'usage'   => $usageTotal,
            'percent' => $percent,
            'color'   => $color
        ];

        $emails = [
            'quota' => CacheSetting::get('emails_total'),
            'usage' => Email::filterSource()->whereNull('deleted_at')->count()
        ];

        return $this->setContent('admin.cpanel.emails.index', compact('quota', 'emails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $emailAccount = new Email();

        $domain = Email::getHost();;

        $data = compact(
            'emailAccount',
            'domain'
        );

        return view('admin.cpanel.emails.create', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $host  = Email::getHost();
        $input = $request->all();
        $input['email'] = $input['email'] . '@' . $host;
        $input['quota'] = empty($input['quota']) ? 'unlimited' : $input['quota'];

        try {

            $emailAccount = new Email();
            $emailAccount->fill($input);
            $emailAccount->source = config('app.source');
            $emailAccount->created_by = Auth::user()->id;
            $result = $emailAccount->save();

            if($result) {

                $params = [
                    'email'    => $emailAccount->email,
                    'password' => $emailAccount->password,
                    'quota'    => $emailAccount->quota
                ];

                $emailAccount->addEmail($params);

                $response = [
                    'result'   => true,
                    'feedback' => 'Conta de e-mail criada com sucesso.'
                ];
            } else {
                $response = [
                    'result'   => false,
                    'feedback' => 'Erro ao criar conta e-mail.'
                ];
            }

        } catch (\Exception $e) {

            $emailAccount->forceDelete();

            $message = $e->getMessage();

            if(str_contains($message, 'too weak')) {
                $message = 'Deve indicar uma password mais segura.';
            }

            $response = [
                'result'   => false,
                'feedback' => $message
            ];
        }


        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $emailAccount = Email::findOrfail($id);

        $data = compact(
            'emailAccount'
        );

        return view('admin.cpanel.emails.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $input = $request->all();
        $input['login_suspended']    = $request->get('login_suspended', 0);
        $input['incoming_suspended'] = $request->get('incoming_suspended', 0);
        $input['outgoing_suspended'] = $request->get('outgoing_suspended', 0);

        $emailAccount = Email::filterSource()
            ->whereId($id)
            ->firstOrFail();

        $email  = explode('@', $emailAccount->email);
        $domain = $email[1];
        $email  = $email[0];

        try {

            //change quota
            if($input['quota'] != $emailAccount->quota) {

                $params = [
                    'domain' => $domain,
                    'email'  => $email,
                    'quota'  => $input['quota'] ? $input['quota'] : 'unlimited'
                ];

                $emailAccount->updateQuota($params);
                $emailAccount->quota = $input['quota'];
                $emailAccount->save();
            }

            //change password
            if(!empty($input['password']) && $input['password'] != $emailAccount->password) {

                $params = [
                    'email'    => $emailAccount->email,
                    'password' => trim($input['password'])
                ];

                $emailAccount->updatePassword($params);
                $emailAccount->password = $input['password'];
                $emailAccount->save();
            }

            //login suspended
            if($input['login_suspended'] != $emailAccount->login_suspended) {
                $emailAccount->suspendLogin($emailAccount->email, $input['login_suspended']);
                $emailAccount->login_suspended = $input['login_suspended'];
                $emailAccount->save();
            }

            if($input['incoming_suspended'] != $emailAccount->incoming_suspended) {
                $emailAccount->suspendIncoming($emailAccount->email, $input['incoming_suspended']);
                $emailAccount->incoming_suspended = $input['incoming_suspended'];
                $emailAccount->save();
            }

            if($input['outgoing_suspended'] != $emailAccount->outgoing_suspended) {
                $emailAccount->suspendOutgoing($emailAccount->email, $input['outgoing_suspended']);
                $emailAccount->outgoing_suspended = $input['outgoing_suspended'];
                $emailAccount->save();
            }


            $response = [
                'result'   => true,
                'feedback' => 'Altrações gravadas com sucesso.'
            ];


        } catch (\Exception $e) {

            $message = $e->getMessage();

            if(str_contains($message, 'too weak')) {
                $message = 'Deve indicar uma password mais segura.';
            }

            $response = [
                'result'   => false,
                'feedback' => $message
            ];
        }


        return response()->json($response);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $emailAccount = Email::filterSource()
                    ->whereId($id)
                    ->first();

        try {

            $params = [
                'email' => $emailAccount->email
            ];

            $emailAccount->deleteEmail($params);
            $emailAccount->deleted_by = Auth::user()->id;
            $emailAccount->deleted_at = date('Y-m-d H:i:s');
            $emailAccount->save();

            return Redirect::route('admin.cpanel.emails.index')->with('success', 'Conta de E-mail removida com sucesso.');

        } catch (\Exception $e) {
            if(str_contains($e->getMessage(), 'You do not have an email account')) {
                $emailAccount->delete();
                return Redirect::route('admin.cpanel.emails.index')->with('success', 'Conta de E-mail removida com sucesso.');
            }
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
    

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = Email::filterSource()
                    ->whereNull('deleted_at')
                    ->select();

        return Datatables::of($data)
            ->edit_column('email', function($row) {
                return view('admin.cpanel.emails.datatables.email', compact('row'))->render();
            })
            ->edit_column('login_suspended', function($row) {
                return view('admin.cpanel.emails.datatables.status', compact('row'))->render();
            })
            ->edit_column('quota', function($row) {
                return view('admin.cpanel.emails.datatables.quota', compact('row'))->render();
            })
            ->edit_column('incoming_suspended', function($row) {
                return view('admin.cpanel.emails.datatables.incoming', compact('row'))->render();
            })
            ->edit_column('outgoing_suspended', function($row) {
                return view('admin.cpanel.emails.datatables.outgoing', compact('row'))->render();
            })
            ->edit_column('autoresponder_active', function($row) {
                return view('admin.cpanel.emails.datatables.autoresponder', compact('row'))->render();
            })
            ->edit_column('forwarding_active', function($row) {
                return view('admin.cpanel.emails.datatables.forwarding', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.cpanel.emails.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function configs(Request $request, $configType = 'smtp') {

        if($configType == 'smtp') {
            $email = Email::filterSource()->first();

            $emailAccounts = Email::filterSource()
                ->whereNull('deleted_at')
                ->pluck('email', 'password');

            $params = [
                'account' => $email->email
            ];

            $emailAccount = new Email();
            $configs = $emailAccount->getSmtpSettings($params);

            return view('admin.cpanel.emails.configs', compact('configs', 'emailAccounts', 'email'))->render();

        } else if($configType == 'list') {

            $emailAccounts = Email::filterSource()
                ->whereNull('deleted_at')
                ->get();

            return view('admin.cpanel.emails.list_accounts', compact('emailAccounts'))->render();

        } else if($configType == 'install') {
            return view('admin.cpanel.emails.install')->render();
        }
    }

    /**
     * Start remote login
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remoteLogin(Request $request, $id) {

        $emailAccount = Email::filterSource()
            ->whereNull('deleted_at')
            ->whereId($id)
            ->first();


        $loginDetails = $emailAccount->remoteLoginUrl($emailAccount->email);

        return $this->setContent('admin.cpanel.emails.login', compact('loginDetails', 'emailAccount'));
    }

    /**
     * Install e-mails
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function install(Request $request) {

        $host     = Email::getHost();
        $quota    = $request->get('quota');
        $quota    = empty($quota) ? 'unlimited' : $quota;
        $emails   = $request->get('emails');
        $emails   = explode(',', $emails);
        $passwordFormat = $request->get('password');

        $dataInstall = [];
        foreach ($emails as $emailName) {

            $emailName = trim(strtolower($emailName));
            $emailName = removeAccents($emailName);
            $emailName = str_replace(' ', '.', $emailName);

            $email = $emailName.'@'.$host;

            if($passwordFormat == 'format1') {
                $password = $emailName.'#'.strtoupper(config('app.source'));
            } elseif($passwordFormat == 'format2') {
                $password = $emailName.'#'.date('Y');
            } else {
                $password = randomPassword(8);
            }


            $dataInstall[] = [
                'email'    => $email,
                'password' => $password,
                'quota'    => $quota
            ];
        }

        foreach ($dataInstall as $row) {
            try {
                $emailAccount = new Email();
                $emailAccount->email    = $row['email'];
                $emailAccount->password = $row['password'];
                $emailAccount->quota    = $row['quota'];
                $emailAccount->source   = config('app.source');
                $emailAccount->created_by = Auth::user()->id;
                $emailAccount->save();

                $emailAccount->addEmail($row);

            } catch (\Exception $e) {
                dd($e->getMessage());
                @$emailAccount->forceDelete();
            }
        }

        $emailAccounts = Email::filterSource()->get();

        $response = [
            'result'   => true,
            'feedback' => 'E-mails criados com sucesso.',
            'html'     => view('admin.cpanel.emails.partials.list_accounts', compact('emailAccounts'))->render()
        ];

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function forwardersEdit(Request $request, $id) {

        $emailAccount = Email::filterSource()->find($id);
        $forwarders = $emailAccount->getForwarders();

        $listForwarders = [];
        foreach ($forwarders as $forwarder) {
            if($forwarder['dest'] == $emailAccount->email) {
                $listForwarders[] = $forwarder['forward'];
            }
        }

        return view('admin.cpanel.emails.forwarders', compact('emailAccount', 'listForwarders'))->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function forwardersStore(Request $request, $id) {

        $emailAccount  = Email::filterSource()->find($id);

        $addEmails     = $request->get('add');
        $addEmails     = explode(',', $addEmails);
        $addEmails     = array_filter($addEmails);
        $addEmails     = array_unique($addEmails);

        try {
            foreach ($addEmails as $email) {
                $emailAccount->addForwarder($emailAccount->email, $email);
            }
        } catch (\Exception $e) {
            $response = [
                'result'   => false,
                'feedback' => 'Erro ao redirecionar para '.$email.': ' . $e->getMessage(),
            ];

            return response()->json($response);
        }


        $removeEmails  = $request->get('remove');
        $removeEmails  = explode(',', $removeEmails);
        $removeEmails  = array_filter($removeEmails);
        $removeEmails  = array_unique($removeEmails);

        try {
            foreach ($removeEmails as $email) {
                $emailAccount->deleteForwarder($emailAccount->email, $email);
            }
        } catch (\Exception $e) {
            $response = [
                'result'   => false,
                'feedback' => 'Erro ao eliminar redirecionamento para '.$email.': ' . $e->getMessage(),
            ];

            return response()->json($response);
        }

        //atualiza lista
        $forwarders = $emailAccount->getForwarders();
        $forwardingActive = false;
        foreach ($forwarders as $forwarder) {
            if($forwarder['dest'] == $emailAccount->email) {
                $forwardingActive = true;
            }
        }
        $emailAccount->forwarding_active = $forwardingActive;
        $emailAccount->save();


        $response = [
            'result'   => true,
            'feedback' => 'Alterações gravadas com sucesso.',
        ];

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function autorespondersEdit(Request $request, $id) {

        $emailAccount = Email::filterSource()->find($id);

        $params = [
            'email' => $emailAccount->email
        ];

        $autoresponder = $emailAccount->getAutoResponse($params);

        $start = @$autoresponder['start'];
        $stop  = @$autoresponder['stop'];

        if(!empty($start)) {
            @$autoresponder['start'] = date('Y-m-d', $start);
            @$autoresponder['start_hour'] = date('H:i', $start);
        }

        if(!empty($stop)) {
            @$autoresponder['stop'] = date('Y-m-d', $stop);
            @$autoresponder['stop_hour'] = date('H:i', $stop);
        }

        return view('admin.cpanel.emails.autoresponders', compact('emailAccount', 'autoresponder'))->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function autorespondersStore(Request $request, $id) {

        $emailAccount  = Email::filterSource()->find($id);

        $domain = explode('@', $emailAccount->email);
        $email  = $domain[0];
        $domain = $domain[1];


        $start = strtotime($request->get('start'). ' ' .$request->get('start_hour').':00');
        $stop  = $request->get('stop');
        if(!empty($stop)) {
            $stop = $request->get('stop');
            if($request->get('stop_hour')) {
                $hour = $request->get('stop_hour').':59';
            }

            $stop = strtotime($stop.' ' . $hour);
        }

        try {

            if($request->get('body')) {
                $params = [
                    'domain'   => $domain,
                    'email'    => $email,
                    'from'     => $emailAccount->email,
                    'subject'  => $request->get('subject'),
                    'body'     => $request->get('body'),
                    'interval' => $request->get('interval'),
                    'is_html'  => $request->get('is_html', false),
                    'start'    => $start,
                    'stop'     => $stop,
                ];

                $emailAccount->addAutoResponse($params);
                $emailAccount->autoresponder_active = true;
                $emailAccount->save();
            } else {

                $params = [
                    'email'  => $emailAccount->email,
                ];

                $emailAccount->deleteAutoResponse($params);
                $emailAccount->autoresponder_active = false;
                $emailAccount->save();
            }


            $response = [
                'result'   => true,
                'feedback' => 'Alterações gravadas com sucesso.',
            ];

        } catch (\Exception $e) {
            $response = [
                'result'   => false,
                'feedback' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }
}
