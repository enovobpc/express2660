<?php
namespace App\Exceptions;

use App\Models\LogViewer;
use App\Models\Notification;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Mail;
use Exception, Setting, Request, Log, Auth, DB;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        $trace = LogViewer::getTrace($exception);
        Log::warning(br2nl($trace));

        parent::report($exception);
    }
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Render full exception details in debug mode
       if(config('app.debug')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => '500', 'message' => $exception->getMessage(). ' on line '. $exception->getLine() . '('.$exception->getFile().')'], 500);
            }

            return parent::render($request, $exception);
        }

        // Redirect if token mismatch error
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            if ($request->ajax()){
                return response('Token Mismatch Exception', 401);
            }

            return redirect()->back()->with('error', 'A sessão expirou por inatividade.');
        }

        // Model not found
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Data Not Found.'], 404);
            }

            return response()->view('errors.404', [], 404);
        }

        // Http exceptions
        if ($this->isHttpException($exception))
        {
            // try to find right error page for this exception
            $statusCode = $exception->getStatusCode($exception);

            if (in_array($statusCode, array(403, 404, 503))){

                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Internal server error or page not found.'], 404);
                }

                return response()->view('errors.' . $statusCode, [], $statusCode);
            }
        }

        /*$notification = Notification::firstOrNew(['source_class' => 'ErrorLog']);
        $notification->message   = 'Erro na plataforma ' . env('APP_SOURCE');
        $notification->recipient = 1;
        $notification->alert_at  = date('Y-m-d H:i:s');
        $notification->read      = false;
        $notification->save();*/
        //$notification->setPusher(BroadcastPusher::getChannel(1));

        if(Setting::get('error_log_email_active')) {
            $notificationEmail = Setting::get('error_log_email');
            Mail::send('emails.error_log', compact('exception'), function($message) use($notificationEmail) {
                $message->to($notificationEmail)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Erro na plataforma ' . config('app.source'));
            });
        }


        return response()->view('errors.500', [], 500);

        //return parent::render($request, $exception); //ORIGINAL
    }
    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest(route('account.login'));
    }

}