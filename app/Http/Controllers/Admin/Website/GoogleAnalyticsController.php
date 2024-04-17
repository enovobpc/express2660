<?php

namespace App\Http\Controllers\Admin\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\App;
use LaravelAnalytics;
use Date;

class GoogleAnalyticsController extends \App\Http\Controllers\Admin\Controller
{
    
    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'log_google_analytics';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',log_google_analytics']);
    }

    
    /**
     * Show the application log for google analytics
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $startYear = config('laravel-analytics.startYear');

        $filter = $request->get('filter');
        $tab    = $request->get('tab', 'monthly');

        switch ($tab) {
            
            case 'yearly': 
                $dimensions = 'ga:month';
                $startDate  = new Date($request->get('start', date('Y')).'-01-01');
                $endDate    = new Date($request->get('start', date('Y')).'-12-31');
                $filters    = array_reverse(yearsArr($startYear, date('Y')));
                break;
            
            case 'daily': 
                $dimensions = 'ga:hour';
                $startDate  = new Date($request->get('start','today'));
                $endDate    = new Date($request->get('end','today'));
                $filters    = trans('admin/google_analytics.filters.day');
                break;
            
            default: 
                $dimensions = 'ga:date';
                
                $startDate = $request->get('start', 30);
                
                if(in_array($startDate, array_keys(trans('admin/google_analytics.filters.month')))) {
                    $today = new Date();
                    $startDate = $today->subDays($startDate);
                } else {
                    $startDate  = new Date($startDate);
                }
                
                $endDate    = new Date($request->get('end'));
                $filters    = trans('admin/google_analytics.filters.month');
                break;
        }
        
        /**
         * Visits chart
         */
        $adicional = array('dimensions' => $dimensions);
        
        try {
            
            $analyticsVisits = LaravelAnalytics::performQuery($startDate, $endDate, 'ga:visits,ga:pageviews,ga:percentNewSessions,ga:avgSessionDuration,ga:bounceRate', $adicional);
            
            $rows   = $analyticsVisits['rows'];
            $totals = $analyticsVisits['totalsForAllResults'];
            
        } catch(\Google_Service_Exception $e) {
            return Redirect::back()->with('error', 'Não foi possível obter a estatística de visitas para os parâmetros selecionados.');
        }

        $analyticsGraphData = [];
        foreach ($rows as $row) {

            if($dimensions == 'ga:date') {
                $row[0] = new Date($row[0]);
                $row[0] = $row[0]->format('d M');
            } else if($dimensions == 'ga:month') {
                $row[0] = new Date($startYear.'-'.$row[0].'-01');
                $row[0] = $row[0]->format('M');
            } else {
                $row[0] = $row[0].'h';
            } 
            
            $analyticsGraphData['labels'][]             = "'" . $row[0] . "'";
            $analyticsGraphData['visits'][]             = $row[1];
            $analyticsGraphData['pageViews'][]          = $row[2];
            $analyticsGraphData['percentNewSessions'][] = $row[3];
            $analyticsGraphData['avgSessionDuration'][] = $row[4];
            $analyticsGraphData['bounceRate'][]         = $row[5];
            $analyticsGraphData['background'][]         = '"#337AB7"';
        }

        $analyticsTotals = mapArrayKeys($totals, array(
            'ga:visits'             => 'visits',
            'ga:sessions'           => 'sessions',
            'ga:pageviews'          => 'pageViews',
            'ga:percentNewSessions' => 'percentNewSessions',
            'ga:avgSessionDuration' => 'avgSessionDuration',
            'ga:bounceRate'         => 'bounceRate'
        ));

        $analyticsGraphData['labels'] = implode($analyticsGraphData['labels'], ',');
        $analyticsGraphData['visits'] = implode($analyticsGraphData['visits'], ',');
        $analyticsGraphData['background'] = implode($analyticsGraphData['background'], ',');
        
        $mostVisitedPages = LaravelAnalytics::getMostVisitedPagesForPeriod($startDate, $endDate, 100);

        $topPages = [];
        $pos = 1;
        foreach ($mostVisitedPages as $page) {
            $page['pos'] = $pos++;
            $topPages[]  = $page;
        }
        
        $topPages = json_encode($topPages);

        return $this->setContent('admin.website.visits.index', compact('analyticsGraphData','analyticsTotals', 'filters', 'filter', 'tab', 'topPages'));
    }

    /**
     * 
     * @param type $slug
     * @return type
     */
    public function get(Request $request, $slug) {
        
        $startDate = empty($request->get('startDate')) ? 
                config('laravel-analytics.startYear').'-01-01' 
                : $request->get('startDate');
        
        $endDate = $request->get('endDate');
        
        if(!empty($endDate) && $endDate < $startDate) {
            App::abort(500);
        }
        
        $adicional = array('dimensions' => 'ga:'.$slug);
        
        try {
            
            $startDate = new Date($startDate);
            $endDate = new Date($endDate);
            
            $analyticsVisits = LaravelAnalytics::performQuery($startDate, $endDate, 'ga:visits,ga:pageviews', $adicional);
            
            $rows   = $analyticsVisits['rows'];
            
        } catch(\Google_Service_Exception $e) {
            return Redirect::back()->with('error', 'Não foi possível obter a estatística de visitas para os parâmetros selecionados.');
        }
        
        $view = snake_case($slug);
        
        return Response::json([
            'data' => $rows,
            'html' => view('admin.website.visits.partials.'.$view)->render()
        ]);
    }
}
