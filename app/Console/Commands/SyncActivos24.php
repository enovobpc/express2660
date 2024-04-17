<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\InvoiceGateway\OnSearch\Item;
use App\Models\Logistic\Category;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductImage;
use App\Models\Logistic\SubCategory;
use Illuminate\Console\Command;
use DB, File;

class SyncActivos24 extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:activos24';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync ACTIVOS 24 logistic DB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


        $this->info("Sync ACTIVOS24");

        $sql = "SELECT TOP 15000 dbo.OnS3StockArtigos.*, dbo.ItemMaster.ClientRef, ItemMaster.ItemValue, dbo.ItemMaster.ItemWeight, 
            dbo.ItemMaster.ItemWidth, dbo.ItemMaster.ItemHeight, 
            dbo.ItemMaster.ItemLength, dbo.ItemMaster.StorageLocation,
            dbo.ItemMaster.ModifDateTime, dbo.ItemMaster.stkMin, dbo.ItemMaster.MinTemp,
            dbo.ItemMaster.CategoryId, dbo.ItemMaster.SubCategoryId,
            dbo.Brands.BrandDesc, dbo.Brands.BrandId
            FROM dbo.OnS3StockArtigos 
            INNER JOIN dbo.ItemMaster on dbo.OnS3StockArtigos.Ref = dbo.ItemMaster.ItemID
            INNER JOIN dbo.Brands on dbo.ItemMaster.BrandID = dbo.Brands.BrandID";

        if (1) {
            //$sql.= " WHERE dbo.OnS3StockArtigos.Ref = '7144COFFAF'";
            //$sql.= " WHERE dbo.OnS3StockArtigos.Ref = '5601226041669'"; //5601164113701
        }

        if (1) {
            //$sql.= " WHERE dbo.ItemMaster.ModifDateTime >= '2020-06-06 00:00:00.000'";
        }

        if (1) {
            //$sql.= " WHERE dbo.OnS3StockArtigos.lot = '1COFFTREN006221'";
        }

        $sql .= " ORDER BY dbo.ItemMaster.ModifDateTime DESC";

        $products = DB::connection('dbActivos24')->select($sql);



        //set all products with stock = 0
        // Product::filterSource()
        //     ->update([
        //         'stock_total'     => 0,
        //         'stock_allocated' => 0
        //     ]);

        $updateArr = [];
        $customers = [];
        foreach ($products as $product) {

            /*if($product->Ref == '5601226041669') {
                dd($product);
            }*/

            $customerCode = $product->cl;

            $customers[$customerCode] = @$customers[$customerCode] ? $customers[$customerCode] + 1 : 1;

            $validade = $product->lotValidade ? substr($product->lotValidade, 0, 10) : '';
            $entrada  = $product->lotEntrada  ? substr($product->lotEntrada, 0, 10) : '';
            $validade = $validade == '1900-01-01' ? null : $validade;
            $entrada  = $entrada == '1900-01-01' ? null : $entrada;

            $stockStatus = 'available';
            $stockStatus = $product->inactivo ? 'blocked' : $stockStatus;
            $stockUnity  = $this->getUnity($product->unidade);

            $unitiesByPack = $product->QtyVolumesUnits;
            if ($product->Qty && $product->Qty > 0) {
                $unitiesByPack = $product->QtyVolumesUnits / $product->Qty;
            }

            if ($product->Obs != 'BLOQUEADO TEMPORARIAMENTE') {
                $updateArr[] = [
                    'customer_code'     => $customerCode,
                    'warehouse_id'      => ($product->WHID == '2' ? 1 : 2), //2=lisboa, 6=porto
                    'sku'               => $product->Ref,
                    'customer_ref'      => $product->ClientRef,
                    'name'              => $product->design,
                    'has_lote'          => $product->Usalote,
                    'serial_no'         => strtoupper($product->serie),
                    'lote'              => strtoupper($product->Lot),
                    'expiration_date'   => $validade,
                    'production_date'   => $entrada,
                    'stock_total'       => $product->Qty,
                    'stock_allocated'   => null,
                    'stock_min'         => $product->stkMin,
                    'price'             => $product->ItemValue,
                    'vat'               => $product->Taxa,
                    'warewhouse'        => $product->u_arm,
                    'unity'             => $stockUnity,
                    'is_active'         => !$product->inactivo,
                    'obs'               => $product->Obs,
                    'photo_url'         => $product->imagem,
                    'brand_name'        => $product->BrandDesc,
                    //'category_id'       => $product->CategoryID,
                    //'status'            => $product->Status,
                    'stock_status'      => $stockStatus,
                    'weight'            => $product->ItemWeight * 0.001,
                    'height'            => $product->ItemHeight,
                    'width'             => $product->ItemWidth,
                    'length'            => $product->ItemLength,
                    'master_location'   => $product->StorageLocation,
                    'last_update'       => date('Y-m-d H:i:s'), //$product->ModifDateTime,
                    'need_validation'   => $product->MinTemp,
                    'category_id'       => $product->CategoryId,
                    'subcategory_id'    => $product->SubCategoryId,
                    'unities_by_pack'   => $unitiesByPack,
                ];
            }
        }

        $customers = Customer::whereIn('code', array_keys($customers))->pluck('id', 'code')->toArray();

        $ids = [];
        foreach ($updateArr as $item) {

            $customerId = @$customers[$item['customer_code']];

            if (empty($item['category_id']) || empty($item['subcategory_id'])) {
                $item['category_id']    = null;
                $item['subcategory_id'] = null;
            } else {
                // Populate category_id with correct id
                $category = Category::firstOrNew([
                    'source'      => config('app.source'),
                    'name'        => $item['category_id'],
                    'customer_id' => $customerId
                ]);
                if (!$category->exists) {
                    $category->save();
                }
                $item['category_id'] = $category->id;
                //--

                // Populate subcategory_id with correct id
                $subCategory = SubCategory::firstOrNew([
                    'source'      => config('app.source'),
                    'name'        => $item['subcategory_id'],
                    'customer_id' => $customerId,
                    'category_id' => $item['category_id']
                ]);
                if (!$subCategory->exists) {
                    $subCategory->save();
                }
                $item['subcategory_id'] = $subCategory->id;
                //--
            }

            $product = Product::firstOrNew([
                'customer_id'   => $customerId,
                'warehouse_id'  => $item['warehouse_id'],
                'sku'           => $item['sku'],
                'serial_no'     => $item['serial_no'],
                'lote'          => $item['lote'],
            ]);

            $product->fill($item);
            $product->customer_id = $customerId;
            $product->save();

            $ids[] = $product->id;
        }

        Product::whereNotIn('id', $ids)
            ->update([
                'stock_total'     => 0,
                'stock_allocated' => 0
            ]);

        //Product::whereIn('id', $ids)->update(['source' => 'activos24']);

        /**
         * SYNC IMAGES
         */
        $maxPages = 100;

        for ($page = 1; $page <= $maxPages; $page++) {
            $products = new Item();
            $products = $products->listsItems($page);


            if (!empty($products)) {
                foreach ($products as $key => $image) {

                    /*if($image['ItemID'] == 'ZDGTL21VA0001') {
                        dd($image['ItemID']);
                    }*/

                    if (@$image['ItemImages']) {


                        $sku        = $image['ItemID'];
                        $serialNo   = $image['SerialNum'] ? $image['SerialNum'] : '';
                        $lote       = $image['Lots'] ? $image['Lots'] : '';

                        echo '<hr/>PAGE ' . $page . ' - SKU ' . $sku . ' - ';

                        //get product from DB
                        $products = Product::where('sku', $sku)->get();

                        foreach ($products as $product) { //caso exista mais que um produto com a mesma SKU
                            //insert image if not exists
                            if (@$product && !$product->filepath) {
                                $folder = ProductImage::DIRECTORY;

                                if (!File::exists(public_path($folder))) {
                                    File::makeDirectory(public_path($folder));
                                }



                                echo 'IMAGEM NAO GRAVADA - ' . $image['ItemImages'][0]['FileURL'] . '<br/>';

                                $filecontent = file_get_contents($image['ItemImages'][0]['FileURL']);


                                //$filename  = $image['ItemImages'][0]['Description'];
                                $extension = @$image['ItemImages'][0]['FileExtension'];
                                $extension = empty($extension) ? '.png' : $extension;

                                $filename = strtolower(str_random(8) . $extension);
                                $filepath = $folder . '/' . $filename;

                                $result = File::put(public_path($filepath), $filecontent);

                                if ($result) {
                                    $productImage = new ProductImage();
                                    $productImage->product_id = $product->id;
                                    $productImage->filepath = $filepath;
                                    $productImage->filename = $filename;
                                    $productImage->is_cover = true;
                                    $productImage->save();

                                    $product->filehost = env('APP_URL');
                                    $product->filepath = $filepath;
                                    $product->filename = $filename;
                                    $product->save();
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->info("Sync completed");
        return;
    }

    public function getUnity($unity)
    {

        $unity = strtolower($unity);

        if ($unity == 'un') {
            return 'unity';
        } elseif ($unity == 'cx') {
            return 'box';
        } elseif ($unity == 'mt') {
            return 'meter';
        }

        return $unity;
    }
}
