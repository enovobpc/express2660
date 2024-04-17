<?php
namespace App\Models\Sms;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Setting;

class Sms extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_sms';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sms_logs';

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $gateway;

    /**
     * @var string
     */
    public $token;

    /**
     * @var
     */
    public $user;
    public $password;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gateway', 'from', 'to', 'message', 'status', 'status_code', 'status', 'sms_id', 'sms_parts',
        'source_type', 'source_id', 'success', 'created_by', 'token'
    ];

    /**
     * Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($token = null, $gateway = null)
    {
        if(empty($token)) {
            $token = config('app.sms_token');
        }

        if(empty($gateway)) {
            $gateway = config('app.sms_gateway');
        }

        if($gateway == 'smsapi' || empty($gateway)) {
            if (config('app.env') == 'local') {
                $token = '1wh91UNCnNNaj9hvamaUb4dbh7BIo8UISoKMG6NX';
                $gateway = 'SmsApi';
            } elseif (empty($token)) {
                $token = '1wh91UNCnNNaj9hvamaUb4dbh7BIo8UISoKMG6NX';
                $gateway = 'SmsApi';
            }


            if(empty($token)) {
                throw new \Exception('Não está configurada nenhuma chave da API para emissão de SMS');
            }
        }

        $this->user     = env('SMS_USERNAME');
        $this->password = env('SMS_PASSWORD');


        if(empty($gateway)) {
            $gateway = 'SmsApi';
        }

        $this->token   = $token;
        $this->gateway = $gateway;
        $this->source  = config('app.source');
    }

    /**
     * Count SMS message parts
     * @param $smsText
     */
    public static function countSmsParts($message) {

        $smsParts = strlen($message);
        $smsParts = ceil($smsParts / 160);

        return $smsParts;
    }

    /**
     * Execute a soap request
     *
     * @param $nif
     * @return mixed
     * @throws \Exception
     */
    public function send($params = null, $sourceId = null, $sourceType = null)
    {
       /* try {*/

            Pack::flushCache(Pack::CACHE_TAG);

            $class = 'App\Models\Sms\\' . $this->gateway;

            if(!empty($this->message)) {
                $params = $this->toArray();
            }

            if($sourceId) {
                $params['source_id'] = $sourceId;
            }

            if($sourceType) {
                $params['source_type'] = $sourceType;
            }

            $mobiles = $this->normalizeMobiles($params['to']);

            if(empty($mobiles['valid'])) {
                throw new \Exception('Os telefones indicados estão errados.');
            } elseif(!empty($mobiles['error'])) {
                throw new \Exception('Um ou mais telefones estão incorretos. Verifique se indicou o indicativo do país.');
            }

            $params['to'] = implode(',', $mobiles['valid']);
            $params['message'] = removeAccents($params['message']);

            $smsParts = Sms::countSmsParts($params['message']);
            $availableSms = Pack::countAvailableSms();

            if($availableSms >= $smsParts) {

                $gateway  = new $class($this->token);
                $response = $gateway->sendSMS($params);

                $smsLog = new Sms();
                $smsLog->source = config('app.source');
                $smsLog->fill($response);
                $smsLog->source_id   = @$params['source_id'];
                $smsLog->source_type = @$params['source_type'];
                $smsLog->created_by  = Auth::check() ? Auth::user()->id : null;
                $smsLog->save();

                //update sms pack
               /* $smsPack->remaining_sms = $smsPack->remaining_sms - $smsLog->sms_parts;
                $smsPack->save();*/

                return $smsLog;
            }

            throw new \Exception('Não tem mensagens disponíveis para envio desta mensagem. Adquira um novo pacote de SMS.');

        /*} catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }*/
    }

    /**
     * Check mobiles and normalize it
     * @param $mobiles
     * @return array
     */
    public function normalizeMobiles($mobiles) {

        $to = explode(';', str_replace(' ', '', trim($mobiles)));

        $mobiles = [];
        foreach ($to as $item) {

            if(strlen($item) == 9 && in_array(substr($item, 0, 2), ['91','92','93','96'])) {
                $item = '+351'.$item;
                $mobiles['valid'][] = $item;
            }

            if(substr($item, 0, 1) != '+') {
                $mobiles['error'][] = $item;
            } else {
                $mobiles['valid'][] = $item;
            }
        }

        @$mobiles['valid'] ? $mobiles['valid'] = array_unique($mobiles['valid']) : '';
        @$mobiles['error'] ? $mobiles['error'] = array_unique($mobiles['error']) : '';

        return $mobiles;
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
        return $this->belongsTo('App\Models\User', 'created_by');
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

    public function setCreatedByAttribute($value)
    {
        $this->attributes['created_by'] = empty($value) ? null : $value;
    }

    public function setSourceTypeAttribute($value) {
        $this->attributes['source_type'] = empty($value) ? null : $value;
    }

    public function setSourceIdAttribute($value) {
        $this->attributes['source_id'] = empty($value) ? null : $value;
    }
}