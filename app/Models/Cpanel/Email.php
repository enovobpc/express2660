<?php

namespace App\Models\Cpanel;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;

class Email extends \App\Models\Cpanel\Base {


    use SoftDeletes,
        FileTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cpanel_emails';

    /**
     * @var string
     */
    private $module = 'Email';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'quota', 'usage', 'login_suspended', 'incoming_suspended',
        'outgoing_suspended', 'forwarding_active', 'autoresponder_active'
    ];

    /**
     * Filter source
     * @param $query
     * @return mixed
     */
    public function scopeFilterSource($query) {
        return $query->where('source', config('app.source'));
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
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = trim($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = trim($value);
    }

    public function setQuotaAttribute($value)
    {
        $this->attributes['quota'] = empty($value) ? null : $value;
    }

    /*
     |--------------------------------------------------------------------------
     | API FUNCTIONS
     |--------------------------------------------------------------------------
     */

    /**
     * List all email accounts with disk usage
     * https://api.docs.cpanel.net/openapi/cpanel/operation/list_pops_with_disk/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function listEmails()
    {
        $response = $this->execute($this->module, 'list_pops_with_disk');
        return $response;
    }

    /**
     * Return cPanel account's email account total
     * https://api.docs.cpanel.net/openapi/cpanel/operation/count_pops/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getMaxEmails()
    {
        $response = $this->execute($this->module, 'count_pops');
        return $response;
    }

    /**
     * Get Disk Usage
     * https://api.docs.cpanel.net/openapi/cpanel/operation/get_disk_usage/
     *
     * @param $user
     * @param $domain
     * @return mixed
     * @throws \Exception
     */
    public function getDiskUsage($params)
    {
        $response = $this->execute($this->module, 'get_disk_usage', $params);
        return $response;
    }

    /**
     * Return email account's client settings
     * https://api.docs.cpanel.net/openapi/cpanel/operation/get_client_settings/
     *
     * @param $user
     * @param $domain
     * @return mixed
     * @throws \Exception
     */
    public function getSmtpSettings($account)
    {
        $params = [
            'account' => $account
        ];

        $response = $this->execute($this->module, 'get_client_settings', $params);
        return $response;
    }

    /**
     * Add new email account
     * https://api.docs.cpanel.net/openapi/cpanel/operation/add_pop/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function addEmail($params)
    {
        $response = $this->execute($this->module, 'add_pop', $params);
        return $response;
    }

    /**
     * Update e-mail quota
     * https://api.docs.cpanel.net/openapi/cpanel/operation/edit_pop_quota/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function updateQuota($params)
    {
        $response = $this->execute($this->module, 'edit_pop_quota', $params);
        return $response;
    }

    /**
     * Update e-mail password
     * https://api.docs.cpanel.net/openapi/cpanel/operation/passwd_pop/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function updatePassword ($params)
    {
        $response = $this->execute($this->module, 'passwd_pop', $params);
        return $response;
    }

    /**
     * Suspend email account login
     * https://api.docs.cpanel.net/openapi/cpanel/operation/suspend_login/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function suspendLogin ($email, $suspend = true)
    {
        $params = [
            'email' => $email
        ];

        if($suspend) {
            $response = $this->execute($this->module, 'suspend_login', $params);
        } else {
            $response = $this->execute($this->module, 'unsuspend_login', $params);
        }

        return $response;
    }

    /**
     * Suspend email incoming
     * https://api.docs.cpanel.net/openapi/cpanel/operation/suspend_incoming/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function suspendIncoming ($email, $suspend = true)
    {
        $params = [
            'email' => $email
        ];

        if($suspend) {
            $response = $this->execute($this->module, 'suspend_incoming', $params);
        } else {
            $response = $this->execute($this->module, 'unsuspend_incoming', $params);
        }

        return $response;
    }

    /**
     * Suspend email outgoing
     * https://api.docs.cpanel.net/openapi/cpanel/operation/suspend_outgoing/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function suspendOutgoing ($email, $suspend = true)
    {
        $params = [
            'email' => $email
        ];

        if($suspend) {
            $response = $this->execute($this->module, 'suspend_outgoing', $params);
        } else {
            $response = $this->execute($this->module, 'unsuspend_outgoing', $params);
        }

        return $response;
    }

    /**
     * Delete email address
     * https://api.docs.cpanel.net/openapi/cpanel/operation/delete_pop/
     *
     * @param $user
     * @param $domain
     * @return mixed
     * @throws \Exception
     */
    public function deleteEmail ($params)
    {
        $response = $this->execute($this->module, 'delete_pop', $params);
        return $response;
    }

    /**
     * Add email autoresponder
     * https://api.docs.cpanel.net/openapi/cpanel/operation/add_auto_responder/
     *
     * @param $user
     * @param $domain
     * @return mixed
     * @throws \Exception
     */
    public function addAutoResponse ($params)
    {
        $response = $this->execute($this->module, 'add_auto_responder', $params);
        return $response;
    }

    /**
     * Get email autoresponder
     * https://api.docs.cpanel.net/openapi/cpanel/operation/get_auto_responder/
     *
     * @param $user
     * @param $domain
     * @return mixed
     * @throws \Exception
     */
    public function getAutoResponse ($params)
    {
        $response = $this->execute($this->module, 'get_auto_responder', $params);
        return $response;
    }

    /**
     * Add email autoresponder
     * https://api.docs.cpanel.net/openapi/cpanel/operation/delete_auto_responder/
     *
     * @param $user
     * @param $domain
     * @return mixed
     * @throws \Exception
     */
    public function deleteAutoResponse ($params)
    {
        $response = $this->execute($this->module, 'delete_auto_responder', $params);
        return $response;
    }

    /**
     * Get e-mail forwarder
     * https://api.docs.cpanel.net/openapi/cpanel/operation/list_forwarders/
     *
     * @param $user
     * @param $domain
     * @return mixed
     * @throws \Exception
     */
    public function getForwarders()
    {

        $domain = request()->getHttpHost();
        $domain = env('APP_ENV') == 'local' ? 'quickbox.pt' : $domain;

        $params = [
            'domain' => $domain
        ];

        $response = $this->execute($this->module, 'list_forwarders', $params);
        return $response;
    }

    /**
     * Add email forwarder
     * https://api.docs.cpanel.net/openapi/cpanel/operation/add_forwarder/
     *
     * @param $user
     * @param $domain
     * @return mixed
     * @throws \Exception
     */
    public function addForwarder ($email, $forwarder)
    {
        $domain = explode('@', $email);
        $domain = $domain[1];

        $params = [
            'domain'   => $domain,
            'email'    => $email,
            'fwdemail' => $forwarder,
            'fwdopt'   => 'fwd'
        ];

        $response = $this->execute($this->module, 'add_forwarder', $params);
        return $response;
    }

    /**
     * Add email forwarder
     * https://api.docs.cpanel.net/openapi/cpanel/operation/delete_forwarder/
     *
     * @param $user
     * @param $domain
     * @return mixed
     * @throws \Exception
     */
    public function deleteForwarder ($email, $forwarder)
    {
        $params = [
            'address'   => $email,
            'forwarder' => $forwarder
        ];

        $response = $this->execute($this->module, 'delete_forwarder', $params);
        return $response;
    }

    /**
     * Create Webmail session
     * https://api.docs.cpanel.net/openapi/cpanel/operation/create_webmail_session_for_mail_user/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function remoteLoginUrl($email)
    {
        $email = explode('@', $email);

        $params = [
            'domain' => $email[1],
            'login'  => $email[0]
        ];

        $response = $this->execute('Session', 'create_webmail_session_for_mail_user', $params);

        $host = request()->getHttpHost();

        if(env('APP_ENV') == 'local') {
            $host = 'quickbox.pt';
        }

        $response = [
            'url'  => 'https://'.$host.':2096'.@$response['token'].'/login',
            'session' => $response['session']
        ];

        return $response;
    }

    public static function getHost() {

        if(env('APP_ENV') == 'local') {
            return 'quickbox.pt';
        }

        $host = request()->getHttpHost();

        $myhost = strtolower(trim($host));
        $count = substr_count($myhost, '.');
        if($count === 2){
            if(strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
        } else if($count > 2){
            $myhost = get_domain(explode('.', $myhost, 2)[1]);
        }

        return $myhost;
    }
}