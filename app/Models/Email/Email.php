<?php

namespace App\Models\Email;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Setting;

class Email extends \App\Models\BaseModel
{
    use SoftDeletes;
    
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logs';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_email';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sended_emails';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'source', 'subject', 'from', 'to', 'cc', 'bcc', 'message', 'message_id', 'is_draft',
        'modal_title', 'attached_docs', 'attached_files'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'subject' => 'required',
        'from'    => 'required',
    );

    /**
     * Create temporary filepath
     *
     * @param [type] $fileContent
     * @return void
     */
    public static function createTempAttachment($fileContent) {
        $datetime       = time();
        $tmpFilename    = '/uploads/tmp_files/tmp_attachment-'.$datetime.'.pdf';
        file_put_contents(public_path($tmpFilename), $fileContent);
        return $tmpFilename;
    }

    /**
     * Get email HTML signature
     *
     * @param string $locale
     * @return string
     */
    public static function getSignature($locale = 'pt') {

        $user = Auth::user();

        if(Setting::get('email_signature') || @$user->settings['email_signature']) {
            $html = @$user->settings['email_signature'] ? @$user->settings['email_signature'] : Setting::get('email_signature');
            $html = str_replace(':name', $user->name, $html);
            $html = str_replace(':email', $user->professional_email ?? $user->email, $html);
            $html = str_replace(':phone', $user->professional_mobile, $html);;
        } else {
            $html = '<p>' . transLocale('admin/email.cargo-instructions.regards', $locale);
            $html.= '<br/>' . $user->name;
            $html.= '<br/><i>'. $user->email . '</i></p>';
        }
        
        return $html;
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
        return $this->belongsTo('App\Models\User', 'sended_by');
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
    public function setSendedByAttribute($value)
    {
        $this->attributes['sended_by'] = empty($value) ? null : $value;
    }

    public function setFromAttribute($value)
    {
        $this->attributes['from'] = empty($value) ? null : $value;
    }

    public function setToAttribute($value)
    {
        $this->attributes['to'] = empty($value) ? null : $value;
    }

    public function setCcAttribute($value)
    {
        $this->attributes['cc'] = empty($value) ? null : $value;
    }

    public function setBccAttribute($value)
    {
        $this->attributes['bcc'] = empty($value) ? null : $value;
    }

    public function setAttachedDocsAttribute($value)
    {
        $this->attributes['attached_docs'] = empty($value) ? null : json_encode($value);
    }

    public function setAttachedFilesAttribute($value)
    {
        $this->attributes['attached_files'] = empty($value) ? null : json_encode($value);
    }

    public function getAttachedDocsAttribute($value)
    {
        return json_decode($value);
    }

    public function getAttachedFilesAttribute($value)
    {
        return json_decode($value);
    }
}
