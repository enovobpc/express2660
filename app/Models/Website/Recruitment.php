<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;

class Recruitment extends \App\Models\BaseModel
{

    use SoftDeletes, 
        FileTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_website';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'recruitments';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'area', 'role', 'availability', 'driving_licence', 'name', 'address', 'zip_code', 'city', 'phone', 'mobile', 'email', 
        'birthdate', 'gender', 'has_experiencece', 'professional_situation', 'company', 'company_role', 'company_time',
        'qualifications', 'formation_area', 'obs','filepath', 'filename', 'ip', 'source'
    ];

    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/candidaturas';
    
    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'area'     => 'required',
        'role'     => 'required',
        'gender'   => 'required',
        'name'     => 'required',
        'mobile'   => 'required',
        'email'    => 'required|email',
        'address'  => 'required',
        'zip_code' => 'required',
        'city'     => 'required',
        'company'  => 'required',
        'company_role'     => 'required',
        'company_time'     => 'required',
        'qualifications'   => 'required',
        'formation_area'   => 'required',
        'availability'     => 'required',
        'driving_licence'  => 'required',
        'has_exeprience'   => 'required',
        'professional_situation' => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'area'     => 'Área',
        'role'     => 'Função',
        'gender'   => 'Sexo',
        'name'     => 'Nome',
        'phone'    => 'Telefone',
        'mobile'   => 'Telemóvel',
        'email'    => 'E-mail',
        'address'  => 'Morada',
        'zip_code' => 'Código Postal',
        'city'     => 'Localidade',
        'company'  => 'Empresa',
        'company_role'     => 'A sua Função',
        'company_time'     => 'Quanto tempo trabalhou',
        'qualifications'   => 'Habilitações',
        'formation_area'   => 'Área de formação',
        'availability'     => 'Disponibilidade',
        'driving_licence'  => 'Carta de Condução',
        'has_exeprience'   => 'Experiência anterior',
        'professional_situation' => 'Situação Profissional'
    );

    /**
     * Create tracking code
     *
     * @return int
     */
    public function setCode()
    {
        $this->save();

        $max = Recruitment::whereSource(config('app.source'))
                        ->orderBy('code', 'desc')
                        ->first();

        if(!$max) {
            $code = 1;
        } else {
            $code = (int) $max->code;
            $code++;
        }

        $code = str_pad($code, 4, "0", STR_PAD_LEFT);

        $this->hash = str_random(8);
        $this->code = $code;
        $this->save();
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
    public function setIpAttribute($value)
    {
        $this->attributes['ip'] = ip2long($value);
    }
    public function getIpAttribute($value)
    {
        return long2ip($value);
    }
  
   /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */
    
    public function scopeFilterSource($query) {
        return $query->where('source', config('app.source'));
    }
}
