<?php

namespace App\Http\Controllers\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use App\Models\EventManager;
use App\Models\EventProductLine;
use App\Models\Logistic\Product;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;

class EventsManagerController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'event-manager';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //  || !Setting::get('show_customers_ballance')
        if (!hasModule('events_management')) {
            return App::abort(404);
        }
    }

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request)
    {
        // $customer = Auth::guard('customer')->user();
        return $this->setContent('account.event_manager.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $event = new EventManager();

        $action = 'Adicionar Evento';
        $hours = listHours(1);

        $formOptions = array('route' => array('account.event-manager.store'), 'method' => 'POST', 'class' => 'form-event-manager');

        $data = compact(
            'event',
            'action',
            'formOptions',
            'hours'
        );

        return view('account.event_manager.edit', $data)->render();
    }

    public function store(Request $request)
    {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     $customer = Auth::guard('customer')->user();
    //     $ticket = Ticket::filterSource()
    //         ->where('customer_id', $customer->id)
    //         ->where('code', $id)
    //         ->firstOrFail();
    //     return $this->setContent('account.event_manager.show', compact('ticket'))->render();
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Auth::guard('customer')->user();
        $event = EventManager::where('customer_id', $customer->id)
            ->findOrfail($id);

        $action = 'Editar evento';
        $hours = listHours(1);

        $formOptions = array('route' => array('account.event-manager.update', $event->id), 'method' => 'PUT', 'class' => 'form-event-manager');

        $data = compact(
            'event',
            'action',
            'formOptions',
            'hours'
        );

        return view('account.event_manager.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $input = $request->all();
        $id =  empty($id) ? ($input['event_id'] ?? null) : null; // When is already on sistem but the method comes from store
        $isAjax = $request->get('ajax', false);
        $customer = Auth::guard('customer')->user();

        $eventManager = EventManager::where('customer_id', $customer->id)
            ->findOrNew($id);
        $input["customer_id"] = $customer->id;

        if (!$eventManager->validate($input)) {
            return Redirect::back()->withInput()->with('error', $eventManager->errors()->first());
        }
        if ($eventManager->is_draft == '0') {
            return Redirect::back()->withInput()->with('error', 'Não pode gravar rascunhos');
        }

        $eventManager->fill($input);
        $eventManager->horary = [
            "start_date" => $input['start_date'] ?? null,
            "start_hour" => $input['start_hour'] ?? null,
            "end_date"   => $input['end_date'] ?? null,
            "end_hour"   => $input['end_hour'] ?? null,
        ];
        $eventManager->save();

        return ($isAjax) ? $eventManager->id : Redirect::back()->with('success', 'Dados gravados com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Auth::guard('customer')->user();
        EventManager::flushCache(EventManager::CACHE_TAG);

        $event = EventManager::where('customer_id', $customer->id)
            ->findOrFail($id);

        if (empty($event) || !$event->is_draft) {
            return Redirect::back()->with('error', 'Não é possível eliminar o Evento porque já não se encontra como rascunho.');
        }
        $result = $event->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o pedido.');
        }

        return Redirect::back()->with('success', 'Pedido removido com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $data = EventManager::where('customer_id', $customer->id)
            ->select();

        //filter created_at
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('created_at', [$dtMin . ' 00:00:00', $dtMax . ' 23:59:59']);
        }

        //filter types
        $value = $request->get('types');
        if ($request->has('types')) {
            $data->where('type', $value);
        }

        //filter status
        $value = $request->get('status');
        if ($request->has('status')) {
            switch ($value) {
                case 'draft':
                    $data->where('is_draft', 1);
                    break;
                case 'active':
                    $data->where('is_active', 1);
                    break;
                case 'inactive':
                    $data->where(function ($q) {
                        $q->where('is_active', 0);
                        $q->where('is_draft', 0);
                    });
                    break;
                default:
                    break;
            }
        }

        return Datatables::of($data)
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('account.event_manager.datatables.name', compact('row'))->render();
            })
            ->edit_column('type', function ($row) {
                return view('account.event_manager.datatables.type', compact('row'))->render();
            })
            ->edit_column('start_date', function ($row) {
                return view('account.event_manager.datatables.date_start', compact('row'))->render();
            })
            ->edit_column('end_date', function ($row) {
                return view('account.event_manager.datatables.date_end', compact('row'))->render();
            })
            ->edit_column('is_active', function ($row) {
                return view('account.event_manager.datatables.active', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('account.event_manager.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('account.event_manager.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Add product and saves the event if not created
     *
     * @return type
     */
    public function updateEventLine(Request $request, $eventId, $lineId = null)
    {
        $input = $request->toArray();

        $eventProductLine = EventProductLine::findOrNew($lineId ?? null);
        try {
            if (!empty($eventProductLine->id)) {
                // Already exists
                if (empty($input['qty'])) { // Qty invalid
                    $result = [
                        'result'   => false,
                        'feedback' => 'Quantidade invalida!',
                    ];
                } else {
                    // Adds new Qty to Event Line
                    $eventProductLine->qty = $input['qty'];
                    $eventProductLine->save();

                    $result = [
                        'result'   => true,
                        'feedback' => 'Adicionado ao pedido',
                    ];
                }
            } else {
                // New event line
                $event = EventManager::findOrFail($eventId);

                $result = $this->newEventLine($input, $event, $eventProductLine);
            }
        } catch (\Exception $e) {
            $eventProductLine->forceDelete();

            $result = [
                'result'   => false,
                'feedback' => $e->getMessage()
            ];
        }

        return Response::json($result);
    }

    /**
     * Creates a Event Line after 
     *
     * @return result
     */
    public function newEventLine($input, EventManager $event, EventProductLine $eventProductLine)
    {

        //Get the product
        $product_id = $input['product_id'];
        if (!empty($product_id) && hasModule('logistic')) {
            $product = Product::findOrFail($product_id);
        }

        if (!$eventProductLine->validate($input)) {
            return [
                'result'   => false,
                'feedback' => 'Produto não é possivel ser adicionado.',
            ];
        }

        //ToDo melhorar este mapping
        $data = [
            'event_manager_id' => $event->id,
            'product_id'       => null,
            'location_id'      => $input['location_id'] ?? null,
            'name'             => $input['product'],
            'qty'              => $input['qty'],
            // 'price'            => null,
            'barcode'          => null,
        ];

        // Quando tiver o produto para colocar os dados do produto
        if (isset($product)) {
            $data['product_id'] = $product->id;
            $data['name'] = $product->name;
            // $data['price'] = $product->price;
            $data['barcode'] = $product->barcode;
        }

        $eventProductLine->fill($data);
        $eventProductLine->save();

        $result = [
            'result'   => true,
            'feedback' => 'Adicionado ao pedido',
            'id'       => $event->id,
            'html'     => view('admin.event_manager.partials.product_table', compact('event'))->render()
        ];

        return $result;
    }

    /**
     * remove a line from a event
     *
     * @return array
     */
    public function removeEventLine($eventId, $lineId)
    {
        $customer = Auth::guard('customer')->user();

        $event = EventManager::where('customer_id', $customer->id)
            ->where('id', $eventId)
            ->exists();

        $result = $event ? EventProductLine::whereId($lineId)->delete() : false;

        if (!$result) {
            return [
                'result'   => false,
                'feedback' => 'Impossivel foi removida a linha'
            ];
        }

        return [
            'result'   => true,
            'feedback' => 'Foi removida a Linha',
        ];
    }

    /**
     * Change the current status
     * 
     * @return bool
     */
    public function statusUpdate(Request $request, $id)
    {
        try {
            $event = EventManager::findOrFail($id);

            if ($event->is_draft) {
                throw new \Exception("Estado em rascunho, necessita de revisão.");
            }

            if ($event->is_active) {
                $event->is_active = 0;
                $feedBack = "Inativo";
            } else {
                $event->is_active = 1;
                $feedBack = "Ativo";
            }

            $event->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return Redirect::back()->with('success', 'Foi alterado o Estado para ' . $feedBack . '.');
    }
}
