<?php

namespace App\Models\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class HelpcenterAuthToken extends \App\Models\BaseModel {

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_enovo';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'helpcenter_auth_tokens';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'target', 'user_id', 'hash'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'hash'    => 'required',
        'user_id' => 'required',
    );


    /**
     * Return redirect URL to helpcenter
     * @param $request
     * @param $target
     * @return string
     */
    public static function redirect2Helpcenter($request, $target)
    {
        $source = config('app.source');
        $user   = Auth::user();
        $hash   = str_random(50);
        $redirectUrl = 'https://suporte.enovo.pt/auth/auto/' . $hash . '?' . http_build_query($request->toArray());

        HelpcenterAuthToken::where('source', $source)
            ->where('user_id', $user->id)
            ->where('email', $user->email)
            ->forceDelete();

        HelpcenterAuthToken::insert([
            'source'        => $source,
            'user_id'       => $user->id,
            'target'        => $target,
            'email'         => $user->email,
            'hash'          => $hash,
            'target_url'    => $redirectUrl,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return $redirectUrl;
    }

    /**
     * Return article route
     * @param $articleId
     */
    public static function redirect2Article($articleId)
    {
        $request = new Request([
            'article' => $articleId
        ]);

        return Self::redirect2Helpcenter($request, 'article');
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */

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
}
