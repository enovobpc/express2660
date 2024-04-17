<?php

namespace App\Http\Controllers\Admin;

use Html, Croppa, Response, DB, File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductImageImage;

class IncidencesImagesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_incidences';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_incidences']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function index($productId) {
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create() {
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $productId) {
        
        $input = $request->file('file');

        $message = 'Os ficheiros foram anexados ao produto.';
        $status = 'success';

        $image = new ProductImage;
        $image->product_id = $productId;

        if (!$image->upload($input, true,  40, [])) {
            return Response::json('error', 400);
        }

        $image->html = view('admin.products.partials.photo', array('image' => $image))->render();

        return Response::json($image, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id) {
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id) {
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id) {
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId, $id) {
        
        $image = ProductImage::find($id);
        $file  = $image->filepath;

        if($image->is_cover) {
            Product::whereId($productId)
                  ->update([
                      'filepath' => null,
                      'filename' => null
                  ]);
        }
        
        $result = $image->delete();
        
        if ($result) {
            if(File::exists($file)) {
                Croppa::delete($file);
            }
            return Redirect::back()->with('success', 'Imagem removida com sucesso.');
        }
        
        return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a imagem.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/features/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $errors = false;
        foreach($ids as $id) {
            $image = ProductImage::where('id', $id)->first();

            if(File::exists($image->filepath)) {
                Croppa::delete($image->filepath);
            }

            $result = $image->delete();
            
            if(!$result) {
                $errors = true;
                exit;
            }
        }
        
        if ($errors) {
            return Redirect::back()->with('error', 'Não foi possível remover todos os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Update the specified resource order in storage.
     * POST /admin/features/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        $result = ProductImage::setNewOrder($request->ids);
        
        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }
    
   /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setCover($productId, $id) {

        ProductImage::where('product_id', $productId)
                    ->update(['is_cover' => 0]);
        
        $image = ProductImage::where('product_id', $productId)
                        ->where('id', $id)
                        ->firstOrFail();
        $image->is_cover = 1;
        $image->save();
        
        $product = Product::find($productId);
        $product->filepath = $image->filepath;
        $product->filename = $image->filename;
        $product->save();

        return Redirect::back()->with('success', 'Imagem principal alterada com sucesso');
    }
}
