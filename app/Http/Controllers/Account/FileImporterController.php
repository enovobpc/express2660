<?php

namespace App\Http\Controllers\Account;

use App\Models\FileImporter;
use App\Models\ImporterModel;
use App\Models\Shipment;
use App\Models\Webservice\Base;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Auth, Date, Setting, Excel;

class FileImporterController extends \App\Http\Controllers\Controller
{

    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.account';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'shipments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $models = ImporterModel::where(function ($q) {
            $q->whereNull('source');
            $q->orWhere('source', config('app.source'));
        })
            ->where('available_customers', 1)
            ->where(function ($q) use ($customer) {
                $q->where('customer_code', $customer->code);
                $q->orWhereNull('customer_code');
                $q->orWhere('customer_code', '');
            })
            ->pluck('name', 'id')
            ->toArray();

        $hasErrors = false;
        $previewRows = false;

        $data = compact(
            'models',
            'hasErrors',
            'previewMode',
            'previewRows'
        );

        return $this->setContent('account.file_importer.index', $data);
    }


    /**
     * Init importation process
     *
     * @return \Illuminate\Http\Response
     */
    public function executeImportation(Request $request)
    {

        $file        = $request->file('file');
        $filepath    = $request->get('filepath');
        $previewMode = $request->get('preview_mode');

        $model = ImporterModel::where(function ($q) {
            $q->whereNull('source');
            $q->orWhere('source', config('app.source'));
        })
            ->where('available_customers', 1)
            ->find($request->import_model);

        if (!$model) {
            return Redirect::back()->with('error', 'O método escolhido não foi encontrado no sistema.');
        }

        $model->mapping = (array)$model->mapping;
        $method = $model->type ? $model->type : 'shipments';

        if (!empty($file)) {
            $destinationPath = storage_path() . '/importer/';
            $filename = 'temporary.' . $file->getClientOriginalExtension();
            $filepath = $destinationPath . $filename;

            if (!$file->move($destinationPath, $filename)) {
                return Redirect::back()->with('error', 'Não foi possível validar o ficheiro a carregar. Verifique se o tamanho é inferior a 2MB ou a extensão.');
            }
            $previewMode = 1;
        } elseif (empty($filepath)) {
            return Redirect::back()->withInput()->with('error', 'Não foi encontrado o ficheiro para imporar. Tente de novo.');
        }

        return $this->{$method}($request, $model, $filepath, $previewMode, 'account');
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function shipments(Request $request, $model, $filepath, $previewMode, $source)
    {

        $customer = Auth::guard('customer')->user();
        $billingCustomerCode = $customer->customer_id ? @$customer->customer->code : $customer->code;

        $model->customer_code = $billingCustomerCode;

        $importationHashId = str_random(6);
        $fileImporter    = new FileImporter();
        $data = $fileImporter->shipments($request, $model, $filepath, $previewMode, $source, $importationHashId);

        if ($previewMode) {
            return $this->setContent('account.file_importer.index', $data);
        }

        //importa automatico para o fornecedor
        $shipments = Shipment::where('trailer', $importationHashId)->get();
        foreach ($shipments as $shipment) {
            try {
                $webservice = new Base();
                $webservice->submitShipment($shipment);
                unset($shipment->provider_weight);
            } catch (\Exception $e) {
            }
        }

        Shipment::where('trailer', $importationHashId)->update(['trailer' => null]);

        return Redirect::route('account.shipments.index')->with('success', 'Ficheiro importado com sucesso.');
    }

    /**
     * Submit native file
     *
     * @param Request $request
     * @return \App\Models\type
     */
    public function shipments_dimensions(Request $request, $model, $filepath, $previewMode, $source)
    {

        // dd($request->all());
        $customer = Auth::guard('customer')->user();
        $billingCustomerCode = $customer->customer_id ? @$customer->customer->code : $customer->code;

        $model->customer_code = $billingCustomerCode;

        $importationHashId = str_random(6);
        $fileImporter    = new FileImporter();

        $data = $fileImporter->shipments_dimensions($request, $model, $filepath, $previewMode, $source, $importationHashId);

        if ($previewMode) {
            return $this->setContent('account.file_importer.index', $data);
        }

        return Redirect::route('account.shipments.index')->with('success', 'Ficheiro importado com sucesso.');
    }
}
