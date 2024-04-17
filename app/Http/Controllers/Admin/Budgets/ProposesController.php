<?php

namespace App\Http\Controllers\Admin\Budgets;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Budget;
use Html, Croppa, Auth, App, Mail, Setting, File;

class ProposesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'budgets';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',budgets']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($budgetId) {

        $budget = Budget\Budget::filterAgencies()
                            ->findOrFail($budgetId);

        $action = 'Novo pedido a fornecedor';

        $propose = new Budget\Propose();

        $formOptions = ['route' => array('admin.budgets.proposes.store', $budget->id), 'method' => 'POST'];

        $filename = storage_path() . '/budgets_contacts_list.json';

        $contacts = [];
        if(File::exists($filename)) {
            $contacts = json_decode(File::get($filename));
            $contacts = (array) $contacts;
        }

        $groupsList = [];
        foreach ($contacts as $group => $contact) {
            $groupsList[$group] = $group;
        }

        return view('admin.budgets.budgets_email.modals.propose', compact('budget', 'propose','providers', 'action', 'formOptions', 'contacts', 'groupsList'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $budgetId) {
        return $this->update($request, $budgetId, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($budgetId, $email) {

        $proposeMessages = Budget\Propose::where('budget_id', $budgetId)
                                ->where('email', $email)
                                ->orderBy('id', 'desc')
                                ->get();

        Budget\Propose::where('budget_id', $budgetId)
            ->where('email', $email)
            ->update(['read' => true]);

        return view('admin.budgets.budgets_email.modals.propose_detail', compact('proposeMessages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $budgetId, $id) {

        $input = $request->all();

        $budget = Budget\Budget::findOrFail($budgetId);

        $emails = validateNotificationEmails($input['to']);
        $emails = $emails['valid'];

        foreach ($emails as $providerEmail) {

            $propose = new Budget\Propose();

            if ($propose->validate($input)) {
                $propose->fill($input);
                $propose->email     = $providerEmail;
                $propose->budget_id = $budgetId;
                $propose->from_name = Auth::user()->name;
                $propose->from      = Setting::get('budgets_mail');
                $propose->read      = 1;
                $propose->save();


                //last message
                $lastPropose = Budget\Propose::where('budget_id', $budgetId)
                            ->where('id', '<>', $propose->id)
                            ->where('email', $providerEmail)
                            ->orderBy('id', 'desc')
                            ->first();

                if($lastPropose) {
                    $lastMessageHtml = "<blockquote style='border-left: 5px solid #ddd; padding-left: 15px'>";
                    $lastMessageHtml.= 'Às '.$lastPropose->created_at->format('H:i').' de '.$lastPropose->created_at->format('d-m-Y').', '.$lastPropose->from_name.' escreveu:<br/><br/>';
                    $lastMessageHtml.= $lastPropose->message;

                    $propose->message = $propose->message . '<br/>' . $lastMessageHtml . '</blockquote>';
                }

                $budget->provider_status = Budget\Budget::STATUS_PROVIDER_REQUESTED;
                $budget->save();

                try {
                    $data = $propose->toArray();
                    $data['subject'] = '[PBR-'.$budget->budget_no.'] ' . $data['subject'];

                    Mail::send('emails.budgets.budget', compact('data'), function ($message) use($data, $providerEmail) {
                        $message->to($providerEmail)
                            ->from(Setting::get('budgets_mail'), config('mail.from.name'))
                            ->subject($data['subject']);
                    });

                } catch (\Exception $e) {
                    return Redirect::back()->with('error', 'Erro ao enviar e-mail. ' . $e->getMessage());
                }
            } else {
                return Redirect::back()->withInput()->with('error', $propose->errors()->first());
            }
        }

        return Redirect::back()->with('success', 'Mensagem enviada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($budgetId, $email) {

        $result = Budget\Propose::where('budget_id', $budgetId)
                        ->whereEmail($email)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a proposta.');
        }

        return Redirect::back()->with('success', 'Proposta removida com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable($budgetId) {

        $data = Budget\Propose::with('provider')
            ->where('budget_id', $budgetId)
            ->whereRaw('(email, id) in (
                          select email, max(id) 
                          from budgets_proposes
                          where budget_id = '.$budgetId.'
                          group by email)')
            ->select();

        return Datatables::of($data)
                ->edit_column('to', function($row) {
                    return view('admin.budgets.budgets_email.datatables.proposes.provider', compact('row'))->render();
                })
                ->edit_column('subject', function($row) {
                    return view('admin.budgets.budgets_email.datatables.proposes.subject', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.budgets.budgets_email.datatables.proposes.actions', compact('row', 'myAgencies'))->render();
                })
                ->make(true);
    }

    /**
     * Download attachment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attachment($id, $nameSlug) {

        $budget = Budget\Propose::findOrfail($id);

        $data = null;
        $filename = null;
        foreach ($budget->attachments as $attachment) {
            $filename = $attachment->name;

            $filenameSlug = str_slug($attachment->name);
            if($filenameSlug == $nameSlug) {
                $contentType = $attachment->content_type;
                $data = $attachment->content;
            }
        }

        header('content-type:' . $contentType);
        echo base64_decode($data);
    }
}
