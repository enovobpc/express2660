<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Models\Billing\ApiKey;
use App\Models\Billing\VatRate;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\Company;
use App\Models\InvoiceGateway\KeyInvoice\Document;
use Response, Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

use App\Models\Invoice;
use App\Models\Billing\Item;
use App\Models\Provider;

class ItemsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'billing-items';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',invoices']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $companies = Company::filterSource()
            ->pluck('display_name', 'id')
            ->toArray();

        $brands = Brand::filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $models = BrandModel::whereHas('brand', function ($q) {
                $q->filterSource();
            })
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $data = compact(
            'companies',
            'brands',
            'models'
        );

        return $this->setContent('admin.billing.items.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $isFleetPart = $request->get('is_fleet_part', false);

        $product = new Item();
        $action = 'Adicionar Artigo de Faturação';
        $formOptions = [
            'route'                   => ['admin.billing.items.store'],
            'method'                  => 'POST',
            'data-toggle'             => 'ajax-form',
            'data-refresh-datatables' => 1,
            'data-target-datatables'  => 'oTableItems'
        ];

        $taxRates = Invoice::getVatTaxes();

        $brands = Brand::filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $providers = Provider::filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $data = compact(
            'product',
            'taxRates',
            'action',
            'formOptions',
            'brands',
            'providers',
            'isFleetPart'
        );

        return view('admin.billing.items.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return string
     */
    public function edit(Request $request, $id)
    {
        $product = Item::filterSource()
            ->findOrfail($id);

        $action = 'Editar Artigo de Faturação';
        $formOptions = [
            'route'                   => ['admin.billing.items.update', $product->id],
            'method'                  => 'PUT',
            'data-toggle'             => 'ajax-form',
            'data-refresh-datatables' => 1,
            'data-target-datatables'  => 'oTableItems'
        ];

        $taxRates = Invoice::getVatTaxes();

        $brands = Brand::filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $brandModels = BrandModel::where('brand_id', $product->brand_id)
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $providers = Provider::filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $data = compact(
            'product',
            'taxRates',
            'action',
            'formOptions',
            'brands',
            'brandModels',
            'providers'
        );

        return view('admin.billing.items.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id = null)
    {

        Item::flushCache(Item::CACHE_TAG);
        $product = Item::filterSource()->findOrNew($id);

        $input = $request->all();

        if ($product->exists) {
            $input['is_service']    = $product->is_service;
            $input['has_stock']     = $product->has_stock;
            $input['is_fleet_part'] = $product->is_fleet_part;
        } else {
            $input['is_service']    = $request->get('is_service', false);
            $input['has_stock']     = $request->get('has_stock', false);
            $input['is_fleet_part'] = $request->get('is_fleet_part', false);
        }
        
        $input['is_active'] = $request->get('is_active', false);
        $input['is_customer_customizable'] = $request->get('is_customer_customizable', false);

        if ($product->validate($input)) {
            $product->fill($input);
            unset($product->api_key);
            $product->source = config('app.source');
            $product->save();

            return Response::json([
                'result' => true,
                'feedback' => 'Dados gravados com sucesso.'
            ]);
        }
        
        return Response::json([
            'result' => false,
            'feedback' => $product->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Item::flushCache(Item::CACHE_TAG);

        $result = Item::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o artigo');
        }

        return Redirect::back()->with('success', 'Artigo removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        Item::flushCache(Item::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = Item::filterSource()
            ->whereIn('id', $ids)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = Item::filterSource()
            ->filterRequest($request)
            ->with(['brand', 'brandModel'])
            ->select();

        return Datatables::of($data)
            ->edit_column('name', function ($row) {
                return view('admin.billing.items.datatables.name', compact('row'))->render();
            })
            ->edit_column('provider_id', function($row) {
                return view('admin.billing.items.datatables.provider', compact('row'))->render();
            })
            ->edit_column('price', function ($row) {
                return view('admin.billing.items.datatables.price', compact('row'))->render();
            })
            ->edit_column('sell_price', function ($row) {
                return view('admin.billing.items.datatables.sell_price', compact('row'))->render();
            })
            ->edit_column('tax_rate', function ($row) {
                return money($row->tax_rate, '%', 0);
            })
            ->edit_column('stock_total', function ($row) {
                return view('admin.billing.items.datatables.stock_total', compact('row'))->render();
            })
            ->edit_column('is_service', function ($row) {
                return view('admin.billing.items.datatables.is_service', compact('row'))->render();
            })
            ->edit_column('is_active', function ($row) {
                return view('admin.billing.items.datatables.is_active', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.items.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    public function sortEdit()
    {

        $items = Item::filterSource()
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.billing.items.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/billing.items/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request)
    {

        Item::flushCache(Item::CACHE_TAG);

        try {
            Item::filterSource()->setNewOrder($request->ids);

            $response = [
                'result'  => true,
                'message' => 'Ordenação gravada com sucesso.',
            ];
        } catch (\Exception $e) {
            $response = [
                'result'  => false,
                'message' => 'Erro ao gravar ordenação. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }

    /**
     * Sync products from
     * GET /admin/billing.items/sync
     *
     * @return Response
     */
    public function sync(Request $request)
    {

        if ($request->has('install')) {
            return $this->installItems($request);
        }

        if (hasModule('invoices')) {
            try {
                $product = new Item();
                $product->syncProducts();

                /*$product = new Item();
                $product->syncTaxes();*/
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }

            return Redirect::back()->with('success', 'Artigos sincronizados com sucesso.');
        } else {
            return Redirect::back()->with('error', 'Não possui ligação ao programa de faturação.');
        }
    }

    /**
     * Insert items on keyinvoice
     * GET /admin/billing.items/sync
     *
     * @return Response
     */
    public function installItems(Request $request)
    {

        $companyId = null;
        $apiKey = ApiKey::getDefaultKey($companyId);

        $vatRates = VatRate::filterSource()
            ->isActive()
            ->get();

        $m05 = VatRate::getBillingCode('M05');
        $vatNormal = Setting::get('vat_rate_normal');

        if (empty(Setting::get('vat_rate_normal')) || $vatRates->isEmpty() || !$m05) {
            return Redirect::back()->with('error', 'Taxas de IVA não configuradas.');
        }

        $m05 = VatRate::getBillingCode('M05');
        $document = new Document($apiKey);
        $taxes = $document->getTaxes();
        foreach ($taxes as $tax) {
            $taxId   = $tax->Key;
            $taxName = $tax->Info3;

            if (str_contains($taxName, 'M05')) {
                $m05 = $taxId;
            }
        }

        $items = [
            [
                'ref'           => 'NAC',
                'designation'   => 'Serviço de transporte Nacional',
                'short_name'    => 'STNAC',
                'tax_value'     => $vatNormal,
                'tax_id'        => '1', //23
                'price'         => '0',
                'is_service'    => '0',
            ],
            [
                'ref'           => 'ESP',
                'designation'   => 'Serviço de transporte Espanha',
                'short_name'    => 'STESP',
                'tax_value'     => '0',
                'tax_id'        => $m05,
                'price'         => '0',
                'is_service'    => '0',
            ],
            [
                'ref'           => 'INT',
                'designation'   => 'Serviço de transporte Internacional',
                'short_name'    => 'STINT',
                'tax_value'     => '0',
                'tax_id'        => $m05,
                'price'         => '0',
                'is_service'    => '0'
            ],
            [
                'ref'           => 'IMP',
                'designation'   => 'Serviço de transporte Importação',
                'short_name'    => 'STIMP',
                'tax_value'     => $vatNormal,
                'tax_id'        => '1',
                'price'         => '0',
                'is_service'    => '0'
            ],
            [
                'ref'           => 'EST',
                'designation'   => 'Serviço de Estafetagem',
                'short_name'    => 'STESTF',
                'tax_value'     => $vatNormal,
                'tax_id'        => '1',
                'price'         => '0',
                'is_service'    => '0'
            ],
            [
                'ref'           => 'EXP',
                'designation'   => 'Serviço expresso',
                'short_name'    => 'EXPR',
                'tax_value'     => $vatNormal,
                'tax_id'        => '1',
                'price'         => '0',
                'is_service'    => '0'
            ],
            [
                'ref'           => 'AVE',
                'designation'   => 'Avença mensal',
                'short_name'    => 'AVENC',
                'tax_value'     => $vatNormal,
                'tax_id'        => '1',
                'price'         => '0',
                'is_service'    => '0'
            ],
            [
                'ref'           => 'OTR',
                'designation'   => 'Outros serviços',
                'short_name'    => 'OUTROS',
                'tax_value'     => $vatNormal,
                'tax_id'        => '1',
                'price'         => '0',
                'is_service'    => '0'
            ]
        ];


        if (hasModule('invoices')) {

            foreach ($items as $item) {
                try {
                    $product = new Item($apiKey);
                    $product->insertOrUpdate($item);

                    $billingItem = new Item($apiKey);
                    unset($billingItem->api_key);
                    $billingItem->source        = config('app.source');
                    $billingItem->reference     = $item['ref'];
                    $billingItem->name          = $item['designation'];
                    $billingItem->short_name    = $item['short_name'];
                    $billingItem->tax_rate      = $item['tax_value'];
                    $billingItem->is_service    = $item['is_service'];
                    $billingItem->save();
                } catch (\Exception $e) {
                    return Redirect::back()->with('error', $e->getMessage());
                }
            }


            Setting::set('invoice_item_nacional_ref', $items[0]['ref']);
            Setting::set('invoice_item_nacional_desc', $items[0]['designation']);

            Setting::set('invoice_item_spain_ref', $items[1]['ref']);
            Setting::set('invoice_item_spain_desc', $items[1]['designation']);

            Setting::set('invoice_item_internacional_ref', $items[2]['ref']);
            Setting::set('invoice_item_internacional_desc', $items[2]['designation']);

            Setting::set('invoice_item_import_ref', $items[3]['ref']);
            Setting::set('invoice_item_import_desc', $items[3]['designation']);

            Setting::set('invoice_item_courier_ref', $items[4]['ref']);
            Setting::set('invoice_item_courier_desc', $items[4]['designation']);

            Setting::set('invoice_item_express_service_ref', $items[5]['ref']);
            Setting::set('invoice_item_express_service_desc', $items[5]['designation']);

            Setting::set('invoice_item_covenants_ref', $items[6]['ref']);
            Setting::set('invoice_item_covenants_desc', $items[6]['designation']);

            Setting::set('invoice_item_products_ref', $items[7]['ref']);
            Setting::set('invoice_item_products_desc', $items[7]['designation']);

            Setting::save();

            return Redirect::back()->with('success', 'Artigos sincronizados com sucesso.');
        } else {
            return Redirect::back()->with('error', 'Não possui ligação ao programa de faturação.');
        }
    }
}
