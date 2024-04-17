<?php

namespace App\Http\Controllers\Admin;

use App\Models\BroadcastPusher;
use App\Models\OperatorTask;
use App\Models\PackType;
use App\Models\TransportType;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Agency;
use Yajra\Datatables\Facades\Datatables;
use Html, Cache, Response, Auth, Setting;

class OperatorsTasksController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'shipments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipments']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $agencies = Agency::filterAgencies()
            ->with(['users' => function ($q) {
                $q->select(['id', 'agency_id']);
                $q->isOperator();
            }])
            ->orderBy('name', 'asc')
            ->get(['id', 'name'])
            ->toArray();

        $shipments = Shipment::filterAgencies()
            ->whereIn('status_id', [ShippingStatus::PENDING_ID, ShippingStatus::ACCEPTED_ID])
            ->whereNull('pickup_operator_id')
            ->orderBy('id', 'desc')
            ->select(['id', \DB::raw('CONCAT(tracking_code, " - ", sender_name, " (", sender_city, ")") as name')])
            ->pluck('name', 'id')
            ->take(400)
            ->toArray();

        $serviceTypes = TransportType::filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $date = date('Y-m-d');
        $data = compact(
            'agencies',
            'shipments',
            'date',
            'serviceTypes'
        );

        $data = array_merge($data, $this->tasksListView(null, $date, false));

        return view('admin.operator_tasks.index', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return string
     */
    public function edit(Request $request, $id)
    {
        $action = 'Editar Tarefa';
        $task = OperatorTask::filterSource()->findOrfail($id);
        $formOptions = [
            'route' => ['admin.operator.tasks.update', 'id' => $task->id],
            'method' => 'PUT',
            'data-toggle' => 'ajax-form',
            'id' => 'modal-edit-operator-task'
        ];

        $operators = User::filterAgencies()
            ->isOperator()
            ->isActive()
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.operator_tasks.edit', compact('task', 'action', 'formOptions', 'operators'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $input    = $request->all();
        $date     = $request->get('date', date('Y-m-d'));
        $customer = $request->get('customer');

        $date = empty($date) ? date('Y-m-d') : $date;
        $lastUpdate = date('Y-m-d') == $date ? date('Y-m-d H:i:s') : $date . ' 00:00:00';
        $operators = $request->operators;

        if (isset($input['status'])) {
            switch ($input['status']) {
                case 'accepted':
                    $input['readed'] = 1;
                    $input['concluded'] = 0;
                    break;
                case 'concluded':
                    $input['readed'] = 1;
                    $input['concluded'] = 1;
                    break;
                case 'pending': // Runs Default
                default:
                    $input['readed'] = 0;
                    $input['concluded'] = 0;
                    break;
            }
        }

        $allOperators = User::filterAgencies()
            ->isOperator()
            ->isActive()
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $task = OperatorTask::findOrNew($id);

        $exists = $task->exists;

        if ($task->validate($input)) {
            $task->fill($input);
            $task->last_update  = $lastUpdate;
            $task->date         = $date;
            $task->start_hour   = @$input['start_hour'];
            $task->end_hour     = @$input['end_hour'];
            $task->source       = config('app.source');
            $task->created_by   = Auth::user()->id;

            if (!$exists && !empty($task->shipments)) {
                $shipment       = Shipment::with('customer')->find($task->shipments[0]);
                $task->address  = @$shipment->customer->address;
                $task->zip_code = @$shipment->customer->zip_code;
                $task->city     = @$shipment->customer->city;
                $task->phone    = @$shipment->customer->phone;
            }

            if ($customer) {
                $task->customer_id = $customer;
                $task->address     = @$input['address'];
                $task->zip_code    = @$input['zip_code'];
                $task->city        = @$input['city'];
                $task->phone       = @$input['phone'];
            }

            $task->save();

            if (!$exists) {
                if (Setting::get('mobile_app_autotasks')) {
                    if (count($operators) == $allOperators) {
                        $task->notifyAllOperators();
                    } else {
                        foreach ($operators as $operatorId) {
                            $task->setNotification(BroadcastPusher::getChannel($operatorId));
                        }
                    }
                }
            }
            
            // $result = $this->tasksListView('Tarefa gravada com sucesso.');
            $result = [
                'result' => true,
                'feedback' => 'Tarefa gravada com sucesso.',
                'trigger_change' => '[name="operator_task_date"]'
            ];
        } else {
            $result = [
                'result'   => false,
                'feedback' => $task->errors()->first()
            ];
        }

        return Response::json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $result = OperatorTask::filterSource()
            ->whereId($id)
            ->delete();

        if ($result) {
            $result = $this->tasksListView('Tarefa removida com sucesso.');
        } else {
            $result = [
                'result'   => true,
                'feedback' => 'Não foi possível remover a tarefa.'
            ];
        }

        return Response::json($result);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = OperatorTask::with('operator')
            ->filterSource()
            ->with('operator')
            ->select();

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter readed
        $value = $request->readed;
        if ($request->has('readed')) {
            $data = $data->where('readed', $value);
        }

        //filter concluded
        $value = $request->status;
        if ($request->has('status')) {
            if ($value == '0') {
                $data = $data->where('readed', 0);
            } elseif ($value == '1') {
                $data = $data->where('readed', 1)
                    ->where('concluded', 0);
            } else {
                $data = $data->where('concluded', 1);
            }
        }

        $operators = User::pluck('name', 'id')->toArray();

        return Datatables::of($data)
            ->edit_column('name', function ($row) {
                return view('admin.operator_tasks.datatables.name', compact('row'))->render();
            })
            ->edit_column('operator_id', function ($row) use ($operators) {
                return view('admin.operator_tasks.datatables.operator', compact('row', 'operators'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('admin.operator_tasks.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.operator_tasks.datatables.actions', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.operator_tasks.datatables.date', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Change operator modal
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $taskId
     * @return string
     */
    public function changeOperator(Request $request, int $taskId) {
        $task = OperatorTask::filterSource()
            ->where('concluded', 0)
            ->findOrfail($taskId);

        $operators = User::filterAgencies()
            ->isOperator()
            ->isActive()
            ->orderBy('name', 'asc')
            ->get();

        $formOptions = [
            'route' => ['admin.operator.tasks.change-operator.store', $task->id],
            'method' => 'POST',
            'data-toggle' => 'ajax-form',
            'id'    => 'modal-change-operator'
        ];

        return view('admin.operator_tasks.modals.change_operator', compact('task', 'operators', 'formOptions'))->render();
    }

    public function changeOperatorStore(Request $request, int $taskId) {
        $task = OperatorTask::filterSource()
            ->where('concluded', 0)
            ->findOrfail($taskId);

        $task->operator_id = $request->get('operator_id');
        $task->save();

        return Response::json([
            'result' => true,
            'feedback' => 'Operador alterado com sucesso.',
            'trigger_change' => '[name="operator_task_date"]'
        ]);
    }

    /**
     * Filter tabs list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterTabs(Request $request)
    {
        return Response::json($this->tasksListView());
    }

    private function tasksListView($feedback = null, $date = null, $returnView = true) {
        $date     = request()->get('operator_task_date', $date);
        $operator = request()->get('operator_task_operator');

        // $date = empty($date) ? date('Y-m-d') : $date;

        $packTypes = PackType::pluck('name', 'code')->toArray();
        $operators = User::filterAgencies()
            ->isOperator()
            ->isActive()
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $allTasks = OperatorTask::with('customer', 'operator')
            ->filterSource();

        if (!empty($date)) {
            $allTasks = $allTasks->where(function ($q) use ($date) {
                $q->where('date', $date);
                $q->orWhere(function ($q) use ($date) {
                    $q->where('date', '<', $date);
                    $q->where('concluded', 0);
                });
            });
        }

        if (!empty($operator)) {
            $allTasks = $allTasks->where(function ($q) use ($operator) {
                $q->where('operator_id', $operator)
                    ->orWhere(function ($q) use ($operator) {
                        $q->where('operators', 'like', '%"' . $operator . '"%')
                            ->whereNull('operator_id');
                    });
            });
        }

        $allTasks = $allTasks->orderBy('readed', 'asc')
            ->orderBy('date', 'asc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $tasksPending = $allTasks->filter(function ($item) {
            return $item->readed == 0 && !$item->operator_id;
        });

        $tasksPendingWithOperator = $allTasks->filter(function ($item) {
            return $item->readed == 0 && $item->operator_id;
        });

        $tasksAccepted = $allTasks->filter(function ($item) {
            return $item->concluded == 0 && $item->readed == 1;
        });

        $tasksConcluded = $allTasks->filter(function ($item) {
            return $item->concluded == 1;
        });

        if ($returnView) {
            $result = [
                'result' => true,
                'html'   => view('admin.operator_tasks.partials.tabs', compact('operators', 'tasksConcluded', 'tasksAccepted', 'tasksPending', 'tasksPendingWithOperator', 'operator', 'date', 'packTypes'))->render(),
                'target' => '#operator-tasks-lists',
            ];
    
            if ($feedback) {
                $result['feedback'] = $feedback;
            }
        } else {
            $result = [
                'operators'                 => $operators,
                'packTypes'                 => $packTypes,
                'tasksPending'              => $tasksPending,
                'tasksPendingWithOperator'  => $tasksPendingWithOperator,
                'tasksAccepted'             => $tasksAccepted,
                'tasksConcluded'            => $tasksConcluded,
            ];
        }

        return $result;
    }
}
