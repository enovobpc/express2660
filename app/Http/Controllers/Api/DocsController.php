<?php

namespace App\Http\Controllers\Api;

use App\Models\Agency;
use App\Models\Api\Docs\Category;
use App\Models\Api\Docs\Method;
use App\Models\Api\Docs\Section;
use App\Models\CustomerType;
use App\Models\IncidenceResolutionType;
use App\Models\IncidenceType;
use App\Models\PaymentCondition;
use App\Models\Route;
use App\Models\Service;
use App\Models\ShippingStatus;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Request;

class DocsController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     * 
     * @var string 
     */
    protected $layout = 'layouts.api_docs';

    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     *
     * @param Request $request
     * @param null $version
     * @param null $level Tipo de api [public|partners|mobile]
     * @param string $categorySlug
     * @param null $sectionSlug
     * @return \App\Http\Controllers\type
     */
    public function index(Request $request, $level=null, $version = null, $categorySlug = 'shipments', $sectionSlug = null) {

        $mostRecentVersion = '2.x';

        if(empty($level)) {
            return Redirect::route('api.docs.index', ['public', $version]);
        } elseif(!in_array($level, ['public', 'partners', 'mobile'])){
            return App::abort(404);
        }

        if(empty($version) || $version == 'v1') {
            return Redirect::route('api.docs.index', [$level, $mostRecentVersion]);
        }

        if(!in_array($version, ['2.x'])){
            return App::abort(404);
        }

        $category = Category::where('slug', $categorySlug)->first();

        if(!$category && $categorySlug != 'examples') {
            App::abort(404);
        }

        $allCategories = Category::where('api_version', $version)
            ->where('levels', 'like', '%'.$level.'%')
            ->ordered()
            ->get();

        $allSections = Section::with(['methods' => function($q) use($level) {
                $q->where('levels', 'like', '%'.$level.'%');
                $q->ordered();
            }])
            ->where('api_version', $version)
            ->where('category_id', $categorySlug)
            ->ordered()
            ->get();

        $endpoint = env('API_DOMAIN');



        $data = compact(
            'endpoint',
            'level',
            'version',
            'category',
            'categorySlug',
            'sectionSlug',

            'allCategories',
            'allSections'
        );

        if($categorySlug == 'examples') {

            $statusList = ShippingStatus::ordered()->get();

            $servicesList = Service::filterSource()->ordered()->get();

            $incidencesList = IncidenceType::filterSource()->isActive()->ordered()->get();

            $resolutionsTypes = IncidenceResolutionType::ordered()->get();

            $customerTypes = CustomerType::get();

            $agencies = Agency::filterSource()->get();

            $paymentConditions = PaymentCondition::filterSource()->ordered()->get();

            $routes = Route::filterSource()->ordered()->get();

            $data = compact(
                'endpoint',
                'level',
                'version',
                'category',
                'categorySlug',
                'sectionSlug',

                'allCategories',
                'allSections',

                'statusList',
                'servicesList',
                'incidencesList',
                'resolutionsTypes',
                'agencies',
                'paymentConditions',
                'customerTypes',
                'routes'
            );

            return $this->setContent('api.docs.examples', $data);
        }

        if($sectionSlug) {
            return $this->setContent('api.docs.show', $data);
        }

        return $this->setContent('api.docs.index', $data);
    }
}