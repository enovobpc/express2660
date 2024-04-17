<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\AdminResetPassword as ResetPasswordNotification;
use App\Models\Equipment\Location;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Watson\Rememberable\Rememberable;
use Illuminate\Support\Facades\Redirect;
use App\Notifications\resetPassword;
use App\Models\Traits\FileTrait;
use Auth, Date, DB, Setting, Mail;

class User extends Authenticatable
{
    use Notifiable, Rememberable, FileTrait;

    use SoftDeletes, EntrustUserTrait {
        SoftDeletes::restore insteadof EntrustUserTrait;
        EntrustUserTrait::restore insteadof SoftDeletes;
    }

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_users';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'last_login'];

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'code_abbrv', 'name', 'email', 'password', 'uncrypted_password', 'active', 'agencies', 'source',
        'phone', 'mobile', 'obs', 'allowed_actions',
        'location_enabled', 'location_denied', 'location_lat', 'location_lng', 'location_last_update', 'vehicle',
        'provider_id', 'settings', 'comission_percent', 'is_operator', 'ss_allowance', 'christmas_allowance', 'holiday_allowance', 'locale',
        'salary_obs', 'salary_working_time_exemption', 'salary_expenses', 'salary_food_allowance', 'salary_value_hour',
        'salary_price', 'fiscal_dependents', 'fiscal_titularity', 'fiscal_deficiency', 'fiscal_address', 'fiscal_zip_code',
        'fiscal_city', 'fiscal_country', 'academic_degree', 'teaching_institution', 'course', 'course_avaliation',
        'bank_swift', 'bank_iban', 'bank_name', 'professional_obs', 'professional_chief_id', 'professional_role',
        'linkedin', 'twitter', 'facebook', 'vat', 'ss_card', 'id_card', 'emergency_mobile', 'emergency_phone',
        'emergency_kinship', 'emergency_name', 'professional_mobile', 'professional_phone', 'professional_email',
        'personal_mobile', 'personal_phone', 'personal_email', 'country', 'city', 'zip_code', 'address', 'about',
        'nacionality', 'civil_status', 'gender', 'birthdate', 'fullname', 'admission_date', 'resignation_date', 'agency_id',
        'login_app', 'login_admin', 'popup_notification', 'holidays_days', 'allowance_value_nacional', 'allowance_value_spain', 'allowance_value_internacional'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Set code
     * @param bool $save
     */
    public function setCode($save = true)
    {

        //$prefix      = 'M';
        $prefix        = ''; //digitos à esquerda
        $digitsLeft    = 0;
        $useEmptyCodes = false;

        if ($useEmptyCodes) {

            $allCodes = User::filterSource()
                ->whereNotNull('code')
                ->orderByRaw('CAST(REPLACE(code, "' . $prefix . '", "") as unsigned) asc')
                ->select([DB::raw('(REPLACE(code, "' . $prefix . '", "")) as code')])
                ->pluck('code')
                ->toArray();

            $allCodes      = array_values(array_filter(array_map('intval', $allCodes)));
            $possibleCodes = range(1, end($allCodes));

            $diff = array_diff($possibleCodes, $allCodes);

            if (empty($diff)) {
                $code = end($allCodes) + 1;
            } else {
                $code = @array_values($diff)[0];
            }
        } else {
            $lastCode = User::filterSource()
                ->whereNotNull('code')
                ->orderByRaw('CAST(REPLACE(code, "' . $prefix . '", "") as unsigned) desc')
                ->first([DB::raw('(REPLACE(code, "' . $prefix . '", "")) as code')]);

            $code = empty($lastCode->code) ? 0 : intval($lastCode->code) + 1;
        }

        $code = str_pad($code, $digitsLeft, '0', STR_PAD_LEFT);
        $code = $prefix . $code;

        if ($save) {
            $this->code = $code;
            $this->save();
        }

        return $code;
    }

    /**
     * Get user setting
     * @param $var
     * @param null $default
     * @return null
     */
    public function setting($var, $default = null)
    {
        $settings = $this->settings;
        return @$settings[$var] ? $settings[$var] : $default;
    }


    /**
     * Store user settings
     * @param $var
     * @param $value
     * @param bool $save
     * @return mixed
     */
    public function setSetting($var, $value, $save = true)
    {
        $settings = $this->settings;

        $this->settings = $settings[$var] = $value;

        if ($save) {
            $this->save();
        }

        return $settings;
    }


    /**
     * Store user settings from array
     * @param $var
     * @param $value
     * @param bool $save
     * @return mixed
     */
    public function setSettingArray($settingsArr, $save = true)
    {
        $settings = $this->settings;

        $hasChanges = false;
        foreach ($settingsArr as $key => $value) {
            if (@$settings[$key] != $value) {
                $hasChanges = true;
            }

            $settings[$key] = $value;
        }

        $this->settings = $settings;

        if ($save && $hasChanges) {
            $this->save();
        }

        return $settings;
    }


    /**
     * Return all notifications grouped by vehicle
     * @param null $vehicleId
     * @param array $params
     * @return array|null
     */
    public static function getNotifications($userId = null, $params = [])
    {

        try {
            $today     = Date::today();
            $startDate = @$params['start_date'] ? $params['start_date'] : $today->format('Y-m-d');
            $endDate   = @$params['end_date'] ? $params['end_date'] : Date::today()->addDays(30)->format('Y-m-d');
            $includeExpireds = isset($params['expireds']) ? @$params['expireds'] : false;

            //obtem aniversarios
            $birthdays = User::whereRaw('MONTH(birthdate) = "' . $today->month . '"')
                ->whereRaw('DAY(birthdate) = "' . $today->day . '"')
                ->get();

            $birthdays = $birthdays->groupBy('id')->toArray();

            //obtem cartoes a expirar
            $cards = UserCard::filterSource()
                ->where('notification_date', '<=',$today->format('Y-m-d'))
                ->get();
      
            $cards = $cards->groupBy('user_id')->toArray();

            //obtem férias ou ausencias agendadas
            $absences = UserAbsence::with('type')
                ->filterSource()
                ->where(function($q) use($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate]);
                })
                ->get();
      
            $absences = $absences->groupBy('user_id')->toArray();

            //obtem array com todos os utilizadores com alguma coisa para notificar
            //obtem da base de dados a lista dos utilizadores com notificacoes 
            $usersIds = [];
            $usersIds = array_merge($usersIds, array_keys($birthdays));
            $usersIds = array_merge($usersIds, array_keys($cards));
            $usersIds = array_merge($usersIds, array_keys($absences));
            $usersIds = empty($usersIds) ? [] : array_unique($usersIds);
           
            $users = User::whereIn('id', $usersIds)->get(['id', 'code', 'birthdate', 'name', 'fullname', 'email', 'agency_id']);
           
            $resultList = [];
            $startDate = new Date($startDate);
            $endDate   = new Date($endDate);
            $totalWarnings = 0;
            $totalExpireds = 0;

            foreach($users as $user) {

                $countWarnings = 0;
                $countExpireds = 0;
                $reminders = [];
                
                //alerta aniversários
                if(@$birthdays[$user->id]) {

                    $birthdate = new Date($user->birthdate);
                    $date      = new Date(date('Y') . '-' . $birthdate->month . '-' . $birthdate->day);
                    $diffDays  = $date->diffInDays($today);
                  
                    $reminders[] = [
                        'type'       => 'birthday',
                        'title'      => 'Aniversário',
                        'date'       => $date,
                        'days_alert' => 8,
                        'days_left'  => $diffDays,
                        'obs'        => '',
                        'status'     => 'warning'
                    ];
                }

                //alerta cartões
                if(@$cards[$user->id]) {

                    foreach(@$cards[$user->id] as $card) {
                     
                        $date = new Date($card['validity_date']);
                        $diffDays = $date->diffInDays($today);
                        if ($date->lt($today)) {
                            $diffDays = -1 * $diffDays;
                        }

                        if ($diffDays >= 0) {
                            $status = 'warning';
                            $countWarnings++;
                        } else {
                            $status = 'expired';
                            $countExpireds++;
                        }
                        
                        $reminders[] = [
                            'type'       => $card['type'],
                            'title'      => $card['name'],
                            'date'       => $date,
                            'days_alert' => $card['notification_days'],
                            'days_left'  => $diffDays,
                            'obs'        => '',
                            'status'     => $status
                        ];
                    }
                }

                //alerta férias ou faltas
                if(@$absences[$user->id]) {

                    foreach($absences[$user->id] as $absence) {
                     
                        $date = new Date($absence['start_date']);
                        $diffDays = $date->diffInDays($today);
                        if ($date->lt($today)) {
                            $diffDays = -1 * $diffDays;
                        }

                        if ($diffDays >= 0) {
                            $status = 'warning';
                            $countWarnings++;
                        } else {
                            $status = 'expired';
                            $countExpireds++;
                        }
                        
                        $reminders[] = [
                            'type'       => 'absence',
                            'title'      => @$absence['type']['name'],
                            'date'       => $date,
                            'days_alert' => $card['notification_days'],
                            'days_left'  => $diffDays,
                            'obs'        => '',
                            'status'     => $status
                        ];
                    }
                }

                $user->notifications         = $reminders;
                $user->notifications_warning = $countWarnings;
                $user->notifications_expired = $countExpireds;
                $totalWarnings += $countWarnings;
                $totalExpireds += $countExpireds;
                $resultList[] = $user;
            }

            if (@$params['return_totals']) {
                return [
                    'users'    => collect($resultList),
                    'warnings' => $totalWarnings,
                    'expireds' => $totalExpireds
                ];
            }

            if ($userId) {
                return @$resultList[0];
            }

            $resultList = collect($resultList);
            return $resultList;

        } catch(\Exception $e) {
           //dd($e->getMessage(). ' file '. $e->getFile(). ' line '. $e->getLine());
        }

    }

    /**
     * Return all notifications grouped by vehicle
     * @param null $userId
     * @param array $params
     * @return array|null
     */
    public static function sendNotificationsEmail($userId = null, $params = [], $emails = null, $usersCollection=null)
    {

        if($usersCollection) {
            $users = $usersCollection;
        } else {
            $users = self::getNotifications($userId, $params);
        }
        
        if ($users) {

            if (empty($emails)) {
                //obtem todos os destinatários para onde enviar o email
                $emails = \App\Models\User::where('source', config('app.source'))
                    ->whereHas('roles.perms', function ($query) {
                        $query->whereIn('name', ['users_cards', 'users_absences', 'users_profissional_info']);
                    })
                    ->pluck('email')
                    ->toArray();

                $emails = implode(';', $emails);
            }

            $emails = validateNotificationEmails($emails);
            $emails = $emails['valid'];

            if (!empty($emails)) {

                Mail::send('emails.users.notify_validities', compact('users'), function ($message) use ($emails) {
                    $message->to($emails);
                    $message->subject('Colaboradores - alerta de validades');
                });

                if (count(Mail::failures()) > 0) {
                    throw new \Exception('Falhou o envio do e-mail. Tente de novo.');
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has agency
     *
     * @param type $agency
     * @return boolean
     */
    public function hasAgency($agency)
    {
        $agencies = $this->agencies;

        if ($this->hasRole([config('permissions.role.admin')])) {
            return true;
        }

        if ($agencies) {

            if (is_array($agency)) {
                return empty(array_intersect($agency, $agencies)) ? false : true;
            } elseif (in_array($agency, $agencies)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Check if user is allowed to take an action
     *
     * @param $permission
     * @return bool
     */
    public function allowedAction($permission)
    {
        if ($this->isAdmin()) {
            return true;
        }

        $actions = (array) $this->allowed_actions;

        if (isset($actions[$permission]) && $actions[$permission] == '1') {
            return true;
        }

        return false;
    }

    /**
     * Create map marker from uploaded image
     *
     * @param $permission
     * @return bool
     */
    public function createMapMarker()
    {
        $filepath = $this->filepath;
        $path     = '/uploads/users/marker_' . $this->id . '.png';
        $destination = public_path() . $path;

        cropImageRounded($filepath, $destination);

        $this->location_marker = $path;
        $this->save();

        return true;
    }

    /**
     * Return list of user agencies
     *
     * @return type
     */
    public function listsAgencies($groupedList = true)
    {
        $agencies = $this->agencies;

        if ($this->hasRole([config('permissions.role.admin')]) || empty($agencies)) {

            if ($groupedList) {
                $agencies = Agency::remember(config('cache.query_ttl'))->cacheTags(Agency::CACHE_TAG)->orderBy('name')->get();
                $agencies = $agencies->groupBy('source');

                $list = [];
                foreach ($agencies as $source => $agency) {
                    $list[strtoupper($source)] = $agency->pluck('name', 'id')->toArray();;
                }

                return $list;
            } else {
                return Agency::remember(config('cache.query_ttl'))->cacheTags(Agency::CACHE_TAG)->orderBy('name')->pluck('name', 'id')->toArray();
            }
        } else {
            return Agency::remember(config('cache.query_ttl'))->cacheTags(Agency::CACHE_TAG)->whereIn('id', $agencies)->orderBy('name')->pluck('name', 'id')->toArray();
        }
    }

    /**
     * List all operators grouped by source
     * @param $allOperators
     * @param bool $grouped
     * @return array
     */
    public static function listOperators($allOperators, $grouped = false, $simpleResult = true, $emptyValue = false)
    {

        $grouped = false; //Adicionado em 15 janeiro 2021 para remover permanentemente a devisão por delegação

        if ($grouped) {
            $groupedOperators = $allOperators->groupBy('source');
        } else {
            $groupedOperators = [$allOperators];
        }


        $operators = [];
        foreach ($groupedOperators as $source => $allOperators) {
            foreach ($allOperators as $operator) {

                if ($simpleResult) {
                    $data = $operator->name;
                } else {
                    $data = [
                        'value'         => $operator->id,
                        'display'       => $operator->first_last_name,
                        'data-vehicle'  => @$operator->vehicle,
                        'data-provider' => @$operator->provider_id,
                    ];
                }

                if ($grouped) {
                    $operators[$source][$operator->id] = $data;
                } else {
                    $operators[$operator->id] = $data;
                }
            }
        }

        return $operators;
    }


    /**
     * Check if user can see shipment prices
     * @return mixed
     */
    public function showPrices()
    {
        return Auth::user()->ability(config('permissions.role.admin'), 'show_prices');
    }

    /**
     * Return next model ID
     * @param $query
     * @return mixed
     */
    public function nextId()
    {
        return User::filterSource()
            ->filterAgencies()
            ->where('id', '>', $this->id)
            ->min('id');
    }

    /**
     * Return previous model ID
     * @param $query
     * @return mixed
     */
    public function previousId()
    {
        return User::filterSource()
            ->filterAgencies()
            ->where('id', '<', $this->id)
            ->max('id');
    }

    /**
     * Print user validities
     *
     * @param $ids
     * @param string $outputFormat
     * @return mixed
     */
    public static function printValidities($startDate, $endDate, $otherData = null, $outputFormat = 'I')
    {

        ini_set("memory_limit", "-1");

        $cards = UserCard::with('user')
            ->filterSource()
            ->whereBetween('validity_date', [$startDate, $endDate]);


        if (@$otherData['user']) {
            $cards = $cards->where('user_id', $otherData['user']);
        }

        $cards = $cards->orderBy('validity_date', 'asc')
            ->get();

        $contracts = UserContract::with('user')
            ->whereHas('user', function ($q) {
                $q->filterSource();
            })
            ->whereBetween('end_date', [$startDate, $endDate])
            ->orderBy('end_date', 'asc')
            ->get();

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 14,
            'margin_right'  => 5,
            'margin_top'    => 25,
            'margin_bottom' => 15,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'cards'             => $cards,
            'contracts'         => $contracts,
            'documentTitle'     => 'Documentos a Expirar',
            'documentSubtitle'  => 'Resumo de ' . $startDate . ' até ' . $endDate,
            'view'              => 'admin.printer.users.validities'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Documentos a Expirar.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Limit query to user agencies
     * Atenção! Existe uma cópia desta função no modelo "Customers"
     *
     * @return type
     */
    public function scopeFilterAgencies($query, $agencies = null, $acceptNull = false)
    {

        $user = Auth::user();

        if (is_null($agencies)) {
            $agencies = $user->agencies;
        }

        if ($user->hasRole([config('permissions.role.admin')]) && !empty($agencies)) {
            return $query->whereNull('agencies')
                ->orWhere(function ($q) use ($agencies) {
                    foreach ($agencies as $agency) {
                        $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                    }
                });
        } elseif ((!$user && $agencies) || ($user && (!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)))) {

            if ($acceptNull) {
                return $query->whereNull('agencies')
                    ->orWhere(function ($q) use ($agencies) {
                        foreach ($agencies as $agency) {
                            $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                        }
                    });
            } else {
                return $query->whereNotNull('agencies')
                    ->where(function ($q) use ($agencies) {
                        foreach ($agencies as $agency) {
                            $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                        }
                    });
            }
        }
    }

    /**
     * Filter is active
     *
     * @param $query
     * @param bool $isActive
     * @return mixed
     */
    public function scopeIsActive($query, $isActive = true)
    {
        return $query->where('active', $isActive);
    }

    /**
     * Limit query to user agencies
     * Atenção! Existe uma cópia desta função no modelo "Customers"
     *
     * @return type
     */
    public function scopeFilterSource($query)
    {

        $agencies = Agency::whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        return $this->where(function ($q) use ($agencies) {
            $q->whereNull('agencies');
            $q->orWhere(function ($q) use ($agencies) {
                foreach ($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                }
            });
        });

        /*if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            return $this->where(function($q) use ($agencies){
                $q->whereNull('agencies');
                foreach($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%'.$agency.'%');
                }
            });
        }*/
    }

    /**
     * Check if user has seller role
     *
     * @param type $agency
     * @return boolean
     */
    public function scopeIsSeller($query, $includeAgencyUsers = false)
    {
        return $query->whereHas('roles', function ($q) use ($includeAgencyUsers) {
            $q->where('name', config('permissions.role.seller'));
            if ($includeAgencyUsers) {
                $q->orWhere('name', config('permissions.role.agency'));
            }
        });
    }

    /**
     * Check if user is operator
     *
     * @param type $agency
     * @return boolean
     */
    public function scopeIsOperator($query, $operator = true)
    {
        if ($operator) {
            return $query->where(function ($q) {
                $q->whereHas('roles', function ($q) {
                    $q->where('name', config('permissions.role.operator'));
                });
                $q->orWhere('login_app', 1);
            });
        } else {
            return $query->where(function ($q) {
                $q->whereDoesntHave('roles');
                $q->orWhereHas('roles', function ($q) {
                    $q->where('name', '<>', config('permissions.role.operator'));
                });
            });
        }
    }

    /**
     * Ignore users that are admin
     *
     * @param $query
     * @return mixed
     */
    public function scopeIgnoreAdmins($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', '<>', config('permissions.role.admin'));
        })
            ->where('active', 1);
    }


    public function getLocationStatus()
    {

        $location = $this->last_location;

        if (empty($location->latitude) || !$this->location_enabled) {
            return 'disabled';
        } else {

            $now = Date::now();
            $lastStatus = new Date($location->created_at);

            if ($lastStatus->diffInSeconds($now) >= 7200) { //2h
                return 'disabled';
            } elseif ($lastStatus->diffInSeconds($now) >= 3600) { //1h
                return 'timeago';
            } else {
                return 'enabled';
            }
        }
    }

    public function getLocationMarker()
    {

        $location = $this->last_location;

        if (empty($location->latitude) || !$this->location_enabled) {
            return null;
        } else {

            $now = Date::now();
            $lastStatus = new Date($location->created_at);

            if ($lastStatus->diffInSeconds($now) >= 7200) { //2h
                $marker = asset('assets/img/default/marker_offline.png');
            } elseif ($lastStatus->diffInSeconds($now) >= 3600) { //1h
                $marker = asset('assets/img/default/marker_timeago.png');
            } else {
                $marker = asset('assets/img/default/marker.png');
            }
        }

        return $this->filehost . ($this->filepath ? $this->location_marker : $marker);
    }

    /**
     * Store settings array to customer
     *
     * @param $permissions
     * @return mixed
     */
    public function storeSettings($settingsArr, $autosave = true)
    {
        $arr = $this->settings;
        $arr = empty($arr) ? [] : $arr;
        $newArr = array_merge($arr, $settingsArr);
        $this->settings = $newArr;
        return $this->save();
    }

    /**
     * Store settings array to customer
     *
     * @param $permissions
     * @return mixed
     */
    public function deleteSettings($settingKey, $autosave = true)
    {
        $arr = $this->settings;

        if (empty(@$arr[$settingKey])) {
            return false;
        }

        if (empty($arr)) {
            return true;
        }

        unset($arr[$settingKey]);

        $this->settings = $arr;
        return $this->save();
    }

    /**
     * Check if user have some permissions
     * @param $permissions
     * @return mixed
     */
    public function getSetting($settingKey)
    {
        $arr = json_decode($this->attributes['settings'], true);
        return @$arr[$settingKey];
    }


    /**
     * Check if user have some permissions
     * @param $permissions
     * @return mixed
     */
    public function perm($permissions)
    {
        return Auth::user()->ability(config('permissions.role.admin'), $permissions);
    }


    /**
     * Check if user is admin
     *
     * @return mixed
     */
    public function isAdmin($isAdmin = true)
    {
        if ($isAdmin) {
            return Auth::user()->hasRole(config('permissions.role.admin'));
        } else {
            return Auth::user()->whereHas('role', function ($q) {
                return $q->where('name', '<>', config('permissions.role.admin'));
            });
        }
    }

    /**
     * Check if user is admin
     *
     * @return mixed
     */
    public function isManager($isAdmin = true)
    {
        if ($isAdmin) {
            return Auth::user()->hasRole(config('permissions.role.agency'));
        } else {
            return Auth::user()->whereHas('role', function ($q) {
                return $q->where('name', '<>', config('permissions.role.agency'));
            });
        }
    }

    /**
     * Check if user is guest agency
     *
     * @return mixed
     */
    public function isGuest()
    {
        return Auth::user()->hasRole(config('permissions.role.guest_agency'));
    }


    /**
     * Check if user is seller
     *
     * @return mixed
     */
    public function isSeller($isSeller = true)
    {
        if ($isSeller) {
            return Auth::user()->hasRole(config('permissions.role.seller'));
        } else {
            return Auth::user()->whereHas('role', function ($q) {
                return $q->where('name', '<>', config('permissions.role.seller'));
            });
        }
    }

    /**
     * Check if user is operator
     *
     * @return mixed
     */
    public function isOperator($isOperator = true)
    {
        if ($isOperator) {
            return Auth::user()->hasRole(config('permissions.role.operator'));
        } else {
            return Auth::user()->whereHas('role', function ($q) {
                return $q->where('name', '<>', config('permissions.role.operator'));
            });
        }
    }

    public static function printSIMCommunications($userData, $outputFormat = 'I')
    {

        ini_set("memory_limit", "-1");

        if (@$userData['customer']) {
            $user = User::where('id', $userData['customer'])->first();
            $NIF = $user->vat;
            if ($NIF == "") {
                return Redirect::route('admin.users.index')->with('error', 'O Utilizador não apresenta NIF Associado!');
            }
            $fullName = $user->fullname;
            if ($user->fullname == "") {
                return Redirect::route('admin.users.index')->with('error', 'O Utilizador não apresenta Nome Completo!');
            }
            if ($user->address == "" || $user->zip_code == "" || $user->city == "") {
                return Redirect::route('admin.users.index')->with('error', 'O Utilizador não apresenta todos os dados do Endereço!');
            }
            $fullAddress = $user->address . ', ' . $user->zip_code . ', ' . $user->city;
            $cards = UserCard::where('user_id', $userData['customer'])->get();
            $cc = $cards->where('type', 'cc')->first();
            if ($cc == null) {
                return Redirect::route('admin.users.index')->with('error', 'O Utilizador não apresenta O Cartão de Cidadão associado!');
            }
        }

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 14,
            'margin_right'  => 5,
            'margin_top'    => 25,
            'margin_bottom' => 15,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'NIF'           => $NIF,
            'fullName'      => $fullName,
            'fullAddress'   => $fullAddress,
            'cc'            => $cc->card_no,
            'documentTitle' => '',
            'view'          => 'admin.printer.users.sim_communications'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Informações de Comunicação.pdf', $outputFormat);
    }

    public static function printUniformDoc($userData, $outputFormat = 'I')
    {
        ini_set("memory_limit", "-1");

        $user = User::where('id', $userData['user'])->first();

        $uniforms = Location::with('equipments')
            ->filterSource()
            ->where('operator_id', $user->id)
            ->first();

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 14,
            'margin_right'  => 5,
            'margin_top'    => 25,
            'margin_bottom' => 15,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'user' => $user,
            'documentTitle' => '',
            'uniforms'      => $uniforms,
            'view'          => 'admin.printer.users.uniform'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Declaração de Fardamento.pdf', $outputFormat);
    }

    public static function printInternationalActivityDoc($userData, $outputFormat = 'I')
    {
        ini_set("memory_limit", "-1");

        $user = User::where('id', $userData['user'])->first();

        $activities = Setting::get('collaborators_activities');
        $activities = explode("\r\n", $activities);

        $activitiesPT = [];
        $activitiesEn = [];
        $currentLanguage = '';

        foreach ($activities as $activity) {
            if (strcasecmp($activity, 'PT') === 0 || strcasecmp($activity, 'PT:') === 0) {
                $currentLanguage = 'PT';
            } elseif (strcasecmp($activity, 'EN') === 0 || strcasecmp($activity, 'EN:') === 0) {
                $currentLanguage = 'EN';
            } else {
                if ($currentLanguage === 'PT') {
                    $activitiesPT[] = $activity;
                } elseif ($currentLanguage === 'EN') {
                    $activitiesEn[] = $activity;
                }
            }
        }

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 14,
            'margin_right'  => 5,
            'margin_top'    => 25,
            'margin_bottom' => 15,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'user' => $user,

            'documentTitle' => '',
            'activitiesPT'      => $activitiesPT,
            'activitiesEn'      => $activitiesEn,
            'view'          => 'admin.printer.users.activity'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Certificado Trab. de Transp. Internacional.pdf', $outputFormat);
    }

    public static function printEquipmentDoc($userData, $outputFormat = 'I')
    {
        ini_set("memory_limit", "-1");

        $user = User::where('id', $userData['user'])->first();

        $cards = UserCard::where('user_id', $user->id)->get();
        $cc = $cards->where('type', 'cc')->first();


        $equipmentInfo = $userData['info']['equipment_info'];
        $equipmentPrice = $userData['info']['price'];

        $equipmentInfo = explode("\r\n", $equipmentInfo);

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 14,
            'margin_right'  => 5,
            'margin_top'    => 25,
            'margin_bottom' => 15,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'user'               => $user,
            'documentTitle'      => '',
            'cc'                 => $cc,
            'equipmentInfo'      => $equipmentInfo,
            'equipmentPrice'     => $equipmentPrice,
            'view'               => 'admin.printer.users.equipment'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Declaração de Entrega de Equipamento.pdf', $outputFormat);
    }






    /**
     * Check if user is operator
     *
     * @return mixed
     */
    public function isPlatformer()
    {
        return Auth::user()->hasRole(config('permissions.role.platformer'));
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function vehicle_data()
    {
        if (hasModule('fleet')) {
            return $this->belongsTo('App\Models\FleetGest\Vehicle', 'vehicle', 'license_plate');
        }

        return $this->belongsTo('App\Models\Vehicle', 'vehicle', 'license_plate');
    }

    public function agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function chief()
    {
        return $this->belongsTo('App\Models\User', 'professional_chief_id');
    }

    public function workgroups()
    {
        return $this->belongsToMany('App\Models\UserWorkgroup', 'users_assigned_workgroups', 'user_id', 'workgroup_id');
    }

    public function notices()
    {
        return $this->belongsToMany('App\Models\Notice', 'notices_assigned_users', 'user_id', 'notice_id')
            ->withPivot(['readed']);
    }

    public function last_notice()
    {
        return $this->belongsToMany('App\Models\Notice', 'notices_assigned_users', 'user_id', 'notice_id')
            ->wherePivot('readed', 0)
            ->orderBy('notices.date', 'desc')
            ->take(1);
    }

    public function meetings()
    {
        return $this->hasMany('App\Models\Meeting', 'seller_id');
    }

    public function operator_customers()
    {
        return $this->hasMany('App\Models\Customer', 'operator_id');
    }

    public function seller_customers()
    {
        return $this->hasMany('App\Models\Customer', 'seller_id');
    }

    public function contracts()
    {
        return $this->hasMany('App\Models\UserContract', 'user_id');
    }

    public function absences()
    {
        return $this->hasMany('App\Models\UserAbsence', 'user_id');
    }

    public function locations()
    {
        return $this->hasMany('App\Models\UserLocation', 'operator_id');
    }

    public function last_location()
    {
        return $this->hasOne('App\Models\UserLocation', 'operator_id')->latest('id');
    }

    public function trips()
    {
        return $this->hasMany('App\Models\Trip\Trip', 'operator_id');
    }

    public function allowances()
    {
        return $this->hasMany('App\Models\Allowance', 'operator_id');
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

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = empty($value) ? null : $value;
    }

    public function setIpAttribute($value)
    {
        $this->attributes['ip'] = ip2long($value);
    }

    public function setAgenciesAttribute($value)
    {
        $this->attributes['agencies'] = empty($value) ? null : json_encode($value); //json_encode(array_map('intval', $value));
    }

    public function setAllowedActionsAttribute($value)
    {
        $this->attributes['allowed_actions'] = empty($value) ? null : json_encode($value);
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = empty($value) ? null : json_encode($value);
    }

    public function setSourceAttribute($value)
    {
        $this->attributes['source'] = empty($value) ? null : $value;
    }

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setSalaryExpensesAttribute($value)
    {
        $this->attributes['salary_expenses'] = empty($value) ? null : $value;
    }

    public function setSalaryPriceAttribute($value)
    {
        $this->attributes['salary_price'] = empty($value) ? null : $value;
    }

    public function setSalaryFoodAllowanceAttribute($value)
    {
        $this->attributes['salary_food_allowance'] = empty($value) ? null : $value;
    }

    public function setSalaryValueHourAttribute($value)
    {
        $this->attributes['salary_value_hour'] = empty($value) ? null : $value;
    }

    public function setSalaryWorkingTimeExemptionAttribute($value)
    {
        $this->attributes['salary_working_time_exemption'] = empty($value) ? null : $value;
    }

    public function setWorkgroupsArrAttribute($value)
    {
        $this->attributes['workgroups_arr'] = empty($value) ? null : json_encode($value);
    }

    public function setProfessionalChiefAttribute($value)
    {
        $this->attributes['professional_chief_id'] = empty($value) ? null : $value;
    }

    public function setBirthdateAttribute($value)
    {
        $this->attributes['birthdate'] = empty($value) ? null : $value;
    }

    public function setAdmissionDateAttribute($value)
    {
        $this->attributes['admission_date'] = empty($value) ? null : $value;
    }

    public function setResignationDateAttribute($value)
    {
        $this->attributes['resignation_date'] = empty($value) ? null : $value;
    }

    public function getFirstLastNameAttribute()
    {
        return split_name($this->attributes['name']);
    }

    public function getIpAttribute()
    {
        return long2ip($this->attributes['ip']);
    }

    public function getAgenciesAttribute()
    {
        return json_decode(@$this->attributes['agencies'], true);
    }

    public function getWorkgroupsArrAttribute()
    {
        return json_decode(@$this->attributes['workgroups_arr'], true);
    }

    public function getAllowedActionsAttribute()
    {
        return json_decode(@$this->attributes['allowed_actions'], true);
    }

    public function getSettingsAttribute()
    {
        return json_decode(@$this->attributes['settings'], true);
    }

    public function setLocationLatAttribute($value)
    {
        $this->attributes['location_lat'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setLocationLngAttribute($value)
    {
        $this->attributes['location_lng'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setAllowanceValueNacionalAttribute($value)
    {
        $this->attributes['allowance_value_nacional'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setAllowanceValueSpainAttribute($value)
    {
        $this->attributes['allowance_value_spain'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setAllowanceValueInternacionalAttribute($value)
    {
        $this->attributes['allowance_value_internacional'] = empty($value) || $value == 0.00 ? null : $value;
    }
    

    public function getLocationMarkerAttribute()
    {
        return empty($this->attributes['location_marker']) ? asset('assets/img/default/marker.png') : $this->attributes['location_marker'];
    }

    public function getShowRefenceAttribute()
    {
        $showReference = @$this->attributes['show_reference'];

        if (is_null($showReference)) {
            $showReference = Setting::get('show_customers_reference');
        }

        return $showReference;
    }
}
