<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Auth, Response, Date;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.master';

    /**
     * The main menu option that should be used for responses
     *
     * @var string
     */
    protected $menuOption = 'home';

    /**
     * The sidebar menu option that should be used for responses
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = view($this->layout);
        }

        view()->share('auth', Auth::guard('customer')->user());
    }

    public function callAction($method, $parameters)
    {
        $this->setupLayout();

        $response = call_user_func_array(array($this, $method), $parameters);

        if (is_null($response) && !is_null($this->layout)) {
            $response = $this->layout;
        }

        return $response;
    }

    /**
     * Set content used by the controller.
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public function setContent($view, $data = [])
    {
        if (!is_null($this->layout)) {
            view()->share('menuOption', $this->menuOption);
            view()->share('sidebarActiveOption', $this->sidebarActiveOption);
            return $this->layout->nest('child', $view, $data);
        }

        return view($view, $data);
    }

    /**
     * Set the layout used by the controller.
     *
     * @param $name
     * @return void
     */
    protected function setLayout($name)
    {
        $this->layout = $name;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function syncShipments(Request $request)
    {

        $date = $request->get('date');
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $date = new Date($date);
        $date = $date->subDays(2)->format('Y-m-d');

        $shipments = Shipment::query();
        if (config('app.source') == 'okestafetas') {
            $idGls = 745;
            $shipments = $shipments->whereIn('provider_id', [$idGls]);
        }

        $shipments = $shipments->where('webservice_method', 'gls_zeta')
            ->where('date', '>=', $date)
            ->get();

        return Response::json($shipments->toArray());
    }

    /**
     * Método alternativo de SOS para chamar a gls
     *
     * @param Request $request
     */
    public function glsApi(Request $request) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $request->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode($request->xml));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
        $response = curl_exec($ch);
        curl_close($ch);

        header ("Content-Type:text/xml");
        echo $response;
        exit;
    }

    /**
     * Método alternativo de SOS para chamar o keyinvoice
     * @return false|string
     * @throws \SoapFault
     */
    public function keyinvoiceApi(Request $request) {

        $input = $request->all();

        if($request->has('download') && $request->get('download')) {
            return file_get_contents($request->get('download'));
        }

        $endpoint = 'https://login.keyinvoice.com/API3_ws.php?wsdl';

        $apiKey = $input['api_key'];
        $method = $input['method'];
        $data   = json_decode($input['data'], true);

        if(empty($data['sid'])) { //tem de fazer login e obter sid

            $client   = new \SoapClient($endpoint, ['encoding' => 'UTF-8']);
            $response = $client->authenticate($apiKey);

            if($response[0] > 0 || $response[0] == "1") {
                $data['sid'] = $response[1];
            } else {
                throw new \Exception($this->mapError($response[0]));
            }

        }

        $data = array_values($data);
        $instance = new \SoapClient($endpoint, array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => true));
        $response = call_user_func_array(array($instance, $method), $data);
        $response = json_encode($response);
        return $response;
    }
}
