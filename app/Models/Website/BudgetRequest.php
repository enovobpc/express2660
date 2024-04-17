<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetRequest extends \App\Models\BaseModel
{

    use SoftDeletes;

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
    protected $table = 'budget_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'frequency', 'description', 'pack_type', 'qty', 'pack_length',
        'pack_width', 'pack_height', 'weight', 'name', 'email', 'phone','ip', 'source'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'from'        => 'required',
        'to'          => 'required',
        'description' => 'required',
        'pack_type'   => 'required',
        'pack_length' => 'required',
        'pack_height' => 'required',
        'pack_width'  => 'required',
        'weight'      => 'required',
        'name'        => 'required',
        'phone'       => 'required',
        'email'       => 'required|email',
        'type'        => 'required'
    );

    /**
     * Validator custom attributes
     *
     * @var array
     */
    protected $customAttributes = array(
        'from'        => 'De',
        'to'          => 'Para',
        'pack_type'   => 'Tipo de volumes',
        'description' => 'Descrição',
        'pack_length' => 'Comprimento',
        'pack_height' => 'Altura',
        'pack_width'  => 'Largura',
        'weight'      => 'Peso',
        'name'        => 'Nome',
        'phone'       => 'Telefone',
        'email'       => 'E-mail',
    );

    /**
     * Create tracking code
     *
     * @return int
     */
    public function setCode()
    {
        $this->save();

        $max = BudgetRequest::whereSource(config('app.source'))
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


}
