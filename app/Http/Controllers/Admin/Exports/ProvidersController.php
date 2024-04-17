<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Provider;
use Auth, Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProvidersController extends \App\Http\Controllers\Admin\Controller {

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',providers']);
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
            'Forma de Pagamento',
            'Pessoa Contacto',
            'Categoria',
            'Ativo'
        ];

        try {
            $data = Provider::with('category')
                ->filterAgencies();

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            } else {

                //filter agency
                $value = $request->agency;
                if($request->has('agency')) {
                    $data = $data->where('agencies', 'like', '%"'.$value.'"%');
                }
                
                //filter code
                $value = $request->code;
                if($request->has('code')) {
                    $data = $data->where('code', $value);
                }

                //filter active
                $value = $request->active;
                if($request->has('active')) {
                    $data = $data->where('is_active', $value);
                }

                //filter country
                $value = $request->country;
                if($request->has('country')) {
                    $data = $data->where('country', $value);
                }

                //filter category
                $value = $request->category;
                if($request->has('category')) {
                    $data = $data->whereIn('category_id', $value);
                }

                //filter payment method
                $value = $request->payment_method;
                if($request->has('payment_method')) {
                    $data = $data->whereIn('payment_method', $value);
                }
            }

            if (Auth::user()->isGuest()) {
                $data = $data->where('agency_id', '99999'); //hide data to gest agency role
            }

            $data = $data->get();

            Excel::create('Listagem de Fornecedores', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $provider) {

                        $rowData = [
                            $provider->code,
                            $provider->company ? $provider->company : $provider->name,
                            $provider->vat,
                            $provider->address,
                            $provider->zip_code,
                            $provider->city,
                            $provider->country,
                            $provider->phone,
                            $provider->mobile,
                            $provider->email,
                            $provider->payment_method ? @$provider->paymentCondition->name : '',
                            $provider->responsable,
                            @$provider->category->name,
                            $provider->is_active ? 'Sim' : 'Não'
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
