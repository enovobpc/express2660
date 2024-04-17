<?php

namespace App\Http\Middleware;

use Closure;
use League\OAuth2\Server\ResourceServer;
use Illuminate\Auth\AuthenticationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class CheckClientCredentials
{
    /**
     * The Resource Server instance.
     *
     * @var ResourceServer
     */
    private $server;

    /**
     * Create a new middleware instance.
     *
     * @param  ResourceServer  $server
     * @return void
     */
    public function __construct(ResourceServer $server)
    {
        $this->server = $server;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        $psr = (new DiactorosFactory)->createRequest($request);

        try{

            $psr = $this->server->validateAuthenticatedRequest($psr);

        } catch (OAuthServerException $e) {
            return response()->json([
                'error'   => 'unauthenticated',
                'message' => 'Your authentication on API is wrong or has failed.',
                'hint'    => 'Check if your bearer access token is valid or generate a new bearer access token.'
            ], 401);
        }

        foreach ($scopes as $scope) {
           if (!in_array($scope,$psr->getAttribute('oauth_scopes'))) {
               return response()->json([
                   'error'   => 'unauthenticated',
                   'message' => 'Your authentication on API is wrong or has failed.',
                   'hint'    => 'Check if your bearer access token is valid or generate a new bearer access token.'
               ], 401);
           }
         }

        return $next($request);
    }
}
