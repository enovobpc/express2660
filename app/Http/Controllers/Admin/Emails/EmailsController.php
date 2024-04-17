<?php

namespace App\Http\Controllers\Admin\Emails;

use Illuminate\Http\Request;
use App\Models\Email\Email;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Mail, File, Log;

class EmailsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'emails';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',emails']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->setContent('admin.emails.emails.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $email = Email::filterSource()
                    ->find($id);

        $data = compact('email');

        return view('admin.emails.emails.show', $data)->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $email = new Email();

        $formOptions = array('route' => array('admin.emails.store'), 'method' => 'POST', 'files' => true);

        $data = compact(
            'email',
            'formOptions'
        );

        return view('admin.emails.emails.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, $request->get('id'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $action = 'Editar e-mail';

        $email = Email::filterSource()
            ->whereId($id)
            ->firstOrfail();

        $data = compact(
            'email',
            'action',
            'formOptions'
        );

        if(!$email->is_draft) {
            return view('admin.emails.emails.show', $data)->render();
        }

        return view('admin.emails.emails.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $attachedDocsUrl    = $request->get('docs_attached');
        $attachedDocsTitles = $request->get('docs_attached_title');

        //documentos anexados por defeito
        $attachedDocs = [];
        if($attachedDocsUrl) {
            foreach($attachedDocsUrl as $key => $url) {

                $title      = @$attachedDocsTitles[$key] ? @$attachedDocsTitles[$key] : 'Documento Anexado';
                $mime       = mime_content_type(public_path($url));
                $extension  = pathinfo($url, PATHINFO_EXTENSION);
                $attachedDocs[] = [
                    'mime'      => $mime ? $mime : 'application/pdf',
                    'name'      => $title.'.'.$extension,
                    'filepath'  => $url
                ];
            }
        }

        //STORE CUSTOM ATTACHMENTS
        $attachedFiles = [];
        if($request->hasFile('attachments')) {

            $files = $request->file('attachments');

            foreach ($files as $file) {
                $filecontent = base64_encode(file_get_contents($file->getRealPath()));

                $attachedFiles[] = [
                    'content' => $filecontent,
                    'name'    => $file->getClientOriginalName(),
                    'mime'    => $file->getClientMimeType()
                ];
            }
        }

        $input = $request->all();

        $email = Email::findOrNew($id);

        $exists = $email->exists;
        if ($email->validate($input)) {
            $email->fill($input);
            $email->source    = config('app.source');
            $email->sended_at = null;

            if(!empty($attachedDocs)) {
                $email->attached_docs = $attachedDocs;
            }

            if(!empty($attachedFiles)) {
                $email->attached_files = $attachedFiles;
            }

            $email->save();

            if($request->get('is_draft')) {
                return Redirect::back()->with('success', 'Rascunho gravado com sucesso.');
            }

            $emailsTo  = validateNotificationEmails($email->to);
            $emailsTo  = $emailsTo['valid'];
            $emailsCc  = validateNotificationEmails($email->cc);
            $emailsCc  = $emailsCc['valid'];
            $emailsBcc = validateNotificationEmails($email->bcc);
            $emailsBcc = $emailsBcc['valid'];


            if (!empty($emailsTo) || !empty($emailsCc) || !empty($emailsBcc)) {
    
                try {
                    Mail::send('emails.blank_email', compact('email'), function ($message) use ($email, $emailsTo, $emailsCc, $emailsBcc, &$messageId) {
             
                        $messageId = $message->getId();

                        if($emailsTo){
                            $message->to($emailsTo);
                        }

                        if($emailsCc){
                            $message->cc($emailsCc);
                        }

                        if($emailsBcc){
                            $message->bcc($emailsBcc);
                        }
                       
                
                        $message->subject($email->subject);
    
                        //attach summary file
                        if ($email->attached_docs) {
                            foreach($email->attached_docs as $attachment) {
                                $content = file_get_contents(public_path(@$attachment->filepath));
                                $message->attachData($content, @$attachment->name, ['mime' => @$attachment->mime]);
                            }
                        }

                        //attach uploaded files
                        if ($email->attached_files) {
                            foreach($email->attached_files as $attachment) {
                                $content = file_get_contents(public_path());
                                $message->attachData(base64_decode(@$attachment->content), @$attachment->name, ['mime' => @$attachment->mime]);
                            }
                        }
                    });

                    
                    if (count(Mail::failures()) > 0) {
                        $email->sended_at = null;
                        $email->save();
                    } else {

                        //procura o email com o msgId indicado para prevenir a duplicação
                        $sendedEmail = Email::where('message_id', $messageId)
                                ->orderBy('id', 'desc')
                                ->first();

                        $sendedEmail->update([
                            'attached_docs'  => $email->attached_docs,
                            'attached_files' => $email->attached_files
                        ]);

                        //apaga anexos 
                        foreach($email->attached_docs as $attachment) {
                            if(File::exists(public_path(@$attachment->filepath))){
                                File::delete(public_path(@$attachment->filepath));
                            }
                        }
                        $email->forceDelete(); //apaga o email inicialmente criado
                    }

                } catch (\Exception $e) {
                    Log::error('Erro ao enviar email: '. $e);
                }
            }
    
            return Redirect::back()->with('success', 'E-mail enviado com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $email->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $result = Email::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar eliminar o grupo.');
        }

        return Redirect::back()->with('success', 'Grupo eliminado com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        $ids = explode(',', $request->ids);

        $result = Email::filterSource()
            ->whereIn('id', $ids)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = Email::filterSource()
            ->select();

        return Datatables::of($data)
            ->edit_column('subject', function ($row) {
                return view('admin.emails.emails.datatables.subject', compact('row'))->render();
            })
            ->edit_column('from', function ($row) {
                return view('admin.emails.emails.datatables.from', compact('row'))->render();
            })
            ->edit_column('to', function ($row) {
                return view('admin.emails.emails.datatables.to', compact('row'))->render();
            })
            ->edit_column('sended_by', function ($row) {
                return view('admin.emails.emails.datatables.sended_by', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.emails.emails.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
