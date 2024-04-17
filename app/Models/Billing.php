<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Setting;

class Billing extends BaseModel
{

    //use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    //protected $table = 'billing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $fillable = [];

    /**
     * Validator rules
     * 
     * @var array 
     */
    //protected $rules = [];


    public static function getPeriodDates($year = null, $month = null, $period = '30d') {

        $year  = empty($year) ? date('Y') : $year;
        $month = empty($month) ? date('m') : $month;
        $lastDayOfMonth = Carbon::createFromDate($year, $month, 1)->lastOfMonth()->format('d');

        $periodType = substr($period, -1) ;

        if($periodType == 'q') {
            if($period == '1q') { //biweekly
                $firstMonthDay = $year.'-'.$month.'-01';
                $lastMonthDay  = $year.'-'.$month.'-15';
            } elseif($period == '2q') {
                $firstMonthDay = $year.'-'.$month.'-16';
                $lastMonthDay  = $year.'-'.$month.'-'.$lastDayOfMonth;
            }
        } elseif($periodType == 'w') { //week

            $firstDay = Carbon::createFromDate($year, $month, 1);
            $lastDay  = Carbon::createFromDate($year, $month, 1)->lastOfMonth();

            $firstWeek = $firstDay->weekOfYear;
            $lastWeek  = $lastDay->weekOfYear;

            $weeksArr = [];
            for($i = $firstWeek ; $i <= $lastWeek ; $i++) {
                $weeksArr[] = $i;
            }

            $selectedWeek = str_replace('w', '', $period);

            $firstDay->setISODate($year,$weeksArr[$selectedWeek]);
            $firstMonthDay = $firstDay->startOfWeek();
            $lastMonthDay  = $firstDay->endOfWeek();


        } else { //monthly
            $firstMonthDay = $year.'-'.$month.'-01';
            $lastMonthDay  = $year.'-'.$month.'-'.$lastDayOfMonth;
        }

        return [
            'first'     => $firstMonthDay,
            'last'      => $lastMonthDay,
            'allMonth'  => $period == '30d' ? true : false
        ];
    }

    /**
     * Return period name
     *
     * @param null $year
     * @param null $month
     * @param string $period
     */
    public static function getPeriodName($year = null, $month = null, $period = '30d') {

        $name = trans('datetime.month.' . $month) . ' ' . $year;

        $periodType = substr($period, -1) ;

        if($periodType == 'q') {
            if($period == '1q') { //biweekly
                $name = '1ª quinzena ' . $name;
            } elseif($period == '2q') {
                $name = '2ª quinzena ' . $name;
            }
        }

        return $name;
    }

    /**
     * Return list of products to billing
     *
     * @return array
     */
    public static function listProducts() {

        $products = [];

        if(Setting::get('invoice_item_nacional_ref')) {
            $products['invoice_item_nacional'] = Setting::get('invoice_item_nacional_ref').' - '.Setting::get('invoice_item_nacional_system_name');
        }

        if(Setting::get('invoice_item_import_ref')) {
            $products['invoice_item_import'] = Setting::get('invoice_item_import_ref').' - '.Setting::get('invoice_item_import_system_name');
        }

        if(Setting::get('invoice_item_spain_ref')) {
            $products['invoice_item_spain'] = Setting::get('invoice_item_spain_ref').' - '.Setting::get('invoice_item_spain_system_name');
        }

        if(Setting::get('invoice_item_internacional_ref')) {
            $products['invoice_item_internacional'] = Setting::get('invoice_item_internacional_ref').' - '.Setting::get('invoice_item_internacional_system_name');
        }

        if(Setting::get('invoice_item_export_particular_ref')) {
            $products['invoice_item_export_particular'] = Setting::get('invoice_item_export_particular_ref').' - '.Setting::get('invoice_item_export_particular_system_name');
        }

        if(Setting::get('invoice_item_covenants_ref')) {
            $products['invoice_item_covenants'] = Setting::get('invoice_item_covenants_ref').' - '.Setting::get('invoice_item_covenants_system_name');
        }

        return $products;
    }

    /**
     * Return product reference from product slug
     *
     * @param $productSlug
     * @return mixed
     */
    public static function getProductRef($productSlug) {
        return Setting::get($productSlug.'_ref');
    }

    /**
     * Return product tax from product slug
     *
     * @param $productSlug
     * @return mixed
     */
    public static function getProductTax($productSlug) {
        $tax =  Setting::get($productSlug.'_tax');

        return empty($tax) ? 0 : $tax;
    }

    /**
     * Return product description from product slug
     *
     * @param $productSlug
     * @return mixed
     */
    public static function getProductDescription($productSlug) {
        return Setting::get($productSlug.'_desc');
    }

    /**
     * Get vat details to a given nif
     *
     * @param $vat
     * @return mixed
     * @throws \Exception
     */
    public static function getVatDetails($vat) {

        $vat = str_replace(' ', '', trim($vat));

        require_once base_path() . '/resources/helpers/DOMhtml.php';

        try {
            $html = file_get_html('https://www.portugalio.com/pesquisa/?tipo=empresas&q=' . $vat);

            $result = [];
            foreach($html->find('.list-row') as $row) {

                $logo         = @$row->find('.company-list-logo', 0)->src; //0 = first element found
                $name         = @$row->find('.list-item-title', 0)->plaintext;
                $fullAddress  = @$row->find('.list-item-address', 0)->plaintext;
                $zipCode      = @$row->find('.list-item-address .pc', 0)->plaintext;
                $phone        = @$row->find('.list-item-phones-block', 0)->plaintext;
                $mobile       = @$row->find('.list-item-phones-block', 1)->plaintext;

                $address = trim(str_replace($zipCode, '', $fullAddress));

                $matches = [];
                preg_match("/^\d{4}-\d{3}(.*)$/", $zipCode, $matches);
                $city    = trim(@$matches[1]);
                $zipCode = trim(str_replace($city, '', $zipCode));

                $result[] = [
                    'result'    => true,
                    'feedback'  => 'Success',
                    'vat'       => $vat,
                    'name'      => $name,
                    'address'   => $address,
                    'zip_code'  => $zipCode,
                    'city'      => $city,
                    'phone'     => trim(str_replace(' ', '', $phone)),
                    'mobile'    => trim(str_replace(' ', '', $mobile)),
                    'logo'      => $logo
                ];
            }

            return @$result[0];

        } catch (\Exception $e) {

            $result = [
                'result'    => false,
                'feedback'  => 'Não foram encontrados resultados ou ocorreu um erro na obtenção de resultados.',
            ];

            return @$result;
        }

        return [];
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
