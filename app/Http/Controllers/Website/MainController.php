<?php

namespace App\Http\Controllers\Website;

use App\Models\Agency;
use App\Models\Shipment;
use App\Models\Website\Brand;
use App\Models\Website\Page;
use Illuminate\Http\Request;
use App\Models\Website\Slider;
use Illuminate\Support\Facades\App;

class MainController extends \App\Http\Controllers\Website\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->bodyClass = 'static';

        $sliders = Slider::visible()
                    ->ordered()
                    ->get();

        return $this->setContent('website.home', compact('sliders'));
    }

    /**
     * Show about page
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        $this->menuOption = 'about';
        return $this->setContent('website.about');
    }

    /**
     * Show services page
     *
     * @return \Illuminate\Http\Response
     */
    public function services()
    {
        $this->menuOption = 'services';
        return $this->setContent('website.services');
    }
    /**
     * Show servic storage page
     *
     * @return \Illuminate\Http\Response
     */
    public function storage()
    {
        $this->menuOption = 'storage';
        return $this->setContent('website.storage');
    }

    /**
     * Show servic packaging page
     *
     * @return \Illuminate\Http\Response
     */
    public function packaging()
    {
        $this->menuOption = 'packaging';
        return $this->setContent('website.packaging');
    }

    /**
     * Show servic distribution page
     *
     * @return \Illuminate\Http\Response
     */
    public function distribution()
    {
        $this->menuOption = 'distribution';
        return $this->setContent('website.distribution');
    }

    /**
     * Show servic call center page
     *
     * @return \Illuminate\Http\Response
     */
    public function callcenter()
    {
        $this->menuOption = 'callcenter';
        return $this->setContent('website.callcenter');
    }

    /**
     * Show servic E-commerce page
     *
     * @return \Illuminate\Http\Response
     */
    public function ecommerce()
    {
        $this->menuOption = 'ecommerce';
        return $this->setContent('website.ecommerce');
    }

     /**
     * Show servic contacts page
     *
     * @return \Illuminate\Http\Response
     */
    public function contacts()
    {
        $this->menuOption = 'contacts';
        return $this->setContent('website.contacts');
    }

      /**
     * Show servic recruitment page
     *
     * @return \Illuminate\Http\Response
     */
    public function recruitment()
    {
        $this->menuOption = 'recruitment';
        return $this->setContent('website.recruitment');
    }

      /**
     * Show servic budget page
     *
     * @return \Illuminate\Http\Response
     */
    public function budget()
    {
        $this->menuOption = 'budget';
        return $this->setContent('website.budget');
    }
    

    /**
     * Show services page
     *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function services($slug = null)
    // {
    //     $this->menuOption = 'services';

    //     $view = 'website.services';
    //     if($slug == 'cdt-mooving') {
    //         $view = 'website.mooving';
    //     }
    //     return $this->setContent($view, compact('sliders'));
    // }

    /**
     * Show customers page
     *
     * @return \Illuminate\Http\Response
     */
    public function customers()
    {
        $this->menuOption = 'customers';

        $brands = Brand::ordered()->get();

        return $this->setContent('website.customers', compact('brands'));
    }

    /**
     * Show tracking page
     *
     * @return \Illuminate\Http\Response
     */
    public function tracking(Request $request, $tracking = null)
    {
        $this->bodyClass  = 'static';
        $this->menuOption = 'tracking';

        if(empty($tracking)) {
            $tracking = $request->get('tracking');
        }

        $tracking = str_replace(';', ',', $tracking);
        $trackingCodes = explode(',', $tracking);
        $trackingCodes = array_filter($trackingCodes);

        $agencies = Agency::where('source', config('app.source'))
            ->pluck('id')
            ->toArray();

        $shipments = Shipment::with(['history' => function($q){
            $q->with('status', 'agency')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');
        }])
        ->whereIn('tracking_code', $trackingCodes)
        ->whereIn('agency_id', $agencies)
        ->get();

        $shipmentsResults = [];
        if(!$shipments->isEmpty()) {

            foreach ($shipments as $shipment) {

                $lastHistory = $shipment->history->first();
                $stepStatus = @$lastHistory->status->tracking_step;

                if (empty($stepStatus)) {
                    $stepStatus = 'pending';
                    $stepId = 1;
                } else {
                    if ($stepStatus == 'pending') {
                        $stepId = 1;
                    } elseif ($stepStatus == 'accepted') {
                        $stepId = 2;
                    } elseif ($stepStatus == 'pickup') {
                        $stepId = 3;
                    } elseif ($stepStatus == 'transport') {
                        $stepId = 4;
                    } elseif (in_array($stepStatus, ['delivered', 'incidence', 'returned'])) {
                        $stepId = 5;
                    } elseif (in_array($stepStatus, ['canceled'])) {
                        $stepStatus = 'canceled';
                        $stepId = 4;
                    } else {
                        $stepStatus = 'transport';
                        $stepId = 4;
                    }
                }

                $shipmentsResults[] = [
                    'shipment'   => $shipment,
                    'stepId'     => $stepId,
                    'stepStatus' => $stepStatus
                ];
            }
        }

        $data = compact(
            'shipmentsResults',
            'tracking'
        );

        $view = 'website.tracking.index';
        if(!empty($shipmentsResults)) {
            $view = 'website.tracking.detail';
        }

        return $this->setContent($view, $data);
    }

    /**
     * Show legal informations page
     *
     * @return \Illuminate\Http\Response
     */
    public function customPage($slug = null)
    {
        $page = Page::with(['sections' => function($q){
                $q->with('contents');
                $q->ordered();
            }])
            ->whereHas('translations', function($q) use($slug) {
                $q->where('url', $slug);
                $q->where('locale', App::getLocale());
            })
            ->published()
            ->first();

        if(empty($page)) {
            App::abort(404);
        }

        return $this->setContent('website.custom_page', compact('page', 'slug'));
    }
}
