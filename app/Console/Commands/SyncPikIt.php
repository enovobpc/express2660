<?php

namespace App\Console\Commands;

use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\OperatorTask;
use App\Models\Route;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShipmentPackDimension;
use App\Models\ShippingStatus;
use App\Models\User;
use Illuminate\Console\Command;
use DB, File, Setting, Date;

class SyncPikIt extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:pikit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Pik-it orders DB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        
        $this->info("Sync Pik-it");

        $lastUpdate = Date::now();
        $lastUpdate->subMinutes(4);
        $lastUpdate = $lastUpdate->format('Y-m-d H:i').':00';

        //UPDATE STATUS
        $historyChanged = ShipmentHistory::with('shipment')
            ->where('status_id', 5)
            ->where('created_at', '>=', $lastUpdate)
            ->get();

        if($historyChanged) {

            foreach ($historyChanged as $history) {
                $orderId = @$history->shipment->reference;

                if($orderId) {
                    //echo $orderId.' -  ';
                    $sql = "update wpu6_wcfm_marketplace_orders set order_status='completed', commission_status='completed' where order_id=".$orderId;
                    DB::connection('mysql_wordpress')->statement($sql);

                    $sql = "update wpu6_posts set post_status='wc-completed' where id=".$orderId;
                    DB::connection('mysql_wordpress')->statement($sql);
                }
            }
        }

        //IMPORT NEW DATA
        $sql   = "select * from wpu6_posts where post_date >= '".$lastUpdate."'";
        $newOrders = DB::connection('mysql_wordpress')->select($sql);

        if(!empty($newOrders)) {

            //$this->importNewShops();

            $this->importOrders();
        }

        $this->info("Sync completed");
        return;
    }

    /**
     * IMPORT NEW USERS
     */
    public function importNewShops() {

        //$sql = "select * from wpu6_users where DATE(user_registered) = '".date('Y-m-d')."' order by id asc";
        $sql   = "select user_id from wpu6_usermeta where meta_key='wpu6_user_level' and meta_value=6";
        $users = DB::connection('mysql_wordpress')->select($sql);

        $insertData = [];
        foreach ($users as $user) {

            $userId = $user->user_id;
            $sql = "select * from wpu6_usermeta where user_id = " . $userId;
            $userDetails = DB::connection('mysql_wordpress')->select($sql);
            $userDetails = collect($userDetails);
            $userDetails = $userDetails->pluck('meta_value', 'meta_key')->toArray();

            if(@$userDetails['wpu6_user_level'] == 6) {
                $user = [
                    'billing_code' => $userId,
                    'name'      => @$userDetails['wcfmmp_store_name'],
                    'address'   => @$userDetails['_wcfm_street_1']. ' ' . @$userDetails['_wcfm_street_2'],
                    'zip_code'  => @$userDetails['_wcfm_zip'],
                    'city'      => @$userDetails['_wcfm_city'],
                    'country'   => strtolower(@$userDetails['_wcfm_country']),
                    'contact_email' => @$userDetails['_wcfm_email_verified_for'],
                    'responsable' => @$userDetails['first_name'] . ' ' . @$userDetails['last_name'],
                    'type_id'   => 1
                ];

                $insertData[] = $user;
            }
        }

        foreach ($insertData as $shop) {
            $customer = Customer::firstOrNew([
                'type_id' => 1,
                'billing_code' => $shop['billing_code']
            ]);
            $customer->fill($shop);
            $customer->source    = config('app.source');
            $customer->agency_id = 131;
            $customer->setCode();
        }
    }

        /**
     * Import orders
     */
    public function importOrders() {

        $date = date('Y-m-d');

        $vendors = Customer::where('type_id', 1)
            ->get([
                'id',
                'agency_id',
                'name',
                'address',
                'zip_code',
                'city',
                'country',
                'phone',
                'billing_code'
            ]);
        $vendors = $vendors->groupBy('billing_code')->toArray();


        $sql = "select order_id, customer_id, vendor_id, order_status, post_excerpt, payment_method, sum(quantity) as volumes, (sum(item_total) - sum(commission_amount)) as commission, created 
            from wpu6_wcfm_marketplace_orders inner join wpu6_posts on wpu6_wcfm_marketplace_orders.order_id = wpu6_posts.id
            where date(created) = '".$date."' 
            group by order_id";

        //and order_status='processing'

        $orders = DB::connection('mysql_wordpress')->select($sql);

        $insertData   = [];
        $vendorsIds   = [];
        $customersIds = [];
        foreach ($orders as $order) {

            //get order data
            $orderId = $order->order_id;
            $sql = "select * from wpu6_postmeta where post_id = " . $orderId;
            $orderDetails = DB::connection('mysql_wordpress')->select($sql);
            $orderDetails = collect($orderDetails);
            $orderDetails = $orderDetails->pluck('meta_value', 'meta_key')->toArray();

            $sql = "select quantity as qty, order_item_type, order_item_name as description 
                    from wpu6_woocommerce_order_items 
                    left join wpu6_wcfm_marketplace_orders on wpu6_woocommerce_order_items.order_item_id = wpu6_wcfm_marketplace_orders.item_id
                    where wpu6_woocommerce_order_items.order_id=" . $orderId;
            $items = DB::connection('mysql_wordpress')->select($sql);
            $items = json_decode(json_encode($items), true);

            $dims = [];
            $kms  = 0;
            foreach ($items as $item) {

                if($item['order_item_type'] == 'shipping') {
                    $kms = str_replace('Taxa de distância (', '', $item['description']);
                    $kms = str_replace(' km)', '', $kms);
                } else {
                    $dims[] = [
                        'qty'   => $item['qty'],
                        'type'  => 'cx',
                        'description' => $item['description']
                    ];
                }
            }


            $customersIds[] = $order->customer_id;
            $vendor = @$vendors[$order->vendor_id][0];

            $order = [
                'kms'            => $kms,
                'obs'            => $order->post_excerpt,
                'reference'      => $order->order_id,
                'created_at'     => $order->created,
                'total_price'    => @$order->commission + @$orderDetails['_order_shipping'],
                'order_status'   => $order->order_status,
                'payment_method' => $order->payment_method,
                'volumes'        => $order->volumes,
                'weight'         => '',
                'date'           => substr($order->created, 0, 10),
                'start_hour'     => substr($order->created, 11, 5),
                //'shipping_date'  => $order->created,

                //billing_info
                'billing_code'        => $order->customer_id,
                'billing_name'        => @$orderDetails['_billing_first_name']. ' '. @$orderDetails['_billing_last_name'],
                'billing_address'     => @$orderDetails['_billing_address_1'] . ' '. @$orderDetails['_billing_address_2'],
                'billing_zip_code'    => @$orderDetails['_billing_postcode'],
                'billing_city'        => @$orderDetails['_billing_city'],
                'billing_country'     => strtolower(@$orderDetails['_billing_country']),
                'billing_vat'         => @$orderDetails['_vat_number'] ? @$orderDetails['_vat_number'] : '999999990',

                //recipient_info
                'recipient_name'      => @$orderDetails['_shipping_first_name']. ' '. @$orderDetails['_shipping_last_name'],
                'recipient_address'   => @$orderDetails['_shipping_address_1'] . ' '. @$orderDetails['_shipping_address_2'],
                'recipient_zip_code'  => @$orderDetails['_shipping_postcode'],
                'recipient_city'      => @$orderDetails['_shipping_city'],
                'recipient_country'   => strtolower(@$orderDetails['_shipping_country']),
                'recipient_email'     => @$orderDetails['_billing_email'],
                'recipient_phone'     => @$orderDetails['_billing_phone'],

                //recipient_info
                'sender_id'        => @$vendor['id'],
                'sender_name'      => @$vendor['name'],
                'sender_address'   => @$vendor['address'],
                'sender_zip_code'  => @$vendor['zip_code'],
                'sender_city'      => @$vendor['city'],
                'sender_country'   => @$vendor['country'],
                'sender_phone'     => @$vendor['phone'],
                'agency_id'        => @$vendor['agency_id'],
                'sender_agency_id' => @$vendor['agency_id'],

                'dimensions' => $dims
            ];

            $insertData[] = $order;
        }

        $customers = Customer::where('type_id', 2)
            ->whereIn('billing_code', $vendorsIds)
            ->get([
                'id',
                'agency_id',
                'name',
                'address',
                'zip_code',
                'city',
                'country',
                'phone',
                'billing_code'
            ]);
        $customers = $customers->groupBy('billing_code')->toArray();

        foreach ($insertData as $data) {

            $shipment = Shipment::firstOrNew([
                'reference' => $data['reference']
            ]);

            if(!$shipment->exists) {
                $shipment->fill($data);

                //1. GET AGENCY FROM ZIP CODE
                //detect route
                $route = Route::getRouteFromZipCode($shipment->recipient_zip_code);
                $shipment->route_id    = @$route->id;
                $shipment->operator_id = @$route->operator_id;

                $zipCodeInfo = Shipment::getAgencyByZipCode($shipment->recipient_zip_code);
                $shipment->provider_id = @$zipCodeInfo->provider_id ? $zipCodeInfo->provider_id : Setting::get('shipment_default_provider');
                $shipment->zone        = @$zipCodeInfo->zone;
                $shipment->recipient_agency_id = @$zipCodeInfo->agency_id ? @$zipCodeInfo->agency_id : @$shipment->agency_id;
                $shipment->provider_id = @$zipCodeInfo->provider_id ? @$zipCodeInfo->provider_id : @$shipment->provider_id;

                //1. VERIFY CUSTOMER
                if(!@$customers[$data['billing_code']]) {
                    $customer = Customer::firstOrNew([
                        'type_id'      => 2,
                        'billing_code' => $data['billing_code']
                    ]);

                    $customer->fill($data);
                    $customer->name         = $data['recipient_name'];
                    $customer->address      = $data['recipient_address'];
                    $customer->zip_code     = $data['recipient_zip_code'];
                    $customer->city         = $data['recipient_city'];
                    $customer->country      = $data['recipient_country'];
                    $customer->phone        = $data['recipient_phone'];
                    $customer->contact_email= $data['recipient_email'];
                    $customer->billing_name = $data['recipient_name'];
                    $customer->vat          = $customer->vat ? $customer->vat : '999999990';
                    $customer->agency_id    = $shipment->recipient_agency_id;
                    $customer->setCode();
                } else {
                    $shipment->recipient_id = @$customers[$data['billing_code']];
                }

                $shipment->customer_id      = $customer->id;
                $shipment->service_id       = 1; //FOOD delivery
                $shipment->weight           = 2;
                $shipment->status_id        = ShippingStatus::PENDING_ID;
                unset($shipment->dimensions);

                //dd($shipment->toArray());
                $shipment->setTrackingCode();

                if($data['dimensions']) {
                    foreach ($data['dimensions'] as $item) {
                        $dimension = new ShipmentPackDimension();
                        $dimension->fill($item);
                        $dimension->shipment_id = $shipment->id;
                        $dimension->save();
                    }
                }


                //SEND NOTIFICATION BY EMAIL
                if(!empty($shipment->recipient_email)) {
                    //$shipment->sendEmail();
                }

                //SAVE TRACKING HISTORY
                $history = new ShipmentHistory();
                $history->shipment_id = $shipment->id;
                $history->status_id   = empty(Setting::get('shipment_status_after_create')) ? ShippingStatus::PENDING_ID : Setting::get('shipment_status_after_create');
                $history->agency_id   = $shipment->agency_id;
                $history->save();


                //SET NOTIFICATION TO OPERATORS
                if(Setting::get('shipment_notify_operator') || Setting::get('mobile_app_notify_all_operators')) {

                    try {
                        $task = OperatorTask::where('source', config('app.source'))
                            ->where('concluded', 0)
                            ->where('customer_id', $shipment->customer_id)
                            ->where('date', $shipment->date)
                            ->whereRaw('DATE(last_update) = "' . date('Y-m-d') . '"')
                            ->first();

                        $taskExists = true;
                        $notifyOperator = false;
                        if (!$task) {
                            $taskExists = false;
                            $notifyOperator = true;
                        }

                        $operators = User::where('agencies', 'like', '%"' . $customer->agency_id . '"%')
                            ->isOperator();

                        if(!Setting::get('mobile_app_notify_all_operators') && $shipment->operator_id) { //notifica apenas o motorista que faz a recolha
                            $operators = $operators->where('id', $shipment->operator_id);
                        }

                        $operators = $operators->orderBy('name', 'asc')
                            ->pluck('id')
                            ->toArray();

                        $details = '';
                        foreach ($shipment->pack_dimensions as $dimension) {
                            $details.= $dimension->qty . 'x ' . $dimension->description.'<br/>';
                        }

                        if (!$task) {
                            $task = new OperatorTask();
                        }

                        $shipmentsArr = empty($task->shipments) ? [] : $task->shipments;
                        array_push($shipmentsArr, $shipment->id);

                        $task->source      = config('app.source');
                        $task->last_update = date('Y-m-d H:i:s');
                        $task->name        = $shipment->sender_name;
                        $task->description = '';
                        $task->details     = br2nl(($task->details ? $task->details . '<br/>' . $details : $details));
                        $task->address     = br2nl($shipment->sender_address . ' ' . $shipment->sender_zip_code . ' ' . $shipment->sender_city.'<br/>Contacto: '.$shipment->sender_phone);
                        $task->operators   = $operators;
                        $task->customer_id = $shipment->customer_id;
                        $task->volumes     = $task->volumes + $shipment->volumes;
                        $task->weight      = $task->weight + $shipment->weight;
                        $task->deleted     = 0;
                        $task->shipments   = $shipmentsArr;
                        $task->date        = $shipment->date;
                        $task->save();

                        if ($notifyOperator && Setting::get('mobile_app_notifications')) {
                            if(Setting::get('mobile_app_notify_all_operators')) {
                                $task->notifyAllOperators();
                            } else if($shipment->operator_id) {
                                $task->setNotification(BroadcastPusher::getChannel(@$shipment->operator_id)); //PARA OS TELEMÓVEIS ESTÁ A SER NOTIFICADO O channel-xxx e não o channel-operator-xxx
                            }
                        }

                        //se a tarefa existe e já foi aceite, marca o envio criado como "em recolha"
                        if($taskExists && $task->readed && !$task->concluded && !$task->deleted) {
                            $history = new ShipmentHistory();
                            $history->shipment_id = $shipment->id;
                            $history->agency_id   = $shipment->agency_id;
                            $history->status_id   = ShippingStatus::IN_PICKUP_ID;
                            $history->operator_id = @$task->operator_id;
                            $history->save();

                            $shipment->status_id = ShippingStatus::IN_PICKUP_ID;
                            $shipment->save();
                        }
                    } catch (\Exception $e) {
                        dd($e->getMessage(). ' line ' . $e->getLine(). ' msg '. $e->getMessage());
                    }
                }
            }
        }
    }
}
