<?php

namespace App\Http\Controllers\Admin\Budgets;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Budget;
use Html, Croppa, Auth, App, Mail, Setting;

class MessagesController extends \App\Http\Controllers\Admin\Controller {

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

        $budget = Budget\Budget::filterAgencies()->findOrFail($budgetId);

        return view('admin.budgets.budgets_email.modals.message', compact('budget'))->render();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $budgetId, $id) {

        $input = $request->all();
        $input['message'] = $input['message'];

        $budget = Budget\Budget::filterAgencies()->findOrFail($budgetId);

        if(empty($budget->user_id)) { //auto assign budget
            $input['user_id'] = Auth::user()->id;
        }

        $message = Budget\Message::where('budget_id', $budget->id)->findOrNew($id);

        if ($message->validate($input)) {
            $message->fill($input);
            $message->budget_id = $budgetId;
            $message->subject   = $message->getSubject($input['subject'], $budget->budget_no);
            $message->from      = Setting::get('budgets_mail');
            $message->from_name = Auth::user()->name;
            $message->to_name   = $budget->name;
            $message->save();

            //last message
            $lastMessage = Budget\Message::where('budget_id', $budgetId)
                ->where('id', '<>', $message->id)
                ->orderBy('id', 'desc')
                ->first();

            if(!$lastMessage) {
                $lastMessage = new Budget\Message();
                $lastMessage->created_at = $budget->created_at;
                $lastMessage->message    = $budget->message;
                $lastMessage->from_name  = $budget->name;
            }

            if($lastMessage) {
                $message->subject = 'Re: ' . $message->subject;
                $lastMessageHtml = "<blockquote style='border-left: 5px solid #ddd; padding-left: 15px'>";
                $lastMessageHtml.= 'Às '.@$lastMessage->created_at->format('H:i').' de '.@$lastMessage->created_at->format('d-m-Y').', '.@$lastMessage->from_name.' escreveu:<br/><br/>';
                $lastMessageHtml.= $lastMessage->message;

                $message->message = $message->message . '<br/>' . $lastMessageHtml . '</blockquote>';
            }

            $budget->status = Budget\Budget::STATUS_WAINTING_CUSTOMER;
            $budget->save();

            try {
                $data = $message->toArray();

                $emails = validateNotificationEmails($data['to']);
                $emails = $emails['valid'];

                Mail::send('emails.budgets.budget', compact('data'), function ($message) use($data, $emails) {
                    $message->to($emails)
                        ->from(Setting::get('budgets_mail'), config('mail.from.name'))
                        ->subject($data['subject']);
                });

            } catch (\Exception $e) {
                Budget\Message::where('budget_id', $budget->id)->whereId($message->id)->forceDelete();
                return Redirect::back()->with('error', 'Erro ao enviar e-mail. ' . $e->getMessage());
            }

            return Redirect::back()->with('success', 'Mensagem enviada com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $message->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($budgetId, $id) {

        $result = Budget\Message::filterAgencies()
                        ->where('budget_id', $budgetId)
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a mensagem.');
        }

        return Redirect::back()->with('success', 'Mensagem removida com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable($budgetId) {

        $data = Budget\Message::where('budget_id', $budgetId)
                    ->select();

        return Datatables::of($data)
                ->edit_column('id', function($row) {
                    return view('admin.budgets.budgets_email.datatables.messages.from', compact('row'))->render();
                })
                ->edit_column('subject', function($row) {
                    return view('admin.budgets.budgets_email.datatables.messages.subject', compact('row'))->render();
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

        $budget = Budget\Message::findOrfail($id);

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
