<?php

namespace App\Models;

use function foo\func;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Date;

class Notice extends BaseModel
{
    use SoftDeletes,
        FileTrait,
        Sluggable;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_notices';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notices';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'summary', 'content', 'date', 'published', 'slug', 'sources', 'auto'
    ];
    
    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/notices';

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = [
        'image' => 'image',
    ];

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification()
    {
        $agencies = Agency::whereIn('source', $this->sources)->pluck('id')->toArray();

        //get notification recipients
        $recipients = \App\Models\User::where(function($q) use($agencies) {
            $q->whereNull('agencies');
            $q->orWhere(function($q) use ($agencies){
                foreach($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%"'.$agency.'"%');
                }
            });
        })
        ->whereHas('roles', function($query) {
            $query->whereNotIn('name', ['operador']);
        })
        ->get();

        $recipientsIds = $recipients->pluck('id')->toArray();

        $this->users()->sync($recipientsIds);
        $this->users()->update(['readed' => 0]);

        foreach ($recipients as $recipient) {
            $recipient->count_notices = $recipient->count_notices + 1;
            $recipient->save();
        }

        return true;
    }


    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function deleteNotification()
    {
        $ids = $this->users()->pluck('users.id')->toArray();

        $this->users()->detach();

        $users = User::whereIn('id', $ids)
            ->with('notices')
            ->whereHas('notices', function ($q) {
                $q->where('readed', 0);
            })
            ->get();

        foreach ($users as $user) {
            $user->count_notices = $user->notices->count();
            $user->save();
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'notices_assigned_users', 'notice_id','user_id')->withPivot(['readed']);
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

    public function scopeFilterSources($query){

        $user = Auth::user();
        $sources = $user->sources;

        if(!$user->isAdmin() || !empty($sources)) {
            return $query->where(function($q) use($sources) {
                foreach ($sources as $source) {
                    $q->orWhere('sources', 'like', '%'.$source.'%');
                }
            });
        }
    }

    public function hasSource($source = null){
        if(empty($source)) {
            $source = config('app.source');
        }

        $sources = $this->sources ? $this->sources : [];
        return in_array($source, $sources);
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
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim($value);
    }
    
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = trim($value);
    }

    public function setSourcesAttribute($value)
    {
        $this->attributes['sources'] = empty($value) ? null : json_encode($value);
    }
    
    public function getFilepathAttribute($value)
    {
        return empty($value) ? null : $value;
    }
    
    public function getDateAttribute($value)
    {
        return new Date($value);
    }
    
    public function getCreatedAtAttribute($value)
    {
        return new Date($value);
    }

    public function getSourcesAttribute($value)
    {
        return empty($value) ? null : json_decode($value);
    }
}
