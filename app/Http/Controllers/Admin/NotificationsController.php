<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notice;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests;
use Yajra\Datatables\Facades\Datatables;
use Html, Response, Redirect;
use Auth;

class NotificationsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'notifications';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',notifications']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.notifications.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $notification = Notice::findOrFail($id);

        return view('admin.notifications.show', compact('notification'))->render();
    }

    /**
     * Load notifications panel
     *
     * @return \Illuminate\Http\Response
     */
    public function load() {

        $notifications = Notification::filterMyNotifications()
                                    ->excludeScheduled()
                                    ->orderBy('alert_at', 'desc')
                                    ->take(50)
                                    ->get();

        return Response::json(view('admin.partials.notifications', compact('notifications'))->render());
    }

    /**
     * Mark notification as read or unread
     *
     * @return \Illuminate\Http\Response
     */
    public function read($id) {

        $notification = Notification::filterMyNotifications()
                                    ->findOrFail($id);

        $notification->read = !$notification->read;
        $notification->save();

        return Response::json([
            'read' => $notification->read
        ]);
    }

    /**
     * Mark all notifications as read
     *
     * @return \Illuminate\Http\Response
     */
    public function readAll() {

        Notification::where('recipient', Auth::user()->id)->update(['read' => 1]);

        $notifications = Notification::filterMyNotifications()
            ->excludeScheduled()
            ->orderBy('alert_at', 'desc')
            ->get();

        return Response::json(view('admin.partials.notifications', compact('notifications'))->render());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $result = Notification::filterMyNotifications()->where('id', $id)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a notificação.');
        }

        return Redirect::back()->with('success', 'Notificação removida com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/brands/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $ids = explode(',', $request->ids);

        $result = Notification::filterMyNotifications()->whereIn('id', $ids)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = Notification::filterMyNotifications()
                            ->excludeScheduled()
                            ->orderBy('alert_at', 'desc')
                            ->select();

        return Datatables::of($data)
            ->add_column('icon', function($row) {
                return '<img src="' . asset(trans('admin/notifications.icons.'.strtolower($row->source_class))) . '"/>';
            })
            ->edit_column('alert_at', function($row) {
                return $row->alert_at->format('Y-m-d H:i');
            })
            ->edit_column('message', function($row) {
                return view('admin.notifications.datatables.message', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.notifications.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
