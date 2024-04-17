<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\Billing\Item;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Setting;

class BillingItemsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers']);
    }

    public function list(Request $request) {
        $items = Item::filterSource()
            ->filterRequest($request)
            ->with(['provider', 'brand', 'brandModel'])
            ->get();

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_top'    => 28,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'documentTitle'     => 'Listagem Artigos Faturação',
            'documentSubtitle'  => date('Y-m-d H:i'),
            'view'              => 'admin.printer.billing.items.list',
            'items' => $items
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->Output('Listagem Artigos Faturação.pdf', 'I');
    }
}
