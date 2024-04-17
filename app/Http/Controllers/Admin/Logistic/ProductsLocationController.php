<?php

namespace App\Http\Controllers\Admin\Logistic;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Logistic\Location;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\ProductHistory;
use App\Models\Logistic\Product;
use Html, Croppa, Auth, App;
use Illuminate\Support\Facades\Redirect;

class ProductsLocationController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_products';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_products']);
    }

    /**
     * Show the modal to edit stock transfer
     *
     * @return \Illuminate\Http\Response
     */
    public function editReception($productId) {

        $product = Product::filterSource()->whereId($productId)->firstOrFail();

        $locations = Location::filterSource()
            ->pluck('code', 'id')
            ->toArray();

        return view('admin.logistic.products.modals.reception', compact('product','locations'))->render();
    }

    /**
     * Save stock transfer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveReception(Request $request, $productId) {

        $input = $request->all();

        $product = Product::filterSource()->whereId($productId)->firstOrFail();

        $locations = Location::filterSource()->whereIn('id', $input['location'])->get();

        foreach ($locations as $key => $location) {
            if(!empty($input['qty'][$key])) {

                $newLocation = ProductLocation::firstOrNew([
                    'location_id' => $location->id,
                    'product_id'  => $product->id
                ]);

                $newLocation->product_id  = $product->id;
                $newLocation->location_id = $location->id;
                $newLocation->stock       = $newLocation->stock + @$input['qty'][$key];
                $newLocation->save();

                $newLocation->location->status = 'filled';
                $newLocation->location->save();

            }
        }

        $product->updateStockTotal();

        return [
            'result'   => true,
            'feedback' => 'Stock adicionado com sucesso.',
            'html'     => view('admin.logistic.products.partials.locations_table', compact('product'))->render()
        ];
    }

    /**
     * Show the modal to edit stock transfer
     *
     * @return \Illuminate\Http\Response
     */
    public function editTransfer($productId, $id) {

        $stock = ProductLocation::where('product_id', $productId)
                ->where('location_id', $id)
                ->firstOrFail();

        $locations = Location::filterSource()
            ->where('id', '<>', $id)
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();

        return view('admin.logistic.products.modals.transfer_stock', compact('stock', 'locations'))->render();
    }

    /**
     * Save stock transfer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveTransfer(Request $request, $productId, $id) {

        $input = $request->all();

        $locations = Location::filterSource()->whereIn('id', $input['location'])->get();

        $product = Product::filterSource()->whereId($productId)->first();

        $oldLocation = ProductLocation::where('product_id', $productId)
            ->where('location_id', $id)
            ->firstOrFail();


        $totalTransfered = 0;

        foreach ($locations as $key => $location) {
            if(!empty($input['qty'][$key])) {

                $newLocation = ProductLocation::firstOrNew([
                    'location_id' => $location->id,
                    'product_id'  => $productId
                ]);

                $newLocation->product_id  = $productId;
                $newLocation->location_id = $location->id;
                $newLocation->stock       = $newLocation->stock + @$input['qty'][$key];
                $newLocation->setBarcode();

                $newLocation->location->status = 'filled';
                $newLocation->location->save();

                $totalTransfered+= @$input['qty'][$key];

                //Save History
                $history = new ProductHistory();
                $history->product_id     = $product->id;
                $history->action         = 'transfer';
                $history->source_id      = $oldLocation->location_id;
                $history->destination_id = $newLocation->location_id;
                $history->qty            = $totalTransfered;
                $history->save();
            }
        }

        //remove stock from current location
        $stockCurrentLocation = $oldLocation->stock - $totalTransfered;
        $oldLocation->updateStock($stockCurrentLocation);

        $product->updateStockTotal();

        return [
            'result'   => true,
            'feedback' => 'Stock transferido com sucesso.',
            'html'     => view('admin.logistic.products.partials.locations_table', compact('product'))->render()
        ];
    }

    /**
     * Show modal to add stock to a product
     * @param $productId
     * @return string
     * @throws \Throwable
     */
    public function addNewStock(Request $request) {

        $product = null;

        if($request->has('product')) {
            $product = Product::filterSource()->whereId($request->get('product'))->firstOrFail();
        }

        $locations = Location::filterSource()
            ->pluck('code', 'id')
            ->toArray();

        return view('admin.logistic.products.modals.add_stock', compact('product','locations'))->render();
    }

    /**
     * Store Stock Location
     *
     * @param Request $request
     * @param $productId
     * @param $id
     * @return array
     * @throws \Throwable
     */
    public function storeNewStock(Request $request) {

        $input = $request->all();

        $product = Product::filterSource()->find($input['product_id']);

        foreach ($input['location'] as $key => $locationId) {

            $availableQty = (int) @$input['qty'][$key];
            $allocatedQty = (int) @$input['allocated'][$key];

            if(!empty($locationId) && !empty($availableQty)) {

                $productLocation = ProductLocation::firstOrNew([
                    'location_id' => $locationId,
                    'product_id'  => $product->id
                ]);

                if ($productLocation->exists) {
                    $productLocation->stock          += $availableQty + $allocatedQty;
                    $productLocation->stock_available+= $availableQty;
                    $productLocation->stock_allocated+= $allocatedQty;
                } else {
                    $productLocation->product_id      = $product->id;
                    $productLocation->location_id     = $locationId;
                    $productLocation->stock           = ($availableQty + $allocatedQty);
                    $productLocation->stock_available = $availableQty;
                    $productLocation->stock_allocated = $allocatedQty;
                    $productLocation->setBarcode();
                }

                $productLocation->save();

                $productLocation->location->status = 'filled';
                $productLocation->location->save();

                //Save History
                $history = new ProductHistory();
                $history->product_id     = $product->id;
                $history->action         = 'add';
                $history->source_id      = null;
                $history->destination_id = $locationId;
                $history->qty            = $availableQty;
                $history->save();
            }
        }

        $product->updateStockTotal();

        return Redirect::back()->with('success', 'Stock adicionado com sucesso.');
    }
}
