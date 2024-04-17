<?php

namespace App\Http\Controllers\Admin\Exports;

use Auth, Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Customer;
use App\Models\CustomerRecipient;

class CustomersController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Store last row of each iteration
     *
     * @var type
     */
    protected $lastRow = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers']);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $ids = $request->id;

        $header = [
            'Agência',
            'Rota',
            'Código',
            'Nome',
            'NIF',
            'Morada',
            'Código Postal',
            'Localidade',
            'País',
            'Telefone',
            'Telemóvel',
            'E-mail',
            'Designação Social',
            'Morada Faturação',
            'Código Postal Faturação',
            'Localidade Faturação',
            'País Faturação',
            'Forma de Pagamento',
            'Pessoa Contacto',
            'Categoria',
            'Comercial',
            'Tabela Preços'
        ];

        try {
            $data = Customer::with('route', 'agency', 'seller', 'type')
                ->filterAgencies()
                ->filterSeller()
                ->isProspect(false)
                ->isDepartment(false);

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            } else {
                //filter active
                $value = $request->active;
                if ($request->has('active')) {
                    $data = $data->where('active', $value);
                }

                //filter type
                $value = $request->type_id;
                if ($request->has('type_id')) {
                    $data = $data->where('type_id', $value);
                }

                //filter country
                $value = $request->country;
                if ($request->has('country')) {
                    $data = $data->where('country', $value);
                }

                //filter agency
                $value = $request->agency;
                if ($request->has('agency')) {
                    $data = $data->where('agency_id', $value);
                }

                //filter seller
                $value = $request->seller;
                if ($request->has('seller')) {
                    $data = $data->where('seller_id', $value);
                }

                //filter payment method
                $value = $request->payment_method;
                if ($request->has('payment_method')) {
                    $data = $data->where('payment_method', $value);
                }

                //filter prices
                $value = $request->prices;
                if ($request->has('prices')) {
                    if ($value == '-1') {
                        $data = $data->where('has_prices', 0);
                    } elseif ($value == '0') {
                        $data = $data->where('has_prices', 1)->whereNull('price_table_id');
                    } else {
                        $data = $data->where('has_prices', 1)
                            ->where('price_table_id', $value);
                    }
                }

                //filter route
                $value = $request->route;
                if ($request->has('route')) {
                    if ($value == '0') {
                        $data = $data->whereNull('route_id');
                    } else {
                        $data = $data->where('route_id', $value);
                    }

                }

                //filter webservices
                $value = $request->webservices;
                if ($request->has('webservices')) {
                    $data = $data->where('has_webservices', $value);
                }

                //filter login
                $value = $request->login;
                if ($request->has('login')) {
                    if ($value == '1') {
                        $data = $data->whereNotNull('password');
                    } else {
                        $data = $data->whereNull('password');
                    }

                }

                //filter login
                $value = $request->billing_code;
                if ($request->has('billing_code')) {
                    if ($value == '1') {
                        $data = $data->where(function ($q) {
                            $q->whereNotNull('billing_code');
                            $q->orWhere('billing_code', '<>', '');
                        });
                    } else {
                        $data = $data->where(function ($q) {
                            $q->whereNull('billing_code');
                            $q->orWhere('billing_code', '');
                        });
                    }

                }
            }

            if (Auth::user()->isGuest()) {
                $data = $data->where('agency_id', '99999'); //hide data to gest agency role
            }

            $data = $data->get();

            Excel::create('Listagem de Clientes', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $shipment) {

                        $rowData = [
                            @$shipment->agency->name,
                            @$shipment->route->name,
                            $shipment->code,
                            $shipment->name,
                            $shipment->vat,
                            $shipment->address,
                            $shipment->zip_code,
                            $shipment->city,
                            $shipment->country,
                            $shipment->phone,
                            $shipment->mobile,
                            $shipment->email,
                            $shipment->billing_name,
                            $shipment->billing_address,
                            $shipment->billing_zip_code,
                            $shipment->billing_city,
                            $shipment->billing_country,
                            $shipment->payment_method ? @$shipment->paymentCondition->name : '',
                            $shipment->responsable,
                            @$shipment->type->name,
                            @$shipment->seller->name,
                            @$shipment->prices_table_id ? $shipment->prices_table->name : 'Personalizada'
                        ];
                        $sheet->appendRow($rowData);
                    }
                });

            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Export customer recipients
     * @param Request $request
     * @param $customerId
     */
    public function recipients(Request $request, $customerId)
    {

        try {
            $ids = $request->id;

            $customer = Customer::find($customerId);

            $header = [
                'Código',
                'Nome',
                'Morada',
                'Código Postal',
                'Localidade',
                'País',
                'Telefone',
                'E-mail',
                'Pessoa de Contacto'
            ];

            $data = CustomerRecipient::where('customer_id', $customerId);

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            }

            $data = $data->get();

            Excel::create('Moradas Frequentes - ' . $customer->name, function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $shipment) {

                        $rowData = [
                            $shipment->code,
                            $shipment->name,
                            $shipment->address,
                            $shipment->zip_code,
                            $shipment->city,
                            $shipment->country,
                            $shipment->phone,
                            $shipment->email,
                            $shipment->responsable,
                        ];
                        $sheet->appendRow($rowData);
                    }
                });

            })->export('xls');

        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }
}
