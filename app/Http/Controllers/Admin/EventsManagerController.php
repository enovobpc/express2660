<?php

namespace App\Http\Controllers\Admin;

use App\Models\EventManager;
use App\Models\EventProductLine;
use App\Models\Logistic\Product;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class EventsManagerController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'events_management';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // change: event_manager => events_management
        $this->middleware(['ability:' . config('permissions.role.admin') . ',events_management']);
        validateModule('events_management');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return $this->setContent('admin.event_manager.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $event = new EventManager();

        $action = 'Adicionar Evento';
        $hours = listHours(1);

        $formOptions = array('route' => array('admin.event-manager.store'), 'method' => 'POST', 'class' => 'form-event_manager');

        $data = compact(
            'event',
            'action',
            'formOptions',
            'hours'
        );

        return view('admin.event_manager.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
    public function edit($id)
    {
        $event = EventManager::findOrfail($id);

        $action = 'Editar evento';
        $hours = listHours(1);

        $formOptions = array('route' => array('admin.event-manager.update', $event->id), 'method' => 'PUT', 'class' => 'form-event_manager');

        $data = compact(
            'event',
            'action',
            'formOptions',
            'hours'
        );

        return view('admin.event_manager.edit', $data)->render();
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
        $isAjax = $request->get('ajax', false);
        $id =  empty($id) ? ($input['event_id'] ?? null) : $id; // When is already on sistem but the method comes from store

        $eventManager = EventManager::findOrNew($id);

        if (!$eventManager->validate($input)) {
            return Redirect::back()->withInput()->with('error', $eventManager->errors()->first());
        }
        $eventManager->fill($input);
        $eventManager->horary = [
            "start_date" => $input['start_date'] ?? '',
            "start_hour" => $input['start_hour'] ?? '',
            "end_date"   => $input['end_date'] ?? '',
            "end_hour"   => $input['end_hour'] ?? '',
        ];
        $eventManager->save();

        if ($isAjax) {
            return $eventManager->id;
        }

        // Efetua a tranzição de rascunho para inativo
        $isFinish = $input['is_finish'] ?? null;
        if ($isFinish) {
            $this->finishEvent($eventManager);
        }

        return Redirect::back()->with('success', 'Dados gravados com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        EventManager::flushCache(EventManager::CACHE_TAG);

        $event = EventManager::findOrFail($id);

        if ($event->is_draft == '0') {
            return Redirect::back()->with('error', 'Não pode remover um evento finalizado');
        }

        $result = $event->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a evento');
        }

        return Redirect::back()->with('success', 'Evento removida com sucesso.');
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
        EventManager::flushCache(EventManager::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $events = EventManager::whereIn('id', $ids)->get();

        foreach ($events as $key => $event) {
            if ($event->is_draft) {
                $result = $event->delete();
            }
        }

        if (empty($result) || !$result) {
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

        $data = EventManager::with('customer')->select();

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
                return view('admin.event_manager.datatables.name', compact('row'))->render();
            })
            ->edit_column('type', function ($row) {
                return view('admin.event_manager.datatables.type', compact('row'))->render();
            })
            ->edit_column('customer', function ($row) {
                return view('admin.event_manager.datatables.customer', compact('row'))->render();
            })
            ->edit_column('start_date', function ($row) {
                return view('admin.event_manager.datatables.date_start', compact('row'))->render();
            })
            ->edit_column('end_date', function ($row) {
                return view('admin.event_manager.datatables.date_end', compact('row'))->render();
            })
            // ->edit_column('products', function ($row) {
            //     return view('admin.event_manager.datatables.name', compact('row'))->render();
            // })
            ->edit_column('is_active', function ($row) {
                return view('admin.event_manager.datatables.active', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.event_manager.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.event_manager.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Add product
     *
     * @return type
     */
    public function updateEventLine(Request $request, $eventId, $lineId = null)
    {
        // Plano
        // Ao adicionar um produto vai colocar o evento como rascunho
        // No final de tudo preenchido ao colocar gravar em vez de gravar normalmente, vai percorrer os produtos e descontar se necessário
        // ps: ao colocar o evento em rascunho significa gravar na base de dados com um estádo de rascunho
        $input = $request->toArray();

        $eventProductLine = EventProductLine::findOrNew($lineId ?? null);
        try {
            if (!empty($eventProductLine->id)) {
                // Already exists
                $eventProductLine->qty = $input['qty'];
                $eventProductLine->save();

                $result = [
                    'result'   => true,
                    'feedback' => 'Adicionado ao pedido',
                ];
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
        if (!empty($product_id)) {
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
        $result = EventProductLine::whereId($lineId)
            ->where('event_manager_id', $eventId)
            ->delete();

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
                // Passar para 0 e dar baixa no stock
                $this->finishEvent($event);
                $feedBack = "Finalizado";

                // ToDo Implementar chamada a API e Baixa de Stocks
            } elseif ($event->is_active) {
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
        return $feedBack;
        // return Redirect::route('admin.event-manager.index')->with('success', 'Foi alterado o Estado para ' . $feedBack . '.');
    }

    /**
     * Change the current status
     * 
     * @return bool
     */
    public function finishEvent($event)
    {
        $event->is_draft = 0;
        $event->save();

        return true;
    }
}
