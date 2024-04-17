<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use App\Models\Traits\FileTrait;
use Auth;


class FileRepository extends BaseModel
{
    use SoftDeletes,
        SortableTrait,
        FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_files_repository';

    /**
     * Default folders
     */
    const FOLDER_CUSTOMERS = 1;
    const FOLDER_PROVIDERS = 5;
    const FOLDER_USERS     = 2;
    const FOLDER_VEHICLES  = 3;
    const FOLDER_SHIPMENTS = 4;
    const FOLDER_PURCHASE_INVOICES = 5;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files_repository';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'name', 'filepath', 'filename', 'filehost', 'source_class', 'source_id', 'type_id',
        'is_folder', 'operator_visible', 'customer_visible', 'created_by_customer', 'obs', 'user_id'
    ];


    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/repository';
    
    /**
     * Default sort column
     * 
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];
    
    /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'name'      => 'required',
        'filepath'  => 'required',
    ];

    /**
     * Get path of documents directory
     *
     * @return string
     */
    public static function getDirectoryBase() {
        return public_path() . '/' . self::DIRECTORY . '/';
    }

    /**
     * Get folder navigation breadcrumbs
     * @param int $curFolder id of current folder
     * @return array
     */
    public static function getFolderBreadcrumb($curFolder, $breadcrumb = array(), $root_folder = 0) {

        $breadcrumb[] = array(
            'id'    => $curFolder->id,
            'title' => $curFolder->name
        );

        if ($curFolder->id == $root_folder)
            $breadcrumb = array_reverse($breadcrumb);
        elseif ($curFolder->parent_id == 0) {
            $breadcrumb[] = array(
                'id'    => 0,
                'title' => 'Documentos'
            );
            $breadcrumb = array_reverse($breadcrumb);
        } else {
            $curFolder = self::whereId($curFolder->parent_id)->first();
            $breadcrumb = self::getFolderBreadcrumb($curFolder, $breadcrumb, $root_folder);
        }

        return $breadcrumb;
    }

    /**
     * Get folder navigation breadcrumbs
     * @param int $curFolder id of current folder
     * @return array
     */
    public static function destroyRecursive($file) {

        if($file->is_folder) {
            $files = self::where('parent_id', $file->id)->get();

            if($files->isEmpty()) {
                $file->delete();
                return true;
            }

            foreach ($files as $item) {
                $result = self::destroyRecursive($item);
                if(!$result) {
                    $errors[] = $item->filepath;
                }
            }

            if(empty($errors)) {
                $file->delete();
                return true;
            }

            return false;
        } else {

            if(\File::exists($file->filepath)) {
                $result = \File::delete($file->filepath);

                if($result) {
                    $file->delete();
                    return true;
                }

                return false;

            } else {
                $file->delete();
                return true;
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
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'source_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'source_id');
    }

    public function created_customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function created_user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'source_id');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'source_id');
    }

    public function vehicle()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'source_id');
    }

    public function purchase_invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice', 'source_id');
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
    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id']  = empty($value) ? null : $value;
    }

    public function setTypeIdAttribute($value)
    {
        $this->attributes['type_id']  = empty($value) ? null : $value;
    }

    public function setFilepathAttribute($value)
    {
        $this->attributes['filepath']  = $value ? $value : null;
        $this->attributes['extension'] = \File::extension(public_path($value));
        $this->attributes['filehost']  = env('APP_URL');
    }

    public function setFilenameAttribute($value)
    {
        $this->attributes['filename']  = empty($value) ? null : $value;

        if(!@$this->attributes['name']) {
            $this->attributes['name'] = $this->attributes['filename'];
        }
    }

    public function setFilehostAttribute($value)
    {
        $this->attributes['filehost']  = empty($value) ? null : $value;
    }
}
