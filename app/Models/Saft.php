<?php

namespace App\Models;

use App\Models\Billing\ApiKey;
use App\Models\InvoiceGateway\Base;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Date\Date;
use Auth, Mail, Setting;

class Saft extends BaseModel
{

    use SoftDeletes;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'safts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'year', 'month', 'created_by', 'issued'
    ];


    /**
     * Download SAFT file
     * @param $year
     * @param $month
     * @return mixed
     */
    public static function download($year, $month, $companyId, $returnFile = false){


        if($year > date('Y')) {
            throw new \Exception('Ano inválido ('. $year.')');
        }

        if($year == date('Y') && $month > date('m')) {
            throw new \Exception('Não pode emitir o ficheiro SAF-T para o mês e ano indicado');
        }

        $class = Base::getNamespaceTo('Document');
        $gateway = new $class(ApiKey::getDefaultKey($companyId));
        $file = $gateway->getSaftFile($year, $month);

        $saft = self::firstOrCreate([
            'source' => config('app.source'),
            'year'   => $year,
            'month'  => $month
        ]);

        $saft->source = config('app.source');
        $saft->month  = $month;
        $saft->year   = $year;
        $saft->company_id = $companyId;
        $saft->created_by = empty($saft->created_by) ? Auth::user()->id : $saft->created_by;
        $saft->issued = 1;
        $saft->save();

        if($returnFile) {
            $saft->file = $file;
            return $saft;
        }

        header("Content-Type: application/zip");
        header('Content-Disposition: inline; filename="SAFT_'.trans('datetime.month.'.$saft->month).'_'.$saft->year.'.zip"');
        echo base64_decode($file);
        exit;
    }

    /**
     * Send saft email
     * @param $year
     * @param $month
     * @param $input
     * @throws \Exception
     */
    public static function sendMail($year, $month, $companyId, $input) {

        $company = Company::filterSource()->find($companyId);

        $saft = Saft::download($year, $month, $companyId, true);
        $file = $saft->file;

        if($file) {
            $emails = validateNotificationEmails($input['email']);

            if (!empty($input['email']) && !empty($emails['error'])) {
                return [
                    'result'   => true,
                    'feedback' => 'Ficheiro emitido com sucesso. Não foi possível enviar e-mail.'
                ];
            }

            if (!empty($emails['valid'])) {

                Mail::send('emails.billing.saft', compact('saft', 'company'), function ($message) use ($input, $emails, $file, $saft) {

                    $message->subject('Envio ficheiro SAF-T ('.trans('datetime.month.'.$saft->month).' '.$saft->year.')');
                    $message->to($emails['valid']);

                    if($input['email_cc']){
                        $message->cc(Auth::user()->email);
                    }

                    //attach saft file
                    if ($file) {
                        $filedata = base64_decode($file);
                        $filename = 'SAFT_'.trans('datetime.month.'.$saft->month).'_'.$saft->year.'.zip';
                        $message->attachData($filedata, $filename, ['mime' => 'application/zip']);
                    }
                });
            }

            return [
                'result'   => true,
                'feedback' => 'E-mail enviado com sucesso.'
            ];
        }

        return [
            'result'   => false,
            'feedback' => 'Falha no download do ficheiro'
        ];
    }

    /**
     * Download SAFT file
     * @param $year
     * @param $month
     * @return mixed
     */
    public static function sendNotification()
    {
        $today    = Date::today();
        $curMonth = $today->month;
        $curYear  = $today->year;

        $saftDay = Setting::get('saft_day') ? Setting::get('saft_day') : \App\Models\Core\Setting::get('saft_day');
        $saftDeadline = new Date($curYear . '-' . $curMonth . '-' . $saftDay);
        $lastMonth = $saftDeadline->copy()->subMonth(1);
        $daysLeft = $saftDeadline->diffInDays($today);

        $saftLimitDays = [
            $saftDeadline->format('Y-m-d'),
            $saftDeadline->subDays(1)->format('Y-m-d'),
            $saftDeadline->subDays(1)->format('Y-m-d'),
            $saftDeadline->subDays(3)->format('Y-m-d'),
        ];

        if (in_array($today->format('Y-m-d'), $saftLimitDays)) {
            $saft = Saft::where('source', config('app.source'))
                ->where('month', $lastMonth->month)
                ->where('year', $lastMonth->year)
                ->where('issued', 0)
                ->first();

            if ($saft) {


                $title = 'Envio do ficheiro SAF-T dentro de '. $daysLeft.' dias.';
                $subtitle = 'O prazo termina em <b>'.$saftDay.' de '.trans('datetime.month.'.$today->month).'</b>. Pretende emitir e enviar o ficheiro para a contabilidade?&nbsp;&nbsp;&nbsp;&nbsp;';
                if($daysLeft == 0) {
                    $title    = '<b>ATENÇÃO! Hoje é o último dia para envio do ficheiro SAF-T</b>';
                    $subtitle = 'O prazo termina <b>hoje</b>. Pretende emitir e enviar o ficheiro para a contabilidade?&nbsp;&nbsp;&nbsp;&nbsp;';
                }

                $subtitle.= '<a href="'.route('admin.invoices.saft.email', [$lastMonth->year, $lastMonth->month]).'" data-toggle="modal" data-target="#modal-remote-xs" class="btn btn-xs btn-primary"><i class="fas fa-envelope"></i> Enviar para a contabilidade</a>';
                $subtitle.= '<a href="'.route('admin.invoices.saft.download', [$lastMonth->year, $lastMonth->month]).'" class="btn btn-xs btn-primary m-l-10"><i class="fas fa-download"></i> Download SAF-T</a>';

                $content = '<p>';
                $content.= 'O prazo para envio do ficheiro SAF-T termina em <b>'.$saftDay.' de '.trans('datetime.month.'.$today->month).'</b>';
                $content.='<br/>Através do Software é possível o download do ficheiro SAF-T e consequentemente o seu envio na hora para a contabilidade.';
                $content.= '</p>';
                $content.= '<a href="'.route('admin.invoices.saft.email', [$lastMonth->year, $lastMonth->month]).'" data-toggle="modal" data-target="#modal-remote" class="btn btn-xs btn-primary"><i class="fas fa-envelope"></i> Enviar para a contabilidade</a>';
                $content.= '<a href="'.route('admin.invoices.saft.download', [$lastMonth->year, $lastMonth->month]).'" class="btn btn-xs btn-primary m-l-10"><i class="fas fa-download"></i> Download SAF-T</a>';

                //get notification recipients
                $recipients = \App\Models\User::where('source', config('app.source'))
                    ->whereHas('roles.perms', function ($query) {
                        $query->whereIn('name', ['invoices']);
                    })
                    ->get();

                if (!$recipients->isEmpty()) {
                    $notice = new Notice();
                    $notice->title = $title;
                    $notice->summary = $subtitle;
                    $notice->content = $content;
                    $notice->date = date('Y-m-d');
                    $notice->sources = [config('app.source')];
                    $notice->published = 1;
                    $notice->auto = 1;
                    $notice->save();

                    $recipientsIds = $recipients->pluck('id')->toArray();
                    $notice->users()->sync($recipientsIds);

                    foreach ($recipients as $recipient) {
                        $recipient->count_notices = $recipient->count_notices + 1;
                        $recipient->save();
                    }
                }
            }
        }

    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /*
     |--------------------------------------------------------------------------
     | Accessors & Mutators
     |--------------------------------------------------------------------------
     |
     | Eloquent provides a convenient way to transform your model attributes when
     | getting or setting them. Simply define a 'getFooAttribute' method on your model
     | to declare an accessor. Keep in mind that the methods should follow camel-casing,
     | even though your database columns are snake-case.
     |
     */
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function getNameAttribute()
    {
        if($this->attributes['doc_type']) {
            return trans('admin/billing.types_code.' . $this->attributes['doc_type']) . ' ' . $this->attributes['doc_id'];
        }

        return $this->attributes['doc_id'];
    }

}
